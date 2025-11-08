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
}" @include('partials.persist-tab', ['tabKey'=> 'admin-configuracion-acceso-tab'])>
    <!-- Tabs -->
    <div class="flex border-b mb-6 flex-wrap gap-2 border-gray-200 dark:border-gray-700">
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
    <div x-show="tab === 'gestion'" x-data="{ ready: false }" x-init="$store.access.init(); ready = true;">
        <x-admin.tabla-crud class="nunito-bold" :titulo="'Gestión de Permisos'">
            <x-slot name="filtros">
                <div class="flex flex-wrap gap-2 sm:gap-4 items-center">
                    <div class="text-xs sm:text-sm text-red-600" x-text="$store.access.error"></div>
                </div>
            </x-slot>
            <x-slot name="boton">
                <div class="w-full flex justify-end gap-2">
                    <button type="button" @click.prevent="(() => { const p=new URLSearchParams(); p.set('modulo','configuracion-acceso'); p.set('seccion','gestion'); const sel=$store.access.selectedRoleId; if(sel){ p.set('rol_id', sel); const rr=$store.access.roles.find(r=>r.id===sel); if(rr?.rol) p.set('rol', rr.rol); } const url=`/admin/reportes-header?${p.toString()}`; window.open(url,'_blank'); })()"
                        class="duration-200 ease-in-out bg-blue-700 hover:bg-blue-800 text-white px-3 sm:px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2 text-xs sm:text-sm">
                        <i class="fas fa-file-alt text-sm sm:text-base"></i>
                        <span class="nunito-regular">Generar Reporte</span>
                    </button>
                </div>
            </x-slot>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 sm:gap-5 text-gray-900 dark:text-gray-200 items-start lg:rounded-lg b">
                <div class="sm:col-span-1 lg:sticky lg:top-20 bg-slate-100 dark:bg-gray-800 lg:rounded-lg sm:rounded-2xl shadow-lg ring-1 ring-gray-500 dark:ring-gray-700 p-3 sm:p-4 h-auto sm:h-[500px] lg:h-auto lg:max-h-[calc(100vh-9rem)] flex flex-col lg:overflow-hidden" x-data="{ roleQ: '' }">
                    <div class="flex items-center justify-between mb-2 sm:mb-3">
                        <h3 class="font-semibold text-sm sm:text-base text-gray-800 dark:text-gray-100">Roles</h3>
                        <span class="text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-white" x-text="$store.access.roles.length + ' totales'"></span>
                    </div>
                    <div class="relative mb-2 sm:mb-3">
                        <input type="text" x-model="roleQ" class="w-full bg-white dark:bg-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg px-2 sm:px-3 py-1.5 sm:py-2 pr-8 sm:pr-9 text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400 dark:placeholder-gray-400" placeholder="Buscar rol..." />
                        <i class="fas fa-search absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
                    </div>
                    <ul class="space-y-1 pr-1 custom-scrollbar flex-1 overflow-auto max-h-[300px] sm:max-h-none lg:max-h-full">
                        <template x-for="r in $store.access.roles.filter(rr => !roleQ || (rr.rol||'').toLowerCase().includes(roleQ.toLowerCase()))" :key="r.id">
                            <li>
                                <button class="w-full text-left px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg transition flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm"
                                    :class="$store.access.selectedRoleId===r.id ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 ring-1 ring-blue-200 dark:ring-blue-600' : 'text-gray-700 dark:text-gray-300'"
                                    @click="$store.access.selectRole(r.id)">
                                    <i class="fas fa-user-shield text-xs sm:text-sm"></i>
                                    <span class="truncate" x-text="r.rol"></span>
                                </button>
                            </li>
                        </template>
                    </ul>
                </div>
                <div class="sm:col-span-3 bg-white dark:bg-gray-800 lg:rounded-lg sm:rounded-2xl shadow-lg ring-1 ring-gray-500 dark:ring-gray-700 p-3 sm:p-4 overflow-x-auto" x-data="{ objQ: '' }">
                    <template x-if="!$store.access.selectedRoleId">
                        <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 p-4">Selecciona un rol para configurar sus permisos.</div>
                    </template>
                    <template x-if="$store.access.selectedRoleId">
                        <div>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-3 gap-2 sm:gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Rol:</span>
                                    <span class="inline-flex items-center gap-1 text-xs sm:text-sm px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                        <i class="fas fa-shield-alt text-[10px] sm:text-xs"></i>
                                        <span x-text="($store.access.roles.find(r=>r.id===$store.access.selectedRoleId)?.rol)||'—'"></span>
                                    </span>
                                </div>
                                <div class="relative w-full sm:w-64 max-w-full">
                                    <input type="text" x-model="objQ" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 sm:px-3 py-1.5 sm:py-2 pr-8 sm:pr-9 text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400 dark:placeholder-gray-400" placeholder="Filtrar objetos..." />
                                    <i class="fas fa-filter absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
                                </div>
                            </div>
                            <div class="space-y-3 sm:space-y-5">
                                <template x-for="g in $store.access.grupos()" :key="'grp-'+g.id">
                                    <div class="mb-3 sm:mb-5 border border-gray-400 dark:border-gray-700 rounded-lg sm:rounded-xl overflow-hidden bg-white dark:bg-gray-900">
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-3 sm:px-4 py-2 sm:py-3 bg-slate-200 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700 gap-2 sm:gap-0">
                                            <div class="flex items-center gap-1.5 sm:gap-2">
                                                <i class="fas fa-folder text-gray-500 dark:text-gray-400 text-xs sm:text-sm"></i>
                                                <h4 class="font-semibold text-sm sm:text-base text-gray-800 dark:text-gray-100" x-text="g.nombre"></h4>
                                                <span x-show="$store.access.isProtectedModule(g.id)" class="inline-flex items-center gap-1 text-[10px] sm:text-xs text-amber-500">
                                                    <i class="fas fa-lock"></i>
                                                    Protegido
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto justify-between sm:justify-end">
                                                <span class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400" x-text="(g.objetos||[]).length + ' submódulos'"></span>
                                                <label class="inline-flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm text-gray-700 dark:text-gray-300">
                                                    <span>Acceso</span>
                                                    @perm(['Configuración de accesos','Configuracion de accesos','Configuración de Accesos','Configuracion de Accesos','Gestión de Permisos','Gestion de Permisos'],'actualizacion')
                                                    <button type="button" @click.prevent="$store.access.toggleModulo(g.id, !$store.access.moduloTieneAcceso(g.id))"
                                                        class="relative inline-flex flex-shrink-0 h-5 w-9 sm:h-6 sm:w-11 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                                        :title="$store.access.isProtectedModule(g.id) ? 'Protegido: el rol Administrador debe mantener Seguridad habilitado.' : ($store.access.moduloTieneAcceso(g.id) ? 'Desactivar acceso al módulo' : 'Activar acceso al módulo')"
                                                        :class="$store.access.moduloTieneAcceso(g.id) ? 'bg-blue-500' : 'bg-gray-200 dark:bg-gray-600'"
                                                        role="switch" :aria-checked="$store.access.moduloTieneAcceso(g.id)">
                                                        <span aria-hidden="true"
                                                            class="pointer-events-none inline-block h-4 w-4 sm:h-5 sm:w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                            :class="$store.access.moduloTieneAcceso(g.id) ? 'translate-x-4 sm:translate-x-5' : 'translate-x-0'">
                                                        </span>
                                                    </button>
                                                    @else
                                                    <button disabled title="Sin permiso para actualizar acceso" type="button"
                                                        class="relative inline-flex flex-shrink-0 h-5 w-9 sm:h-6 sm:w-11 rounded-full border-2 border-transparent bg-gray-300 dark:bg-gray-600 opacity-60 cursor-not-allowed">
                                                        <span aria-hidden="true"
                                                            class="inline-block h-4 w-4 sm:h-5 sm:w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0"></span>
                                                    </button>
                                                    @endperm
                                                </label>
                                            </div>
                                        </div>
                                        <div class="p-2 sm:p-3" x-show="$store.access.moduloTieneAcceso(g.id)">
                                            <div class="sm:hidden space-y-2">
                                                <div class="flex flex-wrap gap-1.5 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                                                    <template x-for="col in $store.access.permColumns" :key="col.field">
                                                        @perm(['Configuración de accesos','Configuracion de accesos','Gestión de Permisos','Gestion de Permisos'],'actualizacion')
                                                        <button type="button"
                                                            class="text-[10px] px-2 py-1 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-1"
                                                            @click.prevent="(() => { const objs=g.objetos||[]; const visibles = objs.filter(o => !objQ || (o.nombre_objeto||'').toLowerCase().includes(objQ.toLowerCase())); const allOn = visibles.length && visibles.every(o => $store.access.isChecked(o.id, col.field)); const target=!allOn; for(const o of visibles){ if($store.access.isChecked(o.id,col.field) !== target){ $store.access.toggle(o.id,col.field); } } })()">
                                                            <span x-text="col.label"></span>
                                                            <span class="text-gray-400">Todos</span>
                                                        </button>
                                                        @else
                                                        <button disabled title="Sin permiso para actualizar" type="button" class="text-[10px] px-2 py-1 rounded-full border border-gray-300 dark:border-gray-600 bg-gray-200 dark:bg-gray-700 flex items-center gap-1 opacity-60 cursor-not-allowed">
                                                            <span x-text="col.label"></span>
                                                            <span class="text-gray-400">Todos</span>
                                                        </button>
                                                        @endperm
                                                    </template>
                                                </div>

                                                <template x-for="o in g.objetos" :key="o.id">
                                                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700"
                                                        x-show="!objQ || (o.nombre_objeto||'').toLowerCase().includes(objQ.toLowerCase())">
                                                        <div class="font-medium text-xs mb-2 text-gray-800 dark:text-gray-100" x-text="o.nombre_objeto"></div>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <template x-for="col in $store.access.permColumns" :key="col.field">
                                                                @perm(['Configuración de accesos','Configuracion de accesos','Gestión de Permisos','Gestion de Permisos'],'actualizacion')
                                                                <button type="button"
                                                                    class="flex items-center gap-2 px-3 py-2 rounded-lg border transition text-xs"
                                                                    :class="$store.access.isChecked(o.id,col.field)
                                                                    ? 'bg-blue-600 border-blue-600 text-white'
                                                                    : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300'"
                                                                    @click="$store.access.toggle(o.id, col.field)">
                                                                    <div class="h-4 w-4 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                                                                        :class="$store.access.isChecked(o.id,col.field)
                                                                        ? 'border-white'
                                                                        : 'border-gray-400 dark:border-gray-500'">
                                                                        <div class="h-2 w-2 rounded-full bg-white" x-show="$store.access.isChecked(o.id,col.field)"></div>
                                                                    </div>
                                                                    <span x-text="col.label"></span>
                                                                </button>
                                                                @else
                                                                <button disabled title="Sin permiso" type="button" class="flex items-center gap-2 px-3 py-2 rounded-lg border text-xs bg-gray-200 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                                                                    <div class="h-4 w-4 rounded-full border-2 flex items-center justify-center flex-shrink-0 border-gray-300 dark:border-gray-600"></div>
                                                                    <span x-text="col.label"></span>
                                                                </button>
                                                                @endperm
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                                <div x-show="(g.objetos||[]).filter(o => !objQ || (o.nombre_objeto||'').toLowerCase().includes(objQ.toLowerCase())).length === 0"
                                                    class="p-4 text-center text-xs text-gray-500 dark:text-gray-400">
                                                    Sin submódulos visibles
                                                </div>
                                            </div>

                                            <div class="hidden sm:block overflow-x-auto">
                                                <table class="min-w-full text-sm text-gray-900 dark:text-gray-200">
                                                    <thead class="bg-gray-100 dark:bg-gray-700">
                                                        <tr>
                                                            <th class="text-left p-3 sticky left-0 z-10 bg-gray-100 dark:bg-gray-700">Submódulo</th>
                                                            <template x-for="col in $store.access.permColumns" :key="col.field">
                                                                <th class="p-3 text-center">
                                                                    <div class="flex items-center justify-center gap-2 text-gray-700 dark:text-gray-300">
                                                                        <span class="text-xs" x-text="col.label"></span>
                                                                        <button type="button" title="Marcar/Desmarcar todos del módulo" class="text-[11px] px-2 py-0.5 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700"
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
                                                            <tr class="border-t border-slate-400/70 dark:border-gray-700 bg-white dark:bg-gray-900" x-show="!objQ || (o.nombre_objeto||'').toLowerCase().includes(objQ.toLowerCase())">
                                                                <td class="p-3 sticky left-0 z-10 bg-inherit text-sm" x-text="o.nombre_objeto"></td>
                                                                <template x-for="col in $store.access.permColumns" :key="col.field">
                                                                    <td class="p-3 text-center">
                                                                        @perm(['Configuración de accesos','Configuracion de accesos','Gestión de Permisos','Gestion de Permisos'],'actualizacion')
                                                                        <button type="button"
                                                                            class="h-6 w-6 rounded-full border flex items-center justify-center transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                                                                            :class="$store.access.isChecked(o.id,col.field)
                                                                            ? 'bg-blue-600 border-blue-600'
                                                                            : 'bg-white dark:bg-gray-800 border-gray-400 dark:border-white/70 hover:border-gray-500 dark:hover:border-white focus-visible:border-white'"
                                                                            :title="$store.access.isChecked(o.id,col.field) ? 'Permitido' : 'No permitido'"
                                                                            @click="$store.access.toggle(o.id, col.field)"
                                                                            :aria-pressed="$store.access.isChecked(o.id,col.field) ? 'true' : 'false'">
                                                                            <span class="sr-only" x-text="'Cambiar ' + col.label"></span>
                                                                            <span class="h-2.5 w-2.5 rounded-full bg-white" x-show="$store.access.isChecked(o.id,col.field)"></span>
                                                                        </button>
                                                                        @else
                                                                        <button disabled title="Sin permiso" type="button" class="h-6 w-6 rounded-full border flex items-center justify-center bg-gray-200 dark:bg-gray-700 border-gray-400 dark:border-gray-600 opacity-60 cursor-not-allowed"></button>
                                                                        @endperm
                                                                    </td>
                                                                </template>
                                                            </tr>
                                                        </template>
                                                        <tr x-show="(g.objetos||[]).filter(o => !objQ || (o.nombre_objeto||'').toLowerCase().includes(objQ.toLowerCase())).length === 0">
                                                            <td :colspan="$store.access.permColumns.length + 1" class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">Sin submódulos visibles</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </x-admin.tabla-crud>
        <x-admin.edit-modal class="nunito-bold" modalName="isEditRoleModalOpen" title="Editar Permisos del Rol" itemToEdit="roleToEdit"
            maxWidth="max-w-xl">
            <div class="mb-3 sm:mb-4">
                <label class="block text-xs sm:text-sm font-medium mb-1 nunito-bold">Rol</label>
                <input type="text" class="w-full border rounded px-2 sm:px-3 py-1.5 sm:py-2 bg-gray-100 nunito-regular text-xs sm:text-sm" :value="roleToEdit?.rol"
                    readonly />
            </div>
            <div class="mb-3 sm:mb-4">
                <label class="block text-xs sm:text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea class="w-full border rounded px-2 sm:px-3 py-1.5 sm:py-2 bg-gray-100 nunito-regular text-xs sm:text-sm" :value="roleToEdit?.descripcion_rol"
                    readonly x-text="roleToEdit?.descripcion_rol"></textarea>
            </div>
            <div class="mb-3 sm:mb-4">
                <label class="block text-xs sm:text-sm font-medium mb-1 nunito-bold">Objeto</label>
                <select class="w-full border rounded px-2 sm:px-3 py-1.5 sm:py-2 nunito-regular text-xs sm:text-sm" x-model="roleToEdit.objeto">
                    <option>Sistema</option>
                    <option>Tickets</option>
                    <option>Reportes</option>
                    <option>Facturación</option>
                </select>
            </div>
            <div class="mb-3 sm:mb-4">
                <label class="block text-xs sm:text-sm font-medium mb-1 nunito-bold">Permisos</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 mt-2">
                    <button type="button"
                        :class="roleToEdit?.permisos?.includes('Crear') ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700'"
                        class="flex items-center gap-1.5 sm:gap-2 rounded px-2 sm:px-3 py-1.5 sm:py-2 shadow transition-colors focus:outline-none nunito-regular text-xs sm:text-sm"
                        @click="roleToEdit.permisos = roleToEdit?.permisos?.includes('Crear') ? roleToEdit.permisos.filter(p => p !== 'Crear') : [...(roleToEdit?.permisos || []), 'Crear']">
                        <i class="fas fa-plus text-xs sm:text-sm"></i> Crear
                    </button>
                    <button type="button"
                        :class="roleToEdit?.permisos?.includes('Editar') ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700'"
                        class="flex items-center gap-1.5 sm:gap-2 rounded px-2 sm:px-3 py-1.5 sm:py-2 shadow transition-colors focus:outline-none nunito-regular text-xs sm:text-sm"
                        @click="roleToEdit.permisos = roleToEdit?.permisos?.includes('Editar') ? roleToEdit.permisos.filter(p => p !== 'Editar') : [...(roleToEdit?.permisos || []), 'Editar']">
                        <i class="fas fa-edit text-xs sm:text-sm"></i> Editar
                    </button>
                    <button type="button"
                        :class="roleToEdit?.permisos?.includes('Eliminar') ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700'"
                        class="flex items-center gap-1.5 sm:gap-2 rounded px-2 sm:px-3 py-1.5 sm:py-2 shadow transition-colors focus:outline-none nunito-regular text-xs sm:text-sm"
                        @click="roleToEdit.permisos = roleToEdit?.permisos?.includes('Eliminar') ? roleToEdit.permisos.filter(p => p !== 'Eliminar') : [...(roleToEdit?.permisos || []), 'Eliminar']">
                        <i class="fas fa-trash text-xs sm:text-sm"></i> Eliminar
                    </button>
                    <button type="button"
                        :class="roleToEdit?.permisos?.includes('Ver') ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700'"
                        class="flex items-center gap-1.5 sm:gap-2 rounded px-2 sm:px-3 py-1.5 sm:py-2 shadow transition-colors focus:outline-none nunito-regular text-xs sm:text-sm"
                        @click="roleToEdit.permisos = roleToEdit?.permisos?.includes('Ver') ? roleToEdit.permisos.filter(p => p !== 'Ver') : [...(roleToEdit?.permisos || []), 'Ver']">
                        <i class="fas fa-eye text-xs sm:text-sm"></i> Ver
                    </button>
                </div>
            </div>
        </x-admin.edit-modal>
        <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteRoleModalOpen" itemToDelete="roleToDelete"
            message="¿Estás seguro de que quieres eliminar el rol?" />
    </div

        <!-- TAB: Lista de Roles -->
    <div x-show="tab === 'crear'" x-data="{ ready:false, searchRoles:'', ordenarPor:'rol', direction:'asc' }" x-init="$store.roles.init(); ready=true; $watch('searchRoles', v => $store.roles.setSearch(v)); $watch('ordenarPor', v => $store.roles.setSort(v)); $watch('direction', v => $store.roles.setDirection(v));">
        <x-admin.tabla-crud class="nunito-bold" :titulo="'Lista de Roles'">
            <x-slot name="filtros">
                <div class="flex flex-wrap gap-4 items-center w-full">
                    @include('partials.filtros-generales', [
                    'searchModel' => 'searchRoles',
                    'filtrosSelect' => [
                    'direction' => [
                    'label' => 'Dirección',
                    'options' => ['Ascendente','Descendente']
                    ]
                    ],
                    'ordenarOptions' => [
                    'rol' => 'Rol',
                    'descripcion' => 'Descripción',
                    'creado' => 'Creado'
                    ]
                    ])
                    <div class="text-sm text-red-600" x-text="$store.roles.error"></div>
                </div>
            </x-slot>
            <x-slot name="boton">
                <div class="w-full flex justify-end gap-2">
                    @perm(['Configuración de accesos','Configuracion de accesos','Roles','Gestión de Permisos','Gestion de Permisos'],'insercion')
                    <button @click="$store.roles.openCreate()" class="duration-200 ease-in-out bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap"><span class="nunito-regular">Agregar rol</span></button>
                    @else
                    <button disabled title="Sin permiso para crear" class="duration-200 ease-in-out bg-green-600 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap opacity-60 cursor-not-allowed"><span class="nunito-regular">Agregar rol</span></button>
                    @endperm
                    <button type="button" @click.prevent="(() => { const p=new URLSearchParams(); p.set('modulo','configuracion-acceso'); p.set('seccion','roles'); if($store.roles.q) p.set('q',$store.roles.q); if($store.roles.sort){ p.set('sort',$store.roles.sort); p.set('direction',$store.roles.direction||'asc'); } const url=`/admin/reportes-header?${p.toString()}`; window.open(url,'_blank'); })()" class="duration-200 ease-in-out bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2">
                        <i class="fas fa-file-alt"></i> <span class="nunito-regular text-sm">Generar Reporte</span>
                    </button>
                </div>
            </x-slot>

            <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full text-sm text-gray-900 dark:text-gray-200">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                        <tr>
                            <th class="py-2 px-4 text-left nunito-bold">Rol</th>
                            <th class="py-2 px-4 text-left nunito-bold">Descripción</th>
                            <th class="py-2 px-4 text-left nunito-bold">Creado por</th>
                            <th class="py-2 px-4 text-left nunito-bold">Fecha de creación</th>
                            <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="role in $store.roles.items" :key="role.id">
                            <tr class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                <td class="py-2 px-4 nunito-regular" x-text="role.rol"></td>
                                <td class="py-2 px-4 nunito-regular" x-text="role.descripcion_rol || ''"></td>
                                <td class="py-2 px-4 nunito-regular" x-text="role.creado_por || ''"></td>
                                <td class="py-2 px-4 nunito-regular" x-text="role.fecha_creacion_formatted || role.fecha_creacion || ''"></td>
                                <td class="py-2 px-4 flex gap-2 text-sm">
                                    @perm(['Configuración de accesos','Configuracion de accesos','Roles'],'actualizacion')
                                    <button @click.prevent="$store.roles.openEdit(role)" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"><i class="fas fa-edit"></i></button>
                                    @else
                                    <span title="Sin permiso para editar" class="text-blue-300 cursor-not-allowed"><i class="fas fa-edit"></i></span>
                                    @endperm
                                    @perm(['Configuración de accesos','Configuracion de accesos','Roles'],'eliminacion')
                                    <button @click.prevent="$store.roles.openDelete(role)" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"><i class="fas fa-trash"></i></button>
                                    @else
                                    <span title="Sin permiso para eliminar" class="text-red-300 cursor-not-allowed"><i class="fas fa-trash"></i></span>
                                    @endperm
                                </td>
                            </tr>
                        </template>
                        <tr x-show="$store.roles.items.length === 0">
                            <td colspan="5" class="py-6 text-center text-gray-500 dark:text-gray-400">Sin resultados</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="md:hidden space-y-4">
                <template x-for="role in $store.roles.items" :key="role.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="role.rol"></h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular mt-1" x-text="role.descripcion_rol || 'Sin descripción'"></p>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1 nunito-regular">
                            <div><span class="font-semibold">Creado por:</span> <span x-text="role.creado_por || 'N/A'"></span></div>
                            <div><span class="font-semibold">Fecha:</span> <span x-text="role.fecha_creacion_formatted || role.fecha_creacion || 'N/A'"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            @perm(['Configuración de accesos','Configuracion de accesos','Roles'],'actualizacion')
                            <button @click.prevent="$store.roles.openEdit(role)" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button disabled title="Sin permiso para editar" class="px-3 py-1 text-xs bg-blue-600 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm
                            @perm(['Configuración de accesos','Configuracion de accesos','Roles'],'eliminacion')
                            <button @click.prevent="$store.roles.openDelete(role)" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @else
                            <button disabled title="Sin permiso para eliminar" class="px-3 py-1 text-xs bg-red-600 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
                <div x-show="$store.roles.items.length === 0" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    Sin resultados
                </div>
            </div>

        </x-admin.tabla-crud>

        <div x-show="$store.roles.allItems.length > $store.roles.perPage" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
            <div class="mb-2">
                <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                    Mostrando
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="($store.roles.currentPage - 1) * $store.roles.perPage + 1"></strong>
                    a
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min($store.roles.currentPage * $store.roles.perPage, $store.roles.allItems.length)"></strong>
                    de
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="$store.roles.allItems.length"></strong>
                    resultados
                </span>
            </div>
            <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
                <button @click="$store.roles.prevPage()" :disabled="$store.roles.currentPage === 1"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Anterior</span>
                </button>
                <div class="flex items-center gap-1">
                    <template x-for="page in Array.from({length: $store.roles.totalPages()}, (_, i) => i + 1).slice(Math.max(0, $store.roles.currentPage - 3), $store.roles.currentPage + 2)" :key="page">
                        <button @click="$store.roles.goToPage(page)"
                            class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                            :class="page === $store.roles.currentPage ? 'bg-blue-600 text-white' : ''">
                            <span x-text="page"></span>
                        </button>
                    </template>
                </div>
                <button @click="$store.roles.nextPage()" :disabled="$store.roles.currentPage === $store.roles.totalPages()"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                    <span>Siguiente</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>

        @perm(['Configuración de accesos','Configuracion de accesos','Roles'],'insercion')
        <x-admin.form-modal class="nunito-bold" modalName="$store.roles.isCreateOpen" title="Agregar Rol" submitLabel="Guardar Rol" maxWidth="max-w-xl" formId="form-create-role">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Rol</label>
                <input type="text" maxlength="50" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Supervisor" x-model="$store.roles.form.rol"
                    @input="$store.roles.form._touched = true"
                    @blur="$store.roles.form._touched = true"
                    :class="$store.roles.form._touched && (!$store.roles.form.rol || $store.roles.form.rol.length >= 50) ? 'border-red-500' : ''" />
                <small class="text-xs text-gray-500 block mt-1" :class="$store.roles.form._touched && (!$store.roles.form.rol || $store.roles.form.rol.length >= 50) ? 'text-red-500' : ''">Requerido. Máximo 50 caracteres.</small>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea maxlength="255" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Describe el propósito del rol..." x-model="$store.roles.form.descripcion_rol"
                    @input="$store.roles.form._touched = true"
                    @blur="$store.roles.form._touched = true"
                    :class="$store.roles.form._touched && ($store.roles.form.descripcion_rol && $store.roles.form.descripcion_rol.length >= 250) ? 'border-red-500' : ''"></textarea>
                <small class="text-xs text-gray-500 block mt-1" :class="$store.roles.form._touched && ($store.roles.form.descripcion_rol && $store.roles.form.descripcion_rol.length >= 250) ? 'text-red-500' : ''">Opcional. Máximo 250 caracteres.</small>
            </div>
            <div @modal-submit.window="if($event.detail.formId==='form-create-role'){ $store.roles.create() }"></div>
        </x-admin.form-modal>
        @endperm

        @perm(['Configuración de accesos','Configuracion de accesos','Roles'],'actualizacion')
        <x-admin.edit-modal class="nunito-bold" modalName="$store.roles.isEditOpen" title="Editar Rol" itemToEdit="$store.roles.current" maxWidth="max-w-xl" formId="form-edit-role">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Rol</label>
                <input type="text" maxlength="50" class="w-full border rounded px-3 py-2 nunito-regular" x-model="$store.roles.form.rol"
                    @input="$store.roles.form._touched = true"
                    @blur="$store.roles.form._touched = true"
                    :class="$store.roles.form._touched && (!$store.roles.form.rol || $store.roles.form.rol.length >= 50) ? 'border-red-500' : ''" />
                <small class="text-xs text-gray-500 block mt-1" :class="$store.roles.form._touched && (!$store.roles.form.rol || $store.roles.form.rol.length >= 50) ? 'text-red-500' : ''">Requerido. Máximo 50 caracteres.</small>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea maxlength="255" class="w-full border rounded px-3 py-2 nunito-regular" x-model="$store.roles.form.descripcion_rol"
                    @input="$store.roles.form._touched = true"
                    @blur="$store.roles.form._touched = true"
                    :class="$store.roles.form._touched && ($store.roles.form.descripcion_rol && $store.roles.form.descripcion_rol.length >= 250) ? 'border-red-500' : ''"></textarea>
                <small class="text-xs text-gray-500 block mt-1" :class="$store.roles.form._touched && ($store.roles.form.descripcion_rol && $store.roles.form.descripcion_rol.length >= 250) ? 'text-red-500' : ''">Opcional. Máximo 250 caracteres.</small>
            </div>
            <div @modal-submit.window="if($event.detail.formId==='form-edit-role'){ $store.roles.update() }"></div>
        </x-admin.edit-modal>
        @endperm

        @perm(['Configuración de accesos','Configuracion de accesos','Roles'],'eliminacion')
        <x-admin.confirmation-modal class="nunito-bold" modalName="$store.roles.isDeleteOpen" itemToDelete="$store.roles.current" itemNameProperty="rol" message="¿Estás seguro de que quieres eliminar el rol?" />
        @endperm
        <div @confirm-delete.window="$store.roles.remove()"></div>
    </div>

    <div x-show="tab === 'objetos'" x-data="{ ready:false, searchObjetos:'' }" x-init="$store.objetos.init(); ready=true; $watch('searchObjetos', v => $store.objetos.setSearch(v));">
        <x-admin.tabla-crud class="nunito-bold" :titulo="'Gestión de Objetos'">
            <x-slot name="filtros">
                <div class="flex flex-wrap gap-4 items-center w-full">
                    @include('partials.filtros-generales', [
                    'searchModel' => 'searchObjetos',
                    'filtrosSelect' => [],
                    'ordenarOptions' => []
                    ])
                    <select class="border rounded border-gray-600 text-left px-3 py-2 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" @change="$store.objetos.setTipo($event.target.value)">
                        <option value="">Todos los tipos</option>
                        <template x-for="t in $store.objetos.tipoOptions()" :key="'tipo-'+t.id">
                            <option :value="t.id" x-text="t.nombre"></option>
                        </template>
                    </select>
                    <div class="flex gap-2 ml-auto">
                        @perm(['Configuración de accesos','Configuracion de accesos','Objetos'],'insercion')
                        <button @click="$store.objetos.openCreate()"
                            class="duration-200 ease-in-out bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">
                            <span class="nunito-regular">Agregar objeto</span>
                        </button>
                        @else
                        <button disabled title="Sin permiso para crear" class="duration-200 ease-in-out bg-green-600 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap opacity-60 cursor-not-allowed">
                            <span class="nunito-regular">Agregar objeto</span>
                        </button>
                        @endperm
                        <button type="button" @click.prevent="(() => { const p=new URLSearchParams(); p.set('modulo','configuracion-acceso'); p.set('seccion','objetos'); if($store.objetos.q) p.set('q',$store.objetos.q); if($store.objetos.tipoId) p.set('id_tipo_objetos_fk',$store.objetos.tipoId); const url=`/admin/reportes-header?${p.toString()}`; window.open(url,'_blank'); })()" class="duration-200 ease-in-out bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2">
                            <i class="fas fa-file-alt"></i> <span class="nunito-regular text-sm">Generar Reporte</span>
                        </button>
                    </div>
                    <div class="text-sm text-red-600 w-full" x-text="$store.objetos.error"></div>
                </div>
            </x-slot>
            <x-slot name="boton"></x-slot>

            <div class="hidden md:block overflow-auto bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full text-sm text-gray-900 dark:text-gray-200">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                        <tr>
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
                            <tr class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                <td class="py-2 px-4 nunito-regular" x-text="item.nombre_objeto"></td>
                                <td class="py-2 px-4 nunito-regular" x-text="item.descripcion_objeto || ''"></td>
                                <td class="py-2 px-4 nunito-regular" x-text="$store.objetos.tipoNombre(item.id_tipo_objetos_fk)"></td>
                                <td class="py-2 px-4 nunito-regular" x-text="item.creado_por || ''"></td>
                                <td class="py-2 px-4 nunito-regular" x-text="item.fecha_creacion_formatted || item.fecha_creacion || ''"></td>
                                <td class="py-2 px-4 flex gap-2">
                                    @perm(['Configuración de accesos','Configuracion de accesos','Objetos'],'actualizacion')
                                    <button @click.prevent="$store.objetos.openEdit(item)" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"><i class="fas fa-edit"></i></button>
                                    @else
                                    <span title="Sin permiso para editar" class="text-blue-300 cursor-not-allowed"><i class="fas fa-edit"></i></span>
                                    @endperm
                                    @perm(['Configuración de accesos','Configuracion de accesos','Objetos'],'eliminacion')
                                    <button @click.prevent="$store.objetos.openDelete(item)" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"><i class="fas fa-trash"></i></button>
                                    @else
                                    <span title="Sin permiso para eliminar" class="text-red-300 cursor-not-allowed"><i class="fas fa-trash"></i></span>
                                    @endperm
                                </td>
                            </tr>
                        </template>
                        <tr x-show="$store.objetos.items.length === 0">
                            <td colspan="6" class="py-6 text-center text-gray-500 dark:text-gray-400">Sin resultados</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="md:hidden space-y-4">
                <template x-for="item in $store.objetos.items" :key="item.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="item.nombre_objeto"></h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular mt-1" x-text="item.descripcion_objeto || 'Sin descripción'"></p>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1 nunito-regular">
                            <div><span class="font-semibold">Tipo:</span> <span x-text="$store.objetos.tipoNombre(item.id_tipo_objetos_fk)"></span></div>
                            <div><span class="font-semibold">Creado por:</span> <span x-text="item.creado_por || 'N/A'"></span></div>
                            <div><span class="font-semibold">Fecha:</span> <span x-text="item.fecha_creacion_formatted || item.fecha_creacion || 'N/A'"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            @perm(['Configuración de accesos','Configuracion de accesos','Objetos'],'actualizacion')
                            <button @click.prevent="$store.objetos.openEdit(item)" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button disabled title="Sin permiso para editar" class="px-3 py-1 text-xs bg-blue-600 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm
                            @perm(['Configuración de accesos','Configuracion de accesos','Objetos'],'eliminacion')
                            <button @click.prevent="$store.objetos.openDelete(item)" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @else
                            <button disabled title="Sin permiso para eliminar" class="px-3 py-1 text-xs bg-red-600 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
                <div x-show="$store.objetos.items.length === 0" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    Sin resultados
                </div>
            </div>

        </x-admin.tabla-crud>

        <div x-show="$store.objetos.totalFiltered > $store.objetos.perPage" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
            <div class="mb-2">
                <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                    Mostrando
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="($store.objetos.currentPage - 1) * $store.objetos.perPage + 1"></strong>
                    a
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min($store.objetos.currentPage * $store.objetos.perPage, $store.objetos.totalFiltered)"></strong>
                    de
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="$store.objetos.totalFiltered"></strong>
                    resultados
                </span>
            </div>
            <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
                <button @click="$store.objetos.prevPage()" :disabled="$store.objetos.currentPage === 1"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Anterior</span>
                </button>
                <div class="flex items-center gap-1">
                    <template x-for="page in Array.from({length: $store.objetos.totalPages()}, (_, i) => i + 1).slice(Math.max(0, $store.objetos.currentPage - 3), $store.objetos.currentPage + 2)" :key="page">
                        <button @click="$store.objetos.goToPage(page)"
                            class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                            :class="page === $store.objetos.currentPage ? 'bg-blue-600 text-white' : ''">
                            <span x-text="page"></span>
                        </button>
                    </template>
                </div>
                <button @click="$store.objetos.nextPage()" :disabled="$store.objetos.currentPage === $store.objetos.totalPages()"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                    <span>Siguiente</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>

        @perm(['Configuración de accesos','Configuracion de accesos','Objetos'],'insercion')
        <x-admin.form-modal class="nunito-bold" modalName="$store.objetos.isCreateOpen" title="Agregar Objeto" submitLabel="Guardar Objeto"
            maxWidth="max-w-xl" formId="form-create-obj">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
                <input type="text" maxlength="100" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Objeto X"
                    x-model="$store.objetos.form.nombre_objeto"
                    @input="$store.objetos.form._touched = true"
                    @blur="$store.objetos.form._touched = true"
                    :class="$store.objetos.form._touched && (!$store.objetos.form.nombre_objeto || $store.objetos.form.nombre_objeto.length >= 100) ? 'border-red-500' : ''" />
                <small class="text-xs text-gray-500 block mt-1" :class="$store.objetos.form._touched && (!$store.objetos.form.nombre_objeto || $store.objetos.form.nombre_objeto.length >= 100) ? 'text-red-500' : ''">Requerido. Máximo 100 caracteres.</small>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea maxlength="255" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Describe el objeto..."
                    x-model="$store.objetos.form.descripcion_objeto"
                    @input="$store.objetos.form._touched = true"
                    @blur="$store.objetos.form._touched = true"
                    :class="$store.objetos.form._touched && ($store.objetos.form.descripcion_objeto && $store.objetos.form.descripcion_objeto.length >= 255) ? 'border-red-500' : ''"></textarea>
                <small class="text-xs text-gray-500 block mt-1" :class="$store.objetos.form._touched && ($store.objetos.form.descripcion_objeto && $store.objetos.form.descripcion_objeto.length >= 255) ? 'text-red-500' : ''">Opcional. Máximo 255 caracteres.</small>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Tipo</label>
                <select class="w-full border rounded px-3 py-2 nunito-regular" x-model="$store.objetos.form.id_tipo_objetos_fk"
                    @change="$store.objetos.form._touched = true"
                    :class="$store.objetos.form._touched && !$store.objetos.form.id_tipo_objetos_fk ? 'border-red-500' : ''">
                    <option value="">Seleccione…</option>
                    <template x-for="t in $store.objetos.tipoOptions()" :key="'tipo-form-'+t.id">
                        <option :value="t.id" x-text="t.nombre"></option>
                    </template>
                </select>
                <small class="text-xs text-gray-500 block mt-1" :class="$store.objetos.form._touched && !$store.objetos.form.id_tipo_objetos_fk ? 'text-red-500' : ''">Requerido. Seleccione un tipo.</small>
            </div>
            <div @modal-submit.window="if($event.detail.formId==='form-create-obj'){ $store.objetos.create() }"></div>
        </x-admin.form-modal>
        @endperm

        @perm(['Configuración de accesos','Configuracion de accesos','Objetos'],'actualizacion')
        <x-admin.edit-modal class="nunito-bold" modalName="$store.objetos.isEditOpen" title="Editar Objeto" itemToEdit="$store.objetos.current"
            maxWidth="max-w-xl" formId="form-edit-obj">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
                <input type="text" maxlength="100" class="w-full border rounded px-3 py-2 nunito-regular"
                    x-model="$store.objetos.form.nombre_objeto"
                    @input="$store.objetos.form._touched = true"
                    @blur="$store.objetos.form._touched = true"
                    :class="$store.objetos.form._touched && (!$store.objetos.form.nombre_objeto || $store.objetos.form.nombre_objeto.length >= 100) ? 'border-red-500' : ''" />
                <small class="text-xs text-gray-500 block mt-1" :class="$store.objetos.form._touched && (!$store.objetos.form.nombre_objeto || $store.objetos.form.nombre_objeto.length >= 100) ? 'text-red-500' : ''">Requerido. Máximo 100 caracteres.</small>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea maxlength="255" class="w-full border rounded px-3 py-2 nunito-regular"
                    x-model="$store.objetos.form.descripcion_objeto"
                    @input="$store.objetos.form._touched = true"
                    @blur="$store.objetos.form._touched = true"
                    :class="$store.objetos.form._touched && ($store.objetos.form.descripcion_objeto && $store.objetos.form.descripcion_objeto.length >= 255) ? 'border-red-500' : ''"></textarea>
                <small class="text-xs text-gray-500 block mt-1" :class="$store.objetos.form._touched && ($store.objetos.form.descripcion_objeto && $store.objetos.form.descripcion_objeto.length >= 255) ? 'text-red-500' : ''">Opcional. Máximo 255 caracteres.</small>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Tipo</label>
                <select class="w-full border rounded px-3 py-2 nunito-regular" x-model="$store.objetos.form.id_tipo_objetos_fk"
                    @change="$store.objetos.form._touched = true"
                    :class="$store.objetos.form._touched && !$store.objetos.form.id_tipo_objetos_fk ? 'border-red-500' : ''">
                    <option value="">Seleccione…</option>
                    <template x-for="t in $store.objetos.tipoOptions()" :key="'tipo-form-edit-'+t.id">
                        <option :value="t.id" x-text="t.nombre"></option>
                    </template>
                </select>
                <small class="text-xs text-gray-500 block mt-1" :class="$store.objetos.form._touched && !$store.objetos.form.id_tipo_objetos_fk ? 'text-red-500' : ''">Requerido. Seleccione un tipo.</small>
            </div>
            <div @modal-submit.window="if($event.detail.formId==='form-edit-obj'){ $store.objetos.update() }"></div>
        </x-admin.edit-modal>
        @endperm

        @perm(['Configuración de accesos','Configuracion de accesos','Objetos'],'eliminacion')
        <x-admin.confirmation-modal class="nunito-bold" modalName="$store.objetos.isDeleteOpen" itemToDelete="$store.objetos.current" itemNameProperty="nombre_objeto"
            message="¿Estás seguro de que quieres eliminar el objeto?" />
        @endperm
        <div @confirm-delete.window="$store.objetos.remove()"></div>
    </div>

    <div x-show="tab === 'asignar'" x-data="{ ready:false, searchAssign:'' }" x-init="$store.assignRoles.init(); ready=true; $watch('searchAssign', v => $store.assignRoles.setSearch(v));">
        <x-admin.tabla-crud class="nunito-bold" :titulo="'Asignación de Roles a Usuarios'">
            <x-slot name="filtros">
                <div class="flex flex-wrap gap-4 mb-4 items-center w-full">
                    @include('partials.filtros-generales', [
                    'searchModel' => 'searchAssign',
                    'filtrosSelect' => [],
                    'ordenarOptions' => []
                    ])
                    <select class="border rounded border-gray-600 px-4 py-2 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" @change="$store.assignRoles.setFilterRol($event.target.value)">
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
                    <button type="button" @click.prevent="(() => { const p=new URLSearchParams(); p.set('modulo','configuracion-acceso'); p.set('seccion','asignar'); if($store.assignRoles.q) p.set('q',$store.assignRoles.q); if($store.assignRoles.filterRol) p.set('id_rol_fk',$store.assignRoles.filterRol); if($store.assignRoles.sort){ p.set('sort',$store.assignRoles.sort); p.set('direction',$store.assignRoles.direction||'asc'); } p.set('all','1'); const url=`/admin/reportes-header?${p.toString()}`; window.open(url,'_blank'); })()" class="duration-200 ease-in-out bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2">
                        <i class="fas fa-file-alt"></i> <span class="nunito-regular text-sm">Generar Reporte</span>
                    </button>
                </div>
            </x-slot>

            <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full text-sm text-gray-900 dark:text-gray-200">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                        <tr>
                            <th class="py-2 px-4 text-left nunito-bold">Usuario</th>
                            <th class="py-2 px-4 text-left nunito-bold">Nombre</th>
                            <th class="py-2 px-4 text-left nunito-bold">Rol</th>
                            <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="u in $store.assignRoles.items" :key="u.id">
                            <tr class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                <td class="py-2 px-4 nunito-regular" x-text="u.usuario"></td>
                                <td class="py-2 px-4 nunito-regular" x-text="u.nombre_usuario"></td>
                                <td class="py-2 px-4 nunito-regular" x-text="$store.assignRoles.rolNombre(u.id_rol_fk)"></td>
                                <td class="py-2 px-4">
                                    @perm(['Configuración de accesos','Configuracion de accesos','Asignación de Roles','Asignacion de Roles'],'actualizacion')
                                    <button @click.prevent="$store.assignRoles.openAssign(u)" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"><i class="fas fa-edit"></i></button>
                                    @else
                                    <span title="Sin permiso para asignar" class="text-blue-300 cursor-not-allowed"><i class="fas fa-edit"></i></span>
                                    @endperm
                                </td>
                            </tr>
                        </template>
                        <tr x-show="$store.assignRoles.items.length === 0">
                            <td colspan="4" class="py-6 text-center text-gray-500 dark:text-gray-400">Sin resultados</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="md:hidden space-y-4">
                <template x-for="u in $store.assignRoles.items" :key="u.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="u.usuario"></h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular mt-1" x-text="u.nombre_usuario"></p>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1 nunito-regular">
                            <div><span class="font-semibold">Rol:</span> <span x-text="$store.assignRoles.rolNombre(u.id_rol_fk)"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            @perm(['Configuración de accesos','Configuracion de accesos','Asignación de Roles','Asignacion de Roles'],'actualizacion')
                            <button @click.prevent="$store.assignRoles.openAssign(u)" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Asignar
                            </button>
                            @else
                            <button disabled title="Sin permiso para asignar" class="px-3 py-1 text-xs bg-blue-600 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Asignar
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
                <div x-show="$store.assignRoles.items.length === 0" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    Sin resultados
                </div>
            </div>

        </x-admin.tabla-crud>

        <div x-show="$store.assignRoles.totalFiltered > $store.assignRoles.perPage" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
            <div class="mb-2">
                <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                    Mostrando
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="($store.assignRoles.currentPage - 1) * $store.assignRoles.perPage + 1"></strong>
                    a
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min($store.assignRoles.currentPage * $store.assignRoles.perPage, $store.assignRoles.totalFiltered)"></strong>
                    de
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="$store.assignRoles.totalFiltered"></strong>
                    resultados
                </span>
            </div>
            <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
                <button @click="$store.assignRoles.prevPage()" :disabled="$store.assignRoles.currentPage === 1"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Anterior</span>
                </button>
                <div class="flex items-center gap-1">
                    <template x-for="page in Array.from({length: $store.assignRoles.totalPages()}, (_, i) => i + 1).slice(Math.max(0, $store.assignRoles.currentPage - 3), $store.assignRoles.currentPage + 2)" :key="page">
                        <button @click="$store.assignRoles.goToPage(page)"
                            class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                            :class="page === $store.assignRoles.currentPage ? 'bg-blue-600 text-white' : ''">
                            <span x-text="page"></span>
                        </button>
                    </template>
                </div>
                <button @click="$store.assignRoles.nextPage()" :disabled="$store.assignRoles.currentPage === $store.assignRoles.totalPages()"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                    <span>Siguiente</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>

        @perm(['Configuración de accesos','Configuracion de accesos','Asignación de Roles','Asignacion de Roles'],'actualizacion')
        <x-admin.form-modal class="nunito-bold" modalName="$store.assignRoles.isAssignOpen" title="Asignar Roles a Usuario" submitLabel="Guardar" maxWidth="max-w-md" formId="form-assign-role">
            <div class="mb-4" x-show="$store.assignRoles.current">
                <label class="block text-sm font-medium mb-1 nunito-bold">Usuario</label>
                <input type="text" class="w-full border rounded px-3 py-2 bg-gray-100 nunito-regular" :value="$store.assignRoles.current?.usuario" readonly />
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Rol principal</label>
                <select class="w-full border rounded px-3 py-2 nunito-regular" x-model="$store.assignRoles.rol_principal" @change="$store.assignRoles.setPrincipal($event.target.value)" required>
                    <option value="">Seleccione…</option>
                    <template x-for="r in $store.assignRoles.roles" :key="'rol-opt-'+r.id">
                        <option :value="r.id" x-text="r.rol"></option>
                    </template>
                </select>
            </div>
            <div class="mb-2">
                <label class="block text-sm font-medium mb-1 nunito-bold">Roles adicionales</label>
                <div class="max-h-48 overflow-auto border rounded p-2 space-y-1">
                    <template x-if="$store.assignRoles.rolesLoading">
                        <div class="text-sm text-gray-500">Cargando roles asignados…</div>
                    </template>
                    <template x-if="!$store.assignRoles.rolesLoading">
                        <div>
                            <template x-for="r in $store.assignRoles.roles" :key="'rol-check-'+r.id">
                                <label class="flex items-center gap-2 text-sm nunito-regular">
                                    <input type="checkbox" class="rounded accent-blue-600 dark:accent-blue-400 cursor-pointer disabled:cursor-not-allowed disabled:opacity-60"
                                        :value="String(r.id)"
                                        :checked="$store.assignRoles.rol_principal==String(r.id) || $store.assignRoles.rolesSelected.map(String).includes(String(r.id))"
                                        @change="$store.assignRoles.toggleRole(String(r.id))"
                                        :disabled="$store.assignRoles.isRoleDisabled(r.id)"
                                        :title="$store.assignRoles.isRoleDisabled(r.id) ? ($store.assignRoles.rol_principal==String(r.id) ? 'El rol principal siempre está asignado' : 'Combinación inválida con el rol seleccionado') : 'Asignar/Remover rol'" />
                                    <span x-text="r.rol"></span>
                                    <div class="ml-auto flex items-center gap-2">
                                        <span x-show="$store.assignRoles.rol_principal==String(r.id)"
                                            class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200">Principal</span>
                                        <span x-show="$store.assignRoles.rolesSelected.map(String).includes(String(r.id))"
                                            class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200">Asignado</span>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </template>
                </div>
                <p class="text-xs text-gray-500 mt-1">El rol principal no puede desmarcarse. Puedes agregar roles adicionales marcando las casillas.</p>
            </div>
            <div @modal-submit.window="if($event.detail.formId==='form-assign-role'){ $store.assignRoles.saveAssignMulti() }"></div>
        </x-admin.form-modal>
        @endperm
    </div>
</div>