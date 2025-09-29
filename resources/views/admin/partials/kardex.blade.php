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
            <h2 class="text-2xl dark:text-white text-gray-800 nunito-bold">Kardex (Movimientos)</h2>
        </x-slot>
        <div class="flex flex-col gap-4"> 

    <x-slot name="filtros">
        @include('partials.filtros-generales', [
            'searchModel' => 'searchMovimiento',
            'filtrosSelect' => [
                'productoKardexFiltro' => [
                    'label' => 'Producto',
                    'options' => ['Producto Ejemplo', 'Producto 2']
                ],
                'tipoKardexFiltro' => [
                    'label' => 'Tipo Movimiento',
                    'options' => ['Entrada', 'Salida']
                ]
            ],
            'ordenarOptions' => [
                'fecha_movimiento' => 'Fecha Movimiento',
                'cantidad' => 'Cantidad',
                'motivo' => 'Motivo'
            ]
        ])
    </x-slot>

    <x-slot name="boton">
        <div class="flex flex-col gap-2"> {{-- Ajuste para que los botones internos también estén en 2 filas --}}
            <a href="{{ url('/admin/reportes-header?modulo=Kardex') }}" target="_blank"
                class="w-full bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 justify-center text-sm">
                <i class="fas fa-file-alt"></i> Generar Reporte
            </a>
            <button @click="isModalOpen = true"
                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo movimiento
            </button>
        </div>
    </x-slot>

</div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">ID Kardex</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">ID Producto</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">ID Tipo Movimiento</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Cantidad</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Fecha Movimiento</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Motivo</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">ID Técnico</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="movimiento in [
                        {id_kardex_pk: 1, id_producto_fk: 101, id_tipo_movimiento_fk: 'Entrada', cantidad: 10, fecha_movimiento: '2025-07-26', motivo: 'Inventario inicial', id_tecnico_fk: 5},
                        {id_kardex_pk: 2, id_producto_fk: 102, id_tipo_movimiento_fk: 'Salida', cantidad: 3, fecha_movimiento: '2025-08-01', motivo: 'Venta', id_tecnico_fk: 4}
                    ]" :key="movimiento.id_kardex_pk">
                        <tr class="border-b nunito-regular bg-white dark:bg-gray-900" x-show="
                                (!searchMovimiento || movimiento.motivo.toLowerCase().includes(searchMovimiento.toLowerCase())) &&
                                (!searchProducto || String(movimiento.id_producto_fk) === searchProducto) &&
                                (!searchTipo || movimiento.id_tipo_movimiento_fk === searchTipo)
                            ">
                            <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="movimiento.id_kardex_pk"></td>
                            <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="movimiento.id_producto_fk"></td>
                            <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="movimiento.id_tipo_movimiento_fk"></td>
                            <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="movimiento.cantidad"></td>
                            <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="movimiento.fecha_movimiento"></td>
                            <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="movimiento.motivo"></td>
                            <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="movimiento.id_tecnico_fk"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click="isEditModalOpen = true; movimientoToEdit = {...movimiento}"
                                    class="text-blue-500 hover:text-blue-700 dark:text-blue-300 nunito-regular"><i class="fas fa-edit"></i></a>
                                <a href="#" @click="isDeleteModalOpen = true; movimientoToDelete = {...movimiento}"
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 nunito-regular"><i class="fas fa-trash"></i></a>
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