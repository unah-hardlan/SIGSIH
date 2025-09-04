<div x-data="usuariosCrud" x-init="init()" class="p-4 space-y-4 bg-white dark:bg-gray-900 rounded-lg shadow">
    <x-admin.tabla-crud class="nunito-bold" :titulo="'Lista de Usuarios'">
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
                    <span class="nunito-regular text-sm">Agregar usuario</span>
                </button>
                <button @click="openReporte()"
                    class="duration-200 ease-in-out w-full sm:w-auto h-10 sm:h-8 inline-flex items-center justify-center gap-1.5 px-4 rounded-md bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-medium text-xs tracking-wide transition focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <i class="fas fa-file-alt text-[11px]"></i>
                    <span class="nunito-regular text-sm">Generar Reporte</span>
                </button>
            </div>
        </x-slot>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <th class="py-2 px-4 text-left nunito-bold dark:text-white">Nombre</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-white">Usuario</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-white">Correo</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-white">Estado</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-white">Creado por</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-white">Creación</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-white">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <tr>
                            <td colspan="7" class="py-4 text-center nunito-regular">Cargando...</td>
                        </tr>
                    </template>
                    <template x-if="!loading && users.length===0">
                        <tr>
                            <td colspan="7" class="py-4 text-center text-gray-500 nunito-regular">Sin resultados</td>
                        </tr>
                    </template>
                    <template x-for="u in users" :key="u.id">
                        <tr class="border-b dark:border-gray-700 nunito-regular">
                            <td class="py-2 px-4 nunito-regular dark:text-white" x-text="u.nombre_usuario"></td>
                            <td class="py-2 px-4 nunito-regular dark:text-white" x-text="u.usuario"></td>
                            <td class="py-2 px-4 nunito-regular dark:text-white" x-text="u.correo_electronico"></td>
                            <td class="py-2 px-4">
                                <span :class="u.estado_usuario==='ACTIVO' ? 'bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200' : 'bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-200'" class="px-2 py-1 rounded nunito-regular" x-text="u.estado_usuario"></span>
                            </td>
                            <td class="py-2 px-4 nunito-regular dark:text-white" x-text="u.creado_por||'-'"></td>
                            <td class="py-2 px-4 nunito-regular dark:text-white" x-text="u.fecha_creacion_formatted || u.fecha_creacion || '-' "></td>
                            <td class="py-2 px-4 flex gap-2">
                                <button @click="openEdit(u)" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></button>
                                <button @click="openInactivar(u)" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="mt-3 flex items-center justify-between" x-show="pagination.total>0">
            <div class="text-xs nunito-regular">Página <span x-text="pagination.page"></span>/<span
                    x-text="pagination.last_page"></span> • Total <span x-text="pagination.total"></span></div>
            <div class="flex gap-2">
                <button class="px-2 py-1 border rounded nunito-regular" :disabled="pagination.page<=1"
                    @click="changePage(pagination.page-1)">Anterior</button>
                <button class="px-2 py-1 border rounded nunito-regular" :disabled="pagination.page>=pagination.last_page"
                    @click="changePage(pagination.page+1)">Siguiente</button>
            </div>
        </div>
        <div class="mt-2 text-red-600 text-sm nunito-regular" x-show="error" x-text="error"></div>
    </x-admin.tabla-crud>

    <!-- Crear -->
    <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Agregar Usuario" submitLabel="Guardar" formId="formCrear">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><label class="block text-sm nunito-bold dark:text-white">Nombre</label><input type="text" x-model="createForm.nombre_usuario"
            class="mt-1 w-full border rounded px-2 py-1 nunito-regular dark:bg-gray-700 dark:text-white dark:border-gray-600" required></div>
        <div><label class="block text-sm nunito-bold dark:text-white">Usuario</label><input type="text" x-model="createForm.usuario"
            class="mt-1 w-full border rounded px-2 py-1 nunito-regular dark:bg-gray-700 dark:text-white dark:border-gray-600" required></div>
        <div><label class="block text-sm nunito-bold dark:text-white">Correo</label><input type="email" x-model="createForm.correo_electronico"
            class="mt-1 w-full border rounded px-2 py-1 nunito-regular dark:bg-gray-700 dark:text-white dark:border-gray-600" required></div>
        <div><label class="block text-sm nunito-bold dark:text-white">Estado</label><select x-model="createForm.estado_usuario"
            class="mt-1 w-full border rounded px-2 py-1 nunito-regular dark:bg-gray-700 dark:text-white dark:border-gray-600">
            <option value="ACTIVO">ACTIVO</option>
            <option value="INACTIVO">INACTIVO</option>
        </select></div>
        <div class="sm:col-span-2"><label class="block text-sm nunito-bold dark:text-white">Contraseña</label><input type="password"
            x-model="createForm.contrasena" minlength="8" class="mt-1 w-full border rounded px-2 py-1 nunito-regular dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
        </div>
        <div class="sm:col-span-2 text-red-600 text-sm nunito-regular" x-show="formError" x-text="formError"></div>
    </div>
    </x-admin.form-modal>

    <!-- Editar -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditUserModalOpen" title="Editar Usuario" itemToEdit="userToEdit"
    submitLabel="Actualizar" formId="formEditar">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><label class="block text-sm nunito-bold dark:text-white">Nombre</label><input type="text" x-model="editForm.nombre_usuario"
            class="mt-1 w-full border rounded px-2 py-1 nunito-regular dark:bg-gray-700 dark:text-white dark:border-gray-600" required></div>
        <div><label class="block text-sm nunito-bold dark:text-white">Usuario</label><input type="text" x-model="editForm.usuario"
            class="mt-1 w-full border rounded px-2 py-1 bg-gray-100 dark:bg-gray-700 dark:text-white dark:border-gray-600 nunito-regular" disabled></div>
        <div><label class="block text-sm nunito-bold dark:text-white">Correo</label><input type="email" x-model="editForm.correo_electronico"
            class="mt-1 w-full border rounded px-2 py-1 nunito-regular dark:bg-gray-700 dark:text-white dark:border-gray-600" required></div>
        <div><label class="block text-sm nunito-bold dark:text-white">Estado</label><select x-model="editForm.estado_usuario"
            class="mt-1 w-full border rounded px-2 py-1 nunito-regular dark:bg-gray-700 dark:text-white dark:border-gray-600">
            <option value="ACTIVO">ACTIVO</option>
            <option value="INACTIVO">INACTIVO</option>
        </select></div>
        <div class="sm:col-span-2"><label class="block text-sm nunito-bold dark:text-white">Nueva Contraseña (opcional)</label><input
            type="password" x-model="editForm.contrasena" minlength="8"
            class="mt-1 w-full border rounded px-2 py-1 nunito-regular dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Dejar en blanco"></div>
        <div class="sm:col-span-2 text-red-600 text-sm nunito-regular" x-show="formError" x-text="formError"></div>
    </div>
    </x-admin.edit-modal>

    <!-- Confirmar inactivación -->
    <x-admin.confirmation-modal class="nunito-bold" modalName="showDeleteModal" title="Confirmar" itemToDelete="userToInactivate"
        itemNameProperty="nombre_usuario" message="¿Seguro que deseas inactivar al usuario" />
</div>