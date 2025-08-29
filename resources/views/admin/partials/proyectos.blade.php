{{-- Pestañas --}}
<div x-data="{ 
    tab: 'proyectos', 
    minIngresoMonto: '', 
    maxIngresoMonto: '', 
    minGastoMonto: '', 
    maxGastoMonto: '', 
    isModalOpen: false, 
    isCategoriaModalOpen: false, 
    isEditCategoriaModalOpen: false, 
    categoriaToEdit: { id: '', tipo: '', nombre: '' }, 
    isIngresoModalOpen: false, 
    isGastoModalOpen: false, 
    isEditIngresoModalOpen: false, 
    isEditGastoModalOpen: false, 
    ingresoToEdit: { id: '', proyecto: '', nombre: '', fecha: '', monto: '', categoria: '', descripcion: '' }, 
    gastoToEdit: { id: '', proyecto: '', nombre: '', fecha: '', monto: '', categoria: '', descripcion: '' }, 
    isDeleteProjectModalOpen: false, 
    projectToDelete: null, 
    isDeleteCategoriaModalOpen: false, 
    categoriaToDelete: null, 
    isDeleteIngresoModalOpen: false, 
    ingresoToDelete: null, 
    isDeleteGastoModalOpen: false, 
    gastoToDelete: null, 
    isEditProjectModalOpen: false, 
    projectToEdit: { id: '', nombre: '', fecha_inicio: '', fecha_estimada_fin: '', fecha_fin: '', descripcion: '', actividades: '', orden_servicio: '', estado: '' }, 
    isEstadoModalOpen: false, 
    isEditEstadoModalOpen: false, 
    isDeleteEstadoModalOpen: false, 
    estadoToEdit: { id: '', nombre: '', descripcion: '' }, 
    estadoToDelete: null 
    }" @include('partials.persist-tab', ['tabKey' => 'admin-proyectos-tab'])>
    <ul class="flex border-b nunito-bold">
        <li @click="tab='proyectos'" :class="tab==='proyectos' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 hover:text-blue-500 cursor-pointer'" class="mr-6 pb-2 nunito-bold">Proyectos</li>
        <li @click="tab='movimientos'" :class="tab==='movimientos' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 hover:text-blue-500 cursor-pointer'" class="mr-6 pb-2 nunito-bold">Movimientos</li>
    </ul>

    <div x-show="tab==='proyectos'" class="overflow-x-auto">
        <x-admin.tabla-mobile class="nunito-bold" titulo="Proyectos">
            <x-slot name="filtros">
                <input type="text" placeholder="Buscar proyecto..." class="border rounded px-3 py-2 text-sm w-full sm:w-48 nunito-regular" />
                <select class="border rounded px-1 py-2 text-sm w-full sm:w-40 nunito-regular">
                    <option class="nunito-regular" value="">Todos los estados</option>
                    <option class="nunito-regular">En Proceso</option>
                    <option class="nunito-regular">Finalizado</option>
                    <option class="nunito-regular">Pendiente</option>
                    <option class="nunito-regular">Cancelado</option>
                </select>
            </x-slot>
            <x-slot name="boton">
                <div class="flex flex-col sm:flex-row gap-2">
                    <a href="{{ url('/admin/reportes-header?modulo=Proyectos') }}" target="_blank"
                        class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                    <button @click="isModalOpen = true" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">Nuevo proyecto</button>
                </div>
            </x-slot>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left nunito-bold">ID</th>
                        <th class="py-2 px-4 text-left nunito-bold">Nombre</th>
                        <th class="py-2 px-4 text-left nunito-bold">Fecha Inicial</th>
                        <th class="py-2 px-4 text-left nunito-bold">Fecha Fin Estimada</th>
                        <th class="py-2 px-4 text-left nunito-bold">Fecha Fin Real</th>
                        <th class="py-2 px-4 text-left nunito-bold">Descripción</th>
                        <th class="py-2 px-4 text-left nunito-bold">Actividades</th>
                        <th class="py-2 px-4 text-left nunito-bold">Orden de Servicio</th>
                        <th class="py-2 px-4 text-left nunito-bold">Estado</th>
                        <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4 nunito-regular">1</td>
                        <td class="py-2 px-4 nunito-regular">Proyecto Alpha</td>
                        <td class="py-2 px-4 nunito-regular">2025-01-15</td>
                        <td class="py-2 px-4 nunito-regular">2025-07-30</td>
                        <td class="py-2 px-4 nunito-regular">2025-07-29</td>
                        <td class="py-2 px-4 nunito-regular">Implementación inicial del sistema</td>
                        <td class="py-2 px-4 nunito-regular">5 tareas</td>
                        <td class="py-2 px-4 nunito-regular">OS-00123</td>
                        <td class="py-2 px-4">
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded nunito-regular">Finalizado</span>
                        </td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click="isEditProjectModalOpen = true; projectToEdit = {id: 1, nombre: 'Proyecto Alpha', fecha_inicio: '2025-01-15', fecha_estimada_fin: '2025-07-30', fecha_fin: '2025-07-29', descripcion: 'Implementación inicial del sistema', actividades: '5 tareas', orden_servicio: 'OS-00123', estado: 'Finalizado'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteProjectModalOpen = true; projectToDelete = {id: 1, nombre: 'Proyecto Alpha'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4 nunito-regular">2</td>
                        <td class="py-2 px-4 nunito-regular">Proyecto Beta</td>
                        <td class="py-2 px-4 nunito-regular">2025-02-01</td>
                        <td class="py-2 px-4 nunito-regular">2025-08-20</td>
                        <td class="py-2 px-4 nunito-regular">-</td>
                        <td class="py-2 px-4 nunito-regular">Planificación y diseño preliminar</td>
                        <td class="py-2 px-4 nunito-regular">3 tareas</td>
                        <td class="py-2 px-4 nunito-regular">OS-00124</td>
                        <td class="py-2 px-4">
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded nunito-regular">En Proceso</span>
                        </td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click="isEditProjectModalOpen = true; projectToEdit = {id: 2, nombre: 'Proyecto Beta', fecha_inicio: '2025-02-01', fecha_estimada_fin: '2025-08-20', fecha_fin: '', descripcion: 'Planificación y diseño preliminar', actividades: '3 tareas', orden_servicio: 'OS-00124', estado: 'En Proceso'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteProjectModalOpen = true; projectToDelete = {id: 2, nombre: 'Proyecto Beta'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <x-slot name="mobileTemplate">
                <div class="space-y-4">
                    <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 nunito-bold">Proyecto Alpha</h3>
                                <p class="text-sm text-gray-500 nunito-regular">OS-00123</p>
                            </div>
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs nunito-regular">Finalizado</span>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600 nunito-bold">Fecha Inicial:</span> <span class="nunito-regular">2025-01-15</span></div>
                            <div><span class="font-medium text-gray-600 nunito-bold">Fecha Fin Estimada:</span> <span class="nunito-regular">2025-07-30</span></div>
                            <div><span class="font-medium text-gray-600 nunito-bold">Fecha Fin Real:</span> <span class="nunito-regular">2025-07-29</span></div>
                            <div><span class="font-medium text-gray-600 nunito-bold">Actividades:</span> <span class="nunito-regular">5 tareas</span></div>
                            <div><span class="font-medium text-gray-600 nunito-bold">Descripción:</span> <span class="nunito-regular">Implementación inicial del sistema</span></div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200">
                            <button @click="isEditProjectModalOpen = true; projectToEdit = {id: 1, nombre: 'Proyecto Alpha', fecha_inicio: '2025-01-15', fecha_estimada_fin: '2025-07-30', fecha_fin: '2025-07-29', descripcion: 'Implementación inicial del sistema', actividades: '5 tareas', orden_servicio: 'OS-00123', estado: 'Finalizado'}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click="isDeleteProjectModalOpen = true; projectToDelete = {id: 1, nombre: 'Proyecto Alpha'}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 nunito-bold">Proyecto Beta</h3>
                                <p class="text-sm text-gray-500 nunito-regular">OS-00124</p>
                            </div>
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs nunito-regular">En Proceso</span>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600 nunito-bold">Fecha Inicial:</span> <span class="nunito-regular">2025-02-01</span></div>
                            <div><span class="font-medium text-gray-600 nunito-bold">Fecha Fin Estimada:</span> <span class="nunito-regular">2025-08-20</span></div>
                            <div><span class="font-medium text-gray-600 nunito-bold">Fecha Fin Real:</span> <span class="nunito-regular">-</span></div>
                            <div><span class="font-medium text-gray-600 nunito-bold">Actividades:</span> <span class="nunito-regular">3 tareas</span></div>
                            <div><span class="font-medium text-gray-600 nunito-bold">Descripción:</span> <span class="nunito-regular">Planificación y diseño preliminar</span></div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200">
                            <button @click="isEditProjectModalOpen = true; projectToEdit = {id: 2, nombre: 'Proyecto Beta', fecha_inicio: '2025-02-01', fecha_estimada_fin: '2025-08-20', fecha_fin: '', descripcion: 'Planificación y diseño preliminar', actividades: '3 tareas', orden_servicio: 'OS-00124', estado: 'En Proceso'}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click="isDeleteProjectModalOpen = true; projectToDelete = {id: 2, nombre: 'Proyecto Beta'}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-admin.tabla-mobile>
    </div>
    <div x-show="tab==='movimientos'" class="space-y-6">
        <x-admin.tabla-crud class="nunito-bold">
            <x-slot name="titulo">
                <h3 class="text-2xl text-gray-800 mb-4 border-b pb-4 nunito-bold">Ingresos</h3>
            </x-slot>
            <x-slot name="filtros">
                <div class="flex flex-wrap gap-2">
                    <input type="text" placeholder="Buscar ingreso..." class="border rounded px-3 py-2 text-sm w-full sm:w-40 nunito-regular" />
                    <select class="border rounded px-1 py-2 text-sm w-full sm:w-40 nunito-regular">
                        <option class="nunito-regular" value="">Todos los proyectos</option>
                        <option class="nunito-regular">Proyecto Alpha</option>
                        <option class="nunito-regular">Proyecto Beta</option>
                        <option class="nunito-regular">Proyecto BAC</option>
                    </select>
                    <div class="flex items-center gap-2">
                        <input type="number" x-model="minIngresoMonto" placeholder="Monto mín." class="border rounded px-3 py-2 text-sm w-28 nunito-regular" />
                        <span class="text-gray-500">-</span>
                        <input type="number" x-model="maxIngresoMonto" placeholder="Monto máx." class="border rounded px-3 py-2 text-sm w-28 nunito-regular" />
                    </div>
                </div>
            </x-slot>
            <x-slot name="boton">
                <button @click="isIngresoModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">Agregar Ingreso</button>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left nunito-bold">ID</th>
                            <th class="py-2 px-4 text-left nunito-bold">Proyecto</th>
                            <th class="py-2 px-4 text-left nunito-bold">Nombre</th>
                            <th class="py-2 px-4 text-left nunito-bold">Fecha</th>
                            <th class="py-2 px-4 text-left nunito-bold">Monto</th>
                            <th class="py-2 px-4 text-left nunito-bold">Categoría</th>
                            <th class="py-2 px-4 text-left nunito-bold">Descripción</th>
                            <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4 nunito-regular">1</td>
                            <td class="py-2 px-4 nunito-regular">Proyecto BAC</td>
                            <td class="py-2 px-4 nunito-regular">Pago inicial</td>
                            <td class="py-2 px-4 nunito-regular">2025-07-20</td>
                            <td class="py-2 px-4 nunito-regular">L. 15,000.00</td>
                            <td class="py-2 px-4 nunito-regular">Salarios</td>
                            <td class="py-2 px-4 nunito-regular">Primer pago del Proyecto BAC</td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click="isEditIngresoModalOpen = true; ingresoToEdit = {id: 1, proyecto: 'Proyecto BAC', nombre: 'Pago inicial', fecha: '2025-07-20', monto: '15000.00', categoria: 'Salarios', descripcion: 'Primer pago del Proyecto BAC'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click="isDeleteIngresoModalOpen = true; ingresoToDelete = {id: 1, nombre: 'Pago inicial'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-admin.tabla-crud>

        <x-admin.tabla-crud class="nunito-bold">
            <x-slot name="titulo">
                <h3 class="text-2xl text-gray-800 mb-4 border-b pb-4 nunito-bold">Gastos</h3>
            </x-slot>
            <x-slot name="filtros">
                <div class="flex flex-wrap gap-2">
                    <input type="text" placeholder="Buscar gasto..." class="border rounded px-3 py-2 text-sm w-full sm:w-40 nunito-regular" />
                    <select class="border rounded px-1 py-2 text-sm w-full sm:w-40 nunito-regular">
                        <option class="nunito-regular" value="">Todos los proyectos</option>
                        <option class="nunito-regular">Proyecto Alpha</option>
                        <option class="nunito-regular">Proyecto Beta</option>
                        <option class="nunito-regular">Proyecto BAC</option>
                    </select>
                    <div class="flex items-center gap-2">
                        <input type="number" x-model="minGastoMonto" placeholder="Monto mín." class="border rounded px-3 py-2 text-sm w-28 nunito-regular" />
                        <span class="text-gray-500">-</span>
                        <input type="number" x-model="maxGastoMonto" placeholder="Monto máx." class="border rounded px-3 py-2 text-sm w-28 nunito-regular" />
                    </div>
                </div>
            </x-slot>
            <x-slot name="boton">
                <button @click="isGastoModalOpen = true" class="bg-red-800 hover:bg-red-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">Agregar Gasto</button>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left nunito-bold">ID</th>
                            <th class="py-2 px-4 text-left nunito-bold">Proyecto</th>
                            <th class="py-2 px-4 text-left nunito-bold">Nombre</th>
                            <th class="py-2 px-4 text-left nunito-bold">Fecha</th>
                            <th class="py-2 px-4 text-left nunito-bold">Monto</th>
                            <th class="py-2 px-4 text-left nunito-bold">Categoría</th>
                            <th class="py-2 px-4 text-left nunito-bold">Descripción</th>
                            <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4 nunito-regular">1</td>
                            <td class="py-2 px-4 nunito-regular">Proyecto BAC</td>
                            <td class="py-2 px-4 nunito-regular">Compra de software</td>
                            <td class="py-2 px-4 nunito-regular">2025-07-22</td>
                            <td class="py-2 px-4 nunito-regular">L. 5,500.00</td>
                            <td class="py-2 px-4 nunito-regular">Licencias</td>
                            <td class="py-2 px-4 nunito-regular">Licencias de software de desarrollo</td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click="isEditGastoModalOpen = true; gastoToEdit = {id: 1, proyecto: 'Proyecto BAC', nombre: 'Compra de software', fecha: '2025-07-22', monto: '5500.00', categoria: 'Licencias', descripcion: 'Licencias de software de desarrollo'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click="isDeleteGastoModalOpen = true; gastoToDelete = {id: 1, nombre: 'Compra de software'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-admin.tabla-crud>
    </div>

    <!-- Modal Nuevo Proyecto -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isModalOpen"
        title="Nuevo Proyecto"
        submitLabel="Guardar Proyecto"
        maxWidth="max-w-4xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                <input type="text" id="nombre" name="nombre"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Inicial del Proyecto</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_estimada_fin" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Estimada de Finalización</label>
                <input type="date" id="fecha_estimada_fin" name="fecha_estimada_fin"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de Finalización</label>
                <input type="date" id="fecha_fin" name="fecha_fin"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="descripcion" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
            <div>
                <label for="actividades" class="block text-sm font-medium text-gray-700 nunito-bold">Actividades</label>
                <input type="text" id="actividades" name="actividades"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="orden_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Orden de Servicio</label>
                <input type="text" id="orden_servicio" name="orden_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="estado_proyecto" class="block text-sm font-medium text-gray-700 nunito-bold">Estado de Proyecto</label>
                <select id="estado_proyecto" name="estado_proyecto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option class="nunito-regular">En Proceso</option>
                    <option class="nunito-regular">Finalizado</option>
                    <option class="nunito-regular">Pendiente</option>
                    <option class="nunito-regular">Cancelado</option>
                </select>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Ingreso -->
    <x-admin.edit-modal class="nunito-bold"
        modalName="isEditIngresoModalOpen"
        title="Editar Ingreso"
        itemToEdit="ingresoToEdit"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_ingreso_proyecto" class="block text-sm font-medium text-gray-700 nunito-bold">Proyecto</label>
                <select id="edit_ingreso_proyecto" name="edit_ingreso_proyecto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option :selected="ingresoToEdit.proyecto === 'Proyecto Alpha'" class="nunito-regular">Proyecto Alpha</option>
                    <option :selected="ingresoToEdit.proyecto === 'Proyecto Beta'" class="nunito-regular">Proyecto Beta</option>
                    <option :selected="ingresoToEdit.proyecto === 'Proyecto BAC'" class="nunito-regular">Proyecto BAC</option>
                </select>
            </div>
            <div>
                <label for="edit_ingreso_nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Ingreso</label>
                <input type="text" id="edit_ingreso_nombre" name="edit_ingreso_nombre" :value="ingresoToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_ingreso_fecha" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                <input type="date" id="edit_ingreso_fecha" name="edit_ingreso_fecha" :value="ingresoToEdit.fecha" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_ingreso_monto" class="block text-sm font-medium text-gray-700 nunito-bold">Monto</label>
                <input type="number" id="edit_ingreso_monto" name="edit_ingreso_monto" :value="ingresoToEdit.monto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="edit_ingreso_categoria" class="block text-sm font-medium text-gray-700 nunito-bold">Categoría</label>
                <select id="edit_ingreso_categoria" name="edit_ingreso_categoria" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option :selected="ingresoToEdit.categoria === 'Salarios'" class="nunito-regular">Salarios</option>
                </select>
            </div>
            <div class="col-span-2">
                <label for="edit_ingreso_descripcion" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="edit_ingreso_descripcion" name="edit_ingreso_descripcion" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2" x-text="ingresoToEdit.descripcion"></textarea>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Editar Gasto -->
    <x-admin.edit-modal class="nunito-bold"
        modalName="isEditGastoModalOpen"
        title="Editar Gasto"
        itemToEdit="gastoToEdit"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_gasto_proyecto" class="block text-sm font-medium text-gray-700 nunito-bold">Proyecto</label>
                <select id="edit_gasto_proyecto" name="edit_gasto_proyecto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option :selected="gastoToEdit.proyecto === 'Proyecto Alpha'" class="nunito-regular">Proyecto Alpha</option>
                    <option :selected="gastoToEdit.proyecto === 'Proyecto Beta'" class="nunito-regular">Proyecto Beta</option>
                    <option :selected="gastoToEdit.proyecto === 'Proyecto BAC'" class="nunito-regular">Proyecto BAC</option>
                </select>
            </div>
            <div>
                <label for="edit_gasto_nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Gasto</label>
                <input type="text" id="edit_gasto_nombre" name="edit_gasto_nombre" :value="gastoToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_gasto_fecha" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                <input type="date" id="edit_gasto_fecha" name="edit_gasto_fecha" :value="gastoToEdit.fecha" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_gasto_monto" class="block text-sm font-medium text-gray-700 nunito-bold">Monto</label>
                <input type="number" id="edit_gasto_monto" name="edit_gasto_monto" :value="gastoToEdit.monto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="edit_gasto_categoria" class="block text-sm font-medium text-gray-700 nunito-bold">Categoría</label>
                <select id="edit_gasto_categoria" name="edit_gasto_categoria" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option :selected="gastoToEdit.categoria === 'Alquiler'" class="nunito-regular">Alquiler</option>
                    <option :selected="gastoToEdit.categoria === 'Licencias'" class="nunito-regular">Licencias</option>
                </select>
            </div>
            <div class="col-span-2">
                <label for="edit_gasto_descripcion" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="edit_gasto_descripcion" name="edit_gasto_descripcion" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2" x-text="gastoToEdit.descripcion"></textarea>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Nuevo Ingreso -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isIngresoModalOpen"
        title="Nuevo Ingreso"
        submitLabel="Guardar Ingreso"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="ingreso_proyecto" class="block text-sm font-medium text-gray-700 nunito-bold">Proyecto</label>
                <select id="ingreso_proyecto" name="ingreso_proyecto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option class="nunito-regular">Proyecto Alpha</option>
                    <option class="nunito-regular">Proyecto Beta</option>
                    <option class="nunito-regular">Proyecto BAC</option>
                </select>
            </div>
            <div>
                <label for="ingreso_nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Ingreso</label>
                <input type="text" id="ingreso_nombre" name="ingreso_nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="ingreso_fecha" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                <input type="date" id="ingreso_fecha" name="ingreso_fecha" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="ingreso_monto" class="block text-sm font-medium text-gray-700 nunito-bold">Monto</label>
                <input type="number" id="ingreso_monto" name="ingreso_monto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="ingreso_categoria" class="block text-sm font-medium text-gray-700 nunito-bold">Categoría</label>
                <select id="ingreso_categoria" name="ingreso_categoria" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option class="nunito-regular">Salarios</option>
                </select>
            </div>
            <div class="col-span-2">
                <label for="ingreso_descripcion" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="ingreso_descripcion" name="ingreso_descripcion" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Nuevo Gasto -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isGastoModalOpen"
        title="Nuevo Gasto"
        submitLabel="Guardar Gasto"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="gasto_proyecto" class="block text-sm font-medium text-gray-700 nunito-bold">Proyecto</label>
                <select id="gasto_proyecto" name="gasto_proyecto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option class="nunito-regular">Proyecto Alpha</option>
                    <option class="nunito-regular">Proyecto Beta</option>
                    <option class="nunito-regular">Proyecto BAC</option>
                </select>
            </div>
            <div>
                <label for="gasto_nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Gasto</label>
                <input type="text" id="gasto_nombre" name="gasto_nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="gasto_fecha" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                <input type="date" id="gasto_fecha" name="gasto_fecha" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="gasto_monto" class="block text-sm font-medium text-gray-700 nunito-bold">Monto</label>
                <input type="number" id="gasto_monto" name="gasto_monto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="gasto_categoria" class="block text-sm font-medium text-gray-700 nunito-bold">Categoría</label>
                <select id="gasto_categoria" name="gasto_categoria" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option class="nunito-regular">Alquiler</option>
                    <option class="nunito-regular">Licencias</option>
                </select>
            </div>
            <div class="col-span-2">
                <label for="gasto_descripcion" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="gasto_descripcion" name="gasto_descripcion" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Confirmar Eliminación Proyecto -->
    <x-admin.confirmation-modal class="nunito-bold"
        modalName="isDeleteProjectModalOpen"
        itemToDelete="projectToDelete"
        message="¿Estás seguro de que quieres eliminar el proyecto" />

    <!-- Modal Confirmar Eliminación Ingreso -->
    <x-admin.confirmation-modal class="nunito-bold"
        modalName="isDeleteIngresoModalOpen"
        itemToDelete="ingresoToDelete"
        message="¿Estás seguro de que quieres eliminar el ingreso" />

    <!-- Modal Confirmar Eliminación Gasto -->
    <x-admin.confirmation-modal class="nunito-bold"
        modalName="isDeleteGastoModalOpen"
        itemToDelete="gastoToDelete"
        message="¿Estás seguro de que quieres eliminar el gasto" />

    <!-- Modal Editar Proyecto -->
    <x-admin.edit-modal class="nunito-bold"
        modalName="isEditProjectModalOpen"
        title="Editar Proyecto"
        itemToEdit="projectToEdit"
        maxWidth="max-w-4xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                <input type="text" id="edit_nombre" name="edit_nombre" :value="projectToEdit.nombre"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_inicio" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Inicial del Proyecto</label>
                <input type="date" id="edit_fecha_inicio" name="edit_fecha_inicio" :value="projectToEdit.fecha_inicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_estimada_fin" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Estimada de Finalización</label>
                <input type="date" id="edit_fecha_estimada_fin" name="edit_fecha_estimada_fin" :value="projectToEdit.fecha_estimada_fin"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_fin" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de Finalización</label>
                <input type="date" id="edit_fecha_fin" name="edit_fecha_fin" :value="projectToEdit.fecha_fin"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="edit_descripcion" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="edit_descripcion" name="edit_descripcion" rows="3" x-text="projectToEdit.descripcion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
            <div>
                <label for="edit_actividades" class="block text-sm font-medium text-gray-700 nunito-bold">Actividades</label>
                <input type="text" id="edit_actividades" name="edit_actividades" :value="projectToEdit.actividades"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_orden_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Orden de Servicio</label>
                <input type="text" id="edit_orden_servicio" name="edit_orden_servicio" :value="projectToEdit.orden_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_estado_proyecto" class="block text-sm font-medium text-gray-700 nunito-bold">Estado de Proyecto</label>
                <select id="edit_estado_proyecto" name="edit_estado_proyecto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option :selected="projectToEdit.estado === 'En Proceso'" class="nunito-regular">En Proceso</option>
                    <option :selected="projectToEdit.estado === 'Finalizado'" class="nunito-regular">Finalizado</option>
                    <option :selected="projectToEdit.estado === 'Pendiente'" class="nunito-regular">Pendiente</option>
                    <option :selected="projectToEdit.estado === 'Cancelado'" class="nunito-regular">Cancelado</option>
                </select>
            </div>
        </div>
    </x-admin.edit-modal>
</div>