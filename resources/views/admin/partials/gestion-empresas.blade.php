<div x-data="empresaData()" @include('partials.persist-tab', ['tabKey'=> 'admin-gestion-empresas-tab'])
    @modal-submit.window="
    if($event.detail.formId==='empresa-form'){
    submitEmpresa();
    }
    "
    @keydown.window.escape="isEmpresaModalOpen = false; isDeleteEmpresaModalOpen = false"
    @confirm-delete.window="
    if (isDeleteEmpresaModalOpen) {
    deleteEmpresa();
    }
    ">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Gestión de Empresas</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'searchEmpresa',
            'filtrosSelect' => [],
            'ordenarOptions' => [
            'nombre_comercial' => 'Nombre',
            'estado_cliente' => 'Estado',
            'fecha_registro' => 'Fecha'
            ]
            ])
        </x-slot>
        <x-slot name="actions">
            <div class="flex flex-col gap-2 w-full sm:w-auto">
                @perm(['Empresas','Gestión de Empresas','Gestion de Empresas'], 'insercion')
                <button @click="isEmpresaModalOpen = true"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                    Nueva Empresa
                </button>
                @else
                <button disabled
                    class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm cursor-not-allowed"
                    title="Sin permiso para crear">
                    Nueva Empresa
                </button>
                @endperm
                <a :href="reportUrl()" target="_blank"
                    class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                    <i class="fas fa-file-alt"></i> Generar Reporte
                </a>
            </div>
        </x-slot>
        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold rounded-t-lg">
                    <tr>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Nombre Comercial</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Razón Social</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Número de identificación fiscal</< /th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Fecha Registro</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Horario</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Estado</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingEmpresas">
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando empresas...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEmpresas && empresas.length === 0">
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay empresas registradas
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEmpresas && empresas.length > 0">
                        <template x-for="e in paginatedEmpresas()" :key="e.id">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                                <td class="py-2 px-4" x-text="e.nombre_comercial"></td>
                                <td class="py-2 px-4" x-text="e.razon_social"></td>
                                <td class="py-2 px-4" x-text="e.descripcion_empresa"></td>
                                <td class="py-2 px-4" x-text="e.rtn"></td>
                                <td class="py-2 px-4 break-all" x-text="e.fecha_registro"></td>
                                <td class="py-2 px-4" x-text="e.horario_atencion"></td>
                                <td class="py-2 px-4">
                                    <span class="px-2 py-1 rounded nunito-regular"
                                        :class="e.estado_label==='Activo' ? 'bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-100' : 'bg-red-700 text-red-100'"
                                        x-text="e.estado_label"></span>
                                </td>
                                <td class="py-2 px-4 flex gap-2">
                                    @perm(['Empresas','Gestión de Empresas','Gestion de Empresas'], 'actualizacion')
                                    <a href="#" @click.prevent="openEmpresaModal(true, e)"
                                        class="text-blue-500 hover:text-blue-700" title="Editar"><i class="fas fa-edit"></i></a>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para editar"><i class="fas fa-edit"></i></span>
                                    @endperm
                                    @perm(['Empresas','Gestión de Empresas','Gestion de Empresas'], 'eliminacion')
                                    <a href="#" @click.prevent="openDeleteEmpresaModal(e)"
                                        class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                    @else
                                    <span class="text-red-300 cursor-not-allowed" title="Sin permiso para eliminar"><i class="fas fa-trash"></i></span>
                                    @endperm
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>
        <x-slot name="cards">
            <template x-if="loadingEmpresas">
                <div class="p-8 text-center text-gray-500 nunito-regular"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando empresas...</div>
            </template>
            <template x-if="!loadingEmpresas && empresas.length === 0">
                <div class="p-8 text-center text-gray-500 nunito-regular">No hay empresas registradas</div>
            </template>
            <template x-for="e in paginatedEmpresas()" :key="'card-emp-'+(e.id || e.raw?.id_cliente_fk || Math.random())">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3 border border-gray-600 dark:border-gray-500">
                    <div class="flex justify-between items-start gap-3">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white" x-text="e.nombre_comercial"></h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="e.razon_social"></p>
                        </div>
                        <span class="px-2 py-1 rounded text-xs font-semibold"
                            :class="e.estado_label==='Activo' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                            x-text="e.estado_label"></span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300" x-text="e.descripcion_empresa || '—'"></p>
                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-300">
                        <div><span class="nunito-bold">RTN:</span> <span x-text="e.rtn || '—'"></span></div>
                        <div><span class="nunito-bold">Registro:</span> <span x-text="e.fecha_registro || '—'"></span></div>
                        <div class="col-span-2"><span class="nunito-bold">Horario:</span> <span x-text="e.horario_atencion || '—'"></span></div>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                        @perm(['Empresas','Gestión de Empresas','Gestion de Empresas'], 'actualizacion')
                        <button @click="openEmpresaModal(true, e)" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        @else
                        <button class="px-3 py-1 text-xs bg-gray-300 text-gray-600 rounded cursor-not-allowed flex items-center gap-1" disabled title="Sin permiso para editar">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        @endperm
                        @perm(['Empresas','Gestión de Empresas','Gestion de Empresas'], 'eliminacion')
                        <button @click="openDeleteEmpresaModal(e)" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                        @else
                        <button class="px-3 py-1 text-xs bg-red-600/50 text-white rounded cursor-not-allowed flex items-center gap-1" disabled title="Sin permiso para eliminar">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                        @endperm
                    </div>
                </div>
            </template>
        </x-slot>
    </x-responsive-table>

    <x-pagination />

    <x-admin.form-modal modalName="isEmpresaModalOpen" title="Empresa" submitLabel="Guardar Empresa"
        formId="empresa-form" maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nombre_comercial" class="block text-sm font-medium nunito-bold">Nombre Comercial <span
                        class="text-red-500">*</span></label>
                <input type="text" id="nombre_comercial"
                    class="mt-1 block w-full rounded-md shadow-sm border-gray-300 nunito-regular"
                    maxlength="100"
                    x-model="formEmpresa.nombre_comercial"
                    @input="formEmpresa._touched = formEmpresa._touched || {}; formEmpresa._touched.nombre_comercial = true"
                    @blur="formEmpresa._touched = formEmpresa._touched || {}; formEmpresa._touched.nombre_comercial = true"
                    :class="formEmpresa._touched && (!formEmpresa.nombre_comercial || formEmpresa.nombre_comercial.length >= 100) ? 'border-red-500' : ''"
                    required>
                <template x-if="errors.nombre_comercial">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.nombre_comercial[0]"></p>
                </template>
                <small x-show="!errors.nombre_comercial" class="text-xs text-gray-500 block mt-1" :class="formEmpresa._touched && (!formEmpresa.nombre_comercial || formEmpresa.nombre_comercial.length >= 100) ? 'text-red-500' : ''">Requerido. Máximo 100 caracteres.</small>
            </div>
            <div>
                <label for="razon_social" class="block text-sm font-medium nunito-bold">Razón Social</label>
                <input type="text" id="razon_social"
                    class="mt-1 block w-full rounded-md shadow-sm border-gray-300 nunito-regular"
                    maxlength="150"
                    x-model="formEmpresa.razon_social"
                    @input="formEmpresa._touched = formEmpresa._touched || {}; formEmpresa._touched.razon_social = true"
                    @blur="formEmpresa._touched = formEmpresa._touched || {}; formEmpresa._touched.razon_social = true"
                    :class="formEmpresa._touched && (formEmpresa.razon_social && formEmpresa.razon_social.length >= 150) ? 'border-red-500' : ''">
                <template x-if="errors.razon_social">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.razon_social[0]"></p>
                </template>
                <small x-show="!errors.razon_social" class="text-xs text-gray-500 block mt-1" :class="formEmpresa._touched && (formEmpresa.razon_social && formEmpresa.razon_social.length >= 150) ? 'text-red-500' : ''">Opcional. Máximo 150 caracteres.</small>
            </div>
            <div>
                <label for="rtn" class="block text-sm font-medium nunito-bold">Número de identificación fiscal</label>
                <input type="text" id="rtn"
                    class="mt-1 block w-full rounded-md shadow-sm border-gray-300 nunito-regular"
                    maxlength="30"
                    x-model="formEmpresa.rtn"
                    @input="formEmpresa._touched = formEmpresa._touched || {}; formEmpresa._touched.rtn = true"
                    @blur="formEmpresa._touched = formEmpresa._touched || {}; formEmpresa._touched.rtn = true"
                    :class="formEmpresa._touched && (formEmpresa.rtn && formEmpresa.rtn.length >= 30) ? 'border-red-500' : ''">
                <template x-if="errors.rtn">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.rtn[0]"></p>
                </template>
                <small x-show="!errors.rtn" class="text-xs text-gray-500 block mt-1" :class="formEmpresa._touched && (formEmpresa.rtn && formEmpresa.rtn.length >= 30) ? 'text-red-500' : ''">Opcional. Máximo 30 caracteres.</small>
            </div>
            <div class="md:col-span-2">
                <label for="descripcion_empresa" class="block text-sm font-medium nunito-bold">Descripción</label>
                <textarea id="descripcion_empresa"
                    class="mt-1 block w-full rounded-md shadow-sm border-gray-300 nunito-regular" rows="3"
                    maxlength="255" x-model="formEmpresa.descripcion_empresa"
                    @input="formEmpresa._touched = formEmpresa._touched || {}; formEmpresa._touched.descripcion_empresa = true"
                    @blur="formEmpresa._touched = formEmpresa._touched || {}; formEmpresa._touched.descripcion_empresa = true"
                    :class="formEmpresa._touched && (formEmpresa.descripcion_empresa && formEmpresa.descripcion_empresa.length >= 255) ? 'border-red-500' : ''"></textarea>
                <template x-if="errors.descripcion_empresa">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.descripcion_empresa[0]"></p>
                </template>
                <small x-show="!errors.descripcion_empresa" class="text-xs text-gray-500 block mt-1" :class="formEmpresa._touched && (formEmpresa.descripcion_empresa && formEmpresa.descripcion_empresa.length >= 255) ? 'text-red-500' : ''">Opcional. Máximo 255 caracteres.</small>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium nunito-bold">Horario de atención</label>
                <div class="mt-2 space-y-2">
                    <div class="flex flex-wrap gap-2 items-center">
                        <template x-for="d in diasLabels()" :key="d.k">
                            <label
                                class="inline-flex items-center gap-1 px-2 py-1 border rounded-md text-xs cursor-pointer bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600">
                                <input type="checkbox" class="rounded" :checked="horarioUI.dias[d.k]"
                                    @change="horarioUI.dias[d.k] = !horarioUI.dias[d.k]">
                                <span x-text="d.t"></span>
                            </label>
                        </template>
                        <button type="button" class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 rounded-md"
                            @click="setDias('lv')">Lun–Vie</button>
                        <button type="button" class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 rounded-md"
                            @click="setDias('todos')">Todos</button>
                        <button type="button" class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 rounded-md"
                            @click="setDias('ninguno')">Ninguno</button>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-xs text-gray-600 dark:text-gray-300">Hora:</label>
                        <input type="time" x-model="horarioUI.desde"
                            class="border rounded px-2 py-1 text-sm bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600" />
                        <span>–</span>
                        <input type="time" x-model="horarioUI.hasta"
                            class="border rounded px-2 py-1 text-sm bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600" />
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Horario: <span class="italic"
                            x-text="formEmpresa.horario_atencion || '—'"></span></p>
                    <input type="hidden" x-model="formEmpresa.horario_atencion">
                    <template x-if="errors.horario_atencion">
                        <p class="text-xs text-red-600" x-text="errors.horario_atencion[0]"></p>
                    </template>
                </div>
            </div>
            <div>
                <label for="fecha_registro" class="block text-sm font-medium nunito-bold">Fecha registro <span
                        class="text-red-500">*</span></label>
                <input type="date" id="fecha_registro"
                    class="mt-1 block w-full rounded-md shadow-sm border-gray-300 nunito-regular"
                    x-model="formEmpresa.fecha_registro"
                    @change="formEmpresa._touched = formEmpresa._touched || {}; formEmpresa._touched.fecha_registro = true"
                    @blur="formEmpresa._touched = formEmpresa._touched || {}; formEmpresa._touched.fecha_registro = true"
                    :max="new Date().toISOString().slice(0,10)"
                    :class="formEmpresa._touched && (!formEmpresa.fecha_registro || (formEmpresa.fecha_registro && new Date(formEmpresa.fecha_registro + 'T00:00:00') > new Date(new Date().setHours(0,0,0,0)))) ? 'border-red-500' : ''"
                    required>
                <template x-if="errors.fecha_registro">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_registro[0]"></p>
                </template>
                <small x-show="!errors.fecha_registro" class="text-xs text-gray-500 block mt-1" :class="formEmpresa._touched && !formEmpresa.fecha_registro ? 'text-red-500' : ''">Requerido. No se permiten fechas futuras.</small>
            </div>
            <div>
                <label for="estado_cliente" class="block text-sm font-medium nunito-bold">Estado <span
                        class="text-red-500">*</span></label>
                <select id="estado_cliente"
                    class="mt-1 block w-full rounded-md shadow-sm border-gray-300 nunito-regular"
                    x-model="formEmpresa.estado_cliente"
                    @change="formEmpresa._touched = formEmpresa._touched || {}; formEmpresa._touched.estado_cliente = true"
                    :class="formEmpresa._touched && !formEmpresa.estado_cliente ? 'border-red-500' : ''"
                    required>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>

            </div>
        </div>
    </x-admin.form-modal>

    <x-admin.confirmation-modal modal-name="isDeleteEmpresaModalOpen" title="Eliminar Empresa Cliente"
        item-to-delete="empresaToDelete" item-name-property="nombre_comercial"
        message="¿Estás seguro de que deseas eliminar la empresa cliente" />
</div>