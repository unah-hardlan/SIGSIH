<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Servicio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CDN Tailwind -->
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

<body class="bg-white text-gray-900 font-sans text-[13px] px-8 py-6">

    <!-- Encabezado -->
    <div class="flex flex-row items-center border-b-2 border-blue-900 pb-2 mb-6 w-full">
    <img src="{{ $appLogoUrl ?? asset('images/LOGO.png') }}" alt="Logo" class="app-logo mb-2 mr-4" style="--app-logo-max: {{ ($appLogoHeight ?? 96) }}px;">
        <div class="flex-1 flex justify-center">
            <div class="text-xl font-bold text-blue-900 uppercase tracking-wider text-center">Reporte de Servicio
            </div>
        </div>
    </div>

    <!-- Datos Principales -->
    <div class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <div class="flex items-center space-x-2">
                <span class="font-semibold w-36">ID Reporte:</span>
                <span>12345</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="font-semibold w-36">Fecha de Reporte:</span>
                <span>2025-08-01</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="font-semibold w-36">Orden de Servicio:</span>
                <span>OS-00874</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="font-semibold w-36">Tipo de Visita:</span>
                <span>Visita Técnica</span>
            </div>
        </div>
    </div>

    <!-- Servicio y Acción -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <div class="bg-blue-50 px-4 py-2 rounded-t-md font-semibold text-blue-900">Servicio Realizado</div>
            <div class="border border-blue-200 px-4 py-2 rounded-b-md min-h-[40px]">
                Mantenimiento preventivo de equipos informáticos.
            </div>
        </div>
        <div>
            <div class="bg-blue-50 px-4 py-2 rounded-t-md font-semibold text-blue-900">Acción Realizada</div>
            <div class="border border-blue-200 px-4 py-2 rounded-b-md min-h-[40px]">
                Revisión general, limpieza y ajustes de configuración.
            </div>
        </div>
    </div>

    <!-- Observaciones -->
    <div class="mb-10">
        <div class="bg-blue-900 text-white px-4 py-2 rounded-t-md font-semibold">Observaciones</div>
        <div class="border border-blue-900 px-4 py-2 rounded-b-md min-h-[56px]">
            No se reportan novedades adicionales. Todos los equipos quedaron funcionando.
        </div>
    </div>

    <!-- Firmas -->
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


<!-- Botón Generar PDF (no sale al imprimir) -->
<div class="mt-12 flex justify-center no-print">
    <button onclick="window.print()"
        class="bg-blue-900 text-white px-8 py-2 rounded shadow hover:bg-blue-700 text-base font-semibold transition">
        Generar PDF
    </button>
</div>

</html>