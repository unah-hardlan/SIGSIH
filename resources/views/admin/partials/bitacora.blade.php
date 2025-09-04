{{-- resources/views/admin/partials/bitacora.blade.php --}}

<div x-data="{ isModalOpen: false }" class="max-w-6xl mx-auto py-8">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between gap-2 mb-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white nunito-bold">Bitácora</h2>
            <a href="/admin/reportes-header?modulo=Bitacora&fecha={{ now()->format('d-M-Y') }}" target="_blank"
               class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center gap-2 text-sm">
                <i class="fas fa-file-alt"></i> Generar Reporte
            </a>
        </div>
        @include('partials.filtros-generales', [
            'searchModel' => 'searchBitacora',
            'filtrosSelect' => [
                'accionBitacoraFiltro' => [
                    'label' => 'Acción',
                    'options' => ['Insertar', 'Actualizar', 'Eliminar', 'Login']
                ],
                'usuarioBitacoraFiltro' => [
                    'label' => 'Usuario',
                    'options' => ['admin', 'soporte']
                ]
            ],
            'ordenarOptions' => [
                'fecha_evento' => 'Fecha Evento',
                'usuario' => 'Usuario',
                'accion' => 'Acción'
            ]
        ])
        <div class="overflow-x-auto mt-5">
            <table class="min-w-full text-xs md:text-sm whitespace-nowrap">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">ID</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Fecha Evento</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Usuario</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Objeto</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Acción</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Descripción</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Creado por</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Fecha Creación</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b bg-white dark:bg-gray-900">
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">1</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">2025-07-31 10:00</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">admin</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">usuarios</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">Login</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">Inicio de sesión exitoso</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">admin</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">2025-07-31 10:00</td>
                    </tr>
                    <tr class="border-b bg-white dark:bg-gray-900">
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">2</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">2025-07-30 09:30</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">soporte</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">roles</td>
                        <td class="py-2 px-4 nunito-regular">Insertar</td>
                        <td class="py-2 px-4 nunito-regular">Creación de nuevo rol</td>
                        <td class="py-2 px-4 nunito-regular">soporte</td>
                        <td class="py-2 px-4 nunito-regular">2025-07-30 09:30</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
