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
    <x-admin.tabla-crud>
        <x-slot name="titulo">
            <h2 class="text-2xl text-gray-800 nunito-bold">Productos</h2>
        </x-slot>
        <x-slot name="filtros">
            <div class="flex flex-wrap gap-2 items-center">
                <input type="text" x-model="searchProducto" placeholder="Buscar por nombre..."
                    class="border rounded px-3 py-2 text-sm w-full sm:w-48" />
                <select x-model="searchCategoria" class="border rounded px-1 py-2 text-sm w-full sm:w-40">
                    <option value="">Todas las categorías</option>
                    <option>Computadoras</option>
                    <option>Accesorios</option>
                    <option>Redes</option>
                    <option>Impresoras</option>
                    <option>Software</option>
                    <option>Componentes</option>
                    <option>Licencias</option>
                </select>
                <select x-model="searchStock" class="border rounded px-1 py-2 text-sm w-full sm:w-40">
                    <option value="">Stock</option>
                    <option value="disponible">Disponible</option>
                    <option value="agotado">Agotado</option>
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
                        <th class="py-2 px-4 text-left">ID</th>
                        <th class="py-2 px-4 text-left">Nombre</th>
                        <th class="py-2 px-4 text-left">Categoría</th>
                        <th class="py-2 px-4 text-left">Precio</th>
                        <th class="py-2 px-4 text-left">Stock</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
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
                            <td class="py-2 px-4" x-text="producto.id"></td>
                            <td class="py-2 px-4" x-text="producto.nombre"></td>
                            <td class="py-2 px-4" x-text="producto.categoria"></td>
                            <td class="py-2 px-4">L.<span x-text="producto.precio.toFixed(2)"></span></td>
                            <td class="py-2 px-4" x-text="producto.stock"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click="isEditModalOpen = true; productoToEdit = {...producto}"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click="isDeleteModalOpen = true; productoToDelete = {...producto}"
                                    class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>

    <!-- Modal Nuevo Producto -->
    <x-admin.form-modal modalName="isModalOpen" title="Nuevo Producto" submitLabel="Guardar" maxWidth="max-w-md">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" class="w-full border rounded px-3 py-2" placeholder="Nombre" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Categoría</label>
            <select class="w-full border rounded px-3 py-2">
                <option>Computadoras</option>
                <option>Accesorios</option>
                <option>Redes</option>
                <option>Impresoras</option>
                <option>Software</option>
                <option>Componentes</option>
                <option>Licencias</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Precio</label>
            <input type="number" class="w-full border rounded px-3 py-2" placeholder="Precio" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Stock</label>
            <input type="number" class="w-full border rounded px-3 py-2" placeholder="Stock" />
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Producto -->
    <x-admin.edit-modal modalName="isEditModalOpen" title="Editar Producto" itemToEdit="productoToEdit"
        maxWidth="max-w-md">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" x-model="productoToEdit.nombre" class="w-full border rounded px-3 py-2" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Categoría</label>
            <select x-model="productoToEdit.categoria" class="w-full border rounded px-3 py-2">
                <option>Computadoras</option>
                <option>Accesorios</option>
                <option>Redes</option>
                <option>Impresoras</option>
                <option>Software</option>
                <option>Componentes</option>
                <option>Licencias</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Precio</label>
            <input type="number" x-model="productoToEdit.precio" class="w-full border rounded px-3 py-2" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Stock</label>
            <input type="number" x-model="productoToEdit.stock" class="w-full border rounded px-3 py-2" />
        </div>
    </x-admin.edit-modal>

    <!-- Modal Eliminar Producto -->
    <x-admin.confirmation-modal modalName="isDeleteModalOpen" itemToDelete="productoToDelete"
        message="¿Seguro que deseas eliminar este producto?" />
</div>