<div x-data="usuariosCrud" x-init="init()" class="bg-white dark:bg-gray-900 rounded-xl shadow-lg">

    <div class="p-4 md:p-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold">Catálogo de Usuarios</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'search',
            'filtrosSelect' => [
            'filtroPerfil' => [ 'label' => 'Estado', 'options' => ['ACTIVO','INACTIVO','BLOQUEADO'] ]
            ],
            'ordenarOptions' => [ 'nombre' => 'Nombre', 'usuario' => 'Usuario', 'correo_electronico' =>
            'Correo', 'estado_usuario' => 'Estado']
            ])
        </x-slot>

        <x-slot name="actions">
            <div class="flex flex-col sm:flex-row items-center gap-2">
                @perm(['Usuarios','Usuario','Catálogo de Usuarios','Catalogo de Usuarios','Gestión de Usuarios','Gestion
                de Usuarios'],'insercion')
                <button @click="openCreate()"
                    class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus"></i> Agregar Usuario
                </button>
                @else
                <button disabled title="Sin permiso para crear"
                    class="w-full sm:w-auto bg-green-600 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm flex items-center justify-center gap-2 opacity-60 cursor-not-allowed">
                    <i class="fas fa-user-plus"></i> Agregar Usuario
                </button>
                @endperm
                <button @click="openReporte()"
                    class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-file-alt"></i> Generar Reporte
                </button>
            </div>
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left border-0">Nombre</th>
                        <th class="py-2 px-4 text-left border-0">Usuario</th>
                        <th class="py-2 px-4 text-left border-0">Rol</th>
                        <th class="py-2 px-4 text-left border-0">Estado</th>
                        <th class="py-2 px-4 text-left border-0">Creación</th>
                        <th class="py-2 px-4 text-left border-0">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular"><i
                                    class="fas fa-spinner fa-spin mr-2"></i> Cargando...</td>
                        </tr>
                    </template>
                    <template x-if="!loading && users.length === 0">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">Sin resultados</td>
                        </tr>
                    </template>
                    <template x-for="u in paginatedUsuarios()" :key="u.id">
                        <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                            <td class="py-2 px-4" x-text="userName(u)"></td>
                            <td class="py-2 px-4" x-text="u.usuario"></td>
                            <td class="py-2 px-4" x-text="userRole(u)"></td>
                            <td class="py-2 px-4">
                                <span class="px-2 py-1 rounded text-xs font-semibold" :class="{
                                          'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': u.estado_usuario === 'ACTIVO',
                                          'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': u.estado_usuario === 'BLOQUEADO',
                                          'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': u.estado_usuario === 'INACTIVO'
                                      }" x-text="u.estado_usuario"></span>
                            </td>
                            <td class="py-2 px-4" x-text="u.fecha_creacion_formatted || u.fecha_creacion || '-' "></td>
                            <td class="py-2 px-4 flex gap-2">
                                @perm(['Usuarios','Usuario','Catálogo de Usuarios','Catalogo de Usuarios','Gestión de
                                Usuarios','Gestion de Usuarios'],'actualizacion')
                                <button @click="openEdit(u)" class="text-blue-500 hover:text-blue-700"><i
                                        class="fas fa-edit"></i></button>
                                @else
                                <span title="Sin permiso para editar" class="text-blue-300 cursor-not-allowed"><i
                                        class="fas fa-edit"></i></span>
                                @endperm
                                @perm(['Usuarios','Usuario','Catálogo de Usuarios','Catalogo de Usuarios','Gestión de
                                Usuarios','Gestion de Usuarios'],'eliminacion')
                                <button @click="openInactivar(u)" class="text-red-500 hover:text-red-700"><i
                                        class="fas fa-trash"></i></button>
                                @else
                                <span title="Sin permiso para inactivar" class="text-red-300 cursor-not-allowed"><i
                                        class="fas fa-trash"></i></span>
                                @endperm
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="pagination">
            <div class="mt-2 text-red-500 text-sm" x-show="error" x-text="error"></div>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loading">
                <div class="p-8 text-center text-gray-500 dark:text-gray-400"><i
                        class="fas fa-spinner fa-spin mr-2"></i> Cargando...</div>
            </template>
            <template x-if="!loading && users.length === 0">
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">Sin resultados</div>
            </template>
            <template x-for="u in paginatedUsuarios()" :key="u.id">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3 border border-black dark:border-gray-600">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white" x-text="userName(u)"></h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="u.usuario"></p>
                        </div>
                        <span class="px-2 py-1 rounded text-xs font-semibold" :class="{
                                  'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': u.estado_usuario === 'ACTIVO',
                                  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': u.estado_usuario === 'BLOQUEADO',
                                  'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': u.estado_usuario === 'INACTIVO'
                              }" x-text="u.estado_usuario"></span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Rol: <span x-text="userRole(u)"></span></p>
                    <p class="text-xs text-gray-400">Creado: <span
                            x-text="u.fecha_creacion_formatted || u.fecha_creacion || '-' "></span></p>
                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                        @perm(['Usuarios','Usuario','Catálogo de Usuarios','Catalogo de Usuarios','Gestión de
                        Usuarios','Gestion de Usuarios'],'actualizacion')
                        <button @click="openEdit(u)"
                            class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        @else
                        <button disabled title="Sin permiso para editar"
                            class="px-3 py-1 text-xs bg-blue-600 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        @endperm
                        @perm(['Usuarios','Usuario','Catálogo de Usuarios','Catalogo de Usuarios','Gestión de
                        Usuarios','Gestion de Usuarios'],'eliminacion')
                        <button @click="openInactivar(u)"
                            class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                            <i class="fas fa-trash"></i> Inactivar
                        </button>
                        @else
                        <button disabled title="Sin permiso para inactivar"
                            class="px-3 py-1 text-xs bg-red-600 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1">
                            <i class="fas fa-trash"></i> Inactivar
                        </button>
                        @endperm
                    </div>
                </div>
            </template>
        </x-slot>
    </x-responsive-table>

    <div x-show="numbers.length > perPage"
        class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <div class="mb-2">
            <span
                class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white"
                    x-text="(currentPage - 1) * perPage + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white"
                    x-text="Math.min(currentPage * perPage, numbers.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="numbers.length"></strong>
                resultados
            </span>
        </div>
        <div
            class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPage()" :disabled="currentPage === 1"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Anterior</span>
            </button>
            <div class="flex items-center gap-1">
                <template
                    x-for="page in Array.from({length: totalPages()}, (_, i) => i + 1).slice(Math.max(0, currentPage - 3), currentPage + 2)"
                    :key="page">
                    <button @click="goToPage(page)"
                        class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-blue-900 hover:text-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        :class="page === currentPage ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>
            <button @click="nextPage()" :disabled="currentPage === totalPages()"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <div>
        <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Agregar Usuario" submitLabel="Guardar"
            formId="formCrear" maxWidth="max-w-xl">
            @perm(['Usuarios','Usuario','Catálogo de Usuarios','Catalogo de Usuarios','Gestión de Usuarios','Gestion de
            Usuarios'],'insercion')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm">Usuario</label>
                    <input type="text" x-model="createForm.usuario" @click="$event.target.select()"
                        @focus="$event.target.select()" @mouseup.prevent
                        @blur="createForm._touched = createForm._touched || {}; createForm._touched.usuario = true"
                        @input="createForm._touched = createForm._touched || {}; createForm._touched.usuario = true"
                        :class="{'border-red-500': (createForm._touched && createForm._touched.usuario) && (createForm.usuario === '' || createForm.usuario.length >= 50 || !/^[A-Z0-9_]+$/.test(createForm.usuario))}"
                        class="mt-1 w-full border rounded px-2 py-1" required maxlength="50" autocomplete="off">
                    <small
                        :class="(createForm._touched && createForm._touched.usuario) && (createForm.usuario === '' || createForm.usuario.length >= 50 || !/^[A-Z0-9_]+$/.test(createForm.usuario)) ? 'text-red-500' : 'text-gray-500 dark:text-white'"
                        class="text-xs">Requerido. Solo mayúsculas, números y guiones bajos. Máximo 50 caracteres. Debe
                        ser único.</small>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm">Correo</label>
                    <input type="email" x-model="createForm.correo_electronico" @click="$event.target.select()"
                        @focus="$event.target.select()" @mouseup.prevent
                        @blur="createForm._touched = createForm._touched || {}; createForm._touched.correo_electronico = true"
                        @input="createForm._touched = createForm._touched || {}; createForm._touched.correo_electronico = true"
                        :class="{'border-red-500': (createForm._touched && createForm._touched.correo_electronico) && (createForm.correo_electronico === '' || createForm.correo_electronico.length >= 100 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(createForm.correo_electronico))}"
                        class="mt-1 w-full border rounded px-2 py-1" required maxlength="100" autocomplete="off">
                    <small
                        :class="(createForm._touched && createForm._touched.correo_electronico) && (createForm.correo_electronico === '' || createForm.correo_electronico.length >= 100 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(createForm.correo_electronico)) ? 'text-red-500' : 'text-gray-500 dark:text-white'"
                        class="text-xs">Requerido. Formato de email válido. Máximo 100 caracteres. Debe ser
                        único.</small>
                </div>
                <div>
                    <label class="block text-sm">Estado</label>
                    <select x-model="createForm.estado_usuario"
                        @change="createForm._touched = createForm._touched || {}; createForm._touched.estado_usuario = true"
                        :class="{'border-red-500': (createForm._touched && createForm._touched.estado_usuario) && createForm.estado_usuario && createForm.estado_usuario.length >= 20}"
                        class="mt-1 w-full border rounded px-2 py-1">
                        <option value="ACTIVO">ACTIVO</option>
                        <option value="INACTIVO">INACTIVO</option>
                        <option value="BLOQUEADO">BLOQUEADO</option>
                    </select>
                    <small
                        :class="(createForm._touched && createForm._touched.estado_usuario) && createForm.estado_usuario && createForm.estado_usuario.length >= 20 ? 'text-red-500' : 'text-gray-500 dark:text-white'"
                        class="text-xs">Opcional. Máximo 20 caracteres.</small>
                </div>
                <div>
                    <label class="block text-sm">Rol</label>
                    <select x-model="createForm.id_rol_fk"
                        @change="createForm._touched = createForm._touched || {}; createForm._touched.id_rol_fk = true"
                        required
                        :class="{'border-red-500': (createForm._touched && createForm._touched.id_rol_fk) && !createForm.id_rol_fk}"
                        class="mt-1 w-full border rounded px-2 py-1">
                        <option value="" disabled selected>Seleccione...</option>
                        <template x-for="r in roles" :key="r.id">
                            <option :value="r.id" x-text="r.rol"></option>
                        </template>
                    </select>
                    <small
                        :class="(createForm._touched && createForm._touched.id_rol_fk) && !createForm.id_rol_fk ? 'text-red-500' : 'text-gray-500 dark:text-white'"
                        class="text-xs">Requerido. Debe seleccionar un rol válido.</small>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm">Contraseña</label>
                    <input type="password" x-model="createForm.contrasena"
                        @blur="createForm._touched = createForm._touched || {}; createForm._touched.contrasena = true"
                        @input="createForm._touched = createForm._touched || {}; createForm._touched.contrasena = true"
                        :class="{'border-red-500': (createForm._touched && createForm._touched.contrasena) && (createForm.contrasena === '' || createForm.contrasena.length < 8 || createForm.contrasena.length >= 100 || !(window.getAdminPasswordRegex ? window.getAdminPasswordRegex().test(createForm.contrasena) : /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/.test(createForm.contrasena))) }"
                        minlength="8" maxlength="100" class="mt-1 w-full border rounded px-2 py-1" required
                        autocomplete="off">
                    <small
                        :class="(createForm._touched && createForm._touched.contrasena) && (createForm.contrasena === '' || createForm.contrasena.length < 8 || createForm.contrasena.length >= 100 || !(window.getAdminPasswordRegex ? window.getAdminPasswordRegex().test(createForm.contrasena) : /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/.test(createForm.contrasena))) ? 'text-red-500' : 'text-gray-500 dark:text-white'"
                        class="text-xs">Requerido. Debe cumplir la estructura definida en Mantenimiento → Parámetros
                        (fallback: mínimo 8 caracteres, incluir mayúsculas, minúsculas, números y símbolos).</small>
                </div>
                <div class="sm:col-span-2 text-red-500 text-sm" x-show="formError" x-text="formError"></div>
            </div>
            @else
            <div class="p-4 text-center text-sm text-gray-600 dark:text-gray-300">Sin permiso para crear usuarios.</div>
            @endperm
        </x-admin.form-modal>

        <x-admin.edit-modal class="nunito-bold" modalName="isEditUserModalOpen" title="Editar Usuario"
            itemToEdit="userToEdit" formId="formEditar" maxWidth="max-w-xl">
            @perm(['Usuarios','Usuario','Catálogo de Usuarios','Catalogo de Usuarios','Gestión de Usuarios','Gestion de
            Usuarios'],'actualizacion')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm">Usuario</label>
                    <input type="text" x-model="editForm.usuario"
                        class="mt-1 w-full border rounded px-2 py-1 bg-gray-100" disabled>
                    <small
                        :class="(editForm._touched && editForm._touched.usuario) && (editForm.usuario === '' || editForm.usuario.length >= 50 || !/^[A-Z0-9_]+$/.test(editForm.usuario)) ? 'text-red-500' : 'text-gray-500 dark:text-white'"
                        class="text-xs">Solo mayúsculas, números y guiones bajos. Máximo 50 caracteres. Debe ser
                        único.</small>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm">Correo</label>
                    <input type="email" x-model="editForm.correo_electronico" @click="$event.target.select()"
                        @focus="$event.target.select()" @mouseup.prevent
                        @blur="editForm._touched = editForm._touched || {}; editForm._touched.correo_electronico = true"
                        @input="editForm._touched = editForm._touched || {}; editForm._touched.correo_electronico = true"
                        :class="{'border-red-500': (editForm._touched && editForm._touched.correo_electronico) && (editForm.correo_electronico === '' || editForm.correo_electronico.length >= 100 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(editForm.correo_electronico))}"
                        class="mt-1 w-full border rounded px-2 py-1" required maxlength="100">
                    <small
                        :class="(editForm._touched && editForm._touched.correo_electronico) && (editForm.correo_electronico === '' || editForm.correo_electronico.length >= 100 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(editForm.correo_electronico)) ? 'text-red-500' : 'text-gray-500 dark:text-white'"
                        class="text-xs">Requerido. Formato de email válido. Máximo 100 caracteres. Debe ser
                        único.</small>
                </div>
                <div>
                    <label class="block text-sm">Estado</label>
                    <select x-model="editForm.estado_usuario"
                        @change="editForm._touched = editForm._touched || {}; editForm._touched.estado_usuario = true"
                        :class="{'border-red-500': (editForm._touched && editForm._touched.estado_usuario) && editForm.estado_usuario && editForm.estado_usuario.length >= 20}"
                        class="mt-1 w-full border rounded px-2 py-1">
                        <option value="ACTIVO">ACTIVO</option>
                        <option value="INACTIVO">INACTIVO</option>
                        <option value="BLOQUEADO">BLOQUEADO</option>
                    </select>
                    <small
                        :class="(editForm._touched && editForm._touched.estado_usuario) && editForm.estado_usuario && editForm.estado_usuario.length >= 20 ? 'text-red-500' : 'text-gray-500 dark:text-white'"
                        class="text-xs">Opcional. Máximo 20 caracteres.</small>
                </div>
                <div>
                    <label class="block text-sm">Rol</label>
                    <select x-model="editForm.id_rol_fk"
                        @change="editForm._touched = editForm._touched || {}; editForm._touched.id_rol_fk = true"
                        :class="{'border-red-500': (editForm._touched && editForm._touched.id_rol_fk) && !editForm.id_rol_fk}"
                        class="mt-1 w-full border rounded px-2 py-1">
                        <template x-for="r in roles" :key="'er-'+r.id">
                            <option :value="r.id" x-text="r.rol"></option>
                        </template>
                    </select>
                    <small
                        :class="(editForm._touched && editForm._touched.id_rol_fk) && !editForm.id_rol_fk ? 'text-red-500' : 'text-gray-500 dark:text-white'"
                        class="text-xs">Opcional. Debe seleccionar un rol válido.</small>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm">Nueva Contraseña (opcional)</label>
                    <input type="password" x-model="editForm.contrasena"
                        @blur="editForm._touched = editForm._touched || {}; editForm._touched.contrasena = true"
                        @input="editForm._touched = editForm._touched || {}; editForm._touched.contrasena = true"
                        :class="{'border-red-500': (editForm._touched && editForm._touched.contrasena) && (editForm.contrasena && (editForm.contrasena.length < 8 || editForm.contrasena.length >= 100 || !(window.getAdminPasswordRegex ? window.getAdminPasswordRegex().test(editForm.contrasena) : /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/.test(editForm.contrasena))) )}"
                        minlength="8" maxlength="100" class="mt-1 w-full border rounded px-2 py-1"
                        placeholder="Dejar en blanco">
                    <small
                        :class="(editForm._touched && editForm._touched.contrasena) && (editForm.contrasena && (editForm.contrasena.length < 8 || editForm.contrasena.length >= 100 || !(window.getAdminPasswordRegex ? window.getAdminPasswordRegex().test(editForm.contrasena) : /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/.test(editForm.contrasena)))) ? 'text-red-500' : 'text-gray-500 dark:text-white'"
                        class="text-xs">Opcional. Si se proporciona, debe cumplir la estructura definida en
                        Mantenimiento → Parámetros (fallback: mínimo 8 caracteres, incluir mayúsculas, minúsculas,
                        números y símbolos).</small>
                </div>
                <div class="sm:col-span-2 text-red-500 text-sm" x-show="formError" x-text="formError"></div>
            </div>
            @else
            <div class="p-4 text-center text-sm text-gray-600 dark:text-gray-300">Sin permiso para editar usuarios.
            </div>
            @endperm
        </x-admin.edit-modal>

        @perm(['Usuarios','Usuario','Catálogo de Usuarios','Catalogo de Usuarios','Gestión de Usuarios','Gestion de
        Usuarios'],'eliminacion')
        <x-admin.confirmation-modal class="nunito-bold" modalName="showDeleteModal" title="Confirmar Inactivación"
            itemToDelete="userToInactivate" itemNameProperty="nombre"
            message="¿Seguro que deseas inactivar al usuario" />
        @endperm
    </div>


    <script>
        window.getAdminPasswordRegex = function() {
                if (window._adminPasswordRegex) return window._adminPasswordRegex;

                function buildRegexFromValue(v) {
                    if (!v) return null;
                    v = v.toString().trim();
                    if (v.length > 2 && v.charAt(0) === '/' && v.charAt(v.length - 1) === '/') {
                        try {
                            return new RegExp(v.slice(1, -1));
                        } catch (e) {}
                    }
                    if (/[\\^\[\]()+*?.|]/.test(v)) {
                        try {
                            return new RegExp(v);
                        } catch (e) {}
                    }
                    var lookaheads = [];
                    if (/[a-z]/.test(v)) lookaheads.push('(?=.*[a-z])');
                    if (/[A-Z]/.test(v)) lookaheads.push('(?=.*[A-Z])');
                    if (/\d/.test(v)) lookaheads.push('(?=.*\\d)');
                    if (/[^A-Za-z0-9]/.test(v)) lookaheads.push('(?=.*[^A-Za-z0-9])');
                    if (lookaheads.length > 0) {
                        var minLen = Math.max(8, v.length);
                        var pattern = '^' + lookaheads.join('') + '.{' + minLen + ',100}$';
                        try {
                            return new RegExp(pattern);
                        } catch (e) {}
                    }
                    return null;