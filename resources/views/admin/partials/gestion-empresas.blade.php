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
    @keydown.window.escape="isEmpresaModalOpen = false; isEmpresaRegistradaModalOpen = false; isOficinaModalOpen = false; isDeleteEmpresaModalOpen = false; isDeleteEmpresaRegistradaModalOpen = false; isDeleteOficinaModalOpen = false">

    <!-- Tabs -->
    <ul class="flex border-b nunito-bold mb-6 flex-wrap gap-2">
        <li @click="tab='empresas'"
            :class="tab==='empresas' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-100 hover:text-blue-500 cursor-pointer'"
            class="pb-2 mr-4 nunito-bold">Empresas</li>
        <li @click="tab='form-nombre'"
            :class="tab==='form-nombre' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-100 hover:text-blue-500 cursor-pointer'"
            class="pb-2 mr-4 nunito-bold">Empresas Registradas</li>
        <li @click="tab='oficinas'"
            :class="tab==='oficinas' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-100 hover:text-blue-500 cursor-pointer'"
            class="pb-2 nunito-bold">Oficinas Empresa</li>
    </ul>

    <!-- TAB 1: Empresas Cliente -->
    <div x-show="tab==='empresas'" class="overflow-x-auto">
        <h2 class="text-lg font-semibold mb-4 nunito-bold">Empresas</h2>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 md:gap-4 w-full mb-4">
            <div class="flex flex-row items-center space-x-4 w-full md:w-auto">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchEmpresa',
                    'filtrosSelect' => [
                        'tipoEmpresa' => ['label' => 'Tipo', 'options' => ['Pública', 'Privada']]
                    ],
                    'ordenarOptions' => [
                        'nombre_empresa' => 'Nombre',
                        'fecha_registro' => 'Fecha Registro'
                    ]
                ])
            </div>
            <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto justify-end">
                <button @click="openEmpresaModal(false)"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg nunito-regular transition whitespace-nowrap w-full md:w-auto text-sm">
                    Nueva Empresa
                </button>
                <a href="/admin/reportes-header?modulo=Empresas&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                   class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                    <i class="fas fa-file-alt"></i> Generar Reporte
                </a>
            </div>
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-800 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">ID</th>
                    <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Fecha Registro</th>
                    <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Nombre Empresa</th>
                    <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Dirección</th>
                    <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Oficina</th>
                    <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b nunito-regular">
                    <td class="py-2 px-4 nunito-regular">1</td>
                    <td class="py-2 px-4 nunito-regular">2025-08-03</td>
                    <td class="py-2 px-4 nunito-regular">Empresa Ejemplo</td>
                    <td class="py-2 px-4 nunito-regular">Av. Principal 123</td>
                    <td class="py-2 px-4 nunito-regular">Oficina Central</td>
                    <td class="py-2 px-4 flex gap-2">
                        <a href="#"
                            @click.prevent="openEmpresaModal(true, {id: 1, nombre_empresa: 'Empresa Ejemplo', descripcion_empresa: 'Descripción de ejemplo', estado_empresa: 'Activo'})"
                            class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                        <a href="#" @click.prevent="openDeleteEmpresaModal({id: 1, nombre_empresa: 'Empresa Ejemplo', descripcion_empresa: 'Descripción de ejemplo', estado_empresa: 'Activo'})" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- TAB 2: Empresas Registradas -->
    <div x-show="tab==='form-nombre'" class="overflow-x-auto">
        <h2 class="text-lg font-semibold mb-4 nunito-bold">Empresas Registradas</h2>
        <!-- Filtros -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 md:gap-4 w-full mb-4">
            <div class="flex flex-row items-center space-x-4 w-full md:w-auto">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchEmpresaRegistrada',
                    'filtrosSelect' => [
                        'estadoEmpresa' => ['label' => 'Estado', 'options' => ['Activo', 'Inactivo']]
                    ],
                    'ordenarOptions' => [
                        'nombre_empresa' => 'Nombre',
                        'id' => 'ID'
                    ]
                ])
            </div>
            <button @click="openEmpresaRegistradaModal(false)"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg nunito-regular transition whitespace-nowrap w-full md:w-auto text-sm">
                Agregar empresa registrada
            </button>
        </div>
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
                                class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click.prevent="openDeleteEmpresaRegistradaModal(empresa)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- TAB 3: Oficinas Empresa -->
    <div x-show="tab==='oficinas'" class="overflow-x-auto">
        <h2 class="text-lg font-semibold mb-4 nunito-bold">Oficinas de las Empresas</h2>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 md:gap-4 w-full mb-4">
            <div class="flex flex-row items-center space-x-4 w-full md:w-auto">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchOficina',
                    'filtrosSelect' => [
                        'departamento' => ['label' => 'Departamento', 'options' => ['Ventas', 'Soporte']]
                    ],
                    'ordenarOptions' => [
                        'nombre' => 'Nombre',
                        'id' => 'ID Oficina'
                    ]
                ])
            </div>
            <button @click="openOficinaModal(false)"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg nunito-regular transition whitespace-nowrap font-bold w-full md:w-auto text-sm">
                Nueva Oficina
            </button>
        </div>
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
    <div x-show="isDeleteEmpresaModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
         @click.away="isDeleteEmpresaModalOpen = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md" @click.stop>
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-xl font-bold text-gray-700 nunito-bold">Eliminar Empresa Cliente</h3>
                <button @click="isDeleteEmpresaModalOpen = false" class="text-gray-500 hover:text-gray-800"><i class="fas fa-times"></i></button>
            </div>
            <div class="mt-4">
                <p class="nunito-regular">¿Estás seguro de que deseas eliminar la empresa cliente <strong class="nunito-regular" x-text="empresaToDelete ? empresaToDelete.nombre_empresa : ''"></strong>? Esta acción no se puede deshacer.</p>
            </div>
            <div class="flex justify-end pt-4">
                <button type="button" @click="isDeleteEmpresaModalOpen = false" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 mr-2 nunito-regular">Cancelar</button>
                <button type="submit" @click="deleteEmpresa()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 nunito-regular">Eliminar</button>
            </div>
        </div>
    </div>

    <div x-show="isDeleteEmpresaRegistradaModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
         @click.away="isDeleteEmpresaRegistradaModalOpen = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md" @click.stop>
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-xl font-bold text-gray-700 nunito-bold">Eliminar Empresa Registrada</h3>
                <button @click="isDeleteEmpresaRegistradaModalOpen = false" class="text-gray-500 hover:text-gray-800"><i class="fas fa-times"></i></button>
            </div>
            <div class="mt-4">
                <p class="nunito-regular">¿Estás seguro de que deseas eliminar la empresa registrada <strong class="nunito-regular" x-text="empresaRegistradaToDelete ? empresaRegistradaToDelete.nombre_empresa : ''"></strong>? Esta acción no se puede deshacer.</p>
            </div>
            <div class="flex justify-end pt-4">
                <button type="button" @click="isDeleteEmpresaRegistradaModalOpen = false" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 mr-2 nunito-regular">Cancelar</button>
                <button type="submit" @click="deleteEmpresaRegistrada()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 nunito-regular">Eliminar</button>
            </div>
        </div>
    </div>

    <div x-show="isDeleteOficinaModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
         @click.away="isDeleteOficinaModalOpen = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md" @click.stop>
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-xl font-bold text-gray-700 nunito-bold">Eliminar Oficina</h3>
                <button @click="isDeleteOficinaModalOpen = false" class="text-gray-500 hover:text-gray-800"><i class="fas fa-times"></i></button>
            </div>
            <div class="mt-4">
                <p class="nunito-regular">¿Estás seguro de que deseas eliminar la oficina <strong class="nunito-regular" x-text="oficinaToDelete ? oficinaToDelete.nombre : ''"></strong>? Esta acción no se puede deshacer.</p>
            </div>
            <div class="flex justify-end pt-4">
                <button type="button" @click="isDeleteOficinaModalOpen = false" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 mr-2 nunito-regular">Cancelar</button>
                <button type="submit" @click="deleteOficina()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 nunito-regular">Eliminar</button>
            </div>
        </div>
    </div>
</div>
