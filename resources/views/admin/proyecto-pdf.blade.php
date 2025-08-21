<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reporte de Proyecto BAC</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
      @page { size: A4; margin: 12mm; }
      @media print {
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .page { box-shadow: none !important; }
        .avoid-break { page-break-inside: avoid; break-inside: avoid; }
      }
    </style>
  </head>
  <body class="bg-gray-100 text-gray-800 print:bg-white">
    <div class="page max-w-5xl mx-auto bg-white shadow-lg rounded-lg p-6 md:p-10 my-6 print:shadow-none print:rounded-none print:p-0">
      <!-- Header del reporte -->
      <x-admin.reportes-header :titulo="'Proyecto BAC'" :fecha="now()->format('d/m/Y')" :modulo="'Proyectos'" />

      <!-- Resumen -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-8 avoid-break">
        <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-5">
          <div class="text-sm text-blue-700 font-semibold">Ingresos Totales</div>
          <div class="mt-2 text-2xl md:text-3xl font-bold text-blue-900">L. 29,230.00</div>
        </div>
        <div class="rounded-xl border border-rose-100 bg-gradient-to-br from-rose-50 to-white p-5">
          <div class="text-sm text-rose-700 font-semibold">Gastos Totales</div>
          <div class="mt-2 text-2xl md:text-3xl font-bold text-rose-900">L. 15,983.00</div>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-5">
          <div class="text-sm text-emerald-700 font-semibold">Balance</div>
          <div class="mt-2 text-2xl md:text-3xl font-bold text-emerald-900">L. 13,247.00</div>
        </div>
      </section>

      <!-- Ingresos -->
      <section class="mb-10 avoid-break">
        <h2 class="text-xl md:text-2xl font-bold text-blue-900 border-b-4 border-blue-900/80 pb-1 mb-4">Ingresos</h2>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr class="text-left text-gray-700">
                <th class="px-4 py-3 font-semibold">Nombre</th>
                <th class="px-4 py-3 font-semibold">Fecha</th>
                <th class="px-4 py-3 font-semibold">Monto</th>
                <th class="px-4 py-3 font-semibold">Categoría</th>
                <th class="px-4 py-3 font-semibold">Descripción</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr class="bg-emerald-50/50">
                <td class="px-4 py-3">Pago inicial</td>
                <td class="px-4 py-3">2025-07-20</td>
                <td class="px-4 py-3 font-semibold text-emerald-700">L. 15,000.00</td>
                <td class="px-4 py-3">Ingreso</td>
                <td class="px-4 py-3">Primer pago del Proyecto Alpha</td>
              </tr>
              <tr class="bg-emerald-50/50">
                <td class="px-4 py-3">Segundo pago</td>
                <td class="px-4 py-3">2025-07-25</td>
                <td class="px-4 py-3 font-semibold text-emerald-700">L. 14,230.00</td>
                <td class="px-4 py-3">Ingreso</td>
                <td class="px-4 py-3">Segundo pago del Proyecto Beta</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Gastos -->
      <section class="mb-10 avoid-break">
        <h2 class="text-xl md:text-2xl font-bold text-blue-900 border-b-4 border-blue-900/80 pb-1 mb-4">Gastos</h2>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr class="text-left text-gray-700">
                <th class="px-4 py-3 font-semibold">Nombre</th>
                <th class="px-4 py-3 font-semibold">Fecha</th>
                <th class="px-4 py-3 font-semibold">Monto</th>
                <th class="px-4 py-3 font-semibold">Categoría</th>
                <th class="px-4 py-3 font-semibold">Descripción</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr class="bg-rose-50/50">
                <td class="px-4 py-3">Compra de software</td>
                <td class="px-4 py-3">2025-07-22</td>
                <td class="px-4 py-3 font-semibold text-rose-700">L. 5,500.00</td>
                <td class="px-4 py-3">Gasto</td>
                <td class="px-4 py-3">Licencias de software de desarrollo</td>
              </tr>
              <tr class="bg-rose-50/50">
                <td class="px-4 py-3">Alquiler de oficina</td>
                <td class="px-4 py-3">2025-07-26</td>
                <td class="px-4 py-3 font-semibold text-rose-700">L. 10,483.00</td>
                <td class="px-4 py-3">Gasto</td>
                <td class="px-4 py-3">Pago de alquiler mensual</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Gráficas -->
      <section class="grid grid-cols-1 md:grid-cols-2 gap-6 avoid-break mb-6">
        <div class="rounded-lg border border-gray-200 p-4">
          <div class="text-sm text-gray-700 font-semibold mb-2">Gráfica de Ingresos</div>
          <canvas id="incomeChart" height="200"></canvas>
        </div>
        <div class="rounded-lg border border-gray-200 p-4">
          <div class="text-sm text-gray-700 font-semibold mb-2">Gráfica de Gastos</div>
          <canvas id="expenseChart" height="200"></canvas>
        </div>
      </section>

      <!-- Botón imprimir -->
      <div class="print:hidden flex justify-end">
        <button onclick="window.print()" class="mt-2 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md shadow">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M6 7V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v3h1a3 3 0 0 1 3 3v4a1 1 0 0 1-1 1h-2v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-4H3a1 1 0 0 1-1-1v-4a3 3 0 0 1 3-3h1Zm2 0h8V5H8v2Zm8 10v-3H8v3h8Zm4-7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v3h2v-1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1h2v-3Z"/></svg>
          Imprimir
        </button>
      </div>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // Datos para la gráfica de ingresos
        const incomeData = {
          labels: ["Pago inicial", "Segundo pago"],
          datasets: [
            {
              label: "Ingresos",
              data: [15000, 14230],
              backgroundColor: ["rgba(16, 185, 129, 0.2)", "rgba(59, 130, 246, 0.2)"],
              borderColor: ["rgba(16, 185, 129, 1)", "rgba(59, 130, 246, 1)"],
              borderWidth: 1,
            },
          ],
        };

        // Datos para la gráfica de gastos
        const expenseData = {
          labels: ["Compra de software", "Alquiler de oficina"],
          datasets: [
            {
              label: "Gastos",
              data: [5500, 10483],
              backgroundColor: ["rgba(239, 68, 68, 0.2)", "rgba(245, 158, 11, 0.2)"],
              borderColor: ["rgba(239, 68, 68, 1)", "rgba(245, 158, 11, 1)"],
              borderWidth: 1,
            },
          ],
        };

        // Opciones comunes para las gráficas
        const options = {
          scales: { y: { beginAtZero: true } },
          plugins: { legend: { display: false } },
        };

        // Crear gráfica de ingresos
        const incomeCtx = document.getElementById("incomeChart").getContext("2d");
        new Chart(incomeCtx, { type: "bar", data: incomeData, options });

        // Crear gráfica de gastos
        const expenseCtx = document.getElementById("expenseChart").getContext("2d");
        new Chart(expenseCtx, { type: "bar", data: expenseData, options });
      });
    </script>
  </body>
</html>
