<div x-data="gestionSolicitudes()" x-init="init()" @include('partials.persist-tab', ['tabKey'=>
    'admin-solicitudes-tab'])
    @modal-submit.window="
    if ($event.detail.formId === 'solicitud-form' || $event.detail.formId === 'solicitud-edit-form') {
    submitSolicitud();
    } else if ($event.detail.formId === 'contacto-form' || $event.detail.formId === 'contacto-edit-form') {
    submitContacto();
    }
    "
    @confirm-delete.window="
    if (isDeleteModalOpen) {
    performDeleteSolicitud();
    } else if (isDeleteEstadoModalOpen) {
    // future: performDeleteEstado();
    } else if (isDeleteContactoModalOpen) {
    performDeleteContacto();
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
    <div x-show="tab==='solicitudes'">
        <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
            <x-slot name="filters">
                <div class="flex flex-col sm:flex-row gap-3 items-stretch w-full">
                    <input type="text" x-model="searchSolicitud" placeholder="Buscar..."
                        class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-48 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />
                    <select x-model="estadoSolicitud"
                        class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 md:w-64 sm:min-w-[14rem] md:min-w-[16rem] shrink-0 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                        <option value="">Todos los estados</option>
                        <template x-for="opt in estadosOptions" :key="opt.value">
                            <option :value="opt.value" x-text="opt.label"></option>
                        </template>
                    </select>
                    <select x-model="ordenarPor"
                        class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 md:w-64 sm:min-w-[14rem] md:min-w-[16rem] shrink-0 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                        <option value="estado_solicitud">Ordenar por Estado</option>
                        <option value="cliente">Ordenar por Cliente</option>
                        <option value="solicitud_acf">Ordenar por Solicitud ACF</option>
                        <option value="solicitud_cliente">Ordenar por Solicitud Cliente</option>
                    </select>
                </div>
            </x-slot>
            <x-slot name="actions">
                <div class="flex flex-col sm:flex-row items-center gap-2">
                    <button @click="openCreateSolicitud()" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-plus"></i> Nueva Solicitud
                    </button>
                    <a :href="reportUrl()" target="_blank" class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                </div>
            </x-slot>
            <x-slot name="table">
                <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Cliente</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">N° Solicitud ACF</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">N° Solicitud Cliente</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Descripción</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Contacto</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Estado</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingSolicitudes">
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando solicitudes...</td>
                            </tr>
                        </template>
                        <template x-if="!loadingSolicitudes && filteredSolicitudes().length === 0">
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular">No se encontraron solicitudes.</td>
                            </tr>
                        </template>
                        <template x-for="sol in filteredSolicitudes()" :key="sol.id">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                                <td class="py-2 px-4" x-text="sol.cliente_nombre || clienteLabelById(sol.id_cliente_fk) || '—'"></td>
                                <td class="py-2 px-4" x-text="sol.numero_solicitud_acf || '—'"></td>
                                <td class="py-2 px-4" x-text="sol.numero_solicitud_cliente || '—'"></td>
                                <td class="py-2 px-4" x-text="sol.descripcion_problema || '—'"></td>
                                <td class="py-2 px-4" x-text="sol.contacto_valor || '—'"></td>
                                <td class="py-2 px-4"><span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300" x-text="sol.estado_nombre || '—'"></span></td>
                                <td class="py-2 px-4 flex gap-2">
                                    <button @click.prevent="openEditSolicitud(sol)" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                                    <button @click.prevent="openDeleteSolicitud(sol)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </x-slot>
            <x-slot name="cards">
                <template x-if="loadingSolicitudes">
                    <div class="p-8 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando solicitudes...</div>
                </template>
                <template x-if="!loadingSolicitudes && filteredSolicitudes().length === 0">
                    <div class="p-8 text-center text-gray-500">No se encontraron solicitudes.</div>
                </template>
                <template x-for="sol in filteredSolicitudes()" :key="'card-'+sol.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white" x-text="sol.cliente_nombre || clienteLabelById(sol.id_cliente_fk) || '—'"></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><span class="nunito-bold">ACF:</span> <span x-text="sol.numero_solicitud_acf || '—'"></span> • <span class="nunito-bold">Cliente:</span> <span x-text="sol.numero_solicitud_cliente || '—'"></span></p>
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300" x-text="sol.estado_nombre || '—'"></span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300"><span class="nunito-bold">Contacto:</span> <span x-text="sol.contacto_valor || '—'"></span></p>
                        <p class="text-sm text-gray-600 dark:text-gray-300" x-text="sol.descripcion_problema || '—'"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="openEditSolicitud(sol)" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="openDeleteSolicitud(sol)" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </x-slot>
        </x-responsive-table>
    </div>

    <!-- TAB: Contactos -->
    <div x-show="tab==='contactos'" class="mt-6">
        <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
            <x-slot name="filters">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchContacto',
                    'filtrosSelect' => [],
                    'ordenarOptions' => [
                        'tipo_contacto' => 'Tipo Contacto',
                        'valor_contacto' => 'Valor Contacto',
                        'cliente' => 'Cliente',
                    ],
                    'ordenarModel' => 'ordenarPorContacto',
                ])
            </x-slot>
            <x-slot name="actions">
                <div class="flex flex-col sm:flex-row items-center gap-2">
                    <button @click="openCreateContacto()" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-plus"></i> Nuevo Contacto
                    </button>
                </div>
            </x-slot>
            <x-slot name="table">
                <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Tipo Contacto</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Valor Contacto</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Cliente</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingContactos">
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500 nunito-regular"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando contactos...</td>
                            </tr>
                        </template>
                        <template x-if="!loadingContactos && filteredContactos().length === 0">
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500 nunito-regular">No se encontraron contactos.</td>
                            </tr>
                        </template>
                        <template x-for="c in filteredContactos()" :key="c.id">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                                <td class="py-2 px-4" x-text="c.tipo_contacto"></td>
                                <td class="py-2 px-4" x-text="c.valor_contacto"></td>
                                <td class="py-2 px-4" x-text="clienteLabelById(c.id_cliente_fk) || '—'"></td>
                                <td class="py-2 px-4 flex gap-2">
                                    <button @click.prevent="openEditContacto(c)" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                                    <button @click.prevent="openDeleteContacto(c)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </x-slot>
            <x-slot name="cards">
                <template x-if="loadingContactos">
                    <div class="p-8 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando contactos...</div>
                </template>
                <template x-if="!loadingContactos && filteredContactos().length === 0">
                    <div class="p-8 text-center text-gray-500">No se encontraron contactos.</div>
                </template>
                <template x-for="c in filteredContactos()" :key="'card-c-'+c.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white" x-text="c.tipo_contacto"></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="clienteLabelById(c.id_cliente_fk) || '—'"></p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300"><span class="nunito-bold">Valor:</span> <span x-text="c.valor_contacto"></span></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="openEditContacto(c)" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="openDeleteContacto(c)" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </x-slot>
        </x-responsive-table>
    </div>

    <!-- Modal Nueva Solicitud -->
    <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Nueva Solicitud"
        submitLabel="Guardar Solicitud" formId="solicitud-form" maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl"
        minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="id_cliente" class="block text-sm font-medium text-gray-700 nunito-bold">Cliente</label>
                <select id="id_cliente" name="id_cliente" x-model="formSolicitud.id_cliente_fk" @change="onClienteChange()"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un cliente</option>
                    <template x-for="opt in clientesOptions" :key="opt.value">
                        <option :value="opt.value" x-text="opt.label"></option>
                    </template>
                </select>
                <p class="mt-1 text-xs text-red-600" x-show="errors.id_cliente_fk" x-text="errors.id_cliente_fk?.[0]"></p>
            </div>

            <div class="col-span-2">
                <label for="descripcion_problema"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Descripción del
                    Problema</label>
                <textarea id="descripcion_problema" name="descripcion_problema" rows="2"
                    x-model="formSolicitud.descripcion_problema"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                <p class="mt-1 text-xs text-red-600" x-show="errors.descripcion_problema" x-text="errors.descripcion_problema?.[0]"></p>
            </div>
            <div>
                <label for="estado_solicitud" class="block text-sm font-medium text-gray-700 nunito-bold">Estado de la
                    Solicitud</label>
                <select id="estado_solicitud" name="estado_solicitud" x-model="formSolicitud.id_estado_solicitud_fk"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un estado</option>
                    <template x-for="opt in estadosOptions" :key="opt.value">
                        <option :value="opt.value" x-text="opt.label"></option>
                    </template>
                </select>
                <p class="mt-1 text-xs text-red-600" x-show="errors.id_estado_solicitud_fk" x-text="errors.id_estado_solicitud_fk?.[0]"></p>
            </div>
            <div>
                <label for="id_contacto" class="block text-sm font-medium text-gray-700 nunito-bold">Contacto</label>
                <select id="id_contacto" name="id_contacto" x-model="formSolicitud.id_contacto_fk"
                    :disabled="!formSolicitud.id_cliente_fk"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2 disabled:opacity-60 disabled:cursor-not-allowed">
                    <option value="" class="nunito-regular">Seleccione un contacto</option>
                    <template x-for="opt in filteredContactosForSelectedCliente()" :key="opt.value">
                        <option :value="opt.value" x-text="opt.label"></option>
                    </template>
                </select>
                <p class="mt-1 text-xs text-red-600" x-show="errors.id_contacto_fk" x-text="errors.id_contacto_fk?.[0]"></p>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Solicitud -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Solicitud" itemToEdit="solicitudToEdit" formId="solicitud-edit-form"
        maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl" minHeight="min-h-[400px] xl:min-h-[600px]">
        <template x-if="solicitudToEdit">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="edit_id_cliente" class="block text-sm font-medium text-gray-700 nunito-bold">Cliente</label>
                <select id="edit_id_cliente" name="edit_id_cliente" x-model="formSolicitud.id_cliente_fk" @change="onClienteChange()"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un cliente</option>
                    <template x-for="opt in clientesOptions" :key="opt.value">
                        <option :value="opt.value" x-text="opt.label"></option>
                    </template>
                </select>
                <p class="mt-1 text-xs text-red-600" x-show="errors.id_cliente_fk" x-text="errors.id_cliente_fk?.[0]"></p>
            </div>

            <div class="col-span-2">
                <label for="edit_descripcion_problema"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Descripción del
                    Problema</label>
                <textarea id="edit_descripcion_problema" name="edit_descripcion_problema" rows="2"
                    x-model="formSolicitud.descripcion_problema"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                <p class="mt-1 text-xs text-red-600" x-show="errors.descripcion_problema" x-text="errors.descripcion_problema?.[0]"></p>
            </div>
            <div>
                <label for="edit_estado_solicitud" class="block text-sm font-medium text-gray-700 nunito-bold">Estado de
                    la
                    Solicitud</label>
                <select id="edit_estado_solicitud" name="edit_estado_solicitud"
                    x-model="formSolicitud.id_estado_solicitud_fk"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un estado</option>
                    <template x-for="opt in estadosOptions" :key="opt.value">
                        <option :value="opt.value" x-text="opt.label"></option>
                    </template>
                </select>
                <p class="mt-1 text-xs text-red-600" x-show="errors.id_estado_solicitud_fk" x-text="errors.id_estado_solicitud_fk?.[0]"></p>
            </div>
            <div>
                <label for="edit_id_contacto"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Contacto</label>
                <select id="edit_id_contacto" name="edit_id_contacto" x-model="formSolicitud.id_contacto_fk"
                    :disabled="!formSolicitud.id_cliente_fk"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2 disabled:opacity-60 disabled:cursor-not-allowed">
                    <option value="" class="nunito-regular">Seleccione un contacto</option>
                    <template x-for="opt in filteredContactosForSelectedCliente()" :key="opt.value">
                        <option :value="opt.value" x-text="opt.label"></option>
                    </template>
                </select>
                <p class="mt-1 text-xs text-red-600" x-show="errors.id_contacto_fk" x-text="errors.id_contacto_fk?.[0]"></p>
            </div>
        </div>
        </template>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Solicitud -->
    <x-admin.confirmation-modal modal-name="isDeleteModalOpen" title="Eliminar Solicitud"
        item-to-delete="solicitudToDelete" item-name-property="id"
        message="¿Estás seguro de que deseas eliminar la solicitud ID" />

    <!-- Modal Nuevo Estado -->
    <x-admin.form-modal class="nunito-bold" modalName="isEstadoModalOpen" title="Nuevo Estado de Solicitud"
        submitLabel="Guardar Estado" maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl"
        minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="nombre_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del
                    Estado</label>
                <input type="text" id="nombre_estado" name="nombre_estado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="descripcion_estado"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="descripcion_estado" name="descripcion_estado" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Estado -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditEstadoModalOpen" title="Editar Estado de Solicitud" itemToEdit="estadoToEdit"
        maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl" minHeight="min-h-[400px] xl:min-h-[600px]">
        <template x-if="estadoToEdit">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="edit_nombre_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del
                    Estado</label>
                <input type="text" id="edit_nombre_estado" name="edit_nombre_estado"
                    x-model="estadoToEdit.nombre_estado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_descripcion_estado"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="edit_descripcion_estado" name="edit_descripcion_estado" rows="2"
                    x-model="estadoToEdit.descripcion_estado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
        </template>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Estado -->
    <x-admin.confirmation-modal modal-name="isDeleteEstadoModalOpen" title="Eliminar Estado"
        item-to-delete="estadoToDelete" item-name-property="nombre_estado"
        message="¿Estás seguro de que deseas eliminar el estado" />

    <!-- Modal Nuevo Contacto -->
    <x-admin.form-modal class="nunito-bold" modalName="isContactoModalOpen" title="Nuevo Contacto"
        submitLabel="Guardar Contacto" formId="contacto-form" maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl"
        minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="tipo_contacto" class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de
                    Contacto</label>
                <input type="text" id="tipo_contacto" name="tipo_contacto" x-model="formContacto.tipo_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="valor_contacto" class="block text-sm font-medium text-gray-700 nunito-bold">Valor
                    Contacto</label>
                <input type="text" id="valor_contacto" name="valor_contacto" x-model="formContacto.valor_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="id_cliente_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Cliente</label>
                <select id="id_cliente_fk" name="id_cliente_fk" x-model="formContacto.id_cliente_fk"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un cliente</option>
                    <template x-for="opt in clientesOptions" :key="opt.value">
                        <option :value="opt.value" x-text="opt.label"></option>
                    </template>
                </select>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Contacto -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditContactoModalOpen" title="Editar Contacto" itemToEdit="contactoToEdit" formId="contacto-edit-form"
        maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl" minHeight="min-h-[400px] xl:min-h-[600px]">
        <template x-if="contactoToEdit">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="edit_tipo_contacto" class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de
                    Contacto</label>
                <input type="text" id="edit_tipo_contacto" name="edit_tipo_contacto"
                    x-model="formContacto.tipo_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_valor_contacto" class="block text-sm font-medium text-gray-700 nunito-bold">Valor
                    Contacto</label>
                <input type="text" id="edit_valor_contacto" name="edit_valor_contacto"
                    x-model="formContacto.valor_contacto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_id_cliente_fk"
                    class="block textsm font-medium text-gray-700 nunito-bold">Cliente</label>
                <select id="edit_id_cliente_fk" name="edit_id_cliente_fk" x-model="formContacto.id_cliente_fk"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un cliente</option>
                    <template x-for="opt in clientesOptions" :key="opt.value">
                        <option :value="opt.value" x-text="opt.label"></option>
                    </template>
                </select>
            </div>
        </div>
        </template>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Contacto -->
    <x-admin.confirmation-modal modal-name="isDeleteContactoModalOpen" title="Eliminar Contacto"
        item-to-delete="contactoToDelete" item-name-property="id"
        message="¿Estás seguro de que deseas eliminar el contacto ID" />
</div>