<div class="container mx-auto space-y-6">
    {{-- Header con navegación de proyecto y botón de nuevo proyecto --}}
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <button class="p-2 rounded hover:bg-gray-200"><i class="fas fa-chevron-left"></i></button>
            <h2 class="text-xl nunito-bold">Proyecto BAC</h2>
            <button class="p-2 rounded hover:bg-gray-200"><i class="fas fa-chevron-right"></i></button>
        </div>
       <div class="bg-transparent items-center justify-center flex">
        <a href="{{ route('admin.proyecto-pdf') }}" target="_blank" class="flex items-center gap-2 px-6 py-2 border-2 border-emerald-500 rounded-md text-emerald-500 nunito-bold text-sm hover:bg-emerald-500 hover:text-white transition-colors duration-300 w-full min-w-[170px] justify-center">
            <i class="fas fa-file-pdf"></i>
            Generar PDF
        </a>
</div>

    </div>
    {{-- Tarjetas de estadísticas --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 nunito-bold">
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500 nunito-bold">Ingresos</p>
            <p class="text-lg font-semibold nunito-regular">L. 29,230.00</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500 nunito-bold">Gastos</p>
            <p class="text-lg font-semibold nunito-regular">L. 15,983.00</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500 nunito-bold">Balance</p>
            <p class="text-lg font-semibold nunito-regular">L. 13,247.00</p>
        </div>
    </div>

    {{-- Tabla de Movimientos --}}
    <div class="overflow-x-auto mt-6">
        <div class="bg-white rounded-lg shadow p-6">
            <table class="min-w-full text-sm border-collapse border border-gray-600">
                <div class="block xl:hidden">
                    <!-- Mobile card/list design -->
                    <div class="space-y-4">
                        <div class="bg-emerald-100 rounded-lg shadow p-4 border border-emerald-300">
                            <div class="flex flex-col gap-1">
                                <span class="nunito-bold text-gray-700">Nombre: <span class="nunito-regular">Pago inicial</span></span>
                                <span class="nunito-bold text-gray-700">Fecha: <span class="nunito-regular">2025-07-20</span></span>
                                <span class="nunito-bold text-gray-700">Monto: <span class="nunito-regular">L. 15,000.00</span></span>
                                <span class="nunito-bold text-gray-700">Categoría: <span class="nunito-regular">Ingreso</span></span>
                                <span class="nunito-bold text-gray-700">Descripción: <span class="nunito-regular">Primer pago del Proyecto Alpha</span></span>
                                <span class="nunito-bold text-gray-700">Movimiento: <span class="nunito-regular">Ingreso</span></span>
                            </div>
                        </div>
                        <div class="bg-slate-200 rounded-lg shadow p-4 border border-slate-400">
                            <div class="flex flex-col gap-1">
                                <span class="nunito-bold text-gray-700">Nombre: <span class="nunito-regular">Compra de software</span></span>
                                <span class="nunito-bold text-gray-700">Fecha: <span class="nunito-regular">2025-07-22</span></span>
                                <span class="nunito-bold text-gray-700">Monto: <span class="nunito-regular">L. 5,500.00</span></span>
                                <span class="nunito-bold text-gray-700">Categoría: <span class="nunito-regular">Gasto</span></span>
                                <span class="nunito-bold text-gray-700">Descripción: <span class="nunito-regular">Licencias de software de desarrollo</span></span>
                                <span class="nunito-bold text-gray-700">Movimiento: <span class="nunito-regular">Gasto</span></span>
                            </div>
                        </div>
                        <div class="bg-emerald-100 rounded-lg shadow p-4 border border-emerald-300">
                            <div class="flex flex-col gap-1">
                                <span class="nunito-bold text-gray-700">Nombre: <span class="nunito-regular">Segundo pago</span></span>
                                <span class="nunito-bold text-gray-700">Fecha: <span class="nunito-regular">2025-07-25</span></span>
                                <span class="nunito-bold text-gray-700">Monto: <span class="nunito-regular">L. 14,230.00</span></span>
                                <span class="nunito-bold text-gray-700">Categoría: <span class="nunito-regular">Ingreso</span></span>
                                <span class="nunito-bold text-gray-700">Descripción: <span class="nunito-regular">Segundo pago del Proyecto Beta</span></span>
                                <span class="nunito-bold text-gray-700">Movimiento: <span class="nunito-regular">Ingreso</span></span>
                            </div>
                        </div>
                        <div class="bg-slate-200 rounded-lg shadow p-4 border border-slate-400">
                            <div class="flex flex-col gap-1">
                                <span class="nunito-bold text-gray-700">Nombre: <span class="nunito-regular">Alquiler de oficina</span></span>
                                <span class="nunito-bold text-gray-700">Fecha: <span class="nunito-regular">2025-07-26</span></span>
                                <span class="nunito-bold text-gray-700">Monto: <span class="nunito-regular">L. 10,483.00</span></span>
                                <span class="nunito-bold text-gray-700">Categoría: <span class="nunito-regular">Gasto</span></span>
                                <span class="nunito-bold text-gray-700">Descripción: <span class="nunito-regular">Pago de alquiler mensual</span></span>
                                <span class="nunito-bold text-gray-700">Movimiento: <span class="nunito-regular">Gasto</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="hidden xl:table min-w-full text-sm border-collapse border border-gray-600">
                    <thead class="bg-gray-100 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left border border-gray-600 nunito-bold">Nombre</th>
                            <th class="py-2 px-4 text-left border border-gray-600 nunito-bold">Fecha</th>
                            <th class="py-2 px-4 text-left border border-gray-600 nunito-bold">Monto</th>
                            <th class="py-2 px-4 text-left border border-gray-600 nunito-bold">Categoría</th>
                            <th class="py-2 px-4 text-left border border-gray-600 nunito-bold">Descripción</th>
                            <th class="py-2 px-4 text-left border border-gray-600 nunito-bold">Movimiento</th>
                        </tr>
                    </thead>
                    <tbody class="nunito-regular">
                        <tr class="bg-emerald-300">
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Pago inicial</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">2025-07-20</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">L. 15,000.00</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Ingreso</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Primer pago del Proyecto Alpha</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Ingreso</td>
                        </tr>
                        <tr class="bg-slate-400">
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Compra de software</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">2025-07-22</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">L. 5,500.00</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Gasto</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Licencias de software de desarrollo</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Gasto</td>
                        </tr>
                        <tr class="bg-emerald-300">
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Segundo pago</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">2025-07-25</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">L. 14,230.00</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Ingreso</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Segundo pago del Proyecto Beta</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Ingreso</td>
                        </tr>
                        <tr class="bg-slate-400">
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Alquiler de oficina</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">2025-07-26</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">L. 10,483.00</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Gasto</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Pago de alquiler mensual</td>
                            <td class="py-2 px-4 border border-gray-600 nunito-regular">Gasto</td>
                        </tr>
                    </tbody>
                </table>
            </table>
        </div>
    </div>
</div>
