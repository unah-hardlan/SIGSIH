{{-- resources/views/admin/partials/gestion-db.blade.php --}}

<div class="max-w-4xl mx-auto py-8" x-data="{
        tab: localStorage.getItem('dbTab') || 'respaldo',
        showModal: false,
        modalMsg: '',
        openModal(msg) { this.modalMsg = msg; this.showModal = true; },
        closeModal() { this.showModal = false; },
        estadoConexion: 'inicial'
     }">

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2 nunito-bold">Gestión de Base de Datos</h2>

        <div class="flex border-b mb-4 space-x-4 text-base">
            <button @click="tab = 'respaldo'; localStorage.setItem('dbTab', 'respaldo')"
                :class="tab === 'respaldo' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-blue-600'"
                class="pb-2 px-2 focus:outline-none transition nunito-regular">Respaldo</button>
            <button @click="tab = 'restore'; localStorage.setItem('dbTab', 'restore')"
                :class="tab === 'restore' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-blue-600'"
                class="pb-2 px-2 focus:outline-none transition nunito-regular">Restaurar</button>
        </div>

        <!-- RESPALDO -->
        <div x-show="tab === 'respaldo'" class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2 nunito-bold">Respaldar base de datos</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 nunito-bold">Servidor</label>
                    <input type="text" value="(local)" class="w-full border rounded px-3 py-2 nunito-regular" />
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 nunito-bold">Base de Datos</label>
                    <input type="text" value="SIGSIH" class="w-full border rounded px-3 py-2 nunito-regular" />
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 nunito-bold">Usuario</label>
                    <input type="text" value="sa" class="w-full border rounded px-3 py-2 nunito-regular" />
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 nunito-bold">Contraseña</label>
                    <input type="password" value="123456" class="w-full border rounded px-3 py-2 nunito-regular" />
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700 nunito-bold">Guardar respaldo en:</label>
                    <input type="text" value="C:\backups\SIGSIH.bak" class="w-full border rounded px-3 py-2 nunito-regular" />
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-4 items-center">
                <!-- Simulación -->
                <button @click="
                        estadoConexion = 'cargando';
                        setTimeout(() => { estadoConexion = 'exito'; }, 2000);
                    "
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-semibold transition flex items-center justify-center gap-2 min-w-[150px] nunito-regular"
                    :disabled="estadoConexion === 'cargando'" style="min-width:150px;">
                    <template x-if="estadoConexion === 'inicial'">
                        <span class="nunito-regular text-sm">Probar conexión</span>
                    </template>
                    <template x-if="estadoConexion === 'cargando'">
                        <svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span class="nunito-regular">Conectando...</span>
                    </template>
                    <template x-if="estadoConexion === 'exito'">
                        <span class="flex items-center gap-1 nunito-regular">
                            <div class="w-2.5 h-2.5 rounded-full bg-green-400 border border-white"></div> Éxito
                        </span>
                    </template>
                </button>

                <button @click="openModal('¿Deseas confirmar el respaldo de la base de datos?')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold transition nunito-regular text-sm">
                    Respaldar
                </button>
            </div>
        </div>

        <!-- RESTORE -->
        <div x-show="tab === 'restore'" class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2 nunito-bold">Restaurar base de datos</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 nunito-bold">Servidor Local</label>
                    <input type="text" value="(local)" class="w-full border rounded px-3 py-2 nunito-regular" />
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 nunito-bold">Base de Datos</label>
                    <input type="text" value="SIGSIH" class="w-full border rounded px-3 py-2 nunito-regular" />
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700 nunito-bold">Seleccionar archivo .bak</label>
                    <div class="flex gap-2">
                        <input type="text" value="C:\backups\SIGSIH.bak" class="w-full border rounded px-3 py-2 nunito-regular" />
                        <button class="transitions duration-200 ease-in-out bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded nunito-regular">Examinar</button>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <button @click="openModal('¿Deseas confirmar la restauración de la base de datos?')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold transition nunito-regular text-sm">
                    Restaurar
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
            <div class="text-gray-800 text-lg font-semibold mb-4 nunito-bold" x-text="modalMsg"></div>
            <div class="flex justify-end gap-2 mt-4">
                <button @click="closeModal()"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded nunito-regular">Cancelar</button>
                <button @click="closeModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded nunito-regular">Aceptar</button>
            </div>
        </div>
    </div>
</div>
