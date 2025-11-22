<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Servicio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            background: #fff !important;
        }
    }
    </style>
</head>

<body class="bg-white text-gray-900 font-sans text-[13px] px-4 md:px-8 py-6">

    <div class="flex flex-row items-center border-b-2 border-blue-900 pb-2 mb-6 w-full">
        <img src="{{ $appLogoUrl ?? asset('images/LOGO.png') }}" alt="Logo"
                class="mb-2 mr-4 max-h-16 md:max-h-20 lg:max-h-24 w-auto object-contain"
                 data-fallback="{{ asset('images/LOGO.png') }}"
                 onerror="this.onerror=null;this.src=this.dataset.fallback;">
        <div class="flex-1 flex justify-center">
            <div class="text-xl font-bold text-blue-900 uppercase tracking-wider text-center">Reporte de Servicio
            </div>
        </div>
    </div>

    <div class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <div class="flex items-center space-x-2">
                <span class="font-semibold w-36">N° de Reporte:</span>
                <span>{{ request('id_reporte') }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="font-semibold w-36">Fecha de Reporte:</span>
                <span>{{ request('fecha_reporte') }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="font-semibold w-36">Orden de Servicio:</span>
                <span>{{ request('orden_servicio') }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="font-semibold w-36">Tipo de Visita:</span>
                <span>{{ request('tipo_visita') }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <div class="bg-blue-50 px-4 py-2 rounded-t-md font-semibold text-blue-900">Servicio Realizado</div>
            <div class="border border-blue-200 px-4 py-2 rounded-b-md min-h-[40px]">{{ request('servicio_realizado') }}
            </div>
        </div>
        <div>
            <div class="bg-blue-50 px-4 py-2 rounded-t-md font-semibold text-blue-900">Acción Realizada</div>
            <div class="border border-blue-200 px-4 py-2 rounded-b-md min-h-[40px]">{{ request('accion_realizada') }}
            </div>
        </div>
    </div>

    <div class="mb-10">
        <div class="bg-blue-900 text-white px-4 py-2 rounded-t-md font-semibold">Observaciones</div>
        <div class="border border-blue-900 px-4 py-2 rounded-b-md min-h-[56px]">{{ request('observaciones') }}</div>
    </div>

    <div class="flex justify-between mt-14">
        <div class="flex flex-col items-center">
            <div class="border-t border-gray-700 w-48"></div>
            <span class="text-xs mt-1 text-gray-700">Firma Técnico</span>
        </div>
        <div class="flex flex-col items-center">
            <div class="border-t border-gray-700 w-48"></div>
            <span class="text-xs mt-1 text-gray-700">Firma Cliente</span>
        </div>
    </div>

</body>


<div class="mt-12 flex justify-center no-print">
    <button onclick="window.print()"
        class="bg-blue-900 text-white px-8 py-2 rounded shadow hover:bg-blue-700 text-base font-semibold transition">
        Generar PDF
    </button>
</div>

</html>