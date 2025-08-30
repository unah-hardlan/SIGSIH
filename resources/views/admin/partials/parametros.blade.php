<div x-data="parametrosCrud" x-init="init()" class="p-4 space-y-4">
    <x-admin.tabla-crud class="nunito-bold" :titulo="'Gestión de Parámetros'">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'search',
                'filtrosSelect' => [],
                'ordenarOptions' => ['parametro' => 'Parámetro', 'valor' => 'Valor', 'creado' => 'Creación']
            ])
        </x-slot>
        <x-slot name="boton">
            <div class="flex flex-col sm:flex-row sm:items-center gap-1.5">
                <button @click="openCreate()"
                    class="duration-200 ease-in-out w-full sm:w-auto h-10 sm:h-8 inline-flex items-center justify-center gap-1.5 px-4 rounded-md bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-medium text-xs tracking-wide transition focus:outline-none focus:ring-1 focus:ring-green-500">
                    <i class="fas fa-plus text-[11px]"></i>
                    <span class="nunito-regular text-sm">Agregar parámetro</span>
                </button>
                <button @click="openReporte()"
                    class="duration-200 ease-in-out w-full sm:w-auto h-10 sm:h-8 inline-flex items-center justify-center gap-1.5 px-4 rounded-md bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-medium text-xs tracking-wide transition focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <i class="fas fa-file-alt text-[11px]"></i>
                    <span class="nunito-regular text-sm">Generar Reporte</span>
                </button>
            </div>
        </x-slot>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-2 px-4 text-left nunito-bold">Parámetro</th>
                        <th class="py-2 px-4 text-left nunito-bold">Valor</th>
                        <th class="py-2 px-4 text-left nunito-bold">Creado por</th>
                        <th class="py-2 px-4 text-left nunito-bold">Creación</th>
                        <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading"><tr><td colspan="5" class="py-4 text-center nunito-regular">Cargando...</td></tr></template>
                    <template x-if="!loading && parametros.length===0"><tr><td colspan="5" class="py-4 text-center text-gray-500 nunito-regular">Sin resultados</td></tr></template>
                    <template x-for="p in parametros" :key="p.id">
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-4 nunito-regular" x-text="p.parametro"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="p.valor"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="p.creado_por||'-'"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="p.fecha_creacion||'-'"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <button @click="openEdit(p)" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                                <button @click="openDelete(p)" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="mt-3 flex items-center justify-between" x-show="pagination.total>0">
            <div class="text-xs nunito-regular">Página <span x-text="pagination.page"></span>/<span x-text="pagination.last_page"></span> • Total <span x-text="pagination.total"></span></div>
            <div class="flex gap-2">
                <button class="px-2 py-1 border rounded nunito-regular" :disabled="pagination.page<=1" @click="changePage(pagination.page-1)">Anterior</button>
                <button class="px-2 py-1 border rounded nunito-regular" :disabled="pagination.page>=pagination.last_page" @click="changePage(pagination.page+1)">Siguiente</button>
            </div>
        </div>
        <div class="mt-2 text-red-600 text-sm nunito-regular" x-show="error" x-text="error"></div>
    </x-admin.tabla-crud>

    <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Agregar Parámetro" submitLabel="Guardar" formId="formCrearParametro">
        <div class="grid grid-cols-1 gap-4">
            <div><label class="block text-sm nunito-bold">Parámetro</label><input type="text" x-model="createForm.parametro" class="mt-1 w-full border rounded px-2 py-1 nunito-regular" required></div>
            <div><label class="block text-sm nunito-bold">Valor</label><input type="text" x-model="createForm.valor" class="mt-1 w-full border rounded px-2 py-1 nunito-regular" required></div>
            <div class="text-red-600 text-sm nunito-regular" x-show="formError" x-text="formError"></div>
        </div>
    </x-admin.form-modal>

    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Parámetro" itemToEdit="parametroToEdit" submitLabel="Actualizar" formId="formEditarParametro">
        <div class="grid grid-cols-1 gap-4">
            <div><label class="block text-sm nunito-bold">Parámetro</label><input type="text" x-model="editForm.parametro" class="mt-1 w-full border rounded px-2 py-1 bg-gray-100 nunito-regular" disabled></div>
            <div><label class="block text-sm nunito-bold">Valor</label><input type="text" x-model="editForm.valor" class="mt-1 w-full border rounded px-2 py-1 nunito-regular" required></div>
            <div class="text-red-600 text-sm nunito-regular" x-show="formError" x-text="formError"></div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-bold" modalName="showDeleteModal" title="Confirmar" itemToDelete="parametroToDelete" itemNameProperty="parametro" message="¿Seguro que deseas eliminar el parámetro" />
</div>
