<div x-data="{
    // UI state
    deleteModal:false,
    selectedItem:null,
    generateCotizacionModal:false,
    editModal:false,
    showFilters:false,
    loading:false,
    saving:false,
    // Data
    cotizaciones:[],
    clientes:[],
    filters:{ search:'', desde:'', hasta:'', cliente:'', montoMin:'', montoMax:'' },
    // Items manager (por cotización)
    itemsModal:false, currentCotizacionId:null, itemsLoading:false, itemsSearch:'', items:[],
    itemMode:'list', itemForm:{ descripcion:'', precio_unitario:0, cantidad:1, impuesto:0 }, itemEditId:null, itemErrors:{},
    // Catalog selector state
    catalogModal:false, catalogSearch:'', catalogLoading:false, catalogItems:[], catalogSelected:{}, activeFormRef:'form',
    // Forms
    form:{ id:null, id_cliente_fk:'', fecha_cotizacion:'', valido_hasta:'', imponible:0, impuesto:0, total_impuesto:0, subtotal:0, otros_cargos:0, anticipo_requerido:0, total:0, items:[ { descripcion:'', precio_unitario:0, cantidad:1, impuesto:0 } ] },
    editForm:null,
    errors:{},
    // Computed helpers
    calcTotals(form){
        let imponible=0; let totalImp=0; let subtotal=0; let total=0; const items=form.items||[];
        items.forEach(it=>{ const precio=parseFloat(it.precio_unitario)||0; const cant=parseFloat(it.cantidad)||0; const imp=parseFloat(it.impuesto)||0; const linea=precio*cant; imponible+=linea; totalImp+=imp; subtotal+= (linea+imp); });
        total = subtotal + (parseFloat(form.otros_cargos)||0);
        form.imponible = +imponible.toFixed(2);
        form.total_impuesto = +totalImp.toFixed(2);
        form.subtotal = +subtotal.toFixed(2);
        form.total = +total.toFixed(2);
    },
    addItem(formRef='form'){ this[formRef].items.push({ descripcion:'', precio_unitario:0, cantidad:1, impuesto:0 }); },
    removeItem(index, formRef='form'){ this[formRef].items.splice(index,1); this.calcTotals(this[formRef]); },
    apiHeaders(){ const t=localStorage.getItem('authToken'); return { 'Content-Type':'application/json','Accept':'application/json', ...(t?{ 'Authorization':'Bearer '+t }:{}) }; },
    showToast(msg,type='ok'){ let d=document.createElement('div'); d.className='fixed top-4 right-4 z-50 px-3 py-2 rounded text-sm shadow '+(type==='error'?'bg-red-600 text-white':'bg-green-600 text-white'); d.textContent=msg; document.body.appendChild(d); setTimeout(()=>d.remove(),3000); },
    // Auth helpers: get a JWT from web session if needed
    async ensureAuth(){
        try{
            const r=await fetch('/session/token',{ headers:{ 'Accept':'application/json' } });
            if(!r.ok) return null;
            const j=await r.json().catch(()=>({}));
            const token=j?.token||j?.access_token||j?.jwt||j?.bearer||j?.data?.token;
            if(token){ localStorage.setItem('authToken', token); return token; }
        }catch(e){}
        return null;
    },
    async doFetch(url, opts={}, tryAuth=true){
        const t=localStorage.getItem('authToken');
        const hasBody = !!opts.body;
        const headers={ 'Accept':'application/json', ...(opts.headers||{}), ...(t?{ 'Authorization':'Bearer '+t }:{}), ...(hasBody?{ 'Content-Type':'application/json' }:{}) };
        const res=await fetch(url, { ...opts, headers });
        if(res.status===401 && tryAuth){
            const tok=await this.ensureAuth();
            if(tok){
                const headers2={ ...headers, 'Authorization':'Bearer '+tok };
                return fetch(url, { ...opts, headers: headers2 });
            }
        }
        return res;
    },
    handleModalSubmit(evt){
        const id = evt?.detail?.formId;
        if(id==='generateCotizacionForm') return this.createCotizacion();
        if(id==='editCotizacionForm') return this.updateCotizacion();
        if(id==='catalog-selector') return this.addSelectedFromCatalog();
        if(id==='items-manager'){
            if(this.itemMode==='create') return this.submitCreateItem();
            if(this.itemMode==='edit') return this.submitUpdateItem();
            // En modo lista, usa Guardar para recalcular totales de la cotización y cerrar
            if(this.itemMode==='list'){
                this.refreshCotizacionRow(this.currentCotizacionId);
                this.itemsModal=false;
            }
            return;
        }
    },
    // Catalog helpers
    async fetchCatalogItems(){
        this.catalogLoading=true;
        try{
            const p=new URLSearchParams();
            if(this.catalogSearch) p.set('q',this.catalogSearch);
            p.set('per_page','200');
            const r=await this.doFetch('/api/items-cotizacion?'+p.toString());
            if(!r.ok) throw new Error();
            const j=await r.json();
            const data=j.data||j||[];
            this.catalogItems = data.map(it=>({ id:it.id_item_cotizacion_pk, descripcion:it.descripcion, precio_unitario:it.precio_unitario, cantidad:it.cantidad, impuesto:it.impuesto, total:it.total }));
        }catch(e){ this.showToast('Error cargando catálogo','error'); }
        finally{ this.catalogLoading=false; }
    },
    // Items manager helpers
    async fetchItemsForCurrent(){
        if(!this.currentCotizacionId) return;
        this.itemsLoading=true;
        try{
            const p=new URLSearchParams();
            p.set('id_cotizacion_fk', this.currentCotizacionId);
            if(this.itemsSearch) p.set('q', this.itemsSearch);
            p.set('per_page','200');
            const r=await this.doFetch('/api/items-cotizacion?'+p.toString());
            if(!r.ok) throw new Error();
            const j=await r.json();
            const data=j.data||j||[];
            this.items = data.map(it=>({ id:it.id_item_cotizacion_pk, descripcion:it.descripcion, precio_unitario:Number(it.precio_unitario||0), cantidad:Number(it.cantidad||0), impuesto:Number(it.impuesto||0), total:Number(it.total||0) }));
        }catch(e){ this.showToast('Error cargando items','error'); }
        finally{ this.itemsLoading=false; }
    },
    recomputeCurrentRowTotalsUsingItems(){
        const id=this.currentCotizacionId; if(!id) return;
        const rowIndex=this.cotizaciones.findIndex(c=>String(c.id)===String(id)); if(rowIndex<0) return;
        const row={...this.cotizaciones[rowIndex]};
        const imponible = this.items.reduce((acc,it)=> acc + (Number(it.precio_unitario||0)*Number(it.cantidad||0)), 0);
        const totalImp = this.items.reduce((acc,it)=> acc + Number(it.impuesto||0), 0);
        const subtotal = imponible + totalImp;
        const otros = Number(row.otros_cargos ?? 0);
        const total = subtotal + otros;
        row.imponible = +imponible.toFixed(2);
        row.total_impuesto = +totalImp.toFixed(2);
        row.subtotal = +subtotal.toFixed(2);
        row.total = +total.toFixed(2);
        this.cotizaciones.splice(rowIndex,1,row);
    },
    openItems(c){ this.currentCotizacionId=c?.id; this.itemsModal=true; this.itemMode='list'; this.itemForm={ descripcion:'', precio_unitario:0, cantidad:1, impuesto:0 }; this.itemEditId=null; this.itemsSearch=''; this.$nextTick(()=>this.fetchItemsForCurrent()); },
    openNewItem(){ this.itemMode='create'; this.itemForm={ descripcion:'', precio_unitario:0, cantidad:1, impuesto:0 }; this.itemErrors={}; },
    openEditItem(it){ this.itemMode='edit'; this.itemEditId=it.id; this.itemForm={ descripcion:it.descripcion, precio_unitario:it.precio_unitario, cantidad:it.cantidad, impuesto:it.impuesto }; this.itemErrors={}; },
    cancelItemEdit(){ this.itemMode='list'; this.itemForm={ descripcion:'', precio_unitario:0, cantidad:1, impuesto:0 }; this.itemEditId=null; this.itemErrors={}; },
    calcItemTotal(o){ const pu=Number(o.precio_unitario||0); const c=Number(o.cantidad||0); const imp=Number(o.impuesto||0); return +(pu*c+imp).toFixed(2); },
    async submitCreateItem(){ try{ const payload={ ...this.itemForm, id_cotizacion_fk:this.currentCotizacionId, total:this.calcItemTotal(this.itemForm) }; const r=await this.doFetch('/api/items-cotizacion',{ method:'POST', body:JSON.stringify(payload) }); if(r.status===422){ this.itemErrors=await r.json(); throw new Error('valid'); } if(!r.ok) throw new Error(); this.showToast('Item creado'); this.cancelItemEdit(); await this.fetchItemsForCurrent(); this.recomputeCurrentRowTotalsUsingItems(); }catch(e){ this.showToast('No se creó item','error'); } },
    async submitUpdateItem(){ if(!this.itemEditId) return; try{ const payload={ ...this.itemForm, id_cotizacion_fk:this.currentCotizacionId, total:this.calcItemTotal(this.itemForm) }; const r=await this.doFetch('/api/items-cotizacion/'+this.itemEditId,{ method:'PUT', body:JSON.stringify(payload) }); if(r.status===422){ this.itemErrors=await r.json(); throw new Error('valid'); } if(!r.ok) throw new Error(); this.showToast('Item actualizado'); this.cancelItemEdit(); await this.fetchItemsForCurrent(); this.recomputeCurrentRowTotalsUsingItems(); }catch(e){ this.showToast('No se actualizó item','error'); } },
    async deleteItem(it){ if(!it?.id) return; if(!confirm('¿Eliminar este item?')) return; try{ const r=await this.doFetch('/api/items-cotizacion/'+it.id,{ method:'DELETE' }); if(!r.ok) throw new Error(); this.items=this.items.filter(x=>x.id!==it.id); this.showToast('Item eliminado'); this.recomputeCurrentRowTotalsUsingItems(); }catch(e){ this.showToast('No se eliminó item','error'); } },
    openCatalog(formRef='form'){
        this.activeFormRef=formRef;
        this.catalogSelected={};
        this.catalogSearch='';
        this.catalogModal=true;
        this.$nextTick(()=>this.fetchCatalogItems());
    },
    toggleSelectAll(ev){ const checked=ev?.target?.checked; const map={}; this.catalogItems.forEach(it=>{ map[it.id]=!!checked; }); this.catalogSelected=map; },
    addSelectedFromCatalog(){
        const formRef=this.activeFormRef||'form';
        const target=this[formRef];
        if(!target || !Array.isArray(target.items)) return;
        const selectedIds=Object.keys(this.catalogSelected).filter(id=>this.catalogSelected[id]);
        selectedIds.forEach(id=>{
            const it=this.catalogItems.find(x=>String(x.id)===String(id));
            if(!it) return;
            target.items.push({ descripcion:it.descripcion||'', precio_unitario:it.precio_unitario||0, cantidad:it.cantidad||1, impuesto:it.impuesto||0 });
        });
        this.calcTotals(target);
        this.catalogModal=false;
    },
    async fetchCotizaciones(){ this.loading=true; try{ const p=new URLSearchParams(); if(this.filters.search) p.set('q',this.filters.search); if(this.filters.desde) p.set('desde',this.filters.desde); if(this.filters.hasta) p.set('hasta',this.filters.hasta); if(this.filters.cliente) p.set('id_cliente_fk',this.filters.cliente); const r=await this.doFetch('/api/cotizaciones?per_page=100&'+p.toString()); if(!r.ok) throw new Error(); const j=await r.json(); this.cotizaciones = (j.data||j||[])
            .map(c=>({ id:c.id_cotizacion_pk, fecha:c.fecha_cotizacion?.split(' ')[0]||'', valido_hasta:c.valido_hasta, imponible:c.imponible, impuesto:c.impuesto, total_impuesto:c.total_impuesto, otros_cargos:c.otros_cargos, anticipo_requerido:c.anticipo_requerido, total:c.total, cliente_id:c.id_cliente_fk, cliente_nombre:(c.cliente_nombre || c.cliente?.empresa?.nombre_comercial || c.cliente?.empresa?.razon_social || '') }))
            .filter(c=>c.id!=null);
        }catch(e){ this.showToast('Error cargando cotizaciones','error'); } finally { this.loading=false; } },
    async fetchClientes(){ try{ const r=await this.doFetch('/api/empresas-cliente?per_page=200'); if(!r.ok) throw new Error(); const j=await r.json(); const data=j.data||j||[]; this.clientes = data.map(e=>({ id:e.id_cliente_fk, nombre:(e.nombre_comercial||e.razon_social||('Cliente #'+e.id_cliente_fk)) })); }catch(e){ this.clientes=[]; } },
    async refreshCotizacionRow(id){ try{ if(!id) return; const r=await this.doFetch('/api/cotizaciones/'+id); if(!r.ok) return; const c=await r.json(); const idx=this.cotizaciones.findIndex(x=>String(x.id)===String(id)); if(idx>-1){ const updated={ id:c.id_cotizacion_pk, fecha:c.fecha_cotizacion?.split(' ')[0]||'', valido_hasta:c.valido_hasta, imponible:c.imponible, impuesto:c.impuesto, total_impuesto:c.total_impuesto, otros_cargos:c.otros_cargos, anticipo_requerido:c.anticipo_requerido, total:c.total, cliente_id:c.id_cliente_fk, cliente_nombre:(c.cliente_nombre || c.cliente?.empresa?.nombre_comercial || c.cliente?.empresa?.razon_social || '') }; this.cotizaciones.splice(idx,1,updated); } }catch(e){} },
    resetForm(){ const today=new Date(); const plus30=new Date(today.getTime()+30*24*60*60*1000); const fmt=(d)=>d.toISOString().slice(0,10); this.form={ id:null, id_cliente_fk:'', fecha_cotizacion:fmt(today), valido_hasta:fmt(plus30), imponible:0, impuesto:0, total_impuesto:0, subtotal:0, otros_cargos:0, anticipo_requerido:0, total:0, items:[ { descripcion:'', precio_unitario:0, cantidad:1, impuesto:0 } ] }; },
    openCreate(){ this.resetForm(); this.generateCotizacionModal=true; },
    openEdit(c){ this.editForm={ ...c, id:c.id, id_cliente_fk:c.cliente_id, fecha_cotizacion:c.fecha, items:[ { descripcion:'', precio_unitario:0, cantidad:1, impuesto:0 } ] }; this.editModal=true; },
    async createCotizacion(){ this.saving=true; this.calcTotals(this.form); try{ const payload={ fecha_cotizacion:this.form.fecha_cotizacion, valido_hasta:this.form.valido_hasta, subtotal:this.form.subtotal, total:this.form.total, imponible:this.form.imponible, impuesto:this.form.total_impuesto, total_impuesto:this.form.total_impuesto, otros_cargos:this.form.otros_cargos||0, anticipo_requerido:this.form.anticipo_requerido||0, id_cliente_fk:this.form.id_cliente_fk }; const r=await this.doFetch('/api/cotizaciones',{ method:'POST', body:JSON.stringify(payload) }); if(r.status===422){ this.errors=await r.json(); throw new Error('valid'); } if(!r.ok) throw new Error(); const j=await r.json(); const newId=j.data?.id_cotizacion_pk||j.id_cotizacion_pk; // Crear items
        for(const it of this.form.items){ if(!it.descripcion) continue; await this.doFetch('/api/items-cotizacion',{ method:'POST', body:JSON.stringify({ descripcion:it.descripcion, precio_unitario:it.precio_unitario, cantidad:it.cantidad, impuesto:it.impuesto, id_cotizacion_fk:newId }) }); }
            this.showToast('Cotización creada'); this.generateCotizacionModal=false; this.fetchCotizaciones(); }
        catch(e){ this.showToast('No se creó','error'); }
        finally{ this.saving=false; } },
    async updateCotizacion(){ if(!this.editForm) return; this.saving=true; try{ this.calcTotals(this.editForm); const payload={ valido_hasta:this.editForm.valido_hasta, subtotal:this.editForm.subtotal, total:this.editForm.total, imponible:this.editForm.imponible, impuesto:this.editForm.total_impuesto, total_impuesto:this.editForm.total_impuesto, otros_cargos:this.editForm.otros_cargos||0, anticipo_requerido:this.editForm.anticipo_requerido||0, id_cliente_fk:this.editForm.id_cliente_fk }; const r=await this.doFetch('/api/cotizaciones/'+this.editForm.id,{ method:'PUT', body:JSON.stringify(payload) }); if(!r.ok) throw new Error(); this.showToast('Actualizada'); this.editModal=false; this.fetchCotizaciones(); }catch(e){ this.showToast('No se actualizó','error'); } finally{ this.saving=false; } },
    async deleteCotizacion(){ if(!this.selectedItem) return; try{ const r=await this.doFetch('/api/cotizaciones/'+this.selectedItem,{ method:'DELETE' }); if(!r.ok) throw new Error(); this.cotizaciones=this.cotizaciones.filter(c=>c.id!==this.selectedItem); this.showToast('Eliminada'); }catch(e){ this.showToast('No se eliminó','error'); } finally{ this.deleteModal=false; this.selectedItem=null; } },
    applyFilters(){ this.fetchCotizaciones(); },
    init(){
        // Proactively ensure API auth from web session
        this.ensureAuth();
        this.fetchClientes(); this.fetchCotizaciones();
        const debounce=(fn,ms=400)=>{let h;return(...a)=>{clearTimeout(h);h=setTimeout(()=>fn(...a),ms);};};
        this.$watch('filters.search',debounce(()=>this.fetchCotizaciones()));
        this.$watch('catalogSearch',debounce(()=>{ if(this.catalogModal){ this.fetchCatalogItems(); } }, 400));
    }
}" x-init="init()" @modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="deleteCotizacion()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Cotizaciones</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            <div class="w-full">
                <div class="flex">
                    <div class="flex-1 relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                            <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current text-gray-500">
                                <path
                                    d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1114.32 4.906l5.387 5.387a1 1 0 01-1.414 1.414l-5.387-5.387A8 8 0 012 10z">
                                </path>
                            </svg>
                        </span>
                        <input placeholder="Buscar por ID o cliente" x-model="filters.search"
                            class="appearance-none rounded-md border border-gray-300 dark:border-gray-700 block pl-8 pr-6 py-2 w-full bg-white dark:bg-gray-900 text-sm placeholder-gray-400 dark:placeholder-gray-400 text-gray-700 dark:text-gray-200 focus:border-blue-500 focus:outline-none nunito-regular" />
                    </div>
                    <button @click="showFilters = !showFilters"
                        class="ml-2 px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm flex items-center nunito-regular">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filtros
                    </button>
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <button @click="openCreate()"
                class="text-sm w-full sm:w-40 h-12 rounded bg-emerald-500 text-white hover:bg-emerald-600 duration-300 nunito-regular">
                <i class="fas fa-plus"></i> Generar Cotización
            </button>
        </x-slot>

        <x-slot name="table">
            <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                class="bg-gray-50 dark:bg-gray-800 p-4 rounded-md shadow-sm mb-4">
                <div class="flex flex-wrap md:flex-nowrap gap-4 mb-4">
                    <div class="w-full md:w-1/2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1 nunito-bold">Rango de
                            fechas</label>
                        <div class="flex space-x-2">
                            <input type="date" x-model="filters.desde"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1 text-sm bg-white dark:bg-gray-900 text-gray-700 dark:text-white" />
                            <input type="date" x-model="filters.hasta"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1 text-sm bg-white dark:bg-gray-900 text-gray-700 dark:text-white" />
                        </div>
                    </div>

                    <!-- Cliente -->
                    <div class="w-full md:w-1/2">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1 nunito-bold">Cliente</label>
                        <select
                            class="w-full rounded-md border border-gray-300 dark:border-gray-700 shadow-sm focus:ring-blue-500 nunito-regular p-1 text-sm bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200"
                            x-model="filters.cliente">
                            <option value="">Todos los clientes</option>
                            <template x-for="cl in clientes" :key="cl.id">
                                <option :value="cl.id" x-text="cl.nombre"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1 nunito-bold">Rango de
                        montos</label>
                    <div class="flex flex-wrap md:flex-nowrap space-x-0 md:space-x-2 space-y-2 md:space-y-0">
                        <input type="number" placeholder="Monto mínimo" x-model="filters.montoMin"
                            class="w-full md:w-1/2 rounded-md border border-gray-300 dark:border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1 text-sm bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200" />
                        <input type="number" placeholder="Monto máximo" x-model="filters.montoMax"
                            class="w-full md:w-1/2 rounded-md border border-gray-300 dark:border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1 text-sm bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200" />
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button"
                        @click="filters={ search:'', desde:'', hasta:'', cliente:'', montoMin:'', montoMax:'' }; fetchCotizaciones();"
                        class="px-4 py-1 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 text-sm nunito-regular">Limpiar</button>
                    <button type="button" @click="applyFilters()"
                        class="px-4 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm nunito-regular">Aplicar
                        filtros</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="nunito-bold">
                        <tr>
                            <th class="px-4 py-3 text-left bg-white dark:bg-gray-800 nunito-bold dark:text-gray-300">ID</th>
                            <th class="px-4 py-3 text-left bg-white dark:bg-gray-800 nunito-bold dark:text-gray-300">ID
                                Cliente</th>
                            <th class="px-4 py-3 text-left bg-white dark:bg-gray-800 nunito-bold dark:text-gray-300">Fecha
                                Cotización</th>
                            <th class="px-4 py-3 text-left bg-white dark:bg-gray-800 nunito-bold dark:text-gray-300">Válida
                                Hasta</th>
                            <th class="px-4 py-3 text-left bg-white dark:bg-gray-800 nunito-bold dark:text-gray-300">
                                Imponible</th>
                            <th class="px-4 py-3 text-left bg-white dark:bg-gray-800 nunito-bold dark:text-gray-300">
                                Impuesto</th>
                            <th class="px-4 py-3 text-left bg-white dark:bg-gray-800 nunito-bold dark:text-gray-300">Total
                                Imp.</th>
                            <th class="px-4 py-3 text-left bg-white dark:bg-gray-800 nunito-bold dark:text-gray-300">Otros
                                Cargos</th>
                            <th class="px-4 py-3 text-left bg-white dark:bg-gray-800 nunito-bold dark:text-gray-300">
                                Anticipo</th>
                            <th class="px-4 py-3 text-left bg-white dark:bg-gray-800 nunito-bold dark:text-gray-300">Total
                            </th>
                            <th class="px-4 py-3 text-left bg-white dark:bg-gray-800 nunito-bold dark:text-gray-300">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="nunito-regular">
                        <template x-for="c in cotizaciones" :key="c.id">
                            <tr>
                                <td class="px-4 py-3 border-t border-gray-200" x-text="c.id"></td>
                                <td class="px-4 py-3 border-t border-gray-200" x-text="c.cliente_id"></td>
                                <td class="px-4 py-3 border-t border-gray-200" x-text="c.fecha"></td>
                                <td class="px-4 py-3 border-t border-gray-200" x-text="c.valido_hasta"></td>
                                <td class="px-4 py-3 border-t border-gray-200"
                                    x-text="'$'+(Number(c.imponible ?? 0)).toFixed(2)"></td>
                                <td class="px-4 py-3 border-t border-gray-200"
                                    x-text="'$'+(Number(c.impuesto ?? 0)).toFixed(2)"></td>
                                <td class="px-4 py-3 border-t border-gray-200"
                                    x-text="'$'+(Number(c.total_impuesto ?? 0)).toFixed(2)"></td>
                                <td class="px-4 py-3 border-t border-gray-200"
                                    x-text="'$'+(Number(c.otros_cargos ?? 0)).toFixed(2)"></td>
                                <td class="px-4 py-3 border-t border-gray-200"
                                    x-text="'$'+(Number(c.anticipo_requerido ?? 0)).toFixed(2)"></td>
                                <td class="px-4 py-3 border-t border-gray-200"
                                    x-text="'$'+(Number(c.total ?? 0)).toFixed(2)"></td>
                                <td class="px-4 py-3 border-t border-gray-200 flex items-center gap-2">
                                    <a :href="'/admin/detalle-cotizacion?id='+c.id" target="_blank"
                                        class="inline-flex items-center justify-center text-xs px-3 h-8 rounded bg-emerald-500 text-white hover:bg-emerald-600 duration-300 nunito-regular">
                                        <i class='fas fa-eye mr-1'></i> Ver
                                    </a>
                                    <a href="#" @click.prevent="openItems(c)"
                                        class="inline-flex items-center justify-center text-xs px-3 h-8 rounded bg-indigo-600 text-white hover:bg-indigo-700 duration-300 nunito-regular">
                                        <i class="fas fa-database mr-1"></i> Items
                                    </a>
                                    <a href="#" @click.prevent="openEdit(c)" class="text-blue-500 hover:text-blue-700"><i
                                            class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="deleteModal=true; selectedItem=c.id"
                                        class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="!cotizaciones.length && !loading">
                            <td colspan="11" class="text-center text-gray-500 py-4">Sin datos</td>
                        </tr>
                        <tr x-show="loading">
                            <td colspan="11" class="text-center text-gray-500 py-4 animate-pulse">Cargando...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-slot>

          <x-slot name="cards">
            <div class="space-y-4">
                <template x-if="loading"><div class="p-4 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando cotizaciones...</div></template>
                <template x-if="!loading && cotizaciones.length === 0"><div class="p-4 text-center text-gray-500">No hay cotizaciones para mostrar.</div></template>
                <template x-for="c in cotizaciones" :key="c.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white" x-text="'Cotización #' + c.id"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="c.cliente_nombre || 'Cliente sin nombre'"></p>
                            </div>
                            <p class="text-lg font-bold text-gray-800 dark:text-white" x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(c.total)"></p>
                        </div>
                        <p class="text-xs text-gray-400">
                            Fecha: <span x-text="c.fecha"></span> | Válida hasta: <span x-text="c.valido_hasta"></span>
                        </p>
                        <div class="flex justify-end flex-wrap gap-2 pt-3 border-t dark:border-gray-700">
                            <a :href="'/admin/detalle-cotizacion?id='+c.id" target="_blank" class="px-3 py-1 text-xs bg-emerald-600 text-white rounded hover:bg-emerald-700 flex items-center gap-1">
                                <i class='fas fa-eye'></i> Ver
                            </a>
                             <button @click.prevent="openItems(c)" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700 flex items-center gap-1">
                                <i class="fas fa-database"></i> Items
                            </button>
                             <button @click.prevent="openEdit(c)" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                             <button @click.prevent="deleteModal=true; selectedItem=c.id" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>

    </x-responsive-table>

    <x-admin.form-modal class="nunito-bold" modalName="itemsModal" title="Items de la Cotización" submitLabel="Guardar" formId="items-manager" maxWidth="max-w-5xl">
        <div class="space-y-4 p-4">
            <!-- Search and Action Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-sm text-gray-600 dark:text-gray-300 nunito-regular">
                    Cotización ID: <span class="font-semibold" x-text="currentCotizacionId"></span>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                    <input type="text" placeholder="Buscar descripción..." x-model="itemsSearch"
                        class="w-full sm:w-64 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-gray-200 nunito-regular focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                    <button type="button" @click="openNewItem()"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg nunito-regular w-full sm:w-auto">
                        <i class="fas fa-plus mr-1"></i> Nuevo
                    </button>
                    <template x-if="itemMode!=='list'">
                        <button type="button" @click="cancelItemEdit()"
                                class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg nunito-regular w-full sm:w-auto">
                            Cancelar
                        </button>
                    </template>
                </div>
            </div>

            <!-- Item Form -->
            <template x-if="itemMode!=='list'">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800">
                    <div class="space-y-4 sm:col-span-2">
                        <div>
                            <label for="item_descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Descripción</label>
                            <input type="text" id="item_descripcion" x-model="itemForm.descripcion"
                                class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-gray-200 nunito-regular focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                            <template x-if="itemErrors.descripcion">
                                <p class="text-xs text-red-500 mt-1" x-text="itemErrors.descripcion[0]"></p>
                            </template>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="item_precio" class="block text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Precio Unit.</label>
                                <input type="number" step="0.01" id="item_precio" x-model.number="itemForm.precio_unitario"
                                    class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-gray-200 nunito-regular focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-right" />
                                <template x-if="itemErrors.precio_unitario">
                                    <p class="text-xs text-red-500 mt-1" x-text="itemErrors.precio_unitario[0]"></p>
                                </template>
                            </div>
                            <div>
                                <label for="item_cantidad" class="block text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Cantidad</label>
                                <input type="number" step="0.01" id="item_cantidad" x-model.number="itemForm.cantidad"
                                    class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-gray-200 nunito-regular focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-right" />
                                <template x-if="itemErrors.cantidad">
                                    <p class="text-xs text-red-500 mt-1" x-text="itemErrors.cantidad[0]"></p>
                                </template>
                            </div>
                            <div>
                                <label for="item_impuesto" class="block text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Impuesto</label>
                                <input type="number" step="0.01" id="item_impuesto" x-model.number="itemForm.impuesto"
                                    class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-gray-200 nunito-regular focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-right" />
                                <template x-if="itemErrors.impuesto">
                                    <p class="text-xs text-red-500 mt-1" x-text="itemErrors.impuesto[0]"></p>
                                </template>
                            </div>
                        </div>
                        <div>
                            <label for="item_total" class="block text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Total</label>
                            <input type="number" id="item_total" :value="calcItemTotal(itemForm)" disabled
                                class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 nunito-regular text-right" />
                        </div>
                        <div class="text-right">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg nunito-regular"
                                    x-text="itemMode === 'create' ? 'Guardar Item' : 'Actualizar Item'"></button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Responsive Items Table -->
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden bg-white dark:bg-gray-800">
                <div class="block md:hidden space-y-3 p-4">
                    <!-- Mobile: Card Layout -->
                    <template x-if="itemsLoading">
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 text-center text-gray-500 dark:text-gray-300 nunito-regular">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Cargando...
                        </div>
                    </template>
                    <template x-if="!itemsLoading && items.length === 0">
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 text-center text-gray-500 dark:text-gray-300 nunito-regular">
                            Sin items
                        </div>
                    </template>
                    <template x-for="it in items" :key="it.id">
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3 bg-white dark:bg-gray-800">
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Descripción</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300 nunito-regular breack text-end" x-text="it.descripcion"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Precio Unit.</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300 nunito-regular" x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(it.precio_unitario)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Cantidad</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300 nunito-regular" x-text="Number(it.cantidad).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Impuesto</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300 nunito-regular" x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(it.impuesto)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Total</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300 nunito-regular" x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(it.total)"></span>
                                </div>
                                <div class="flex justify-end gap-2 pt-2">
                                    <button @click.prevent="openEditItem(it)" class="text-blue-500 hover:text-blue-600">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click.prevent="deleteItem(it)" class="text-red-500 hover:text-red-600">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="hidden md:block overflow-x-auto">
                    <!-- Desktop: Table Layout -->
                    <table class="min-w-full text-sm bg-white dark:bg-gray-800">
                        <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                            <tr>
                                <th class="py-2 px-4 text-left text-gray-700 dark:text-gray-200">Descripción</th>
                                <th class="py-2 px-4 text-right text-gray-700 dark:text-gray-200">Precio Unit.</th>
                                <th class="py-2 px-4 text-right text-gray-700 dark:text-gray-200">Cantidad</th>
                                <th class="py-2 px-4 text-right text-gray-700 dark:text-gray-200">Impuesto</th>
                                <th class="py-2 px-4 text-right text-gray-700 dark:text-gray-200">Total</th>
                                <th class="py-2 px-4 text-center text-gray-700 dark:text-gray-200">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="itemsLoading">
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-gray-500 dark:text-gray-300 nunito-regular">
                                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando...
                                    </td>
                                </tr>
                            </template>
                            <template x-if="!itemsLoading && items.length === 0">
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-gray-500 dark:text-gray-300 nunito-regular">
                                        Sin items
                                    </td>
                                </tr>
                            </template>
                            <template x-for="it in items" :key="it.id">
                                <tr class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="py-2 px-4 text-gray-600 dark:text-gray-300 nunito-regular" x-text="it.descripcion"></td>
                                    <td class="py-2 px-4 text-right text-gray-600 dark:text-gray-300 nunito-regular" x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(it.precio_unitario)"></td>
                                    <td class="py-2 px-4 text-right text-gray-600 dark:text-gray-300 nunito-regular" x-text="Number(it.cantidad).toFixed(2)"></td>
                                    <td class="py-2 px-4 text-right text-gray-600 dark:text-gray-300 nunito-regular" x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(it.impuesto)"></td>
                                    <td class="py-2 px-4 text-right text-gray-600 dark:text-gray-300 nunito-regular" x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(it.total)"></td>
                                    <td class="py-2 px-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <button @click.prevent="openEditItem(it)" class="text-blue-500 hover:text-blue-600">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button @click.prevent="deleteItem(it)" class="text-red-500 hover:text-red-600">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-admin.form-modal>

    <x-admin.form-modal class="nunito-bold" modalName="generateCotizacionModal" title="Generar Cotización"
        submitLabel="Guardar" formId="generateCotizacionForm" maxWidth="max-w-4xl">
        <div class="grid grid-cols-1 gap-4">

            <!-- ID del Cliente -->
            <div> {{-- Este div ahora ocupa todo el ancho --}}
                <label for="clienteId" class="block text-sm font-medium text-gray-700 nunito-bold">ID del
                    Cliente</label>
                <select id="clienteId" name="clienteId" x-model="form.id_cliente_fk"
                    class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                    <option value="">Seleccione un cliente</option>
                    <template x-for="cl in clientes" :key="cl.id">
                        <option :value="cl.id" x-text="cl.nombre"></option>
                    </template>
                </select>
            </div>

            <!-- Fecha de Cotización -->
            <div> {{-- Este div ahora ocupa todo el ancho --}}
                <label for="fechaCotizacion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de
                    Cotización</label>
                <input type="date" id="fechaCotizacion" name="fechaCotizacion" x-model="form.fecha_cotizacion"
                    class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
            </div>

            <!-- Válido Hasta -->
            <div> {{-- Este div ahora ocupa todo el ancho --}}
                <label for="validoHasta" class="block text-sm font-medium text-gray-700 nunito-bold">Válido
                    Hasta</label>
                <input type="date" id="validoHasta" name="validoHasta" x-model="form.valido_hasta"
                    class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
            </div>

            <!-- Descripción dinámica -->
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <!-- Encabezados de columnas -->
                <div
                    class="hidden sm:grid grid-cols-12 gap-2 mt-2 text-xs text-gray-600 dark:text-gray-300 nunito-bold">
                    <div class="col-span-3">Descripción</div>
                    <div class="col-span-2 text-right">Precio Unit.</div>
                    <div class="col-span-2 text-right">Cantidad</div>
                    <div class="col-span-2 text-right">Impuesto</div>
                    <div class="col-span-2 text-right">Total</div>
                    <div class="col-span-1"></div>
                </div>
                <div class="max-h-48 overflow-y-auto pr-2">
                    <template x-for="(description, index) in form.items" :key="index">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 mt-2 items-center">
                            <div class="col-span-1 sm:col-span-3">
                                <input type="text" x-model="description.descripcion" placeholder="Descripción"
                                    class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                            </div>
                            <div class="col-span-1 sm:col-span-2">
                                <input type="number" step="0.01" x-model="description.precio_unitario"
                                    @input="calcTotals(form)" placeholder="Precio Unitario"
                                    class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                            </div>
                            <div class="col-span-1 sm:col-span-2">
                                <input type="number" step="0.01" x-model="description.cantidad"
                                    @input="calcTotals(form)" placeholder="Cantidad"
                                    class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                            </div>
                            <div class="col-span-1 sm:col-span-2">
                                <input type="number" step="0.01" x-model="description.impuesto"
                                    @input="calcTotals(form)" placeholder="Impuesto"
                                    class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                            </div>
                            <div class="col-span-1 sm:col-span-2">
                                <input type="number"
                                    :value="(Number(description.precio_unitario||0)*Number(description.cantidad||0)+Number(description.impuesto||0)).toFixed(2)"
                                    disabled placeholder="Total"
                                    class="w-full rounded-md border border-gray-200 bg-gray-100 shadow-sm nunito-regular p-1">
                            </div>
                            <div class="col-span-1 sm:col-span-1 text-right">
                                <button type="button" @click="removeItem(index,'form')"
                                    class="text-red-500 hover:text-red-700" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="flex gap-2 mt-2">
                    <button type="button" @click="addItem('form')"
                        class="mt-2 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm nunito-regular">
                        <i class="fas fa-plus"></i> Añadir Descripción
                    </button>
                    <button type="button" @click="openCatalog('form')"
                        class="mt-2 bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-sm nunito-regular">
                        <i class="fas fa-list"></i> Seleccionar del catálogo
                    </button>
                </div>
            </div>

            <!-- Fila para Imponible, Total Impuesto, Otros Cargos -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Este es el nuevo contenedor para la fila de 3 elementos --}}
                <!-- Imponible -->
                <div>
                    <label for="imponible" class="block text-sm font-medium text-gray-700 nunito-bold">Imponible</label>
                    <input type="number" id="imponible" name="imponible" x-model="form.imponible" readonly
                        class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                </div>

                <!-- Total impuesto -->
                <div>
                    <label for="totalImpuesto" class="block text-sm font-medium text-gray-700 nunito-bold">Total
                        Impuesto</label>
                    <input type="number" id="totalImpuesto" name="totalImpuesto" x-model="form.total_impuesto" readonly
                        class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                </div>

                <!-- Otros cargos -->
                <div>
                    <label for="otrosCargos" class="block text-sm font-medium text-gray-700 nunito-bold">Otros
                        Cargos</label>
                    <input type="number" id="otrosCargos" name="otrosCargos" x-model="form.otros_cargos"
                        @input="calcTotals(form)"
                        class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                </div>
            </div>

            <!-- Total -->
            <div> {{-- Este div ahora ocupa todo el ancho --}}
                <label for="total" class="block text-sm font-medium text-gray-700 nunito-bold">Total</label>
                <input type="number" id="total" name="total" x-model="form.total" readonly
                    class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal de Edición de Cotización -->
    <x-admin.edit-modal class="nunito-bold" modalName="editModal" title="Editar Cotización" submitLabel="Actualizar"
        itemToEdit="editForm" maxWidth="max-w-4xl" formId="editCotizacionForm">
        <div x-show="editForm" class="space-y-4">
            <div class="grid grid-cols-1 gap-4"> {{-- Contenedor principal para organizar en filas --}}

                <!-- ID del Cliente -->
                <div>
                    <label for="editClienteId" class="block text-sm font-medium text-gray-700 nunito-bold">ID del
                        Cliente</label>
                    <select id="editClienteId" name="clienteId" x-model="editForm.id_cliente_fk"
                        class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                        <option value="">Seleccione un cliente</option>
                        <template x-for="cl in clientes" :key="cl.id">
                            <option :value="cl.id" x-text="cl.nombre"></option>
                        </template>
                    </select>
                </div>

                <!-- Fecha de Cotización -->
                <div>
                    <label for="editFechaCotizacion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                        de
                        Cotización</label>
                    <input type="date" id="editFechaCotizacion" name="fechaCotizacion" disabled
                        class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-100 shadow-sm nunito-regular p-1"
                        x-model="editForm.fecha">
                </div>

                <!-- Válido Hasta -->
                <div>
                    <label for="editValidoHasta" class="block text-sm font-medium text-gray-700 nunito-bold">Válido
                        Hasta</label>
                        Hasta</label>
                    <input type="date" id="editValidoHasta" name="validoHasta"
                        class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                        x-model="editForm.valido_hasta">
                </div>

                <!-- Descripción dinámica -->
                <div class="col-span-1"> {{-- Aquí la clase col-span-1 es redundante pero no hace daño --}}
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <!-- Encabezados de columnas -->
                    <div
                        class="hidden sm:grid grid-cols-12 gap-2 mt-2 text-xs text-gray-600 dark:text-gray-300 nunito-bold">
                        <div class="col-span-3">Descripción</div>
                        <div class="col-span-2 text-right">Precio Unit.</div>
                        <div class="col-span-2 text-right">Cantidad</div>
                        <div class="col-span-2 text-right">Impuesto</div>
                        <div class="col-span-2 text-right">Total</div>
                        <div class="col-span-1"></div>
                    </div>
                    <div class="max-h-48 overflow-y-auto pr-2">
                        <template x-for="(descripcion, index) in (editForm.items || [])" :key="index">
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 mt-2 items-center">
                                <div class="col-span-1 sm:col-span-3">
                                    <input type="text" x-model="descripcion.descripcion" placeholder="Descripción"
                                        class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                                        x-model="descripcion.descripcion">
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <input type="number" step="0.01" x-model="descripcion.precio_unitario"
                                        @input="calcTotals(editForm)" placeholder="Precio Unitario"
                                        class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                                        x-model="descripcion.precio">
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <input type="number" step="0.01" x-model="descripcion.cantidad"
                                        @input="calcTotals(editForm)" placeholder="Cantidad"
                                        class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                                        x-model="descripcion.cantidad">
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <input type="number" step="0.01" x-model="descripcion.impuesto"
                                        @input="calcTotals(editForm)" placeholder="Impuesto"
                                        class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                                        x-model="descripcion.impuesto">
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <input type="number"
                                        :value="(Number(descripcion.precio_unitario||0)*Number(descripcion.cantidad||0)+Number(descripcion.impuesto||0)).toFixed(2)"
                                        placeholder="Total" disabled
                                        class="w-full rounded-md border border-gray-200 bg-gray-100 shadow-sm nunito-regular p-1">
                                </div>
                                <div class="col-span-1 sm:col-span-1 text-right">
                                    <button type="button"
                                        @click="editForm.items.splice(index, 1); calcTotals(editForm);"
                                        class="text-red-500 hover:text-red-700" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="flex gap-2 mt-2">
                        <button type="button"
                            @click="editForm.items.push({ descripcion:'', precio_unitario:0, cantidad:1, impuesto:0 }); calcTotals(editForm);"
                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm nunito-regular">
                            <i class="fas fa-plus"></i> Añadir Descripción
                        </button>
                        <button type="button" @click="openCatalog('editForm')"
                            class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-sm nunito-regular">
                            <i class="fas fa-list"></i> Seleccionar del catálogo
                        </button>
                    </div>
                </div>

                <!-- Fila para Imponible, Total Impuesto, Otros Cargos -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Imponible -->
                    <div>
                        <label for="editImponible"
                            class="block text-sm font-medium text-gray-700 nunito-bold">Imponible</label>
                        <input type="number" id="editImponible" name="imponible" readonly
                            class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-100 shadow-sm nunito-regular p-1"
                            x-model="editForm.imponible">
                    </div>

                    <!-- Total impuesto -->
                    <div>
                        <label for="editTotalImpuesto" class="block text-sm font-medium text-gray-700 nunito-bold">Total
                            Impuesto</label>
                        <input type="number" id="editTotalImpuesto" name="totalImpuesto" readonly
                            class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-100 shadow-sm nunito-regular p-1"
                            x-model="editForm.total_impuesto">
                    </div>

                    <!-- Otros cargos -->
                    <div>
                        <label for="editOtrosCargos" class="block text-sm font-medium text-gray-700 nunito-bold">Otros
                            Cargos</label>
                        <input type="number" id="editOtrosCargos" name="otrosCargos"
                            class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                            x-model="editForm.otros_cargos" @input="calcTotals(editForm)">
                    </div>
                </div>

                <!-- Total -->
                <div> {{-- Este div ahora ocupa todo el ancho --}}
                    <label for="editTotal" class="block text-sm font-medium text-gray-700 nunito-bold">Total</label>
                    <input type="number" id="editTotal" name="total" readonly
                        class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-100 shadow-sm nunito-regular p-1"
                        x-model="editForm.total">
                </div>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal selector de Items de Cotización -->
    <x-admin.form-modal class="nunito-bold" modalName="catalogModal" title="Seleccionar items del catálogo"
        submitLabel="Agregar seleccionados" formId="catalog-selector" maxWidth="max-w-4xl">
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <input type="text" placeholder="Buscar descripción..." x-model="catalogSearch"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1 text-sm bg-white dark:bg-gray-900 text-gray-700 dark:text-white" />
            </div>
            <div
                class="overflow-x-auto max-h-[420px] overflow-y-auto border border-gray-200 dark:border-gray-700 rounded">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-3"><input type="checkbox" @change="toggleSelectAll($event)"
                                    title="Seleccionar todo" /></th>
                            <th class="py-2 px-3 text-left">Descripción</th>
                            <th class="py-2 px-3 text-right">Precio Unit.</th>
                            <th class="py-2 px-3 text-right">Cantidad</th>
                            <th class="py-2 px-3 text-right">Impuesto</th>
                            <th class="py-2 px-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="catalogLoading">
                            <tr>
                                <td colspan="6" class="py-4 px-4 text-center text-gray-600 dark:text-gray-300"><i
                                        class="fas fa-spinner fa-spin mr-2"></i> Cargando...</td>
                            </tr>
                        </template>
                        <template x-if="!catalogLoading && catalogItems.length===0">
                            <tr>
                                <td colspan="6" class="py-4 px-4 text-center text-gray-600 dark:text-gray-300">Sin items
                                    en catálogo.</td>
                            </tr>
                        </template>
                        <template x-for="it in catalogItems" :key="it.id">
                            <tr
                                class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 nunito-regular">
                                <td class="py-2 px-3"><input type="checkbox" x-model="catalogSelected[it.id]" /></td>
                                <td class="py-2 px-3" x-text="it.descripcion"></td>
                                <td class="py-2 px-3 text-right" x-text="Number(it.precio_unitario||0).toFixed(2)"></td>
                                <td class="py-2 px-3 text-right" x-text="Number(it.cantidad||0).toFixed(2)"></td>
                                <td class="py-2 px-3 text-right" x-text="Number(it.impuesto||0).toFixed(2)"></td>
                                <td class="py-2 px-3 text-right"
                                    x-text="Number(it.total|| (Number(it.precio_unitario||0)*Number(it.cantidad||0)+Number(it.impuesto||0))).toFixed(2)">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Confirmación de eliminación -->
    <x-admin.confirmation-modal class="nunito-bold" modalName="deleteModal" title="Eliminar Cotización"
        itemToDelete="selectedItem" itemNameProperty="id" message="¿Estás seguro de eliminar la cotización" />
</div>

<style>
    table tbody td {
        font-size: 0.875rem;
    }
</style>