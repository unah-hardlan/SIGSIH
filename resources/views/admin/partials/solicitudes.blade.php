<div
    x-data="{ 
        tab: 'solicitudes', 
        isModalOpen: false, 
        isEditModalOpen: false, 
        solicitudToEdit: {
            id: null,
            id_cliente: '',
            num_solicitud_acf: '',
            num_solicitud_cliente: '',
            descripcion_problema: '',
            fecha_creacion: '',
            fecha_modificacion: '',
            estado_solicitud: '',
            id_contacto: ''
        }, 
        isDeleteModalOpen: false, 
        solicitudToDelete: null, 
        isEstadoModalOpen: false, 
        isEditEstadoModalOpen: false, 
        estadoToEdit: {
            id: null,
            nombre_estado: '',
            descripcion_estado: ''
        }, 
        isDeleteEstadoModalOpen: false, 
        estadoToDelete: null,
        isContactoModalOpen: false,
        isEditContactoModalOpen: false,
        contactoToEdit: {
            id: null,
            tipo_contacto: '',
            valor_contacto: '',
            id_persona: ''
        },
        isDeleteContactoModalOpen: false,
        contactoToDelete: null
    }" @include('partials.persist-tab', ['tabKey' => 'admin-solicitudes-tab'])>
    <ul class="flex border-b nunito-bold">
        <li @click="tab='solicitudes'"
            :class="tab==='solicitudes' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 hover:text-blue-500 cursor-pointer'"
            class="mr-6 pb-2">Solicitudes</li>
        <li @click="tab='contactos'"
            :class="tab==='contactos' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 hover:text-blue-500 cursor-pointer'"
            class="mr-6 pb-2">Contactos</li>
    </ul>
    
    <!-- TAB: Solicitudes -->
    <div x-show="tab==='solicitudes'" class="overflow-x-auto">
        <x-admin.tabla-crud>
            <x-slot name="titulo">
                <h2 class="text-2xl text-gray-800 nunito-bold">Gestión de Solicitudes</h2>
            </x-slot>
            <x-slot name="filtros">
                <input type="text" placeholder="Buscar solicitud..."
                    class="border rounded px-3 py-2 text-sm w-full sm:w-48" />
                <select class="border rounded px-1 py-2 text-sm w-full sm:w-40">
                    <option class="nunito-bold" value="">Todos los estados</option>
                    <option class="nunito-bold">Abierta</option>
                    <option class="nunito-bold">En Proceso</option>
                    <option class="nunito-bold">Cerrada</option>
                </select>
            </x-slot>
            <x-slot name="boton">
                <div class="flex flex-col gap-2 w-full sm:w-auto">
                    <button @click="isModalOpen = true"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">Nueva
                        Solicitud</button>
                    <a href="/admin/reportes-header?modulo=Solicitudes&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                       class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                </div>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left">ID</th>
                            <th class="py-2 px-4 text-left">Cliente</th>
                            <th class="py-2 px-4 text-left">N° Solicitud ACF</th>
                            <th class="py-2 px-4 text-left">N° Solicitud Cliente</th>
                            <th class="py-2 px-4 text-left">Descripción</th>
                            <th class="py-2 px-4 text-left">Fecha Creación</th>
                            <th class="py-2 px-4 text-left">Estado</th>
                            <th class="py-2 px-4 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4">1</td>
                            <td class="py-2 px-4">CLI-001</td>
                            <td class="py-2 px-4">ACF-2025-001</td>
                            <td class="py-2 px-4">SOL-001</td>
                            <td class="py-2 px-4">Problema con equipo de red</td>
                            <td class="py-2 px-4">2025-07-01</td>
                            <td class="py-2 px-4"><span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Abierta</span></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click="isEditModalOpen = true; solicitudToEdit = {
                                    id: 1,
                                    id_cliente: 'CLI-001',
                                    num_solicitud_acf: 'ACF-2025-001',
                                    num_solicitud_cliente: 'SOL-001',
                                    descripcion_problema: 'Problema con equipo de red',
                                    fecha_creacion: '2025-07-01',
                                    fecha_modificacion: '2025-07-01',
                                    estado_solicitud: 'Abierta',
                                    id_contacto: 'CON-001'
                                }"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click="isDeleteModalOpen = true; solicitudToDelete = {id: 1}"
                                    class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-admin.tabla-crud>
    </div>

    <!-- TAB: Contactos -->
    <div x-show="tab==='contactos'" class="overflow-x-auto mt-6">
        <x-admin.tabla-crud>
            <x-slot name="titulo">
                <h2 class="text-2xl text-gray-800 nunito-bold">Contactos</h2>
            </x-slot>
            <x-slot name="filtros">
                <input type="text" placeholder="Buscar contacto..."
                    class="border rounded px-3 py-2 text-sm w-full sm:w-48" />
            </x-slot>
            <x-slot name="boton">
                <button @click="isContactoModalOpen = true"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">Nuevo
                    Contacto</button>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left">ID</th>
                            <th class="py-2 px-4 text-left">Tipo Contacto</th>
                            <th class="py-2 px-4 text-left">Valor Contacto</th>
                            <th class="py-2 px-4 text-left">ID Persona</th>
                            <th class="py-2 px-4 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4">1</td>
                            <td class="py-2 px-4">Email</td>
                            <td class="py-2 px-4">contacto@empresa.com</td>
                            <td class="py-2 px-4">PER-001</td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click="isEditContactoModalOpen = true; contactoToEdit = {
                                    id: 1,
                                    tipo_contacto: 'Email',
                                    valor_contacto: 'contacto@empresa.com',
                                    id_persona: 'PER-001'
                                }"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click="isDeleteContactoModalOpen = true; contactoToDelete = {id: 1}"
                                    class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-admin.tabla-crud>
    </div>

    <!-- Modal Nueva Solicitud -->
    <x-admin.form-modal modalName="isModalOpen" title="Nueva Solicitud" submitLabel="Guardar Solicitud"
        maxWidth="max-w-4xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="id_cliente" class="block text-sm font-medium text-gray-700">ID Cliente</label>
                <input type="text" id="id_cliente" name="id_cliente"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="num_solicitud_acf" class="block text-sm font-medium text-gray-700">N° Solicitud ACF</label>
                <input type="text" id="num_solicitud_acf" name="num_solicitud_acf"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="num_solicitud_cliente" class="block text-sm font-medium text-gray-700">N° Solicitud
                    Cliente</label>
                <input type="text" id="num_solicitud_cliente" name="num_solicitud_cliente"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="descripcion_problema" class="block text-sm font-medium text-gray-700">Descripción del
                    Problema</label>
                <textarea id="descripcion_problema" name="descripcion_problema" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
            <div>
                <label for="fecha_creacion" class="block text-sm font-medium text-gray-700">Fecha de Creación</label>
                <input type="date" id="fecha_creacion" name="fecha_creacion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_modificacion" class="block text-sm font-medium text-gray-700">Fecha de
                    Modificación</label>
                <input type="date" id="fecha_modificacion" name="fecha_modificacion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="estado_solicitud" class="block text-sm font-medium text-gray-700">Estado de la
                    Solicitud</label>
                <select id="estado_solicitud" name="estado_solicitud"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option>Abierta</option>
                    <option>En Proceso</option>
                    <option>Cerrada</option>
                </select>
            </div>
            <div>
                <label for="id_contacto" class="block text-sm font-medium text-gray-700">ID Contacto</label>
                <input type="text" id="id_contacto" name="id_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Solicitud -->
    <x-admin.edit-modal modalName="isEditModalOpen" title="Editar Solicitud" itemToEdit="solicitudToEdit"
        maxWidth="max-w-4xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_id_cliente" class="block text-sm font-medium text-gray-700">ID Cliente</label>
                <input type="text" id="edit_id_cliente" name="edit_id_cliente" x-model="solicitudToEdit.id_cliente"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_num_solicitud_acf" class="block text-sm font-medium text-gray-700">N° Solicitud
                    ACF</label>
                <input type="text" id="edit_num_solicitud_acf" name="edit_num_solicitud_acf"
                    x-model="solicitudToEdit.num_solicitud_acf"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_num_solicitud_cliente" class="block text-sm font-medium text-gray-700">N° Solicitud
                    Cliente</label>
                <input type="text" id="edit_num_solicitud_cliente" name="edit_num_solicitud_cliente"
                    x-model="solicitudToEdit.num_solicitud_cliente"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="edit_descripcion_problema" class="block text-sm font-medium text-gray-700">Descripción del
                    Problema</label>
                <textarea id="edit_descripcion_problema" name="edit_descripcion_problema" rows="2"
                    x-model="solicitudToEdit.descripcion_problema"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
            <div>
                <label for="edit_fecha_creacion" class="block text-sm font-medium text-gray-700">Fecha de
                    Creación</label>
                <input type="date" id="edit_fecha_creacion" name="edit_fecha_creacion"
                    x-model="solicitudToEdit.fecha_creacion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_modificacion" class="block text-sm font-medium text-gray-700">Fecha de
                    Modificación</label>
                <input type="date" id="edit_fecha_modificacion" name="edit_fecha_modificacion"
                    x-model="solicitudToEdit.fecha_modificacion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_estado_solicitud" class="block text-sm font-medium text-gray-700">Estado de la
                    Solicitud</label>
                <select id="edit_estado_solicitud" name="edit_estado_solicitud" x-model="solicitudToEdit.estado_solicitud"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option>Abierta</option>
                    <option>En Proceso</option>
                    <option>Cerrada</option>
                </select>
            </div>
            <div>
                <label for="edit_id_contacto" class="block text-sm font-medium text-gray-700">ID Contacto</label>
                <input type="text" id="edit_id_contacto" name="edit_id_contacto" x-model="solicitudToEdit.id_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Solicitud -->
    <x-admin.confirmation-modal modalName="isDeleteModalOpen" itemToDelete="solicitudToDelete"
        message="¿Estás seguro de que quieres eliminar la solicitud?" />

    <!-- Modal Nuevo Estado -->
    <x-admin.form-modal modalName="isEstadoModalOpen" title="Nuevo Estado de Solicitud" submitLabel="Guardar Estado"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="nombre_estado" class="block text-sm font-medium text-gray-700">Nombre del Estado</label>
                <input type="text" id="nombre_estado" name="nombre_estado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="descripcion_estado" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="descripcion_estado" name="descripcion_estado" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Estado -->
    <x-admin.edit-modal modalName="isEditEstadoModalOpen" title="Editar Estado de Solicitud" itemToEdit="estadoToEdit"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="edit_nombre_estado" class="block text-sm font-medium text-gray-700">Nombre del
                    Estado</label>
                <input type="text" id="edit_nombre_estado" name="edit_nombre_estado" x-model="estadoToEdit.nombre_estado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_descripcion_estado" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="edit_descripcion_estado" name="edit_descripcion_estado" rows="2"
                    x-model="estadoToEdit.descripcion_estado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Estado -->
    <x-admin.confirmation-modal modalName="isDeleteEstadoModalOpen" itemToDelete="estadoToDelete"
        message="¿Estás seguro de que quieres eliminar el estado?" />

    <!-- Modal Nuevo Contacto -->
    <x-admin.form-modal modalName="isContactoModalOpen" title="Nuevo Contacto" submitLabel="Guardar Contacto"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="tipo_contacto" class="block text-sm font-medium text-gray-700">Tipo de Contacto</label>
                <input type="text" id="tipo_contacto" name="tipo_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="valor_contacto" class="block text-sm font-medium text-gray-700">Valor Contacto</label>
                <input type="text" id="valor_contacto" name="valor_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="id_persona" class="block text-sm font-medium text-gray-700">ID Persona</label>
                <input type="text" id="id_persona" name="id_persona"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Contacto -->
    <x-admin.edit-modal modalName="isEditContactoModalOpen" title="Editar Contacto" itemToEdit="contactoToEdit"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="edit_tipo_contacto" class="block text-sm font-medium text-gray-700">Tipo de Contacto</label>
                <input type="text" id="edit_tipo_contacto" name="edit_tipo_contacto"
                    x-model="contactoToEdit.tipo_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_valor_contacto" class="block text-sm font-medium text-gray-700">Valor Contacto</label>
                <input type="text" id="edit_valor_contacto" name="edit_valor_contacto"
                    x-model="contactoToEdit.valor_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_id_persona" class="block text-sm font-medium text-gray-700">ID Persona</label>
                <input type="text" id="edit_id_persona" name="edit_id_persona" x-model="contactoToEdit.id_persona"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Contacto -->
    <x-admin.confirmation-modal modalName="isDeleteContactoModalOpen" itemToDelete="contactoToDelete"
        message="¿Estás seguro de que quieres eliminar el contacto?" />
</div>
