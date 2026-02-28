<aside x-data="{ logoutModalOpen: false }" x-init="
    $nextTick(() => {
        if (window.sidebarScrollManager) {
            const savedScrollTop = localStorage.getItem('sidebar-scroll-position');
            if (savedScrollTop !== null) {
                $el.scrollTop = parseInt(savedScrollTop, 10);
            }
            
            // Configurar listener de scroll
            let scrollTimeout;
            $el.addEventListener('scroll', () => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    localStorage.setItem('sidebar-scroll-position', $el.scrollTop);
                }, 100);
            });
        }
        
        document.addEventListener('logout-modal-show', () => {
            logoutModalOpen = true;
        });
        
        document.addEventListener('logout-modal-hide', () => {
            logoutModalOpen = false;
        });
    });
    " x-show="sidebarOpen" :class="{
        'fixed inset-y-0 left-0 w-72 min-w-[18rem] h-full': isMobile,
        'w-72 min-w-[18rem]': !isMobile && sidebarOpen,
        'w-20 min-w-[5rem]': !isMobile && !sidebarOpen,
        'brightness-50 pointer-events-none': logoutModalOpen,
        'brightness-100 pointer-events-auto': !logoutModalOpen
    }"
    class="bg-gray-900 dark:bg-gray-800 text-gray-200 dark:text-gray-100 flex flex-col flex-shrink-0 p-0 shadow-lg transition-all duration-300 ease-in-out overflow-y-auto md:sticky md:top-0 md:h-screen"
    style="scrollbar-width: thin; scrollbar-color: #4B5563 #1F2937; -webkit-overflow-scrolling: touch;"
    :style="{ zIndex: (isMobile && sidebarOpen) ? 9999 : 40 }">

    @php
    use App\Services\PermissionService;
    use App\Models\Objeto;
    /** @var \App\Models\Usuario|null $u */
    $u = Auth::user();
    /** @var PermissionService $perm */
    $perm = app(PermissionService::class);

    $hasObj = function(string $name): bool {
    return Objeto::whereRaw('LOWER(nombre_objeto) = ?', [mb_strtolower($name)])->exists();
    };
    $canModule = function(string $title, array $subNames = []) use ($perm, $u, $hasObj): bool {
    if (!$u) return false;
    if ($hasObj($title)) {
    return $perm->can($u, [$title], 'ver');
    }
    return !empty($subNames) ? $perm->can($u, $subNames, 'ver') : false;
    };

    $canSeguridad = $canModule('Seguridad', ['Gestión de Usuarios','Gestion de
    Usuarios','Usuarios','Parámetros','Parametros','Configuración de accesos','Configuracion de accesos']);
    $canClientes = $canModule('Clientes', ['Empresas','Cotizaciones','Solicitudes','Órdenes de Servicios','Ordenes de
    Servicios']);
    $canProyectos = $canModule('Proyectos', ['Proyectos','Gestión de proyectos','Gestion de proyectos','Vista de
    proyectos']);
    $canTickets = $canModule('Tickets', ['Gestión de tickets','Gestion de tickets','Tickets']);
    $canCalendario = $canModule('Calendario', ['Agencias','Calendario','Gestión de Calendario','Gestion de
    Calendario']);
    $canFacturacion = $canModule('Facturación', ['Facturas','CAI','Facturacion']);
    $canReportes = $canModule('Reportes', ['Gestión de Reportes','Gestion de Reportes','Reportes']);
    $canInventario = $canModule('Inventario', ['Productos','Kardex']);
    $canAdministracion = $canModule('Administración', ['Gestión de personas','Gestion de personas','Mi
    perfil','Bitácora','Bitacora','Gestión de base de datos','Gestion de base de datos','Administracion']);
    $canMantenimiento = $canModule('Mantenimiento', ['Mantenimiento del Sistema','Mantenimiento del sistema']);
    $catalogoCandidates = ['Catalogo','Catálogo'];
    foreach (\App\Support\AdminModuleRegistry::views() as $def) {
    if (($def['module'] ?? null) === 'catalogo') {
    foreach (\Illuminate\Support\Arr::wrap($def['objects'] ?? []) as $nm) { $catalogoCandidates[] = $nm; }
    }
    }
    $canCatalogo = $canModule('Catalogo', array_values(array_unique($catalogoCandidates)));
    $catEstadosKeys = ['Estados CAI','Estados de Proyecto','Estados de Solicitud','Estados de Tickets','Estados del
    Calendario'];
    $canCatalogoEstados = $perm->can($u, $catEstadosKeys, 'ver');
    $catServiciosKeys = ['Servicio Factura','Servicios Realizados'];
    $canCatalogoServicios = $perm->can($u, $catServiciosKeys, 'ver');
    $catTiposKeys = ['Tipo de Movimiento','Tipo de Objeto','Tipo de Producto','Tipo de Visita','Tipo de Mantenimiento'];
    $canCatalogoTipos = $perm->can($u, $catTiposKeys, 'ver');
    @endphp

    <nav class="flex-1 flex flex-col py-4">
        <ul class="space-y-3 flex-1">
            <li :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                <x-admin.sidebar-link href="#" :active="false" view-name="dashboard"
                    class="py-2 px-2 rounded-l-full no-flash">
                    <i class="fas fa-house-chimney w-5 text-center text-white"></i>
                    <span :class="!sidebarOpen && 'hidden'" class="nunito-bold">Dashboard</span>
                </x-admin.sidebar-link>
            </li>

            @if($canSeguridad)
            <li class="mt-2" x-data="sidebarDropdown('seguridad', false)" x-init="init()"
                @close-all-dropdowns.window="close()"
                :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                <button @click="toggle()"
                    :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                    class="w-full flex items-center justify-between px-4 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-shield-alt w-5 text-center"></i>
                        <span :class="!sidebarOpen && 'hidden'" class="text-sm nunito-bold uppercase">Seguridad</span>
                    </div>
                    <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}" class="w-4 h-4 ml-2 transition-transform"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                    @if($perm->can($u, ['Gestión de Usuarios','Gestion de Usuarios','Usuarios'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="gestion-usuarios" class="py-1 px-3">
                            <i class="fas fa-user text-sm w-4 text-center"></i>
                            Gestión de Usuarios
                        </x-admin.sidebar-link>
                    </li>
                    @endif

                    @if($perm->can($u, ['Parámetros','Parametros'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="parametros" class="py-1 px-3">
                            <i class="fas fa-sliders-h text-sm w-4 text-center"></i>
                            Parámetros
                        </x-admin.sidebar-link>
                    </li>
                    @endif

                    @if($perm->can($u, ['Configuración de accesos','Configuracion de accesos'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="configuracion-acceso"
                            class="py-1 px-3">
                            <i class="fas fa-key text-sm w-4 text-center"></i>
                            Configuración de accesos al sistema
                        </x-admin.sidebar-link>
                    </li>
                    @endif

                </ul>
            </li>
            @endif

            @if($canClientes)
            <li class="mt-2" x-data="sidebarDropdown('clientes', false)" x-init="init()"
                @close-all-dropdowns.window="close()"
                :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                <button @click="toggle()"
                    :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                    class="w-full flex items-center justify-between px-4 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-users w-5 text-center"></i>
                        <span :class="!sidebarOpen && 'hidden'" class="text-sm nunito-bold uppercase">Clientes</span>
                    </div>
                    <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}" class="w-4 h-4 ml-2 transition-transform"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                    @if($perm->can($u, ['Empresas','Gestión de Empresas','Gestion de Empresas'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="gestion-empresas" class="py-1 px-3">
                            <i class="fas fa-building text-sm w-4 text-center"></i>
                            Gestión de Empresas
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Cotizaciones','Gestión de Cotizaciones','Gestion de Cotizaciones'],
                    'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="cotizaciones" class="py-1 px-3">
                            <i class="fas fa-file-invoice text-sm w-4 text-center"></i>
                            Gestión de Cotizaciones
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Solicitudes','Gestión de Solicitudes','Gestion de Solicitudes'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="solicitudes" class="py-1 px-3">
                            <i class="fas fa-envelope-open-text text-sm w-4 text-center"></i>
                            Gestión de Solicitudes
                        </x-admin.sidebar-link>
                    </li>
                    @endif

                    @if($perm->can($u, ['Órdenes de Servicios','Ordenes de Servicios','Ordenes de Servicio'],
                    'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="gestion-ordenes" class="py-1 px-3">
                            <i class="fas fa-plus text-sm w-4 text-center"></i>
                            Gestion Ordenes de Servicios
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                </ul>
            </li>
            @endif


            @if($canProyectos)
            <li class="mt-2" x-data="sidebarDropdown('proyectos', false)" x-init="init()"
                @close-all-dropdowns.window="close()"
                :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                <button @click="toggle()"
                    :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                    class="w-full flex items-center justify-between px-4 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-project-diagram w-5 text-center"></i>
                        <span :class="!sidebarOpen && 'hidden'" class="text-sm nunito-bold uppercase">Proyectos</span>
                    </div>
                    <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}" class="w-4 h-4 ml-2 transition-transform"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                    @if($perm->can($u, ['Gestión de proyectos','Gestion de proyectos'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="proyectos" class="py-1 px-3">
                            <i class="fas fa-cogs text-sm w-4 text-center"></i>
                            Gestión de proyectos
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Vista de proyectos','Proyectos (Vista)'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="vista-proyectos" class="py-1 px-3">
                            <i class="fas fa-eye text-sm w-4 text-center"></i>
                            Vista de proyectos
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if($canTickets)
            <li class="mt-2" x-data="sidebarDropdown('tickets', false)" x-init="init()"
                @close-all-dropdowns.window="close()"
                :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                <button @click="toggle()"
                    :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                    class="w-full flex items-center justify-between px-4 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-ticket-alt w-5 text-center"></i>
                        <span :class="!sidebarOpen && 'hidden'" class="text-sm nunito-bold uppercase">Tickets</span>
                    </div>
                    <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}" class="w-4 h-4 ml-2 transition-transform"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                    @if($perm->can($u, ['Gestión de tickets','Gestion de tickets','Tickets'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="tickets" class="py-1 px-3">
                            <i class="fas fa-ticket-alt text-sm w-4 text-center"></i>
                            Gestión de tickets
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if($canCalendario)
            <li class="mt-2" x-data="sidebarDropdown('calendario', false)" x-init="init()"
                @close-all-dropdowns.window="close()"
                :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                <button @click="toggle()"
                    :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                    class="w-full flex items-center justify-between px-4 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-calendar-alt w-5 text-center"></i>
                        <span :class="!sidebarOpen && 'hidden'" class="text-sm nunito-bold uppercase">Calendario</span>
                    </div>
                    <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}" class="w-4 h-4 ml-2 transition-transform"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                    @if($perm->can($u, ['Agencias'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="agencias" class="py-1 px-3">
                            <i class="fas fa-map-marker-alt text-sm w-4 text-center"></i>
                            Gestión de Agencias
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Gestión de Calendario','Gestion de Calendario'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="calendario" class="py-1 px-3">
                            <i class="fas fa-calendar-alt text-sm w-4 text-center"></i>
                            Calendario
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if($canFacturacion)
            <li class="mt-2" x-data="sidebarDropdown('facturacion', false)" x-init="init()"
                @close-all-dropdowns.window="close()"
                :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                <button @click="toggle()"
                    :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                    class="w-full flex items-center justify-between px-4 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
                        <span :class="!sidebarOpen && 'hidden'" class="text-sm nunito-bold uppercase">Facturación</span>
                    </div>
                    <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}" class="w-4 h-4 ml-2 transition-transform"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                    @if($perm->can($u, ['Facturas','Gestión de Facturas','Gestion de Facturas'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="facturas" class="py-1 px-3">
                            <i class="fas fa-file-invoice-dollar text-sm w-4 text-center"></i>
                            Gestión de Facturas
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['CAI'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="cai" class="py-1 px-3">
                            <i class="fas fa-barcode text-sm w-4 text-center"></i>
                            Gestión de CAI
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if($canReportes)
            <li class="mt-2" x-data="sidebarDropdown('reportes', false)" x-init="init()"
                @close-all-dropdowns.window="close()"
                :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                <button @click="toggle()"
                    :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                    class="w-full flex items-center justify-between px-4 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-chart-bar w-5 text-center"></i>
                        <span :class="!sidebarOpen && 'hidden'" class="text-sm nunito-bold uppercase">Reportes</span>
                    </div>
                    <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}" class="w-4 h-4 ml-2 transition-transform"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                    @if($perm->can($u, ['Gestión de Reportes','Gestion de Reportes','Reportes'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="reportes" class="py-1 px-3">
                            <i class="fas fa-file-alt text-sm w-4 text-center"></i>
                            Gestión de Reportes
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if($canInventario)
            <li class="mt-2" x-data="sidebarDropdown('inventario', false)" x-init="init()"
                @close-all-dropdowns.window="close()"
                :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                <button @click="toggle()"
                    :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                    class="w-full flex items-center justify-between px-4 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-boxes w-5 text-center"></i>
                        <span :class="!sidebarOpen && 'hidden'" class="text-sm nunito-bold uppercase">Inventario</span>
                    </div>
                    <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}" class="w-4 h-4 ml-2 transition-transform"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                    @if($perm->can($u, ['Productos'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="productos" class="py-1 px-3">
                            <i class="fas fa-box text-sm w-4 text-center"></i>
                            Gestión de Productos
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Kardex'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="kardex" class="py-1 px-3">
                            <i class="fas fa-archive text-sm w-4 text-center"></i>
                            Gestión de Kardex
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if($canAdministracion)
            <li class="mt-2" x-data="sidebarDropdown('administracion', false)" x-init="init()"
                @close-all-dropdowns.window="close()">
                <button @click="toggle()"
                    :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                    class="w-full flex items-center justify-between px-4 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-cogs w-5 text-center"></i>
                        <span :class="!sidebarOpen && 'hidden'"
                            class="text-sm nunito-bold uppercase">Administración</span>
                    </div>
                    <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}" class="w-4 h-4 ml-2 transition-transform"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                    @if($perm->can($u, ['Gestión de personas','Gestion de personas'], 'ver'))
                    <li :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                        <x-admin.sidebar-link href="#" :active="false" view-name="gestion-personas" class="py-1 px-3">
                            <i class="fas fa-user-cog text-sm w-4 text-center"></i>
                            Gestión de personas
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Mi perfil','Perfil','Perfil de usuario','Mi cuenta'], 'ver'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="perfil" class="py-1 px-3">
                            <i class="fas fa-user-circle text-sm w-4 text-center"></i>
                            Mi perfil
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Bitácora','Bitacora'], 'ver'))
                    <li :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                        <x-admin.sidebar-link href="#" :active="false" view-name="bitacora" class="py-1 px-3">
                            <i class="fas fa-book text-sm w-4 text-center"></i>
                            Bitácora
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Gestión de base de datos','Gestion de base de datos'], 'consultar'))
                    <li :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                        <x-admin.sidebar-link href="#" :active="false" view-name="gestion-db" class="py-1 px-3">
                            <i class="fas fa-database text-sm w-4 text-center"></i>
                            Gestión de Base de Datos
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if($canMantenimiento)
            <li class="mt-2" x-data="sidebarDropdown('mantenimiento', false)" x-init="init()"
                @close-all-dropdowns.window="close()"
                :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                <button @click="toggle()"
                    :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                    class="w-full flex items-center justify-between px-4 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-tools w-5 text-center"></i>
                        <span :class="!sidebarOpen && 'hidden'"
                            class="text-sm nunito-bold uppercase">Mantenimiento</span>
                    </div>
                    <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}" class="w-4 h-4 ml-2 transition-transform"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                    @if($perm->can($u, ['Mantenimiento del Sistema','Mantenimiento del sistema'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="mantenimiento-general"
                            class="py-1 px-3">
                            <i class="fas fa-wrench text-sm w-4 text-center"></i>
                            Mantenimiento del Sistema
                        </x-admin.sidebar-link>
                    </li>
                    @endif

                </ul>


            </li>
            @endif

            @if($canCatalogo)
            <li class="mt-2" x-data="sidebarDropdown('catalogo', false)" x-init="init()"
                @close-all-dropdowns.window="close()"
                :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                <button @click="toggle()"
                    :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                    class="w-full flex items-center justify-between px-4 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-book-open w-5 text-center"></i>
                        <span :class="!sidebarOpen && 'hidden'" class="text-sm nunito-bold uppercase">Catalogo</span>
                    </div>
                    <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}" class="w-4 h-4 ml-2 transition-transform"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                    @if($canCatalogoEstados)
                    <li x-data="sidebarDropdown('catalogo-estados', false)" x-init="init()"
                        @close-all-dropdowns.window="close()">
                        <button @click="toggle()"
                            :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                            class="w-full flex items-center justify-between px-3 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-flag w-4 text-center"></i>
                                <span :class="!sidebarOpen && 'hidden'"
                                    class="text-sm nunito-bold uppercase">Estados</span>
                            </div>
                            <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}"
                                class="w-4 h-4 ml-2 transition-transform" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                            @if($perm->can($u, ['Estados CAI'], 'ver'))
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-estados-cai"
                                    class="py-1 px-3">
                                    <i class="fas fa-barcode text-sm w-4 text-center"></i>
                                    Estados CAI
                                </x-admin.sidebar-link>
                            </li>
                            @endif

                            @if($perm->can($u, ['Estados de Proyecto'], 'ver'))
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-estados-proyecto"
                                    class="py-1 px-3">
                                    <i class="fas fa-project-diagram text-sm w-4 text-center"></i>
                                    Estados de Proyecto
                                </x-admin.sidebar-link>
                            </li>
                            @endif

                            @if($perm->can($u, ['Estados de Solicitud'], 'ver'))
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-estados-solicitud"
                                    class="py-1 px-3">
                                    <i class="fas fa-tasks text-sm w-4 text-center"></i>
                                    Estados de Solicitud
                                </x-admin.sidebar-link>
                            </li>
                            @endif

                            @if($perm->can($u, ['Estados de Tickets'], 'ver'))
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-estados-tickets"
                                    class="py-1 px-3">
                                    <i class="fas fa-ticket-alt text-sm w-4 text-center"></i>
                                    Estados de Tickets
                                </x-admin.sidebar-link>
                            </li>
                            @endif

                            @if($perm->can($u, ['Estados del Calendario'], 'ver'))
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-estados-calendario"
                                    class="py-1 px-3">
                                    <i class="fas fa-calendar-check text-sm w-4 text-center"></i>
                                    Estados del Calendario
                                </x-admin.sidebar-link>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if($canCatalogoServicios)
                    <li x-data="sidebarDropdown('catalogo-servicios', false)" x-init="init()"
                        @close-all-dropdowns.window="close()">
                        <button @click="toggle()"
                            :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                            class="w-full flex items-center justify-between px-3 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-concierge-bell w-4 text-center"></i>
                                <span :class="!sidebarOpen && 'hidden'"
                                    class="text-sm nunito-bold uppercase">Servicios</span>
                            </div>
                            <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}"
                                class="w-4 h-4 ml-2 transition-transform" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                            @if($perm->can($u, ['Servicio Factura'], 'ver'))
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-servicios-factura"
                                    class="py-1 px-3">
                                    <i class="fas fa-list text-sm w-4 text-center"></i>
                                    Servicio Factura
                                </x-admin.sidebar-link>
                            </li>
                            @endif

                            @if($perm->can($u, ['Servicios Realizados'], 'ver'))
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-servicios-realizados"
                                    class="py-1 px-3">
                                    <i class="fas fa-plus text-sm w-4 text-center"></i>
                                    Servicios Realizados
                                </x-admin.sidebar-link>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if($canCatalogoTipos)
                    <li x-data="sidebarDropdown('catalogo-tipos', false)" x-init="init()"
                        @close-all-dropdowns.window="close()">
                        <button @click="toggle()"
                            :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                            class="w-full flex items-center justify-between px-3 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-tags w-4 text-center"></i>
                                <span :class="!sidebarOpen && 'hidden'"
                                    class="text-sm nunito-bold uppercase">Tipos</span>
                            </div>
                            <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}"
                                class="w-4 h-4 ml-2 transition-transform" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                            @if($perm->can($u, ['Tipo de Movimiento'], 'ver'))
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-tipo-movimiento"
                                    class="py-1 px-3">
                                    <i class="fas fa-clipboard-list text-sm w-4 text-center"></i>
                                    Tipo de Movimiento
                                </x-admin.sidebar-link>
                            </li>
                            @endif

                            @if($perm->can($u, ['Tipo de Objeto'], 'ver'))
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-tipo-objeto"
                                    class="py-1 px-3">
                                    <i class="fas fa-object-group text-sm w-4 text-center"></i>
                                    Tipo de Objeto
                                </x-admin.sidebar-link>
                            </li>
                            @endif

                            @if($perm->can($u, ['Tipo de Producto'], 'ver'))
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-tipo-producto"
                                    class="py-1 px-3">
                                    <i class="fas fa-box text-sm w-4 text-center"></i>
                                    Tipo de Producto
                                </x-admin.sidebar-link>
                            </li>
                            @endif

                            @if($perm->can($u, ['Tipo de Visita'], 'ver'))
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-tipo-visita"
                                    class="py-1 px-3">
                                    <i class="fas fa-user-friends text-sm w-4 text-center"></i>
                                    Tipo de Visita
                                </x-admin.sidebar-link>
                            </li>
                            @endif

                            @if($perm->can($u, ['Tipo de Mantenimiento'], 'ver'))
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-tipo-mantenimiento"
                                    class="py-1 px-3">
                                    <i class="fas fa-wrench text-sm w-4 text-center"></i>
                                    Tipo de Mantenimiento
                                </x-admin.sidebar-link>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if($perm->can($u, ['Acciones Realizadas'], 'ver'))
                    <li x-data="sidebarDropdown('catalogo-acciones-realizadas', false)" x-init="init()"
                        @close-all-dropdowns.window="close()">
                        <button @click="toggle()"
                            :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                            class="w-full flex items-center justify-between px-3 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-list-alt w-4 text-center"></i>
                                <span :class="!sidebarOpen && 'hidden'"
                                    class="text-sm nunito-bold uppercase">Acciones</span>
                            </div>
                            <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}"
                                class="w-4 h-4 ml-2 transition-transform" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-acciones-realizadas"
                                    class="py-1 px-3">
                                    <i class="fas fa-list-alt text-sm w-4 text-center"></i>
                                    Acciones Realizadas
                                </x-admin.sidebar-link>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @if($perm->can($u, ['Administración de Facturas','Administracion de Facturas'], 'ver'))
                    <li x-data="sidebarDropdown('catalogo-admin-facturas', false)" x-init="init()"
                        @close-all-dropdowns.window="close()">
                        <button @click="toggle()"
                            :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                            class="w-full flex items-center justify-between px-3 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-file-invoice-dollar w-4 text-center"></i>
                                <span :class="!sidebarOpen && 'hidden'"
                                    class="text-sm nunito-bold uppercase">Administración</span>
                            </div>
                            <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}"
                                class="w-4 h-4 ml-2 transition-transform" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-admin-facturas"
                                    class="py-1 px-3">
                                    <i class="fas fa-file-invoice-dollar text-sm w-4 text-center"></i>
                                    Administración de Facturas
                                </x-admin.sidebar-link>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @if($perm->can($u, ['Categorías de Ingresos y Gastos','Categorias de Ingresos y Gastos'], 'ver'))
                    <li x-data="sidebarDropdown('catalogo-categorias-ingresos-gastos', false)" x-init="init()"
                        @close-all-dropdowns.window="close()">
                        <button @click="toggle()"
                            :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                            class="w-full flex items-center justify-between px-3 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-coins w-4 text-center"></i>
                                <span :class="!sidebarOpen && 'hidden'"
                                    class="text-sm nunito-bold uppercase">Categorías</span>
                            </div>
                            <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}"
                                class="w-4 h-4 ml-2 transition-transform" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                            <li>
                                <x-admin.sidebar-link href="#" :active="false"
                                    view-name="catalogo-categorias-ingresos-gastos" class="py-1 px-3">
                                    <i class="fas fa-coins text-sm w-4 text-center"></i>
                                    Categorías de Ingresos y Gastos
                                </x-admin.sidebar-link>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @if($perm->can($u, ['Género','Genero'], 'ver'))
                    <li x-data="sidebarDropdown('catalogo-genero', false)" x-init="init()"
                        @close-all-dropdowns.window="close()">
                        <button @click="toggle()"
                            :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                            class="w-full flex items-center justify-between px-3 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-venus-mars w-4 text-center"></i>
                                <span :class="!sidebarOpen && 'hidden'"
                                    class="text-sm nunito-bold uppercase">Género</span>
                            </div>
                            <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}"
                                class="w-4 h-4 ml-2 transition-transform" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-genero"
                                    class="py-1 px-3">
                                    <i class="fas fa-venus-mars text-sm w-4 text-center"></i>
                                    Género
                                </x-admin.sidebar-link>
                            </li>
                        </ul>
                    </li>
                    @endif

                    @if($perm->can($u, ['Origen Kardex','Origenes','Origen'], 'ver'))
                    <li x-data="sidebarDropdown('catalogo-origen-kardex', false)" x-init="init()"
                        @close-all-dropdowns.window="close()">
                        <button @click="toggle()"
                            :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                            class="w-full flex items-center justify-between px-3 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-map-signs w-4 text-center"></i>
                                <span :class="!sidebarOpen && 'hidden'" class="text-sm nunito-bold uppercase">Origen
                                    Kardex</span>
                            </div>
                            <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}"
                                class="w-4 h-4 ml-2 transition-transform" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-origen-kardex"
                                    class="py-1 px-3">
                                    <i class="fas fa-map-signs text-sm w-4 text-center"></i>
                                    Origen Kardex
                                </x-admin.sidebar-link>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @if($perm->can($u, ['Ubicaciones'], 'ver'))
                    <li x-data="sidebarDropdown('catalogo-ubicaciones', false)" x-init="init()"
                        @close-all-dropdowns.window="close()">
                        <button @click="toggle()"
                            :class="open ? 'bg-gray-800 text-yellow-400 dark:bg-gray-700 dark:text-blue-400' : 'text-gray-400 dark:text-gray-300'"
                            class="w-full flex items-center justify-between px-3 py-1.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-600">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-map-marker-alt w-4 text-center"></i>
                                <span :class="!sidebarOpen && 'hidden'"
                                    class="text-sm nunito-bold uppercase">Ubicaciones</span>
                            </div>
                            <svg :class="{'rotate-90': open, 'hidden': !sidebarOpen}"
                                class="w-4 h-4 ml-2 transition-transform" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <ul x-show="open && sidebarOpen" x-transition class="space-y-0.5 ml-4 mt-1">
                            <li>
                                <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-ubicaciones"
                                    class="py-1 px-3">
                                    <i class="fas fa-map-marker-alt text-sm w-4 text-center"></i>
                                    Ubicaciones
                                </x-admin.sidebar-link>
                            </li>
                        </ul>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
        </ul>
    </nav>

    <div class="sticky bottom-0 p-4 border-t border-gray-700 dark:border-gray-600 bg-gray-900 dark:bg-gray-800">
        <button @click="$dispatch('close-all-dropdowns')"
            class="w-full flex items-center gap-3 px-4 py-2 text-gray-400 dark:text-gray-300 hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors rounded-lg">
            <i class="fas fa-times w-5 text-center"></i>
            <span :class="!sidebarOpen && 'hidden'" class="text-sm nunito-bold">Cerrar desplegables</span>
        </button>
    </div>

</aside>