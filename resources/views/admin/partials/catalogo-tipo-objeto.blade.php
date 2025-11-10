<div x-data="{
    tab: 'tipos',
    tipos: [],
    isTipoModalOpen: false,
    isTipoEditModalOpen: false,
    isTipoDeleteModalOpen: false,
    tipoToEdit: {nombre: '', descripcion: ''},
    tipoToDelete: {nombre: '', descripcion: ''},
    ordenarPor: 'nombre',
    loadingTipos: false,
    searchTipos: '', 
    // Estados de validación para evitar ReferenceError cuando Alpine evalúa expresiones
    formTipoObjeto: { _touched: { nombre: false, descripcion: false } },
    formEditTipoObjeto: { _touched: { nombre: false, descripcion: false } },
    
    async fetchTipoObjetos() {
        await window.tipoObjetosApiHandlers.fetchTipoObjetos(this);
    },
    async storeTipoObjeto() {
        await window.tipoObjetosApiHandlers.storeTipoObjeto(this);
    },
    async updateTipoObjeto() {
        await window.tipoObjetosApiHandlers.updateTipoObjeto(this);
    },
    async deleteTipoObjeto() {
        await window.tipoObjetosApiHandlers.deleteTipoObjeto(this);
    },
    handleModalSubmit(e){
        if(e.detail.formId === 'form-agregar-tipo-objeto') this.storeTipoObjeto();
        if(e.detail.formId === 'form-editar-tipo-objeto') this.updateTipoObjeto();
    },
    handleDelete(){ if(this.isTipoDeleteModalOpen) this.deleteTipoObjeto(); },
    getFilteredTipos(){
        let list = Array.isArray(this.tipos) ? [...this.tipos] : [];
        const q = (this.searchTipos || '').toString().toLowerCase().trim();
        if (q) {
            list = list.filter(t =>
                (t.nombre || '').toString().toLowerCase().includes(q) ||
                (t.descripcion || '').toString().toLowerCase().includes(q)
            );
        }
        const field = this.ordenarPor || 'nombre';
        list.sort((a,b)=>{
            const av = (a?.[field] ?? '').toString().toLowerCase();
            const bv = (b?.[field] ?? '').toString().toLowerCase();
            if (av < bv) return -1; if (av > bv) return 1; return 0;
        });
        return list;
    }
}" x-init="fetchTipoObjetos()"
    @keydown.escape.window="isTipoModalOpen=false; isTipoEditModalOpen=false; isTipoDeleteModalOpen=false;"
    @modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="handleDelete()">
    <div x-show="tab === 'tipos'">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-4">Catálogo de Tipos de Objeto
            </h1>
        </div>

        <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
            <x-slot name="filters">
                @include('partials.filtros-generales', [
                'searchModel' => 'searchTipos',
                'ordenarOptions' => [
                'nombre' => 'Nombre',
                'descripcion' => 'Descripción'
                ]
                ])
            </x-slot>

            <x-slot name="actions">
                <div class="w-full flex justify-center sm:justify-end">
                    @perm(['Catálogo','Tipos de Objeto','Tipo de Objeto'], 'insercion')
                    <button
                        @click="formTipoObjeto = { _touched: { nombre:false, descripcion:false } }; tipoToEdit = { nombre: '', descripcion: '' }; isTipoModalOpen = true"
                        class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                        Agregar tipo
                    </button>
                    @else
                    <button type="button" disabled title="Sin permiso para crear"
                        class="w-11/12 sm:w-auto bg-green-600/60 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm opacity-60 cursor-not-allowed">
                        Agregar tipo
                    </button>
                    @endperm
                </div>
            </x-slot>

            <x-slot name="table">
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                        <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                            <tr>
                                <th class="py-2 px-4 text-left">Nombre</th>
                                <th class="py-2 px-4 text-left">Descripción</th>
                                <th class="py-2 px-4 text-left">Creado Por</th>
                                <th class="py-2 px-4 text-left">Fecha Creación</th>
                                <th class="py-2 px-4 text-left">Modificado Por</th>
                                <th class="py-2 px-4 text-left">Fecha Modificación</th>
                                <th class="py-2 px-4 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="loadingTipos">
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular">
                                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando tipos de objeto...
                                    </td>
                                </tr>
                            </template>
                            <template x-if="!loadingTipos && tipos.length === 0">
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular">
                                        No hay tipos de objeto registrados
                                    </td>
                                </tr>
                            </template>
                            <template x-if="!loadingTipos && getFilteredTipos().length > 0">
                                <template x-for="tipo in getFilteredTipos()" :key="tipo.id || tipo.nombre">
                                    <tr class="border-b dark:border-gray-700 nunito-regular">
                                        <td class="py-2 px-4" x-text="tipo.nombre"></td>
                                        <td class="py-2 px-4" x-text="tipo.descripcion"></td>
                                        <td class="py-2 px-4" x-text="tipo.creado_por"></td>
                                        <td class="py-2 px-4"
                                            x-text="new Date(tipo.fecha_creacion).toLocaleDateString()"></td>
                                        <td class="py-2 px-4" x-text="tipo.modificado_por || '-'"></td>
                                        <td class="py-2 px-4"
                                            x-text="tipo.fecha_modificacion ? new Date(tipo.fecha_modificacion).toLocaleDateString() : '-'">
                                        </td>
                                        <td class="py-2 px-4 flex gap-2">
                                            @perm(['Catálogo','Tipos de Objeto','Tipo de Objeto'], 'actualizacion')
                                            <button
                                                @click="formEditTipoObjeto = { _touched: { nombre:false, descripcion:false } }; tipoToEdit = {id: tipo.id, nombre: tipo.nombre, descripcion: tipo.descripcion}; isTipoEditModalOpen = true"
                                                class="text-blue-500 hover:text-blue-700"><i
                                                    class="fas fa-edit"></i></button>
                                            @else
                                            <span title="Sin permiso para editar"
                                                class="text-blue-300 cursor-not-allowed"><i
                                                    class="fas fa-edit"></i></span>
                                            @endperm
                                            @perm(['Catálogo','Tipos de Objeto','Tipo de Objeto'], 'eliminacion')
                                            <button
                                                @click="tipoToDelete = {id: tipo.id, nombre: tipo.nombre}; isTipoDeleteModalOpen = true"
                                                class="text-red-500 hover:text-red-700"><i
                                                    class="fas fa-trash"></i></button>
                                            @else
                                            <span title="Sin permiso para eliminar"
                                                class="text-red-300 cursor-not-allowed"><i
                                                    class="fas fa-trash"></i></span>
                                            @endperm
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </x-slot>

            <x-slot name="cards">
                <template x-if="loadingTipos">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-8 text-center text-gray-500 nunito-regular">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando tipos de objeto...
                    </div>
                </template>
                <template x-if="!loadingTipos && tipos.length === 0">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-8 text-center text-gray-500 nunito-regular">
                        No hay tipos de objeto registrados
                    </div>
                </template>
                <template x-if="!loadingTipos && getFilteredTipos().length > 0">
                    <template x-for="tipo in getFilteredTipos()" :key="tipo.id || tipo.nombre">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-gray-800 p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold"
                                            x-text="tipo.nombre"></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-1 text-sm">
                                <div><span
                                        class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span>
                                    <span class="text-gray-900 dark:text-gray-200 nunito-regular"
                                        x-text="tipo.descripcion"></span>
                                </div>
                                <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Creado
                                        por:</span>
                                    <span class="text-gray-900 dark:text-gray-200 nunito-regular"
                                        x-text="tipo.creado_por"></span>
                                </div>
                                <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Fecha
                                        creación:</span>
                                    <span class="text-gray-900 dark:text-gray-200 nunito-regular"
                                        x-text="new Date(tipo.fecha_creacion).toLocaleDateString()"></span>
                                </div>
                                <template x-if="tipo.modificado_por">
                                    <div><span
                                            class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Modificado
                                            por:</span>
                                        <span class="text-gray-900 dark:text-gray-200 nunito-regular"
                                            x-text="tipo.modificado_por"></span>
                                    </div>
                                </template>
                                <template x-if="tipo.fecha_modificacion">
                                    <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Fecha
                                            modificación:</span>
                                        <span class="text-gray-900 dark:text-gray-200 nunito-regular"
                                            x-text="new Date(tipo.fecha_modificacion).toLocaleDateString()"></span>
                                    </div>
                                </template>
                            </div>
                            <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                @perm(['Catálogo','Tipos de Objeto','Tipo de Objeto'], 'actualizacion')
                                <button
                                    @click="formEditTipoObjeto = { _touched: { nombre:false, descripcion:false } }; tipoToEdit = {id: tipo.id, nombre: tipo.nombre, descripcion: tipo.descripcion}; isTipoEditModalOpen = true"
                                    class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                @else
                                <button type="button" disabled title="Sin permiso para editar"
                                    class="px-3 py-1 text-xs bg-blue-600/60 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                @endperm
                                @perm(['Catálogo','Tipos de Objeto','Tipo de Objeto'], 'eliminacion')
                                <button
                                    @click="tipoToDelete = {id: tipo.id, nombre: tipo.nombre}; isTipoDeleteModalOpen = true"
                                    class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                                @else
                                <button type="button" disabled title="Sin permiso para eliminar"
                                    class="px-3 py-1 text-xs bg-red-600/60 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                                @endperm
                            </div>
                        </div>
                    </template>
                </template>
            </x-slot>

        </x-responsive-table>


        @perm(['Catálogo','Tipos de Objeto','Tipo de Objeto'], 'insercion')
        <x-admin.edit-modal modalName="isTipoModalOpen" title="Agregar Tipo de Objeto" submitLabel="Guardar Tipo"
            :itemToEdit="'tipoToEdit'" maxWidth="max-w-2xl" formId="form-agregar-tipo-objeto">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
                <input type="text" x-model="tipoToEdit.nombre" maxlength="150"
                    @input="formTipoObjeto = formTipoObjeto || { _touched: {} }; formTipoObjeto._touched.nombre = true"
                    @blur="formTipoObjeto = formTipoObjeto || { _touched: {} }; formTipoObjeto._touched.nombre = true"
                    :class="formTipoObjeto && formTipoObjeto._touched && formTipoObjeto._touched.nombre && (tipoToEdit.nombre === '' || (tipoToEdit.nombre && tipoToEdit.nombre.length > 150)) ? 'border-red-500' : ''"
                    class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Analítica">
                <small class="block text-xs nunito-regular mt-1"
                    :class="formTipoObjeto && formTipoObjeto._touched && formTipoObjeto._touched.nombre && (tipoToEdit.nombre === '' || (tipoToEdit.nombre && tipoToEdit.nombre.length > 150)) ? 'text-red-500' : ''">Requerido.
                    Máximo 150 caracteres.</small>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea x-model="tipoToEdit.descripcion" maxlength="255"
                    @input="formTipoObjeto = formTipoObjeto || { _touched: {} }; formTipoObjeto._touched.descripcion = true"
                    @blur="formTipoObjeto = formTipoObjeto || { _touched: {} }; formTipoObjeto._touched.descripcion = true"
                    :class="formTipoObjeto && formTipoObjeto._touched && formTipoObjeto._touched.descripcion && (tipoToEdit.descripcion === '' || (tipoToEdit.descripcion && tipoToEdit.descripcion.length > 255)) ? 'border-red-500' : ''"
                    class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Describe el tipo..."></textarea>
                <small class="block text-xs nunito-regular mt-1"
                    :class="formTipoObjeto && formTipoObjeto._touched && formTipoObjeto._touched.descripcion && (tipoToEdit.descripcion === '' || (tipoToEdit.descripcion && tipoToEdit.descripcion.length > 255)) ? 'text-red-500' : ''">Requerido.
                    Máximo 255 caracteres.</small>
            </div>
        </x-admin.edit-modal>
        @endperm

        @perm(['Catálogo','Tipos de Objeto','Tipo de Objeto'], 'actualizacion')
        <x-admin.edit-modal modalName="isTipoEditModalOpen" title="Editar Tipo de Objeto" submitLabel="Guardar Cambios"
            :itemToEdit="'tipoToEdit'" maxWidth="max-w-2xl" formId="form-editar-tipo-objeto">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
                <input type="text" x-model="tipoToEdit.nombre" maxlength="150"
                    @input="formEditTipoObjeto = formEditTipoObjeto || { _touched: {} }; formEditTipoObjeto._touched.nombre = true"
                    @blur="formEditTipoObjeto = formEditTipoObjeto || { _touched: {} }; formEditTipoObjeto._touched.nombre = true"
                    :class="formEditTipoObjeto && formEditTipoObjeto._touched && formEditTipoObjeto._touched.nombre && (tipoToEdit.nombre === '' || (tipoToEdit.nombre && tipoToEdit.nombre.length > 150)) ? 'border-red-500' : ''"
                    class="w-full border rounded px-3 py-2 nunito-regular">
                <small class="block text-xs nunito-regular mt-1"
                    :class="formEditTipoObjeto && formEditTipoObjeto._touched && formEditTipoObjeto._touched.nombre && (tipoToEdit.nombre === '' || (tipoToEdit.nombre && tipoToEdit.nombre.length > 150)) ? 'text-red-500' : ''">Requerido.
                    Máximo 150 caracteres.</small>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea x-model="tipoToEdit.descripcion" maxlength="255"
                    @input="formEditTipoObjeto = formEditTipoObjeto || { _touched: {} }; formEditTipoObjeto._touched.descripcion = true"
                    @blur="formEditTipoObjeto = formEditTipoObjeto || { _touched: {} }; formEditTipoObjeto._touched.descripcion = true"
                    :class="formEditTipoObjeto && formEditTipoObjeto._touched && formEditTipoObjeto._touched.descripcion && (tipoToEdit.descripcion === '' || (tipoToEdit.descripcion && tipoToEdit.descripcion.length > 255)) ? 'border-red-500' : ''"
                    class="w-full border rounded px-3 py-2 nunito-regular"></textarea>
                <small class="block text-xs nunito-regular mt-1"
                    :class="formEditTipoObjeto && formEditTipoObjeto._touched && formEditTipoObjeto._touched.descripcion && (tipoToEdit.descripcion === '' || (tipoToEdit.descripcion && tipoToEdit.descripcion.length > 255)) ? 'text-red-500' : ''">Requerido.
                    Máximo 255 caracteres.</small>
            </div>
        </x-admin.edit-modal>
        @endperm

        @perm(['Catálogo','Tipos de Objeto','Tipo de Objeto'], 'eliminacion')
        <x-admin.confirmation-modal modalName="isTipoDeleteModalOpen" title="Confirmar Eliminación"
            :itemToDelete="'tipoToDelete'" itemNameProperty="nombre"
            message="¿Estás seguro de que deseas eliminar el tipo de objeto" />
        @endperm

    </div>
</div>