{{-- resources/views/admin/partials/bitacora.blade.php --}}

<div x-data="bitacoraList" x-init="init()" class="max-w-6xl mx-auto py-8">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between gap-2 mb-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white nunito-bold">Bitácora</h2>
            <a :href="reportUrl()" target="_blank"
               class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center gap-2 text-sm">
                <i class="fas fa-file-alt"></i> Generar Reporte
            </a>
        </div>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <div class="flex-1 flex gap-2">
                <input type="text" placeholder="Buscar (acción/descripcion)" x-model="filters.search" @keyup.enter="fetch()" class="border rounded px-3 py-2 w-full nunito-regular bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700" />
            </div>
            <div class="flex gap-2 flex-wrap">
                <select x-model="filters.accion" @change="fetch()" class="border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700">
                    <option class="nunito-regular" value="">Acción</option>
                    <option class="nunito-regular" value="Login">Login</option>
                    <option class="nunito-regular" value="Logout">Logout</option>
                    <option class="nunito-regular" value="Insertar">Insertar</option>
                    <option class="nunito-regular" value="Actualizar">Actualizar</option>
                    <option class="nunito-regular" value="Eliminar">Eliminar</option>
                    <option class="nunito-regular" value="Consulta">Consulta</option>
                </select>
                <select x-model="filters.sort" @change="fetch()" class="border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700">
                    <option value="fecha_evento">Ordenar por fecha</option>
                    <option value="usuario">Ordenar por usuario</option>
                    <option value="objeto">Ordenar por objeto</option>
                    <option value="accion">Ordenar por acción</option>
                    <option value="fecha_creacion">Ordenar por creación</option>
                </select>
                <select x-model="filters.direction" @change="fetch()" class="border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700">
                    <option value="desc">Desc</option>
                    <option value="asc">Asc</option>
                </select>
                <input type="text" placeholder="Usuario/Nombre" x-model="filters.usuario" @keyup.enter="fetch()" class="border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700" />
                <input type="text" placeholder="Objeto" x-model="filters.objeto" @keyup.enter="fetch()" class="border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700" />
                <input type="date" x-model="filters.desde" @change="fetch()" class="border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700" />
                <input type="date" x-model="filters.hasta" @change="fetch()" class="border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700" />
                <button @click="resetFilters()" class="px-3 py-2 border rounded nunito-regular bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 border-gray-300 dark:border-gray-700">Limpiar</button>
                <button @click="fetch()" class="px-3 py-2 bg-blue-600 text-white rounded nunito-regular hover:bg-blue-700">Buscar</button>
            </div>
        </div>
        <div class="overflow-x-auto mt-5">
            <table class="min-w-full text-xs md:text-sm whitespace-nowrap text-gray-800 dark:text-gray-200">
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
                    <template x-if="loading">
                        <tr><td colspan="8" class="py-3 px-4 text-center nunito-regular text-gray-700 dark:text-gray-300">Cargando...</td></tr>
                    </template>
                    <template x-if="!loading && items.length===0">
                        <tr><td colspan="8" class="py-3 px-4 text-center text-gray-500 dark:text-gray-400 nunito-regular">Sin resultados</td></tr>
                    </template>
                    <template x-for="b in items" :key="b.id">
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                            <td class="py-2 px-4 nunito-regular" x-text="b.id"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="b.fecha_evento_formatted || b.fecha_evento"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="b.usuario?.usuario || '-' "></td>
                            <td class="py-2 px-4 nunito-regular" x-text="b.objeto?.nombre_objeto || '-' "></td>
                            <td class="py-2 px-4 nunito-regular" x-text="b.accion"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="b.descripcion || '-' "></td>
                            <td class="py-2 px-4 nunito-regular" x-text="b.creado_por || b.usuario?.usuario || '-' "></td>
                            <td class="py-2 px-4 nunito-regular" x-text="b.fecha_creacion_formatted || b.fecha_creacion || '-' "></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="mt-3 flex items-center justify-between text-gray-800 dark:text-gray-200" x-show="pagination.total>0">
            <div class="text-xs nunito-regular">Página <span x-text="pagination.page"></span>/<span x-text="pagination.last_page"></span> • Total <span x-text="pagination.total"></span></div>
            <div class="flex gap-2">
                <button class="px-2 py-1 border rounded nunito-regular bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 border-gray-300 dark:border-gray-700" :disabled="pagination.page<=1" @click="changePage(pagination.page-1)">Anterior</button>
                <button class="px-2 py-1 border rounded nunito-regular bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 border-gray-300 dark:border-gray-700" :disabled="pagination.page>=pagination.last_page" @click="changePage(pagination.page+1)">Siguiente</button>
            </div>
        </div>
        <div class="mt-2 text-red-600 dark:text-red-400 text-sm nunito-regular" x-show="error" x-text="error"></div>
    </div>
</div>

{{-- El componente x-data=bitacoraList se define en resources/js/bitacora.js y se importa en app.js --}}