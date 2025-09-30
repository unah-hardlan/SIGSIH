<aside x-data="{ logoutConfirm: false }" x-init="
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
    });
    " x-show="sidebarOpen" :class="{
        'fixed inset-y-0 left-0 w-72 min-w-[18rem] h-full': isMobile,
        'w-72 min-w-[18rem]': !isMobile && sidebarOpen,
        'w-20 min-w-[5rem]': !isMobile && !sidebarOpen
    }"
    class="bg-gray-900 dark:bg-gray-800 text-gray-200 dark:text-gray-100 flex flex-col flex-shrink-0 p-0 shadow-lg transition-all duration-300 ease-in-out overflow-y-auto md:sticky md:top-0 md:h-screen"
    style="scrollbar-width: thin; scrollbar-color: #4B5563 #1F2937; -webkit-overflow-scrolling: touch; z-index: 9999;">

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
    return $perm->can($u, [$title], 'consultar');
    }
    return !empty($subNames) ? $perm->can($u, $subNames, 'consultar') : false;
    };

    // Candidatos por módulo (con y sin acentos cuando aplica)
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
    $canCatalogo = $canModule('Catalogo', [
    'Acciones Realizadas','Administración de Facturas','Administracion de Facturas','Categorias de Ingresos y
    Gastos','Categorías de Ingresos y Gastos',
    'Estados CAI','Estados de Proyecto','Estados de Solicitud','Estados de Tickets','Estados del
    Calendario','Género','Genero','Perfiles',
    'Servicio Factura','Servicios Realizados','Tipo de Movimiento','Tipo de Objeto','Tipo de Personas','Tipo de
    Producto','Tipo de Visita','Ubicaciones'
    ]);
    @endphp

    <!-- Menú -->
    <nav class="flex-1 flex flex-col py-4">
        <ul class="space-y-3 flex-1">
            {{-- Dashboard --}}
            <li :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                <x-admin.sidebar-link href="#" :active="false" view-name="dashboard"
                    class="py-2 px-2 rounded-l-full no-flash">
                    <i class="fas fa-house-chimney w-5 text-center text-white"></i>
                    <span :class="!sidebarOpen && 'hidden'" class="nunito-bold">Dashboard</span>
                </x-admin.sidebar-link>
            </li>

            {{-- Seguridad --}}
            @if($canSeguridad)
            <li class="mt-2" x-data="sidebarDropdown('seguridad', false)" x-init="init()"
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
                    @if($perm->can($u, ['Gestión de Usuarios','Gestion de Usuarios','Usuarios'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="gestion-usuarios" class="py-1 px-3">
                            <i class="fas fa-user text-sm w-4 text-center"></i>
                            Gestión de Usuarios
                        </x-admin.sidebar-link>
                    </li>
                    @endif

                    @if($perm->can($u, ['Parámetros','Parametros'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="parametros" class="py-1 px-3">
                            <i class="fas fa-sliders-h text-sm w-4 text-center"></i>
                            Parámetros
                        </x-admin.sidebar-link>
                    </li>
                    @endif

                    @if($perm->can($u, ['Configuración de accesos','Configuracion de accesos'], 'consultar'))
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

            {{-- Clientes --}}
            @if($canClientes)
            <li class="mt-2" x-data="sidebarDropdown('clientes', false)" x-init="init()"
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
                    @if($perm->can($u, ['Empresas','Gestión de Empresas','Gestion de Empresas'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="gestion-empresas" class="py-1 px-3">
                            <i class="fas fa-building text-sm w-4 text-center"></i>
                            Gestión de Empresas
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Cotizaciones','Gestión de Cotizaciones','Gestion de Cotizaciones'],
                    'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="cotizaciones" class="py-1 px-3">
                            <i class="fas fa-file-invoice text-sm w-4 text-center"></i>
                            Gestión de Cotizaciones
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Solicitudes','Gestión de Solicitudes','Gestion de Solicitudes'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="solicitudes" class="py-1 px-3">
                            <i class="fas fa-envelope-open-text text-sm w-4 text-center"></i>
                            Gestión de Solicitudes
                        </x-admin.sidebar-link>
                    </li>
                    @endif

                    @if($perm->can($u, ['Órdenes de Servicios','Ordenes de Servicios','Ordenes de Servicio'],
                    'consultar'))
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


            {{-- Proyectos --}}
            @if($canProyectos)
            <li class="mt-2" x-data="sidebarDropdown('proyectos', false)" x-init="init()"
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
                    @if($perm->can($u, ['Gestión de proyectos','Gestion de proyectos'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="proyectos" class="py-1 px-3">
                            <i class="fas fa-cogs text-sm w-4 text-center"></i>
                            Gestión de proyectos
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Vista de proyectos','Proyectos (Vista)'], 'consultar'))
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

            {{-- Tickets --}}
            @if($canTickets)
            <li class="mt-2" x-data="sidebarDropdown('tickets', false)" x-init="init()"
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
                    @if($perm->can($u, ['Gestión de tickets','Gestion de tickets','Tickets'], 'consultar'))
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

            {{-- Calendario --}}
            @if($canCalendario)
            <li class="mt-2" x-data="sidebarDropdown('calendario', false)" x-init="init()"
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
                    @if($perm->can($u, ['Agencias'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="agencias" class="py-1 px-3">
                            <i class="fas fa-map-marker-alt text-sm w-4 text-center"></i>
                            Gestión de Agencias
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Gestión de Calendario','Gestion de Calendario'], 'consultar'))
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

            {{-- Facturación --}}
            @if($canFacturacion)
            <li class="mt-2" x-data="sidebarDropdown('facturacion', false)" x-init="init()"
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
                    @if($perm->can($u, ['Facturas','Gestión de Facturas','Gestion de Facturas'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="facturas" class="py-1 px-3">
                            <i class="fas fa-file-invoice-dollar text-sm w-4 text-center"></i>
                            Gestión de Facturas
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['CAI'], 'consultar'))
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

            {{-- Reportes --}}
            @if($canReportes)
            <li class="mt-2" x-data="sidebarDropdown('reportes', false)" x-init="init()"
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
                    @if($perm->can($u, ['Gestión de Reportes','Gestion de Reportes','Reportes'], 'consultar'))
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

            {{-- Inventario --}}
            @if($canInventario)
            <li class="mt-2" x-data="sidebarDropdown('inventario', false)" x-init="init()"
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
                    @if($perm->can($u, ['Productos'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="productos" class="py-1 px-3">
                            <i class="fas fa-box text-sm w-4 text-center"></i>
                            Gestión de Productos
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Kardex'], 'consultar'))
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

            {{-- Administración --}}
            @if($canAdministracion)
            <li class="mt-2" x-data="sidebarDropdown('administracion', false)" x-init="init()">
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
                    @if($perm->can($u, ['Gestión de personas','Gestion de personas'], 'consultar'))
                    <li :class="$store.perfil.firstTime ? 'opacity-50 pointer-events-none' : ''">
                        <x-admin.sidebar-link href="#" :active="false" view-name="gestion-personas" class="py-1 px-3">
                            <i class="fas fa-user-cog text-sm w-4 text-center"></i>
                            Gestión de personas
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Mi perfil','Perfil','Perfil de usuario','Mi cuenta'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="perfil" class="py-1 px-3">
                            <i class="fas fa-user-circle text-sm w-4 text-center"></i>
                            Mi perfil
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Bitácora','Bitacora'], 'consultar'))
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

            {{-- Mantenimiento --}}
            @if($canMantenimiento)
            <li class="mt-2" x-data="sidebarDropdown('mantenimiento', false)" x-init="init()"
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


            {{-- Catalogo --}}
            @if($canCatalogo)
            <li class="mt-2" x-data="sidebarDropdown('catalogo', false)" x-init="init()"
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
                    @if($perm->can($u, ['Acciones Realizadas'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-acciones-realizadas"
                            class="py-1 px-3">
                            <i class="fas fa-list-alt text-sm w-4 text-center"></i>
                            Acciones Realizadas
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Administración de Facturas','Administracion de Facturas'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-admin-facturas"
                            class="py-1 px-3">
                            <i class="fas fa-file-invoice-dollar text-sm w-4 text-center"></i>
                            Administración de Facturas
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Categorías de Ingresos y Gastos','Categorias de Ingresos y Gastos'],
                    'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-categorias-ingresos-gastos"
                            class="py-1 px-3">
                            <i class="fas fa-coins text-sm w-4 text-center"></i>
                            Categorías de Ingresos y Gastos
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Estados CAI'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-estados-cai"
                            class="py-1 px-3">
                            <i class="fas fa-barcode text-sm w-4 text-center"></i>
                            Estados CAI
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Estados de Proyecto'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-estados-proyecto"
                            class="py-1 px-3">
                            <i class="fas fa-project-diagram text-sm w-4 text-center"></i>
                            Estados de Proyecto
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Estados de Solicitud'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-estados-solicitud"
                            class="py-1 px-3">
                            <i class="fas fa-tasks text-sm w-4 text-center"></i>
                            Estados de Solicitud
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Estados de Tickets'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-estados-tickets"
                            class="py-1 px-3">
                            <i class="fas fa-ticket-alt text-sm w-4 text-center"></i>
                            Estados de Tickets
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Estados del Calendario'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-estados-calendario"
                            class="py-1 px-3">
                            <i class="fas fa-calendar-check text-sm w-4 text-center"></i>
                            Estados del Calendario
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Género','Genero'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-genero" class="py-1 px-3">
                            <i class="fas fa-venus-mars text-sm w-4 text-center"></i>
                            Genero
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Perfiles'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-perfil" class="py-1 px-3">
                            <i class="fas fa-user-shield text-sm w-4 text-center"></i>
                            Perfiles
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Servicio Factura'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-servicios-factura"
                            class="py-1 px-3">
                            <i class="fas fa-list text-sm w-4 text-center"></i>
                            Servicio Factura
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Servicios Realizados'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-servicios-realizados"
                            class="py-1 px-3">
                            <i class="fas fa-plus text-sm w-4 text-center"></i>
                            Servicios Realizados
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Tipo de Movimiento'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-tipo-movimiento"
                            class="py-1 px-3">
                            <i class="fas fa-clipboard-list text-sm w-4 text-center"></i>
                            Tipo de Movimiento
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Tipo de Objeto'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-tipo-objeto"
                            class="py-1 px-3">
                            <i class="fas fa-object-group text-sm w-4 text-center"></i>
                            Tipo de Objeto
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Tipo de Personas'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-tipo-persona"
                            class="py-1 px-3">
                            <i class="fas fa-user-tag text-sm w-4 text-center"></i>
                            Tipo de Personas
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Tipo de Producto'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-tipo-producto"
                            class="py-1 px-3">
                            <i class="fas fa-box text-sm w-4 text-center"></i>
                            Tipo de Producto
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Tipo de Visita'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-tipo-visita"
                            class="py-1 px-3">
                            <i class="fas fa-user-friends text-sm w-4 text-center"></i>
                            Tipo de Visita
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                    @if($perm->can($u, ['Ubicaciones'], 'consultar'))
                    <li>
                        <x-admin.sidebar-link href="#" :active="false" view-name="catalogo-ubicaciones"
                            class="py-1 px-3">
                            <i class="fas fa-map-marker-alt text-sm w-4 text-center"></i>
                            Ubicaciones
                        </x-admin.sidebar-link>
                    </li>
                    @endif
                </ul>




            </li>
            @endif
        </ul>
    </nav>

</aside>