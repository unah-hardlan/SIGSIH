<div x-data="bitacoraList" x-init="init()" @confirm-delete.window="if (isClearAllModalOpen) clearAllRecords()"
    class="w-full mx-auto py-8 px-4">
    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4" title="Bitácora">
        <x-slot name="filters">
            <div class="flex flex-col sm:flex-row flex-wrap items-stretch gap-3 w-full">
                <input type="text" x-model="filters.search" @keyup.enter="fetch()"
                    placeholder="Buscar (acción/descripción)"
                    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-64 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />
                <select x-model="filters.accion" @change="fetch()"
                    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-44 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                    <option value="">Acción</option>
                    <option value="Insertar">Insertar</option>
                    <option value="Actualizar">Actualizar</option>
                    <option value="Eliminar">Eliminar</option>
                </select>
                <input type="text" x-model="filters.usuario" @keyup.enter="fetch()" placeholder="Usuario/Nombre"
                    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-48 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />
                <input type="text" x-model="filters.objeto" @keyup.enter="fetch()" placeholder="Objeto"
                    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-44 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />
                <input type="date" x-model="filters.desde" @change="fetch()"
                    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-40 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />
                <input type="date" x-model="filters.hasta" @change="fetch()"
                    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-40 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />
                <select x-model="filters.sort" @change="fetch()"
                    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 md:w-64 sm:min-w-[14rem] md:min-w-[16rem] shrink-0 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                    <option value="fecha_evento">Ordenar por fecha</option>
                    <option value="usuario">Ordenar por usuario</option>
                    <option value="objeto">Ordenar por objeto</option>
                    <option value="accion">Ordenar por acción</option>
                    <option value="fecha_creacion">Ordenar por creación</option>
                </select>
                <select x-model="filters.direction" @change="fetch()"
                    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-28 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                    <option value="desc">Desc</option>
                    <option value="asc">Asc</option>
                </select>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button @click="resetFilters()"
                        class="px-4 py-2 border rounded nunito-regular bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 border-gray-300 dark:border-gray-600">Limpiar</button>
                    <button @click="fetch()"
                        class="px-4 py-2 bg-blue-600 text-white rounded nunito-regular hover:bg-blue-700">Buscar</button>
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <a :href="reportUrl()" target="_blank"
                    class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-file-alt"></i> Generar Reporte
                </a>
                <button @click="exportCsv()"
                    class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center gap-2 text-sm"
                    :disabled="loading">
                    <i class="fas fa-file-csv"></i> Exportar CSV
                </button>
                <button @click="isClearAllModalOpen = true"
                    class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center gap-2 text-sm"
                    :disabled="loading">
                    <i class="fas fa-trash-alt"></i> Limpiar Bitácora
                </button>
            </div>
        </x-slot>

        <x-slot name="table">
            <table
                class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse table-white-dividers">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left">Fecha Evento</th>
                        <th class="py-2 px-4 text-left">Usuario</th>
                        <th class="py-2 px-4 text-left">Objeto</th>
                        <th class="py-2 px-4 text-left">Acción</th>
                        <th class="py-2 px-4 text-left">Descripción</th>
                        <th class="py-2 px-4 text-left">Creado por</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular"><i
                                    class="fas fa-spinner fa-spin mr-2"></i> Cargando...</td>
                        </tr>
                    </template>
                    <template x-if="!loading && items.length===0">
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular">Sin resultados</td>
                        </tr>
                    </template>
                    <template x-for="b in items" :key="b.id">
                        <tr class="border-b dark:border-gray-700 nunito-regular">
                            <td class="py-2 px-4" x-text="b.fecha_evento_formatted || b.fecha_evento || '-' "></td>
                            <td class="py-2 px-4" x-text="b.usuario?.usuario || '-' "></td>
                            <td class="py-2 px-4" x-text="b.objeto?.nombre_objeto || '-' "></td>
                            <td class="py-2 px-4">
                                <span :class="{
                                    'px-2 py-1 rounded text-xs font-semibold': true,
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': b.accion === 'Insertar',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': b.accion === 'Actualizar',
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': b.accion === 'Eliminar'
                                }" x-text="b.accion"></span>
                            </td>
                            <td class="py-2 px-4 break-words" x-text="friendlyDescription(b)"></td>
                            <td class="py-2 px-4" x-text="b.creado_por || b.usuario?.usuario || '-' "></td>
                            <td class="py-2 px-4">
                                <button @click="openDetail(b)"
                                    class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700 flex items-center gap-1">
                                    <i class="fas fa-eye"></i> Ver detalle
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loading">
                <div class="p-8 text-center text-gray-500 nunito-regular"><i class="fas fa-spinner fa-spin mr-2"></i>
                    Cargando...</div>
            </template>
            <template x-if="!loading && items.length===0">
                <div class="p-8 text-center text-gray-500 nunito-regular">Sin resultados</div>
            </template>
            <template x-for="b in items" :key="'card-bit-'+b.id">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white" x-text="b.usuario?.usuario || '-' ">
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400"
                                x-text="b.fecha_evento_formatted || b.fecha_evento || '-' "></p>
                        </div>
                        <span class="px-2 py-1 rounded text-xs font-semibold" x-text="b.accion"></span>
                    </div>
                    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                        <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Objeto:</span> <span
                                x-text="b.objeto?.nombre_objeto || '-' "></span></div>
                        <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Descripción:</span> <span
                                x-text="friendlyDescription(b)"></span></div>
                        <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Creado por:</span> <span
                                x-text="b.creado_por || b.usuario?.usuario || '-' "></span></div>
                    </div>
                    <div class="pt-3 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                        <button @click="openDetail(b)"
                            class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700 flex items-center gap-1">
                            <i class="fas fa-eye"></i> Ver detalle
                        </button>
                    </div>
                </div>
            </template>
        </x-slot>
    </x-responsive-table>

    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50 dark:bg-gray-800 rounded-lg p-4"
        x-show="pagination.total>0">
        <div class="text-sm nunito-regular text-gray-700 dark:text-gray-300">
            <i class="fas fa-info-circle mr-2"></i>
            Página <span class="font-semibold" x-text="pagination.page"></span> de <span class="font-semibold"
                x-text="pagination.last_page"></span> •
            Total: <span class="font-semibold" x-text="pagination.total"></span> registros
        </div>
        <div class="flex gap-2">
            <button
                class="px-4 py-2 border rounded-lg nunito-regular bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 border-gray-300 dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                :disabled="pagination.page<=1" @click="changePage(pagination.page-1)">
                <i class="fas fa-chevron-left mr-2"></i>Anterior
            </button>
            <button
                class="px-4 py-2 border rounded-lg nunito-regular bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 border-gray-300 dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                :disabled="pagination.page>=pagination.last_page" @click="changePage(pagination.page+1)">
                Siguiente<i class="fas fa-chevron-right ml-2"></i>
            </button>
        </div>
    </div>

    <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg"
        x-show="error">
        <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="nunito-regular text-sm" x-text="error"></span>
        </div>
    </div>

    <x-admin.confirmation-modal modalName="isClearAllModalOpen" itemToDelete="itemToDelete" itemNameProperty="nombre"
        message="¿Estás seguro de que deseas eliminar" title="Limpiar Bitácora" />

    <x-admin.form-modal modalName="isDetailModalOpen" title="Detalle de Acción" submitLabel="" hideActions="true"
        maxWidth="max-w-4xl">
        <div class="space-y-4" x-show="selectedItem">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div><span class="nunito-bold">Acción:</span> <span x-text="selectedItem?.accion || '-' "></span></div>
                <div><span class="nunito-bold">Objeto:</span> <span
                        x-text="selectedItem?.objeto?.nombre_objeto || '-' "></span></div>
                <div><span class="nunito-bold">Usuario:</span> <span
                        x-text="selectedItem?.usuario?.usuario || '-' "></span></div>
                <div><span class="nunito-bold">Fecha:</span> <span
                        x-text="selectedItem?.fecha_evento_formatted || '-' "></span></div>
            </div>

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3 bg-gray-50 dark:bg-gray-900/40">
                <p class="nunito-bold text-sm mb-2">Descripción</p>
                <p class="text-sm" x-text="friendlyDescription(selectedItem)"></p>
            </div>

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3 bg-gray-50 dark:bg-gray-900/40"
                x-show="(selectedItem?.accion || '').toLowerCase() === 'actualizar'">
                <p class="nunito-bold text-sm mb-2">Detalle de cambios</p>
                <template x-if="buildDetailRows(selectedItem).length === 0">
                    <p class="text-sm text-gray-500">Sin detalles disponibles.</p>
                </template>
                <template x-if="buildDetailRows(selectedItem).length > 0">
                    <div class="space-y-2 text-sm">
                        <template x-for="row in buildDetailRows(selectedItem)"
                            :key="row.campo + '-' + row.antes + '-' + row.despues">
                            <div
                                class="grid grid-cols-1 md:grid-cols-3 gap-2 border-b border-gray-200 dark:border-gray-700 pb-2">
                                <div><span class="nunito-bold">Campo:</span> <span x-text="row.campo"></span></div>
                                <div><span class="nunito-bold">Antes:</span> <span x-text="row.antes"></span></div>
                                <div><span class="nunito-bold">Después:</span> <span x-text="row.despues"></span></div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div
                class="rounded-lg border border-gray-200 dark:border-gray-700 p-3 bg-gray-50 dark:bg-gray-900/40 text-xs">
                <p><span class="nunito-bold">IP:</span> <span x-text="selectedItem?.ip || '-' "></span></p>
                <p class="mt-1"><span class="nunito-bold">User Agent:</span> <span
                        x-text="selectedItem?.user_agent || '-' "></span></p>
            </div>
        </div>
    </x-admin.form-modal>
</div>

{{-- El componente x-data=bitacoraList se define en resources/js/bitacora.js y se importa en app.js --}}