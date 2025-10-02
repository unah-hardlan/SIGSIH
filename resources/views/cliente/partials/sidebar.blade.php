<aside x-data="{ logoutModalOpen:false }" x-init="document.addEventListener('logout-modal-show',()=>logoutModalOpen=true); document.addEventListener('logout-modal-hide',()=>logoutModalOpen=false);" x-show="sidebarOpen" :class="{
        'fixed inset-y-0 left-0 w-72 min-w-[18rem] h-full': isMobile,
        'w-72 min-w-[18rem]': !isMobile && sidebarOpen,
        'w-20 min-w-[5rem]': !isMobile && !sidebarOpen,
        'brightness-50 pointer-events-none': logoutModalOpen,
        'brightness-100 pointer-events-auto': !logoutModalOpen,
    }"
    class="bg-gray-900 dark:bg-gray-800 text-gray-200 dark:text-gray-100 flex flex-col flex-shrink-0 p-0 shadow-lg transition-all duration-300 ease-in-out overflow-y-auto md:sticky md:top-0 md:h-screen"
    style="scrollbar-width: thin; scrollbar-color: #4B5563 #1F2937; -webkit-overflow-scrolling: touch; z-index: 9999;">

    @php
        /** @var \App\Models\Usuario|null $u */
        $u = Auth::user();
    @endphp

    <nav class="flex-1 flex flex-col py-4 pr-3">
        <ul class="space-y-2 flex-1 pl-2">
            @php
                $linkBase = 'flex items-center gap-3 rounded-md transition-colors group relative';
                $activeClasses = 'text-white bg-blue-600 shadow-sm';
                $inactiveClasses = 'text-gray-300 hover:bg-gray-700 hover:text-white';
            @endphp
            <li>
                <a href="{{ route('cliente.perfil') }}" class="{{$linkBase}} px-4 py-2 {{ request()->routeIs('cliente.perfil') ? $activeClasses : $inactiveClasses }}">
                    <span class="absolute left-0 top-0 h-full w-1 rounded-r bg-blue-400 opacity-0 group-[.bg-blue-600]:opacity-100 transition-opacity"></span>
                    <i class="fas fa-user w-5 text-center"></i>
                    <span :class="!sidebarOpen && 'hidden'" class="nunito-bold">Perfil</span>
                </a>
            </li>
            <li>
                <a href="{{ route('cliente.cotizaciones') }}" class="{{$linkBase}} px-4 py-2 {{ request()->routeIs('cliente.cotizaciones') ? $activeClasses : $inactiveClasses }}">
                    <span class="absolute left-0 top-0 h-full w-1 rounded-r bg-blue-400 opacity-0 group-[.bg-blue-600]:opacity-100 transition-opacity"></span>
                    <i class="fas fa-file-invoice w-5 text-center"></i>
                    <span :class="!sidebarOpen && 'hidden'" class="nunito-bold">Cotizaciones</span>
                </a>
            </li>
            <li>
                <a href="{{ route('cliente.ordenes') }}" class="{{$linkBase}} px-4 py-2 {{ request()->routeIs('cliente.ordenes') ? $activeClasses : $inactiveClasses }}">
                    <span class="absolute left-0 top-0 h-full w-1 rounded-r bg-blue-400 opacity-0 group-[.bg-blue-600]:opacity-100 transition-opacity"></span>
                    <i class="fas fa-clipboard-list w-5 text-center"></i>
                    <span :class="!sidebarOpen && 'hidden'" class="nunito-bold">Órdenes de Servicio</span>
                </a>
            </li>
            <li>
                <a href="{{ route('cliente.facturas') }}" class="{{$linkBase}} px-4 py-2 {{ request()->routeIs('cliente.facturas') ? $activeClasses : $inactiveClasses }}">
                    <span class="absolute left-0 top-0 h-full w-1 rounded-r bg-blue-400 opacity-0 group-[.bg-blue-600]:opacity-100 transition-opacity"></span>
                    <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
                    <span :class="!sidebarOpen && 'hidden'" class="nunito-bold">Facturación</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>