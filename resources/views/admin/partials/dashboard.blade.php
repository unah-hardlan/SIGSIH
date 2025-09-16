<div class="z-10" x-data="scrollPosition()" x-init="restoreScrollPosition()">
    <script>
        function scrollPosition() {
            return {
                saveScrollPosition() {
                    localStorage.setItem('dashboardScrollPosition', window.scrollY);
                },
                restoreScrollPosition() {
                    const savedPosition = localStorage.getItem('dashboardScrollPosition');
                    if (savedPosition) {
                        setTimeout(() => {
                            window.scrollTo(0, parseInt(savedPosition));
                        }, 100);
                    }
                },
                init() {
                    // Save scroll position before page unload
                    window.addEventListener('beforeunload', this.saveScrollPosition);

                    // Also save on visibility change (when tab becomes hidden)
                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) {
                            this.saveScrollPosition();
                        }
                    });

                    // Restore position on page load
                    this.restoreScrollPosition();
                }
            }
        }
    </script>
    <div class="flex items-center my-1 mb-10">
        <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
        <div class="mx-4">
            <div class="flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-full border border-blue-200 dark:border-blue-700 shadow-sm">
                <i class="fas fa-chart-bar text-blue-600 dark:text-blue-400 text-lg"></i>
                <span class="text-sm nunito-bold text-blue-700 dark:text-blue-300 uppercase tracking-wider">Indicadores</span>
                <i class="fas fa-chart-line text-indigo-600 dark:text-indigo-400 text-sm"></i>
            </div>
        </div>
        <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
    </div>    <div class="mb-8" x-data="dashboardKPIs()" x-init="init()">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-blue-500 transition-transform duration-300 ease-in-out hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm nunito-regular text-gray-600 dark:text-gray-400">Total Usuarios</h3>
                        <p class="text-3xl nunito-bold text-blue-600 dark:text-blue-400" x-text="fmt(totalUsuarios)">–</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                        <i class="fas fa-users text-blue-500 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-green-500 transition-transform duration-300 ease-in-out hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm nunito-regular text-gray-600 dark:text-gray-400">Empresas Activas</h3>
                        <p class="text-3xl nunito-bold text-green-600 dark:text-green-400" x-text="fmt(empresasActivas)">–</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                        <i class="fas fa-building text-green-500 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-purple-500 transition-transform duration-300 ease-in-out hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm nunito-regular text-gray-600 dark:text-gray-400">Órdenes de Servicio</h3>
                        <p class="text-3xl nunito-bold text-purple-600 dark:text-purple-400" x-text="fmt(ordenesServicio)">–</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-full">
                        <i class="fas fa-clipboard-list text-purple-500 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-indigo-500 transition-transform duration-300 ease-in-out hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm nunito-regular text-gray-600 dark:text-gray-400">Cotizaciones</h3>
                        <p class="text-3xl nunito-bold text-indigo-600 dark:text-indigo-400" x-text="fmt(cotizaciones)">–</p>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-full">
                        <i class="fas fa-file-invoice-dollar text-indigo-500 dark:text-indigo-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-teal-50 to-teal-100 dark:from-teal-900/20 dark:to-teal-800/20 p-6 rounded-xl shadow-lg border border-teal-200 dark:border-teal-700 relative overflow-hidden transition-transform duration-300 ease-in-out hover:scale-105">
                <div class="absolute top-0 right-0 w-20 h-20 bg-teal-200 dark:bg-teal-700 rounded-full -translate-y-10 translate-x-10 opacity-20"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm nunito-bold text-teal-700 dark:text-teal-300 uppercase tracking-wide">Proyectos</h3>
                        <div class="p-2 bg-teal-500 dark:bg-teal-600 rounded-lg shadow-md">
                            <i class="fas fa-project-diagram text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-blue-400 dark:border-blue-600">
                            <div class="flex justify-between items-center">
                                <span class="text-2xl nunito-bold text-teal-600 dark:text-teal-400" x-text="fmt(proyectosActivos)">–</span>
                                <span class="text-xs nunito-regular text-teal-600 dark:text-teal-400 bg-teal-100 dark:bg-teal-900/30 px-2 py-1 rounded-full">ACTIVOS</span>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-blue-400 dark:border-blue-600">
                            <div class="flex justify-between items-center">
                                <span class="text-lg nunito-bold text-gray-600 dark:text-gray-400" x-text="fmt(proyectosFinalizados)">–</span>
                                <span class="text-xs nunito-regular text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">FINALIZADOS</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 p-6 rounded-xl shadow-lg border border-orange-200 dark:border-orange-700 relative overflow-hidden transition-transform duration-300 ease-in-out hover:scale-105">
                <div class="absolute top-0 right-0 w-16 h-16 bg-orange-200 dark:bg-orange-700 rounded-full -translate-y-8 translate-x-8 opacity-30"></div>
                <div class="absolute bottom-0 left-0 w-12 h-12 bg-orange-300 dark:bg-orange-600 rounded-full translate-y-6 -translate-x-6 opacity-20"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm nunito-bold text-orange-700 dark:text-orange-300 uppercase tracking-wide">Tickets</h3>
                        <div class="p-2 bg-orange-500 dark:bg-orange-600 rounded-lg shadow-md">
                            <i class="fas fa-ticket-alt text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="text-center bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-orange-400 dark:border-orange-600">
                            <div class="text-2xl nunito-bold text-orange-600 dark:text-orange-400 mb-1" x-text="fmt(ticketsAbiertos)">–</div>
                            <div class="text-xs nunito-regular text-orange-600 dark:text-orange-400 uppercase">Abiertos</div>
                            <div class="w-full bg-orange-200 dark:bg-orange-700 rounded-full h-1 mt-2">
                                <div class="bg-orange-500 dark:bg-orange-400 h-1 rounded-full" :style="`width: ${percentTickets('abiertos')}%`"></div>
                            </div>
                        </div>
                        <div class="text-center bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-orange-400 dark:border-orange-600">
                            <div class="text-2xl nunito-bold text-gray-600 dark:text-gray-400 mb-1" x-text="fmt(ticketsCerrados)">–</div>
                            <div class="text-xs nunito-regular text-gray-600 dark:text-gray-400 uppercase">Cerrados</div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1 mt-2">
                                <div class="bg-gray-500 dark:bg-gray-400 h-1 rounded-full" :style="`width: ${percentTickets('cerrados')}%`"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-100 to-green-300 dark:from-green-900/30 dark:to-green-800/30 p-6 rounded-xl shadow-lg border border-emerald-200 dark:border-emerald-700 relative overflow-hidden transition-transform duration-300 ease-in-out hover:scale-105">
                <div class="absolute top-0 left-0 w-24 h-24 bg-green-700 dark:bg-green-600 rounded-full -translate-y-12 -translate-x-12 opacity-25"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm nunito-bold text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">Inventario</h3>
                        <div class="p-2 bg-green-500 dark:bg-green-600 rounded-lg shadow-md">
                            <i class="fas fa-boxes text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-green-700 dark:border-green-600">
                        <div class="flex items-end justify-between mb-2">
                            <span class="text-3xl nunito-bold text-green-600 dark:text-green-400" x-text="fmt(inventarioProductos)">–</span>
                            <div class="text-right">
                                <div class="text-xs nunito-regular text-gray-500 dark:text-gray-400 uppercase">Total productos</div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-400 dark:border-gray-600">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-pink-50 to-rose-100 dark:from-pink-900/20 dark:to-rose-800/20 p-6 rounded-xl shadow-lg border border-pink-200 dark:border-pink-700 relative overflow-hidden transition-transform duration-300 ease-in-out hover:scale-105">
                <div class="absolute bottom-0 right-0 w-20 h-20 bg-pink-200 dark:bg-pink-700 rounded-full translate-y-10 translate-x-10 opacity-25"></div>
                <div class="absolute top-0 left-1/2 w-8 h-8 bg-pink-300 dark:bg-pink-600 rounded-full -translate-y-4 opacity-30"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm nunito-bold text-pink-700 dark:text-pink-300 uppercase tracking-wide">Reportes</h3>
                        <div class="p-2 bg-pink-500 dark:bg-pink-600 rounded-lg shadow-md">
                            <i class="fas fa-chart-line text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-pink-600 dark:border-pink-500">
                        <div class="text-center mb-3">
                            <span class="text-4xl nunito-bold text-pink-600 dark:text-pink-400" x-text="fmt(reportesGenerados)">–</span>
                            <p class="text-xs nunito-regular text-gray-500 dark:text-gray-400 uppercase mt-1">Generados</p>
                        </div>
                        <div class="flex items-center justify-center space-x-2 pt-3 border-t border-gray-400 dark:border-gray-600">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center my-12">
        <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
        <div class="mx-4">
            <div class="flex items-center space-x-3 px-8 py-4 bg-gradient-to-r from-green-50 via-emerald-50 to-teal-50 dark:from-green-900/20 dark:via-emerald-900/20 dark:to-teal-900/20 rounded-full border border-green-200 dark:border-green-700 shadow-md">
                <i class="fas fa-rocket text-green-600 dark:text-green-400 text-xl animate-pulse"></i>
                <span class="text-base nunito-bold text-green-700 dark:text-green-300 uppercase tracking-widest">Accesos Rápidos</span>
                <i class="fas fa-bolt text-teal-600 dark:text-teal-400 text-lg"></i>
            </div>
        </div>
        <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
    </div>

    <div class="mb-8">
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <x-admin.sidebar-link-static href="#" :active="false" view-name="gestion-ordenes" class="bg-white dark:bg-gray-800 p-4 border border-blue-400 border-opacity-50 rounded-lg shadow-md transition-shadow duration-200 text-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full w-12 h-12 mx-auto mb-2 flex items-center justify-center">
                    <i class="fas fa-plus text-blue-500 dark:text-blue-400"></i>
                </div>
                <p class="text-sm nunito-regular text-gray-700 dark:text-gray-300">Nueva Orden</p>
            </x-admin.sidebar-link-static>

            <x-admin.sidebar-link-static href="#" :active="false" view-name="cotizaciones" class="bg-white dark:bg-gray-800 p-4 border border-blue-400 border-opacity-50 rounded-lg shadow-md transition-shadow duration-200 text-center">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full w-12 h-12 mx-auto mb-2 flex items-center justify-center">
                    <i class="fas fa-file-invoice text-green-500 dark:text-green-400"></i>
                </div>
                <p class="text-sm nunito-regular text-gray-700 dark:text-gray-300">Nueva Cotización</p>
            </x-admin.sidebar-link-static>

            <x-admin.sidebar-link-static href="#" :active="false" view-name="proyectos" class="bg-white dark:bg-gray-800 p-4 border border-blue-400 border-opacity-50 rounded-lg shadow-md transition-shadow duration-200 text-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-full w-12 h-12 mx-auto mb-2 flex items-center justify-center">
                    <i class="fas fa-project-diagram text-purple-500 dark:text-purple-400"></i>
                </div>
                <p class="text-sm nunito-regular text-gray-700 dark:text-gray-300">Nuevo Proyecto</p>
            </x-admin.sidebar-link-static>

            <x-admin.sidebar-link-static href="#" :active="false" view-name="productos" class="bg-white dark:bg-gray-800 p-4 border border-blue-400 border-opacity-50 rounded-lg shadow-md transition-shadow duration-200 text-center">
                <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-full w-12 h-12 mx-auto mb-2 flex items-center justify-center">
                    <i class="fas fa-box-open text-orange-500 dark:text-orange-400"></i>
                </div>
                <p class="text-sm nunito-regular text-gray-700 dark:text-gray-300">Agregar Producto</p>
            </x-admin.sidebar-link-static>

            <x-admin.sidebar-link-static href="#" :active="false" view-name="reportes" class="bg-white dark:bg-gray-800 p-4 border border-blue-400 border-opacity-50 rounded-lg shadow-md transition-shadow duration-200 text-center">
                <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-full w-12 h-12 mx-auto mb-2 flex items-center justify-center">
                    <i class="fas fa-chart-bar text-indigo-500 dark:text-indigo-400"></i>
                </div>
                <p class="text-sm nunito-regular text-gray-700 dark:text-gray-300">Nuevo Reporte</p>
            </x-admin.sidebar-link-static>

            <x-admin.sidebar-link-static href="#" :active="false" view-name="gestion-usuarios" class="bg-white dark:bg-gray-800 p-4 border border-blue-400 border-opacity-50 rounded-lg shadow-md transition-shadow duration-200 text-center">
                <div class="p-3 bg-teal-100 dark:bg-teal-900/30 rounded-full w-12 h-12 mx-auto mb-2 flex items-center justify-center">
                    <i class="fas fa-user-plus text-teal-500 dark:text-teal-400"></i>
                </div>
                <p class="text-sm nunito-regular text-gray-700 dark:text-gray-300">Nuevo Usuario</p>
            </x-admin.sidebar-link-static>
        </div>
    </div>

    <div class="flex items-center my-12">
        <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
        <div class="mx-4">
            <div class="flex items-center space-x-3 px-8 py-4 bg-gradient-to-r from-purple-50 via-indigo-50 to-blue-50 dark:from-purple-900/20 dark:via-indigo-900/20 dark:to-blue-900/20 rounded-full border border-purple-200 dark:border-purple-700 shadow-md">
                <i class="fas fa-chart-pie text-purple-600 dark:text-purple-400 text-xl"></i>
                <span class="text-base nunito-bold text-purple-700 dark:text-purple-300 uppercase tracking-widest">Gráficas y Visualizaciones</span>
                <i class="fas fa-chart-line text-blue-600 dark:text-blue-400 text-lg"></i>
            </div>
        </div>
        <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
    </div>

<div class="mb-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-400 border-opacity-50 hover:border-blue-500 transition-colors duration-300">
            <div class="flex items-center px-4 py-3 bg-purple-50 dark:bg-purple-900/20 rounded-t-xl border-b border-purple-100 dark:border-purple-700">
                <i class="fas fa-clipboard-list text-purple-700 dark:text-purple-400 mr-2"></i>
                <h3 class="text-sm nunito-bold text-purple-700 dark:text-purple-300">Órdenes de Servicio por Estado</h3>
            </div>
            <div class="p-6">
                <div class="relative h-64">
                    <canvas id="ordenesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-400 border-opacity-50 hover:border-blue-500 transition-colors duration-300">
            <div class="flex items-center px-4 py-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-t-xl border-b border-indigo-100 dark:border-indigo-700">
                <i class="fas fa-file-invoice-dollar text-indigo-700 dark:text-indigo-400 mr-2"></i>
                <h3 class="text-sm nunito-bold text-indigo-700 dark:text-indigo-300">Cotizaciones por Mes</h3>
            </div>
            <div class="p-6">
                <div class="relative h-64">
                    <canvas id="cotizacionesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-400 border-opacity-50 hover:border-blue-500 transition-colors duration-300">
            <div class="flex items-center px-4 py-3 bg-teal-50 dark:bg-teal-900/20 rounded-t-xl border-b border-teal-100 dark:border-teal-700">
                <i class="fas fa-project-diagram text-teal-700 dark:text-teal-400 mr-2"></i>
                <h3 class="text-sm nunito-bold text-teal-700 dark:text-teal-300">Proyectos por Estado</h3>
            </div>
            <div class="p-6">
                <div class="relative h-64">
                    <canvas id="proyectosChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center my-12">
        <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
        <div class="mx-4">
            <div class="flex items-center space-x-3 px-8 py-4 bg-gradient-to-r from-slate-50 via-gray-50 to-zinc-50 dark:from-slate-900/20 dark:via-gray-900/20 dark:to-zinc-900/20 rounded-full border border-slate-200 dark:border-slate-700 shadow-md">
                <i class="fas fa-clipboard-check text-slate-600 dark:text-slate-400 text-xl"></i>
                <span class="text-base nunito-bold text-slate-700 dark:text-slate-300 uppercase tracking-widest">Bitácora de Actividad</span>
                <i class="fas fa-shield-alt text-gray-600 dark:text-gray-400 text-lg"></i>
            </div>
        </div>
        <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
    </div>

    <div class="mb-12">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-400 border-opacity-50 hover:border-gray-500 transition-colors duration-300">
            <div class="flex items-center px-4 py-3 bg-slate-50 dark:bg-slate-900/20 rounded-t-xl border-b border-slate-100 dark:border-slate-700">
                <i class="fas fa-history text-slate-700 dark:text-slate-400 mr-2"></i>
                <h3 class="text-base nunito-bold text-slate-700 dark:text-slate-300">Registro de acciones recientes de usuarios</h3>
                <div class="ml-auto flex items-center space-x-2">
                    <span class="text-xs nunito-regular text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-slate-700 px-2 py-1 rounded-full">Últimas 10 acciones</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/20 text-slate-800 dark:text-slate-200">
                            <th class="py-3 px-4 text-left nunito-bold">Usuario</th>
                            <th class="py-3 px-4 text-left nunito-bold">Acción</th>
                            <th class="py-3 px-4 text-left nunito-bold">Módulo</th>
                            <th class="py-3 px-4 text-left nunito-bold">Fecha / Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-blue-600 dark:text-blue-400 text-xs"></i>
                                    </div>
                                    <span class="nunito-regular">jlopez</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs nunito-regular bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                    <i class="fas fa-sign-in-alt mr-1"></i>Login
                                </span>
                            </td>
                            <td class="px-4 py-3 nunito-regular text-gray-600 dark:text-gray-400">Autenticación</td>
                            <td class="px-4 py-3 nunito-regular text-gray-600 dark:text-gray-400">02/08/2025 09:15</td>
                        </tr>
                        <tr class="border-b hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-indigo-600 dark:text-indigo-400 text-xs"></i>
                                    </div>
                                    <span class="nunito-regular">aruiz</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs nunito-regular bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400">
                                    <i class="fas fa-plus mr-1"></i>Creación
                                </span>
                            </td>
                            <td class="px-4 py-3 nunito-regular text-gray-600 dark:text-gray-400">Cotizaciones</td>
                            <td class="px-4 py-3 nunito-regular text-gray-600 dark:text-gray-400">02/08/2025 10:05</td>
                        </tr>
                        <tr class="border-b hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-purple-600 dark:text-purple-400 text-xs"></i>
                                    </div>
                                    <span class="nunito-regular">cdiaz</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs nunito-regular bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400">
                                    <i class="fas fa-edit mr-1"></i>Actualización
                                </span>
                            </td>
                            <td class="px-4 py-3 nunito-regular text-gray-600 dark:text-gray-400">Proyectos</td>
                            <td class="px-4 py-3 nunito-regular text-gray-600 dark:text-gray-400">02/08/2025 11:30</td>
                        </tr>
                        <tr class="border-b hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-red-600 dark:text-red-400 text-xs"></i>
                                    </div>
                                    <span class="nunito-regular">admin</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs nunito-regular bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400">
                                    <i class="fas fa-plus mr-1"></i>Creación
                                </span>
                            </td>
                            <td class="px-4 py-3 nunito-regular text-gray-600 dark:text-gray-400">Usuarios</td>
                            <td class="px-4 py-3 nunito-regular text-gray-600 dark:text-gray-400">02/08/2025 12:20</td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-green-600 dark:text-green-400 text-xs"></i>
                                    </div>
                                    <span class="nunito-regular">mgarcia</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs nunito-regular bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">
                                    <i class="fas fa-trash mr-1"></i>Eliminación
                                </span>
                            </td>
                            <td class="px-4 py-3 nunito-regular text-gray-600 dark:text-gray-400">Reportes</td>
                            <td class="px-4 py-3 nunito-regular text-gray-600 dark:text-gray-400">02/08/2025 13:45</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- KPIs powered by global dashboardKPIs() from resources/js/dashboard.js --}}
