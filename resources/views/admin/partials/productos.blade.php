<div x-data="{
        isModalOpen: false,
        isEditModalOpen: false,
        productoToEdit: {id: '', nombre: '', categoria: '', precio: '', stock: ''},
        isDeleteModalOpen: false,
        productoToDelete: {id: '', nombre: ''},
        searchProducto: '',
        searchCategoria: '',
        searchStock: ''
    }">
    <x-admin.tabla-mobile titulo="Productos">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'searchProducto',
                'filtrosSelect' => [
                    'categoriaProductoFiltro' => [
                        'label' => 'Categoría',
                        'options' => ['Computadoras', 'Accesorios', 'Redes', 'Impresoras', 'Software', 'Componentes', 'Licencias']
                    ],
                    'stockProductoFiltro' => [
                        'label' => 'Stock',
                        'options' => ['Disponible', 'Agotado']
                    ]
                ],
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'precio' => 'Precio',
                    'stock' => 'Stock'
                ]
            ])
        </x-slot>
        <x-slot name="boton">
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ url('/admin/reportes-header?modulo=Productos') }}" target="_blank"
                    class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 justify-center text-sm">
                    <i class="fas fa-file-alt"></i> Generar Reporte
                </a>
                <button @click="isModalOpen = true"
                    class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo
                    producto</button>
            </div>
        </x-slot>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-800 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">ID</th>
                    <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Nombre</th>
                    <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Categoría</th>
                    <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Precio</th>
                    <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Stock</th>
                    <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="producto in [
                    {id: 1, nombre: 'Producto Ejemplo', categoria: 'General', precio: 250.00, stock: 50}
                ]" :key="producto.id">
                    <tr class="border-b nunito-regular bg-white dark:bg-gray-900" x-show="
                            (!searchProducto || producto.nombre.toLowerCase().includes(searchProducto.toLowerCase())) &&
                            (!searchCategoria || producto.categoria === searchCategoria) &&
                            (!searchStock || 
                                (searchStock === 'disponible' && producto.stock > 0) ||
                                (searchStock === 'agotado' && producto.stock == 0)
                            )
                        ">
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="producto.id"></td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="producto.nombre"></td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="producto.categoria"></td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">L.<span x-text="producto.precio.toFixed(2)"></span></td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="producto.stock"></td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click="isEditModalOpen = true; productoToEdit = {...producto}"
                                class="text-blue-500 hover:text-blue-700 dark:text-blue-300 nunito-regular"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteModalOpen = true; productoToDelete = {...producto}"
                                class="text-red-500 hover:text-red-700 dark:text-red-400 nunito-regular"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <x-slot name="mobileTemplate">
            <div class="space-y-4 max-w-sm mx-auto">
                <template x-for="producto in [
                    {id: 1, nombre: 'Producto Ejemplo', categoria: 'General', precio: 250.00, stock: 50}
                ]" :key="producto.id">
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm mb-2">
                        <div class="flex flex-col gap-1">
                            <div class="text-base nunito-bold text-gray-800 dark:text-white">#<span x-text="producto.id"></span> · <span x-text="producto.nombre"></span></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Categoría: <span x-text="producto.categoria"></span></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Precio: L.<span x-text="producto.precio.toFixed(2)"></span></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Stock: <span x-text="producto.stock"></span></div>
                        </div>
                        <div class="flex items-center gap-2 mt-3 text-xs">
                            <button @click="isEditModalOpen = true; productoToEdit = {...producto}" class="text-blue-500 hover:text-blue-700 dark:text-blue-300 nunito-regular"><i class="fas fa-edit"></i></button>
                            <button @click="isDeleteModalOpen = true; productoToDelete = {...producto}" class="text-red-500 hover:text-red-700 dark:text-red-400 nunito-regular"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-admin.tabla-mobile>

    <!-- Modal Nuevo Producto -->
    <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Nuevo Producto" submitLabel="Guardar" maxWidth="max-w-md">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
            <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Nombre" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Categoría</label>
            <select class="w-full border rounded px-3 py-2 nunito-regular">
                <option class="nunito-regular">Computadoras</option>
                <option class="nunito-regular">Accesorios</option>
                <option class="nunito-regular">Redes</option>
                <option class="nunito-regular">Impresoras</option>
                <option class="nunito-regular">Software</option>
                <option class="nunito-regular">Componentes</option>
                <option class="nunito-regular">Licencias</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Precio</label>
            <input type="number" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Precio" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Stock</label>
            <input type="number" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Stock" />
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Producto -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Producto" itemToEdit="productoToEdit"
        maxWidth="max-w-md">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
            <input type="text" x-model="productoToEdit.nombre" class="w-full border rounded px-3 py-2 nunito-regular" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Categoría</label>
            <select x-model="productoToEdit.categoria" class="w-full border rounded px-3 py-2 nunito-regular">
                <option class="nunito-regular">Computadoras</option>
                <option class="nunito-regular">Accesorios</option>
                <option class="nunito-regular">Redes</option>
                <option class="nunito-regular">Impresoras</option>
                <option class="nunito-regular">Software</option>
                <option class="nunito-regular">Componentes</option>
                <option class="nunito-regular">Licencias</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Precio</label>
            <input type="number" x-model="productoToEdit.precio" class="w-full border rounded px-3 py-2 nunito-regular" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Stock</label>
            <input type="number" x-model="productoToEdit.stock" class="w-full border rounded px-3 py-2 nunito-regular" />
        </div>
    </x-admin.edit-modal>

    <!-- Modal Eliminar Producto -->
    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteModalOpen" itemToDelete="productoToDelete"
        message="¿Seguro que deseas eliminar este producto?" />
</div>