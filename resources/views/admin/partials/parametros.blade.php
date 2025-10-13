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
                        <th class="py-2 px-4 text-left border-0">Parámetro</th>
                        <th class="py-2 px-4 text-left border-0">Valor</th>
                        <th class="py-2 px-4 text-left border-0">Creado por</th>
                        <th class="py-2 px-4 text-left border-0">Creación</th>
                        <th class="py-2 px-4 text-left border-0">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading"><tr><td colspan="5" class="py-8 text-center text-gray-500 nunito-regular"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando...</td></tr></template>
                    <template x-if="!loading && parametros.length === 0"><tr><td colspan="5" class="py-8 text-center text-gray-500 nunito-regular">Sin resultados</td></tr></template>
                    <template x-for="p in parametros" :key="p.id">
                        <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                            <td class="py-2 px-4" x-text="p.parametro"></td>
                            <td class="py-2 px-4" x-text="p.valor"></td>
                            <td class="py-2 px-4" x-text="p.creado_por || '-'"></td>
                            <td class="py-2 px-4" x-text="p.fecha_creacion_formatted || p.fecha_creacion || '-' "></td>
                            <td class="py-2 px-4 flex gap-2">
                                <button @click="openEdit(p)" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                                <button @click="openDelete(p)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="pagination">
            <div class="mt-4 flex items-center justify-between" x-show="pagination.total > 0">
                <div class="text-sm text-gray-600 dark:text-gray-400">Página <span x-text="pagination.page"></span> de <span x-text="pagination.last_page"></span></div>
                <div class="flex gap-2">
                    <button class="px-3 py-1 text-sm border rounded hover:bg-gray-100 dark:hover:bg-gray-700" :disabled="pagination.page <= 1" @click="changePage(pagination.page - 1)">Anterior</button>
                    <button class="px-3 py-1 text-sm border rounded hover:bg-gray-100 dark:hover:bg-gray-700" :disabled="pagination.page >= pagination.last_page" @click="changePage(pagination.page + 1)">Siguiente</button>
                </div>
            </div>
            <div class="mt-2 text-red-500 text-sm" x-show="error" x-text="error"></div>
        </x-slot>

         <x-slot name="cards">
            <template x-if="loading"><div class="p-8 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando...</div></template>
            <template x-if="!loading && parametros.length === 0"><div class="p-8 text-center text-gray-500">Sin resultados</div></template>
            <template x-for="p in parametros" :key="p.id">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white sm:break-all" x-text="p.parametro"></h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 pt-1" x-text="p.valor"></p>
                    </div>
                    <p class="text-xs text-gray-400">Creado por: <span x-text="p.creado_por || '-'"></span> el <span x-text="p.fecha_creacion_formatted || p.fecha_creacion || '-' "></span></p>
                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <button @click="openEdit(p)" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1"><i class="fas fa-edit"></i> Editar</button>
                        <button @click="openDelete(p)" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1"><i class="fas fa-trash"></i> Eliminar</button>
                    </div>
                </div>
            </template>
        </x-slot>
    </x-responsive-table>

    <!-- Modales -->
    <div>
        <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Agregar Parámetro" submitLabel="Guardar" formId="formCrearParametro" maxWidth="max-w-md">
            <div class="space-y-4">
                <div><label class="block text-sm">Parámetro</label><input type="text" x-model="createForm.parametro" class="mt-1 w-full border rounded px-2 py-1" required></div>
                <div><label class="block text-sm">Valor</label><input type="text" x-model="createForm.valor" class="mt-1 w-full border rounded px-2 py-1" required></div>
                <div class="text-red-500 text-sm" x-show="formError" x-text="formError"></div>
            </div>
        </x-admin.form-modal>

        <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Parámetro" itemToEdit="parametroToEdit" formId="formEditarParametro" maxWidth="max-w-md">
             <div class="space-y-4">
                <div><label class="block text-sm">Parámetro</label><input type="text" x-model="editForm.parametro" class="mt-1 w-full border rounded px-2 py-1 bg-gray-100" disabled></div>
                <div><label class="block text-sm">Valor</label><input type="text" x-model="editForm.valor" class="mt-1 w-full border rounded px-2 py-1" required></div>
                <div class="text-red-500 text-sm" x-show="formError" x-text="formError"></div>
            </div>
        </x-admin.edit-modal>

        <x-admin.confirmation-modal class="nunito-bold" modalName="showDeleteModal" title="Confirmar Eliminación" itemToDelete="parametroToDelete" itemNameProperty="parametro" message="¿Seguro que deseas eliminar el parámetro" />
    </div>
</div>