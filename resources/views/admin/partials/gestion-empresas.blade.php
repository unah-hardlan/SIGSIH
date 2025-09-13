<div x-data="{
        tab: 'empresas',
        isEmpresaModalOpen: false,
        isEmpresaRegistradaModalOpen: false,
        isOficinaModalOpen: false,
        isDeleteEmpresaModalOpen: false,
        isDeleteEmpresaRegistradaModalOpen: false,
        isDeleteOficinaModalOpen: false,
        empresaToEdit: null,
        empresaRegistradaToEdit: null,
        oficinaToEdit: null,
        empresaToDelete: null,
        empresaRegistradaToDelete: null,
        oficinaToDelete: null,
        empresas: [
            {id: 1, nombre_empresa: 'Empresa Ejemplo', descripcion_empresa: 'Descripción de ejemplo', estado_empresa: 'Activo'},
            {id: 2, nombre_empresa: 'Soluciones S.A.', descripcion_empresa: 'Empresa de tecnología', estado_empresa: 'Activo'},
            {id: 3, nombre_empresa: 'Comercial XYZ', descripcion_empresa: 'Comercio mayorista', estado_empresa: 'Inactivo'}
        ],
        empresasRegistradas: [
            {id: 1, nombre_empresa: 'Empresa Registrada 1', descripcion_empresa: 'Desc 1', estado_empresa: 'Activo'},
            {id: 2, nombre_empresa: 'Empresa Registrada 2', descripcion_empresa: 'Desc 2', estado_empresa: 'Inactivo'}
        ],
        openEmpresaModal(edit = false, empresa = null) {
            this.isEmpresaModalOpen = true;
            this.empresaToEdit = edit ? empresa : null;
        },
        openEmpresaRegistradaModal(edit = false, empresa = null) {
            this.isEmpresaRegistradaModalOpen = true;
            this.empresaRegistradaToEdit = edit ? empresa : null;
        },
        openOficinaModal(edit = false, oficina = null) {
            this.isOficinaModalOpen = true;
            this.oficinaToEdit = edit ? oficina : null;
        },
        openDeleteEmpresaModal(empresa) {
            this.empresaToDelete = empresa;
            this.isDeleteEmpresaModalOpen = true;
        },
        openDeleteEmpresaRegistradaModal(empresa) {
            this.empresaRegistradaToDelete = empresa;
            this.isDeleteEmpresaRegistradaModalOpen = true;
        },
        openDeleteOficinaModal(oficina) {
            this.oficinaToDelete = oficina;
            this.isDeleteOficinaModalOpen = true;
        },
        deleteEmpresa() {
            if (this.empresaToDelete) {
                // Aquí iría la lógica para eliminar la empresa
                console.log('Eliminando empresa:', this.empresaToDelete);
                this.isDeleteEmpresaModalOpen = false;
                this.empresaToDelete = null;
            }
        },
        deleteEmpresaRegistrada() {
            if (this.empresaRegistradaToDelete) {
                // Eliminar de la lista local
                this.empresasRegistradas = this.empresasRegistradas.filter(e => e.id !== this.empresaRegistradaToDelete.id);
                console.log('Eliminando empresa registrada:', this.empresaRegistradaToDelete);
                this.isDeleteEmpresaRegistradaModalOpen = false;
                this.empresaRegistradaToDelete = null;
            }
        },
        deleteOficina() {
            if (this.oficinaToDelete) {
                // Eliminar de la lista local
                this.oficinas = this.oficinas.filter(o => o.id !== this.oficinaToDelete.id);
                console.log('Eliminando oficina:', this.oficinaToDelete);
                this.isDeleteOficinaModalOpen = false;
                this.oficinaToDelete = null;
            }
        },
        oficinas: [
            {id: 1, nombre: 'Oficina Central'},
            {id: 2, nombre: 'Sucursal Norte'},
            {id: 3, nombre: 'Sucursal Sur'}
        ]
    }" @include('partials.persist-tab', ['tabKey' => 'admin-gestion-empresas-tab'])
    @keydown.window.escape="isEmpresaModalOpen = false; isEmpresaRegistradaModalOpen = false; isOficinaModalOpen = false; isDeleteEmpresaModalOpen = false; isDeleteEmpresaRegistradaModalOpen = false; isDeleteOficinaModalOpen = false"
    @confirm-delete.window="
        if (isDeleteEmpresaModalOpen) {
            deleteEmpresa();
        } else if (isDeleteEmpresaRegistradaModalOpen) {
            deleteEmpresaRegistrada();
        } else if (isDeleteOficinaModalOpen) {
            deleteOficina();
        }
    ">

    <!-- Tabs -->
    <ul class="flex border-b nunito-bold mb-6 flex-wrap gap-2">
        <li @click="tab='empresas'"
            :class="tab==='empresas' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-100 hover:text-blue-500 dark:hover:text-blue-400 cursor-pointer'"
            class="pb-2 mr-4 nunito-bold">Empresas</li>
        <li @click="tab='form-nombre'"
            :class="tab==='form-nombre' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-100 hover:text-blue-500 dark:hover:text-blue-400 cursor-pointer'"
            class="pb-2 mr-4 nunito-bold">Empresas Registradas</li>
        <li @click="tab='oficinas'"
            :class="tab==='oficinas' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-100 hover:text-blue-500 dark:hover:text-blue-400 cursor-pointer'"
            class="pb-2 nunito-bold">Oficinas Empresa</li>
    </ul>

    <!-- TAB 1: Empresas Cliente -->
    <div x-show="tab==='empresas'" class="overflow-x-auto">
        <x-admin.tabla-mobile class="nunito-bold bg-white dark:bg-gray-900" titulo="Empresas">
            <x-slot name="filtros">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchEmpresa',
                    'filtrosSelect' => [
                        'estadoEmpresa' => [
                            'label' => 'Estados',
                            'options' => ['Activo', 'Inactivo']
                        ]
                    ],
                    'ordenarOptions' => [
                        'id' => 'ID',
                        'nombre_empresa' => 'Nombre',
                        'estado_empresa' => 'Estado'
                    ]
                ])
            </x-slot>
            <x-slot name="boton">
                <div class="flex flex-col gap-2 w-full sm:w-auto">
                    <button @click="openEmpresaModal(false)"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nueva
                        Empresa</button>
                    <a href="/admin/reportes-header?modulo=Empresas&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                       class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                </div>
            </x-slot>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">ID</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Nombre Empresa</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Estado</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="e in empresas" :key="e.id">
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4 nunito-regular" x-text="e.id"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="e.nombre_empresa"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="e.descripcion_empresa"></td>
                            <td class="py-2 px-4"><span class="px-2 py-1 rounded nunito-regular" :class="e.estado_empresa==='Activo' ? 'bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-100' : 'bg-red-700 text-red-100'" x-text="e.estado_empresa"></span></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click.prevent="openEmpresaModal(true, e)" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click.prevent="openDeleteEmpresaModal(e)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <x-slot name="mobileTemplate">
                <div class="space-y-4">
                    <template x-for="e in empresas" :key="e.id">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-lg font-bold text-gray-800" x-text="e.nombre_empresa"></span>
                                <span class="px-2 py-1 rounded text-xs font-semibold" :class="e.estado_empresa==='Activo' ? 'bg-green-100 text-green-700' : 'bg-red-700 text-red-100'" x-text="e.estado_empresa"></span>
                            </div>
                            <div class="text-sm text-gray-600 mb-1">ID: <span x-text="e.id"></span></div>
                            <div class="text-sm text-gray-600 mb-1">Descripción: <span x-text="e.descripcion_empresa"></span></div>
                            <div class="flex justify-end gap-2 mt-3">
                                <button @click="openEmpresaModal(true, e)" class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm"><i class="fas fa-edit"></i> Editar</button>
                                <button @click="openDeleteEmpresaModal(e)" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"><i class="fas fa-trash"></i> Eliminar</button>
                            </div>
                        </div>
                    </template>
                </div>
            </x-slot>
        </x-admin.tabla-mobile>
    </div>

    <!-- TAB 2: Empresas Registradas -->
    <div x-show="tab==='form-nombre'" class="overflow-x-auto">
        <x-admin.tabla-mobile class="nunito-bold bg-white dark:bg-gray-900" titulo="Empresas Registradas">
            <x-slot name="filtros">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchEmpresaRegistrada',
                    'filtrosSelect' => [
                        'estadoEmpresa' => [
                            'label' => 'Estados',
                            'options' => ['Activo', 'Inactivo']
                        ]
                    ],
                    'ordenarOptions' => [
                        'id' => 'ID',
                        'nombre_empresa' => 'Nombre'
                    ]
                ])
            </x-slot>
            <x-slot name="boton">
                <div class="flex flex-col gap-2 w-full sm:w-auto">
                    <button @click="openEmpresaRegistradaModal(false)"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Agregar empresa registrada</button>
                </div>
            </x-slot>
            <table class="min-w-full text-sm mt-2">
                <thead class="bg-gray-100 dark:bg-gray-800 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">ID</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Nombre Empresa</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Estado</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="empresa in empresasRegistradas" :key="empresa.id">
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4 nunito-regular" x-text="empresa.id"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="empresa.nombre_empresa"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="empresa.descripcion_empresa"></td>
                            <td class="py-2 px-4">
                                <span class="px-2 py-1 rounded nunito-regular"
                                    :class="empresa.estado_empresa === 'Activo' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'"
                                    x-text="empresa.estado_empresa"></span>
                            </td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click.prevent="openEmpresaRegistradaModal(true, empresa)"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i> </a>
                                <a href="#" @click.prevent="openDeleteEmpresaRegistradaModal(empresa)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <x-slot name="mobileTemplate">
                <div class="space-y-4">
                    <template x-for="empresa in empresasRegistradas" :key="empresa.id">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-lg font-bold text-gray-800" x-text="empresa.nombre_empresa"></span>
                                <span class="px-2 py-1 rounded text-xs font-semibold" :class="empresa.estado_empresa==='Activo' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'" x-text="empresa.estado_empresa"></span>
                            </div>
                            <div class="text-sm text-gray-600 mb-1">ID: <span x-text="empresa.id"></span></div>
                            <div class="text-sm text-gray-600 mb-1">Descripción: <span x-text="empresa.descripcion_empresa"></span></div>
                            <div class="flex justify-end gap-2 mt-3">
                                <button @click="openEmpresaRegistradaModal(true, empresa)" class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm"><i class="fas fa-edit"></i> Editar</button>
                                <button @click="openDeleteEmpresaRegistradaModal(empresa)" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"><i class="fas fa-trash"></i> Eliminar</button>
                            </div>
                        </div>
                    </template>
                </div>
            </x-slot>
        </x-admin.tabla-mobile>
    </div>

    <!-- TAB 3: Oficinas Empresa -->
    <div x-show="tab==='oficinas'" class="overflow-x-auto">
        <x-admin.tabla-mobile class="nunito-bold bg-white dark:bg-gray-900" titulo="Oficinas de las Empresas">
            <x-slot name="filtros">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchOficina',
                    'filtrosSelect' => [],
                    'ordenarOptions' => [
                        'id' => 'ID Oficina',
                        'nombre' => 'Nombre'
                    ]
                ])
            </x-slot>
            <x-slot name="boton">
                <div class="flex flex-col gap-2 w-full sm:w-auto">
                    <button @click="openOficinaModal(false)"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap font-bold text-sm">Nueva Oficina</button>
                </div>
            </x-slot>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">ID Oficina</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Nombre Oficina</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="oficina in oficinas" :key="oficina.id">
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4 nunito-regular" x-text="oficina.id"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="oficina.nombre"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click.prevent="openOficinaModal(true, oficina)"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click.prevent="openDeleteOficinaModal(oficina)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <x-slot name="mobileTemplate">
                <div class="space-y-4">
                    <template x-for="oficina in oficinas" :key="oficina.id">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-lg font-bold text-gray-800" x-text="oficina.nombre"></span>
                            </div>
                            <div class="text-sm text-gray-600 mb-1">ID Oficina: <span x-text="oficina.id"></span></div>
                            <div class="flex justify-end gap-2 mt-3">
                                <button @click="openOficinaModal(true, oficina)" class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm"><i class="fas fa-edit"></i> Editar</button>
                                <button @click="openDeleteOficinaModal(oficina)" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"><i class="fas fa-trash"></i> Eliminar</button>
                            </div>
                        </div>
                    </template>
                </div>
            </x-slot>
        </x-admin.tabla-mobile>
    </div>

    <!-- Modal Empresas Cliente -->
    <x-admin.form-modal 
        modalName="isEmpresaModalOpen" 
        title="Agregar Empresa" 
        submitLabel="Agregar Empresa"
        formId="empresa-form"
        maxWidth="max-w-md">
        
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold ">Empresa Registrada <span class="text-red-500">*</span></label>
            <select class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" required>
                <option value="">Seleccionar empresa registrada...</option>
                <template x-for="empresa in empresasRegistradas" :key="empresa.id">
                    <option :value="empresa.id" x-text="empresa.nombre_empresa"
                        :selected="empresaToEdit && empresaToEdit.id === empresa.id"></option>
                </template>
            </select>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Dirección <span class="text-red-500">*</span></label>
            <input type="text" class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" maxlength="255"
                :value="empresaToEdit ? empresaToEdit.direccion : ''"
                :placeholder="empresaToEdit ? '' : 'Ejemplo: Av. Principal 123'" required>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Oficina <span class="text-red-500">*</span></label>
            <select class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" required>
                <option value="">Seleccionar oficina...</option>
                <template x-for="oficina in oficinas" :key="oficina.id">
                    <option class="" :value="oficina.id" x-text="oficina.nombre"
                        :selected="empresaToEdit && empresaToEdit.oficina_id === oficina.id"></option>
                </template>
            </select>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Estado <span class="text-red-500">*</span></label>
            <select class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" maxlength="20" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                required>
                <option value="">Seleccionar estado</option>
                <option :selected="empresaToEdit && empresaToEdit.estado_empresa === 'Activo'">Activo</option>
                <option :selected="empresaToEdit && empresaToEdit.estado_empresa === 'Inactivo'">Inactivo
                </option>
            </select>
        </div>
    </x-admin.form-modal>

    <!-- Modal Empresas Registradas -->
    <x-admin.form-modal 
        modalName="isEmpresaRegistradaModalOpen" 
        title="Agregar Empresa Registrada" 
        submitLabel="Agregar Empresa Registrada"
        formId="empresa-registrada-form"
        maxWidth="max-w-lg">

        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Nombre de Empresa <span class="text-red-500">*</span></label>
            <input type="text" class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" maxlength="100"
                pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                :value="empresaRegistradaToEdit ? empresaRegistradaToEdit.nombre_empresa : ''"
                :placeholder="empresaRegistradaToEdit ? '' : 'Ejemplo S.A.'" required>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Descripción</label>
            <textarea class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" rows="2" maxlength="255"
                :placeholder="empresaRegistradaToEdit ? '' : 'Descripción de la empresa'"
                x-text="empresaRegistradaToEdit ? empresaRegistradaToEdit.descripcion_empresa : ''"></textarea>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Estado <span class="text-red-500">*</span></label>
            <select class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular" maxlength="20" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                required>
                <option value="">Seleccionar estado</option>
                <option
                    :selected="empresaRegistradaToEdit && empresaRegistradaToEdit.estado_empresa === 'Activo'">
                    Activo</option>
                <option
                    :selected="empresaRegistradaToEdit && empresaRegistradaToEdit.estado_empresa === 'Inactivo'">
                    Inactivo
                </option>
            </select>
        </div>
    </x-admin.form-modal>

    <!-- Modal Oficina -->
    <x-admin.form-modal 
        modalName="isOficinaModalOpen" 
        title="Agregar Oficina" 
        submitLabel="Agregar Oficina"
        formId="oficina-form"
        maxWidth="max-w-lg">

        <div class="mb-4">
            <label class="block font-medium mb-1 nunito-bold">Nombre de Oficina</label>
            <input type="text" class="border border-gray-300 rounded px-3 py-2 w-full nunito-regular"
                :value="oficinaToEdit ? oficinaToEdit.nombre : ''"
                :placeholder="oficinaToEdit ? '' : 'Oficina Central'">
        </div>
    </x-admin.form-modal>

    <!-- Confirmation Modals -->
    <!-- Modal de confirmación para eliminar empresa cliente -->
    <x-admin.confirmation-modal 
        modal-name="isDeleteEmpresaModalOpen"
        title="Eliminar Empresa Cliente"
        item-to-delete="empresaToDelete"
        item-name-property="nombre_empresa"
        message="¿Estás seguro de que deseas eliminar la empresa cliente"
    />

    <!-- Modal de confirmación para eliminar empresa registrada -->
    <x-admin.confirmation-modal 
        modal-name="isDeleteEmpresaRegistradaModalOpen"
        title="Eliminar Empresa Registrada"
        item-to-delete="empresaRegistradaToDelete"
        item-name-property="nombre_empresa"
        message="¿Estás seguro de que deseas eliminar la empresa registrada"
    />

    <!-- Modal de confirmación para eliminar oficina -->
    <x-admin.confirmation-modal 
        modal-name="isDeleteOficinaModalOpen"
        title="Eliminar Oficina"
        item-to-delete="oficinaToDelete"
        item-name-property="nombre"
        message="¿Estás seguro de que deseas eliminar la oficina"
    />
</div>
