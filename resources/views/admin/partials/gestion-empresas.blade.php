<div x-data="{
    isEmpresaModalOpen: false,
    isDeleteEmpresaModalOpen: false,
    empresaToEdit: null,
    empresaToDelete: null,
    empresas: [],
    formEmpresa: {
        id: null,
        nombre_comercial: '',
        razon_social: '',
        rtn: '',
        descripcion_empresa: '',
        horario_atencion: '',
        fecha_registro: new Date().toISOString().slice(0,10),
        estado_cliente: 'activo'
    },
    loadingEmpresas: false,
    saving: false,
    deleting: false,
    errors: {},
    searchEmpresa: '',
    estadoEmpresa: '',
    ordenarPor: 'nombre_comercial',
    reportUrl(){
        const params = new URLSearchParams();
        params.set('modulo','Empresas');
        if(this.searchEmpresa) params.set('search', this.searchEmpresa);
        if(this.estadoEmpresa) params.set('estado_cliente', this.estadoEmpresa.toLowerCase());
        if(this.ordenarPor) params.set('ordenar_por', this.ordenarPor);
        params.set('fecha_generacion', new Date().toISOString());
        return '/admin/reportes-header?'+params.toString();
    },
    resetForm(){
        this.formEmpresa = {
            id: null,
            nombre_comercial: '',
            razon_social: '',
            rtn: '',
            descripcion_empresa: '',
            horario_atencion: '',
            fecha_registro: new Date().toISOString().slice(0,10),
            estado_cliente: 'activo'
        };
        this.errors = {};
    },
    openEmpresaModal(edit = false, empresa = null){
        this.isEmpresaModalOpen = true;
        this.empresaToEdit = edit ? { ...empresa } : null;
        if(!edit || !empresa){
            this.resetForm();
        } else {
            this.formEmpresa = {
                id: empresa.id,
                nombre_comercial: empresa.nombre_comercial,
                razon_social: empresa.razon_social,
                rtn: empresa.rtn,
                descripcion_empresa: empresa.descripcion_empresa,
                horario_atencion: empresa.horario_atencion,
                fecha_registro: (empresa.raw.fecha_registro || '').toString().split(' ')[0] || new Date().toISOString().slice(0,10),
                estado_cliente: (empresa.estado_cliente || 'activo').toLowerCase()
            };
        }
    },
    openDeleteEmpresaModal(empresa){
        this.empresaToDelete = empresa;
        this.isDeleteEmpresaModalOpen = true;
    },
    apiHeaders(){
        const t = localStorage.getItem('authToken');
        return { 'Content-Type':'application/json','Accept':'application/json', ...(t ? { 'Authorization':'Bearer '+t }: {}) };
    },
    showToast(msg, type='ok'){
        const d=document.createElement('div');
        d.className='fixed top-4 right-4 z-50 px-4 py-2 rounded shadow text-sm '+(type==='error'?'bg-red-600 text-white':'bg-green-600 text-white');
        d.textContent=msg; document.body.appendChild(d); setTimeout(()=>d.remove(),3500);
    },
    mapEmpresa(e){
        const months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        let dateStr = (e.fecha_registro || '').toString().split(' ')[0];
        let formattedDate = '';
        if (dateStr) {
            let parts = dateStr.split('-');
            if (parts.length === 3) {
                let year = parts[0];
                let month = parseInt(parts[1]) - 1;
                let day = parts[2];
                formattedDate = `${day} de ${months[month]} del ${year}`;
            } else {
                formattedDate = dateStr;
            }
        }
        return {
            id: e.id_cliente_fk || e.id || Math.random(),
            nombre_comercial: e.nombre_comercial || '—',
            razon_social: e.razon_social || '',
            rtn: e.rtn || '',
            descripcion_empresa: e.descripcion_empresa || '',
            horario_atencion: e.horario_atencion || '',
            fecha_registro: formattedDate,
            estado_cliente: (e.estado_cliente || 'activo').toLowerCase(),
            estado_label: (e.estado_cliente || 'activo').toLowerCase() === 'activo' ? 'Activo' : 'Inactivo',
            contactos: e.contactos || [],
            raw: e
        };
    },
    async fetchEmpresas(){
        this.loadingEmpresas = true;
        try {
            const params = new URLSearchParams();
            params.set('per_page','100');
            if(this.searchEmpresa) params.set('search', this.searchEmpresa);
            if(this.estadoEmpresa) params.set('estado_cliente', this.estadoEmpresa.toLowerCase());
            const r = await fetch('/api/empresas-cliente?'+params.toString(), { headers: this.apiHeaders() });
            if(!r.ok) throw new Error('Error');
            const j = await r.json();
            this.empresas = (j.data||[]).map(e=> this.mapEmpresa(e));
            this.sortEmpresasLocal();
        } catch(e){ this.showToast('No se pudieron cargar empresas','error'); }
        finally { this.loadingEmpresas=false; }
    },
    async createEmpresaCliente(){
        this.saving = true;
        this.errors = {};
        try {
            const payload = {
                nombre_comercial: this.formEmpresa.nombre_comercial,
                razon_social: this.formEmpresa.razon_social || null,
                rtn: this.formEmpresa.rtn || null,
                descripcion_empresa: this.formEmpresa.descripcion_empresa || null,
                horario_atencion: this.formEmpresa.horario_atencion || null,
                fecha_registro: this.formEmpresa.fecha_registro,
                estado_cliente: (this.formEmpresa.estado_cliente || 'activo').toLowerCase(),
            };
            const r = await fetch('/api/empresas-cliente', { method:'POST', headers:this.apiHeaders(), body: JSON.stringify(payload) });
            if(r.status===422){ const j=await r.json(); this.errors=j.errors||{}; throw new Error('Validación'); }
            if(!r.ok) throw new Error('Error');
            const j=await r.json();
            if(j.data){ this.empresas.unshift(this.mapEmpresa(j.data)); this.sortEmpresasLocal(); }
            this.showToast('Empresa creada');
            this.isEmpresaModalOpen=false;
            this.resetForm();
        } catch(e){ this.showToast('No se creó empresa','error'); }
        finally { this.saving=false; }
    },
    async updateEmpresaCliente(){
        if(!this.formEmpresa.id) return;
        this.saving = true;
        this.errors = {};
        try {
            const payload = {
                nombre_comercial: this.formEmpresa.nombre_comercial,
                razon_social: this.formEmpresa.razon_social || null,
                rtn: this.formEmpresa.rtn || null,
                descripcion_empresa: this.formEmpresa.descripcion_empresa || null,
                horario_atencion: this.formEmpresa.horario_atencion || null,
                fecha_registro: this.formEmpresa.fecha_registro,
                estado_cliente: (this.formEmpresa.estado_cliente || 'activo').toLowerCase(),
            };
            const r = await fetch('/api/empresas-cliente/'+this.formEmpresa.id, { method:'PUT', headers:this.apiHeaders(), body: JSON.stringify(payload) });
            if(r.status===422){ const j=await r.json(); this.errors=j.errors||{}; throw new Error('Validación'); }
            if(!r.ok) throw new Error('Error');
            const j=await r.json();
            const idx=this.empresas.findIndex(e=>e.id===this.formEmpresa.id);
            if(idx>-1 && j.data){ this.empresas.splice(idx,1,this.mapEmpresa(j.data)); this.sortEmpresasLocal(); }
            this.showToast('Empresa actualizada');
            this.isEmpresaModalOpen=false;
            this.resetForm();
        } catch(e){ this.showToast('No se actualizó empresa','error'); }
        finally { this.saving=false; }
    },
    async deleteEmpresaClienteApi(id){
        this.deleting = true;
        try {
            const r= await fetch('/api/empresas-cliente/'+id, { method:'DELETE', headers:this.apiHeaders() });
            if(!r.ok) throw new Error('Error');
            this.empresas = this.empresas.filter(e=>e.id!==id);
            this.showToast('Empresa eliminada');
        } catch(e){ this.showToast('Error al eliminar empresa','error'); }
        finally {
            this.deleting = false;
            this.isDeleteEmpresaModalOpen=false;
            this.empresaToDelete=null;
        }
    },
    init(){
        this.fetchEmpresas();
        const debounce=(fn,ms=400)=>{let h;return(...a)=>{clearTimeout(h);h=setTimeout(()=>fn(...a),ms);};};
        this.$watch('searchEmpresa', debounce(()=>this.fetchEmpresas()));
        this.$watch('estadoEmpresa', ()=>{ this.fetchEmpresas(); });
        this.$watch('ordenarPor', ()=>{ this.sortEmpresasLocal(); });
    },
    sortEmpresasLocal(){
        if(!this.ordenarPor) return;
        const campo=this.ordenarPor;
        this.empresas.sort((a,b)=>{
            const av=(a[campo]||'').toString().toLowerCase();
            const bv=(b[campo]||'').toString().toLowerCase();
            if(av<bv) return -1; if(av>bv) return 1; return 0;
        });
    },
    submitEmpresa(){
        if(this.formEmpresa.id){
            this.updateEmpresaCliente();
        } else {
            this.createEmpresaCliente();
        }
    },
    deleteEmpresa(){
        if(this.empresaToDelete){
            this.deleteEmpresaClienteApi(this.empresaToDelete.id);
        }
    }
}" 
@include('partials.persist-tab', ['tabKey' => 'admin-gestion-empresas-tab'])
@modal-submit.window="
    if($event.detail.formId==='empresa-form'){
        submitEmpresa();
    }
"
@keydown.window.escape="isEmpresaModalOpen = false; isDeleteEmpresaModalOpen = false"
@confirm-delete.window="
    if (isDeleteEmpresaModalOpen) {
        deleteEmpresa();
    }
">

    <!-- Title -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Gestión de Empresas</h1>
    </div>

    <!-- Responsive Table -->
    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
                'searchModel' => 'searchEmpresa',
                'filtrosSelect' => [],
                'ordenarOptions' => [
                    'nombre_comercial' => 'Nombre',
                    'estado_cliente' => 'Estado'
                ]
            ])
        </x-slot>
        <x-slot name="actions">
            <div class="flex flex-col gap-2 w-full sm:w-auto">
                <button @click="isEmpresaModalOpen = true"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                    Nueva Empresa
                </button>
                <a :href="reportUrl()" target="_blank"
                   class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                    <i class="fas fa-file-alt"></i> Generar Reporte
                </a>
            </div>
        </x-slot>
        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold rounded-t-lg">
                    <tr>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Nombre Comercial</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Razón Social</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">RTN</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Fecha Registro</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Horario</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Estado</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingEmpresas">
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando empresas...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEmpresas && empresas.length === 0">
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay empresas registradas
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEmpresas && empresas.length > 0">
                        <template x-for="e in empresas" :key="e.id">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                                <td class="py-2 px-4" x-text="e.nombre_comercial"></td>
                                <td class="py-2 px-4" x-text="e.razon_social"></td>
                                <td class="py-2 px-4" x-text="e.descripcion_empresa"></td>
                                <td class="py-2 px-4" x-text="e.rtn"></td>
                                <td class="py-2 px-4 break-all" x-text="e.fecha_registro"></td>
                                <td class="py-2 px-4" x-text="e.horario_atencion"></td>
                                <td class="py-2 px-4">
                                    <span class="px-2 py-1 rounded nunito-regular"
                                          :class="e.estado_label==='Activo' ? 'bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-100' : 'bg-red-700 text-red-100'"
                                          x-text="e.estado_label"></span>
                                </td>
                                <td class="py-2 px-4 flex gap-2">
                                    <a href="#" @click.prevent="openEmpresaModal(true, e)" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="openDeleteEmpresaModal(e)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>
        <x-slot name="cards">
            <template x-if="loadingEmpresas">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando empresas...
                </div>
            </template>
            <template x-if="!loadingEmpresas && empresas.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay empresas registradas
                </div>
            </template>
            <template x-if="!loadingEmpresas && empresas.length > 0">
                <template x-for="e in empresas" :key="e.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="e.nombre_comercial"></h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="'Razón Social: ' + e.razon_social"></p>
                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="'RTN: ' + e.rtn"></p>
                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="'Descripción: ' + e.descripcion_empresa"></p>
                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="'Horario: ' + e.horario_atencion"></p>
                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="'Estado: ' + e.estado_label"></p>
                        </div>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="openEmpresaModal(true, e)" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="openDeleteEmpresaModal(e)" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <!-- Modal Empresas Cliente -->
    <x-admin.form-modal
        modalName="isEmpresaModalOpen"
        title="Empresa"
        submitLabel="Guardar Empresa"
        formId="empresa-form"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nombre_comercial" class="block text-sm font-medium nunito-bold">Nombre Comercial <span class="text-red-500">*</span></label>
                <input type="text" id="nombre_comercial" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 nunito-regular" maxlength="150" x-model="formEmpresa.nombre_comercial" required>
                <template x-if="errors.nombre_comercial">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.nombre_comercial[0]"></p>
                </template>
            </div>
            <div>
                <label for="razon_social" class="block text-sm font-medium nunito-bold">Razón Social</label>
                <input type="text" id="razon_social" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 nunito-regular" maxlength="150" x-model="formEmpresa.razon_social">
                <template x-if="errors.razon_social">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.razon_social[0]"></p>
                </template>
            </div>
            <div>
                <label for="rtn" class="block text-sm font-medium nunito-bold">RTN</label>
                <input type="text" id="rtn" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 nunito-regular" maxlength="30" x-model="formEmpresa.rtn">
                <template x-if="errors.rtn">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.rtn[0]"></p>
                </template>
            </div>
            <div class="md:col-span-2">
                <label for="descripcion_empresa" class="block text-sm font-medium nunito-bold">Descripción</label>
                <textarea id="descripcion_empresa" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 nunito-regular" rows="3" maxlength="255" x-model="formEmpresa.descripcion_empresa"></textarea>
                <template x-if="errors.descripcion_empresa">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.descripcion_empresa[0]"></p>
                </template>
            </div>
            <div>
                <label for="horario_atencion" class="block text-sm font-medium nunito-bold">Horario de atención</label>
                <input type="text" id="horario_atencion" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 nunito-regular" maxlength="50" x-model="formEmpresa.horario_atencion">
                <template x-if="errors.horario_atencion">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.horario_atencion[0]"></p>
                </template>
            </div>
            <div>
                <label for="fecha_registro" class="block text-sm font-medium nunito-bold">Fecha registro <span class="text-red-500">*</span></label>
                <input type="date" id="fecha_registro" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 nunito-regular" x-model="formEmpresa.fecha_registro" required>
                <template x-if="errors.fecha_registro">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_registro[0]"></p>
                </template>
            </div>
            <div>
                <label for="estado_cliente" class="block text-sm font-medium nunito-bold">Estado <span class="text-red-500">*</span></label>
                <select id="estado_cliente" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 nunito-regular" x-model="formEmpresa.estado_cliente" required>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal de confirmación para eliminar empresa cliente -->
    <x-admin.confirmation-modal
        modal-name="isDeleteEmpresaModalOpen"
        title="Eliminar Empresa Cliente"
        item-to-delete="empresaToDelete"
        item-name-property="nombre_comercial"
        message="¿Estás seguro de que deseas eliminar la empresa cliente" />

    <!-- Ajuste para eliminar espacio en blanco -->
    <style>
        body {
            margin-bottom: 0;
        }
    </style>
</div>