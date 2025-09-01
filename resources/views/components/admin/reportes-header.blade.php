<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div class="flex items-center gap-4">
    <img src="{{ $appLogoUrl ?? asset('images/logo.png') }}" alt="Logo" class="app-logo w-auto" style="--app-logo-max: {{ ($appLogoHeight ?? 96) }}px;">
        <div class="text-xs text-gray-700 leading-tight">
            <div class="nunito-regular">Col. Centro América Oeste, Zona 4, Bloque G, Casa 17</div>
            <div class="nunito-regular">Comayagüela, M.D.C. Francisco Morazán</div>
            <div class="nunito-regular">Teléfono: [504] 2227-0705, 9877-7244</div>
            <div class="nunito-regular">Asesor de venta: Edwyn Lagos</div>
        </div>
    </div>
    <div class="flex-1 flex flex-col items-end">
        <div class="text-3xl text-blue-900 nunito-bold tracking-wide mb-2 border-b-4 border-blue-900 pb-1 pr-1">
            REPORTE{{ isset($titulo) && $titulo ? ' - ' . strtoupper($titulo) : '' }}
        </div>
        <div class="bg-gray-200 rounded p-3 w-full max-w-xs flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <span class="nunito-bold text-xs text-gray-700">FECHA</span>
                <span class="nunito-regular text-xs bg-white rounded px-2 py-1">{{ $fecha ?? '' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="nunito-bold text-xs text-gray-700">MÓDULO</span>
                <span class="nunito-regular text-xs bg-white rounded px-2 py-1">{{ $modulo ?? '' }}</span>
            </div>
        </div>
    </div>
</div>
