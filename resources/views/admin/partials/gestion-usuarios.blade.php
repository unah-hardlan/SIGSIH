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
                'ordenarOptions' => [ 'nombre_usuario' => 'Nombre', 'usuario' => 'Usuario', 'correo_electronico' => 'Correo', 'estado_usuario' => 'Estado']
            ])
        </x-slot>

        <x-slot name="actions">
            <div class="flex flex-col sm:flex-row items-center gap-2">
                <button @click="openCreate()" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus"></i> Agregar Usuario
                </button>
                <button @click="openReporte()" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm flex items-center justify-center gap-2">
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
                        <tr><td colspan="6" class="py-8 text-center text-gray-500 nunito-regular"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando...</td></tr>
                    </template>
                    <template x-if="!loading && users.length === 0">
                        <tr><td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">Sin resultados</td></tr>
                    </template>
                    <template x-for="u in users" :key="u.id">
                        <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                            <td class="py-2 px-4" x-text="u.nombre_usuario"></td>
                            <td class="py-2 px-4" x-text="u.usuario"></td>
                            <td class="py-2 px-4" x-text="userRole(u)"></td>
                            <td class="py-2 px-4">
                                <span class="px-2 py-1 rounded text-xs font-semibold"
                                      :class="{
                                          'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': u.estado_usuario === 'ACTIVO',
                                          'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': u.estado_usuario === 'BLOQUEADO',
                                          'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': u.estado_usuario === 'INACTIVO'
                                      }"
                                      x-text="u.estado_usuario"></span>
                            </td>
                            <td class="py-2 px-4" x-text="u.fecha_creacion_formatted || u.fecha_creacion || '-' "></td>
                            <td class="py-2 px-4 flex gap-2">
                                <button @click="openEdit(u)" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                                <button @click="openInactivar(u)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </x-slot>
        
        <x-slot name="pagination">
            <div class="mt-4 flex items-center justify-between" x-show="pagination.total > 0">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Página <span x-text="pagination.page"></span> de <span x-text="pagination.last_page"></span>
                </div>
                <div class="flex gap-2">
                    <button class="px-3 py-1 text-sm border rounded hover:bg-gray-100 dark:hover:bg-gray-700" :disabled="pagination.page <= 1" @click="changePage(pagination.page - 1)">Anterior</button>
                    <button class="px-3 py-1 text-sm border rounded hover:bg-gray-100 dark:hover:bg-gray-700" :disabled="pagination.page >= pagination.last_page" @click="changePage(pagination.page + 1)">Siguiente</button>
                </div>
            </div>
            <div class="mt-2 text-red-500 text-sm" x-show="error" x-text="error"></div>
        </x-slot>

        <x-slot name="cards">
             <template x-if="loading">
                <div class="p-8 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando...</div>
            </template>
            <template x-if="!loading && users.length === 0">
                <div class="p-8 text-center text-gray-500">Sin resultados</div>
            </template>
            <template x-for="u in users" :key="u.id">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white" x-text="u.nombre_usuario"></h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="u.usuario"></p>
                        </div>
                        <span class="px-2 py-1 rounded text-xs font-semibold"
                              :class="{
                                  'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': u.estado_usuario === 'ACTIVO',
                                  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': u.estado_usuario === 'BLOQUEADO',
                                  'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': u.estado_usuario === 'INACTIVO'
                              }"
                              x-text="u.estado_usuario"></span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Rol: <span x-text="userRole(u)"></span></p>
                    <p class="text-xs text-gray-400">Creado: <span x-text="u.fecha_creacion_formatted || u.fecha_creacion || '-' "></span></p>
                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <button @click="openEdit(u)" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button @click="openInactivar(u)" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                            <i class="fas fa-trash"></i> Inactivar
                        </button>
                    </div>
                </div>
            </template>
        </x-slot>
    </x-responsive-table>

    <!-- Modales -->
    <div>
        <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Agregar Usuario" submitLabel="Guardar" formId="formCrear" maxWidth="max-w-xl">
             <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Contenido del modal de crear, sin cambios, ya que se vincula a tu objeto 'createForm' --}}
                <div><label class="block text-sm">Nombre</label><input type="text" x-model="createForm.nombre_usuario" class="mt-1 w-full border rounded px-2 py-1" required></div>
                <div><label class="block text-sm">Usuario</label><input type="text" x-model="createForm.usuario" class="mt-1 w-full border rounded px-2 py-1" required></div>
                <div class="sm:col-span-2"><label class="block text-sm">Correo</label><input type="email" x-model="createForm.correo_electronico" class="mt-1 w-full border rounded px-2 py-1" required></div>
                <div><label class="block text-sm">Estado</label><select x-model="createForm.estado_usuario" class="mt-1 w-full border rounded px-2 py-1"><option value="ACTIVO">ACTIVO</option><option value="INACTIVO">INACTIVO</option><option value="BLOQUEADO">BLOQUEADO</option></select></div>
                <div><label class="block text-sm">Rol</label><select x-model="createForm.id_rol_fk" required class="mt-1 w-full border rounded px-2 py-1"><option value="" disabled selected>Seleccione...</option><template x-for="r in roles" :key="r.id"><option :value="r.id" x-text="r.rol"></option></template></select></div>
                <div class="sm:col-span-2"><label class="block text-sm">Contraseña</label><input type="password" x-model="createForm.contrasena" minlength="8" class="mt-1 w-full border rounded px-2 py-1" required></div>
                <div class="sm:col-span-2 text-red-500 text-sm" x-show="formError" x-text="formError"></div>
            </div>
        </x-admin.form-modal>

        <x-admin.edit-modal class="nunito-bold" modalName="isEditUserModalOpen" title="Editar Usuario" itemToEdit="userToEdit" formId="formEditar" maxWidth="max-w-xl">
             <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                 {{-- Contenido del modal de editar, sin cambios, ya que se vincula a tu objeto 'editForm' --}}
                <div><label class="block text-sm">Nombre</label><input type="text" x-model="editForm.nombre_usuario" class="mt-1 w-full border rounded px-2 py-1" required></div>
                <div><label class="block text-sm">Usuario</label><input type="text" x-model="editForm.usuario" class="mt-1 w-full border rounded px-2 py-1 bg-gray-100" disabled></div>
                <div class="sm:col-span-2"><label class="block text-sm">Correo</label><input type="email" x-model="editForm.correo_electronico" class="mt-1 w-full border rounded px-2 py-1" required></div>
                <div><label class="block text-sm">Estado</label><select x-model="editForm.estado_usuario" class="mt-1 w-full border rounded px-2 py-1"><option value="ACTIVO">ACTIVO</option><option value="INACTIVO">INACTIVO</option><option value="BLOQUEADO">BLOQUEADO</option></select></div>
                <div><label class="block text-sm">Rol</label><select x-model="editForm.id_rol_fk" class="mt-1 w-full border rounded px-2 py-1"><template x-for="r in roles" :key="'er-'+r.id"><option :value="r.id" x-text="r.rol"></option></template></select></div>
                <div class="sm:col-span-2"><label class="block text-sm">Nueva Contraseña (opcional)</label><input type="password" x-model="editForm.contrasena" minlength="8" class="mt-1 w-full border rounded px-2 py-1" placeholder="Dejar en blanco"></div>
                <div class="sm:col-span-2 text-red-500 text-sm" x-show="formError" x-text="formError"></div>
            </div>
        </x-admin.edit-modal>

        <x-admin.confirmation-modal class="nunito-bold" modalName="showDeleteModal" title="Confirmar Inactivación" itemToDelete="userToInactivate" itemNameProperty="nombre_usuario" message="¿Seguro que deseas inactivar al usuario" />
    </div>
</div>