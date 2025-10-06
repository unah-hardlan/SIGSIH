<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Configurar Perfil - SIGSIH' }}</title>

    @vite([
        'resources/css/app.css',
        'resources/css/global.css',
        'resources/css/theme.css'
    ])

    <!-- Script de tema -->
    <script>
        (function() {
            try {
                const saved = localStorage.getItem('theme');
                const isDark = saved ? saved === 'dark' : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', !!isDark);
            } catch (_) {}
        })();
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Mejoras para el tema oscuro */
        .dark {
            color-scheme: dark;
        }
        
        /* Animaciones suaves - removiendo transición global que causaba conflictos */
        .smooth-transition {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }
        
        /* Transiciones específicas para inputs - evitar parpadeo */
        input, select, textarea {
            transition: border-color 0.2s ease-in-out !important;
        }
        
        input:focus, select:focus, textarea:focus {
            transition: border-color 0.15s ease-in-out !important;
        }
        
        /* Efectos de hover mejorados */
        .hover-scale:hover {
            transform: scale(1.02);
        }
        
        /* Backdrop blur personalizado */
        .backdrop-blur-lg {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        
        /* Eliminación de box-shadow conflictivo en focus */
        input:focus, select:focus, textarea:focus {
            box-shadow: none !important;
        }
        
        /* Estilos para el botón sticky de tema */
        .theme-toggle {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .theme-toggle:hover {
            transform: scale(1.05);
        }
        
        .theme-toggle:active {
            transform: scale(0.95);
        }
        
        /* Animación suave para el cambio de iconos */
        .theme-toggle svg {
            transition: opacity 0.2s ease-in-out;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-cyan-50 dark:from-gray-900 dark:via-gray-800 dark:to-indigo-900">
    <div class="min-h-screen flex items-center justify-center p-4">
        @yield('content')
    </div>

    <!-- Scripts para funcionalidad de avatar -->
    <script>
        // Preview de imagen con validación de tipo
        function previewImage(input) {
            const preview = document.getElementById('avatar-preview');
            const placeholder = document.getElementById('avatar-placeholder');
            const file = input.files[0];
            
            if (file) {
                // Validar tipo de archivo
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Solo se permiten imágenes de tipo JPEG, PNG o WEBP. No se permiten GIF ni videos.');
                    input.value = ''; // Limpiar el input
                    preview.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                    return;
                }
                
                // Validar tamaño (2MB = 2097152 bytes)
                if (file.size > 2097152) {
                    alert('La imagen no puede ser mayor a 2MB.');
                    input.value = '';
                    preview.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
            }
        }

        // Drag and drop
        function setupDragAndDrop() {
            const dropZone = document.getElementById('avatar-drop-zone');
            const fileInput = document.getElementById('avatar');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20', 'dark:border-blue-400');
                    dropZone.classList.remove('border-gray-300', 'dark:border-gray-600');
                }, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20', 'dark:border-blue-400');
                    dropZone.classList.add('border-gray-300', 'dark:border-gray-600');
                }, false);
            });
            
            dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 0) {
                    fileInput.files = files;
                    previewImage(fileInput);
                }
            }, false);
        }

        document.addEventListener('DOMContentLoaded', setupDragAndDrop);
    </script>
</body>

</html>