<div class="container mx-auto space-y-6">
    {{-- Header con navegación de proyecto y botón de nuevo proyecto --}}
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <button class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300"><i class="fas fa-chevron-left"></i></button>
            <h2 class="text-xl nunito-bold text-gray-800 dark:text-white">Proyecto BAC</h2>
            <button class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300"><i class="fas fa-chevron-right"></i></button>
        </div>
       <div class="bg-transparent items-center justify-center flex">
        <a href="{{ route('admin.proyecto-pdf') }}" target="_blank" class="flex items-center gap-2 px-6 py-2 border-2 border-emerald-500 rounded-md text-emerald-500 dark:text-emerald-400 nunito-bold text-sm hover:bg-emerald-500 hover:text-white transition-colors duration-300 w-full min-w-[170px] justify-center">
            <i class="fas fa-file-pdf"></i>
            Generar PDF
        </a>
</div>

    </div>
    {{-- Tarjetas de estadísticas (diseño moderno) --}}
    <div class="sticky top-4 z-10 grid grid-cols-1 sm:grid-cols-3 gap-6 bg-gray-50 dark:bg-gray-800 -mx-6 px-6 py-4 rounded-lg">
        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm dark:shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm nunito-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ingresos</p>
                    <p class="text-2xl nunito-bold text-emerald-600 dark:text-emerald-400 mt-2">L. 29,230.00</p>
                </div>
                <div class="w-6 h-6 bg-emerald-100 dark:bg-emerald-900 rounded-full flex items-center justify-center">
                    <div class="w-3 h-3 bg-emerald-500 dark:bg-emerald-400 rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm dark:shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm nunito-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Gastos</p>
                    <p class="text-2xl nunito-bold text-red-600 dark:text-red-400 mt-2">L. 15,983.00</p>
                </div>
                <div class="w-6 h-6 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                    <div class="w-3 h-3 bg-red-500 dark:bg-red-400 rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm dark:shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm nunito-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Balance</p>
                    <p class="text-2xl nunito-bold text-blue-600 dark:text-blue-400 mt-2">L. 13,247.00</p>
                </div>
                <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                    <div class="w-3 h-3 bg-blue-500 dark:bg-blue-400 rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Timeline de Movimientos (diseño temporal como en el adjunto) --}}
    <div class="mt-6">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm dark:shadow-lg border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg nunito-bold text-gray-800 dark:text-white">Historial de Movimientos</h3>
                <div class="text-sm text-gray-600 dark:text-gray-400 nunito-regular">Total: <span class="nunito-bold text-gray-800 dark:text-gray-200">4 movimientos</span></div>
            </div>
            
            {{-- Timeline de movimientos --}}
            <div class="relative">
                {{-- Línea vertical del timeline --}}
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-600"></div>
                
                <div class="space-y-6">
                    {{-- Movimiento 1: Ingreso --}}
                    <div class="relative flex items-start gap-6">
                        {{-- Punto del timeline --}}
                        <div class="relative z-10 flex items-center justify-center w-12 h-12 bg-red-100 dark:bg-red-900 border-4 border-white dark:border-gray-800 rounded-full shadow-sm">
                            <div class="w-3 h-3 bg-red-500 dark:bg-red-400 rounded-full"></div>
                        </div>
                        
                        {{-- Contenido del movimiento --}}
                        <div class="flex-1 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-600 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="text-base nunito-bold text-gray-800 dark:text-white">Desarrollo App Móvil</div>
                                    <div class="text-sm nunito-regular text-gray-500 dark:text-gray-400 mt-1">Cliente A</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300 nunito-regular mt-2">Pago inicial del cliente por el desarrollo de la aplicación.</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500 nunito-regular mt-3">15 de Agosto, 2024</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xl nunito-bold text-emerald-600 dark:text-emerald-400">+$1,200.00</div>
                                    <div class="mt-2">
                                        <span class="inline-block bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 text-xs nunito-bold px-3 py-1 rounded-full">Ingreso</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Movimiento 2: Gasto --}}
                    <div class="relative flex items-start gap-6">
                        {{-- Punto del timeline --}}
                        <div class="relative z-10 flex items-center justify-center w-12 h-12 bg-red-100 dark:bg-red-900 border-4 border-white dark:border-gray-800 rounded-full shadow-sm">
                            <div class="w-3 h-3 bg-red-500 dark:bg-red-400 rounded-full"></div>
                        </div>
                        
                        {{-- Contenido del movimiento --}}
                        <div class="flex-1 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-600 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="text-base nunito-bold text-gray-800 dark:text-white">Licencia de Software</div>
                                    <div class="text-sm nunito-regular text-gray-500 dark:text-gray-400 mt-1">Herramientas</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300 nunito-regular mt-2">Compra de licencia anual para el Entorno de Desarrollo Integrado (IDE).</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500 nunito-regular mt-3">16 de Agosto, 2024</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xl nunito-bold text-red-600 dark:text-red-400">-$75.00</div>
                                    <div class="mt-2">
                                        <span class="inline-block bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-300 text-xs nunito-bold px-3 py-1 rounded-full">Gasto</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Movimiento 3: Gasto --}}
                    <div class="relative flex items-start gap-6">
                        {{-- Punto del timeline --}}
                        <div class="relative z-10 flex items-center justify-center w-12 h-12 bg-red-100 dark:bg-red-900 border-4 border-white dark:border-gray-800 rounded-full shadow-sm">
                            <div class="w-3 h-3 bg-red-500 dark:bg-red-400 rounded-full"></div>
                        </div>
                        
                        {{-- Contenido del movimiento --}}
                        <div class="flex-1 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-600 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="text-base nunito-bold text-gray-800 dark:text-white">Servicios de Hosting</div>
                                    <div class="text-sm nunito-regular text-gray-500 dark:text-gray-400 mt-1">Infraestructura</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300 nunito-regular mt-2">Pago mensual correspondiente al servidor en la nube.</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500 nunito-regular mt-3">20 de Agosto, 2024</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xl nunito-bold text-red-600 dark:text-red-400">-$50.00</div>
                                    <div class="mt-2">
                                        <span class="inline-block bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-300 text-xs nunito-bold px-3 py-1 rounded-full">Gasto</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Movimiento 4: Ingreso --}}
                    <div class="relative flex items-start gap-6">
                        {{-- Punto del timeline --}}
                        <div class="relative z-10 flex items-center justify-center w-12 h-12 bg-red-100 dark:bg-red-900 border-4 border-white dark:border-gray-800 rounded-full shadow-sm">
                            <div class="w-3 h-3 bg-red-500 dark:bg-red-400 rounded-full"></div>
                        </div>
                        
                        {{-- Contenido del movimiento --}}
                        <div class="flex-1 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-600 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="text-base nunito-bold text-gray-800 dark:text-white">Consultoría SEO</div>
                                    <div class="text-sm nunito-regular text-gray-500 dark:text-gray-400 mt-1">Cliente B</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300 nunito-regular mt-2">Adelanto por servicios de optimización para motores de búsqueda.</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500 nunito-regular mt-3">22 de Agosto, 2024</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xl nunito-bold text-emerald-600 dark:text-emerald-400">+$600.00</div>
                                    <div class="mt-2">
                                        <span class="inline-block bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 text-xs nunito-bold px-3 py-1 rounded-full">Ingreso</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
