{{-- resources/views/admin/partials/bitacora.blade.php --}}

<div x-data="bitacoraList" x-init="init()" class="w-full mx-auto py-8 px-4">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between gap-2 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white nunito-bold">Bitácora</h2>
            <a :href="reportUrl()" target="_blank"
               class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center gap-2 text-sm">
                <i class="fas fa-file-alt"></i> Generar Reporte
            </a>
        </div>
        
        <!-- Filters Section -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-4">
                <div class="col-span-1 md:col-span-2 lg:col-span-2 xl:col-span-2">
                    <input type="text" placeholder="Buscar (acción/descripción)" x-model="filters.search" @keyup.enter="fetch()" class="w-full border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600" />
                </div>
                <div>
                    <select x-model="filters.accion" @change="fetch()" class="w-full border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600">
                        <option class="nunito-regular" value="">Acción</option>
                        <option class="nunito-regular" value="Login">Login</option>
                        <option class="nunito-regular" value="Logout">Logout</option>
                        <option class="nunito-regular" value="Insertar">Insertar</option>
                        <option class="nunito-regular" value="Actualizar">Actualizar</option>
                        <option class="nunito-regular" value="Eliminar">Eliminar</option>
                        <option class="nunito-regular" value="Consulta">Consulta</option>
                    </select>
                </div>
                <div>
                    <input type="text" placeholder="Usuario/Nombre" x-model="filters.usuario" @keyup.enter="fetch()" class="w-full border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600" />
                </div>
                <div>
                    <input type="text" placeholder="Objeto" x-model="filters.objeto" @keyup.enter="fetch()" class="w-full border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600" />
                </div>
                <div>
                    <input type="date" x-model="filters.desde" @change="fetch()" class="w-full border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600" />
                </div>
                <div>
                    <input type="date" x-model="filters.hasta" @change="fetch()" class="w-full border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600" />
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div class="flex gap-2 flex-wrap">
                    <select x-model="filters.sort" @change="fetch()" class="border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600">
                        <option value="fecha_evento">Ordenar por fecha</option>
                        <option value="usuario">Ordenar por usuario</option>
                        <option value="objeto">Ordenar por objeto</option>
                        <option value="accion">Ordenar por acción</option>
                        <option value="fecha_creacion">Ordenar por creación</option>
                    </select>
                    <select x-model="filters.direction" @change="fetch()" class="border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600">
                        <option value="desc">Desc</option>
                        <option value="asc">Asc</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button @click="resetFilters()" class="px-4 py-2 border rounded nunito-regular bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 border-gray-300 dark:border-gray-600">Limpiar</button>
                    <button @click="fetch()" class="px-4 py-2 bg-blue-600 text-white rounded nunito-regular hover:bg-blue-700">Buscar</button>
                </div>
            </div>
        </div>
        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr class="border-0">
                        <th class="py-3 px-4 text-left nunito-bold dark:text-gray-300 w-16 first:rounded-tl-lg border-0">ID</th>
                        <th class="py-3 px-4 text-left nunito-bold dark:text-gray-300 w-32 border-0">Fecha Evento</th>
                        <th class="py-3 px-4 text-left nunito-bold dark:text-gray-300 w-32 border-0">Usuario</th>
                        <th class="py-3 px-4 text-left nunito-bold dark:text-gray-300 w-32 border-0">Objeto</th>
                        <th class="py-3 px-4 text-left nunito-bold dark:text-gray-300 w-24 border-0">Acción</th>
                        <th class="py-3 px-4 text-left nunito-bold dark:text-gray-300 min-w-48 border-0">Descripción</th>
                        <th class="py-3 px-4 text-left nunito-bold dark:text-gray-300 w-32 border-0">Creado por</th>
                        <th class="py-3 px-4 text-left nunito-bold dark:text-gray-300 w-32 last:rounded-tr-lg border-0">Fecha Creación</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <tr>
                            <td colspan="8" class="py-8 px-4 text-center nunito-regular text-gray-700 dark:text-gray-300 border-t-0">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    Cargando...
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loading && items.length===0">
                        <tr>
                            <td colspan="8" class="py-8 px-4 text-center text-gray-500 dark:text-gray-400 nunito-regular border-t-0">
                                <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                Sin resultados
                            </td>
                        </tr>
                    </template>
                    <template x-for="b in items" :key="b.id">
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 ease-in-out last:border-b-0">
                            <td class="py-3 px-4 nunito-regular font-medium first:rounded-bl-lg border-t-0" x-text="b.id"></td>
                            <td class="py-3 px-4 nunito-regular border-t-0" x-text="b.fecha_evento_formatted || b.fecha_evento"></td>
                            <td class="py-3 px-4 nunito-regular border-t-0" x-text="b.usuario?.usuario || '-' "></td>
                            <td class="py-3 px-4 nunito-regular border-t-0" x-text="b.objeto?.nombre_objeto || '-' "></td>
                            <td class="py-3 px-4 nunito-regular border-t-0">
                                <span :class="{
                                    'px-2 py-1 rounded-full text-xs font-medium duration-200 ease-in-out': true,
                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': b.accion === 'Login',
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': b.accion === 'Logout',
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': b.accion === 'Insertar',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': b.accion === 'Actualizar',
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': b.accion === 'Eliminar',
                                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200': b.accion === 'Consulta'
                                }" x-text="b.accion"></span>
                            </td>
                            <td class="py-3 px-4 nunito-regular break-words border-t-0" x-text="b.descripcion || '-' "></td>
                            <td class="py-3 px-4 nunito-regular border-t-0" x-text="b.creado_por || b.usuario?.usuario || '-' "></td>
                            <td class="py-3 px-4 nunito-regular last:rounded-br-lg border-t-0" x-text="b.fecha_creacion_formatted || b.fecha_creacion || '-' "></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <!-- Pagination Section -->
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50 dark:bg-gray-800 rounded-lg p-4" x-show="pagination.total>0">
            <div class="text-sm nunito-regular text-gray-700 dark:text-gray-300">
                <i class="fas fa-info-circle mr-2"></i>
                Página <span class="font-semibold" x-text="pagination.page"></span> de <span class="font-semibold" x-text="pagination.last_page"></span> • 
                Total: <span class="font-semibold" x-text="pagination.total"></span> registros
            </div>
            <div class="flex gap-2">
                <button class="px-4 py-2 border rounded-lg nunito-regular bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 border-gray-300 dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors" 
                        :disabled="pagination.page<=1" 
                        @click="changePage(pagination.page-1)">
                    <i class="fas fa-chevron-left mr-2"></i>Anterior
                </button>
                <button class="px-4 py-2 border rounded-lg nunito-regular bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 border-gray-300 dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors" 
                        :disabled="pagination.page>=pagination.last_page" 
                        @click="changePage(pagination.page+1)">
                    Siguiente<i class="fas fa-chevron-right ml-2"></i>
                </button>
            </div>
        </div>
        
        <!-- Error Message -->
        <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg" x-show="error">
            <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                <i class="fas fa-exclamation-triangle"></i>
                <span class="nunito-regular text-sm" x-text="error"></span>
            </div>
        </div>
    </div>
</div>

{{-- El componente x-data=bitacoraList se define en resources/js/bitacora.js y se importa en app.js --}}