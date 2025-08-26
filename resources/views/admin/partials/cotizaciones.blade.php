<div x-data="{ 
        deleteModal: false, 
        selectedItem: null, 
        generateCotizacionModal: false, 
        editModal: false, 
        itemToEdit: {
            id: null,
            clienteId: '',
            fechaCotizacion: '',
            validoHasta: '',
            imponible: '',
            totalImpuesto: '',
            otrosCargos: '',
            total: '',
            descripciones: []
        }, 
        showFilters: false 
    }">
        <x-admin.tabla-crud titulo="Cotizaciones">
            <x-slot:filtros>
                <div class="w-full">
                    <div class="flex mb-4">
                        <div class="flex-1 relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current text-gray-500">
                                    <path d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1114.32 4.906l5.387 5.387a1 1 0 01-1.414 1.414l-5.387-5.387A8 8 0 012 10z"></path>
                                </svg>
                            </span>
                            <input placeholder="Buscar por ID o cliente" 
                                class="appearance-none rounded-md border border-gray-300 block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:border-blue-500 focus:outline-none" />
                        </div>
                        <button @click="showFilters = !showFilters" class="ml-2 px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filtros
                        </button>
                    </div>
                </div>
            </x-slot:filtros>

            <x-slot:boton>
                                <button
                                    @click="generateCotizacionModal = true"
                                    class="text-sm w-full sm:w-32 h-12 rounded bg-emerald-500 text-white hover:bg-emerald-600 duration-300"
                                >
                  <i class="fas fa-plus"></i> Generar Cotización
                </button>
            </x-slot:boton>

            <div x-show="showFilters" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="bg-gray-50 p-4 rounded-md shadow-sm mb-4">
                <div class="flex flex-wrap md:flex-nowrap gap-4 mb-4">
                    <div class="w-full md:w-1/2">
                        <label class="block text-sm font-medium text-gray-700 mb-1 nunito-bold">Rango de fechas</label>
                        <div class="flex space-x-2">
                            <input type="date" placeholder="dd/mm/aaaa" class="w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1 text-sm" />
                            <input type="date" placeholder="dd/mm/aaaa" class="w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1 text-sm" />
                        </div>
                    </div>

                    <!-- Cliente -->
                    <div class="w-full md:w-1/2">
                        <label class="block text-sm font-medium text-gray-700 mb-1 nunito-bold">Cliente</label>
                        <select class="w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1 text-sm">
                            <option value="">Todos los clientes</option>
                            <option value="CLI-1234">Juan Orlando Hernandez</option>
                            <option value="CLI-5678">Rocky</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1 nunito-bold">Rango de montos</label>
                    <div class="flex flex-wrap md:flex-nowrap space-x-0 md:space-x-2 space-y-2 md:space-y-0">
                        <input type="number" placeholder="Monto mínimo" class="w-full md:w-1/2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1 text-sm" />
                        <input type="number" placeholder="Monto máximo" class="w-full md:w-1/2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1 text-sm" />
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button class="px-4 py-1 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
                        Limpiar
                    </button>
                    <button class="px-4 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm">
                        Aplicar filtros
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="nunito-bold">
                        <tr>
                            <th class="px-4 py-3 text-left bg-white">ID</th>
                            <th class="px-4 py-3 text-left bg-white">ID Cliente</th>
                            <th class="px-4 py-3 text-left bg-white">Fecha Cotización</th>
                            <th class="px-4 py-3 text-left bg-white">Válida Hasta</th>
                            <th class="px-4 py-3 text-left bg-white">Subtotal</th>
                            <th class="px-4 py-3 text-left bg-white">Total</th>
                            <th class="px-4 py-3 text-left bg-white">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="nunito-regular">
                        <tr>
                            <td class="px-4 py-3 border-t border-gray-200">1</td>
                            <td class="px-4 py-3 border-t border-gray-200">CLI-1234</td>
                            <td class="px-4 py-3 border-t border-gray-200">2025-07-28</td>
                            <td class="px-4 py-3 border-t border-gray-200">2025-08-28</td>
                            <td class="px-4 py-3 border-t border-gray-200">$10,500.00</td>
                            <td class="px-4 py-3 border-t border-gray-200">$12,180.00</td>
                            <td class="px-4 py-3 border-t border-gray-200">
                                <a href="/admin/detalle-cotizacion" target="_blank"
                                  class="inline-flex items-center justify-center text-xs w-24 h-9 rounded bg-emerald-500 text-white hover:bg-emerald-600 duration-300 mr-2"
                                >
                                  <i class="fas fa-eye mr-1"></i> Ver detalles
                                </a>
                                <a href="#" class="text-blue-500 hover:text-blue-700 mr-2" @click="editModal = true; itemToEdit = { 
                                    id: 1, 
                                    clienteId: 'CLI-1234', 
                                    fechaCotizacion: '2025-07-28', 
                                    validoHasta: '2025-08-28',
                                    imponible: 10500.00,
                                    totalImpuesto: 1580.00,
                                    otrosCargos: 100.00,
                                    total: 12180.00,
                                    descripciones: [
                                        { descripcion: 'Producto 1', precio: 5000.00, cantidad: 1, impuesto: 750.00, total: 5750.00 },
                                        { descripcion: 'Producto 2', precio: 5500.00, cantidad: 1, impuesto: 830.00, total: 6330.00 }
                                    ]
                                }">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="text-red-500 hover:text-red-700" @click="deleteModal = true; selectedItem = 1">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <x-admin.confirmation-modal modalName="deleteModal" itemToDelete="selectedItem" message="¿Está seguro que desea eliminar la cotización"/>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 border-t border-gray-200">2</td>
                            <td class="px-4 py-3 border-t border-gray-200">CLI-5678</td>
                            <td class="px-4 py-3 border-t border-gray-200">2025-07-26</td>
                            <td class="px-4 py-3 border-t border-gray-200">2025-08-26</td>
                            <td class="px-4 py-3 border-t border-gray-200">$8,750.00</td>
                            <td class="px-4 py-3 border-t border-gray-200">$10,150.00</td>
                            <td class="px-4 py-3 border-t border-gray-200">
                                <a href="/admin/detalle-cotizacion" target="_blank"
                                  class="inline-flex items-center justify-center text-xs w-24 h-9 rounded bg-emerald-500 text-white hover:bg-emerald-600 duration-300 mr-2"
                                >
                                  <i class="fas fa-eye mr-1"></i> Ver detalles
                                </a>
                                <a href="#" class="text-blue-500 hover:text-blue-700 mr-2" @click="editModal = true; itemToEdit = {
                                    id: 2,
                                    clienteId: 'CLI-5678', 
                                    fechaCotizacion: '2025-07-26', 
                                    validoHasta: '2025-08-26',
                                    imponible: 8750.00,
                                    totalImpuesto: 1312.50,
                                    otrosCargos: 87.50,
                                    total: 10150.00,
                                    descripciones: [
                                        { descripcion: 'Servicio A', precio: 4500.00, cantidad: 1, impuesto: 675.00, total: 5175.00 },
                                        { descripcion: 'Servicio B', precio: 4250.00, cantidad: 1, impuesto: 637.50, total: 4887.50 }
                                    ]
                                }">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="text-red-500 hover:text-red-700" @click="deleteModal = true; selectedItem = 2">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <x-admin.confirmation-modal modalName="deleteModal" itemToDelete="selectedItem" message="¿Está seguro que desea eliminar la cotización"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-admin.tabla-crud>

            <x-admin.form-modal modalName="generateCotizacionModal" title="Generar Cotización" submitLabel="Guardar"
                formId="generateCotizacionForm" maxWidth="max-w-4xl">
                <div class="grid grid-cols-1 gap-4"> {{-- Cambiado a grid-cols-1 para que los elementos principales tomen todo el ancho --}}

                <!-- ID del Cliente -->
                <div> {{-- Este div ahora ocupa todo el ancho --}}
                    <label for="clienteId" class="block text-sm font-medium text-gray-700 nunito-bold">ID del Cliente</label>
                    <select id="clienteId" name="clienteId"
                        class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                        <option value="">Seleccione un cliente</option>
                        <option value="">Juan Orlando Hernandez</option>
                        <option value="">Rocky</option>
                        <!-- Opciones dinámicas -->
                    </select>
                </div>

                <!-- Fecha de Cotización -->
                <div> {{-- Este div ahora ocupa todo el ancho --}}
                    <label for="fechaCotizacion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de
                        Cotización</label>
                    <input type="date" id="fechaCotizacion" name="fechaCotizacion"
                        class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                </div>

                <!-- Válido Hasta -->
                <div> {{-- Este div ahora ocupa todo el ancho --}}
                    <label for="validoHasta" class="block text-sm font-medium text-gray-700 nunito-bold">Válido Hasta</label>
                    <input type="date" id="validoHasta" name="validoHasta"
                        class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                </div>

                <!-- Descripción dinámica -->
                <div class="col-span-1" x-data="{ descriptions: [{}] }"> {{-- col-span-1 aquí es redundante pero no hace daño --}}
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <div class="max-h-48 overflow-y-auto pr-2">
                        <template x-for="(description, index) in descriptions" :key="index">
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 mt-2 items-center">
                                <div class="col-span-1 sm:col-span-3">
                                    <input type="text" :name="`descripcion[${index}][descripcion]`"
                                        placeholder="Descripción"
                                        class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <input type="number" :name="`descripcion[${index}][precio]`"
                                        placeholder="Precio Unitario"
                                        class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <input type="number" :name="`descripcion[${index}][cantidad]`" placeholder="Cantidad"
                                        class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <input type="number" :name="`descripcion[${index}][impuesto]`" placeholder="Impuesto"
                                        class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <input type="number" :name="`descripcion[${index}][total]`" placeholder="Total"
                                        class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                                </div>
                                <div class="col-span-1 sm:col-span-1 text-right">
                                    <button type="button" @click="descriptions.splice(index, 1)"
                                        class="text-red-500 hover:text-red-700" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="descriptions.push({})"
                        class="mt-2 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                        <i class="fas fa-plus"></i> Añadir Descripción
                    </button>
                </div>

                <!-- Fila para Imponible, Total Impuesto, Otros Cargos -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4"> {{-- Este es el nuevo contenedor para la fila de 3 elementos --}}
                    <!-- Imponible -->
                    <div>
                        <label for="imponible" class="block text-sm font-medium text-gray-700 nunito-bold">Imponible</label>
                        <input type="number" id="imponible" name="imponible"
                            class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                    </div>

                    <!-- Total impuesto -->
                    <div>
                        <label for="totalImpuesto" class="block text-sm font-medium text-gray-700 nunito-bold">Total
                            Impuesto</label>
                        <input type="number" id="totalImpuesto" name="totalImpuesto"
                            class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                    </div>

                    <!-- Otros cargos -->
                    <div>
                        <label for="otrosCargos" class="block text-sm font-medium text-gray-700 nunito-bold">Otros
                            Cargos</label>
                        <input type="number" id="otrosCargos" name="otrosCargos"
                            class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                    </div>
                </div>

                <!-- Total -->
                <div> {{-- Este div ahora ocupa todo el ancho --}}
                    <label for="total" class="block text-sm font-medium text-gray-700 nunito-bold">Total</label>
                    <input type="number" id="total" name="total"
                        class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1">
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal de Edición de Cotización -->
        <x-admin.edit-modal modalName="editModal" title="Editar Cotización" submitLabel="Actualizar"    itemToEdit="itemToEdit"
            maxWidth="max-w-4xl" formId="editCotizacionForm">
            <div x-show="itemToEdit" class="space-y-4"> {{-- Este div envuelve todo el contenido del slot --}}
                <div class="grid grid-cols-1 gap-4"> {{-- Contenedor principal para organizar en filas --}}

                    <!-- ID del Cliente -->
                    <div>
                        <label for="editClienteId" class="block text-sm font-medium text-gray-700 nunito-bold">ID del
                            Cliente</label>
                        <select id="editClienteId" name="clienteId"
                            class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                            x-model="itemToEdit.clienteId">
                            <option value="">Seleccione un cliente</option>
                            <option value="CLI-1234">Juan Orlando Hernandez</option>
                            <option value="CLI-5678">Rocky</option>
                            <!-- Opciones dinámicas -->
                        </select>
                    </div>

                    <!-- Fecha de Cotización -->
                    <div>
                        <label for="editFechaCotizacion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de
                            Cotización</label>
                        <input type="date" id="editFechaCotizacion" name="fechaCotizacion"
                            class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                            x-model="itemToEdit.fechaCotizacion">
                    </div>

                    <!-- Válido Hasta -->
                    <div>
                        <label for="editValidoHasta" class="block text-sm font-medium text-gray-700 nunito-bold">Válido
                            Hasta</label>
                        <input type="date" id="editValidoHasta" name="validoHasta"
                            class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                            x-model="itemToEdit.validoHasta">
                    </div>

                    <!-- Descripción dinámica -->
                    <div class="col-span-1"> {{-- Aquí la clase col-span-1 es redundante pero no hace daño --}}
                        <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                        <div class="max-h-48 overflow-y-auto pr-2">
                            <template x-for="(descripcion, index) in itemToEdit.descripciones" :key="index">
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 mt-2 items-center">
                                    <div class="col-span-1 sm:col-span-3">
                                        <input type="text" :name="`descripcion[${index}][descripcion]`"
                                            placeholder="Descripción"
                                            class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                                            x-model="descripcion.descripcion">
                                    </div>
                                    <div class="col-span-1 sm:col-span-2">
                                        <input type="number" :name="`descripcion[${index}][precio]`"
                                            placeholder="Precio Unitario"
                                            class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                                            x-model="descripcion.precio">
                                    </div>
                                    <div class="col-span-1 sm:col-span-2">
                                        <input type="number" :name="`descripcion[${index}][cantidad]`"
                                            placeholder="Cantidad"
                                            class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                                            x-model="descripcion.cantidad">
                                    </div>
                                    <div class="col-span-1 sm:col-span-2">
                                        <input type="number" :name="`descripcion[${index}][impuesto]`"
                                            placeholder="Impuesto"
                                            class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                                            x-model="descripcion.impuesto">
                                    </div>
                                    <div class="col-span-1 sm:col-span-2">
                                        <input type="number" :name="`descripcion[${index}][total]`" placeholder="Total"
                                            class="w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                                            x-model="descripcion.total">
                                    </div>
                                    <div class="col-span-1 sm:col-span-1 text-right">
                                        <button type="button" @click="itemToEdit.descripciones.splice(index, 1)"
                                            class="text-red-500 hover:text-red-700" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="itemToEdit.descripciones.push({})"
                            class="mt-2 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                            <i class="fas fa-plus"></i> Añadir Descripción
                        </button>
                    </div>

                    <!-- Fila para Imponible, Total Impuesto, Otros Cargos -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Imponible -->
                        <div>
                            <label for="editImponible"
                                class="block text-sm font-medium text-gray-700 nunito-bold">Imponible</label>
                            <input type="number" id="editImponible" name="imponible"
                                class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                                x-model="itemToEdit.imponible">
                        </div>

                        <!-- Total impuesto -->
                        <div>
                            <label for="editTotalImpuesto" class="block text-sm font-medium text-gray-700 nunito-bold">Total
                                Impuesto</label>
                            <input type="number" id="editTotalImpuesto" name="totalImpuesto"
                                class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                                x-model="itemToEdit.totalImpuesto">
                        </div>

                        <!-- Otros cargos -->
                        <div>
                            <label for="editOtrosCargos" class="block text-sm font-medium text-gray-700 nunito-bold">Otros
                                Cargos</label>
                            <input type="number" id="editOtrosCargos" name="otrosCargos"
                                class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                                x-model="itemToEdit.otrosCargos">
                        </div>
                    </div>

                    <!-- Total -->
                    <div> {{-- Este div ahora ocupa todo el ancho --}}
                        <label for="editTotal" class="block text-sm font-medium text-gray-700 nunito-bold">Total</label>
                        <input type="number" id="editTotal" name="total"
                            class="mt-1 block w-full rounded-md border border-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 nunito-regular p-1"
                            x-model="itemToEdit.total">
                    </div>
                </div>
            </div>
        </x-admin.edit-modal>
</div>

<style>
    table tbody td {
        font-size: 0.875rem;
    }
</style>
