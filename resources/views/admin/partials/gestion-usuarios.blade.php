<div x-data="usuariosCrud" x-init="init()" class="p-4 space-y-4">
    <x-admin.tabla-crud :titulo="'Lista de Usuarios'">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
            'searchModel' => 'search',
            'filtrosSelect' => [
            'filtroPerfil' => [ 'label' => 'Estado', 'options' => ['ACTIVO','INACTIVO'] ]
            ],
            'ordenarOptions' => [ 'nombre_usuario' => 'Nombre', 'usuario' => 'Usuario', 'correo_electronico' =>
            'Correo', 'estado_usuario' => 'Estado']
            ])
        </x-slot>
        <x-slot name="boton">
            <div class="flex flex-col sm:flex-row sm:items-center gap-1.5">
                <button @click="openCreate()"
                    class="duration-200 ease-in-out w-full sm:w-auto h-10 sm:h-8 inline-flex items-center justify-center gap-1.5 px-4 rounded-md bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-medium text-xs tracking-wide transition focus:outline-none focus:ring-1 focus:ring-green-500">
                    <i class="fas fa-user-plus text-[11px]"></i>
                    <span>Agregar usuario</span>
                </button>
                <button @click="openReporte()"
                    class="duration-200 ease-in-out w-full sm:w-auto h-10 sm:h-8 inline-flex items-center justify-center gap-1.5 px-4 rounded-md bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-medium text-xs tracking-wide transition focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <i class="fas fa-file-alt text-[11px]"></i>
                    <span>Generar Reporte</span>
                </button>
            </div>
        </x-slot>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-2 px-4 text-left">Nombre</th>
                        <th class="py-2 px-4 text-left">Usuario</th>
                        <th class="py-2 px-4 text-left">Correo</th>
                        <th class="py-2 px-4 text-left">Estado</th>
                        <th class="py-2 px-4 text-left">Creado por</th>
                        <th class="py-2 px-4 text-left">Creación</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <tr>
                            <td colspan="7" class="py-4 text-center">Cargando...</td>
                        </tr>
                    </template>
                    <template x-if="!loading && users.length===0">
                        <tr>
                            <td colspan="7" class="py-4 text-center text-gray-500">Sin resultados</td>
                        </tr>
                    </template>
                    <template x-for="u in users" :key="u.id">
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-4" x-text="u.nombre_usuario"></td>
                            <td class="py-2 px-4" x-text="u.usuario"></td>
                            <td class="py-2 px-4" x-text="u.correo_electronico"></td>
                            <td class="py-2 px-4"><span
                                    :class="u.estado_usuario==='ACTIVO'?'bg-green-100 text-green-700':'bg-red-100 text-red-700'"
                                    class="px-2 py-1 rounded" x-text="u.estado_usuario"></span></td>
                            <td class="py-2 px-4" x-text="u.creado_por||'-'"></td>
                            <td class="py-2 px-4" x-text="u.fecha_creacion||'-'"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <button @click="openEdit(u)" class="text-blue-600 hover:text-blue-800"><i
                                        class="fas fa-edit"></i></button>
                                <button @click="openInactivar(u)" class="text-red-600 hover:text-red-800"><i
                                        class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="mt-3 flex items-center justify-between" x-show="pagination.total>0">
            <div class="text-xs">Página <span x-text="pagination.page"></span>/<span
                    x-text="pagination.last_page"></span> • Total <span x-text="pagination.total"></span></div>
            <div class="flex gap-2">
                <button class="px-2 py-1 border rounded" :disabled="pagination.page<=1"
                    @click="changePage(pagination.page-1)">Anterior</button>
                <button class="px-2 py-1 border rounded" :disabled="pagination.page>=pagination.last_page"
                    @click="changePage(pagination.page+1)">Siguiente</button>
            </div>
        </div>
        <div class="mt-2 text-red-600 text-sm" x-show="error" x-text="error"></div>
    </x-admin.tabla-crud>

    <!-- Crear -->
    <x-admin.form-modal modalName="isModalOpen" title="Agregar Usuario" submitLabel="Guardar" formId="formCrear">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-sm">Nombre</label><input type="text" x-model="createForm.nombre_usuario"
                    class="mt-1 w-full border rounded px-2 py-1" required></div>
            <div><label class="block text-sm">Usuario</label><input type="text" x-model="createForm.usuario"
                    class="mt-1 w-full border rounded px-2 py-1" required></div>
            <div><label class="block text-sm">Correo</label><input type="email" x-model="createForm.correo_electronico"
                    class="mt-1 w-full border rounded px-2 py-1" required></div>
            <div><label class="block text-sm">Estado</label><select x-model="createForm.estado_usuario"
                    class="mt-1 w-full border rounded px-2 py-1">
                    <option value="ACTIVO">ACTIVO</option>
                    <option value="INACTIVO">INACTIVO</option>
                </select></div>
            <div class="sm:col-span-2"><label class="block text-sm">Contraseña</label><input type="password"
                    x-model="createForm.contrasena" minlength="8" class="mt-1 w-full border rounded px-2 py-1" required>
            </div>
            <div class="sm:col-span-2 text-red-600 text-sm" x-show="formError" x-text="formError"></div>
        </div>
    </x-admin.form-modal>

    <!-- Editar -->
    <x-admin.edit-modal modalName="isEditUserModalOpen" title="Editar Usuario" itemToEdit="userToEdit"
        submitLabel="Actualizar" formId="formEditar">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-sm">Nombre</label><input type="text" x-model="editForm.nombre_usuario"
                    class="mt-1 w-full border rounded px-2 py-1" required></div>
            <div><label class="block text-sm">Usuario</label><input type="text" x-model="editForm.usuario"
                    class="mt-1 w-full border rounded px-2 py-1 bg-gray-100" disabled></div>
            <div><label class="block text-sm">Correo</label><input type="email" x-model="editForm.correo_electronico"
                    class="mt-1 w-full border rounded px-2 py-1" required></div>
            <div><label class="block text-sm">Estado</label><select x-model="editForm.estado_usuario"
                    class="mt-1 w-full border rounded px-2 py-1">
                    <option value="ACTIVO">ACTIVO</option>
                    <option value="INACTIVO">INACTIVO</option>
                </select></div>
            <div class="sm:col-span-2"><label class="block text-sm">Nueva Contraseña (opcional)</label><input
                    type="password" x-model="editForm.contrasena" minlength="8"
                    class="mt-1 w-full border rounded px-2 py-1" placeholder="Dejar en blanco"></div>
            <div class="sm:col-span-2 text-red-600 text-sm" x-show="formError" x-text="formError"></div>
        </div>
    </x-admin.edit-modal>

    <!-- Confirmar inactivación -->
    <x-admin.confirmation-modal modalName="showDeleteModal" title="Confirmar" itemToDelete="userToInactivate"
        itemNameProperty="nombre_usuario" message="¿Seguro que deseas inactivar al usuario" />
</div>