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
    <x-admin.tabla-crud class="nunito-bold">
        <x-slot name="titulo">
            <h2 class="text-2xl text-gray-800 nunito-bold">Productos</h2>
        </x-slot>
        <x-slot name="filtros">
            <div class="flex flex-wrap gap-2 items-center">
                <input type="text" x-model="searchProducto" placeholder="Buscar por nombre..."
                    class="border rounded px-3 py-2 text-sm w-full sm:w-48 nunito-regular" />
                <select x-model="searchCategoria" class="border rounded px-1 py-2 text-sm w-full sm:w-40 nunito-regular">
                    <option value="" class="nunito-regular">Todas las categorías</option>
                    <option class="nunito-regular">Computadoras</option>
                    <option class="nunito-regular">Accesorios</option>
                    <option class="nunito-regular">Redes</option>
                    <option class="nunito-regular">Impresoras</option>
                    <option class="nunito-regular">Software</option>
                    <option class="nunito-regular">Componentes</option>
                    <option class="nunito-regular">Licencias</option>
                </select>
                <select x-model="searchStock" class="border rounded px-1 py-2 text-sm w-full sm:w-40 nunito-regular">
                    <option value="" class="nunito-regular">Stock</option>
                    <option value="disponible" class="nunito-regular">Disponible</option>
                    <option value="agotado" class="nunito-regular">Agotado</option>
                </select>
            </div>
        </x-slot>
        <x-slot name="boton">
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ url('/admin/reportes-header?modulo=Productos') }}" target="_blank"
                    class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2 justify-center">
                    <i class="fas fa-file-alt"></i> Generar Reporte
                </a>
                <button @click="isModalOpen = true"
                    class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">Nuevo
                    producto</button>
            </div>
        </x-slot>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left nunito-bold">ID</th>
                        <th class="py-2 px-4 text-left nunito-bold">Nombre</th>
                        <th class="py-2 px-4 text-left nunito-bold">Categoría</th>
                        <th class="py-2 px-4 text-left nunito-bold">Precio</th>
                        <th class="py-2 px-4 text-left nunito-bold">Stock</th>
                        <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="producto in [
                        {id: 1, nombre: 'Producto Ejemplo', categoria: 'General', precio: 250.00, stock: 50}
                    ]" :key="producto.id">
                        <tr class="border-b nunito-regular" x-show="
                                (!searchProducto || producto.nombre.toLowerCase().includes(searchProducto.toLowerCase())) &&
                                (!searchCategoria || producto.categoria === searchCategoria) &&
                                (!searchStock || 
                                    (searchStock === 'disponible' && producto.stock > 0) ||
                                    (searchStock === 'agotado' && producto.stock == 0)
                                )
                            ">
                            <td class="py-2 px-4 nunito-regular" x-text="producto.id"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="producto.nombre"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="producto.categoria"></td>
                            <td class="py-2 px-4 nunito-regular">L.<span x-text="producto.precio.toFixed(2)"></span></td>
                            <td class="py-2 px-4 nunito-regular" x-text="producto.stock"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click="isEditModalOpen = true; productoToEdit = {...producto}"
                                    class="text-blue-500 hover:text-blue-700 nunito-regular"><i class="fas fa-edit"></i></a>
                                <a href="#" @click="isDeleteModalOpen = true; productoToDelete = {...producto}"
                                    class="text-red-500 hover:text-red-700 nunito-regular"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>

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