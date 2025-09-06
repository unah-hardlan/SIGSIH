<div x-data="{
    tab: 'gestion',
    // Modales roles y permisos
    isModalOpen: false,
    isEditRoleModalOpen: false,
    isDeleteRoleModalOpen: false,
    roleToEdit: {rol: '', descripcion_rol: '', permisos: [], objeto: '', usuario: '', creado_por: '', fecha_creacion: ''},
    roleToDelete: {rol: '', descripcion_rol: '', permisos: [], objeto: '', usuario: '', creado_por: '', fecha_creacion: ''},
    // Modales objetos
    isObjetoModalOpen: false,
    isEditObjetoModalOpen: false,
    isDeleteObjetoModalOpen: false,
    objetoToEdit: {nombre: '', descripcion: '', tipo: '', creado_por: '', fecha: ''},
    objetoToDelete: {nombre: '', descripcion: '', tipo: '', creado_por: '', fecha: ''},
    // Variables para filtros-generales
    search: '',
    searchObjetos: '',
    ordenarPor: ''
}" @include('partials.persist-tab', ['tabKey' => 'admin-configuracion-acceso-tab'])>
    <!-- Tabs -->
<div class="flex border-b mb-6 flex-wrap gap-2">
    <button @click="tab = 'gestion'"
        :class="tab === 'gestion' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 dark:text-gray-200'"
        class="px-4 py-2 font-semibold focus:outline-none nunito-bold">Gestión de Permisos</button>
    <button @click="tab = 'crear'"
        :class="tab === 'crear' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 dark:text-gray-200'"
        class="px-4 py-2 font-semibold focus:outline-none nunito-bold">Roles</button>
    <button @click="tab = 'asignar'"
        :class="tab === 'asignar' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 dark:text-gray-200'"
        class="px-4 py-2 font-semibold focus:outline-none nunito-bold">Asignar a Usuarios</button>
    <button @click="tab = 'objetos'"
        :class="tab === 'objetos' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 dark:text-gray-200'"
        class="px-4 py-2 font-semibold focus:outline-none nunito-bold">Objetos</button>
</div>
    <!-- TAB: Gestión de Roles y Permisos -->
    <div x-show="tab === 'gestion'" x-data="{ ready: false }" x-init="$store.access.init(); ready = true;">
        <x-admin.tabla-crud class="nunito-bold" :titulo="'Gestión de Permisos'">
            <x-slot name="filtros">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="text-sm text-red-600" x-text="$store.access.error"></div>
                </div>
            </x-slot>
            <x-slot name="boton">
                <div class="w-full flex justify-end gap-2">
                    <button type="button" @click.prevent="(() => { const p=new URLSearchParams(); p.set('modulo','configuracion-acceso'); p.set('seccion','gestion'); const sel=$store.access.selectedRoleId; if(sel){ p.set('rol_id', sel); const rr=$store.access.roles.find(r=>r.id===sel); if(rr?.rol) p.set('rol', rr.rol); } const url=`/admin/reportes-header?${p.toString()}`; window.open(url,'_blank'); })()"
                        class="duration-200 ease-in-out bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2">
                        <i class="fas fa-file-alt"></i> <span class="nunito-regular text-sm">Generar Reporte</span>
                    </button>
                </div>
            </x-slot>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <!-- Roles -->
                <div class="md:col-span-1 bg-white rounded-2xl shadow-lg ring-1 ring-gray-200 p-4" x-data="{ roleQ: '' }">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-800">Roles</h3>
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600" x-text="$store.access.roles.length + ' totales'"></span>
                    </div>
                    <div class="relative mb-3">
                        <input type="text" x-model="roleQ" class="w-full border rounded-lg px-3 py-2 pr-9 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Buscar rol..." />
                        <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    </div>
                    <ul class="space-y-1 max-h-[420px] overflow-auto pr-1">
                        <template x-for="r in $store.access.roles.filter(rr => !roleQ || (rr.rol||'').toLowerCase().includes(roleQ.toLowerCase()))" :key="r.id">
                            <li>
                                <button class="w-full text-left px-3 py-2 rounded-lg transition flex items-center gap-2 hover:bg-gray-50"
                                    :class="$store.access.selectedRoleId===r.id ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-200' : 'text-gray-700'"
                                    @click="$store.access.selectRole(r.id)">
                                    <i class="fas fa-user-shield"></i>
                                    <span class="truncate" x-text="r.rol"></span>
                                </button>
                            </li>
                        </template>
                    </ul>
                </div>
                <!-- Matriz -->
                <div class="md:col-span-3 bg-white rounded-2xl shadow-lg ring-1 ring-gray-200 p-4 overflow-auto" x-data="{ objQ: '' }">
                    <template x-if="!$store.access.selectedRoleId">
                        <div class="text-gray-500">Selecciona un rol para configurar sus permisos.</div>
                    </template>
                    <template x-if="$store.access.selectedRoleId">
                        <div>
                            <div class="flex items-center justify-between mb-3 gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500">Rol:</span>
                                    <span class="inline-flex items-center gap-1 text-sm px-2.5 py-1 rounded-full bg-blue-50 text-blue-700">
                                        <i class="fas fa-shield-alt"></i>
                                        <span x-text="($store.access.roles.find(r=>r.id===$store.access.selectedRoleId)?.rol)||'—'"></span>
                                    </span>
                                </div>
                                <div class="relative w-64 max-w-full">
                                    <input type="text" x-model="objQ" class="w-full border rounded-lg px-3 py-2 pr-9 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Filtrar objetos..." />
                                    <i class="fas fa-filter absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            <div class="overflow-auto max-h-[600px]">
                                <!-- Grupos por módulo (tipo de objeto) -->
                                <template x-for="g in $store.access.grupos()" :key="'grp-'+g.id">
                                    <div class="mb-5 border rounded-xl overflow-hidden">
                                        <!-- Encabezado de módulo -->
                                        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-folder text-gray-500"></i>
                                                <h4 class="font-semibold text-gray-800" x-text="g.nombre"></h4>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="text-xs text-gray-500" x-text="(g.objetos||[]).length + ' submódulos'"></span>
                                                <label class="inline-flex items-center gap-2 text-sm">
                                                    <span>Acceso</span>
                                                    <button type="button" @click.prevent="$store.access.toggleModulo(g.id, !$store.access.moduloTieneAcceso(g.id))"
                                                        class="h-6 w-10 rounded-full border transition relative"
                                                        :class="$store.access.moduloTieneAcceso(g.id) ? 'bg-blue-600 border-blue-600' : 'bg-white border-gray-300'"
                                                        :aria-pressed="$store.access.moduloTieneAcceso(g.id) ? 'true' : 'false'">
                                                        <span class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow transform transition"
                                                            :class="$store.access.moduloTieneAcceso(g.id) ? 'translate-x-4' : ''"></span>
                                                    </button>
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Submódulos: se muestran sólo si el módulo tiene acceso (Ver) -->
                                        <div class="p-3" x-show="$store.access.moduloTieneAcceso(g.id)">
                                            <table class="min-w-full text-sm">
                                                <thead>
                                                    <tr class="bg-white">
                                                        <th class="text-left p-3 sticky left-0 z-10 bg-white">Submódulo</th>
                                                        <template x-for="col in $store.access.permColumns" :key="col.field">
                                                            <th class="p-3 text-center">
                                                                <div class="flex items-center justify-center gap-2 text-gray-700">
                                                                    <span x-text="col.label"></span>
                                                                    <button type="button" title="Marcar/Desmarcar todos del módulo" class="text-[11px] px-2 py-0.5 rounded-full border border-gray-300 hover:bg-gray-100"
                                                                        @click.prevent="(() => { const objs=g.objetos||[]; const visibles = objs.filter(o => !objQ || (o.nombre_objeto||'').toLowerCase().includes(objQ.toLowerCase())); const allOn = visibles.length && visibles.every(o => $store.access.isChecked(o.id, col.field)); const target=!allOn; for(const o of visibles){ if($store.access.isChecked(o.id,col.field) !== target){ $store.access.toggle(o.id,col.field); } } })()">
                                                                        Todos
                                                                    </button>
                                                                </div>
                                                            </th>
                                                        </template>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template x-for="o in g.objetos" :key="o.id">
                                                        <tr class="border-t odd:bg-gray-50 hover:bg-blue-50/50" x-show="!objQ || (o.nombre_objeto||'').toLowerCase().includes(objQ.toLowerCase())">
                                                            <td class="p-3 sticky left-0 z-10 bg-inherit" x-text="o.nombre_objeto"></td>
                                                            <template x-for="col in $store.access.permColumns" :key="col.field">
                                                                <td class="p-3 text-center">
                                                                    <button type="button"
                                                                        class="h-6 w-6 rounded-full border flex items-center justify-center hover:border-blue-500"
                                                                        :class="$store.access.isChecked(o.id,col.field) ? 'bg-blue-600 border-blue-600' : 'bg-white border-gray-300'"
                                                                        :title="$store.access.isChecked(o.id,col.field) ? 'Permitido' : 'No permitido'"
                                                                        @click="$store.access.toggle(o.id, col.field)"
                                                                        :aria-pressed="$store.access.isChecked(o.id,col.field) ? 'true' : 'false'">
                                                                        <span class="sr-only" x-text="'Cambiar ' + col.label"></span>
                                                                        <span class="h-2.5 w-2.5 rounded-full bg-white" x-show="$store.access.isChecked(o.id,col.field)"></span>
                                                                    </button>
                                                                </td>
                                                            </template>
                                                        </tr>
                                                    </template>
                                                    <tr x-show="(g.objetos||[]).filter(o => !objQ || (o.nombre_objeto||'').toLowerCase().includes(objQ.toLowerCase())).length === 0">
                                                        <td :colspan="$store.access.permColumns.length + 1" class="p-4 text-center text-gray-500">Sin submódulos visibles</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </x-admin.tabla-crud>
        <!-- Modales gestión (mantener existentes si se usan en otras pestañas) -->
        <x-admin.edit-modal class="nunito-bold" modalName="isEditRoleModalOpen" title="Editar Permisos del Rol" itemToEdit="roleToEdit"
            maxWidth="max-w-xl">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Rol</label>
                <input type="text" class="w-full border rounded px-3 py-2 bg-gray-100 nunito-regular" :value="roleToEdit?.rol"
                    readonly />
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea class="w-full border rounded px-3 py-2 bg-gray-100 nunito-regular" :value="roleToEdit?.descripcion_rol"
                    readonly x-text="roleToEdit?.descripcion_rol"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Objeto</label>
                <select class="w-full border rounded px-3 py-2 nunito-regular" x-model="roleToEdit.objeto">
                    <option>Sistema</option>
                    <option>Tickets</option>
                    <option>Reportes</option>
                    <option>Facturación</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Permisos</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-2">
                    <button type="button"
                        :class="roleToEdit?.permisos?.includes('Crear') ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700'"
                        class="flex items-center gap-2 rounded px-3 py-2 shadow transition-colors focus:outline-none nunito-regular"
                        @click="roleToEdit.permisos = roleToEdit?.permisos?.includes('Crear') ? roleToEdit.permisos.filter(p => p !== 'Crear') : [...(roleToEdit?.permisos || []), 'Crear']">
                        <i class="fas fa-plus"></i> Crear
                    </button>
                    <button type="button"
                        :class="roleToEdit?.permisos?.includes('Editar') ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700'"
                        class="flex items-center gap-2 rounded px-3 py-2 shadow transition-colors focus:outline-none nunito-regular"
                        @click="roleToEdit.permisos = roleToEdit?.permisos?.includes('Editar') ? roleToEdit.permisos.filter(p => p !== 'Editar') : [...(roleToEdit?.permisos || []), 'Editar']">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button type="button"
                        :class="roleToEdit?.permisos?.includes('Eliminar') ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700'"
                        class="flex items-center gap-2 rounded px-3 py-2 shadow transition-colors focus:outline-none nunito-regular"
                        @click="roleToEdit.permisos = roleToEdit?.permisos?.includes('Eliminar') ? roleToEdit.permisos.filter(p => p !== 'Eliminar') : [...(roleToEdit?.permisos || []), 'Eliminar']">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                    <button type="button"
                        :class="roleToEdit?.permisos?.includes('Ver') ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700'"
                        class="flex items-center gap-2 rounded px-3 py-2 shadow transition-colors focus:outline-none nunito-regular"
                        @click="roleToEdit.permisos = roleToEdit?.permisos?.includes('Ver') ? roleToEdit.permisos.filter(p => p !== 'Ver') : [...(roleToEdit?.permisos || []), 'Ver']">
                        <i class="fas fa-eye"></i> Ver
                    </button>
                </div>
            </div>
        </x-admin.edit-modal>
        <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteRoleModalOpen" itemToDelete="roleToDelete"
            message="¿Estás seguro de que quieres eliminar el rol?" />
    </div>

    <!-- TAB: Lista de Roles -->
    <div x-show="tab === 'crear'" x-data="{ ready:false }" x-init="$store.roles.init(); ready=true;">
        <x-admin.tabla-crud class="nunito-bold" :titulo="'Lista de Roles'">
            <x-slot name="filtros">
                <div class="flex flex-wrap gap-4 items-center w-full">
                    <input type="text" class="border rounded px-3 py-2 flex-1 min-w-[200px]" placeholder="Buscar rol..." @input="$store.roles.setSearch($event.target.value)" />
                    <select class="border rounded px-3 py-2" @change="$store.roles.setSort($event.target.value)">
                        <option value="rol">Ordenar por: Rol</option>
                        <option value="descripcion">Ordenar por: Descripción</option>
                        <option value="creado">Ordenar por: Creado</option>
                    </select>
                    <select class="border rounded px-3 py-2" @change="$store.roles.setDirection($event.target.value)">
                        <option value="asc">Asc</option>
                        <option value="desc">Desc</option>
                    </select>
                    <div class="text-sm text-red-600" x-text="$store.roles.error"></div>
                </div>
            </x-slot>
            <x-slot name="boton">
                <div class="w-full flex justify-end gap-2">
                    <button @click="$store.roles.openCreate()" class="duration-200 ease-in-out bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap"><span class="nunito-regular">Agregar rol</span></button>
                    <button type="button" @click.prevent="(() => { const p=new URLSearchParams(); p.set('modulo','configuracion-acceso'); p.set('seccion','roles'); if($store.roles.q) p.set('q',$store.roles.q); if($store.roles.sort){ p.set('sort',$store.roles.sort); p.set('direction',$store.roles.direction||'asc'); } const url=`/admin/reportes-header?${p.toString()}`; window.open(url,'_blank'); })()" class="duration-200 ease-in-out bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2">
                        <i class="fas fa-file-alt"></i> <span class="nunito-regular text-sm">Generar Reporte</span>
                    </button>
                </div>
            </x-slot>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-2 px-4 text-left nunito-bold">Rol</th>
                        <th class="py-2 px-4 text-left nunito-bold">Descripción</th>
                        <th class="py-2 px-4 text-left nunito-bold">Creado por</th>
                        <th class="py-2 px-4 text-left nunito-bold">Fecha de creación</th>
                        <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="role in $store.roles.items" :key="role.id">
                        <tr>
                            <td class="py-2 px-4 nunito-regular" x-text="role.rol"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="role.descripcion_rol || ''"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="role.creado_por || ''"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="role.fecha_creacion_formatted || role.fecha_creacion || ''"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click.prevent="$store.roles.openEdit(role)" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" @click.prevent="$store.roles.openDelete(role)" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="$store.roles.items.length === 0">
                        <td colspan="5" class="py-6 text-center text-gray-500">Sin resultados</td>
                    </tr>
                </tbody>
            </table>
        </x-admin.tabla-crud>
        <!-- Modal Agregar Rol -->
        <x-admin.form-modal class="nunito-bold" modalName="$store.roles.isCreateOpen" title="Agregar Rol" submitLabel="Guardar Rol" maxWidth="max-w-xl" formId="form-create-role">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Rol</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Supervisor" x-model="$store.roles.form.rol" />
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Describe el propósito del rol..." x-model="$store.roles.form.descripcion_rol"></textarea>
            </div>
            <div @modal-submit.window="if($event.detail.formId==='form-create-role'){ $store.roles.create() }"></div>
        </x-admin.form-modal>
        <!-- Modal Editar y Eliminar -->
        <x-admin.edit-modal class="nunito-bold" modalName="$store.roles.isEditOpen" title="Editar Rol" itemToEdit="$store.roles.current" maxWidth="max-w-xl" formId="form-edit-role">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Rol</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="$store.roles.form.rol" />
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea class="w-full border rounded px-3 py-2 nunito-regular" x-model="$store.roles.form.descripcion_rol"></textarea>
            </div>
            <div @modal-submit.window="if($event.detail.formId==='form-edit-role'){ $store.roles.update() }"></div>
        </x-admin.edit-modal>
    <x-admin.confirmation-modal class="nunito-bold" modalName="$store.roles.isDeleteOpen" itemToDelete="$store.roles.current" itemNameProperty="rol" message="¿Estás seguro de que quieres eliminar el rol?" />
        <div @confirm-delete.window="$store.roles.remove()"></div>
    </div>

    <!-- TAB: Objetos -->
    <div x-show="tab === 'objetos'" x-data="{ ready:false }" x-init="$store.objetos.init(); ready=true;">
        <x-admin.tabla-crud class="nunito-bold" :titulo="'Gestión de Objetos'">
            <x-slot name="filtros">
                <div class="flex flex-wrap gap-4 mb-4 items-center w-full">
                    <input type="text" class="border rounded px-3 py-2 flex-1 min-w-[200px]"
                        placeholder="Buscar objeto..." @input="$store.objetos.setSearch($event.target.value)" />
                    <select class="border rounded px-3 py-2" @change="$store.objetos.setTipo($event.target.value)">
                        <option value="">Todos los tipos</option>
                        <template x-for="t in $store.objetos.tipoOptions()" :key="'tipo-'+t.id">
                            <option :value="t.id" x-text="t.nombre"></option>
                        </template>
                    </select>
                    <div class="text-sm text-red-600" x-text="$store.objetos.error"></div>
                </div>
            </x-slot>
            <x-slot name="boton">
                <div class="w-full flex justify-end gap-2">
                    <button @click="$store.objetos.openCreate()"
                        class="duration-200 ease-in-out bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">
                        <span class="nunito-regular">Agregar objeto</span>
                    </button>
                    <button type="button" @click.prevent="(() => { const p=new URLSearchParams(); p.set('modulo','configuracion-acceso'); p.set('seccion','objetos'); if($store.objetos.q) p.set('q',$store.objetos.q); if($store.objetos.tipoId) p.set('id_tipo_objetos_fk',$store.objetos.tipoId); const url=`/admin/reportes-header?${p.toString()}`; window.open(url,'_blank'); })()" class="duration-200 ease-in-out bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2">
                        <i class="fas fa-file-alt"></i> <span class="nunito-regular text-sm">Generar Reporte</span>
                    </button>
                </div>
            </x-slot>
            <div class="overflow-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 text-left nunito-bold">Nombre</th>
                            <th class="py-2 px-4 text-left nunito-bold">Descripción</th>
                            <th class="py-2 px-4 text-left nunito-bold">Tipo</th>
                            <th class="py-2 px-4 text-left nunito-bold">Creado por</th>
                            <th class="py-2 px-4 text-left nunito-bold">Fecha creación</th>
                            <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in $store.objetos.items" :key="item.id">
                            <tr class="border-b">
                                <td class="py-2 px-4 nunito-regular" x-text="item.nombre_objeto"></td>
                                <td class="py-2 px-4 nunito-regular" x-text="item.descripcion_objeto || ''"></td>
                                <td class="py-2 px-4 nunito-regular" x-text="$store.objetos.tipoNombre(item.id_tipo_objetos_fk)"></td>
                                <td class="py-2 px-4 nunito-regular" x-text="item.creado_por || ''"></td>
                                <td class="py-2 px-4 nunito-regular" x-text="item.fecha_creacion_formatted || item.fecha_creacion || ''"></td>
                                <td class="py-2 px-4 flex gap-2">
                                    <a href="#" @click.prevent="$store.objetos.openEdit(item)"
                                        class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="$store.objetos.openDelete(item)"
                                        class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="$store.objetos.items.length === 0">
                            <td colspan="6" class="py-6 text-center text-gray-500">Sin resultados</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Controles de paginación ocultos por solicitud: se removieron Anterior/Siguiente y el indicador de página -->
        </x-admin.tabla-crud>

        <!-- Modal Agregar Objeto -->
        <x-admin.form-modal class="nunito-bold" modalName="$store.objetos.isCreateOpen" title="Agregar Objeto" submitLabel="Guardar Objeto"
            maxWidth="max-w-xl" formId="form-create-obj">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Objeto X"
                    x-model="$store.objetos.form.nombre_objeto" />
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Describe el objeto..."
                    x-model="$store.objetos.form.descripcion_objeto"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Tipo</label>
                <select class="w-full border rounded px-3 py-2 nunito-regular" x-model="$store.objetos.form.id_tipo_objetos_fk">
                    <option value="">Seleccione…</option>
                    <template x-for="t in $store.objetos.tipoOptions()" :key="'tipo-form-'+t.id">
                        <option :value="t.id" x-text="t.nombre"></option>
                    </template>
                </select>
            </div>
            <div @modal-submit.window="if($event.detail.formId==='form-create-obj'){ $store.objetos.create() }"></div>
        </x-admin.form-modal>

        <!-- Modal Editar Objeto -->
        <x-admin.edit-modal class="nunito-bold" modalName="$store.objetos.isEditOpen" title="Editar Objeto" itemToEdit="$store.objetos.current"
            maxWidth="max-w-xl" formId="form-edit-obj">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular"
                    x-model="$store.objetos.form.nombre_objeto" />
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea class="w-full border rounded px-3 py-2 nunito-regular"
                    x-model="$store.objetos.form.descripcion_objeto"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Tipo</label>
                <select class="w-full border rounded px-3 py-2 nunito-regular" x-model="$store.objetos.form.id_tipo_objetos_fk">
                    <option value="">Seleccione…</option>
                    <template x-for="t in $store.objetos.tipoOptions()" :key="'tipo-form-edit-'+t.id">
                        <option :value="t.id" x-text="t.nombre"></option>
                    </template>
                </select>
            </div>
            <div @modal-submit.window="if($event.detail.formId==='form-edit-obj'){ $store.objetos.update() }"></div>
        </x-admin.edit-modal>

        <!-- Modal Eliminar Objeto -->
        <x-admin.confirmation-modal class="nunito-bold" modalName="$store.objetos.isDeleteOpen" itemToDelete="$store.objetos.current" itemNameProperty="nombre_objeto"
            message="¿Estás seguro de que quieres eliminar el objeto?" />
        <div @confirm-delete.window="$store.objetos.remove()"></div>
    </div>

    <!-- TAB: Asignar Rol a Usuario (dinámico) -->
    <div x-show="tab === 'asignar'" x-data="{ ready:false }" x-init="$store.assignRoles.init(); ready=true;">
        <x-admin.tabla-crud class="nunito-bold" :titulo="'Asignación de Roles a Usuarios'">
            <x-slot name="filtros">
                <div class="flex flex-wrap gap-4 mb-4 items-center w-full">
                    <input type="text" class="border rounded px-3 py-2 flex-1 min-w-[200px]" placeholder="Buscar usuario..." @input="$store.assignRoles.setSearch($event.target.value)" />
                    <select class="border rounded px-3 py-2" @change="$store.assignRoles.setFilterRol($event.target.value)">
                        <option value="">Todos los roles</option>
                        <template x-for="r in $store.assignRoles.roles" :key="'rol-filter-'+r.id">
                            <option :value="r.id" x-text="r.rol"></option>
                        </template>
                    </select>
                    
                    <div class="text-sm text-red-600" x-text="$store.assignRoles.error"></div>
                </div>
            </x-slot>
            <x-slot name="boton">
                <div class="w-full flex justify-end gap-2">
                    <button @click="$store.assignRoles.openAssign(null)" class="duration-200 ease-in-out bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">
                        <span class="nunito-regular">Asignar Rol</span>
                    </button>
                    <button type="button" @click.prevent="(() => { const p=new URLSearchParams(); p.set('modulo','configuracion-acceso'); p.set('seccion','asignar'); if($store.assignRoles.q) p.set('q',$store.assignRoles.q); if($store.assignRoles.filterRol) p.set('id_rol_fk',$store.assignRoles.filterRol); if($store.assignRoles.sort){ p.set('sort',$store.assignRoles.sort); p.set('direction',$store.assignRoles.direction||'asc'); } p.set('all','1'); const url=`/admin/reportes-header?${p.toString()}`; window.open(url,'_blank'); })()" class="duration-200 ease-in-out bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2">
                        <i class="fas fa-file-alt"></i> <span class="nunito-regular text-sm">Generar Reporte</span>
                    </button>
                </div>
            </x-slot>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-2 px-4 text-left nunito-bold">Usuario</th>
                        <th class="py-2 px-4 text-left nunito-bold">Nombre</th>
                        <th class="py-2 px-4 text-left nunito-bold">Rol</th>
                        <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="u in $store.assignRoles.items" :key="u.id">
                        <tr>
                            <td class="py-2 px-4 nunito-regular" x-text="u.usuario"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="u.nombre_usuario"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="$store.assignRoles.rolNombre(u.id_rol_fk)"></td>
                            <td class="py-2 px-4">
                                <a href="#" @click.prevent="$store.assignRoles.openAssign(u)" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="$store.assignRoles.items.length === 0">
                        <td colspan="4" class="py-6 text-center text-gray-500">Sin resultados</td>
                    </tr>
                </tbody>
            </table>
        </x-admin.tabla-crud>

        <!-- Modal Asignar Rol -->
        <x-admin.form-modal class="nunito-bold" modalName="$store.assignRoles.isAssignOpen" title="Asignar Rol a Usuario" submitLabel="Guardar" maxWidth="max-w-md" formId="form-assign-role">
            <div class="mb-4" x-show="$store.assignRoles.current">
                <label class="block text-sm font-medium mb-1 nunito-bold">Usuario</label>
                <input type="text" class="w-full border rounded px-3 py-2 bg-gray-100 nunito-regular" :value="$store.assignRoles.current?.usuario" readonly />
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Rol</label>
                <select class="w-full border rounded px-3 py-2 nunito-regular" x-model="$store.assignRoles.form.id_rol_fk" required>
                    <option value="">Seleccione…</option>
                    <template x-for="r in $store.assignRoles.roles" :key="'rol-opt-'+r.id">
                        <option :value="r.id" x-text="r.rol"></option>
                    </template>
                </select>
            </div>
            <div @modal-submit.window="if($event.detail.formId==='form-assign-role'){ $store.assignRoles.saveAssign() }"></div>
        </x-admin.form-modal>
    </div>
</div>