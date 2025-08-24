{{-- resources/views/admin/partials/perfil.blade.php --}}

<div class="container mx-auto py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <!-- Panel lateral con avatar -->
        <div class="md:col-span-1">
            <div class="bg-white shadow-xl rounded-2xl border border-gray-400 border-opacity-50 p-6 text-center">
                <div class="relative inline-block">
                    <img src="https://ui-avatars.com/api/?name=Juan+Hernandez&background=0D8ABC&color=fff&size=128"
                        alt="Avatar" class="w-32 h-32 rounded-full mx-auto border-4 border-white">
                    <button
                        class="absolute bottom-2 right-2 bg-blue-600 hover:bg-blue-700 text-white rounded-full h-8 w-8 flex items-center justify-center">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                <h3 class="text-xl font-bold mt-4 text-gray-800">Juan Hernández</h3>
                <p class="text-sm text-gray-500">Desarrollador de Software</p>
                <p class="text-sm text-gray-500 mt-1">juan.h@unah.hn</p>
            </div>
        </div>

        <!-- Sección editable visual -->
        <div class="md:col-span-2 space-y-8">

            <!-- Información personal -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-400 border-opacity-50" x-data="{ showAlert: false }">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Información Personal</h3>
                <!-- Alerta de cambios guardados -->
                <div x-show="showAlert" x-transition class="mb-4">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">¡Cambios guardados!</strong>
                        <span class="block sm:inline">La información se guardó correctamente.</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Primer Nombre</label>
                        <input type="text" value="Juan" readonly
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Segundo Nombre</label>
                        <input type="text" value="Orlando" readonly
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Primer Apellido</label>
                        <input type="text" value="Hernández" readonly
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Segundo Apellido</label>
                        <input type="text" value="Alvarado" readonly
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Género</label>
                        <select disabled
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                            <option selected>Masculino</option>
                            <option>Femenino</option>
                            <option>Otro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">DNI</label>
                        <input type="text" value="0801-1977-12345" readonly
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cargo</label>
                        <input type="text" value="Desarrollador de Software" readonly
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                </div>

                <hr class="my-6 border-gray-200">

                <!-- Datos de cuenta -->
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Información de la Cuenta</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de Usuario (Login)</label>
                        <input type="text" value="Juanchi" readonly
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                        <input type="email" value="juan.h@unah.hn" readonly
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                </div>

                <div class="mt-8 text-right">
                    <button type="button"
                        class="bg-blue-600 text-white py-2 px-6 rounded-lg font-semibold hover:bg-blue-700 focus:ring-4 focus:ring-blue-600 focus:ring-opacity-50"
                        @click="showAlert = true; setTimeout(() => showAlert = false, 2000)">
                        Guardar Cambios
                    </button>
                </div>
            </div>

            <!-- Cambio de contraseña -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-400 border-opacity-50 " x-data="{ showPassAlert: false }">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Actualizar Contraseña</h3>
                <!-- Alerta de contraseña cambiada -->
                <div x-show="showPassAlert" x-transition class="mb-4">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">¡Contraseña cambiada!</strong>
                        <span class="block sm:inline">La contraseña se actualizó correctamente.</span>
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña Actual</label>
                    <input type="password" placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                        <input type="password" placeholder="••••••••"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar Contraseña</label>
                        <input type="password" placeholder="••••••••"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                </div>
                <div class="mt-8 text-right">
                    <button type="button"
                        class="bg-blue-600 text-white py-2 px-6 rounded-lg font-semibold hover:bg-blue-700 focus:ring-4 focus:ring-blue-600 focus:ring-opacity-50"
                        @click="showPassAlert = true; setTimeout(() => showPassAlert = false, 2000)">
                        Cambiar Contraseña
                    </button>
                </div>
            </div>

        </div>

    </div>
</div>
