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
    </div>    <!-- Dashboard KPIs -->
    <div class="mb-8" x-data="dashboardKPIs()" x-init="init()">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
            
            <!-- Card 1: Total Usuarios -->
            <div class="group dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border-2 border-blue-500 dark:border-blue-700 border-l-4 border-l-blue-500 overflow-hidden">
                <div class="p-5 lg:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm nunito-regular text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Usuarios</h3>
                            <p class="text-2xl md:text-xl lg:text-3xl nunito-bold text-blue-600 dark:text-blue-400 truncate" x-text="fmt(totalUsuarios)">–</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total registrados</p>
                        </div>
                        <div class="flex-shrink-0 ml-4">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                                <i class="fas fa-users text-blue-600 dark:text-blue-400 text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Empresas Activas -->
            <div class="group dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border-2 border-green-500 dark:border-green-700 border-l-4 border-l-green-500 overflow-hidden">
                <div class="p-5 lg:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm nunito-regular text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Empresas</h3>
                            <p class="text-2xl md:text-xl lg:text-3xl nunito-bold text-green-600 dark:text-green-400 truncate" x-text="fmt(empresasActivas)">–</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Activas</p>
                        </div>
                        <div class="flex-shrink-0 ml-4">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                                <i class="fas fa-building text-green-600 dark:text-green-400 text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Órdenes de Servicio -->
            <div class="group dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border-2 border-purple-500 dark:border-purple-700 border-l-4 border-l-purple-500 overflow-hidden">
                <div class="p-5 lg:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm nunito-regular text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Órdenes</h3>
                            <p class="text-2xl md:text-xl lg:text-3xl nunito-bold text-purple-600 dark:text-purple-400 truncate" x-text="fmt(ordenesServicio)">–</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">De servicio</p>
                        </div>
                        <div class="flex-shrink-0 ml-4">
                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                                <i class="fas fa-clipboard-list text-purple-600 dark:text-purple-400 text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Cotizaciones -->
            <div class="group dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border-2 border-indigo-500 dark:border-indigo-700 border-l-4 border-l-indigo-500 overflow-hidden">
                <div class="p-5 lg:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm nunito-regular text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Cotizaciones</h3>
                            <p class="text-2xl md:text-xl lg:text-3xl nunito-bold text-indigo-600 dark:text-indigo-400 truncate" x-text="fmt(cotizaciones)">–</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Generadas</p>
                        </div>
                        <div class="flex-shrink-0 ml-4">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                                <i class="fas fa-file-invoice-dollar text-indigo-600 dark:text-indigo-400 text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 5: Proyectos -->
            <div class="group dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border-2 border-teal-00 dark:border-teal-700 border-l-4 border-l-teal-500 overflow-hidden">
                <div class="p-5 lg:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <h3 class="text-sm nunito-regular text-gray-600 dark:text-gray-400 uppercase tracking-wide">Proyectos</h3>
                        </div>
                        <div class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-project-diagram text-teal-600 dark:text-teal-400"></i>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between bg-teal-50 dark:bg-teal-900/20 rounded-lg p-3">
                            <span class="text-lg md:text-base lg:text-2xl nunito-bold text-teal-600 dark:text-teal-400" x-text="fmt(proyectosActivos)">–</span>
                            <span class="text-xs nunito-regular text-teal-600 dark:text-teal-400 bg-teal-100 dark:bg-teal-900/30 px-2 py-1 rounded-full">ACTIVOS</span>
                        </div>
                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                            <span class="text-base md:text-sm lg:text-lg nunito-bold text-gray-600 dark:text-gray-400" x-text="fmt(proyectosFinalizados)">–</span>
                            <span class="text-xs nunito-regular text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">FINALIZADOS</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 6: Tickets -->
            <div class="group dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border-2 border-orange-300 dark:border-orange-700 border-l-4 border-l-orange-500 overflow-hidden">
                <div class="p-5 lg:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <h3 class="text-sm nunito-regular text-gray-600 dark:text-gray-400 uppercase tracking-wide">Tickets</h3>
                        </div>
                        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-ticket-alt text-orange-600 dark:text-orange-400"></i>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="text-center bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3">
                            <div class="text-lg md:text-base lg:text-2xl nunito-bold text-orange-600 dark:text-orange-400 mb-1" x-text="fmt(ticketsAbiertos)">–</div>
                            <div class="text-xs nunito-regular text-orange-600 dark:text-orange-400 uppercase">Abiertos</div>
                            <div class="w-full bg-orange-200 dark:bg-orange-700/30 rounded-full h-2 mt-2">
                                <div class="bg-orange-500 dark:bg-orange-400 h-2 rounded-full transition-all duration-300" :style="`width: ${percentTickets('abiertos')}%`"></div>
                            </div>
                        </div>
                        <div class="text-center bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                            <div class="text-lg md:text-base lg:text-2xl nunito-bold text-gray-600 dark:text-gray-400 mb-1" x-text="fmt(ticketsCerrados)">–</div>
                            <div class="text-xs nunito-regular text-gray-600 dark:text-gray-400 uppercase">Cerrados</div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2">
                                <div class="bg-gray-500 dark:bg-gray-400 h-2 rounded-full transition-all duration-300" :style="`width: ${percentTickets('cerrados')}%`"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 7: Inventario -->
            <div class="group dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border-2 border-emerald-300 dark:border-emerald-700 border-l-4 border-l-emerald-500 overflow-hidden">
                <div class="p-5 lg:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm nunito-regular text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Inventario</h3>
                            <p class="text-2xl md:text-xl lg:text-3xl nunito-bold text-emerald-600 dark:text-emerald-400 truncate" x-text="fmt(inventarioProductos)">–</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total productos</p>
                        </div>
                        <div class="flex-shrink-0 ml-4">
                            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                                <i class="fas fa-boxes text-emerald-600 dark:text-emerald-400 text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 8: Reportes -->
            <div class="group dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-pink-300 dark:border-pink-700 border-l-4 border-l-pink-500 overflow-hidden">
                <div class="p-5 lg:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm nunito-regular text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Reportes</h3>
                            <p class="text-2xl md:text-xl lg:text-3xl nunito-bold text-pink-600 dark:text-pink-400 truncate" x-text="fmt(reportesGenerados)">–</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Generados</p>
                        </div>
                        <div class="flex-shrink-0 ml-4">
                            <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                                <i class="fas fa-chart-line text-pink-600 dark:text-pink-400 text-lg"></i>
                            </div>
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

    <!-- Bitácora de Actividad con datos dinámicos -->
    <div class="mb-12" x-data="actividadesRecientesDashboard()" x-init="init()">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-400 border-opacity-50 hover:border-gray-500 transition-colors duration-300">
            <div class="flex items-center px-4 py-3 bg-slate-50 dark:bg-slate-900/20 rounded-t-xl border-b border-slate-100 dark:border-slate-700">
                <i class="fas fa-history text-slate-700 dark:text-slate-400 mr-2"></i>
                <h3 class="text-base nunito-bold text-slate-700 dark:text-slate-300">Registro de acciones recientes de usuarios</h3>
                <div class="ml-auto flex items-center space-x-2">
                    <span class="text-xs nunito-regular text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-slate-700 px-2 py-1 rounded-full">Últimas 5 acciones</span>
                    <button @click="cargarActividades()" x-bind:disabled="loading" 
                            class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded-md disabled:opacity-50 transition-colors">
                        <i class="fas fa-sync-alt mr-1" x-bind:class="loading ? 'fa-spin' : ''"></i>Actualizar
                    </button>
                </div>
            </div>
            
            <!-- Estado de carga -->
            <div x-show="loading" class="p-8 text-center">
                <div class="inline-flex items-center text-slate-600 dark:text-slate-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Cargando actividades...
                </div>
            </div>
            
            <!-- Estado de error -->
            <div x-show="error && !loading" class="p-8 text-center">
                <div class="text-red-600 dark:text-red-400">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span x-text="error"></span>
                </div>
                <button @click="cargarActividades()" 
                        class="mt-2 text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md transition-colors">
                    Reintentar
                </button>
            </div>
            
            <!-- Tabla de actividades -->
            <div x-show="!loading && !error" class="p-4">
                <x-responsive-table class="text-sm">
                    <x-slot name="table">
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
                                <!-- Si no hay actividades -->
                                <template x-if="actividades.length === 0">
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                            <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                            No hay actividades recientes registradas
                                        </td>
                                    </tr>
                                </template>

                                <!-- Lista de actividades -->
                                <template x-if="actividades.length > 0">
                                    <template x-for="(actividad, index) in actividades" :key="actividad.id">
                                        <tr class="border-b hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center mr-2"
                                                         :class="getColorUsuario(index)">
                                                        <i class="fas fa-user text-xs"></i>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="nunito-regular" x-text="actividad.usuario"></span>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="actividad.rol_usuario || ''" x-show="actividad.rol_usuario"></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs nunito-regular"
                                                      :class="getColorAccion(actividad.accion)">
                                                    <i :class="getIconoAccion(actividad.accion) + ' mr-1'"></i>
                                                    <span x-text="actividad.accion"></span>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 nunito-regular text-gray-600 dark:text-gray-400" x-text="actividad.modulo"></td>
                                            <td class="px-4 py-3 nunito-regular text-gray-600 dark:text-gray-400" x-text="actividad.fecha_hora"></td>
                                        </tr>
                                    </template>
                                </template>
                            </tbody>
                        </table>
                    </x-slot>

                    <x-slot name="cards">
                        <template x-if="actividades.length === 0">
                            <div class="bg-white dark:bg-slate-800 rounded-lg shadow border border-black dark:border-black p-6 text-center text-slate-500 dark:text-slate-400">
                                <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                No hay actividades recientes registradas
                            </div>
                        </template>

                        <template x-if="actividades.length > 0">
                            <template x-for="(actividad, index) in actividades" :key="actividad.id">
                                <div class="bg-white dark:bg-slate-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                             :class="getColorUsuario(index)">
                                            <i class="fas fa-user text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-base nunito-bold text-slate-800 dark:text-slate-100" x-text="actividad.usuario"></h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="actividad.rol_usuario || ''" x-show="actividad.rol_usuario"></p>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs nunito-regular"
                                              :class="getColorAccion(actividad.accion)">
                                            <i :class="getIconoAccion(actividad.accion) + ' mr-1'"></i>
                                            <span x-text="actividad.accion"></span>
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                        <span class="block nunito-bold text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Módulo</span>
                                        <span class="nunito-regular" x-text="actividad.modulo"></span>
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                        <span class="block nunito-bold text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Fecha / Hora</span>
                                        <span class="nunito-regular" x-text="actividad.fecha_hora"></span>
                                    </div>
                                </div>
                            </template>
                        </template>
                    </x-slot>
                </x-responsive-table>
            </div>
        </div>
    </div>

</div>

{{-- KPIs powered by global dashboardKPIs() from resources/js/dashboard.js --}}
