<div x-data="{
        tab: 'empresas',
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
                    fecha_registro: (empresa.fecha_registro || new Date().toISOString().slice(0,10)),
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
            return {
                id: e.id_cliente_fk || e.id || Math.random(),
                nombre_comercial: e.nombre_comercial || '—',
                razon_social: e.razon_social || '',
                rtn: e.rtn || '',
                descripcion_empresa: e.descripcion_empresa || '',
                horario_atencion: e.horario_atencion || '',
                fecha_registro: (e.fecha_registro || '').toString().split('T')[0] || '',
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
    }" @include('partials.persist-tab', ['tabKey'=> 'admin-gestion-empresas-tab'])
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

    <!-- Tabs -->
    <ul class="flex border-b nunito-bold mb-6 flex-wrap gap-2">
        <li
            :class="'border-b-2 border-blue-500 text-blue-500'"
            class="pb-2 mr-4 nunito-bold">Empresas</li>
    </ul>

    <!-- TAB 1: Empresas Cliente -->
    <div x-show="tab==='empresas'" class="overflow-x-auto">
        <x-admin.tabla-crud class="nunito-bold" :titulo="'Empresas'">
            <x-slot name="filtros">
                @include('partials.filtros-generales', [
                'searchModel' => 'searchEmpresa',
                'filtrosSelect' => [],
                'ordenarOptions' => [
                'nombre_comercial' => 'Nombre',
                'estado_cliente' => 'Estado'
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
                    <template x-for="e in empresas" :key="e.id">
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4 nunito-regular" x-text="e.nombre_comercial"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="e.razon_social"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="e.descripcion_empresa"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="e.rtn"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="e.fecha_registro"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="e.horario_atencion"></td>
                            <td class="py-2 px-4"><span class="px-2 py-1 rounded nunito-regular" :class="e.estado_label==='Activo' ? 'bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-100' : 'bg-red-700 text-red-100'" x-text="e.estado_label"></span></td>
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

    <!-- Modal Empresas Cliente -->
    <x-admin.form-modal
        modalName="isEmpresaModalOpen"
        title="Empresa"
        submitLabel="Guardar Empresa"
        formId="empresa-form"
        maxWidth="max-w-md">

        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Nombre Comercial <span class="text-red-500">*</span></label>
            <input type="text" class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" maxlength="150" x-model="formEmpresa.nombre_comercial" required>
            <template x-if="errors.nombre_comercial">
                <p class='text-xs text-red-600 mt-1' x-text="errors.nombre_comercial[0]"></p>
            </template>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Razón Social</label>
            <input type="text" class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" maxlength="150" x-model="formEmpresa.razon_social">
            <template x-if="errors.razon_social">
                <p class='text-xs text-red-600 mt-1' x-text="errors.razon_social[0]"></p>
            </template>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">RTN</label>
            <input type="text" class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" maxlength="30" x-model="formEmpresa.rtn">
            <template x-if="errors.rtn">
                <p class='text-xs text-red-600 mt-1' x-text="errors.rtn[0]"></p>
            </template>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Descripción</label>
            <textarea class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" rows="2" maxlength="255" x-model="formEmpresa.descripcion_empresa"></textarea>
            <template x-if="errors.descripcion_empresa">
                <p class='text-xs text-red-600 mt-1' x-text="errors.descripcion_empresa[0]"></p>
            </template>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Horario de atención</label>
            <input type="text" class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" maxlength="50" x-model="formEmpresa.horario_atencion">
            <template x-if="errors.horario_atencion">
                <p class='text-xs text-red-600 mt-1' x-text="errors.horario_atencion[0]"></p>
            </template>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium mb-1 nunito-bold">Fecha registro <span class="text-red-500">*</span></label>
                <input type="date" class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" x-model="formEmpresa.fecha_registro" required>
                <template x-if="errors.fecha_registro">
                    <p class='text-xs text-red-600 mt-1' x-text="errors.fecha_registro[0]"></p>
                </template>
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold">Estado <span class="text-red-500">*</span></label>
                <select class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" x-model="formEmpresa.estado_cliente" required>
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
</div>