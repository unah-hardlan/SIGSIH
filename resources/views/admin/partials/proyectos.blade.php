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
        <li @click="tab='proyectos'" :class="tab==='proyectos' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 hover:text-blue-500 cursor-pointer'" class="mr-6 pb-2">Proyectos</li>
        <li @click="tab='movimientos'" :class="tab==='movimientos' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 hover:text-blue-500 cursor-pointer'" class="mr-6 pb-2">Movimientos</li>
    </ul>

    <div x-show="tab==='proyectos'" class="overflow-x-auto">
        <x-admin.tabla-mobile titulo="Proyectos">
            <x-slot name="filtros">
                <input type="text" placeholder="Buscar proyecto..." class="border rounded px-3 py-2 text-sm w-full sm:w-48" />
                <select class="border rounded px-1 py-2 text-sm w-full sm:w-40">
                    <option class="nunito-bold" value="">Todos los estados</option>
                    <option class="nunito-bold">En Proceso</option>
                    <option class="nunito-bold">Finalizado</option>
                    <option class="nunito-bold">Pendiente</option>
                    <option class="nunito-bold">Cancelado</option>
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
                        <th class="py-2 px-4 text-left">ID</th>
                        <th class="py-2 px-4 text-left">Nombre</th>
                        <th class="py-2 px-4 text-left">Fecha Inicial</th>
                        <th class="py-2 px-4 text-left">Fecha Fin Estimada</th>
                        <th class="py-2 px-4 text-left">Fecha Fin Real</th>
                        <th class="py-2 px-4 text-left">Descripción</th>
                        <th class="py-2 px-4 text-left">Actividades</th>
                        <th class="py-2 px-4 text-left">Orden de Servicio</th>
                        <th class="py-2 px-4 text-left">Estado</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4">1</td>
                        <td class="py-2 px-4">Proyecto Alpha</td>
                        <td class="py-2 px-4">2025-01-15</td>
                        <td class="py-2 px-4">2025-07-30</td>
                        <td class="py-2 px-4">2025-07-29</td>
                        <td class="py-2 px-4">Implementación inicial del sistema</td>
                        <td class="py-2 px-4">5 tareas</td>
                        <td class="py-2 px-4">OS-00123</td>
                        <td class="py-2 px-4">
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">Finalizado</span>
                        </td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click="isEditProjectModalOpen = true; projectToEdit = {id: 1, nombre: 'Proyecto Alpha', fecha_inicio: '2025-01-15', fecha_estimada_fin: '2025-07-30', fecha_fin: '2025-07-29', descripcion: 'Implementación inicial del sistema', actividades: '5 tareas', orden_servicio: 'OS-00123', estado: 'Finalizado'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteProjectModalOpen = true; projectToDelete = {id: 1, nombre: 'Proyecto Alpha'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4">2</td>
                        <td class="py-2 px-4">Proyecto Beta</td>
                        <td class="py-2 px-4">2025-02-01</td>
                        <td class="py-2 px-4">2025-08-20</td>
                        <td class="py-2 px-4">-</td>
                        <td class="py-2 px-4">Planificación y diseño preliminar</td>
                        <td class="py-2 px-4">3 tareas</td>
                        <td class="py-2 px-4">OS-00124</td>
                        <td class="py-2 px-4">
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded">En Proceso</span>
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
                                <h3 class="font-semibold text-gray-900">Proyecto Alpha</h3>
                                <p class="text-sm text-gray-500">OS-00123</p>
                            </div>
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">Finalizado</span>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600">Fecha Inicial:</span> 2025-01-15</div>
                            <div><span class="font-medium text-gray-600">Fecha Fin Estimada:</span> 2025-07-30</div>
                            <div><span class="font-medium text-gray-600">Fecha Fin Real:</span> 2025-07-29</div>
                            <div><span class="font-medium text-gray-600">Actividades:</span> 5 tareas</div>
                            <div><span class="font-medium text-gray-600">Descripción:</span> Implementación inicial del sistema</div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200">
                            <button class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900">Proyecto Beta</h3>
                                <p class="text-sm text-gray-500">OS-00124</p>
                            </div>
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">En Proceso</span>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600">Fecha Inicial:</span> 2025-02-01</div>
                            <div><span class="font-medium text-gray-600">Fecha Fin Estimada:</span> 2025-08-20</div>
                            <div><span class="font-medium text-gray-600">Fecha Fin Real:</span> -</div>
                            <div><span class="font-medium text-gray-600">Actividades:</span> 3 tareas</div>
                            <div><span class="font-medium text-gray-600">Descripción:</span> Planificación y diseño preliminar</div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200">
                            <button class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-admin.tabla-mobile>
    </div>
    <div x-show="tab==='movimientos'" class="space-y-6">
        <x-admin.tabla-crud>
            <x-slot name="titulo">
                <h3 class="text-2xl text-gray-800 mb-4 border-b pb-4 nunito-bold">Ingresos</h3>
            </x-slot>
            <x-slot name="filtros">
                <div class="flex flex-wrap gap-2">
                    <input type="text" placeholder="Buscar ingreso..." class="border rounded px-3 py-2 text-sm w-full sm:w-40" />
                    <select class="border rounded px-1 py-2 text-sm w-full sm:w-40">
                        <option class="nunito-bold" value="">Todos los proyectos</option>
                        <option class="nunito-bold">Proyecto Alpha</option>
                        <option class="nunito-bold">Proyecto Beta</option>
                        <option class="nunito-bold">Proyecto BAC</option>
                    </select>
                    <div class="flex items-center gap-2">
                        <input type="number" x-model="minIngresoMonto" placeholder="Monto mín." class="border rounded px-3 py-2 text-sm w-28" />
                        <span class="text-gray-500">-</span>
                        <input type="number" x-model="maxIngresoMonto" placeholder="Monto máx." class="border rounded px-3 py-2 text-sm w-28" />
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
                            <th class="py-2 px-4 text-left">ID</th>
                            <th class="py-2 px-4 text-left">Proyecto</th>
                            <th class="py-2 px-4 text-left">Nombre</th>
                            <th class="py-2 px-4 text-left">Fecha</th>
                            <th class="py-2 px-4 text-left">Monto</th>
                            <th class="py-2 px-4 text-left">Categoría</th>
                            <th class="py-2 px-4 text-left">Descripción</th>
                            <th class="py-2 px-4 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4">1</td>
                            <td class="py-2 px-4">Proyecto BAC</td>
                            <td class="py-2 px-4">Pago inicial</td>
                            <td class="py-2 px-4">2025-07-20</td>
                            <td class="py-2 px-4">L. 15,000.00</td>
                            <td class="py-2 px-4">Salarios</td>
                            <td class="py-2 px-4">Primer pago del Proyecto BAC</td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click="isEditIngresoModalOpen = true; ingresoToEdit = {id: 1, proyecto: 'Proyecto BAC', nombre: 'Pago inicial', fecha: '2025-07-20', monto: '15000.00', categoria: 'Salarios', descripcion: 'Primer pago del Proyecto BAC'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click="isDeleteIngresoModalOpen = true; ingresoToDelete = {id: 1, nombre: 'Pago inicial'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-admin.tabla-crud>

        <x-admin.tabla-crud>
            <x-slot name="titulo">
                <h3 class="text-2xl text-gray-800 mb-4 border-b pb-4 nunito-bold">Gastos</h3>
            </x-slot>
            <x-slot name="filtros">
                <div class="flex flex-wrap gap-2">
                    <input type="text" placeholder="Buscar gasto..." class="border rounded px-3 py-2 text-sm w-full sm:w-40" />
                    <select class="border rounded px-1 py-2 text-sm w-full sm:w-40">
                        <option class="nunito-bold" value="">Todos los proyectos</option>
                        <option class="nunito-bold">Proyecto Alpha</option>
                        <option class="nunito-bold">Proyecto Beta</option>
                        <option class="nunito-bold">Proyecto BAC</option>
                    </select>
                    <div class="flex items-center gap-2">
                        <input type="number" x-model="minGastoMonto" placeholder="Monto mín." class="border rounded px-3 py-2 text-sm w-28" />
                        <span class="text-gray-500">-</span>
                        <input type="number" x-model="maxGastoMonto" placeholder="Monto máx." class="border rounded px-3 py-2 text-sm w-28" />
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
                            <th class="py-2 px-4 text-left">ID</th>
                            <th class="py-2 px-4 text-left">Proyecto</th>
                            <th class="py-2 px-4 text-left">Nombre</th>
                            <th class="py-2 px-4 text-left">Fecha</th>
                            <th class="py-2 px-4 text-left">Monto</th>
                            <th class="py-2 px-4 text-left">Categoría</th>
                            <th class="py-2 px-4 text-left">Descripción</th>
                            <th class="py-2 px-4 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4">1</td>
                            <td class="py-2 px-4">Proyecto BAC</td>
                            <td class="py-2 px-4">Compra de software</td>
                            <td class="py-2 px-4">2025-07-22</td>
                            <td class="py-2 px-4">L. 5,500.00</td>
                            <td class="py-2 px-4">Licencias</td>
                            <td class="py-2 px-4">Licencias de software de desarrollo</td>
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
    <x-admin.form-modal
        modalName="isModalOpen"
        title="Nuevo Proyecto"
        submitLabel="Guardar Proyecto"
        maxWidth="max-w-4xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" id="nombre" name="nombre"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha Inicial del Proyecto</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_estimada_fin" class="block text-sm font-medium text-gray-700">Fecha Estimada de Finalización</label>
                <input type="date" id="fecha_estimada_fin" name="fecha_estimada_fin"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha de Finalización</label>
                <input type="date" id="fecha_fin" name="fecha_fin"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
            <div>
                <label for="actividades" class="block text-sm font-medium text-gray-700">Actividades</label>
                <input type="text" id="actividades" name="actividades"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="orden_servicio" class="block text-sm font-medium text-gray-700">Orden de Servicio</label>
                <input type="text" id="orden_servicio" name="orden_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="estado_proyecto" class="block text-sm font-medium text-gray-700">Estado de Proyecto</label>
                <select id="estado_proyecto" name="estado_proyecto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option>En Proceso</option>
                    <option>Finalizado</option>
                    <option>Pendiente</option>
                    <option>Cancelado</option>
                </select>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Ingreso -->
    <x-admin.edit-modal
        modalName="isEditIngresoModalOpen"
        title="Editar Ingreso"
        itemToEdit="ingresoToEdit"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_ingreso_proyecto" class="block text-sm font-medium text-gray-700">Proyecto</label>
                <select id="edit_ingreso_proyecto" name="edit_ingreso_proyecto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option :selected="ingresoToEdit.proyecto === 'Proyecto Alpha'">Proyecto Alpha</option>
                    <option :selected="ingresoToEdit.proyecto === 'Proyecto Beta'">Proyecto Beta</option>
                    <option :selected="ingresoToEdit.proyecto === 'Proyecto BAC'">Proyecto BAC</option>
                </select>
            </div>
            <div>
                <label for="edit_ingreso_nombre" class="block text-sm font-medium text-gray-700">Nombre del Ingreso</label>
                <input type="text" id="edit_ingreso_nombre" name="edit_ingreso_nombre" :value="ingresoToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_ingreso_fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                <input type="date" id="edit_ingreso_fecha" name="edit_ingreso_fecha" :value="ingresoToEdit.fecha" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_ingreso_monto" class="block text-sm font-medium text-gray-700">Monto</label>
                <input type="number" id="edit_ingreso_monto" name="edit_ingreso_monto" :value="ingresoToEdit.monto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="edit_ingreso_categoria" class="block text-sm font-medium text-gray-700">Categoría</label>
                <select id="edit_ingreso_categoria" name="edit_ingreso_categoria" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option :selected="ingresoToEdit.categoria === 'Salarios'">Salarios</option>
                </select>
            </div>
            <div class="col-span-2">
                <label for="edit_ingreso_descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="edit_ingreso_descripcion" name="edit_ingreso_descripcion" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2" x-text="ingresoToEdit.descripcion"></textarea>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Editar Gasto -->
    <x-admin.edit-modal
        modalName="isEditGastoModalOpen"
        title="Editar Gasto"
        itemToEdit="gastoToEdit"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_gasto_proyecto" class="block text-sm font-medium text-gray-700">Proyecto</label>
                <select id="edit_gasto_proyecto" name="edit_gasto_proyecto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option :selected="gastoToEdit.proyecto === 'Proyecto Alpha'">Proyecto Alpha</option>
                    <option :selected="gastoToEdit.proyecto === 'Proyecto Beta'">Proyecto Beta</option>
                    <option :selected="gastoToEdit.proyecto === 'Proyecto BAC'">Proyecto BAC</option>
                </select>
            </div>
            <div>
                <label for="edit_gasto_nombre" class="block text-sm font-medium text-gray-700">Nombre del Gasto</label>
                <input type="text" id="edit_gasto_nombre" name="edit_gasto_nombre" :value="gastoToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_gasto_fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                <input type="date" id="edit_gasto_fecha" name="edit_gasto_fecha" :value="gastoToEdit.fecha" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_gasto_monto" class="block text-sm font-medium text-gray-700">Monto</label>
                <input type="number" id="edit_gasto_monto" name="edit_gasto_monto" :value="gastoToEdit.monto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="edit_gasto_categoria" class="block text-sm font-medium text-gray-700">Categoría</label>
                <select id="edit_gasto_categoria" name="edit_gasto_categoria" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option :selected="gastoToEdit.categoria === 'Alquiler'">Alquiler</option>
                    <option :selected="gastoToEdit.categoria === 'Licencias'">Licencias</option>
                </select>
            </div>
            <div class="col-span-2">
                <label for="edit_gasto_descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="edit_gasto_descripcion" name="edit_gasto_descripcion" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2" x-text="gastoToEdit.descripcion"></textarea>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Nuevo Ingreso -->
    <x-admin.form-modal
        modalName="isIngresoModalOpen"
        title="Nuevo Ingreso"
        submitLabel="Guardar Ingreso"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="ingreso_proyecto" class="block text-sm font-medium text-gray-700">Proyecto</label>
                <select id="ingreso_proyecto" name="ingreso_proyecto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option>Proyecto Alpha</option>
                    <option>Proyecto Beta</option>
                    <option>Proyecto BAC</option>
                </select>
            </div>
            <div>
                <label for="ingreso_nombre" class="block text-sm font-medium text-gray-700">Nombre del Ingreso</label>
                <input type="text" id="ingreso_nombre" name="ingreso_nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="ingreso_fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                <input type="date" id="ingreso_fecha" name="ingreso_fecha" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="ingreso_monto" class="block text-sm font-medium text-gray-700">Monto</label>
                <input type="number" id="ingreso_monto" name="ingreso_monto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="ingreso_categoria" class="block text-sm font-medium text-gray-700">Categoría</label>
                <select id="ingreso_categoria" name="ingreso_categoria" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option>Salarios</option>
                </select>
            </div>
            <div class="col-span-2">
                <label for="ingreso_descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="ingreso_descripcion" name="ingreso_descripcion" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Nuevo Gasto -->
    <x-admin.form-modal
        modalName="isGastoModalOpen"
        title="Nuevo Gasto"
        submitLabel="Guardar Gasto"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="gasto_proyecto" class="block text-sm font-medium text-gray-700">Proyecto</label>
                <select id="gasto_proyecto" name="gasto_proyecto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option>Proyecto Alpha</option>
                    <option>Proyecto Beta</option>
                    <option>Proyecto BAC</option>
                </select>
            </div>
            <div>
                <label for="gasto_nombre" class="block text-sm font-medium text-gray-700">Nombre del Gasto</label>
                <input type="text" id="gasto_nombre" name="gasto_nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="gasto_fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                <input type="date" id="gasto_fecha" name="gasto_fecha" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="gasto_monto" class="block text-sm font-medium text-gray-700">Monto</label>
                <input type="number" id="gasto_monto" name="gasto_monto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="gasto_categoria" class="block text-sm font-medium text-gray-700">Categoría</label>
                <select id="gasto_categoria" name="gasto_categoria" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option>Alquiler</option>
                    <option>Licencias</option>
                </select>
            </div>
            <div class="col-span-2">
                <label for="gasto_descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="gasto_descripcion" name="gasto_descripcion" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Confirmar Eliminación Proyecto -->
    <x-admin.confirmation-modal
        modalName="isDeleteProjectModalOpen"
        itemToDelete="projectToDelete"
        message="¿Estás seguro de que quieres eliminar el proyecto" />

    <!-- Modal Confirmar Eliminación Ingreso -->
    <x-admin.confirmation-modal
        modalName="isDeleteIngresoModalOpen"
        itemToDelete="ingresoToDelete"
        message="¿Estás seguro de que quieres eliminar el ingreso" />

    <!-- Modal Confirmar Eliminación Gasto -->
    <x-admin.confirmation-modal
        modalName="isDeleteGastoModalOpen"
        itemToDelete="gastoToDelete"
        message="¿Estás seguro de que quieres eliminar el gasto" />

    <!-- Modal Editar Proyecto -->
    <x-admin.edit-modal
        modalName="isEditProjectModalOpen"
        title="Editar Proyecto"
        itemToEdit="projectToEdit"
        maxWidth="max-w-4xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" id="edit_nombre" name="edit_nombre" :value="projectToEdit.nombre"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha Inicial del Proyecto</label>
                <input type="date" id="edit_fecha_inicio" name="edit_fecha_inicio" :value="projectToEdit.fecha_inicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_estimada_fin" class="block text-sm font-medium text-gray-700">Fecha Estimada de Finalización</label>
                <input type="date" id="edit_fecha_estimada_fin" name="edit_fecha_estimada_fin" :value="projectToEdit.fecha_estimada_fin"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_fin" class="block text-sm font-medium text-gray-700">Fecha de Finalización</label>
                <input type="date" id="edit_fecha_fin" name="edit_fecha_fin" :value="projectToEdit.fecha_fin"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="edit_descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="edit_descripcion" name="edit_descripcion" rows="3" x-text="projectToEdit.descripcion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
            <div>
                <label for="edit_actividades" class="block text-sm font-medium text-gray-700">Actividades</label>
                <input type="text" id="edit_actividades" name="edit_actividades" :value="projectToEdit.actividades"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_orden_servicio" class="block text-sm font-medium text-gray-700">Orden de Servicio</label>
                <input type="text" id="edit_orden_servicio" name="edit_orden_servicio" :value="projectToEdit.orden_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_estado_proyecto" class="block text-sm font-medium text-gray-700">Estado de Proyecto</label>
                <select id="edit_estado_proyecto" name="edit_estado_proyecto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option :selected="projectToEdit.estado === 'En Proceso'">En Proceso</option>
                    <option :selected="projectToEdit.estado === 'Finalizado'">Finalizado</option>
                    <option :selected="projectToEdit.estado === 'Pendiente'">Pendiente</option>
                    <option :selected="projectToEdit.estado === 'Cancelado'">Cancelado</option>
                </select>
            </div>
        </div>
    </x-admin.edit-modal>
</div>