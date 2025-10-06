@extends('cliente.layouts.app')
@section('title','Perfil - Cliente')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Mi Perfil</h1>
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Editar Perfil
        </button>
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
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Información Personal</h2>
        </div>
        
        <div class="px-6 py-4">
            @if($persona)
                <!-- Avatar y nombre principal -->
                <div class="flex items-center space-x-6 mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0">
                        @if($persona->avatar_path)
                            <img src="{{ asset('storage/' . $persona->avatar_path) }}" 
                                 alt="Avatar de {{ $persona->primer_nombre }}" 
                                 class="w-20 h-20 rounded-full object-cover border border-blue-200 dark:border-blue-300">
                        @else
                            <div class="w-20 h-20 rounded-full bg-indigo-100 dark:bg-indigo-800 flex items-center justify-center border border-blue-200 dark:border-blue-300">
                                <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ trim($persona->primer_nombre . ' ' . ($persona->segundo_nombre ?? '') . ' ' . $persona->primer_apellido . ' ' . ($persona->segundo_apellido ?? '')) }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ auth()->user()->correo_electronico }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            DNI
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $persona->dni }}</p>
                    </div>

                        @if($persona->genero)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Género
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $persona->genero->genero }}</p>
                    </div>
                    @endif
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Mi Actividad</h3>
                    
                    <!-- Grid de estadísticas -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Facturas -->
                        <div class="bg-white dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">Tus Facturas</h4>
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
                                <a href="{{ route('cliente.facturas') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Ver todas las facturas →</a>
                            </div>
                        </div>

                        <!-- Cotizaciones -->
                        <div class="bg-white dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">Tus Cotizaciones</h4>
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
                                <a href="{{ route('cliente.cotizaciones') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Ver todas las cotizaciones →</a>
                            </div>
                        </div>

                        <!-- Órdenes de Servicio -->
                        <div class="bg-white dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">Órdenes de Servicio</h4>
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
                                <a href="{{ route('cliente.ordenes') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Ver todas las órdenes →</a>
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
                        <a href="{{ route('cliente.configurar-perfil') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Configurar Perfil
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection