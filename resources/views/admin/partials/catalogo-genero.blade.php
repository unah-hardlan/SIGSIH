<div x-data="{
    isGeneroModalOpen: false,
    isGeneroEditModalOpen: false,
    isGeneroDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    generos: [],
    loadingGeneros: false,
    
    numbersGeneros: [],
    currentPageGeneros: 1,
    perPageGeneros: 10,

    genero: '',
    formGenero: { _touched: {} },
    formEditGenero: { _touched: {} },
    filtroGenero: '',
    ordenarPor: 'genero',
    
    get filteredGeneros() {
        const term = String(this.filtroGenero || '').toLowerCase().trim();
        const sortKey = this.ordenarPor || 'genero';
        let items = Array.isArray(this.generos) ? [...this.generos] : [];

        if (term) {
            items = items.filter((g) => {
                const parts = [
                    g?.genero,
                    String(g?.id_genero_pk)
                ].map(v => String(v ?? '').toLowerCase());
                return parts.some(p => p.includes(term));
            });
        }

        items.sort((a,b) => {
            let va = a?.[sortKey];
            let vb = b?.[sortKey];
            if (sortKey === 'genero') {
                va = String(va ?? '').toLowerCase();
                vb = String(vb ?? '').toLowerCase();
            } else if (sortKey === 'id_genero_pk') {
                va = Number(va ?? 0);
                vb = Number(vb ?? 0);
            }
            if (va < vb) return -1;
            if (va > vb) return 1;
            return 0;
        });

        return items;
    },

    paginatedGeneros() {
        return this.filteredGeneros.slice(
            (this.currentPageGeneros - 1) * this.perPageGeneros,
            this.currentPageGeneros * this.perPageGeneros
        );
    },
    totalPagesGeneros() {
        return Math.ceil(this.filteredGeneros.length / this.perPageGeneros);
    },
    nextPageGeneros() {
        if (this.currentPageGeneros < this.totalPagesGeneros()) {
            this.currentPageGeneros++;
        }
    },
    prevPageGeneros() {
        if (this.currentPageGeneros > 1) {
            this.currentPageGeneros--;
        }
    },

    async fetchGeneros() {
        await window.generosApiHandlers.fetchGeneros(this);
        this.numbersGeneros = this.generos; 
    },
    async submitGenero() {
        await window.generosApiHandlers.submitGenero(this);
        this.fetchGeneros();
    },
    async updateGenero() {
        await window.generosApiHandlers.updateGenero(this);
        this.fetchGeneros(); 
    },
    async deleteGenero() {
        await window.generosApiHandlers.deleteGenero(this);
        this.fetchGeneros(); 
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formGenero') this.submitGenero();
        if(event.detail.formId === 'formEditGenero') this.updateGenero();
    },
    handleDelete() {
        if (this.isGeneroDeleteModalOpen) {
            this.deleteGenero();
        }
    }
}"
    x-init="fetchGeneros()"
    x-effect="
    $watch('filtroGenero', () => currentPageGeneros = 1);
    $watch('ordenarPor', () => currentPageGeneros = 1);
"
    @keydown.escape.window="
    isGeneroModalOpen = false;
    isGeneroEditModalOpen = false;
    isGeneroDeleteModalOpen = false;
" @modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="handleDelete()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Géneros</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'filtroGenero',
            'ordenarOptions' => [
            'genero' => 'Nombre',
            'id_genero_pk' => 'ID Género'
            ]
            ])
        </x-slot>

        <x-slot name="actions">
            @perm(['Catálogo','Género','Genero','Géneros'], 'insercion')
            <button @click="formGenero = { _touched: {} }; genero = ''; isGeneroModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo género
            </button>
            @else
            <button type="button" disabled title="Sin permiso para crear"
                class="bg-gray-400 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm opacity-70 cursor-not-allowed">
                Nuevo género
            </button>
            @endperm
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Nombre</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingGeneros">
                        <tr>
                            <td colspan="2" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando géneros...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingGeneros && generos.length === 0">
                        <tr>
                            <td colspan="2" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay géneros registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingGeneros && filteredGeneros.length > 0">
                        <template x-for="(genero, index) in paginatedGeneros()" :key="genero.id_genero_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedGeneros().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="genero.genero"></td>
                                <td class="py-2 px-4 flex gap-2"
                                    :class="{ 'last:rounded-br-lg': index === paginatedGeneros().length - 1 }">
                                    @perm(['Catálogo','Género','Genero','Géneros'], 'actualizacion')
                                    <a href="#"
                                        @click.prevent="formEditGenero = { _touched: {} }; isGeneroEditModalOpen = true; itemToEdit = {id_genero_pk: genero.id_genero_pk, genero: genero.genero}"
                                        class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para editar"><i class="fas fa-edit"></i></span>
                                    @endperm
                                    @perm(['Catálogo','Género','Genero','Géneros'], 'eliminacion')
                                    <a href="#"
                                        @click.prevent="isGeneroDeleteModalOpen = true; itemToDelete = {id_genero_pk: genero.id_genero_pk, nombre: genero.genero}"
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
            <template x-if="loadingGeneros">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando géneros...
                </div>
            </template>
            <template x-if="!loadingGeneros && generos.length === 0">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay géneros registrados
                </div>
            </template>
            <template x-if="!loadingGeneros && filteredGeneros.length > 0">
                <template x-for="genero in paginatedGeneros()" :key="genero.id_genero_pk">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold"
                                x-text="genero.genero"></h3>
                        </div>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            @perm(['Catálogo','Género','Genero','Géneros'], 'actualizacion')
                            <button
                                @click.prevent="formEditGenero = { _touched: {} }; isGeneroEditModalOpen = true; itemToEdit = {id_genero_pk: genero.id_genero_pk, genero: genero.genero}"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para editar"
                                class="px-3 py-1 text-xs bg-gray-400 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm
                            @perm(['Catálogo','Género','Genero','Géneros'], 'eliminacion')
                            <button
                                @click.prevent="isGeneroDeleteModalOpen = true; itemToDelete = {id_genero_pk: genero.id_genero_pk, nombre: genero.genero}"
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

    <div x-show="filteredGeneros.length > perPageGeneros" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <div class="mb-2">
            <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageGeneros - 1) * perPageGeneros + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageGeneros * perPageGeneros, filteredGeneros.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="filteredGeneros.length"></strong>
                resultados
            </span>
        </div>

        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageGeneros()" :disabled="currentPageGeneros === 1"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Anterior</span>
            </button>

            <div class="flex items-center gap-1">
                <template x-for="page in Array.from({length: totalPagesGeneros()}, (_, i) => i + 1).slice(Math.max(0, currentPageGeneros - 3), currentPageGeneros + 2)" :key="page">
                    <button @click="currentPageGeneros = page"
                        class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-blue-900 hover:text-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        :class="page === currentPageGeneros ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>

            <button @click="nextPageGeneros()" :disabled="currentPageGeneros === totalPagesGeneros()"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <div>
        @perm(['Catálogo','Género','Genero','Géneros'], 'insercion')
        <x-admin.form-modal class="nunito-bold" modalName="isGeneroModalOpen" title="Nuevo Género"
            submitLabel="Guardar Género" formId="formGenero" maxWidth="max-w-md">
            <div>
                <label for="genero" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                <input type="text" id="genero" x-model="genero" required maxlength="100"
                    @input="formGenero = formGenero || { _touched: {} }; formGenero._touched.genero = true"
                    @blur="formGenero._touched.genero = true"
                    :class="formGenero && formGenero._touched && formGenero._touched.genero && (genero === '' || genero.length > 100) ? 'border-red-500' : ''"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                <small :class="formGenero && formGenero._touched && formGenero._touched.genero && (genero === '' || genero.length > 100) ? 'text-red-500' : ''">Requerido. Máximo 100 caracteres.</small>
            </div>
        </x-admin.form-modal>
        @endperm

        @perm(['Catálogo','Género','Genero','Géneros'], 'actualizacion')
        <x-admin.edit-modal class="nunito-bold" modalName="isGeneroEditModalOpen" title="Editar Género"
            itemToEdit="itemToEdit" maxWidth="max-w-md" formId="formEditGenero">
            <template x-if="itemToEdit">
                <div>
                    <label for="edit_genero" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="edit_genero" x-model="itemToEdit.genero" required maxlength="100"
                        @input="formEditGenero = formEditGenero || { _touched: {} }; formEditGenero._touched.genero = true"
                        @blur="formEditGenero._touched.genero = true"
                        :class="formEditGenero && formEditGenero._touched && formEditGenero._touched.genero && (itemToEdit.genero === '' || itemToEdit.genero.length > 100) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <small :class="formEditGenero && formEditGenero._touched && formEditGenero._touched.genero && (itemToEdit.genero === '' || itemToEdit.genero.length > 100) ? 'text-red-500' : ''">Requerido. Máximo 100 caracteres.</small>
                </div>
            </template>
        </x-admin.edit-modal>
        @endperm

        @perm(['Catálogo','Género','Genero','Géneros'], 'eliminacion')
        <x-admin.confirmation-modal class="nunito-regular" modalName="isGeneroDeleteModalOpen"
            itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar este género?" />
        @endperm
    </div>
</div>