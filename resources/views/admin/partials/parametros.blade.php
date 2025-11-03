<div x-data="parametrosCrud" x-init="init()" class="bg-white dark:bg-gray-900 rounded-xl shadow-lg">

    <div class="p-4 md:p-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold">Gestión de Parámetros</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
                'searchModel' => 'search',
                'filtrosSelect' => [],
                'ordenarOptions' => ['parametro' => 'Parámetro', 'valor' => 'Valor', 'creado' => 'Creación']
            ])
        </x-slot>

        <x-slot name="actions">
            <div class="flex flex-col sm:flex-row items-center gap-2">
                <button @click="openCreate()" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i> Agregar Parámetro
                </button>
                <button @click="openReporte()" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-file-alt"></i> Generar Reporte
                </button>
            </div>
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg text-gray-700 dark:text-gray-200">Parámetro</th>
                        <th class="py-2 px-4 text-left border-0 text-gray-700 dark:text-gray-200">Valor</th>
                        <th class="py-2 px-4 text-left border-0 text-gray-700 dark:text-gray-200">Creado por</th>
                        <th class="py-2 px-4 text-left border-0 text-gray-700 dark:text-gray-200">Creación</th>
                        <th class="py-2 px-4 text-left border-0 last:rounded-tr-lg text-gray-700 dark:text-gray-200">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <tr><td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-400 nunito-regular"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando...</td></tr>
                    </template>
                    <template x-if="!loading && parametros.length === 0">
                        <tr><td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-400 nunito-regular">Sin resultados</td></tr>
                    </template>
                    <template x-if="!loading && parametros.length > 0">
                        <template x-for="(p, index) in paginatedParametros()" :key="p.id">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedParametros().length - 1 }">
                                <td class="py-2 px-4 text-gray-700 dark:text-gray-300" x-text="p.parametro"></td>
                                <td class="py-2 px-4 text-gray-700 dark:text-gray-300" x-text="p.valor"></td>
                                <td class="py-2 px-4 text-gray-700 dark:text-gray-300" x-text="p.creado_por || '-'"></td>
                                <td class="py-2 px-4 text-gray-700 dark:text-gray-300" x-text="p.fecha_creacion_formatted || p.fecha_creacion || '-' "></td>
                                <td class="py-2 px-4 flex gap-2">
                                    <button @click="openEdit(p)" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                                    <button @click="openDelete(p)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <div class="space-y-4 px-2 sm:px-0">
                <template x-if="loading">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-gray-600 p-8 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando...
                    </div>
                </template>
                <template x-if="!loading && parametros.length === 0">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-gray-600 p-8 text-center text-gray-500 dark:text-gray-400">
                        Sin resultados
                    </div>
                </template>
                <template x-if="!loading && parametros.length > 0">
                    <template x-for="p in paginatedParametros()" :key="p.id">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3 border border-black dark:border-gray-600 max-w-full">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white break-words" x-text="p.parametro"></h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300 pt-1" x-text="p.valor"></p>
                            </div>
                            <p class="text-xs text-gray-400">Creado por: <span x-text="p.creado_por || '-'"></span> el <span x-text="p.fecha_creacion_formatted || p.fecha_creacion || '-' "></span></p>
                            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700 flex-wrap">
                                <button @click="openEdit(p)" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1"><i class="fas fa-edit"></i> Editar</button>
                                <button @click="openDelete(p)" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1"><i class="fas fa-trash"></i> Eliminar</button>
                            </div>
                        </div>
                    </template>
                </template>
            </div>
        </x-slot>
    </x-responsive-table>

    <!-- Paginación client-side -->
    <div x-show="numbersParametros.length > perPageParametros" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <div class="mb-2">
            <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageParametros - 1) * perPageParametros + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageParametros * perPageParametros, numbersParametros.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="numbersParametros.length"></strong>
                resultados
            </span>
        </div>
        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageParametros()" :disabled="currentPageParametros === 1"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                <span>Anterior</span>
            </button>
            <div class="flex items-center gap-1">
                <template x-for="page in Array.from({length: totalPagesParametros()}, (_, i) => i + 1).slice(Math.max(0, currentPageParametros - 3), currentPageParametros + 2)" :key="page">
                    <button @click="currentPageParametros = page"
                            class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                            :class="page === currentPageParametros ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>
            <button @click="nextPageParametros()" :disabled="currentPageParametros === totalPagesParametros()"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>

    <div x-show="error" class="mt-2 text-red-500 text-sm text-center" x-text="error"></div>

    <!-- Modales -->
    <div>
        <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Agregar Parámetro" submitLabel="Guardar" formId="formCrearParametro" maxWidth="max-w-md">
            <div class="space-y-4 text-gray-700 dark:text-gray-200">
                <div>
                    <label class="block text-sm font-medium">Parámetro</label>
                    <input type="text"
                           x-model="createForm.parametro"
                           @blur="createForm._touched = createForm._touched || {}; createForm._touched.parametro = true"
                           @input="createForm._touched = createForm._touched || {}; createForm._touched.parametro = true"
                           :class="{'border-red-500': (createForm._touched && createForm._touched.parametro)  && (createForm.parametro === '' || createForm.parametro.length >= 50)}"
                           maxlength="50"
                           class="mt-1 w-full border border-gray-500 dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800 dark:text-gray-200"
                           required
                           autocomplete="off">
                    <small :class="(createForm._touched && createForm._touched.parametro) && (createForm.parametro === '' || createForm.parametro.length >= 50) ? 'text-red-500' : 'text-gray-500 dark:text-white'" class="text-xs">Requerido. Máximo 50 caracteres.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium">Valor</label>
                    <input type="text"
                           x-model="createForm.valor"
                           @blur="createForm._touched = createForm._touched || {}; createForm._touched.valor = true"
                           @input="createForm._touched = createForm._touched || {}; createForm._touched.valor = true"
                           :class="{'border-red-500': (createForm._touched && createForm._touched.valor) && (createForm.valor === '' || createForm.valor.length >= 100)}"
                           maxlength="100"
                           class="mt-1 w-full border border-gray-500 dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800 dark:text-gray-200"
                           required
                           autocomplete="off">
                    <small :class="(createForm._touched && createForm._touched.valor) && (createForm.valor === '' || createForm.valor.length >= 100) ? 'text-red-500' : 'text-gray-500 dark:text-white'" class="text-xs">Requerido. Máximo 100 caracteres.</small>
                </div>
                <div class="text-red-500 text-sm" x-show="formError" x-text="formError"></div>
            </div>
        </x-admin.form-modal>

        <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Parámetro" itemToEdit="parametroToEdit" formId="formEditarParametro" maxWidth="max-w-md">
            <div class="space-y-4 text-gray-700 dark:text-gray-200">
                <div>
                    <label class="block text-sm font-medium">Parámetro</label>
                    <input type="text" x-model="editForm.parametro" class="mt-1 w-full border border-gray-500 dark:border-gray-600 rounded px-2 py-1 bg-gray-100 dark:bg-gray-700 dark:text-gray-300" disabled>
                    <small class="text-xs">Solo lectura. Máximo 50 caracteres.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium">Valor</label>
                    <input type="text"
                           x-model="editForm.valor"
                           @blur="editForm._touched = editForm._touched || {}; editForm._touched.valor = true"
                           @input="editForm._touched = editForm._touched || {}; editForm._touched.valor = true"
                           :class="{'border-red-500': (editForm._touched && editForm._touched.valor) && (editForm.valor === '' || editForm.valor.length >= 100)}"
                           maxlength="100"
                           class="mt-1 w-full border border-gray-500 dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800 dark:text-gray-200"
                           required>
                    <small :class="(editForm._touched && editForm._touched.valor) && (editForm.valor === '' || editForm.valor.length >= 100) ? 'text-red-500' : 'text-gray-500 dark:text-white'" class="text-xs">Requerido. Máximo 100 caracteres.</small>
                </div>
                <div class="text-red-500 text-sm" x-show="formError" x-text="formError"></div>
            </div>
        </x-admin.edit-modal>

        <x-admin.confirmation-modal class="nunito-bold" modalName="showDeleteModal" title="Confirmar Eliminación" itemToDelete="parametroToDelete" itemNameProperty="parametro" message="¿Seguro que deseas eliminar el parámetro" />
    </div>
</div>