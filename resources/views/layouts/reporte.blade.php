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
            /* Hide UI elements not intended for print */
            .no-print { display: none !important; }

            /* Ensure page uses full width and consistent colors in print */
            html, body {
                background: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Relax large utility constraints that break layout on paper */
            .min-h-screen { min-height: auto !important; }
            .p-6 { padding: 0.75rem !important; }
            .mx-auto { margin-left: 0 !important; margin-right: 0 !important; }
            .max-w-5xl, .max-w-6xl, .max-w-xs, .max-w-full { max-width: 100% !important; }

            /* Make flex layouts wrap and adapt to printed page */
            .flex { display: flex !important; flex-wrap: wrap !important; }
            .flex-col { flex-direction: column !important; }
            .md\:flex-row { flex-direction: row !important; }
            .items-center { align-items: center !important; }
            .justify-center { justify-content: center !important; }
            .justify-between { justify-content: space-between !important; }

            /* Logo sizing */
            img.app-logo { max-width: var(--app-logo-max) !important; height: auto !important; }

                /* Remove shadows */
                .shadow-sm { box-shadow: none !important; }
                /* Allow blocks to break across pages when necessary to avoid large empty gaps.
                    If you need to protect a small card from being broken, add a specific
                    class (e.g. .report-card) to that element and use page-break-inside: avoid
                    for that class in the specific template. */

            /* Tables should occupy full printable width and avoid row breaks */
            table { page-break-inside: auto; width: 100% !important; border-collapse: collapse; }
            tr { page-break-inside: avoid; page-break-after: auto; }

            /* Ensure hover helper classes do not interfere */
            .hover\:bg-gray-50:hover { background-color: transparent !important; }

            /* Hide the on-screen sticky print controls during printing */
            .report-print-controls { display: none !important; }
        }

        /* Default page box: allow browser/user to choose orientation; forcing can cause scaling issues */
        @page {
            size: auto;
            margin: 1cm;
        }
    </style>
    <style>
        /* Sticky print controls (screen only) */
        .report-print-controls {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1200;
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        /* On very small screens place controls slightly inset */
        @media (max-width: 640px) {
            .report-print-controls { top: 0.5rem; right: 0.5rem; }
        }
    </style>
    @stack('head')
</head>
<body class="bg-white min-h-screen">
    @yield('content')
    @stack('scripts')
</body>
</html>
