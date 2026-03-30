{{-- resources/views/admin/partials/perfil.blade.php --}}

<div class="container mx-auto py-8 dark:bg-gray-900 min-h-screen" x-data="perfilPage()" x-init="init()">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <div class="md:col-span-1">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-400 dark:border-gray-700 border-opacity-50 p-6 text-center">
                <div class="relative inline-block">
                    <img :src="avatarUrl || (personaAvatar ? personaAvatar : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(displayNameComputed) + '&background=0D8ABC&color=fff&size=128')"
                        alt="Avatar" class="w-32 h-32 rounded-full mx-auto border-4 border-white object-cover">
                    <div class="absolute left-1/2 bottom-0 transform -translate-x-1/2 translate-y-1/2 flex gap-2">
                        @perm(['Perfil'], 'actualizacion')
                        <label
                            class="bg-blue-600 hover:bg-blue-700 text-white rounded-full h-8 w-8 flex items-center justify-center cursor-pointer shadow" title="Cambiar foto">
                            <i class="fas fa-camera"></i>
                            <input type="file" class="hidden" accept="image/jpeg,image/jpg,image/png,image/webp" @change="onAvatarChange($event)">
                        </label>
                        @else
                        <span class="bg-gray-400 text-white rounded-full h-8 w-8 flex items-center justify-center shadow cursor-not-allowed" title="Sin permiso para actualizar"><i class="fas fa-camera"></i></span>
                        @endperm
                        @perm(['Perfil'], 'actualizacion')
                        <button @click="removeAvatar" title="Eliminar foto"
                            class="bg-red-600 hover:bg-red-700 text-white rounded-full h-8 w-8 flex items-center justify-center shadow">
                            <i class="fas fa-trash"></i>
                        </button>
                        @else
                        <button disabled title="Sin permiso" class="bg-red-300 text-white rounded-full h-8 w-8 flex items-center justify-center shadow cursor-not-allowed"><i class="fas fa-trash"></i></button>
                        @endperm
                    </div>
                </div>
                <h3 class="text-xl font-bold mt-4 text-gray-800 dark:text-white nunito-bold" x-text="displayNameComputed"></h3>

                <p class="text-sm text-gray-500 dark:text-gray-300 mt-1 nunito-regular" x-text="email"></p>

                <div class="mt-6" x-show="hasChanges">
                    @perm(['Perfil'], 'actualizacion')
                    <button type="button" @click="guardar()" :disabled="saving"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:ring-4 focus:ring-blue-600 focus:ring-opacity-50 transition duration-200 nunito-regular">
                        <span x-show="!saving" class="nunito-regular">Guardar Cambios</span>
                        <span x-show="saving" class="inline-flex items-center nunito-regular">
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
                    @else
                    <button disabled title="Sin permiso para actualizar" class="w-full bg-gray-400 text-white py-2 px-4 rounded-lg font-semibold cursor-not-allowed nunito-regular">Guardar Cambios</button>
                    @endperm
                </div>
            </div>
        </div>

        <div class="md:col-span-2 space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-400 dark:border-gray-700 border-opacity-50">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6 nunito-bold">Completa tu Información Personal</h3>
                <div x-show="typeof $data !== 'undefined' && $data.errorBanner" x-transition class="mb-4">
                    <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded relative" role="alert" x-cloak>
                        <strong class="font-bold nunito-bold">Verifica los datos:</strong>
                        <span class="block sm:inline nunito-regular" x-text="typeof $data !== 'undefined' && $data.errorBanner ? $data.errorBanner : ''"></span>
                    </div>
                </div>
                <div x-show="typeof $data !== 'undefined' && $data.success" x-transition class="mb-4">
                    <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded relative" x-cloak
                        role="alert">
                        <strong class="font-bold nunito-bold">¡Información actualizada!</strong>
                        <span class="block sm:inline nunito-regular">Tus datos se guardaron correctamente.</span>
                    </div>
                </div>

                <div x-show="typeof $data !== 'undefined' && $data.hasChanges" x-transition class="mb-4">
                    <div class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-700 text-yellow-700 dark:text-yellow-300 px-4 py-3 rounded relative" x-cloak
                        role="alert">
                        <strong class="font-bold nunito-bold">¡Hay cambios sin guardar!</strong>
                        <span class="block sm:inline nunito-regular">Guarde los cambios para que surtan efecto.</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 nunito-bold">Primer Nombre</label>
                        <input type="text" x-model="form.primer_nombre" @input="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white nunito-regular">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 nunito-bold">Segundo Nombre</label>
                        <input type="text" x-model="form.segundo_nombre" @input="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white nunito-regular">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 nunito-bold">Primer Apellido</label>
                        <input type="text" x-model="form.primer_apellido" @input="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white nunito-regular">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 nunito-bold">Segundo Apellido</label>
                        <input type="text" x-model="form.segundo_apellido" @input="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white nunito-regular">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 nunito-bold">Género</label>
                        <select x-model="form.id_genero_fk" @change="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white nunito-regular">
                            <option class="nunito-regular" value="">Seleccione…</option>
                            <template x-for="g in generos" :key="g.id_genero_pk || g.id">
                                <option class="nunito-regular" :value="g.id_genero_pk || g.id" x-text="g.genero"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 nunito-bold">DNI</label>
                        <input type="text" x-model="form.dni" @input="onFormChange()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white nunito-regular"
                            placeholder="Ej: 0000-0000-00000 o 0000000000000">

                    </div>



                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-400 dark:border-gray-700 border-opacity-50">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6 nunito-regular">Cambiar Contraseña</h3>

                <div x-show="passwordSuccess" x-transition class="mb-4">
                    <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded relative"
                        role="alert">
                        <strong class="font-bold nunito-bold">¡Contraseña actualizada!</strong>
                        <span class="block sm:inline nunito-regular">Tu contraseña se cambió correctamente.</span>
                    </div>
                </div>
                <div x-show="passwordError" x-transition class="mb-4">
                    <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded relative"
                        role="alert">
                        <strong class="font-bold nunito-bold">No se pudo actualizar</strong>
                        <span class="block sm:inline nunito-regular" x-text="passwordError"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2 nunito-bold">Contraseña Actual</label>
                        <input type="password" x-model="passwordForm.current_password"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white nunito-regular"
                            placeholder="Ingresa tu contraseña actual">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2 nunito-bold">Nueva Contraseña</label>
                        <input type="password" x-model="passwordForm.password"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white nunito-regular"
                            placeholder="Mínimo 8 caracteres">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2 nunito-bold">Confirmar Nueva Contraseña</label>
                        <input type="password" x-model="passwordForm.password_confirmation"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white nunito-regular"
                            placeholder="Repite la nueva contraseña">
                    </div>
                </div>

                <div class="mt-8 text-right">
                    @perm(['Perfil'], 'actualizacion')
                    <button type="button" @click="cambiarPassword()" :disabled="changingPassword"
                        class="bg-red-600 text-white py-2 px-6 rounded-lg font-semibold hover:bg-red-700 focus:ring-4 focus:ring-red-600 focus:ring-opacity-50 nunito-regular transition-colors duration-200 ease-in-out">
                        <span x-show="!changingPassword" class="nunito-regular">Cambiar Contraseña</span>
                        <span x-show="changingPassword" class="inline-flex items-center nunito-regular">
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
                    @else
                    <button disabled title="Sin permiso para actualizar" class="bg-gray-400 text-white py-2 px-6 rounded-lg font-semibold cursor-not-allowed nunito-regular">Cambiar Contraseña</button>
                    @endperm
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-400 dark:border-gray-700 border-opacity-50">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 nunito-bold">Autenticación en Dos Pasos (2FA)</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 nunito-regular">
                    Protege tu cuenta con un segundo factor de autenticación usando aplicaciones como Google Authenticator o Microsoft Authenticator.
                </p>

                <template x-if="twoFAEnabled">
                    <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-3">
                        <div>
                            <p class="text-green-700 dark:text-green-300 font-semibold">2FA activo</p>
                            <p class="text-xs text-green-700/80 dark:text-green-300/80">Se te pedirá un código al iniciar sesión.</p>
                        </div>
                        @perm(['Perfil'], 'actualizacion')
                        <button type="button" @click="disable2FA()" :disabled="twoFASetup.loading"
                            class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white text-sm font-semibold disabled:opacity-50">
                            Desactivar 2FA
                        </button>
                        @else
                        <button disabled title="Sin permiso" class="px-4 py-2 rounded bg-red-300 text-white text-sm font-semibold cursor-not-allowed">Desactivar 2FA</button>
                        @endperm
                    </div>
                </template>

                <template x-if="!twoFAEnabled">
                    <div class="flex items-center justify-between bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-3">
                        <div>
                            <p class="text-yellow-800 dark:text-yellow-200 font-semibold">2FA desactivado</p>
                            <p class="text-xs text-yellow-800/80 dark:text-yellow-200/80">Actívalo para mayor seguridad.</p>
                        </div>
                        @perm(['Perfil'], 'actualizacion')
                        <button type="button" @click="start2FA()" :disabled="twoFASetup.loading"
                            class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold disabled:opacity-50">
                            Activar 2FA
                        </button>
                        @else
                        <button disabled title="Sin permiso" class="px-4 py-2 rounded bg-gray-400 text-white text-sm font-semibold cursor-not-allowed">Activar 2FA</button>
                        @endperm
                    </div>
                </template>

                <div x-show="show2FASetup" x-cloak class="mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1 flex items-center justify-center">
                            <template x-if="twoFASetup.qrUrl">
                                <img :src="twoFASetup.qrUrl" alt="QR 2FA" class="w-48 h-48 border rounded-lg bg-white" />
                            </template>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-regular">URL de configuración (otpauth)</label>
                            <div class="flex gap-2">
                                <input type="text" x-model="twoFASetup.otpauthUrl" readonly
                                    class="flex-1 px-3 py-2 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white text-xs" />
                                <button type="button" @click="copyOtpUrl()"
                                    class="px-3 py-2 rounded border border-gray-300 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-200">Copiar</button>
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-regular">Código del autenticador</label>
                                <input type="text" inputmode="numeric" maxlength="10" x-model="twoFASetup.code"
                                    class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white text-sm" placeholder="6 dígitos" />
                                <p x-show="twoFASetup.error" class="mt-1 text-xs text-red-600" x-text="twoFASetup.error"></p>
                            </div>

                            <div class="mt-4 flex items-center gap-2">
                                @perm(['Perfil'], 'actualizacion')
                                <button type="button" @click="confirm2FA()" :disabled="twoFASetup.confirming || !twoFASetup.code"
                                    class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white text-sm font-semibold disabled:opacity-50">
                                    Confirmar
                                </button>
                                <button type="button" @click="cancel2FA()"
                                    class="px-4 py-2 rounded border border-gray-300 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-200">Cancelar</button>
                                @else
                                <button disabled title="Sin permiso" class="px-4 py-2 rounded bg-green-300 text-white text-sm font-semibold cursor-not-allowed">Confirmar</button>
                                <button disabled title="Sin permiso" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-700 text-sm text-gray-400 dark:text-gray-500 cursor-not-allowed">Cancelar</button>
                                @endperm
                            </div>

                            <div x-show="twoFASetup.recoveryCodes && twoFASetup.recoveryCodes.length" class="mt-6">
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">Códigos de recuperación (guárdalos en lugar seguro):</p>
                                <ul class="grid grid-cols-2 gap-2 text-xs text-gray-800 dark:text-gray-100 bg-gray-50 dark:bg-gray-900 p-3 rounded border border-gray-200 dark:border-gray-700">
                                    <template x-for="c in twoFASetup.recoveryCodes" :key="c">
                                        <li class="font-mono" x-text="c"></li>
                                    </template>
                                </ul>
                                <button type="button" @click="copyRecoveryCodes()" class="mt-2 px-3 py-1.5 rounded border border-gray-300 dark:border-gray-700 text-xs text-gray-700 dark:text-gray-200">Copiar códigos</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <div x-cloak
        x-show="showConfirmModal"
        class="fixed inset-0 z-50 flex items-center justify-center"
        style="display: none;"
        x-transition
        aria-modal="true"
        role="dialog">
        <div class="absolute inset-0 bg-black/50" @click="resolveConfirmModal(false)"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 nunito-bold" x-text="confirmTitle || 'Confirmación requerida'"></h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 nunito-regular" x-text="confirmDescription || '¿Deseas continuar?'"></p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200" @click="resolveConfirmModal(false)">Cancelar</button>
                <button type="button" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white" @click="resolveConfirmModal(true)">Confirmar</button>
            </div>
        </div>
    </div>
    <div x-cloak
        x-show="showPasswordModal"
        class="fixed inset-0 z-50 flex items-center justify-center"
        style="display: none;"
        x-transition
        aria-modal="true"
        role="dialog">
        <div class="absolute inset-0 bg-black/50" @click="cancelPasswordModal()"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 nunito-bold" x-text="modalTitle || 'Confirmación requerida'"></h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 nunito-regular" x-text="modalDescription || 'Ingresa tu contraseña actual para continuar.'"></p>
            <div class="mt-4">
                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1 nunito-regular">Contraseña actual</label>
                <input type="password"
                    class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                    placeholder="••••••••"
                    x-model="twoFASetup.currentPassword"
                    @keydown.enter.prevent="submitPasswordModal()"
                    autofocus />
                <p class="mt-2 text-xs text-red-600" x-show="modalError" x-text="modalError"></p>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200" @click="cancelPasswordModal()">Cancelar</button>
                <button type="button" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white" @click="submitPasswordModal()">Confirmar</button>
            </div>
        </div>
    </div>
</div>