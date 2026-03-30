<div x-data="{
    deleteModal:false,
    selectedItem:null,
    restoreModal:false,
    selectedRestoreItem:null,
    confirmMode:'delete',
    generateCotizacionModal:false,
    editModal:false,
    loading:false,
    saving:false,
    cotizaciones:[],
    clientes:[],
    estadosCotizacion:[],
    filters:{ search:'', desde:'', hasta:'', cliente:'', montoMin:'', montoMax:'', estadoRegistro:'activos' },
    ordenarPor:'',
    currentPage: 1,
    perPage: 10,
    numbers: [],
    itemsModal:false, currentCotizacionId:null, itemsLoading:false, itemsSearch:'', items:[],
    itemMode:'list', itemForm:{ descripcion:'', precio_unitario:0, cantidad:1, impuesto:0, id_producto_fk:null, aplicar_impuesto:false }, itemEditId:null, itemErrors:{},
    
    catalogModal:false, catalogSearch:'', catalogLoading:false, catalogItems:[], catalogExisting:{}, catalogSelectedUser:{}, activeFormRef:'form',
    form:{ id:null, id_cliente_fk:'', fecha_cotizacion:'', valido_hasta:'', imponible:0, impuesto:0, total_impuesto:0, subtotal:0, otros_cargos:0, impuesto_otros:0, apply_isv_otros:false, anticipo_requerido:0, total:0, items:[ { descripcion:'', precio_unitario:0, cantidad:1, impuesto:0, aplicar_impuesto:false } ] },
    editForm:null,
    errors:{},
    formCotizacion: { _touched: {} },
    formEditCotizacion: { _touched: {} },
    calcTotals(form){
        let imponible=0; let totalImp=0; let subtotal=0; let total=0; const items=form.items||[];
        items.forEach(it=>{
            const precio=parseFloat(it.precio_unitario)||0;
            const cant=parseFloat(it.cantidad)||0;
            // If the item has aplicar_impuesto true, compute impuesto as 15% of precio*cantidad
            if(it.aplicar_impuesto){
                const calcImp = +(precio * cant * 0.15);
                it.impuesto = +calcImp.toFixed(2);
            } else {
                // Si se desmarca, el impuesto del ítem debe ser 0
                it.impuesto = 0;
            }
            const imp = parseFloat(it.impuesto)||0;
            const linea = precio * cant;
            imponible += linea;
            totalImp += imp;
            subtotal += (linea + imp);
        });
        const otros = parseFloat(form.otros_cargos)||0;
    const otrosImp = form.apply_isv_otros ? +(otros * 0.15).toFixed(2) : 0;
        total = subtotal + otros + otrosImp;
        form.imponible = +imponible.toFixed(2);
        const totalImpuesto = +(totalImp + otrosImp).toFixed(2);
        form.impuesto = totalImpuesto;
        form.total_impuesto = totalImpuesto;
    form.impuesto_otros = otrosImp;
        form.subtotal = +subtotal.toFixed(2);
        form.total = +total.toFixed(2);
        try { form.anticipo_requerido = +(form.total * 0.5).toFixed(2); } catch(e) { form.anticipo_requerido = 0; }
    },
    authToken:null,
    formatCotId(c){
        try{
            const id = c?.id ?? '';
            const raw = (c?.fecha || c?.fecha_cotizacion || '').toString();
            const digits = raw.replace(/[^0-9]/g,'');
            let fh = digits.slice(0,12);
            if(!fh || fh.length < 8) {
                const now = new Date();
                const YYYY = now.getFullYear();
                const MM = String(now.getMonth()+1).padStart(2,'0');
                const DD = String(now.getDate()).padStart(2,'0');
                const hh = String(now.getHours()).padStart(2,'0');
                const mm = String(now.getMinutes()).padStart(2,'0');
                fh = `${YYYY}${MM}${DD}${hh}${mm}`.slice(0,12);
            } else if(fh.length === 8) {
                fh = fh;
            }
            const pad4 = (n)=> (('0000') + String(n)).slice(-4);
            return `COT-${fh}-${pad4(id)}`;
        }catch(e){ return c?.id ?? ''; }
    },
    normalizeClienteNombre(name){
        try{
            const tokens = String(name || '').trim().split(/\s+/).filter(Boolean);
            if(!tokens.length) return '';
            const seen = new Set();
            const unique = tokens.filter(t=>{
                const k = String(t).toLowerCase();
                if(seen.has(k)) return false;
                seen.add(k);
                return true;
            });
            return unique.join(' ');
        }catch(e){
            return String(name || '');
        }
    },
    addItem(formRef='form'){ this[formRef].items.push({ descripcion:'', precio_unitario:0, cantidad:1, impuesto:0, aplicar_impuesto:false }); },
    removeItem(index, formRef='form'){ this[formRef].items.splice(index,1); this.calcTotals(this[formRef]); },
    removeEditItem(index){
        try{
            if(!this.editForm || !Array.isArray(this.editForm.items)) return;
            this.editForm.items.splice(index,1);
            this.editForm.items = this.editForm.items.slice();
            this.calcTotals(this.editForm);
        }catch(e){ console.error('removeEditItem error', e); }
    },
    apiHeaders(){ return { 'Content-Type':'application/json','Accept':'application/json' }; },
    showToast(msg,type='ok'){ let d=document.createElement('div'); d.className='fixed top-4 right-4 z-50 px-3 py-2 rounded text-sm shadow '+(type==='error'?'bg-red-600 text-white':'bg-green-600 text-white'); d.textContent=msg; document.body.appendChild(d); setTimeout(()=>d.remove(),3000); },
    async ensureAuth(){
        try{
            const r=await fetch('/session/token',{ headers:{ 'Accept':'application/json' }, credentials: 'same-origin' });
            if(!r.ok) return null;
            const j=await r.json().catch(()=>({}));
            const token=j?.token||j?.access_token||j?.jwt||j?.bearer||j?.data?.token;
            if(token){ this.authToken = token; return token; }
        }catch(e){}
        return null;
    },
    // Wrapper around fetch that attaches Authorization header from in-memory token and attempts to refresh once on 401.
    async doFetch(url, opts={}, tryAuth=true){
        const t=this.authToken;
        const hasBody = !!opts.body;
        const headers={ 'Accept':'application/json', ...(opts.headers||{}), ...(t?{ 'Authorization':'Bearer '+t }:{}), ...(hasBody?{ 'Content-Type':'application/json' }:{}) };
        const res=await fetch(url, { ...opts, headers, credentials: 'same-origin' });
        if(res.status===401 && tryAuth){
            const tok=await this.ensureAuth();
            if(tok){
                const headers2={ ...headers, 'Authorization':'Bearer '+tok };
                return fetch(url, { ...opts, headers: headers2, credentials: 'same-origin' });
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
            if(this.itemMode==='list'){
                this.refreshCotizacionRow(this.currentCotizacionId);
                this.itemsModal=false;
            }
            return;
        }
    },
    async fetchCatalogItems(){
        this.catalogLoading=true;
        try{
            const p=new URLSearchParams();
            if(this.catalogSearch) p.set('q',this.catalogSearch);
            p.set('per_page','200');
            const r=await this.doFetch('/api/productos?'+p.toString());
            if(!r.ok) throw new Error();
            const j=await r.json();
            const data=j.data||j||[];
            this.catalogItems = data.map(prod=>({
                id: prod.id_producto_pk,
                descripcion: prod.nombre_producto || prod.descripcion_producto || '',
                precio_unitario: Number(prod.precio_unitario ?? prod.precio_venta ?? 0),
                cantidad: 1,
                impuesto: 0,
                total: Number((Number(prod.precio_unitario ?? prod.precio_venta ?? 0) * 1).toFixed(2))
            }));
        }catch(e){ this.showToast('Error cargando catálogo de productos','error'); }
        finally{ this.catalogLoading=false; }
    },
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
    async fetchItemsForEdit(cotizacionId){
        if(!cotizacionId) return;
        try{
            const p=new URLSearchParams();
            p.set('id_cotizacion_fk', cotizacionId);
            p.set('per_page','200');
            const r=await this.doFetch('/api/items-cotizacion?'+p.toString());
            if(!r.ok) throw new Error();
            const j=await r.json();
            const data=j.data||j||[];
            // If the edit form was closed while the request was in-flight, don't apply results
            if(!this.editForm) return;
            this.editForm.items = data.map(it=>({ descripcion: it.descripcion || '', precio_unitario: Number(it.precio_unitario||0), cantidad: Number(it.cantidad||0), impuesto: Number(it.impuesto||0), id_producto_fk: it.id_producto_fk || null, id_item: it.id_item_cotizacion_pk, aplicar_impuesto: Boolean(Number(it.impuesto||0) > 0) }));
            try{ this._editOriginalItemIds = data.map(it=>it.id_item_cotizacion_pk); }catch(e){ this._editOriginalItemIds = []; }
            this.calcTotals(this.editForm);
        }catch(e){
            this.showToast('No se pudieron cargar los items para edición','error');
            if(this.editForm){
                this.editForm.items = this.editForm.items && this.editForm.items.length ? this.editForm.items : [ { descripcion:'', precio_unitario:0, cantidad:1, impuesto:0 } ];
            }
        }
    },

    

    markCatalogSelectedFromForm(){
        try{
            this.catalogExisting = this.catalogExisting || {};
            const formRef = this.activeFormRef || 'form';
            const target = this[formRef];
            if(!target || !Array.isArray(target.items)) return;
            const ids = target.items.map(it=>it.id_producto_fk).filter(Boolean).map(String);
            const map = {};
            this.catalogItems.forEach(ci=>{ map[String(ci.id)] = ids.includes(String(ci.id)); });
            this.catalogExisting = map;
        }catch(e){ console.debug('markCatalogSelectedFromForm error', e); }
    },
    recomputeCurrentRowTotalsUsingItems(){
        const id=this.currentCotizacionId; if(!id) return;
        const rowIndex=this.cotizaciones.findIndex(c=>String(c.id)===String(id)); if(rowIndex<0) return;
        const row={...this.cotizaciones[rowIndex]};
        const imponible = this.items.reduce((acc,it)=> acc + (Number(it.precio_unitario||0)*Number(it.cantidad||0)), 0);
        const totalImp = this.items.reduce((acc,it)=> acc + Number(it.impuesto||0), 0);
        const subtotal = imponible + totalImp;
        const otros = Number(row.otros_cargos ?? 0);
    const otrosImp = Number(row.impuesto_otros || 0);
        const total = subtotal + otros + otrosImp;
        row.imponible = +imponible.toFixed(2);
        row.total_impuesto = +(totalImp + otrosImp).toFixed(2);
        row.impuesto_otros = otrosImp;
        row.subtotal = +subtotal.toFixed(2);
        row.total = +total.toFixed(2);
        // Anticipo requerido: 50% del total
        try { row.anticipo_requerido = +(row.total * 0.5).toFixed(2); } catch(e) { row.anticipo_requerido = 0; }
        this.cotizaciones.splice(rowIndex,1,row);
    },
    openItems(c){ this.currentCotizacionId=c?.id; this.itemsModal=true; this.itemMode='list'; this.itemForm={ descripcion:'', precio_unitario:0, cantidad:1, impuesto:0, id_producto_fk:null }; this.itemEditId=null; this.itemsSearch=''; this.$nextTick(()=>{ this.fetchItemsForCurrent(); }); },
    openNewItem(){ this.itemMode='create'; this.itemForm={ descripcion:'', precio_unitario:0, cantidad:1, impuesto:0, id_producto_fk:null, aplicar_impuesto:false }; this.itemErrors={}; },
    openEditItem(it){ this.itemMode='edit'; this.itemEditId=it.id; this.itemForm={ descripcion:it.descripcion, precio_unitario:it.precio_unitario, cantidad:it.cantidad, impuesto:it.impuesto, id_producto_fk: it.id_producto_fk || null, aplicar_impuesto: Boolean(it.impuesto && Number(it.impuesto) > 0) }; this.itemErrors={}; },
    cancelItemEdit(){ this.itemMode='list'; this.itemForm={ descripcion:'', precio_unitario:0, cantidad:1, impuesto:0, id_producto_fk:null, aplicar_impuesto:false }; this.itemEditId=null; this.itemErrors={}; },
    calcItemTotal(o){ const pu=Number(o.precio_unitario||0); const c=Number(o.cantidad||0); const imp=Number(o.impuesto||0); return +(pu*c+imp).toFixed(2); },
    async submitCreateItem(){ try{ const payload={ ...this.itemForm, id_cotizacion_fk:this.currentCotizacionId, total:this.calcItemTotal(this.itemForm) }; const r=await this.doFetch('/api/items-cotizacion',{ method:'POST', body:JSON.stringify(payload) }); if(r.status===422){ this.itemErrors=await r.json(); throw new Error('valid'); } if(!r.ok) throw new Error(); this.showToast('Item creado'); this.cancelItemEdit(); await this.fetchItemsForCurrent(); this.recomputeCurrentRowTotalsUsingItems(); }catch(e){ this.showToast('No se creó item','error'); } },
    async submitUpdateItem(){ if(!this.itemEditId) return; try{ const payload={ ...this.itemForm, id_cotizacion_fk:this.currentCotizacionId, total:this.calcItemTotal(this.itemForm) }; const r=await this.doFetch('/api/items-cotizacion/'+this.itemEditId,{ method:'PUT', body:JSON.stringify(payload) }); if(r.status===422){ this.itemErrors=await r.json(); throw new Error('valid'); } if(!r.ok) throw new Error(); this.showToast('Item actualizado'); this.cancelItemEdit(); await this.fetchItemsForCurrent(); this.recomputeCurrentRowTotalsUsingItems(); }catch(e){ this.showToast('No se actualizó item','error'); } },
    async deleteItem(it){ if(!it?.id) return; if(!confirm('¿Eliminar este item?')) return; try{ const r=await this.doFetch('/api/items-cotizacion/'+it.id,{ method:'DELETE' }); if(!r.ok) throw new Error(); this.items=this.items.filter(x=>x.id!==it.id); this.showToast('Item eliminado'); this.recomputeCurrentRowTotalsUsingItems(); }catch(e){ this.showToast('No se eliminó item','error'); } },
    openCatalog(formRef='form'){
        this.activeFormRef=formRef;
        this.catalogSelectedUser={};
        this.catalogSearch='';
        this.catalogModal=true;
        this.$nextTick(async ()=>{
            await this.fetchCatalogItems();
            this.markCatalogSelectedFromForm();
            this.catalogSelectedUser = {};
        });
    },
    toggleSelectAll(ev){ const checked=ev?.target?.checked; const map={}; this.catalogItems.forEach(it=>{ map[it.id]=!!checked; }); this.catalogSelectedUser=map; },
    addSelectedFromCatalog(){
        const formRef=this.activeFormRef||'form';
        const target=this[formRef];
        if(!target || !Array.isArray(target.items)) return;
    const selectedIds=Object.keys(this.catalogSelectedUser).filter(id=>this.catalogSelectedUser[id]);
        let added=0, skipped=0;
        selectedIds.forEach(id=>{
            const it=this.catalogItems.find(x=>String(x.id)===String(id));
            if(!it) return;
            const exists = target.items.some(x=> String(x.id_producto_fk || '') === String(it.id));
            if(exists){ skipped++; return; }
            target.items.push({ descripcion:it.descripcion||'', precio_unitario:it.precio_unitario||0, cantidad:it.cantidad||1, impuesto:it.impuesto||0, id_producto_fk: it.id, aplicar_impuesto:false });
            added++;
        });
        try{ target.items = target.items.slice(); }catch(e){}
        this.calcTotals(target);
    this.catalogModal=false;
    this.catalogSelectedUser = {};
        if(added>0) this.showToast(added + ' items agregados');
        if(skipped>0) this.showToast(skipped + ' items ya estaban en la lista', 'error');
    },
    paginatedCotizaciones() {
        return this.cotizaciones.slice((this.currentPage - 1) * this.perPage, this.currentPage * this.perPage);
    },
    totalPages() {
        return Math.ceil(this.cotizaciones.length / this.perPage);
    },
    nextPage() {
        if (this.currentPage < this.totalPages()) {
            this.currentPage++;
        }
    },
    prevPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
        }
    },
    goToPage(page) {
        this.currentPage = page;
    },
    async fetchCotizaciones(){ this.loading=true; try{ const p=new URLSearchParams(); if(this.filters.search) p.set('q',this.filters.search); if(this.filters.desde) p.set('desde',this.filters.desde); if(this.filters.hasta) p.set('hasta',this.filters.hasta); if(this.filters.cliente) p.set('id_cliente_fk',this.filters.cliente); if(this.filters.estadoRegistro==='todos') p.set('include_inactivos','1'); if(this.filters.estadoRegistro==='inactivos') p.set('only_inactivos','1'); if(this.ordenarPor) p.set('sort', this.ordenarPor); const r=await this.doFetch('/api/cotizaciones?per_page=100&'+p.toString()); if(!r.ok) throw new Error(); const j=await r.json(); this.cotizaciones = (j.data||j||[])
    .map(c=>({ id:c.id_cotizacion_pk, fecha:c.fecha_cotizacion?.split(' ')[0]||'', valido_hasta:c.valido_hasta, imponible:c.imponible, impuesto:c.impuesto, total_impuesto:c.total_impuesto, otros_cargos:c.otros_cargos, impuesto_otros: Number(c.impuesto_otros||0), anticipo_requerido:c.anticipo_requerido, total:c.total, es_activo: Boolean(Number(c.es_activo ?? 1)), cliente_id: (c.id_cliente_fk!=null? String(c.id_cliente_fk):''), cliente_nombre:this.normalizeClienteNombre(c.cliente_nombre || c.cliente?.empresa?.nombre_comercial || c.cliente?.empresa?.razon_social || ''), estado_nombre: (c.estado?.nombre || c.estado?.nombre_estado || null), estado_codigo: (c.estado?.codigo || null), estado_id: (c.id_estado_cotizacion_fk!=null? String(c.id_estado_cotizacion_fk):'') }))
            .filter(c=>c.id!=null);
        this.numbers = this.cotizaciones;
        }catch(e){ this.showToast('Error cargando cotizaciones','error'); } finally { this.loading=false; } },
    async fetchClientes(){
        try{
            const r = await this.doFetch('/api/clientes?per_page=200');
            if(!r.ok) throw new Error();
            const j = await r.json();
            const data = j.data || j || [];
            this.clientes = data.map(e => {
                const id = e.id_cliente_fk ?? e.id ?? e.id_cliente_pk ?? null;
                let nombre = '';
                nombre = e.nombre_comercial || e.razon_social || e.nombre || nombre;
                if(!nombre) {
                    const persona = Array.isArray(e.persona) ? e.persona[0] : e.persona || e.personas?.[0] || {};
                    nombre = [persona.primer_nombre, persona.segundo_nombre, persona.primer_apellido, persona.segundo_apellido]
                        .filter(Boolean).join(' ').trim();
                }
                if(!nombre) nombre = 'Cliente #' + (id ?? 'n/d');
                return { id: id, nombre };
            });
        }catch(e){
            this.clientes = [];
        }
    },
    async refreshCotizacionRow(id){ try{ if(!id) return; const r=await this.doFetch('/api/cotizaciones/'+id); if(!r.ok) return; const c=await r.json(); const idx=this.cotizaciones.findIndex(x=>String(x.id)===String(id)); if(idx>-1){ const updated={ id:c.id_cotizacion_pk, fecha:c.fecha_cotizacion?.split(' ')[0]||'', valido_hasta:c.valido_hasta, imponible:c.imponible, impuesto:c.impuesto, total_impuesto:c.total_impuesto, otros_cargos:c.otros_cargos, impuesto_otros: Number(c.impuesto_otros||0), anticipo_requerido:c.anticipo_requerido, total:c.total, es_activo: Boolean(Number(c.es_activo ?? 1)), cliente_id:(c.id_cliente_fk!=null? String(c.id_cliente_fk):''), cliente_nombre:this.normalizeClienteNombre(c.cliente_nombre || c.cliente?.empresa?.nombre_comercial || c.cliente?.empresa?.razon_social || ''), estado_nombre: (c.estado?.nombre || c.estado?.nombre_estado || null), estado_codigo: (c.estado?.codigo || null), estado_id: (c.id_estado_cotizacion_fk!=null? String(c.id_estado_cotizacion_fk):'') }; this.cotizaciones.splice(idx,1,updated); } }catch(e){} },
    async fetchEstadosCotizacion(){
        try{
            const r = await this.doFetch('/api/estados-cotizacion?per_page=100');
            if(!r.ok) throw new Error();
            const j = await r.json();
            const data = j.data || j || [];
            this.estadosCotizacion = data.map(e=>({ id: e.id_estado_cotizacion_pk || e.id || e.id_estado || e.id_pk || null, codigo: e.codigo, nombre: e.nombre, es_final: !!(e.es_final), orden: e.orden }))
                .filter(e=>e.id!=null);
            if(!this.estadosCotizacion.length){
                this.showToast('No hay estados de cotizacion disponibles', 'error');
            }
        }catch(e){
            this.estadosCotizacion = [];
            this.showToast('No se pudieron cargar los estados de cotizacion', 'error');
        }
    },
    resetForm(){ const today=new Date(); const plus30=new Date(today.getTime()+30*24*60*60*1000); const fmt=(d)=>d.toISOString().slice(0,10); this.form={ id:null, id_cliente_fk:'', id_estado_cotizacion_fk:'', fecha_cotizacion:fmt(today), valido_hasta:fmt(plus30), imponible:0, impuesto:0, total_impuesto:0, subtotal:0, otros_cargos:0, impuesto_otros:0, apply_isv_otros:false, anticipo_requerido:0, total:0, items:[ { descripcion:'', precio_unitario:0, cantidad:1, impuesto:0, aplicar_impuesto:false } ] }; },
    resetFormEmpty(){
        this.form = { id:null, id_cliente_fk:'', id_estado_cotizacion_fk:'', fecha_cotizacion:'', valido_hasta:'', imponible:0, impuesto:0, total_impuesto:0, subtotal:0, otros_cargos:0, impuesto_otros:0, apply_isv_otros:false, anticipo_requerido:0, total:0, items: [] };
    },
    openCreate(){
        this.resetFormEmpty();
        this.formCotizacion = { _touched: {} };
        this.generateCotizacionModal=true;
    },
    openEdit(c){
        const clienteId = c?.cliente_id != null ? String(c.cliente_id) : '';
        let estadoId = (c.estado_id ?? c.id_estado_cotizacion_fk ?? '');
        if(estadoId !== null && estadoId !== undefined && estadoId !== '') estadoId = String(estadoId);
            this.editForm = { ...c, id: c.id, id_cliente_fk: clienteId, id_estado_cotizacion_fk: estadoId, fecha_cotizacion: c.fecha, items: [ { descripcion:'', precio_unitario:0, cantidad:1, impuesto:0, id_producto_fk:null, aplicar_impuesto:false } ], apply_isv_otros: Boolean(Number(c.impuesto_otros||0) > 0), impuesto_otros: Number(c.impuesto_otros||0) };
        this._editOriginalItemIds = [];
        this.formEditCotizacion = { _touched: {} };
        this.editModal = true;
            this.$nextTick(()=>{
                try{
                    const cid = clienteId || '';
                    const eid = estadoId || '';
                    this.editForm.id_cliente_fk = '';
                    this.editForm.id_estado_cotizacion_fk = '';
                    setTimeout(()=>{
                        this.editForm.id_cliente_fk = cid;
                        this.editForm.id_estado_cotizacion_fk = eid;
                    }, 0);
                }catch(e){}
                this.fetchItemsForEdit(c.id);
            });
    },
    async createCotizacion(){
        this.saving=true;
        this.calcTotals(this.form);
        try{
            if(!this.form.fecha_cotizacion){
                this.showToast('La fecha de cotización es obligatoria', 'error');
                throw new Error('validation');
            }
            if(!this.form.id_estado_cotizacion_fk){
                this.showToast('El estado de la cotización es obligatorio', 'error');
                throw new Error('validation');
            }
            if(Number(this.form.total || 0) <= 0){
                this.showToast('El total de la cotizacion debe ser mayor a 0', 'error');
                throw new Error('validation');
            }
            const payload={ fecha_cotizacion:this.form.fecha_cotizacion, valido_hasta:this.form.valido_hasta, subtotal:this.form.subtotal, total:this.form.total, imponible:this.form.imponible, impuesto:this.form.total_impuesto, total_impuesto:this.form.total_impuesto, otros_cargos:this.form.otros_cargos||0, impuesto_otros:this.form.impuesto_otros||0, anticipo_requerido:this.form.anticipo_requerido||0, id_cliente_fk:this.form.id_cliente_fk, id_estado_cotizacion_fk: (this.form.id_estado_cotizacion_fk || undefined) };
            const r=await this.doFetch('/api/cotizaciones',{ method:'POST', body:JSON.stringify(payload) });
            const body = await r.json().catch(()=>({}));
            if(!r.ok){
                this.errors = body?.errors || {};
                const msgs = Object.values(this.errors).flat().map(m=>Array.isArray(m)?m.join('; '):String(m)).join(' \n');
                this.showToast(msgs || body?.message || body?.error || `No se creó (HTTP ${r.status})`, 'error');
                throw new Error('validation');
            }
            const j=body;
            const newId=j.data?.id_cotizacion_pk||j.id_cotizacion_pk;
            for(const it of this.form.items){ if(!it.descripcion) continue; await this.doFetch('/api/items-cotizacion',{ method:'POST', body:JSON.stringify({ descripcion:it.descripcion, precio_unitario:it.precio_unitario, cantidad:it.cantidad, impuesto:it.impuesto, id_cotizacion_fk:newId, id_producto_fk: it.id_producto_fk ?? null }) }); }
            this.showToast('Cotización creada'); this.generateCotizacionModal=false; this.fetchCotizaciones();
        }
        catch(e){
            if(e.message === 'validation'){
            }else{
                console.error('createCotizacion error', e);
                this.showToast('No se creó','error');
            }
        }
        finally{ this.saving=false; }
    },
    async updateCotizacion(){
        if(!this.editForm) return;
        this.saving=true;
        try{
            this.calcTotals(this.editForm);
            if(!this.editForm.id_estado_cotizacion_fk){
                this.showToast('El estado de la cotización es obligatorio', 'error');
                throw new Error('validation');
            }
            if(Number(this.editForm.total || 0) <= 0){
                this.showToast('El total de la cotizacion debe ser mayor a 0', 'error');
                throw new Error('validation');
            }
            let vHasta = this.editForm.valido_hasta;
            try{
                const todayStr = new Date().toISOString().slice(0,10);
                if(!vHasta || (new Date(vHasta) < new Date(todayStr))){ vHasta = todayStr; }
            }catch(e){}
            const payload={ valido_hasta:vHasta, subtotal:this.editForm.subtotal, total:this.editForm.total, imponible:this.editForm.imponible, impuesto:this.editForm.total_impuesto, total_impuesto:this.editForm.total_impuesto, otros_cargos:this.editForm.otros_cargos||0, impuesto_otros:this.editForm.impuesto_otros||0, anticipo_requerido:this.editForm.anticipo_requerido||0, id_cliente_fk:this.editForm.id_cliente_fk, id_estado_cotizacion_fk: (this.editForm.id_estado_cotizacion_fk || undefined) };
            const r=await this.doFetch('/api/cotizaciones/'+this.editForm.id,{ method:'PUT', body:JSON.stringify(payload) });
            if(r.status===422){
                try{
                    const body=await r.json();
                    this.errors = body.errors || body;
                    const msgs = Object.values(this.errors).flat().map(m=>Array.isArray(m)?m.join('; '):String(m)).join(' \n');
                    this.showToast(msgs || 'Errores de validación', 'error');
                }catch(e){ this.showToast('Errores de validación (422)', 'error'); }
                throw new Error('validation');
            }
            if(!r.ok) throw new Error('cotizacion update failed');

            const currentItems = Array.isArray(this.editForm.items) ? this.editForm.items : [];
            const originalIds = Array.isArray(this._editOriginalItemIds) ? this._editOriginalItemIds : [];
            const currentIds = currentItems.filter(it=>it.id_item).map(it=>it.id_item);

            const toDelete = originalIds.filter(id=>!currentIds.includes(id));
            for(const id of toDelete){
                try{ await this.doFetch('/api/items-cotizacion/'+id, { method: 'DELETE' }); }catch(e){ console.error('failed deleting item', id, e); }
            }

            for(const it of currentItems){
                const payloadItem = { descripcion: it.descripcion, precio_unitario: it.precio_unitario, cantidad: it.cantidad, impuesto: it.impuesto, id_cotizacion_fk: this.editForm.id, id_producto_fk: it.id_producto_fk ?? null, total: (Number(it.precio_unitario||0)*Number(it.cantidad||0) + Number(it.impuesto||0)) };
                try{
                    if(it.id_item){
                        await this.doFetch('/api/items-cotizacion/'+it.id_item, { method: 'PUT', body: JSON.stringify(payloadItem) });
                    }else{
                        await this.doFetch('/api/items-cotizacion', { method: 'POST', body: JSON.stringify(payloadItem) });
                    }
                }catch(e){ console.error('failed saving item', it, e); }
            }

            this.showToast('Actualizada');
            this.editModal=false;
            this.fetchCotizaciones();
        }catch(e){
            console.error('updateCotizacion error', e);
            this.showToast('No se actualizó','error');
        }finally{ this.saving=false; }
    },
    async deleteCotizacion(){ if(!this.selectedItem) return; try{ const r=await this.doFetch('/api/cotizaciones/'+this.selectedItem,{ method:'DELETE' }); if(!r.ok) throw new Error(); this.showToast('Cotización inactivada'); await this.fetchCotizaciones(); }catch(e){ this.showToast('No se inactivó','error'); } finally{ this.deleteModal=false; this.selectedItem=null; } },
    async restoreCotizacion(id){ if(!id) return; try{ const r=await this.doFetch('/api/cotizaciones/'+id+'/restore',{ method:'PUT' }); if(!r.ok) throw new Error(); this.showToast('Cotización restaurada'); await this.fetchCotizaciones(); }catch(e){ this.showToast('No se restauró','error'); } },
    handleConfirmAction(){
        if(this.confirmMode === 'restore'){
            const id = this.selectedRestoreItem?.id;
            this.restoreCotizacion(id);
            this.selectedRestoreItem = null;
            this.restoreModal = false;
            return;
        }
        this.deleteCotizacion();
    },
    init(){
    this.ensureAuth();
    this.fetchClientes(); this.fetchEstadosCotizacion(); this.fetchCotizaciones();
        const debounce=(fn,ms=400)=>{let h;return(...a)=>{clearTimeout(h);h=setTimeout(()=>fn(...a),ms);};};
        this.$watch('filters.search',debounce(()=>{ this.fetchCotizaciones(); this.currentPage = 1; }));
        this.$watch('ordenarPor',debounce(()=>{ this.fetchCotizaciones(); this.currentPage = 1; }));
        this.$watch('filters.cliente',debounce(()=>{ this.fetchCotizaciones(); this.currentPage = 1; }));
        this.$watch('filters.estadoRegistro',debounce(()=>{ this.fetchCotizaciones(); this.currentPage = 1; }));
        this.$watch('catalogSearch',debounce(()=>{ if(this.catalogModal){ this.fetchCatalogItems(); } }, 400));
    this.$watch('generateCotizacionModal', val=>{ if(!val){ /* wait for Alpine to finish modal closing animation, then clear form to avoid flicker */ setTimeout(()=>{ this.resetFormEmpty(); this.formCotizacion = { _touched: {} }; }, 220); } });
        this.$watch('editModal', val=>{ if(!val){ this.editForm = null; this.formEditCotizacion = { _touched: {} }; } });
    this.$watch('catalogModal', val=>{ if(!val){ this.catalogSelectedUser = {}; this.catalogExisting = {}; this.catalogItems = []; this.catalogSearch = ''; } });
        this.$watch('itemsModal', val=>{ if(!val){ this.items = []; this.currentCotizacionId = null; this.itemMode='list'; this.itemForm = { descripcion:'', precio_unitario:0, cantidad:1, impuesto:0 }; this.itemEditId = null; } });
    }
        
}" x-init="init()" @modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="handleConfirmAction()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Cotizaciones</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            <div class="flex flex-col gap-2 w-full">
                <div class="w-full">
                    @include('partials.filtros-generales', [
                    'searchModel' => 'filters.search',
                    'ordenarOptions' => [
                    'fecha' => 'Fecha Cotización',
                    'valido' => 'Válida Hasta',
                    'total' => 'Total',
                    'subtotal' => 'Subtotal'
                    ]
                    ])
                </div>
                <div class="w-full">
                    <select x-model="filters.cliente"
                        class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                        <option value="">Todos los clientes</option>
                        <template x-for="cl in clientes" :key="cl.id">
                            <option :value="cl.id" x-text="cl.nombre"></option>
                        </template>
                    </select>
                </div>
                <div class="w-full">
                    <select x-model="filters.estadoRegistro"
                        class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                        <option value="activos">Solo activas</option>
                        <option value="inactivos">Solo inactivas</option>
                        <option value="todos">Activas e inactivas</option>
                    </select>
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                @perm(['Cotizaciones','Gestión de Cotizaciones','Gestion de Cotizaciones'], 'insercion')
                <button @click="openCreate()"
                    class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                    <i class="fas fa-plus"></i> Generar Cotización
                </button>
                @else
                <button disabled
                    class="w-full sm:w-auto bg-gray-300 text-gray-600 px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm cursor-not-allowed"
                    title="Sin permiso para crear">
                    <i class="fas fa-plus"></i> Generar Cotización
                </button>
                @endperm
            </div>
        </x-slot>

        <x-slot name="table">
            <div class="overflow-x-auto">
                <table
                    class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse table-white-dividers">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left">Codigo de Cotización</th>
                            <th class="py-2 px-4 text-left">Cliente</th>
                            <th class="py-2 px-4 text-left">Fecha Cotización</th>
                            <th class="py-2 px-4 text-left">Válida Hasta</th>
                            <th class="py-2 px-4 text-left">Imponible</th>
                            <th class="py-2 px-4 text-left">Impuesto</th>
                            <th class="py-2 px-4 text-left">Total Imp.</th>
                            <th class="py-2 px-4 text-left">Otros Cargos</th>
                            <th class="py-2 px-4 text-left">Anticipo</th>
                            <th class="py-2 px-4 text-left">Total</th>
                            <th class="py-2 px-4 text-left">Estado</th>
                            <th class="py-2 px-4 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="c in paginatedCotizaciones()" :key="c.id">
                            <tr class="border-b dark:border-gray-700 nunito-regular">
                                <td class="py-2 px-4" x-text="formatCotId(c)"></td>
                                <td class="py-2 px-4" x-text="c.cliente_nombre || 'Sin cliente'"></td>
                                <td class="py-2 px-4" x-text="c.fecha"></td>
                                <td class="py-2 px-4" x-text="c.valido_hasta"></td>
                                <td class="py-2 px-4 text-right"
                                    x-text="'L.\u00A0'+(Number(c.imponible ?? 0)).toLocaleString('es-HN', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                                </td>
                                <td class="py-2 px-4 text-right"
                                    x-text="'L.\u00A0'+(Number(c.impuesto ?? 0)).toLocaleString('es-HN', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                                </td>
                                <td class="py-2 px-4 text-right"
                                    x-text="'L.\u00A0'+(Number(c.total_impuesto ?? 0)).toLocaleString('es-HN', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                                </td>
                                <td class="py-2 px-4 text-right"
                                    x-text="'L.\u00A0'+(Number(c.otros_cargos ?? 0)).toLocaleString('es-HN', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                                </td>
                                <td class="py-2 px-4 text-right"
                                    x-text="'L.\u00A0'+(Number(c.anticipo_requerido ?? 0)).toLocaleString('es-HN', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                                </td>
                                <td class="py-2 px-4 text-right"
                                    x-text="'L.\u00A0'+(Number(c.total ?? 0)).toLocaleString('es-HN', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                                </td>
                                <td class="py-2 px-4">
                                    <span class="px-2 py-1 rounded text-xs font-semibold" :class="{
                                              'bg-slate-200 text-slate-700': !c.es_activo,
                                              'bg-amber-200 text-amber-800': c.estado_codigo==='BRD' || c.estado_codigo==='PEN' || (String(c.estado_nombre||'').toLowerCase().includes('pend')),
                                              'bg-green-200 text-green-800': c.estado_codigo==='APB' || (String(c.estado_nombre||'').toLowerCase().includes('aproba')),
                                              'bg-red-200 text-red-800':   c.estado_codigo==='REC' || (String(c.estado_nombre||'').toLowerCase().includes('rech')),
                                              'bg-blue-200 text-blue-800':  c.estado_codigo==='VEN' || (String(c.estado_nombre||'').toLowerCase().includes('venc')),
                                              'bg-gray-200 text-gray-800': !c.estado_codigo && !c.estado_nombre
                                          }" x-text="!c.es_activo ? 'Inactiva' : (c.estado_nombre || '—')"></span>
                                </td>
                                <td class="py-2 px-4 flex items-center gap-2">
                                    <a :href="'/admin/detalle-cotizacion?id='+c.id" target="_blank"
                                        class="px-3 py-1 text-xs bg-emerald-600 text-white rounded hover:bg-emerald-700 flex items-center gap-1"><i
                                            class='fas fa-eye'></i> Ver</a>
                                    @perm(['Cotizaciones','Gestión de Cotizaciones','Gestion de Cotizaciones'],
                                    'actualizacion')
                                    <a href="#" @click.prevent="openEdit(c)" class="text-blue-500 hover:text-blue-700"
                                        title="Editar"><i class="fas fa-edit"></i></a>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para editar"><i
                                            class="fas fa-edit"></i></span>
                                    @endperm
                                    @perm(['Cotizaciones','Gestión de Cotizaciones','Gestion de Cotizaciones'],
                                    'eliminacion')
                                    <a x-show="c.es_activo" href="#" @click.prevent="confirmMode='delete'; deleteModal=true; selectedItem=c.id"
                                        class="text-red-500 hover:text-red-700" title="Inactivar"><i
                                            class="fas fa-trash"></i></a>
                                    <a x-show="!c.es_activo" href="#" @click.prevent="confirmMode='restore'; selectedRestoreItem=c; restoreModal=true"
                                        class="text-emerald-600 hover:text-emerald-700" title="Restaurar"><i
                                            class="fas fa-undo"></i></a>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para eliminar">
                                        <i class="fas fa-trash"></i>
                                    </span>
                                    @endperm
                                </td>
                            </tr>
                        </template>
                        <tr x-show="!cotizaciones.length && !loading">
                            <td colspan="12" class="text-center text-gray-500 py-4">Sin datos</td>
                        </tr>
                        <tr x-show="loading">
                            <td colspan="12" class="text-center text-gray-500 py-4 animate-pulse">Cargando...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-slot>

        <x-slot name="cards">
            <div class="space-y-4">
                <template x-if="loading">
                    <div class="p-4 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando
                        cotizaciones...</div>
                </template>
                <template x-if="!loading && cotizaciones.length === 0">
                    <div class="p-4 text-center text-gray-500">No hay cotizaciones para mostrar.</div>
                </template>
                <template x-for="c in paginatedCotizaciones()" :key="c.id">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3 border border-black dark:border-gray-600">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white" x-text="'Cotización #' + c.id">
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400"
                                    x-text="c.cliente_nombre || 'Cliente sin nombre'"></p>
                            </div>
                            <p class="text-lg font-bold text-gray-800 dark:text-white"
                                x-text="'L. ' + (Number(c.total ?? 0)).toLocaleString('es-HN', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                            </p>
                        </div>
                        <div class="flex justify-start">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold" :class="{
                                                                        'bg-slate-200 text-slate-700': !c.es_activo,
                                                                        'bg-amber-200 text-amber-800': c.estado_codigo==='BRD' || c.estado_codigo==='PEN' || (String(c.estado_nombre||'').toLowerCase().includes('pend')),
                                                                        'bg-green-200 text-green-800': c.estado_codigo==='APB' || (String(c.estado_nombre||'').toLowerCase().includes('aproba')),
                                                                        'bg-red-200 text-red-800':   c.estado_codigo==='REC' || (String(c.estado_nombre||'').toLowerCase().includes('rech')),
                                                                        'bg-blue-200 text-blue-800':  c.estado_codigo==='VEN' || (String(c.estado_nombre||'').toLowerCase().includes('venc')),
                                                                        'bg-gray-200 text-gray-800': !c.estado_codigo && !c.estado_nombre
                                                                    }" x-text="!c.es_activo ? 'Inactiva' : (c.estado_nombre || '—')"></span>
                        </div>
                        <p class="text-xs text-gray-400">
                            Fecha: <span x-text="c.fecha"></span> | Válida hasta: <span x-text="c.valido_hasta"></span>
                        </p>
                        <div class="flex justify-end flex-wrap gap-2 pt-3 border-t dark:border-gray-700">
                            <a :href="'/admin/detalle-cotizacion?id='+c.id" target="_blank"
                                class="px-3 py-1 text-xs bg-emerald-600 text-white rounded hover:bg-emerald-700 flex items-center gap-1">
                                <i class='fas fa-eye'></i> Ver
                            </a>
                            <button @click.prevent="openItems(c)"
                                class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700 flex items-center gap-1">
                                <i class="fas fa-database"></i> Items
                            </button>
                            @perm(['Cotizaciones','Gestión de Cotizaciones','Gestion de Cotizaciones'], 'actualizacion')
                            <button @click.prevent="openEdit(c)"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button disabled
                                class="px-3 py-1 text-xs bg-gray-300 text-gray-600 rounded cursor-not-allowed flex items-center gap-1"
                                title="Sin permiso para editar">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm
                            @perm(['Cotizaciones','Gestión de Cotizaciones','Gestion de Cotizaciones'], 'eliminacion')
                            <button x-show="c.es_activo" @click.prevent="confirmMode='delete'; deleteModal=true; selectedItem=c.id"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                                <i class="fas fa-trash"></i> Inactivar
                            </button>
                            <button x-show="!c.es_activo" @click.prevent="confirmMode='restore'; selectedRestoreItem=c; restoreModal=true"
                                class="px-3 py-1 text-xs bg-emerald-600 text-white rounded hover:bg-emerald-700 flex items-center gap-1">
                                <i class="fas fa-undo"></i> Restaurar
                            </button>
                            @else
                            <button disabled
                                class="px-3 py-1 text-xs bg-gray-300 text-gray-600 rounded cursor-not-allowed flex items-center gap-1"
                                title="Sin permiso para eliminar">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>

    </x-responsive-table>

    <x-pagination />

    <x-admin.form-modal class="nunito-bold" modalName="itemsModal" title="Items de la Cotización" submitLabel="Guardar"
        formId="items-manager" maxWidth="max-w-5xl">
        <div class="space-y-4 p-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-sm text-gray-600 dark:text-gray-300 nunito-regular">
                    Cotización ID: <span class="font-semibold" x-text="currentCotizacionId"></span>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                    <input type="text" placeholder="Buscar descripción..." x-model="itemsSearch"
                        class="w-full sm:w-64 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-gray-200 nunito-regular focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                    @perm(['Cotizaciones','Gestión de Cotizaciones','Gestion de Cotizaciones'], 'insercion')
                    <button type="button" @click="openNewItem()"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg nunito-regular w-full sm:w-auto">
                        <i class="fas fa-plus mr-1"></i> Nuevo
                    </button>
                    @else
                    <button type="button" disabled
                        class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg nunito-regular w-full sm:w-auto cursor-not-allowed"
                        title="Sin permiso para agregar items">
                        <i class="fas fa-plus mr-1"></i> Nuevo
                    </button>
                    @endperm
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
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800">
                    <div class="space-y-4 sm:col-span-2">
                        <div>
                            <label for="item_descripcion"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Descripción
                                Del Producto o Servicio</label>
                            <input type="text" id="item_descripcion" x-model="itemForm.descripcion"
                                class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-gray-200 nunito-regular focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                            <template x-if="itemErrors.descripcion">
                                <p class="text-xs text-red-500 mt-1" x-text="itemErrors.descripcion[0]"></p>
                            </template>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="item_precio"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Precio
                                    Unit.</label>
                                <input type="number" step="0.01" id="item_precio"
                                    x-model.number="itemForm.precio_unitario"
                                    class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-gray-200 nunito-regular focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-right" />
                                <template x-if="itemErrors.precio_unitario">
                                    <p class="text-xs text-red-500 mt-1" x-text="itemErrors.precio_unitario[0]"></p>
                                </template>
                            </div>
                            <div>
                                <label for="item_cantidad"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Cantidad</label>
                                <input type="number" step="0.01" id="item_cantidad" x-model.number="itemForm.cantidad"
                                    class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-gray-200 nunito-regular focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-right" />
                                <template x-if="itemErrors.cantidad">
                                    <p class="text-xs text-red-500 mt-1" x-text="itemErrors.cantidad[0]"></p>
                                </template>
                            </div>
                            <div>
                                <label for="item_impuesto"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Impuesto</label>
                                <input type="number" step="0.01" id="item_impuesto" x-model.number="itemForm.impuesto"
                                    class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-gray-200 nunito-regular focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-right" />
                                <template x-if="itemErrors.impuesto">
                                    <p class="text-xs text-red-500 mt-1" x-text="itemErrors.impuesto[0]"></p>
                                </template>
                            </div>
                        </div>
                        <div>
                            <label for="item_total"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Total</label>
                            <input type="number" id="item_total" :value="calcItemTotal(itemForm)" disabled
                                class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 nunito-regular text-right" />
                        </div>
                        <div class="text-right">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg nunito-regular"
                                x-text="itemMode === 'create' ? 'Guardar Item' : 'Actualizar Item'"></button>
                        </div>
                    </div>
                </div>
            </template>

            <div
                class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden bg-white dark:bg-gray-800">
                <div class="block md:hidden space-y-3 p-4">
                    <template x-if="itemsLoading">
                        <div
                            class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 text-center text-gray-500 dark:text-gray-300 nunito-regular">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Cargando...
                        </div>
                    </template>
                    <template x-if="!itemsLoading && items.length === 0">
                        <div
                            class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 text-center text-gray-500 dark:text-gray-300 nunito-regular">
                            Sin items
                        </div>
                    </template>
                    <template x-for="it in items" :key="it.id">
                        <div
                            class="border border-gray-200 dark:border-gray-600 rounded-lg p-3 bg-white dark:bg-gray-800">
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Descripción</span>
                                    <span
                                        class="text-sm text-gray-600 dark:text-gray-300 nunito-regular breack text-end"
                                        x-text="it.descripcion"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Precio
                                        Unit.</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300 nunito-regular"
                                        x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(it.precio_unitario)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Cantidad</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300 nunito-regular"
                                        x-text="Number(it.cantidad).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Impuesto</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300 nunito-regular"
                                        x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(it.impuesto)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200 nunito-bold">Total</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300 nunito-regular"
                                        x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(it.total)"></span>
                                </div>
                                <div class="flex justify-end gap-2 pt-2">
                                    @perm(['Cotizaciones','Gestión de Cotizaciones','Gestion de Cotizaciones'],
                                    'actualizacion')
                                    <button @click.prevent="openEditItem(it)" class="text-blue-500 hover:text-blue-600"
                                        title="Editar item">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed"
                                        title="Sin permiso para editar items">
                                        <i class="fas fa-edit"></i>
                                    </span>
                                    @endperm
                                    @perm(['Cotizaciones','Gestión de Cotizaciones','Gestion de Cotizaciones'],
                                    'eliminacion')
                                    <button @click.prevent="deleteItem(it)" class="text-red-500 hover:text-red-600"
                                        title="Eliminar item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed"
                                        title="Sin permiso para eliminar items">
                                        <i class="fas fa-trash"></i>
                                    </span>
                                    @endperm
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full text-xs bg-white dark:bg-gray-800">
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
                                    <td colspan="6"
                                        class="py-4 text-center text-gray-500 dark:text-gray-300 nunito-regular">
                                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando...
                                    </td>
                                </tr>
                            </template>
                            <template x-if="!itemsLoading && items.length === 0">
                                <tr>
                                    <td colspan="6"
                                        class="py-4 text-center text-gray-500 dark:text-gray-300 nunito-regular">
                                        Sin items
                                    </td>
                                </tr>
                            </template>
                            <template x-for="it in items" :key="it.id">
                                <tr
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="py-2 px-4 text-gray-600 dark:text-gray-300 nunito-regular"
                                        x-text="it.descripcion"></td>
                                    <td class="py-2 px-4 text-right text-gray-600 dark:text-gray-300 nunito-regular"
                                        x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(it.precio_unitario)">
                                    </td>
                                    <td class="py-2 px-4 text-right text-gray-600 dark:text-gray-300 nunito-regular"
                                        x-text="Number(it.cantidad).toFixed(2)"></td>
                                    <td class="py-2 px-4 text-right text-gray-600 dark:text-gray-300 nunito-regular"
                                        x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(it.impuesto)">
                                    </td>
                                    <td class="py-2 px-4 text-right text-gray-600 dark:text-gray-300 nunito-regular"
                                        x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(it.total)">
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            @perm(['Cotizaciones','Gestión de Cotizaciones','Gestion de Cotizaciones'],
                                            'actualizacion')
                                            <button @click.prevent="openEditItem(it)"
                                                class="text-blue-500 hover:text-blue-600" title="Editar item">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @else
                                            <span class="text-gray-400 cursor-not-allowed"
                                                title="Sin permiso para editar items">
                                                <i class="fas fa-edit"></i>
                                            </span>
                                            @endperm
                                            @perm(['Cotizaciones','Gestión de Cotizaciones','Gestion de Cotizaciones'],
                                            'eliminacion')
                                            <button @click.prevent="deleteItem(it)"
                                                class="text-red-500 hover:text-red-600" title="Eliminar item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @else
                                            <span class="text-gray-400 cursor-not-allowed"
                                                title="Sin permiso para eliminar items">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                            @endperm
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

            <div> {{-- Este div ahora ocupa todo el ancho --}}
                <label for="clienteId" class="block text-sm font-medium text-gray-700 nunito-bold">
                    Cliente</label>
                <select id="clienteId" name="clienteId" x-model="form.id_cliente_fk"
                    @change="formCotizacion._touched.cliente = true"
                    :class="formCotizacion._touched && formCotizacion._touched.cliente && !form.id_cliente_fk ? 'border-red-500' : 'border-gray-400'"
                    class="mt-1 block w-full rounded-md border shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                    <option value="">Seleccione un cliente</option>
                    <template x-for="cl in clientes" :key="cl.id">
                        <option :value="String(cl.id)" x-text="cl.nombre"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formCotizacion._touched && formCotizacion._touched.cliente && !form.id_cliente_fk ? 'text-red-500' : ''">
                    Requerido.
                </small>
            </div>

            <div>
                <label for="estadoId" class="block text-sm font-medium text-gray-700 nunito-bold">Estado de la
                    Solicitud</label>
                <select id="estadoId" name="estadoId" x-model="form.id_estado_cotizacion_fk"
                    @change="formCotizacion._touched.estado = true" :disabled="!estadosCotizacion.length"
                    :class="formCotizacion._touched && formCotizacion._touched.estado && !form.id_estado_cotizacion_fk ? 'border-red-500' : 'border-gray-400'"
                    class="mt-1 block w-full rounded-md border shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                    <option value="" disabled
                        x-text="!estadosCotizacion.length ? 'Cargando estados...' : 'Seleccione un estado'"></option>
                    <template x-for="e in estadosCotizacion" :key="e.id">
                        <option :value="String(e.id)" x-text="e.nombre"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formCotizacion._touched && formCotizacion._touched.estado && !form.id_estado_cotizacion_fk ? 'text-red-500' : ''">
                    Requerido.
                </small>
            </div>

            <div> {{-- Este div ahora ocupa todo el ancho --}}
                <label for="fechaCotizacion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de
                    Cotización</label>
                <input type="date" id="fechaCotizacion" name="fechaCotizacion" x-model="form.fecha_cotizacion"
                    @input="formCotizacion._touched.fecha_cotizacion = true"
                    @blur="formCotizacion._touched.fecha_cotizacion = true"
                    :class="formCotizacion._touched && formCotizacion._touched.fecha_cotizacion && !form.fecha_cotizacion ? 'border-red-500' : 'border-gray-400'"
                    class="mt-1 block w-full rounded-md border shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formCotizacion._touched && formCotizacion._touched.fecha_cotizacion && !form.fecha_cotizacion ? 'text-red-500' : ''">
                    Requerido.
                </small>
            </div>

            <div> {{-- Este div ahora ocupa todo el ancho --}}
                <label for="validoHasta" class="block text-sm font-medium text-gray-700 nunito-bold">Válido
                    Hasta</label>
                <input type="date" id="validoHasta" name="validoHasta" x-model="form.valido_hasta"
                    @input="formCotizacion._touched.valido_hasta = true"
                    @blur="formCotizacion._touched.valido_hasta = true"
                    class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                <small class="block mt-1 text-sm text-gray-500">
                    Opcional.
                </small>
            </div>


            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700 nunito-bold">Producto o Servicio</label>

                <div class="max-h-48 overflow-y-auto pr-2">
                    <template x-for="(description, index) in form.items" :key="index">
                        <div class="mb-3 p-3 rounded-md bg-white/5">
                            <div class="flex justify-between items-start">
                                <div class="w-full">
                                    <textarea x-model="description.descripcion"
                                        placeholder="Descripción Del Producto o Servicio (Opcional)"
                                        @input="(e)=>{ e.target.style.height='auto'; e.target.style.height = e.target.scrollHeight + 'px'; calcTotals(form); formCotizacion._touched[`descripcion_${index}`] = true; }"
                                        @blur="formCotizacion._touched[`descripcion_${index}`] = true"
                                        x-bind:title="description.descripcion" maxlength="500"
                                        :class="formCotizacion._touched && formCotizacion._touched[`descripcion_${index}`] && description.descripcion && description.descripcion.length > 500 ? 'border-red-500' : 'border-gray-600'"
                                        class="w-full rounded-md border dark:border-gray-700 bg-transparent focus:border-blue-500 focus:ring-blue-500 nunito-regular p-2 text-sm resize-none overflow-hidden"></textarea>
                                    <small class="block mt-1 text-xs text-gray-500"
                                        :class="formCotizacion._touched && formCotizacion._touched[`descripcion_${index}`] && description.descripcion && description.descripcion.length > 500 ? 'text-red-500' : ''">
                                        Opcional. Máximo 500 caracteres.
                                    </small>
                                    <div class="flex items-center gap-3 mt-2">
                                        <label class="inline-flex items-center text-sm text-gray-400">
                                            <input type="checkbox" class="mr-2 h-4 w-4"
                                                x-model="description.aplicar_impuesto" @change="calcTotals(form)" />
                                            Aplicar ISV 15%
                                        </label>
                                        <div class="text-sm text-gray-500 ml-4">Impuesto: <span
                                                x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(description.impuesto || 0)"></span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" @click="removeItem(index,'form')"
                                    class="ml-3 inline-flex items-center justify-center w-8 h-8 rounded bg-red-600 hover:bg-red-700 text-white"
                                    title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-3">
                                <div>
                                    <label class="text-xs text-gray-400">Precio Unit. <span
                                            class="text-red-400">*</span></label>
                                    <input type="number" step="0.01" min="0"
                                        x-model.number="description.precio_unitario"
                                        @input="calcTotals(form); formCotizacion._touched[`precio_${index}`] = true"
                                        @blur="formCotizacion._touched[`precio_${index}`] = true"
                                        :class="formCotizacion._touched && formCotizacion._touched[`precio_${index}`] && (description.precio_unitario === null || description.precio_unitario === undefined || description.precio_unitario < 0) ? 'border-red-500' : 'border-gray-600'"
                                        class="w-full rounded-md border dark:border-gray-700 p-2 text-sm text-right" />
                                    <small class="block text-xs text-gray-500 mt-1"
                                        :class="formCotizacion._touched && formCotizacion._touched[`precio_${index}`] && (description.precio_unitario === null || description.precio_unitario === undefined || description.precio_unitario < 0) ? 'text-red-500' : ''">
                                        Requerido. Mín: 0
                                    </small>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-400">Cantidad <span
                                            class="text-red-400">*</span></label>
                                    <input type="number" step="0.01" min="1" x-model.number="description.cantidad"
                                        @input="calcTotals(form); formCotizacion._touched[`cantidad_${index}`] = true"
                                        @blur="formCotizacion._touched[`cantidad_${index}`] = true"
                                        :class="formCotizacion._touched && formCotizacion._touched[`cantidad_${index}`] && (description.cantidad === null || description.cantidad === undefined || description.cantidad <= 0) ? 'border-red-500' : 'border-gray-600'"
                                        class="w-full rounded-md border dark:border-gray-700 p-2 text-sm text-right" />
                                    <small class="block text-xs text-gray-500 mt-1"
                                        :class="formCotizacion._touched && formCotizacion._touched[`cantidad_${index}`] && (description.cantidad === null || description.cantidad === undefined || description.cantidad <= 0) ? 'text-red-500' : ''">
                                        Requerido. Mín: 1
                                    </small>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-400">Impuesto</label>
                                    <input type="number" step="0.01" x-model.number="description.impuesto"
                                        @input="calcTotals(form)"
                                        class="w-full rounded-md border border-gray-600 dark:border-gray-700 p-2 text-sm text-right" />
                                </div>
                                <div>
                                    <label class="text-xs text-gray-400">Total</label>
                                    <input type="text"
                                        :value="(Number(description.precio_unitario||0)*Number(description.cantidad||0)+Number(description.impuesto||0)).toFixed(2)"
                                        disabled
                                        class="w-full rounded-md border border-gray-200 bg-gray-100 dark:bg-gray-700 p-2 text-sm text-right" />
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="flex gap-2 mt-2">
                    <button type="button" @click="addItem('form')"
                        class="mt-2 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm nunito-regular">
                        <i class="fas fa-plus"></i> Añadir Prod/Serv
                    </button>
                    <button type="button" @click="openCatalog('form')"
                        class="mt-2 bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-sm nunito-regular">
                        <i class="fas fa-list"></i> Seleccionar del catálogo
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Este es el nuevo contenedor para la fila de 3 elementos --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Imponible</label>
                    <input type="text"
                        :value="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(form.imponible || 0)"
                        readonly
                        class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-50 dark:bg-gray-800 p-2 text-sm text-right font-semibold" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Total Impuesto</label>
                    <input type="text"
                        :value="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(form.total_impuesto || 0)"
                        readonly
                        class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-50 dark:bg-gray-800 p-2 text-sm text-right" />
                </div>

                <div>
                    <label for="otrosCargos" class="block text-sm font-medium text-gray-700 nunito-bold">Otros
                        Cargos</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-600">L.</span>
                        <input type="number" id="otrosCargos" name="otrosCargos" x-model.number="form.otros_cargos"
                            @input="calcTotals(form)"
                            class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1 pl-8 text-right" />
                    </div>
                    <div class="flex items-center gap-3 mt-2">
                        <label class="inline-flex items-center text-sm text-gray-400">
                            <input type="checkbox" class="mr-2 h-4 w-4" x-model="form.apply_isv_otros"
                                @change="calcTotals(form)" />
                            Aplicar ISV 15% a Otros Cargos
                        </label>
                        <div class="text-sm text-gray-500 ml-4">Impuesto:
                            <span
                                x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format((form.apply_isv_otros ? (Number(form.otros_cargos||0) * 0.15) : 0))"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 nunito-bold">Total</label>
                <div
                    class="mt-1 block w-full rounded-md border border-blue-700 bg-blue-600 text-white p-3 text-lg font-bold text-right">
                    <span
                        x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(form.total || 0)"></span>
                </div>
            </div>
        </div>
    </x-admin.form-modal>

    <x-admin.edit-modal class="nunito-bold" modalName="editModal" title="Editar Cotización" submitLabel="Actualizar"
        itemToEdit="editForm" maxWidth="max-w-4xl" formId="editCotizacionForm">
        <template x-if="editForm">
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4"> {{-- Contenedor principal para organizar en filas --}}

                    <div>
                        <label for="editClienteId" class="block text-sm font-medium text-gray-700 nunito-bold">
                            Cliente</label>
                        <select id="editClienteId" name="clienteId" x-model="editForm.id_cliente_fk"
                            @change="formEditCotizacion._touched.cliente = true"
                            :class="formEditCotizacion._touched && formEditCotizacion._touched.cliente && !editForm.id_cliente_fk ? 'border-red-500' : 'border-gray-400'"
                            class="mt-1 block w-full rounded-md border shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                            <option value="">Seleccione un cliente</option>
                            <template x-for="cl in clientes" :key="cl.id">
                                <option :value="String(cl.id)" x-text="cl.nombre"></option>
                            </template>
                        </select>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEditCotizacion._touched && formEditCotizacion._touched.cliente && !editForm.id_cliente_fk ? 'text-red-500' : ''">
                            Requerido.
                        </small>
                    </div>

                    <div>
                        <label for="editEstadoId" class="block text-sm font-medium text-gray-700 nunito-bold">Estado de
                            la Solicitud</label>
                        <select id="editEstadoId" name="editEstadoId" x-model="editForm.id_estado_cotizacion_fk"
                            @change="formEditCotizacion._touched.estado = true" :disabled="!estadosCotizacion.length"
                            :class="formEditCotizacion._touched && formEditCotizacion._touched.estado && !editForm.id_estado_cotizacion_fk ? 'border-red-500' : 'border-gray-400'"
                            class="mt-1 block w-full rounded-md border shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                            <option value="" disabled
                                x-text="!estadosCotizacion.length ? 'Cargando estados...' : 'Seleccione un estado'">
                            </option>
                            <template x-for="e in estadosCotizacion" :key="e.id">
                                <option :value="String(e.id)" x-text="e.nombre"></option>
                            </template>
                        </select>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEditCotizacion._touched && formEditCotizacion._touched.estado && !editForm.id_estado_cotizacion_fk ? 'text-red-500' : ''">
                            Requerido.
                        </small>
                    </div>

                    <div>
                        <label for="editFechaCotizacion"
                            class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                            de
                            Cotización</label>
                        <input type="date" id="editFechaCotizacion" name="fechaCotizacion" disabled
                            class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-100 shadow-sm nunito-regular p-1"
                            x-model="editForm.fecha">
                    </div>

                    <div>
                        <label for="editValidoHasta" class="block text-sm font-medium text-gray-700 nunito-bold">Válido
                            Hasta</label>
                        <input type="date" id="editValidoHasta" name="validoHasta"
                            @input="formEditCotizacion._touched.valido_hasta = true"
                            @blur="formEditCotizacion._touched.valido_hasta = true"
                            class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                            x-model="editForm.valido_hasta">
                        <small class="block mt-1 text-sm text-gray-500">
                            Opcional.
                        </small>
                    </div>

                    <div class="col-span-1"> {{-- Aquí la clase col-span-1 es redundante pero no hace daño --}}
                        <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción Del Producto o
                            Servicio</label>

                        <div class="max-h-48 overflow-y-auto pr-2">
                            <template x-for="(item, index) in (editForm.items || [])"
                                :key="item.id_item || item.id_producto_fk || index">
                                <div class="mb-3 p-3 rounded-md bg-white/5">
                                    <div class="flex justify-between items-start">
                                        <div class="w-full">
                                            <textarea x-model="item.descripcion"
                                                placeholder="Descripción Del Producto o Servicio (Opcional)"
                                                @input="(e)=>{ e.target.style.height='auto'; e.target.style.height = e.target.scrollHeight + 'px'; calcTotals(editForm); formEditCotizacion._touched[`edit_descripcion_${index}`] = true; }"
                                                @blur="formEditCotizacion._touched[`edit_descripcion_${index}`] = true"
                                                x-bind:title="item.descripcion" maxlength="500"
                                                :class="formEditCotizacion._touched && formEditCotizacion._touched[`edit_descripcion_${index}`] && item.descripcion && item.descripcion.length > 500 ? 'border-red-500' : 'border-gray-600'"
                                                class="w-full rounded-md border dark:border-gray-700 bg-transparent focus:border-blue-500 focus:ring-blue-500 nunito-regular p-2 text-sm resize-none overflow-hidden"></textarea>
                                            <small class="block mt-1 text-xs text-gray-500"
                                                :class="formEditCotizacion._touched && formEditCotizacion._touched[`edit_descripcion_${index}`] && item.descripcion && item.descripcion.length > 500 ? 'text-red-500' : ''">
                                                Opcional. Máximo 500 caracteres.
                                            </small>
                                            <div class="flex items-center gap-3 mt-2">
                                                <label class="inline-flex items-center text-sm text-gray-400">
                                                    <input type="checkbox" class="mr-2 h-4 w-4"
                                                        x-model="item.aplicar_impuesto"
                                                        @change="calcTotals(editForm)" />
                                                    Aplicar ISV 15%
                                                </label>
                                                <div class="text-sm text-gray-500 ml-4">Impuesto: <span
                                                        x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(item.impuesto || 0)"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" @click="removeEditItem(index)"
                                            class="ml-3 inline-flex items-center justify-center w-8 h-8 rounded bg-red-600 hover:bg-red-700 text-white"
                                            title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-3">
                                        <div>
                                            <label class="text-xs text-gray-400">Precio Unit. <span
                                                    class="text-red-400">*</span></label>
                                            <input type="number" step="0.01" min="0"
                                                x-model.number="item.precio_unitario"
                                                @input="calcTotals(editForm); formEditCotizacion._touched[`edit_precio_${index}`] = true"
                                                @blur="formEditCotizacion._touched[`edit_precio_${index}`] = true"
                                                :class="formEditCotizacion._touched && formEditCotizacion._touched[`edit_precio_${index}`] && (item.precio_unitario === null || item.precio_unitario === undefined || item.precio_unitario < 0) ? 'border-red-500' : 'border-gray-600'"
                                                class="w-full rounded-md border dark:border-gray-700 p-2 text-sm text-right" />
                                            <small class="block text-xs text-gray-500 mt-1"
                                                :class="formEditCotizacion._touched && formEditCotizacion._touched[`edit_precio_${index}`] && (item.precio_unitario === null || item.precio_unitario === undefined || item.precio_unitario < 0) ? 'text-red-500' : ''">
                                                Requerido. Mín: 0
                                            </small>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-400">Cantidad <span
                                                    class="text-red-400">*</span></label>
                                            <input type="number" step="0.01" min="1" x-model.number="item.cantidad"
                                                @input="calcTotals(editForm); formEditCotizacion._touched[`edit_cantidad_${index}`] = true"
                                                @blur="formEditCotizacion._touched[`edit_cantidad_${index}`] = true"
                                                :class="formEditCotizacion._touched && formEditCotizacion._touched[`edit_cantidad_${index}`] && (item.cantidad === null || item.cantidad === undefined || item.cantidad <= 0) ? 'border-red-500' : 'border-gray-600'"
                                                class="w-full rounded-md border dark:border-gray-700 p-2 text-sm text-right" />
                                            <small class="block text-xs text-gray-500 mt-1"
                                                :class="formEditCotizacion._touched && formEditCotizacion._touched[`edit_cantidad_${index}`] && (item.cantidad === null || item.cantidad === undefined || item.cantidad <= 0) ? 'text-red-500' : ''">
                                                Requerido. Mín: 1
                                            </small>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-400">Impuesto</label>
                                            <input type="number" step="0.01" x-model.number="item.impuesto"
                                                @input="calcTotals(editForm)"
                                                class="w-full rounded-md border border-gray-600 dark:border-gray-700 p-2 text-sm text-right" />
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-400">Total</label>
                                            <input type="text"
                                                :value="(Number(item.precio_unitario||0)*Number(item.cantidad||0)+Number(item.impuesto||0)).toFixed(2)"
                                                disabled
                                                class="w-full rounded-md border border-gray-200 bg-gray-100 dark:bg-gray-700 p-2 text-sm text-right" />
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="flex gap-2 mt-2">
                            <button type="button"
                                @click="(function(){ editForm.items.push({ descripcion:'', precio_unitario:0, cantidad:1, impuesto:0, id_producto_fk:null }); editForm.items = editForm.items.slice(); calcTotals(editForm); })()"
                                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm nunito-regular">
                                <i class="fas fa-plus"></i> Añadir Prod/Serv
                            </button>
                            <button type="button" @click="openCatalog('editForm')"
                                class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-sm nunito-regular">
                                <i class="fas fa-list"></i> Seleccionar del catálogo
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 nunito-bold">Imponible</label>
                            <input type="text"
                                :value="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(editForm.imponible || 0)"
                                readonly
                                class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-50 dark:bg-gray-800 p-2 text-sm text-right font-semibold" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 nunito-bold">Total Impuesto</label>
                            <input type="text"
                                :value="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(editForm.total_impuesto || 0)"
                                readonly
                                class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-50 dark:bg-gray-800 p-2 text-sm text-right" />
                        </div>

                        <div>
                            <label for="editOtrosCargos"
                                class="block text-sm font-medium text-gray-700 nunito-bold">Otros
                                Cargos</label>
                            <div class="relative">
                                <span
                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-600">L.</span>
                                <input type="number" id="editOtrosCargos" name="otrosCargos"
                                    x-model.number="editForm.otros_cargos" @input="calcTotals(editForm)"
                                    class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1 pl-8 text-right" />
                            </div>
                            <div class="flex items-center gap-3 mt-2">
                                <label class="inline-flex items-center text-sm text-gray-400">
                                    <input type="checkbox" class="mr-2 h-4 w-4" x-model="editForm.apply_isv_otros"
                                        @change="calcTotals(editForm)" />
                                    Aplicar ISV 15% a Otros Cargos
                                </label>
                                <div class="text-sm text-gray-500 ml-4">Impuesto:
                                    <span
                                        x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format((editForm.apply_isv_otros ? (Number(editForm.otros_cargos||0) * 0.15) : 0))"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 nunito-bold">Total</label>
                        <div
                            class="mt-1 block w-full rounded-md border border-blue-700 bg-blue-600 text-white p-3 text-lg font-bold text-right">
                            <span
                                x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(editForm.total || 0)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </x-admin.edit-modal>

    <x-admin.form-modal class="nunito-bold" modalName="catalogModal" title="Seleccionar items del catálogo"
        submitLabel="Agregar seleccionados" formId="catalog-selector" maxWidth="max-w-4xl">
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <input type="text" placeholder="Buscar descripción..." x-model="catalogSearch"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1 text-sm bg-white dark:bg-gray-900 text-gray-700 dark:text-white" />
            </div>
            <div
                class="overflow-x-auto max-h-[420px] overflow-y-auto border border-gray-200 dark:border-gray-700 rounded">
                <table class="min-w-full text-xs">
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
                                <td class="py-2 px-3">
                                    <input type="checkbox" :checked="!!catalogSelectedUser[String(it.id)]"
                                        @change="(e)=>{ catalogSelectedUser[String(it.id)] = e.target.checked; }" />
                                </td>
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

    <x-admin.confirmation-modal class="nunito-bold" modalName="deleteModal" title="Inactivar Cotización"
        itemToDelete="selectedItem" itemNameProperty="id" message="¿Estás seguro de inactivar la cotización" />

    <x-admin.confirmation-modal class="nunito-bold" modalName="restoreModal" title="Activar Cotización"
        itemToDelete="selectedRestoreItem" itemNameProperty="id" message="¿Estás seguro de activar la cotización" />
</div>

<style>
    table thead th,
    table tbody td {
        font-size: 0.8125rem;
    }
</style>