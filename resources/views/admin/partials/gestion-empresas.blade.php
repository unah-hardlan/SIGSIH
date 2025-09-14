<div x-data="{
        tab: 'empresas',
        isEmpresaModalOpen: false,
        isEmpresaRegistradaModalOpen: false,
        isOficinaModalOpen: false,
        isDeleteEmpresaModalOpen: false,
        isDeleteEmpresaRegistradaModalOpen: false,
        isDeleteOficinaModalOpen: false,
        empresaToEdit: null,
        empresaRegistradaToEdit: null,
        oficinaToEdit: null,
        empresaToDelete: null,
        empresaRegistradaToDelete: null,
        oficinaToDelete: null,
    empresas: [], // Empresas cliente (empresas-cliente)
    formEmpresa: { id:null, id_nombre_empresa_fk:'', id_oficina_fk:'', id_direccion_fk:'', fecha_registro:'', direccion:'', estado_empresa:'Activo' },
    formEmpresaRegistrada: { id:null, nombre_empresa:'', descripcion_empresa:'' },
    formOficina: { id:null, nombre:'' },
    empresasRegistradas: [], // Catálogo nombres-empresa
    oficinas: [],
    direcciones: [],
    // Estado de carga
    loadingEmpresas: false,
    loadingEmpresasRegistradas: false,
    loadingOficinas: false,
    saving: false,
    deleting: false,
    errors: {},
    searchEmpresa: '',
    searchEmpresaRegistrada: '',
    searchOficina: '',
    // Filtros de selects/tablas usados por partial filtros-generales
    estadoEmpresa: '', // se usa en Empresas (filtro estado)
    ordenarPor: 'id', // default para x-model en filtros-generales
    // Watchers simples (se configuran en init())
    reportUrl(){
        const params=new URLSearchParams();
        params.set('modulo','Empresas');
        if(this.searchEmpresa) params.set('search', this.searchEmpresa);
        if(this.estadoEmpresa) params.set('estado_empresa', this.estadoEmpresa.toLowerCase());
        if(this.ordenarPor && this.ordenarPor!=='id') params.set('ordenar_por', this.ordenarPor);
        // podría agregarse fecha_desde/fecha_hasta si luego se añaden filtros
        params.set('fecha_generacion', new Date().toISOString());
        return '/admin/reportes-header?'+params.toString();
    },
        // ---------------- MODALES ----------------
    openEmpresaModal(edit = false, empresa = null) {
            this.isEmpresaModalOpen = true;
            this.empresaToEdit = edit ? { ...empresa } : null;
            if(!edit){
                this.formEmpresa = { id:null, id_nombre_empresa_fk:'', id_oficina_fk:'', id_direccion_fk:'', fecha_registro: new Date().toISOString().slice(0,10), direccion:'', estado_empresa:'Activo' };
            } else {
                this.formEmpresa = {
                    id: empresa.id,
                    id_nombre_empresa_fk: empresa.raw?.id_nombre_empresa_fk || empresa.id_nombre_empresa_fk || '',
                    id_oficina_fk: empresa.raw?.id_oficina_fk || empresa.id_oficina_fk || '',
            id_direccion_fk: empresa.raw?.id_direccion_fk || empresa.id_direccion_fk || '',
            fecha_registro: ((empresa.raw?.fecha_registro)||empresa.fecha_registro||'').toString().split('T')[0] || new Date().toISOString().slice(0,10),
                    direccion: empresa.raw?.direccion?.direccion || empresa.direccion || '',
                    estado_empresa: empresa.estado_empresa || 'Activo'
                };
            }
        },
        openEmpresaRegistradaModal(edit = false, empresa = null) {
            this.isEmpresaRegistradaModalOpen = true;
            this.empresaRegistradaToEdit = edit ? { ...empresa } : null;
            if(!edit){
                this.formEmpresaRegistrada = { id:null, nombre_empresa:'', descripcion_empresa:'', estado_empresa:'Activo' };
            } else {
                this.formEmpresaRegistrada = { id: empresa.id, nombre_empresa: empresa.nombre_empresa, descripcion_empresa: empresa.descripcion_empresa, estado_empresa: empresa.estado_empresa };
            }
        },
        openOficinaModal(edit = false, oficina = null) {
            this.isOficinaModalOpen = true;
            this.oficinaToEdit = edit ? { ...oficina } : null;
            if(!edit){
                this.formOficina = { id:null, nombre:'' };
            } else {
                this.formOficina = { id: oficina.id, nombre: oficina.nombre };
            }
        },
        openDeleteEmpresaModal(empresa) {
            this.empresaToDelete = empresa;
            this.isDeleteEmpresaModalOpen = true;
        },
        openDeleteEmpresaRegistradaModal(empresa) {
            this.empresaRegistradaToDelete = empresa;
            this.isDeleteEmpresaRegistradaModalOpen = true;
        },
        openDeleteOficinaModal(oficina) {
            this.oficinaToDelete = oficina;
            this.isDeleteOficinaModalOpen = true;
        },
        deleteEmpresa() {
            if (this.empresaToDelete) {
                // Aquí iría la lógica para eliminar la empresa
                console.log('Eliminando empresa:', this.empresaToDelete);
                this.isDeleteEmpresaModalOpen = false;
                this.empresaToDelete = null;
            }
        },
        deleteEmpresaRegistrada() {
            if (this.empresaRegistradaToDelete) {
                // Eliminar de la lista local
                this.empresasRegistradas = this.empresasRegistradas.filter(e => e.id !== this.empresaRegistradaToDelete.id);
                console.log('Eliminando empresa registrada:', this.empresaRegistradaToDelete);
                this.isDeleteEmpresaRegistradaModalOpen = false;
                this.empresaRegistradaToDelete = null;
            }
        },
        deleteOficina() {
            if (this.oficinaToDelete) {
                // Eliminar de la lista local
                this.oficinas = this.oficinas.filter(o => o.id !== this.oficinaToDelete.id);
                console.log('Eliminando oficina:', this.oficinaToDelete);
                this.isDeleteOficinaModalOpen = false;
                this.oficinaToDelete = null;
            }
        },
        // ---------------- API HELPERS ----------------
        apiHeaders(){
            const t = localStorage.getItem('authToken');
            return { 'Content-Type':'application/json','Accept':'application/json', ...(t? { 'Authorization':'Bearer '+t }:{}) };
        },
        showToast(msg, type='ok'){
            let d=document.createElement('div');
            d.className='fixed top-4 right-4 z-50 px-4 py-2 rounded shadow text-sm '+(type==='error'?'bg-red-600 text-white':'bg-green-600 text-white');
            d.textContent=msg; document.body.appendChild(d); setTimeout(()=>d.remove(),3500);
        },
    mapEmpresa(e){
        const ciudad = e.direccion?.ciudad;
        const departamento = ciudad?.departamento;
        const pais = departamento?.pais;
    const registroCat = this.empresasRegistradas.find(er=>er.id===e.id_nombre_empresa_fk);
        const oficinaCat = this.oficinas.find(o=>o.id===e.id_oficina_fk);
        return {
            id: e.id_empresa_cliente_pk || e.id || Math.random(),
            id_nombre_empresa_fk: e.id_nombre_empresa_fk,
            id_oficina_fk: e.id_oficina_fk,
            id_direccion_fk: e.id_direccion_fk,
            nombre_empresa: e.nombre_empresa?.nombre_empresa || registroCat?.nombre_empresa || e.nombreEmpresaNombre || e.nombre_empresa_nombre || '—',
            descripcion_empresa: e.nombre_empresa?.descripcion_empresa || registroCat?.descripcion_empresa || e.descripcion_empresa || e.descripcion || '',
            fecha_registro: (e.fecha_registro || '').toString().split('T')[0],
            ciudad: ciudad?.nombre_ciudad || '',
            departamento: departamento?.nombre_departamento || '',
            pais: pais?.nombre_pais || '',
            oficina: e.oficina?.nombre_oficina || oficinaCat?.nombre || '',
            // Priorizar estado propio de empresa cliente si existe
            estado_empresa: (e.estado_empresa || 'activo').toLowerCase()==='activo' ? 'Activo':'Inactivo',
            raw: e
        };
    },
    async fetchEmpresas(){
            this.loadingEmpresas = true;
            try {
        const params = new URLSearchParams();
        params.set('per_page','100');
        if(this.searchEmpresa) params.set('search', this.searchEmpresa);
        if(this.estadoEmpresa) params.set('estado_empresa', this.estadoEmpresa.toLowerCase());
        const r = await fetch('/api/empresas-cliente?'+params.toString(), { headers: this.apiHeaders() });
                if(!r.ok) throw new Error('Error');
                const j = await r.json();
                this.empresas = (j.data||[]).map(e=> this.mapEmpresa(e));
            } catch(e){ this.showToast('No se pudieron cargar empresas','error'); }
            finally { this.loadingEmpresas=false; }
        },
        async fetchEmpresasRegistradas(){
            this.loadingEmpresasRegistradas = true;
            try {
                const r = await fetch('/api/nombres-empresa?per_page=200&search='+encodeURIComponent(this.searchEmpresaRegistrada||''), { headers: this.apiHeaders() });
                if(!r.ok) throw new Error('Error');
                const j = await r.json();
                this.empresasRegistradas = (j.data||[]).map(e=>({ id: e.id_nombre_empresa_pk || e.id || Math.random(), nombre_empresa: e.nombre_empresa, descripcion_empresa: e.descripcion_empresa }));
            } catch(e){ this.showToast('Error cargando catálogo empresas','error'); }
            finally { this.loadingEmpresasRegistradas=false; }
        },
        async fetchOficinas(){
            this.loadingOficinas = true;
            try {
                const r = await fetch('/api/oficinas-empresa?per_page=200&search='+encodeURIComponent(this.searchOficina||''), { headers: this.apiHeaders() });
                if(!r.ok) throw new Error('Error');
                const j = await r.json();
                this.oficinas = (j.data||[]).map(o=>({ id: o.id_oficina_empresa_pk || o.id || Math.random(), nombre: o.nombre_oficina || o.nombre }));
            } catch(e){ this.showToast('Error cargando oficinas','error'); }
            finally { this.loadingOficinas=false; }
        },
        async fetchDirecciones(){
            try {
                const r = await fetch('/api/direcciones?per_page=200', { headers: this.apiHeaders() });
                if(!r.ok) throw new Error('Error');
                const j = await r.json();
                this.direcciones = (j.data||[]).map(d=>({ id: d.id_direccion_pk || d.id, label: d.ciudad?.nombre_ciudad ? d.ciudad.nombre_ciudad : ('Dirección '+(d.id_direccion_pk||d.id)) }));
            } catch(e){ this.showToast('Error cargando direcciones','error'); }
        },
    async createEmpresaRegistrada(payload){
            try {
        // estado eliminado: ya no se envía
        const r = await fetch('/api/nombres-empresa', { method:'POST', headers: this.apiHeaders(), body: JSON.stringify(payload) });
                if(r.status===422){ const j=await r.json(); this.errors=j.errors||{}; throw new Error('Validación'); }
                if(!r.ok) throw new Error('Error');
                const j=await r.json();
        if(j.data){ this.empresasRegistradas.unshift({ id: j.data.id_nombre_empresa_pk || j.data.id || j.data.id_nombre_empresa || Math.random(), nombre_empresa: j.data.nombre_empresa, descripcion_empresa: j.data.descripcion_empresa }); }
                this.showToast('Creada');
            } catch(e){ this.showToast('No se creó','error'); }
        },
    async updateEmpresaRegistrada(id, payload){
            try {
        // sin estado
        const r = await fetch('/api/nombres-empresa/'+id, { method:'PUT', headers: this.apiHeaders(), body: JSON.stringify(payload) });
                if(r.status===422){ const j=await r.json(); this.errors=j.errors||{}; throw new Error('Validación'); }
                if(!r.ok) throw new Error('Error');
                const j=await r.json();
                const idx=this.empresasRegistradas.findIndex(e=>e.id===id);
        if(idx>-1 && j.data){ this.empresasRegistradas[idx].nombre_empresa=j.data.nombre_empresa; this.empresasRegistradas[idx].descripcion_empresa=j.data.descripcion_empresa; }
                this.showToast('Actualizada');
            } catch(e){ this.showToast('No se actualizó','error'); }
        },
        async deleteEmpresaRegistradaApi(id){
            try { const r= await fetch('/api/nombres-empresa/'+id, { method:'DELETE', headers: this.apiHeaders() }); if(!r.ok) throw new Error('Error'); this.showToast('Eliminada'); }
            catch(e){ this.showToast('Error al eliminar','error'); }
        },
        // --------- CRUD OFICINAS ---------
        async createOficina(payload){
            try {
                const r = await fetch('/api/oficinas-empresa', { method:'POST', headers:this.apiHeaders(), body: JSON.stringify(payload) });
                if(r.status===422){ const j=await r.json(); this.errors=j.errors||{}; throw new Error('Validación'); }
                if(!r.ok) throw new Error('Error');
                const j=await r.json();
                if(j.data){ this.oficinas.unshift({ id: j.data.id_oficina_empresa_pk || j.data.id || Math.random(), nombre: j.data.nombre_oficina || j.data.nombre }); }
                this.showToast('Oficina creada');
            } catch(e){ this.showToast('No se creó oficina','error'); }
        },
        async updateOficina(id, payload){
            try {
                const r = await fetch('/api/oficinas-empresa/'+id, { method:'PUT', headers:this.apiHeaders(), body: JSON.stringify(payload) });
                if(r.status===422){ const j=await r.json(); this.errors=j.errors||{}; throw new Error('Validación'); }
                if(!r.ok) throw new Error('Error');
                const j=await r.json();
                const idx=this.oficinas.findIndex(o=>o.id===id);
                if(idx>-1 && j.data){ this.oficinas[idx].nombre = j.data.nombre_oficina || j.data.nombre; }
                this.showToast('Oficina actualizada');
            } catch(e){ this.showToast('No se actualizó oficina','error'); }
        },
        async deleteOficinaApi(id){
            try { const r= await fetch('/api/oficinas-empresa/'+id, { method:'DELETE', headers:this.apiHeaders() }); if(!r.ok) throw new Error('Error'); this.showToast('Oficina eliminada'); }
            catch(e){ this.showToast('Error al eliminar oficina','error'); }
        },
        // --------- CRUD EMPRESA CLIENTE ---------
    async createEmpresaCliente(payload){
            try {
        // completar campos obligatorios si faltan
        if(!payload.fecha_registro) payload.fecha_registro=new Date().toISOString().slice(0,10);
        if(!payload.id_direccion_fk){ if(this.formEmpresa.id_direccion_fk) payload.id_direccion_fk=this.formEmpresa.id_direccion_fk; }
        // incluir estado (API probablemente espera minúsculas)
        payload.estado_empresa = (payload.estado_empresa || this.formEmpresa.estado_empresa || 'Activo').toLowerCase();
        const r = await fetch('/api/empresas-cliente', { method:'POST', headers:this.apiHeaders(), body: JSON.stringify(payload) });
                if(r.status===422){ const j=await r.json(); this.errors=j.errors||{}; throw new Error('Validación'); }
                if(!r.ok) throw new Error('Error');
                const j=await r.json();
                if(j.data){ this.empresas.unshift(this.mapEmpresa(j.data)); }
                this.showToast('Empresa creada');
            } catch(e){ this.showToast('No se creó empresa','error'); }
        },
    async updateEmpresaCliente(id, payload){
            try {
        if(!payload.fecha_registro) payload.fecha_registro=this.formEmpresa.fecha_registro||new Date().toISOString().slice(0,10);
        if(!payload.id_direccion_fk) payload.id_direccion_fk=this.formEmpresa.id_direccion_fk;
        payload.estado_empresa = (payload.estado_empresa || this.formEmpresa.estado_empresa || 'Activo').toLowerCase();
        const r = await fetch('/api/empresas-cliente/'+id, { method:'PUT', headers:this.apiHeaders(), body: JSON.stringify(payload) });
                if(r.status===422){ const j=await r.json(); this.errors=j.errors||{}; throw new Error('Validación'); }
                if(!r.ok) throw new Error('Error');
                const j=await r.json();
                const idx=this.empresas.findIndex(e=>e.id===id);
                if(idx>-1 && j.data){ this.empresas[idx] = this.mapEmpresa(j.data); }
                this.showToast('Empresa actualizada');
            } catch(e){ this.showToast('No se actualizó empresa','error'); }
        },
        async deleteEmpresaClienteApi(id){
            try { const r= await fetch('/api/empresas-cliente/'+id, { method:'DELETE', headers:this.apiHeaders() }); if(!r.ok) throw new Error('Error'); this.showToast('Empresa eliminada'); }
            catch(e){ this.showToast('Error al eliminar empresa','error'); }
        },
        async initData(){
            await Promise.all([ this.fetchEmpresasRegistradas(), this.fetchOficinas(), this.fetchDirecciones(), this.fetchEmpresas() ]);
        },
        // Hook inicial
        init(){
            this.initData();
            // Debounce genérico
            const debounce=(fn,ms=400)=>{let h;return(...a)=>{clearTimeout(h);h=setTimeout(()=>fn(...a),ms);};};
            this.$watch('searchEmpresa', debounce(()=>this.fetchEmpresas()));
            this.$watch('searchEmpresaRegistrada', debounce(()=>this.fetchEmpresasRegistradas()));
            this.$watch('searchOficina', debounce(()=>this.fetchOficinas()));
            this.$watch('estadoEmpresa', ()=>{ this.fetchEmpresas(); });
            this.$watch('ordenarPor', ()=>{ this.sortEmpresasLocal(); });
        },
        sortEmpresasLocal(){
            if(!this.ordenarPor) return; const campo=this.ordenarPor;
            this.empresas.sort((a,b)=>{ const av=(a[campo]||'').toString().toLowerCase(); const bv=(b[campo]||'').toString().toLowerCase(); if(av<bv) return -1; if(av>bv) return 1; return 0; });
        },
        // Sobrescribir deleteEmpresaRegistrada para llamar API
        deleteEmpresaRegistrada(){ if(this.empresaRegistradaToDelete){ const id=this.empresaRegistradaToDelete.id; this.empresasRegistradas = this.empresasRegistradas.filter(e=>e.id!==id); this.deleteEmpresaRegistradaApi(id); this.isDeleteEmpresaRegistradaModalOpen=false; this.empresaRegistradaToDelete=null; } },
        deleteOficina(){ if(this.oficinaToDelete){ const id=this.oficinaToDelete.id; this.oficinas = this.oficinas.filter(o=>o.id!==id); this.deleteOficinaApi(id); this.isDeleteOficinaModalOpen=false; this.oficinaToDelete=null; } },
        deleteEmpresa(){ if(this.empresaToDelete){ const id=this.empresaToDelete.id; this.empresas = this.empresas.filter(e=>e.id!==id); this.deleteEmpresaClienteApi(id); this.isDeleteEmpresaModalOpen=false; this.empresaToDelete=null; } }
    }" @include('partials.persist-tab', ['tabKey'=> 'admin-gestion-empresas-tab'])
    @modal-submit.window="
    if($event.detail.formId==='empresa-registrada-form'){
    if(empresaRegistradaToEdit){ updateEmpresaRegistrada(formEmpresaRegistrada.id, { nombre_empresa: formEmpresaRegistrada.nombre_empresa, descripcion_empresa: formEmpresaRegistrada.descripcion_empresa }); }
    else { createEmpresaRegistrada({ nombre_empresa: formEmpresaRegistrada.nombre_empresa, descripcion_empresa: formEmpresaRegistrada.descripcion_empresa }); }
    isEmpresaRegistradaModalOpen=false;
    } else if($event.detail.formId==='oficina-form') {
    if(oficinaToEdit){ updateOficina(formOficina.id, { nombre_oficina: formOficina.nombre }); }
    else { createOficina({ nombre_oficina: formOficina.nombre }); }
    isOficinaModalOpen=false;
    } else if($event.detail.formId==='empresa-form') {
    if(empresaToEdit){ updateEmpresaCliente(formEmpresa.id, { id_nombre_empresa_fk: formEmpresa.id_nombre_empresa_fk, id_oficina_fk: formEmpresa.id_oficina_fk, id_direccion_fk: formEmpresa.id_direccion_fk, fecha_registro: formEmpresa.fecha_registro, estado_empresa: formEmpresa.estado_empresa }); }
    else { createEmpresaCliente({ id_nombre_empresa_fk: formEmpresa.id_nombre_empresa_fk, id_oficina_fk: formEmpresa.id_oficina_fk, id_direccion_fk: formEmpresa.id_direccion_fk, fecha_registro: formEmpresa.fecha_registro, estado_empresa: formEmpresa.estado_empresa }); }
    isEmpresaModalOpen=false;
    }
    "
    @keydown.window.escape="isEmpresaModalOpen = false; isEmpresaRegistradaModalOpen = false; isOficinaModalOpen = false; isDeleteEmpresaModalOpen = false; isDeleteEmpresaRegistradaModalOpen = false; isDeleteOficinaModalOpen = false"
    @confirm-delete.window="
    if (isDeleteEmpresaModalOpen) {
    deleteEmpresa();
    } else if (isDeleteEmpresaRegistradaModalOpen) {
    deleteEmpresaRegistrada();
    } else if (isDeleteOficinaModalOpen) {
    deleteOficina();
    }
    ">

    <!-- Tabs -->
    <ul class="flex border-b nunito-bold mb-6 flex-wrap gap-2">
        <li @click="tab='empresas'"
            :class="tab==='empresas' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-100 hover:text-blue-500 dark:hover:text-blue-400 cursor-pointer'"
            class="pb-2 mr-4 nunito-bold">Empresas</li>
        <li @click="tab='form-nombre'"
            :class="tab==='form-nombre' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-100 hover:text-blue-500 dark:hover:text-blue-400 cursor-pointer'"
            class="pb-2 mr-4 nunito-bold">Empresas Registradas</li>
        <li @click="tab='oficinas'"
            :class="tab==='oficinas' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-100 hover:text-blue-500 dark:hover:text-blue-400 cursor-pointer'"
            class="pb-2 nunito-bold">Oficinas Empresa</li>
    </ul>

    <!-- TAB 1: Empresas Cliente -->
    <div x-show="tab==='empresas'" class="overflow-x-auto">
        <x-admin.tabla-mobile class="nunito-bold bg-white dark:bg-gray-900" titulo="Empresas">
            <x-slot name="filtros">
                @include('partials.filtros-generales', [
                'searchModel' => 'searchEmpresa',
                'filtrosSelect' => [],
                'ordenarOptions' => [
                'nombre_empresa' => 'Nombre',
                'estado_empresa' => 'Estado'
                ]
                ])
            </x-slot>
            <x-slot name="boton">
                <div class="flex flex-col gap-2 w-full sm:w-auto">
                    <button @click="openEmpresaModal(false)"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nueva
                        Empresa</button>
                    <a :href="reportUrl()" target="_blank"
                        class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                </div>
            </x-slot>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Nombre Empresa</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Fecha</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Ciudad</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Departamento</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">País</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Oficina</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Estado</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="e in empresas" :key="e.id">
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4 nunito-regular" x-text="e.nombre_empresa"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="e.descripcion_empresa"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="e.fecha_registro"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="e.ciudad"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="e.departamento"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="e.pais"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="e.oficina"></td>
                            <td class="py-2 px-4"><span class="px-2 py-1 rounded nunito-regular" :class="e.estado_empresa==='Activo' ? 'bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-100' : 'bg-red-700 text-red-100'" x-text="e.estado_empresa"></span></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click.prevent="openEmpresaModal(true, e)" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click.prevent="openDeleteEmpresaModal(e)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </x-admin.tabla-crud>
    </div>

    <!-- TAB 2: Empresas Registradas -->
    <div x-show="tab==='form-nombre'" class="overflow-x-auto">
        <x-admin.tabla-mobile class="nunito-bold bg-white dark:bg-gray-900" titulo="Empresas Registradas">
            <x-slot name="filtros">
                @include('partials.filtros-generales', [
                'searchModel' => 'searchEmpresaRegistrada',
                'filtrosSelect' => [],
                'ordenarOptions' => []
                ])
            </x-slot>
            <x-slot name="boton">
                <div class="flex flex-col gap-2 w-full sm:w-auto">
                    <button @click="openEmpresaRegistradaModal(false)"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Agregar empresa registrada</button>
                </div>
            </x-slot>
            <table class="min-w-full text-sm mt-2">
                <thead class="bg-gray-100 dark:bg-gray-800 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Nombre Empresa</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="empresa in empresasRegistradas" :key="empresa.id">
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4 nunito-regular" x-text="empresa.nombre_empresa"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="empresa.descripcion_empresa"></td>

                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click.prevent="openEmpresaRegistradaModal(true, empresa)"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click.prevent="openDeleteEmpresaRegistradaModal(empresa)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </x-admin.tabla-crud>
    </div>

    <!-- TAB 3: Oficinas Empresa -->
    <div x-show="tab==='oficinas'" class="overflow-x-auto">
        <x-admin.tabla-mobile class="nunito-bold bg-white dark:bg-gray-900" titulo="Oficinas de las Empresas">
            <x-slot name="filtros">
                @include('partials.filtros-generales', [
                'searchModel' => 'searchOficina',
                'filtrosSelect' => [],
                'ordenarOptions' => []
                ])
            </x-slot>
            <x-slot name="boton">
                <div class="flex flex-col gap-2 w-full sm:w-auto">
                    <button @click="openOficinaModal(false)"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap font-bold text-sm">Nueva Oficina</button>
                </div>
            </x-slot>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Nombre Oficina</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="oficina in oficinas" :key="oficina.id">
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4 nunito-regular" x-text="oficina.nombre"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click.prevent="openOficinaModal(true, oficina)"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click.prevent="openDeleteOficinaModal(oficina)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </x-admin.tabla-crud>
    </div>

    <!-- Modal Empresas Cliente -->
    <x-admin.form-modal
        modalName="isEmpresaModalOpen"
        title="Empresa"
        submitLabel="Guardar Empresa"
        formId="empresa-form"
        maxWidth="max-w-md">

        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold ">Empresa Registrada <span class="text-red-500">*</span></label>
            <select class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" x-model="formEmpresa.id_nombre_empresa_fk" required>
                <option value="">Seleccionar empresa registrada...</option>
                <template x-for="empresa in empresasRegistradas" :key="empresa.id">
                    <option :value="empresa.id" x-text="empresa.nombre_empresa"
                        :selected="empresaToEdit && empresaToEdit.id === empresa.id"></option>
                </template>
            </select>
            <template x-if="errors.id_nombre_empresa_fk">
                <p class='text-xs text-red-600 mt-1' x-text="errors.id_nombre_empresa_fk[0]"></p>
            </template>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Fecha Registro <span class="text-red-500">*</span></label>
            <input type="date" class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" x-model="formEmpresa.fecha_registro" required>
            <template x-if="errors.fecha_registro">
                <p class='text-xs text-red-600 mt-1' x-text="errors.fecha_registro[0]"></p>
            </template>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Dirección (ID) <span class="text-red-500">*</span></label>
            <select class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" x-model="formEmpresa.id_direccion_fk" required>
                <option value="">Seleccionar dirección...</option>
                <template x-for="d in direcciones" :key="d.id">
                    <option :value="d.id" x-text="d.label"></option>
                </template>
            </select>
            <template x-if="!direcciones.length">
                <p class='text-xs text-yellow-600 mt-1'>No hay direcciones cargadas.</p>
            </template>
            <template x-if="errors.id_direccion_fk">
                <p class='text-xs text-red-600 mt-1' x-text="errors.id_direccion_fk[0]"></p>
            </template>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Oficina <span class="text-red-500">*</span></label>
            <select class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" x-model="formEmpresa.id_oficina_fk" required>
                <option value="">Seleccionar oficina...</option>
                <template x-for="oficina in oficinas" :key="oficina.id">
                    <option class="" :value="oficina.id" x-text="oficina.nombre"
                        :selected="empresaToEdit && empresaToEdit.oficina_id === oficina.id"></option>
                </template>
            </select>
            <template x-if="errors.id_oficina_fk">
                <p class='text-xs text-red-600 mt-1' x-text="errors.id_oficina_fk[0]"></p>
            </template>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Estado <span class="text-red-500">*</span></label>
            <select class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" maxlength="20" x-model="formEmpresa.estado_empresa" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                required>
                <option value="">Seleccionar estado</option>
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
            </select>
        </div>
    </x-admin.form-modal>

    <!-- Modal Empresas Registradas -->
    <x-admin.form-modal
        modalName="isEmpresaRegistradaModalOpen"
        title="Empresa Registrada"
        submitLabel="Guardar Empresa Registrada"
        formId="empresa-registrada-form"
        maxWidth="max-w-lg">

        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Nombre de Empresa <span class="text-red-500">*</span></label>
            <input type="text" class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" maxlength="100"
                pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" x-model="formEmpresaRegistrada.nombre_empresa"
                :placeholder="empresaRegistradaToEdit ? '' : 'Ejemplo S.A.'" required>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Descripción</label>
            <textarea class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" rows="2" maxlength="255"
                :placeholder="empresaRegistradaToEdit ? '' : 'Descripción de la empresa'" x-model="formEmpresaRegistrada.descripcion_empresa"></textarea>
        </div>

    </x-admin.form-modal>

    <!-- Modal Oficina -->
    <x-admin.form-modal
        modalName="isOficinaModalOpen"
        title="Oficina"
        submitLabel="Guardar Oficina"
        formId="oficina-form"
        maxWidth="max-w-lg">

        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Nombre de Oficina</label>
            <input type="text" class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" x-model="formOficina.nombre"
                :placeholder="oficinaToEdit ? '' : 'Oficina Central'" required>
        </div>
    </x-admin.form-modal>

    <!-- Confirmation Modals -->
    <!-- Modal de confirmación para eliminar empresa cliente -->
    <x-admin.confirmation-modal
        modal-name="isDeleteEmpresaModalOpen"
        title="Eliminar Empresa Cliente"
        item-to-delete="empresaToDelete"
        item-name-property="nombre_empresa"
        message="¿Estás seguro de que deseas eliminar la empresa cliente" />

    <!-- Modal de confirmación para eliminar empresa registrada -->
    <x-admin.confirmation-modal
        modal-name="isDeleteEmpresaRegistradaModalOpen"
        title="Eliminar Empresa Registrada"
        item-to-delete="empresaRegistradaToDelete"
        item-name-property="nombre_empresa"
        message="¿Estás seguro de que deseas eliminar la empresa registrada" />

    <!-- Modal de confirmación para eliminar oficina -->
    <x-admin.confirmation-modal
        modal-name="isDeleteOficinaModalOpen"
        title="Eliminar Oficina"
        item-to-delete="oficinaToDelete"
        item-name-property="nombre"
        message="¿Estás seguro de que deseas eliminar la oficina" />
</div>