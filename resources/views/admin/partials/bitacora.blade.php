{{-- resources/views/admin/partials/bitacora.blade.php --}}

<div x-data="{ isModalOpen: false }" class="max-w-6xl mx-auto py-8">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between gap-2 mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Bitácora</h2>
            <a href="/admin/reportes-header?modulo=Bitacora&fecha={{ now()->format('d-M-Y') }}" target="_blank"
               class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center justify-center gap-2">
                <i class="fas fa-file-alt"></i> Generar Reporte
            </a>
        </div>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <div class="flex-1">
                <input type="text" placeholder="Buscar registro..." class="border rounded px-3 py-2 w-full" />
            </div>
            <div class="flex gap-2">
                <select class="border rounded px-3 py-2">
                    <option value="">Acción</option>
                    <option>Insertar</option>
                    <option>Actualizar</option>
                    <option>Eliminar</option>
                    <option>Login</option>
                </select>
                <select class="border rounded px-3 py-2">
                    <option value="">Usuario</option>
                    <option>admin</option>
                    <option>soporte</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs md:text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-2 px-4 text-left">ID</th>
                        <th class="py-2 px-4 text-left">Fecha Evento</th>
                        <th class="py-2 px-4 text-left">Usuario</th>
                        <th class="py-2 px-4 text-left">Objeto</th>
                        <th class="py-2 px-4 text-left">Acción</th>
                        <th class="py-2 px-4 text-left">Descripción</th>
                        <th class="py-2 px-4 text-left">Creado por</th>
                        <th class="py-2 px-4 text-left">Fecha Creación</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="py-2 px-4">1</td>
                        <td class="py-2 px-4">2025-07-31 10:00</td>
                        <td class="py-2 px-4">admin</td>
                        <td class="py-2 px-4">usuarios</td>
                        <td class="py-2 px-4">Login</td>
                        <td class="py-2 px-4">Inicio de sesión exitoso</td>
                        <td class="py-2 px-4">admin</td>
                        <td class="py-2 px-4">2025-07-31 10:00</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2 px-4">2</td>
                        <td class="py-2 px-4">2025-07-30 09:30</td>
                        <td class="py-2 px-4">soporte</td>
                        <td class="py-2 px-4">roles</td>
                        <td class="py-2 px-4">Insertar</td>
                        <td class="py-2 px-4">Creación de nuevo rol</td>
                        <td class="py-2 px-4">soporte</td>
                        <td class="py-2 px-4">2025-07-30 09:30</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
