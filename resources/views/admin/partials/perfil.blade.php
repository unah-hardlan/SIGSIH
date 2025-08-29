{{-- resources/views/admin/partials/perfil.blade.php --}}

<div class="container mx-auto py-8" x-data="perfilPage()" x-init="init()">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <!-- Panel lateral con avatar -->
        <div class="md:col-span-1">
            <div class="bg-white shadow-xl rounded-2xl border border-gray-400 border-opacity-50 p-6 text-center">
                <div class="relative inline-block">
                    <img :src="avatarUrl || (personaAvatar ? personaAvatar : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(displayNameComputed) + '&background=0D8ABC&color=fff&size=128')"
                        alt="Avatar" class="w-32 h-32 rounded-full mx-auto border-4 border-white object-cover">
                    <div class="absolute bottom-2 right-2 flex gap-2">
                        <label
                            class="bg-blue-600 hover:bg-blue-700 text-white rounded-full h-8 w-8 flex items-center justify-center cursor-pointer shadow">
                            <i class="fas fa-camera"></i>
                            <input type="file" class="hidden" @change="onAvatarChange($event)">
                        </label>
                        <button @click="removeAvatar" title="Eliminar foto"
                            class="bg-red-600 hover:bg-red-700 text-white rounded-full h-8 w-8 flex items-center justify-center shadow">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <h3 class="text-xl font-bold mt-4 text-gray-800" x-text="displayNameComputed"></h3>
                <p class="text-sm text-gray-500" x-text="form.cargo || '-' "></p>
                <p class="text-sm text-gray-500 mt-1" x-text="email"></p>

                <!-- Botón de guardar para cambios de avatar -->
                <div class="mt-6" x-show="hasChanges">
                    <button type="button" @click="guardar()" :disabled="saving"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:ring-4 focus:ring-blue-600 focus:ring-opacity-50 transition duration-200">
                        <span x-show="!saving">Guardar Cambios</span>
                        <span x-show="saving" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2z" />
                            </svg>
                            Guardando…
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sección editable -->
        <div class="md:col-span-2 space-y-8">
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-400 border-opacity-50">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Completa tu Información Personal</h3>
                <!-- Alerta de éxito -->
                <div x-show="success" x-transition class="mb-4">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                        role="alert">
                        <strong class="font-bold">¡Información actualizada!</strong>
                        <span class="block sm:inline">Tus datos se guardaron correctamente.</span>
                    </div>
                </div>

                <!-- Alerta de cambios no guardados -->
                <div x-show="hasChanges" x-transition class="mb-4">
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative"
                        role="alert">
                        <strong class="font-bold">¡Hay cambios sin guardar!</strong>
                        <span class="block sm:inline">Guarde los cambios para que surtan efecto.</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Primer Nombre</label>
                        <input type="text" x-model="form.primer_nombre" @input="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Segundo Nombre</label>
                        <input type="text" x-model="form.segundo_nombre" @input="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Primer Apellido</label>
                        <input type="text" x-model="form.primer_apellido" @input="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Segundo Apellido</label>
                        <input type="text" x-model="form.segundo_apellido" @input="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Género</label>
                        <select x-model="form.id_genero_fk" @change="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                            <option value="">Seleccione…</option>
                            <option value="1">Masculino</option>
                            <option value="2">Femenino</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">DNI</label>
                        <input type="text" x-model="form.dni" @input="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cargo</label>
                        <input type="text" x-model="form.cargo" @input="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo Persona</label>
                        <select x-model="form.id_tipo_persona_fk" @change="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                            <option value="">Seleccione…</option>
                            <option value="1">Empleado</option>
                            <option value="2">Cliente</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Perfil</label>
                        <select x-model="form.id_perfil_fk" @change="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800">
                            <option value="">Seleccione…</option>
                            <option value="1">General</option>
                            <option value="2">Técnico</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Sección de cambio de contraseña -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-400 border-opacity-50">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Cambiar Contraseña</h3>

                <!-- Alerta de éxito para contraseña -->
                <div x-show="passwordSuccess" x-transition class="mb-4">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                        role="alert">
                        <strong class="font-bold">¡Contraseña actualizada!</strong>
                        <span class="block sm:inline">Tu contraseña se cambió correctamente.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña Actual</label>
                        <input type="password" x-model="passwordForm.current_password"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800"
                            placeholder="Ingresa tu contraseña actual">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                        <input type="password" x-model="passwordForm.password"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800"
                            placeholder="Mínimo 8 caracteres">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                        <input type="password" x-model="passwordForm.password_confirmation"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800"
                            placeholder="Repite la nueva contraseña">
                    </div>
                </div>

                <div class="mt-8 text-right">
                    <button type="button" @click="cambiarPassword()" :disabled="changingPassword"
                        class="bg-red-600 text-white py-2 px-6 rounded-lg font-semibold hover:bg-red-700 focus:ring-4 focus:ring-red-600 focus:ring-opacity-50">
                        <span x-show="!changingPassword">Cambiar Contraseña</span>
                        <span x-show="changingPassword" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2z" />
                            </svg>
                            Cambiando…
                        </span>
                    </button>
                </div>
            </div>

        </div>

    </div>
</div>

{{-- El JavaScript ahora está en resources/js/perfil.js --}}