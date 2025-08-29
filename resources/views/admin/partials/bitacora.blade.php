{{-- resources/views/admin/partials/bitacora.blade.php --}}

<div x-data="{ isModalOpen: false }" class="max-w-6xl mx-auto py-8">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between gap-2 mb-4">
            <h2 class="text-2xl font-bold text-gray-800 nunito-bold">Bitácora</h2>
            <a href="/admin/reportes-header?modulo=Bitacora&fecha={{ now()->format('d-M-Y') }}" target="_blank"
               class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center gap-2">
                <i class="fas fa-file-alt"></i> Generar Reporte
            </a>
        </div>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <div class="flex-1">
                <input type="text" placeholder="Buscar registro..." class="border rounded px-3 py-2 w-full nunito-regular" />
            </div>
            <div class="flex gap-2">
                <select class="border rounded px-3 py-2 nunito-regular">
                    <option class="nunito-regular" value="">Acción</option>
                    <option class="nunito-regular">Insertar</option>
                    <option class="nunito-regular">Actualizar</option>
                    <option class="nunito-regular">Eliminar</option>
                    <option class="nunito-regular">Login</option>
                </select>
                <select class="border rounded px-3 py-2 nunito-regular">
                    <option class="nunito-regular" value="">Usuario</option>
                    <option class="nunito-regular">admin</option>
                    <option class="nunito-regular">soporte</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs md:text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-2 px-4 text-left nunito-bold">ID</th>
                        <th class="py-2 px-4 text-left nunito-bold">Fecha Evento</th>
                        <th class="py-2 px-4 text-left nunito-bold">Usuario</th>
                        <th class="py-2 px-4 text-left nunito-bold">Objeto</th>
                        <th class="py-2 px-4 text-left nunito-bold">Acción</th>
                        <th class="py-2 px-4 text-left nunito-bold">Descripción</th>
                        <th class="py-2 px-4 text-left nunito-bold">Creado por</th>
                        <th class="py-2 px-4 text-left nunito-bold">Fecha Creación</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="py-2 px-4 nunito-regular">1</td>
                        <td class="py-2 px-4 nunito-regular">2025-07-31 10:00</td>
                        <td class="py-2 px-4 nunito-regular">admin</td>
                        <td class="py-2 px-4 nunito-regular">usuarios</td>
                        <td class="py-2 px-4 nunito-regular">Login</td>
                        <td class="py-2 px-4 nunito-regular">Inicio de sesión exitoso</td>
                        <td class="py-2 px-4 nunito-regular">admin</td>
                        <td class="py-2 px-4 nunito-regular">2025-07-31 10:00</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2 px-4 nunito-regular">2</td>
                        <td class="py-2 px-4 nunito-regular">2025-07-30 09:30</td>
                        <td class="py-2 px-4 nunito-regular">soporte</td>
                        <td class="py-2 px-4 nunito-regular">roles</td>
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
