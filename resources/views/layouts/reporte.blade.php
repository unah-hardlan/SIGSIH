<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/css/global.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <title>@yield('title', 'Reporte')</title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
            }
            .shadow-sm {
                box-shadow: none !important;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
        @page {
            size: landscape;
            margin: 1cm;
        }
    </style>
    @stack('head')
</head>
<body class="bg-white min-h-screen">
    @yield('content')
    @stack('scripts')
</body>
</html>
