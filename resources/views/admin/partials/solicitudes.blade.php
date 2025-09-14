<div
    x-data="{ 
        tab: 'solicitudes', 
        searchSolicitud: '',
        estadoSolicitud: '',
        ordenarPor: 'id',
        searchContacto: '',
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
        contactoToDelete: null,
        deleteSolicitud() {
            if (this.solicitudToDelete) {
                console.log('Eliminando solicitud:', this.solicitudToDelete);
                this.isDeleteModalOpen = false;
                this.solicitudToDelete = null;
            }
        },
        deleteEstado() {
            if (this.estadoToDelete) {
                console.log('Eliminando estado:', this.estadoToDelete);
                this.isDeleteEstadoModalOpen = false;
                this.estadoToDelete = null;
            }
        },
        deleteContacto() {
            if (this.contactoToDelete) {
                console.log('Eliminando contacto:', this.contactoToDelete);
                this.isDeleteContactoModalOpen = false;
                this.contactoToDelete = null;
            }
        }
    }" @include('partials.persist-tab', ['tabKey' => 'admin-solicitudes-tab'])
    @confirm-delete.window="
        if (isDeleteModalOpen) {
            deleteSolicitud();
        } else if (isDeleteEstadoModalOpen) {
            deleteEstado();
        } else if (isDeleteContactoModalOpen) {
            deleteContacto();
        }
    ">
    <ul class="flex border-b border-gray-200 dark:border-gray-700 nunito-bold">
        <li @click="tab='solicitudes'"
            :class="tab==='solicitudes' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-100 hover:text-blue-500 dark:hover:text-blue-400 cursor-pointer'"
            class="mr-6 pb-2 nunito-bold">Solicitudes</li>
        <li @click="tab='contactos'"
            :class="tab==='contactos' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-100 hover:text-blue-500 dark:hover:text-blue-400 cursor-pointer'"
            class="mr-6 pb-2 nunito-bold">Contactos</li>
    </ul>
    
    <!-- TAB: Solicitudes -->
    <div x-show="tab==='solicitudes'" class="overflow-x-auto">
        <x-admin.tabla-mobile class="nunito-bold bg-white dark:bg-gray-900" titulo="Gestión de Solicitudes">
            <x-slot name="filtros">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchSolicitud',
                    'filtrosSelect' => [
                        'estadoSolicitud' => [
                            'label' => 'estados',
                            'options' => ['Abierta', 'En Proceso', 'Cerrada']
                        ]
                    ],
                    'ordenarOptions' => [
                        'id' => 'ID',
                        'fecha_creacion' => 'Fecha Creación',
                        'estado_solicitud' => 'Estado'
                    ]
                ])
            </x-slot>
            <x-slot name="boton">
                <div class="flex flex-col gap-2 w-full sm:w-auto">
                    <button @click="isModalOpen = true"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nueva
                        Solicitud</button>
                    <a href="/admin/reportes-header?modulo=Solicitudes&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                       class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                </div>
            </x-slot>

            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">ID</th>
                        <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Cliente</th>
                        <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">N° Solicitud ACF</th>
                        <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">N° Solicitud Cliente</th>
                        <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Descripción</th>
                        <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Fecha Creación</th>
                        <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Estado</th>
                        <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 nunito-regular">
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">1</td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">CLI-001</td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">ACF-2025-001</td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">SOL-001</td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">Problema con equipo de red</td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">2025-07-01</td>
                        <td class="py-2 px-4"><span class="bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200 px-2 py-1 rounded text-xs nunito-regular">Abierta</span></td>
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

            <x-slot name="mobileTemplate">
                <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <div class="text-lg font-bold text-gray-800 dark:text-gray-200">Proyecto / Solicitud</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">CLI-001 · ACF-2025-001</div>
                            </div>
                            <div><span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200">Abierta</span></div>
                        </div>
                        <div class="text-sm text-gray-700 dark:text-gray-200 mb-1"><strong>ID:</strong> 1</div>
                        <div class="text-sm text-gray-700 dark:text-gray-200 mb-1"><strong>N° Solicitud Cliente:</strong> SOL-001</div>
                        <div class="text-sm text-gray-700 dark:text-gray-200 mb-1"><strong>Descripción:</strong> Problema con equipo de red</div>
                        <div class="text-sm text-gray-700 dark:text-gray-200 mb-3"><strong>Fecha Creación:</strong> 2025-07-01</div>
                        <div class="flex justify-end gap-2">
                            <button @click="isEditModalOpen = true; solicitudToEdit = {
                                id: 1,
                                id_cliente: 'CLI-001',
                                num_solicitud_acf: 'ACF-2025-001',
                                num_solicitud_cliente: 'SOL-001',
                                descripcion_problema: 'Problema con equipo de red',
                                fecha_creacion: '2025-07-01',
                                fecha_modificacion: '2025-07-01',
                                estado_solicitud: 'Abierta',
                                id_contacto: 'CON-001'
                            }" class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm"><i class="fas fa-edit"></i> Editar</button>
                            <button @click="isDeleteModalOpen = true; solicitudToDelete = {id: 1}" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"><i class="fas fa-trash"></i> Eliminar</button>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-admin.tabla-mobile>
    </div>

    <!-- TAB: Contactos -->
    <div x-show="tab==='contactos'" class="overflow-x-auto mt-6">
        <x-admin.tabla-mobile class="nunito-bold bg-white dark:bg-gray-900" titulo="Lista de Contactos">
            <x-slot name="filtros">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchContacto',
                    'filtrosSelect' => [],
                    'ordenarOptions' => [
                        'id' => 'ID',
                        'tipo_contacto' => 'Tipo Contacto',
                        'valor_contacto' => 'Valor Contacto'
                    ]
                ])
            </x-slot>
            <x-slot name="boton">
                <button @click="isContactoModalOpen = true"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo
                    Contacto</button>
            </x-slot>

            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">ID</th>
                        <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Tipo Contacto</th>
                        <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Valor Contacto</th>
                        <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">ID Persona</th>
                        <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 nunito-regular">
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">1</td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">Email</td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">contacto@empresa.com</td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">PER-001</td>
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

            <x-slot name="mobileTemplate">
                <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <div class="text-lg font-bold text-gray-800 dark:text-gray-200">Email</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">contacto@empresa.com</div>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">PER-001</div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3">
                            <button @click="isEditContactoModalOpen = true; contactoToEdit = {
                                id: 1,
                                tipo_contacto: 'Email',
                                valor_contacto: 'contacto@empresa.com',
                                id_persona: 'PER-001'
                            }" class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm"><i class="fas fa-edit"></i> Editar</button>
                            <button @click="isDeleteContactoModalOpen = true; contactoToDelete = {id: 1}" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"><i class="fas fa-trash"></i> Eliminar</button>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-admin.tabla-mobile>
    </div>

    <!-- Modal Nueva Solicitud -->
    <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Nueva Solicitud" submitLabel="Guardar Solicitud"
        maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl" minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="id_cliente" class="block text-sm font-medium text-gray-700 nunito-bold">ID Cliente</label>
                <input type="text" id="id_cliente" name="id_cliente"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="num_solicitud_acf" class="block text-sm font-medium text-gray-700 nunito-bold">N° Solicitud ACF</label>
                <input type="text" id="num_solicitud_acf" name="num_solicitud_acf"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="num_solicitud_cliente" class="block text-sm font-medium text-gray-700 nunito-bold">N° Solicitud
                    Cliente</label>
                <input type="text" id="num_solicitud_cliente" name="num_solicitud_cliente"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="descripcion_problema" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción del
                    Problema</label>
                <textarea id="descripcion_problema" name="descripcion_problema" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
            <div>
                <label for="fecha_creacion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de Creación</label>
                <input type="date" id="fecha_creacion" name="fecha_creacion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_modificacion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de
                    Modificación</label>
                <input type="date" id="fecha_modificacion" name="fecha_modificacion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="estado_solicitud" class="block text-sm font-medium text-gray-700 nunito-bold">Estado de la
                    Solicitud</label>
                <select id="estado_solicitud" name="estado_solicitud"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option class="nunito-regular">Abierta</option>
                    <option class="nunito-regular">En Proceso</option>
                    <option class="nunito-regular">Cerrada</option>
                </select>
            </div>
            <div>
                <label for="id_contacto" class="block text-sm font-medium text-gray-700 nunito-bold">ID Contacto</label>
                <input type="text" id="id_contacto" name="id_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Solicitud -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Solicitud" itemToEdit="solicitudToEdit"
        maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl" minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="edit_id_cliente" class="block text-sm font-medium text-gray-700 nunito-bold">ID Cliente</label>
                <input type="text" id="edit_id_cliente" name="edit_id_cliente" x-model="solicitudToEdit.id_cliente"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_num_solicitud_acf" class="block text-sm font-medium text-gray-700 nunito-bold">N° Solicitud
                    ACF</label>
                <input type="text" id="edit_num_solicitud_acf" name="edit_num_solicitud_acf"
                    x-model="solicitudToEdit.num_solicitud_acf"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_num_solicitud_cliente" class="block text-sm font-medium text-gray-700 nunito-bold">N° Solicitud
                    Cliente</label>
                <input type="text" id="edit_num_solicitud_cliente" name="edit_num_solicitud_cliente"
                    x-model="solicitudToEdit.num_solicitud_cliente"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="edit_descripcion_problema" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción del
                    Problema</label>
                <textarea id="edit_descripcion_problema" name="edit_descripcion_problema" rows="2"
                    x-model="solicitudToEdit.descripcion_problema"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
            <div>
                <label for="edit_fecha_creacion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de
                    Creación</label>
                <input type="date" id="edit_fecha_creacion" name="edit_fecha_creacion"
                    x-model="solicitudToEdit.fecha_creacion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_modificacion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de
                    Modificación</label>
                <input type="date" id="edit_fecha_modificacion" name="edit_fecha_modificacion"
                    x-model="solicitudToEdit.fecha_modificacion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_estado_solicitud" class="block text-sm font-medium text-gray-700 nunito-bold">Estado de la
                    Solicitud</label>
                <select id="edit_estado_solicitud" name="edit_estado_solicitud" x-model="solicitudToEdit.estado_solicitud"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option class="nunito-regular">Abierta</option>
                    <option class="nunito-regular">En Proceso</option>
                    <option class="nunito-regular">Cerrada</option>
                </select>
            </div>
            <div>
                <label for="edit_id_contacto" class="block text-sm font-medium text-gray-700 nunito-bold">ID Contacto</label>
                <input type="text" id="edit_id_contacto" name="edit_id_contacto" x-model="solicitudToEdit.id_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Solicitud -->
    <x-admin.confirmation-modal 
        modal-name="isDeleteModalOpen" 
        title="Eliminar Solicitud"
        item-to-delete="solicitudToDelete"
        item-name-property="id"
        message="¿Estás seguro de que deseas eliminar la solicitud ID" />

    <!-- Modal Nuevo Estado -->
    <x-admin.form-modal class="nunito-bold" modalName="isEstadoModalOpen" title="Nuevo Estado de Solicitud" submitLabel="Guardar Estado"
        maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl" minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="nombre_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Estado</label>
                <input type="text" id="nombre_estado" name="nombre_estado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="descripcion_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="descripcion_estado" name="descripcion_estado" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Estado -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditEstadoModalOpen" title="Editar Estado de Solicitud" itemToEdit="estadoToEdit"
        maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl" minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="edit_nombre_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del
                    Estado</label>
                <input type="text" id="edit_nombre_estado" name="edit_nombre_estado" x-model="estadoToEdit.nombre_estado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_descripcion_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="edit_descripcion_estado" name="edit_descripcion_estado" rows="2"
                    x-model="estadoToEdit.descripcion_estado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Estado -->
    <x-admin.confirmation-modal 
        modal-name="isDeleteEstadoModalOpen" 
        title="Eliminar Estado"
        item-to-delete="estadoToDelete"
        item-name-property="nombre_estado"
        message="¿Estás seguro de que deseas eliminar el estado" />

    <!-- Modal Nuevo Contacto -->
    <x-admin.form-modal class="nunito-bold" modalName="isContactoModalOpen" title="Nuevo Contacto" submitLabel="Guardar Contacto"
        maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl" minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="tipo_contacto" class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de Contacto</label>
                <input type="text" id="tipo_contacto" name="tipo_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="valor_contacto" class="block text-sm font-medium text-gray-700 nunito-bold">Valor Contacto</label>
                <input type="text" id="valor_contacto" name="valor_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="id_persona" class="block text-sm font-medium text-gray-700 nunito-bold">ID Persona</label>
                <input type="text" id="id_persona" name="id_persona"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Contacto -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditContactoModalOpen" title="Editar Contacto" itemToEdit="contactoToEdit"
        maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl" minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="edit_tipo_contacto" class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de Contacto</label>
                <input type="text" id="edit_tipo_contacto" name="edit_tipo_contacto"
                    x-model="contactoToEdit.tipo_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_valor_contacto" class="block text-sm font-medium text-gray-700 nunito-bold">Valor Contacto</label>
                <input type="text" id="edit_valor_contacto" name="edit_valor_contacto"
                    x-model="contactoToEdit.valor_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_id_persona" class="block text-sm font-medium text-gray-700 nunito-bold">ID Persona</label>
                <input type="text" id="edit_id_persona" name="edit_id_persona" x-model="contactoToEdit.id_persona"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Contacto -->
    <x-admin.confirmation-modal 
        modal-name="isDeleteContactoModalOpen" 
        title="Eliminar Contacto"
        item-to-delete="contactoToDelete"
        item-name-property="id"
        message="¿Estás seguro de que deseas eliminar el contacto ID" />
</div>
