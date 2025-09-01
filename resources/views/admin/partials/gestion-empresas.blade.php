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
            :class="tab==='empresas' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 hover:text-blue-500 cursor-pointer'"
            class="pb-2 mr-4">Empresas</li>
        <li @click="tab='form-nombre'"
            :class="tab==='form-nombre' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 hover:text-blue-500 cursor-pointer'"
            class="pb-2 mr-4">Empresas Registradas</li>
        <li @click="tab='oficinas'"
            :class="tab==='oficinas' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 hover:text-blue-500 cursor-pointer'"
            class="pb-2">Oficinas Empresa</li>
    </ul>

    <!-- TAB 1: Empresas Cliente -->
    <div x-show="tab==='empresas'" class="overflow-x-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 md:gap-4 w-full mb-4">
            <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 w-full">
                <input type="text" placeholder="Buscar empresa..."
                    class="border rounded px-3 py-2 text-sm w-full md:w-56" />
                <select class="border rounded px-3 py-2 text-sm w-full md:w-48">
                    <option>Todos los tipos</option>
                    <option>Pública</option>
                    <option>Privada</option>
                </select>
                <select class="border rounded px-3 py-2 text-sm w-full md:w-48">
                    <option>Ordenar por Nombre</option>
                    <option>Fecha Registro</option>
                </select>
            </div>
            <div class="flex flex-col gap-2 w-full md:w-auto">
                <button @click="openEmpresaModal(false)"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg nunito-bold transition whitespace-nowrap font-bold w-full md:w-auto">
                    Nueva Empresa
                </button>
                <a href="/admin/reportes-header?modulo=Empresas&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                   class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2">
                    <i class="fas fa-file-alt"></i> Generar Reporte
                </a>
            </div>
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left">ID</th>
                    <th class="py-2 px-4 text-left">Fecha Registro</th>
                    <th class="py-2 px-4 text-left">Nombre Empresa</th>
                    <th class="py-2 px-4 text-left">Dirección</th>
                    <th class="py-2 px-4 text-left">Oficina</th>
                    <th class="py-2 px-4 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b nunito-regular">
                    <td class="py-2 px-4">1</td>
                    <td class="py-2 px-4">2025-08-03</td>
                    <td class="py-2 px-4">Empresa Ejemplo</td>
                    <td class="py-2 px-4">Av. Principal 123</td>
                    <td class="py-2 px-4">Oficina Central</td>
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
        <!-- Filtros -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 md:gap-4 w-full mb-4">
            <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 w-full">
                <input type="text" placeholder="Buscar por nombre..."
                    class="border rounded px-3 py-2 text-sm w-full md:w-56" />
                <input type="text" placeholder="Buscar por descripción..."
                    class="border rounded px-3 py-2 text-sm w-full md:w-56" />
                <select class="border rounded px-3 py-2 text-sm w-full md:w-48">
                    <option>Ordenar por Nombre</option>
                    <option>Ordenar por ID</option>
                </select>
            </div>
            <button @click="openEmpresaRegistradaModal(false)"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg nunito-bold transition whitespace-nowrap font-bold w-full md:w-auto">
                Agregar empresa registrada
            </button>
        </div>
        <table class="min-w-full text-sm mt-2">
            <thead class="bg-gray-100 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left">ID</th>
                    <th class="py-2 px-4 text-left">Nombre Empresa</th>
                    <th class="py-2 px-4 text-left">Descripción</th>
                    <th class="py-2 px-4 text-left">Estado</th>
                    <th class="py-2 px-4 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="empresa in empresasRegistradas" :key="empresa.id">
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4" x-text="empresa.id"></td>
                        <td class="py-2 px-4" x-text="empresa.nombre_empresa"></td>
                        <td class="py-2 px-4" x-text="empresa.descripcion_empresa"></td>
                        <td class="py-2 px-4">
                            <span class="px-2 py-1 rounded"
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
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 md:gap-4 w-full mb-4">
            <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 w-full">
                <input type="text" placeholder="Buscar oficina..."
                    class="border rounded px-3 py-2 text-sm w-full md:w-56" />
                <select class="border rounded px-3 py-2 text-sm w-full md:w-48">
                    <option>Todos los departamentos</option>
                    <option>Ventas</option>
                    <option>Soporte</option>
                </select>
                <select class="border rounded px-3 py-2 text-sm w-full md:w-48">
                    <option>Ordenar por Nombre</option>
                    <option>Ordenar por ID Oficina</option>
                </select>
            </div>
            <button @click="openOficinaModal(false)"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg nunito-bold transition whitespace-nowrap font-bold w-full md:w-auto">
                Nueva Oficina
            </button>
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left">ID Oficina</th>
                    <th class="py-2 px-4 text-left">Nombre Oficina</th>
                    <th class="py-2 px-4 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="oficina in oficinas" :key="oficina.id">
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4" x-text="oficina.id"></td>
                        <td class="py-2 px-4" x-text="oficina.nombre"></td>
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
    <div x-show="isEmpresaModalOpen" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
        @click.self="isEmpresaModalOpen = false">
            <div class="bg-white rounded-xl shadow-lg p-4 w-auto max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg relative mx-4 sm:mx-2">
            <h2 class="text-xl font-bold mb-4" x-text="empresaToEdit ? 'Editar Empresa' : 'Agregar Empresa'"></h2>
            <form @submit.prevent="isEmpresaModalOpen = false">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block font-medium mb-1">Empresa Registrada <span class="text-red-500">*</span></label>
                        <select class="border rounded px-3 py-2 w-full" required>
                            <option value="">Seleccionar empresa registrada...</option>
                            <template x-for="empresa in empresasRegistradas" :key="empresa.id">
                                <option :value="empresa.id" x-text="empresa.nombre_empresa"
                                    :selected="empresaToEdit && empresaToEdit.id === empresa.id"></option>
                            </template>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block font-medium mb-1">Dirección <span class="text-red-500">*</span></label>
                        <input type="text" class="border rounded px-3 py-2 w-full" maxlength="255"
                            :value="empresaToEdit ? empresaToEdit.direccion : ''"
                            :placeholder="empresaToEdit ? '' : 'Ejemplo: Av. Principal 123'" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block font-medium mb-1">Oficina <span class="text-red-500">*</span></label>
                        <select class="border rounded px-3 py-2 w-full" required>
                            <option value="">Seleccionar oficina...</option>
                            <template x-for="oficina in oficinas" :key="oficina.id">
                                <option :value="oficina.id" x-text="oficina.nombre"
                                    :selected="empresaToEdit && empresaToEdit.oficina_id === oficina.id"></option>
                            </template>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block font-medium mb-1">Estado <span class="text-red-500">*</span></label>
                        <select class="border rounded px-3 py-2 w-full" maxlength="20" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                            required>
                            <option value="">Seleccionar estado</option>
                            <option :selected="empresaToEdit && empresaToEdit.estado_empresa === 'Activo'">Activo</option>
                            <option :selected="empresaToEdit && empresaToEdit.estado_empresa === 'Inactivo'">Inactivo
                            </option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="isEmpresaModalOpen = false"
                        class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                        x-text="empresaToEdit ? 'Guardar Cambios' : 'Agregar Empresa'"></button>
                </div>
            </form>
            <button @click="isEmpresaModalOpen = false"
                class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-2xl">
                &times;
            </button>
        </div>
    </div>

    <!-- Modal Empresas Registradas -->
    <div x-show="isEmpresaRegistradaModalOpen" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
        @click.self="isEmpresaRegistradaModalOpen = false">
            <div class="bg-white rounded-xl shadow-lg p-4 w-auto max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg relative mx-4 sm:mx-2">
            <h2 class="text-xl font-bold mb-4"
                x-text="empresaRegistradaToEdit ? 'Editar Empresa Registrada' : 'Agregar Empresa Registrada'"></h2>
            <form @submit.prevent="isEmpresaRegistradaModalOpen = false">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block font-medium mb-1">Nombre de Empresa <span class="text-red-500">*</span></label>
                        <input type="text" class="border rounded px-3 py-2 w-full" maxlength="100"
                            pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                            :value="empresaRegistradaToEdit ? empresaRegistradaToEdit.nombre_empresa : ''"
                            :placeholder="empresaRegistradaToEdit ? '' : 'Ejemplo S.A.'" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block font-medium mb-1">Descripción</label>
                        <textarea class="border rounded px-3 py-2 w-full" rows="2" maxlength="255"
                            :placeholder="empresaRegistradaToEdit ? '' : 'Descripción de la empresa'"
                            x-text="empresaRegistradaToEdit ? empresaRegistradaToEdit.descripcion_empresa : ''"></textarea>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Estado <span class="text-red-500">*</span></label>
                        <select class="border rounded px-3 py-2 w-full" maxlength="20" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
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
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="isEmpresaRegistradaModalOpen = false"
                        class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                        x-text="empresaRegistradaToEdit ? 'Guardar Cambios' : 'Agregar Empresa Registrada'"></button>
                </div>
            </form>
            <button @click="isEmpresaRegistradaModalOpen = false"
                class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-2xl">
                &times;
            </button>
        </div>
    </div>

    <!-- Modal Oficina -->
    <div x-show="isOficinaModalOpen" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
        @click.self="isOficinaModalOpen = false">
            <div class="bg-white rounded-xl shadow-lg p-4 w-auto max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg relative mx-4 sm:mx-2">
            <h2 class="text-xl font-bold mb-4" x-text="oficinaToEdit ? 'Editar Oficina' : 'Agregar Oficina'"></h2>
            <form @submit.prevent="isOficinaModalOpen = false">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block font-medium mb-1">Nombre de Oficina</label>
                        <input type="text" class="border rounded px-3 py-2 w-full"
                            :value="oficinaToEdit ? oficinaToEdit.nombre : ''"
                            :placeholder="oficinaToEdit ? '' : 'Oficina Central'">
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="isOficinaModalOpen = false"
                        class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                        x-text="oficinaToEdit ? 'Guardar Cambios' : 'Agregar Oficina'"></button>
                </div>
            </form>
            <button @click="isOficinaModalOpen = false"
                class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-2xl">
                &times;
            </button>
        </div>
    </div>

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
                <h3 class="text-xl font-bold text-gray-700">Eliminar Empresa Cliente</h3>
                <button @click="isDeleteEmpresaModalOpen = false" class="text-gray-500 hover:text-gray-800"><i class="fas fa-times"></i></button>
            </div>
            <div class="mt-4">
                <p>¿Estás seguro de que deseas eliminar la empresa cliente <strong x-text="empresaToDelete ? empresaToDelete.nombre_empresa : ''"></strong>? Esta acción no se puede deshacer.</p>
            </div>
            <div class="flex justify-end pt-4">
                <button type="button" @click="isDeleteEmpresaModalOpen = false" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 mr-2">Cancelar</button>
                <button type="submit" @click="deleteEmpresa()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Eliminar</button>
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
                <h3 class="text-xl font-bold text-gray-700">Eliminar Empresa Registrada</h3>
                <button @click="isDeleteEmpresaRegistradaModalOpen = false" class="text-gray-500 hover:text-gray-800"><i class="fas fa-times"></i></button>
            </div>
            <div class="mt-4">
                <p>¿Estás seguro de que deseas eliminar la empresa registrada <strong x-text="empresaRegistradaToDelete ? empresaRegistradaToDelete.nombre_empresa : ''"></strong>? Esta acción no se puede deshacer.</p>
            </div>
            <div class="flex justify-end pt-4">
                <button type="button" @click="isDeleteEmpresaRegistradaModalOpen = false" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 mr-2">Cancelar</button>
                <button type="submit" @click="deleteEmpresaRegistrada()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Eliminar</button>
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
                <h3 class="text-xl font-bold text-gray-700">Eliminar Oficina</h3>
                <button @click="isDeleteOficinaModalOpen = false" class="text-gray-500 hover:text-gray-800"><i class="fas fa-times"></i></button>
            </div>
            <div class="mt-4">
                <p>¿Estás seguro de que deseas eliminar la oficina <strong x-text="oficinaToDelete ? oficinaToDelete.nombre : ''"></strong>? Esta acción no se puede deshacer.</p>
            </div>
            <div class="flex justify-end pt-4">
                <button type="button" @click="isDeleteOficinaModalOpen = false" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 mr-2">Cancelar</button>
                <button type="submit" @click="deleteOficina()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Eliminar</button>
            </div>
        </div>
    </div>
</div>
