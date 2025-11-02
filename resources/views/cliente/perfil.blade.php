@extends('cliente.layouts.app')
@section('title','Perfil - Cliente')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 mt-16 font-nunito" x-data="perfilData($el)" x-init="init()" data-update-url="{{ route('cliente.perfil.update') }}" @if($empresa) data-empresa-update-url="{{ route('cliente.empresa.update') }}" @endif>
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 font-serif">Mi Perfil</h1>
        @if($persona && !$empresa)
        <button @click="openEditModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Editar Perfil
        </button>
        @elseif($empresa)
        <button @click="openEmpresaModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Editar Empresa
        </button>
        @endif
    </div>

    @if(session('success'))
        <div class="rounded-md bg-green-50 dark:bg-green-900 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 font-serif">
                Autenticación en Dos Pasos (2FA)
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Protege tu cuenta con un segundo factor de autenticación usando aplicaciones como Google Authenticator o Microsoft Authenticator.
            </p>
        </div>
        
        <div class="px-6 py-4">
            <template x-if="twoFAEnabled">
                <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-3">
                    <div>
                        <p class="text-green-700 dark:text-green-300 font-semibold">2FA activo</p>
                        <p class="text-xs text-green-700/80 dark:text-green-300/80">Se te pedirá un código al iniciar sesión.</p>
                    </div>
                    <button type="button" @click="disable2FA()" :disabled="twoFASetup.loading"
                        class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white text-sm font-semibold disabled:opacity-50">
                        Desactivar 2FA
                    </button>
                </div>
            </template>

            <template x-if="!twoFAEnabled">
                <div class="flex items-center justify-between bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-3">
                    <div>
                        <p class="text-yellow-800 dark:text-yellow-200 font-semibold">2FA desactivado</p>
                        <p class="text-xs text-yellow-800/80 dark:text-yellow-200/80">Actívalo para mayor seguridad.</p>
                    </div>
                    <button type="button" @click="start2FA()" :disabled="twoFASetup.loading"
                        class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold disabled:opacity-50">
                        Activar 2FA
                    </button>
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL de configuración (otpauth)</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="twoFASetup.otpauthUrl" readonly
                                class="flex-1 px-3 py-2 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white text-xs" />
                            <button type="button" @click="copyOtpUrl()"
                                class="px-3 py-2 rounded border border-gray-300 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-200">Copiar</button>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código del autenticador</label>
                            <input type="text" inputmode="numeric" maxlength="10" x-model="twoFASetup.code"
                                class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white text-sm" placeholder="6 dígitos" />
                            <p x-show="twoFASetup.error" class="mt-1 text-xs text-red-600" x-text="twoFASetup.error"></p>
                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <button type="button" @click="confirm2FA()" :disabled="twoFASetup.confirming || !twoFASetup.code"
                                class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white text-sm font-semibold disabled:opacity-50">
                                Confirmar
                            </button>
                            <button type="button" @click="cancel2FA()"
                                class="px-4 py-2 rounded border border-gray-300 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-200">Cancelar</button>
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

    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-bold serif text-gray-900 dark:text-gray-100 font-serif">
                @if($empresa)
                    Información de la Empresa
                @else
                    Información Personal
                @endif
            </h2>
        </div>
        
        <div class="px-6 py-4">
            @if($persona)
                <div class="flex items-center space-x-6 mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0">
                        @if($empresa && $empresa->avatar)
                            <img src="{{ asset('storage/' . $empresa->avatar) }}" 
                                 alt="Logo de {{ $empresa->nombre_comercial }}" 
                                 class="w-20 h-20 rounded-full object-cover border border-blue-200 dark:border-blue-300">
                        @elseif($persona->avatar_path)
                            <img src="{{ asset('storage/' . $persona->avatar_path) }}" 
                                 alt="Avatar de {{ $persona->primer_nombre }}" 
                                 class="w-20 h-20 rounded-full object-cover border border-blue-200 dark:border-blue-300">
                        @else
                            <div class="w-20 h-20 rounded-full bg-indigo-100 dark:bg-indigo-200 flex items-center justify-center">
                                @if($empresa)
                                    <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                @else
                                    <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div>
                        @if($empresa)
                            <h3 class="text-xl font-medium serif text-gray-900 dark:text-gray-100">
                                {{ $empresa->nombre_comercial }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 serif">{{ auth()->user()->correo_electronico }}</p>
                            @if($empresa->razon_social)
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $empresa->razon_social }}</p>
                            @endif
                        @else
                            <h3 class="text-xl font-medium serif text-gray-900 dark:text-gray-100">
                                {{ trim($persona->primer_nombre . ' ' . ($persona->segundo_nombre ?? '') . ' ' . $persona->primer_apellido . ' ' . ($persona->segundo_apellido ?? '')) }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 serif">{{ auth()->user()->correo_electronico }}</p>
                        @endif
                    </div>
                </div>

                @if($empresa)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($empresa->rtn)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                RTN
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $empresa->rtn }}</p>
                        </div>
                        @endif

                        @if($empresa->horario_atencion)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Horario de Atención
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $empresa->horario_atencion }}</p>
                        </div>
                        @endif
                    </div>

                    @if($empresa->descripcion_empresa)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Descripción de la Empresa
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $empresa->descripcion_empresa }}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-1">
                                Tipo
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">Empresa</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                                Correo de Contacto
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $correoContacto ?: auth()->user()->correo_electronico }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-1">
                                Teléfono Fijo de Contacto
                            </label>
                            <p class="text-sm serif text-gray-900 dark:text-gray-100">+504 83829020</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-1">
                                Teléfono Móvil de Contacto
                            </label>
                            <p class="text-sm serif text-gray-900 dark:text-gray-100">+504 83829020</p>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-1">
                                DNI
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $persona->dni }}</p>
                        </div>

                        @if($persona->genero)
                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-1">
                                Género
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $persona->genero->genero }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-1">
                                Tipo
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">Persona</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-1">
                                Correo de Contacto
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $correoContacto ?: auth()->user()->correo_electronico }}</p>
                        </div>

                    </div>
                @endif

                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6 font-serif">Mi Actividad</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 font-serif">Tus Facturas</h4>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Total:</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">0</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Pagadas:</span>
                                    <span class="font-semibold text-green-600 dark:text-green-400">0</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Pendientes:</span>
                                    <span class="font-semibold text-orange-600 dark:text-orange-400">0</span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('cliente.facturas') }}" data-spa-link class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Ver todas las facturas →</a>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 font-serif">Tus Cotizaciones</h4>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Total:</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">0</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Aprobadas:</span>
                                    <span class="font-semibold text-green-600 dark:text-green-400">0</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">En revisión:</span>
                                    <span class="font-semibold text-yellow-600 dark:text-yellow-400">0</span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('cliente.cotizaciones') }}" data-spa-link class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Ver todas las cotizaciones →</a>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 font-serif">Órdenes de Servicio</h4>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Total:</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">0</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Completadas:</span>
                                    <span class="font-semibold text-green-600 dark:text-green-400">0</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">En proceso:</span>
                                    <span class="font-semibold text-blue-600 dark:text-blue-400">0</span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('cliente.ordenes') }}" data-spa-link class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Ver todas las órdenes →</a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay información personal</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tu información personal no está disponible.</p>
                    <div class="mt-6">
                        <a href="{{ route('cliente.configurar-perfil') }}" data-spa-link
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Configurar Perfil
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($persona && !$empresa)
    <template x-teleport="body">
        <div x-show="showEditModal" x-cloak x-transition.opacity.duration.300ms
            class="modal-underlay fixed inset-0 flex items-center justify-center z-[9999] bg-black/50 backdrop-blur-sm"
            @click.self="closeEditModal()" @keydown.window.escape="closeEditModal()"
            style="margin: 0;">
            <div x-transition:enter="transition ease-out duration-300" 
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-6 w-11/12 max-w-2xl mx-auto max-h-[90vh] overflow-y-auto" 
                @click.stop>
                
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 font-serif">
                        Editar Información Personal
                    </h3>
                    <button @click="closeEditModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form @submit.prevent="updateProfile()" class="space-y-6">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div class="relative">
                                @if($persona->avatar_path)
                                    <img src="{{ asset('storage/' . $persona->avatar_path) }}" 
                                         alt="Avatar actual" 
                                         class="w-20 h-20 rounded-full object-cover border border-gray-300 dark:border-gray-600">
                                @else
                                    <div class="w-20 h-20 rounded-full bg-indigo-100 dark:bg-indigo-800 flex items-center justify-center border border-gray-300 dark:border-gray-600">
                                        <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <label class="absolute bottom-0 right-0 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full p-1 cursor-pointer shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <input type="file" x-ref="avatarInput" @change="handleAvatarChange($event)" accept="image/*" class="hidden">
                                </label>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1 font-serif">Foto de perfil</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">JPG, PNG o GIF. Máximo 2MB.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-2">
                                Primer Nombre *
                            </label>
                            <input type="text" x-model="formData.primer_nombre" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg  dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-2">
                                Segundo Nombre
                            </label>
                            <input type="text" x-model="formData.segundo_nombre"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg  dark:bg-gray-700 dark:text-gray-100">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-2">
                                Primer Apellido *
                            </label>
                            <input type="text" x-model="formData.primer_apellido" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg  dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-2">
                                Segundo Apellido
                            </label>
                            <input type="text" x-model="formData.segundo_apellido"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg  dark:bg-gray-700 dark:text-gray-100">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-2">
                                DNI *
                            </label>
                            <input type="text" x-model="formData.dni" required maxlength="15"
                                   placeholder="0000-0000-00000"
                                   @input="formatDNI($event)"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg  dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-2">
                                Género
                            </label>
                            <select x-model="formData.id_genero_fk"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg  dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Seleccionar género</option>
                                @foreach($generos as $genero)
                                    <option value="{{ $genero->id_genero_pk }}">{{ $genero->genero }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-2">
                                Correo de Contacto
                            </label>
                            <input type="email" x-model="formData.correo_contacto" readonly
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 cursor-not-allowed"
                                   placeholder="No configurado">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <svg class="w-3 h-3 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Este correo se establece durante el registro inicial
                            </p>
                        </div>
                        
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="closeEditModal()" 
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancelar</button>
                        <button type="submit" :disabled="loading"
                                class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                            <span x-show="!loading">Guardar Cambios</span>
                            <span x-show="loading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    @endif

    @if($empresa)
    <template x-teleport="body">
        <div x-show="showEmpresaModal" x-cloak x-transition.opacity.duration.300ms
            class="modal-underlay fixed inset-0 flex items-center justify-center z-[9999] bg-black/50 backdrop-blur-sm"
            @click.self="closeEmpresaModal()" @keydown.window.escape="closeEmpresaModal()"
            style="margin: 0;">
            <div x-transition:enter="transition ease-out duration-300" 
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-6 w-11/12 max-w-3xl mx-auto max-h-[90vh] overflow-y-auto" 
                @click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 font-serif">Editar Empresa</h3>
                    <button @click="closeEmpresaModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form @submit.prevent="updateEmpresa()" class="space-y-6">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div class="relative">
                                @if($empresa->avatar)
                                    <img src="{{ asset('storage/' . $empresa->avatar) }}" alt="Logo actual" class="w-20 h-20 rounded-full object-cover border border-gray-300 dark:border-gray-600" />
                                @else
                                    <div class="w-20 h-20 rounded-full bg-indigo-100 dark:bg-indigo-800 flex items-center justify-center border border-gray-300 dark:border-gray-600">
                                        <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m2 0h5m4 0h5M9 7h1m4 0h1M9 11h1m4 0h1"/></svg>
                                    </div>
                                @endif
                                <label class="absolute bottom-0 right-0 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full p-1 cursor-pointer shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <input type="file" x-ref="empresaAvatar" @change="handleEmpresaAvatar($event)" accept="image/*" class="hidden" />
                                </label>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1 font-serif">Logo</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">JPEG, PNG, WEBP. Máx 2MB.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nombre Comercial *</label>
                            <input type="text" x-model="empresaForm.nombre_comercial" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Razón Social</label>
                            <input type="text" x-model="empresaForm.razon_social" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">RTN</label>
                            <input type="text" x-model="empresaForm.rtn" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Horario de Atención</label>
                            <input type="text" x-model="empresaForm.horario_atencion" placeholder="L-V 8:00-17:00" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Descripción de la Empresa</label>
                        <textarea x-model="empresaForm.descripcion_empresa" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-2">
                                Correo de Contacto *
                            </label>
                            <input type="email" x-model="formData.correo_contacto" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg  dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-2">
                                Teléfono Fijo de Contacto
                            </label>
                            <input type="tel" x-model="formData.telefono_contacto" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg  dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-bold serif text-gray-700 dark:text-gray-300 mb-2">
                                Teléfono Móvil de Contacto
                            </label>
                            <input type="tel" x-model="formData.celular_contacto"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg  dark:bg-gray-700 dark:text-gray-100">
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="closeEmpresaModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancelar</button>
                        <button type="submit" :disabled="empresaLoading" class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center">
                            <span x-show="!empresaLoading">Guardar Cambios</span>
                            <span x-show="empresaLoading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    @endif

    <template x-teleport="body">
        <div x-show="showPasswordModal" x-cloak x-transition.opacity.duration.300ms
            class="modal-underlay fixed inset-0 flex items-center justify-center z-[9999] bg-black/50 backdrop-blur-sm"
            @click.self="closePasswordModal()" @keydown.window.escape="closePasswordModal()"
            style="margin: 0;">
            <div x-transition:enter="transition ease-out duration-300" 
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-6 w-11/12 max-w-md mx-auto" 
                @click.stop>
                
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 font-serif" x-text="passwordModal.title || 'Confirmación requerida'"></h3>
                    <button @click="closePasswordModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4" x-text="passwordModal.description || 'Ingresa tu contraseña actual para continuar.'"></p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contraseña actual</label>
                            <input type="password"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                                   placeholder="••••••••"
                                   x-model="passwordModal.password"
                                   @keydown.enter.prevent="submitPasswordModal()"
                                   autofocus />
                            <p class="mt-2 text-xs text-red-600" x-show="modalError" x-text="modalError"></p>
                        </div>

                        <div class="flex gap-3">
                            <button type="button" 
                                    @click="closePasswordModal()" 
                                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Cancelar
                            </button>
                            <button type="button" 
                                    @click="submitPasswordModal()" 
                                    :disabled="passwordModal.loading || !passwordModal.password.trim()"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                <span x-show="!passwordModal.loading">Confirmar</span>
                                <span x-show="passwordModal.loading">Procesando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script type="application/json" id="persona-json">@json($personaData)</script>
@if($empresa)
@php $empresaJson = json_encode($empresa->only(['nombre_comercial','razon_social','rtn','descripcion_empresa','horario_atencion'])); @endphp
<script type="application/json" id="empresa-json">{!! $empresaJson !!}</script>
@endif

@push('scripts')
@vite(['resources/js/cliente/perfil.js'])
@endpush

@endsection