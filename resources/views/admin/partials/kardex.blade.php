<div x-data="{
        isModalOpen: false,
        isEditModalOpen: false,
        movimientoToEdit: {id_kardex_pk: '', id_producto_fk: '', id_tipo_movimiento_fk: '', cantidad: '', fecha_movimiento: '', motivo: '', id_tecnico_fk: ''},
        isDeleteModalOpen: false,
        movimientoToDelete: {id_kardex_pk: '', id_producto_fk: ''},
        searchMovimiento: '',
        searchProducto: '',
        searchTipo: ''
    }">
    <x-admin.tabla-crud>
        <x-slot name="titulo">
            <h2 class="text-2xl text-gray-800 nunito-bold">Kardex (Movimientos)</h2>
        </x-slot>
        <x-slot name="filtros">
            <input type="text" x-model="searchMovimiento" placeholder="Buscar movimiento..."
                class="border rounded px-3 py-2 text-sm w-full sm:w-48" />
            <select x-model="searchProducto" class="border rounded px-1 py-2 text-sm w-full sm:w-40">
                <option value="">Todos los productos</option>
                <option>Producto Ejemplo</option>
                <option>Producto 2</option>
            </select>
            <select x-model="searchTipo" class="border rounded px-1 py-2 text-sm w-full sm:w-40">
                <option value="">Todos los tipos</option>
                <option value="Entrada">Entrada</option>
                <option value="Salida">Salida</option>
            </select>
        </x-slot>
        <x-slot name="boton">
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ url('/admin/reportes-header?modulo=Kardex') }}" target="_blank"
                    class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2 justify-center">
                    <i class="fas fa-file-alt"></i> Generar Reporte
                </a>
                <button @click="isModalOpen = true"
                    class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">
                    Nuevo movimiento
                </button>
            </div>
        </x-slot>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left">ID Kardex</th>
                        <th class="py-2 px-4 text-left">ID Producto</th>
                        <th class="py-2 px-4 text-left">ID Tipo Movimiento</th>
                        <th class="py-2 px-4 text-left">Cantidad</th>
                        <th class="py-2 px-4 text-left">Fecha Movimiento</th>
                        <th class="py-2 px-4 text-left">Motivo</th>
                        <th class="py-2 px-4 text-left">ID Técnico</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="movimiento in [
                        {id_kardex_pk: 1, id_producto_fk: 101, id_tipo_movimiento_fk: 'Entrada', cantidad: 10, fecha_movimiento: '2025-07-26', motivo: 'Inventario inicial', id_tecnico_fk: 5},
                        {id_kardex_pk: 2, id_producto_fk: 102, id_tipo_movimiento_fk: 'Salida', cantidad: 3, fecha_movimiento: '2025-08-01', motivo: 'Venta', id_tecnico_fk: 4}
                    ]" :key="movimiento.id_kardex_pk">
                        <tr class="border-b nunito-regular" x-show="
                                (!searchMovimiento || movimiento.motivo.toLowerCase().includes(searchMovimiento.toLowerCase())) &&
                                (!searchProducto || String(movimiento.id_producto_fk) === searchProducto) &&
                                (!searchTipo || movimiento.id_tipo_movimiento_fk === searchTipo)
                            ">
                            <td class="py-2 px-4" x-text="movimiento.id_kardex_pk"></td>
                            <td class="py-2 px-4" x-text="movimiento.id_producto_fk"></td>
                            <td class="py-2 px-4" x-text="movimiento.id_tipo_movimiento_fk"></td>
                            <td class="py-2 px-4" x-text="movimiento.cantidad"></td>
                            <td class="py-2 px-4" x-text="movimiento.fecha_movimiento"></td>
                            <td class="py-2 px-4" x-text="movimiento.motivo"></td>
                            <td class="py-2 px-4" x-text="movimiento.id_tecnico_fk"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click="isEditModalOpen = true; movimientoToEdit = {id_kardex_pk: movimiento.id_kardex_pk, id_producto_fk: movimiento.id_producto_fk, id_tipo_movimiento_fk: movimiento.id_tipo_movimiento_fk, cantidad: movimiento.cantidad, fecha_movimiento: movimiento.fecha_movimiento, motivo: movimiento.motivo, id_tecnico_fk: movimiento.id_tecnico_fk}; isModalOpen = false; isDeleteModalOpen = false;"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click="isDeleteModalOpen = true; movimientoToDelete = {id_kardex_pk: movimiento.id_kardex_pk, id_producto_fk: movimiento.id_producto_fk}; isModalOpen = false; isEditModalOpen = false;"
                                    class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>

    <!-- Modal Nuevo Movimiento -->
    <template x-if="isModalOpen">
        <x-admin.form-modal modalName="isModalOpen" title="Nuevo Movimiento" submitLabel="Guardar" maxWidth="max-w-md">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-1">
                    <label class="block text-sm font-medium mb-1">ID Producto</label>
                    <input type="number" class="w-full border rounded px-3 py-2" placeholder="ID Producto" />
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium mb-1">Tipo Movimiento</label>
                    <select class="w-full border rounded px-3 py-2">
                        <option value="Entrada">Entrada</option>
                        <option value="Salida">Salida</option>
                    </select>
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium mb-1">Cantidad</label>
                    <input type="number" class="w-full border rounded px-3 py-2" placeholder="Cantidad" />
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium mb-1">Fecha Movimiento</label>
                    <input type="date" class="w-full border rounded px-3 py-2" />
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Motivo</label>
                    <input type="text" class="w-full border rounded px-3 py-2" placeholder="Motivo" />
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium mb-1">ID Técnico</label>
                    <input type="number" class="w-full border rounded px-3 py-2" placeholder="ID Técnico" />
                </div>
            </div>
        </x-admin.form-modal>
    </template>

    <!-- Modal Editar Movimiento -->
    <template x-if="isEditModalOpen">
        <x-admin.edit-modal modalName="isEditModalOpen" title="Editar Movimiento" itemToEdit="movimientoToEdit"
            maxWidth="max-w-md">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-1">
                    <label class="block text-sm font-medium mb-1">ID Producto</label>
                    <input type="number" x-model="movimientoToEdit.id_producto_fk" class="w-full border rounded px-3 py-2" />
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium mb-1">Tipo Movimiento</label>
                    <select x-model="movimientoToEdit.id_tipo_movimiento_fk" class="w-full border rounded px-3 py-2">
                        <option value="Entrada">Entrada</option>
                        <option value="Salida">Salida</option>
                    </select>
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium mb-1">Cantidad</label>
                    <input type="number" x-model="movimientoToEdit.cantidad" class="w-full border rounded px-3 py-2" />
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium mb-1">Fecha Movimiento</label>
                    <input type="date" x-model="movimientoToEdit.fecha_movimiento" class="w-full border rounded px-3 py-2" />
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Motivo</label>
                    <input type="text" x-model="movimientoToEdit.motivo" class="w-full border rounded px-3 py-2" />
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium mb-1">ID Técnico</label>
                    <input type="number" x-model="movimientoToEdit.id_tecnico_fk" class="w-full border rounded px-3 py-2" />
                </div>
            </div>
        </x-admin.edit-modal>
    </template>

    <!-- Modal Eliminar Movimiento -->
    <template x-if="isDeleteModalOpen">
        <x-admin.confirmation-modal modalName="isDeleteModalOpen" itemToDelete="movimientoToDelete"
            message="¿Seguro que deseas eliminar este movimiento?" />
    </template>
</div>