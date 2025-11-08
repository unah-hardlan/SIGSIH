<div x-data="{
    isServicioRealizadoModalOpen: false,
    isServicioRealizadoEditModalOpen: false,
    isServicioRealizadoDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    serviciosRealizados: [],
    loadingServiciosRealizados: false,
    
    numbersServiciosRealizados: [],
    currentPageServiciosRealizados: 1,
    perPageServiciosRealizados: 10,

    nombre_servicio: '',
    descripcion_servicio: '',
    filtroServicioRealizado: '',
    ordenarPor: 'nombre_servicio',
    get filteredServiciosRealizados() {
        const term = String(this.filtroServicioRealizado || '').toLowerCase().trim();
        const sortKey = this.ordenarPor || 'nombre_servicio';
        let items = Array.isArray(this.serviciosRealizados) ? [...this.serviciosRealizados] : [];

        if (term) {
            items = items.filter((s) => {
                const parts = [
                    s?.nombre_servicio,
                    s?.descripcion_servicio,
                    String(s?.id_servicio_realizado_pk)
                ].map(v => String(v ?? '').toLowerCase());
                return parts.some(p => p.includes(term));
            });
        }

        items.sort((a,b) => {
            let va = a?.[sortKey];
            let vb = b?.[sortKey];
            va = String(va ?? '').toLowerCase();
            vb = String(vb ?? '').toLowerCase();
            if (va < vb) return -1;
            if (va > vb) return 1;
            return 0;
        });

        return items;
    },

    paginatedServiciosRealizados() {
        return this.filteredServiciosRealizados.slice(
            (this.currentPageServiciosRealizados - 1) * this.perPageServiciosRealizados,
            this.currentPageServiciosRealizados * this.perPageServiciosRealizados
        );
    },
    totalPagesServiciosRealizados() {
        return Math.ceil(this.filteredServiciosRealizados.length / this.perPageServiciosRealizados);
    },
    nextPageServiciosRealizados() {
        if (this.currentPageServiciosRealizados < this.totalPagesServiciosRealizados()) {
            this.currentPageServiciosRealizados++;
        }
    },
    prevPageServiciosRealizados() {
        if (this.currentPageServiciosRealizados > 1) {
            this.currentPageServiciosRealizados--;
        }
    },
    
    // 3️⃣ Sincronizar Alias en cada operación CRUD
    async fetchServiciosRealizados() {
        await window.serviciosRealizadosApiHandlers.fetchServiciosRealizados(this);
        this.numbersServiciosRealizados = this.serviciosRealizados; 
    },
    async submitServicioRealizado() {
        await window.serviciosRealizadosApiHandlers.submitServicioRealizado(this);
        this.fetchServiciosRealizados(); 
    },
    async updateServicioRealizado() {
        await window.serviciosRealizadosApiHandlers.updateServicioRealizado(this);
        this.fetchServiciosRealizados(); 
    },
    async deleteServicioRealizado() {
        await window.serviciosRealizadosAervicioRealizado(this);
        this.fetchServiciosRealizados(); 
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formServicioRealizado') this.submitServicioRealizado();
        if(event.detail.formId === 'formEditServicioRealizado') this.updateServicioRealizado();
    },
    handleDelete() {
        if (this.isServicioRealizadoDeleteModalOpen) {
            this.deleteServicioRealizado();
        }
    }
}"
    x-init="fetchServiciosRealizados()"
    x-effect="
    $watch('filtroServicioRealizado', () => currentPageServiciosRealizados = 1);
    $watch('ordenarPor', () => currentPageServiciosRealizados = 1);
"
    @keydown.escape.window="
    isServicioRealizadoModalOpen = false;
    isServicioRealizadoEditModalOpen = false;
    isServicioRealizadoDeleteModalOpen = false;
" @modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="handleDelete()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Servicios Realizados
        </h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'filtroServicioRealizado',
            'ordenarOptions' => [
            'nombre_servicio' => 'Nombre del Servicio',
            'descripcion_servicio' => 'Descripción'
            ]
            ])
        </x-slot>

        <x-slot name="actions">
            @perm(['Catálogo','Servicios Realizados','Servicio Realizado'], 'insercion')
            <button @click="formServicioRealizado = { _touched: {} }; nombre_servicio = ''; descripcion_servicio = ''; isServicioRealizadoModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo servicio realizado
            </button>
            @else
            <button type="button" disabled title="Sin permiso para crear"
                class="bg-gray-400 text-white px-4 py-2 rounded-lg nunito-regular opacity-60 cursor-not-allowed whitespace-nowrap text-sm">
                Nuevo servicio realizado
            </button>
            @endperm
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Nombre del Servicio</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Descripción</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingServiciosRealizados">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando servicios realizados...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingServiciosRealizados && serviciosRealizados.length === 0">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay servicios realizados registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingServiciosRealizados && filteredServiciosRealizados.length > 0">
                        <template x-for="(servicioRealizado, index) in paginatedServiciosRealizados()"
                            :key="servicioRealizado.id_servicio_realizado_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedServiciosRealizados().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="servicioRealizado.nombre_servicio"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="servicioRealizado.descripcion_servicio"></td>
                                <td class="py-2 px-4 flex gap-2"
                                    :class="{ 'last:rounded-br-lg': index === paginatedServiciosRealizados().length - 1 }">
                                    @perm(['Catálogo','Servicios Realizados','Servicio Realizado'], 'actualizacion')
                                    <a href="#"
                                        @click.prevent="formEditServicioRealizado = { _touched: {} }; isServicioRealizadoEditModalOpen = true; itemToEdit = {id_servicio_realizado_pk: servicioRealizado.id_servicio_realizado_pk, nombre_servicio: servicioRealizado.nombre_servicio, descripcion_servicio: servicioRealizado.descripcion_servicio}"
                                        class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para editar"><i class="fas fa-edit"></i></span>
                                    @endperm
                                    @perm(['Catálogo','Servicios Realizados','Servicio Realizado'], 'eliminacion')
                                    <a href="#"
                                        @click.prevent="isServicioRealizadoDeleteModalOpen = true; itemToDelete = {id_servicio_realizado_pk: servicioRealizado.id_servicio_realizado_pk, nombre_servicio: servicioRealizado.nombre_servicio}"
                                        class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para eliminar"><i class="fas fa-trash"></i></span>
                                    @endperm
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingServiciosRealizados">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando servicios realizados...
                </div>
            </template>
            <template x-if="!loadingServiciosRealizados && serviciosRealizados.length === 0">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay servicios realizados registrados
                </div>
            </template>
            <template x-if="!loadingServiciosRealizados && filteredServiciosRealizados.length > 0">
                <template x-for="servicioRealizado in paginatedServiciosRealizados()"
                    :key="servicioRealizado.id_servicio_realizado_pk">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold"
                                x-text="servicioRealizado.nombre_servicio"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular"
                            x-text="servicioRealizado.descripcion_servicio"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            @perm(['Catálogo','Servicios Realizados','Servicio Realizado'], 'actualizacion')
                            <button
                                @click.prevent="formEditServicioRealizado = { _touched: {} }; isServicioRealizadoEditModalOpen = true; itemToEdit = {id_servicio_realizado_pk: servicioRealizado.id_servicio_realizado_pk, nombre_servicio: servicioRealizado.nombre_servicio, descripcion_servicio: servicioRealizado.descripcion_servicio}"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para editar"
                                class="px-3 py-1 text-xs bg-gray-400 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm
                            @perm(['Catálogo','Servicios Realizados','Servicio Realizado'], 'eliminacion')
                            <button
                                @click.prevent="isServicioRealizadoDeleteModalOpen = true; itemToDelete = {id_servicio_realizado_pk: servicioRealizado.id_servicio_realizado_pk, nombre_servicio: servicioRealizado.nombre_servicio}"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para eliminar"
                                class="px-3 py-1 text-xs bg-gray-400 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <div x-show="filteredServiciosRealizados.length > perPageServiciosRealizados" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <div class="mb-2">
            <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageServiciosRealizados - 1) * perPageServiciosRealizados + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageServiciosRealizados * perPageServiciosRealizados, filteredServiciosRealizados.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="filteredServiciosRealizados.length"></strong>
                resultados
            </span>
        </div>

        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageServiciosRealizados()" :disabled="currentPageServiciosRealizados === 1"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Anterior</span>
            </button>

            <div class="flex items-center gap-1">
                <template x-for="page in Array.from({length: totalPagesServiciosRealizados()}, (_, i) => i + 1).slice(Math.max(0, currentPageServiciosRealizados - 3), currentPageServiciosRealizados + 2)" :key="page">
                    <button @click="currentPageServiciosRealizados = page"
                        class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        :class="page === currentPageServiciosRealizados ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>

            <button @click="nextPageServiciosRealizados()" :disabled="currentPageServiciosRealizados === totalPagesServiciosRealizados()"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <div>
        @perm(['Catálogo','Servicios Realizados','Servicio Realizado'], 'insercion')
        <x-admin.form-modal class="nunito-bold" modalName="isServicioRealizadoModalOpen"
            title="Nuevo Servicio Realizado" submitLabel="Guardar Servicio Realizado" formId="formServicioRealizado"
            maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del
                        Servicio</label>
                    <input type="text" id="nombre_servicio" x-model="nombre_servicio" required maxlength="150"
                        @input="formServicioRealizado = formServicioRealizado || { _touched: {} }; formServicioRealizado._touched.nombre_servicio = true"
                        @blur="formServicioRealizado = formServicioRealizado || { _touched: {} }; formServicioRealizado._touched.nombre_servicio = true"
                        :class="formServicioRealizado && formServicioRealizado._touched && formServicioRealizado._touched.nombre_servicio && (nombre_servicio === '' || (nombre_servicio && nombre_servicio.length > 150)) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <small class="block text-xs nunito-regular mt-1" :class="formServicioRealizado && formServicioRealizado._touched && formServicioRealizado._touched.nombre_servicio && (nombre_servicio === '' || (nombre_servicio && nombre_servicio.length > 150)) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div class="col-span-2">
                    <label for="descripcion_servicio"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion_servicio" x-model="descripcion_servicio" rows="2" maxlength="255"
                        @input="formServicioRealizado = formServicioRealizado || { _touched: {} }; formServicioRealizado._touched.descripcion_servicio = true"
                        @blur="formServicioRealizado = formServicioRealizado || { _touched: {} }; formServicioRealizado._touched.descripcion_servicio = true"
                        :class="formServicioRealizado && formServicioRealizado._touched && formServicioRealizado._touched.descripcion_servicio && (descripcion_servicio === '' || (descripcion_servicio && descripcion_servicio.length > 255)) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                    <small class="block text-xs nunito-regular mt-1" :class="formServicioRealizado && formServicioRealizado._touched && formServicioRealizado._touched.descripcion_servicio && (descripcion_servicio === '' || (descripcion_servicio && descripcion_servicio.length > 255)) ? 'text-red-500' : ''">Requerido. Máximo 255 caracteres.</small>
                </div>
            </div>
        </x-admin.form-modal>
        @endperm

        @perm(['Catálogo','Servicios Realizados','Servicio Realizado'], 'actualizacion')
        <x-admin.edit-modal class="nunito-bold" modalName="isServicioRealizadoEditModalOpen"
            title="Editar Servicio Realizado" itemToEdit="itemToEdit" maxWidth="max-w-2xl"
            formId="formEditServicioRealizado">
            <template x-if="itemToEdit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_nombre_servicio"
                            class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Servicio</label>
                        <input type="text" id="edit_nombre_servicio" x-model="itemToEdit.nombre_servicio" required maxlength="150"
                            @input="formEditServicioRealizado = formEditServicioRealizado || { _touched: {} }; formEditServicioRealizado._touched.nombre_servicio = true"
                            @blur="formEditServicioRealizado = formEditServicioRealizado || { _touched: {} }; formEditServicioRealizado._touched.nombre_servicio = true"
                            :class="formEditServicioRealizado && formEditServicioRealizado._touched && formEditServicioRealizado._touched.nombre_servicio && (itemToEdit.nombre_servicio === '' || (itemToEdit.nombre_servicio && itemToEdit.nombre_servicio.length > 150)) ? 'border-red-500' : ''"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <small class="block text-xs nunito-regular mt-1" :class="formEditServicioRealizado && formEditServicioRealizado._touched && formEditServicioRealizado._touched.nombre_servicio && (itemToEdit.nombre_servicio === '' || (itemToEdit.nombre_servicio && itemToEdit.nombre_servicio.length > 150)) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                    </div>
                    <div class="col-span-2">
                        <label for="edit_descripcion_servicio"
                            class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                        <textarea id="edit_descripcion_servicio" x-model="itemToEdit.descripcion_servicio" rows="2" maxlength="255"
                            @input="formEditServicioRealizado = formEditServicioRealizado || { _touched: {} }; formEditServicioRealizado._touched.descripcion_servicio = true"
                            @blur="formEditServicioRealizado = formEditServicioRealizado || { _touched: {} }; formEditServicioRealizado._touched.descripcion_servicio = true"
                            :class="formEditServicioRealizado && formEditServicioRealizado._touched && formEditServicioRealizado._touched.descripcion_servicio && (itemToEdit.descripcion_servicio === '' || (itemToEdit.descripcion_servicio && itemToEdit.descripcion_servicio.length > 255)) ? 'border-red-500' : ''"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                        <small class="block text-xs nunito-regular mt-1" :class="formEditServicioRealizado && formEditServicioRealizado._touched && formEditServicioRealizado._touched.descripcion_servicio && (itemToEdit.descripcion_servicio === '' || (itemToEdit.descripcion_servicio && itemToEdit.descripcion_servicio.length > 255)) ? 'text-red-500' : ''">Requerido. Máximo 255 caracteres.</small>
                    </div>
                </div>
            </template>
        </x-admin.edit-modal>
        @endperm

        @perm(['Catálogo','Servicios Realizados','Servicio Realizado'], 'eliminacion')
        <x-admin.confirmation-modal class="nunito-regular" modalName="isServicioRealizadoDeleteModalOpen"
            itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar este servicio realizado?" />
        @endperm
    </div>
</div>