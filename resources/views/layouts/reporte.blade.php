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
            .no-print { display: none !important; }

            html, body {
                background: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .min-h-screen { min-height: auto !important; }
            .p-6 { padding: 0.75rem !important; }
            .mx-auto { margin-left: 0 !important; margin-right: 0 !important; }
            .max-w-5xl, .max-w-6xl, .max-w-xs, .max-w-full { max-width: 100% !important; }

            .flex { display: flex !important; flex-wrap: wrap !important; }
            .flex-col { flex-direction: column !important; }
            .md\:flex-row { flex-direction: row !important; }
            .items-center { align-items: center !important; }
            .justify-center { justify-content: center !important; }
            .justify-between { justify-content: space-between !important; }

            img.app-logo { max-width: var(--app-logo-max) !important; height: auto !important; }

                .shadow-sm { box-shadow: none !important; }
               
            table { page-break-inside: auto; width: 100% !important; border-collapse: collapse; }
            tr { page-break-inside: avoid; page-break-after: auto; }

            .hover\:bg-gray-50:hover { background-color: transparent !important; }

            .report-print-controls { display: none !important; }
        }

        @page {
            size: auto;
            margin: 1cm;
        }
    </style>
    <style>
        .report-print-controls {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1200;
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
     
        @media (max-width: 640px) {
            .report-print-controls {
                position: static !important;
                top: auto !important;
                right: auto !important;
                left: 0 !important;
                width: 100% !important;
                margin: 0 0 1rem 0 !important;
                display: flex !important;
                justify-content: center !important;
                gap: 0.5rem !important;
                padding: 0.25rem 0.5rem !important;
                background: transparent !important;
            }
            .report-print-controls button {
                flex: 1 1 auto !important;
                min-width: 0 !important;
            }
        }
    </style>
    @stack('head')
</head>
<body class="bg-white min-h-screen">
    @yield('content')
    @stack('scripts')
</body>
</html>
