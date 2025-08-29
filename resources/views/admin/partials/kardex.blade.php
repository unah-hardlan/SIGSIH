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
    <x-admin.tabla-crud class="nunito-bold">
        <x-slot name="titulo">
            <h2 class="text-2xl text-gray-800 nunito-bold">Kardex (Movimientos)</h2>
        </x-slot>
        <x-slot name="filtros">
            <input type="text" x-model="searchMovimiento" placeholder="Buscar movimiento..."
                class="border rounded px-3 py-2 text-sm w-full sm:w-48 nunito-regular" />
            <select x-model="searchProducto" class="border rounded px-1 py-2 text-sm w-full sm:w-40 nunito-regular">
                <option value="" class="nunito-regular">Todos los productos</option>
                <option class="nunito-regular">Producto Ejemplo</option>
                <option class="nunito-regular">Producto 2</option>
            </select>
            <select x-model="searchTipo" class="border rounded px-1 py-2 text-sm w-full sm:w-40 nunito-regular">
                <option value="" class="nunito-regular">Todos los tipos</option>
                <option value="Entrada" class="nunito-regular">Entrada</option>
                <option value="Salida" class="nunito-regular">Salida</option>
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
                        <th class="py-2 px-4 text-left nunito-bold">ID Kardex</th>
                        <th class="py-2 px-4 text-left nunito-bold">ID Producto</th>
                        <th class="py-2 px-4 text-left nunito-bold">ID Tipo Movimiento</th>
                        <th class="py-2 px-4 text-left nunito-bold">Cantidad</th>
                        <th class="py-2 px-4 text-left nunito-bold">Fecha Movimiento</th>
                        <th class="py-2 px-4 text-left nunito-bold">Motivo</th>
                        <th class="py-2 px-4 text-left nunito-bold">ID Técnico</th>
                        <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
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
                            <td class="py-2 px-4 nunito-regular" x-text="movimiento.id_kardex_pk"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="movimiento.id_producto_fk"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="movimiento.id_tipo_movimiento_fk"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="movimiento.cantidad"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="movimiento.fecha_movimiento"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="movimiento.motivo"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="movimiento.id_tecnico_fk"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click="isEditModalOpen = true; movimientoToEdit = {...movimiento}"
                                    class="text-blue-500 hover:text-blue-700 nunito-regular"><i class="fas fa-edit"></i></a>
                                <a href="#" @click="isDeleteModalOpen = true; movimientoToDelete = {...movimiento}"
                                    class="text-red-500 hover:text-red-700 nunito-regular"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>

    <!-- Modal Nuevo Movimiento -->
    <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Nuevo Movimiento" submitLabel="Guardar" maxWidth="max-w-md">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">ID Producto</label>
            <input type="number" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="ID Producto" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Tipo Movimiento</label>
            <select class="w-full border rounded px-3 py-2 nunito-regular">
                <option value="Entrada" class="nunito-regular">Entrada</option>
                <option value="Salida" class="nunito-regular">Salida</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Cantidad</label>
            <input type="number" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Cantidad" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Fecha Movimiento</label>
            <input type="date" class="w-full border rounded px-3 py-2 nunito-regular" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Motivo</label>
            <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Motivo" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">ID Técnico</label>
            <input type="number" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="ID Técnico" />
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Movimiento -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Movimiento" itemToEdit="movimientoToEdit"
        maxWidth="max-w-md">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">ID Producto</label>
            <input type="number" x-model="movimientoToEdit.id_producto_fk" class="w-full border rounded px-3 py-2 nunito-regular" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Tipo Movimiento</label>
            <select x-model="movimientoToEdit.id_tipo_movimiento_fk" class="w-full border rounded px-3 py-2 nunito-regular">
                <option value="Entrada" class="nunito-regular">Entrada</option>
                <option value="Salida" class="nunito-regular">Salida</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Cantidad</label>
            <input type="number" x-model="movimientoToEdit.cantidad" class="w-full border rounded px-3 py-2 nunito-regular" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Fecha Movimiento</label>
            <input type="date" x-model="movimientoToEdit.fecha_movimiento" class="w-full border rounded px-3 py-2 nunito-regular" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Motivo</label>
            <input type="text" x-model="movimientoToEdit.motivo" class="w-full border rounded px-3 py-2 nunito-regular" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">ID Técnico</label>
            <input type="number" x-model="movimientoToEdit.id_tecnico_fk" class="w-full border rounded px-3 py-2 nunito-regular" />
        </div>
    </x-admin.edit-modal>

    <!-- Modal Eliminar Movimiento -->
    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteModalOpen" itemToDelete="movimientoToDelete"
        message="¿Seguro que deseas eliminar este movimiento?" />
</div>