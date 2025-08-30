<div x-data="{
        isModalOpen: false,
        isEditModalOpen: false,
        reporteToEdit: null,
        isDeleteModalOpen: false,
        reporteToDelete: null,
        reportes: [
            {
                id_reporte: 1,
                fecha_reporte: '2025-07-28',
                observaciones: 'Sin novedades',
                tipo_visita: 'Visita Técnica',
                servicio_realizado: 'Mantenimiento preventivo',
                accion_realizada: 'Revisión de equipos',
                orden_servicio: 'OS-00123'
            }
        ],
        filtroReporte: '',
        filtroEstado: ''
    }" class="overflow-x-auto">
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <div
            class="sticky top-0 z-10 bg-white pb-4 mb-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-2xl text-gray-800 nunito-bold">Reportes</h2>
            <div class="flex flex-col sm:flex-row gap-2 flex-1 md:ml-6 nunito-bold">
                <input type="text" x-model="filtroReporte" placeholder="Buscar reporte..."
                    class="border rounded px-3 py-2 text-sm w-full sm:w-48 nunito-regular" />
                <select x-model="filtroEstado" class="border rounded px-1 py-2 text-sm w-full sm:w-40 nunito-regular">
                    <option value="" class="nunito-regular">Todos los estados</option>
                    <option class="nunito-regular">Generado</option>
                    <option class="nunito-regular">Pendiente</option>
                    <option class="nunito-regular">Archivado</option>
                </select>
            </div>
            <button @click="isModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo
                reporte</button>
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left nunito-bold">ID Reporte</th>
                    <th class="py-2 px-4 text-left nunito-bold">Fecha de Reporte</th>
                    <th class="py-2 px-4 text-left nunito-bold">Observaciones</th>
                    <th class="py-2 px-4 text-left nunito-bold">Tipo de Visita</th>
                    <th class="py-2 px-4 text-left nunito-bold">Servicio Realizado</th>
                    <th class="py-2 px-4 text-left nunito-bold">Acción Realizada</th>
                    <th class="py-2 px-4 text-left nunito-bold">Orden de Servicio</th>
                    <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="reporte in reportes
                    .filter(r =>
                        (!filtroReporte || r.observaciones.toLowerCase().includes(filtroReporte.toLowerCase()))
                        && (!filtroEstado || filtroEstado === 'Generado') // Solo a modo ejemplo
                    )" :key="reporte.id_reporte">
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4 nunito-regular" x-text="reporte.id_reporte"></td>
                        <td class="py-2 px-4 nunito-regular" x-text="reporte.fecha_reporte"></td>
                        <td class="py-2 px-4 nunito-regular" x-text="reporte.observaciones"></td>
                        <td class="py-2 px-4 nunito-regular" x-text="reporte.tipo_visita"></td>
                        <td class="py-2 px-4 nunito-regular" x-text="reporte.servicio_realizado"></td>
                        <td class="py-2 px-4 nunito-regular" x-text="reporte.accion_realizada"></td>
                        <td class="py-2 px-4 nunito-regular" x-text="reporte.orden_servicio"></td>
                        <td class="py-2 px-4 flex gap-2">
                            <a :href="`{{ route('admin.formato-reporte') }}?id_reporte=${reporte.id_reporte}&fecha_reporte=${reporte.fecha_reporte}&observaciones=${reporte.observaciones}&tipo_visita=${reporte.tipo_visita}&servicio_realizado=${reporte.servicio_realizado}&accion_realizada=${reporte.accion_realizada}&orden_servicio=${reporte.orden_servicio}`"
                                target="_blank"
                                class="inline-flex items-center justify-center text-xs w-24 h-9 rounded bg-emerald-500 text-white hover:bg-emerald-600 duration-300 mr-2 nunito-regular">
                                <i class="fas fa-eye mr-1"></i> Ver detalles
                            </a>
                            <a href="#" @click.prevent="isEditModalOpen = true; reporteToEdit = reporte"
                                class="text-blue-500 hover:text-blue-700 nunito-regular"><i class="fas fa-edit"></i></a>
                            <a href="#" @click.prevent="isDeleteModalOpen = true; reporteToDelete = reporte"
                                class="text-red-500 hover:text-red-700 nunito-regular"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Modal Nuevo Reporte -->
    <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Nuevo Reporte" submitLabel="Guardar Reporte"
        maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl" minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="id_reporte" class="block text-sm font-medium text-gray-700 nunito-bold">ID Reporte</label>
                <input type="text" id="id_reporte" name="id_reporte"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_reporte" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de Reporte</label>
                <input type="date" id="fecha_reporte" name="fecha_reporte"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="observaciones" class="block text-sm font-medium text-gray-700 nunito-bold">Observaciones</label>
                <textarea id="observaciones" name="observaciones" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
            <div>
                <label for="tipo_visita" class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de Visita</label>
                <input type="text" id="tipo_visita" name="tipo_visita"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="servicio_realizado" class="block text-sm font-medium text-gray-700 nunito-bold">Servicio
                    Realizado</label>
                <input type="text" id="servicio_realizado" name="servicio_realizado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="accion_realizada" class="block text-sm font-medium text-gray-700 nunito-bold">Acción Realizada</label>
                <input type="text" id="accion_realizada" name="accion_realizada"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="orden_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Orden de Servicio</label>
                <input type="text" id="orden_servicio" name="orden_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Reporte -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Reporte" itemToEdit="reporteToEdit"
        maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl" minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="edit_id_reporte" class="block text-sm font-medium text-gray-700 nunito-bold">ID Reporte</label>
                <input type="text" id="edit_id_reporte" name="edit_id_reporte" :value="reporteToEdit?.id_reporte"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_reporte" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de Reporte</label>
                <input type="date" id="edit_fecha_reporte" name="edit_fecha_reporte"
                    :value="reporteToEdit?.fecha_reporte"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="edit_observaciones" class="block text-sm font-medium text-gray-700 nunito-bold">Observaciones</label>
                <textarea id="edit_observaciones" name="edit_observaciones" rows="2"
                    :value="reporteToEdit?.observaciones"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
            <div>
                <label for="edit_tipo_visita" class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de Visita</label>
                <input type="text" id="edit_tipo_visita" name="edit_tipo_visita" :value="reporteToEdit?.tipo_visita"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_servicio_realizado" class="block text-sm font-medium text-gray-700 nunito-bold">Servicio
                    Realizado</label>
                <input type="text" id="edit_servicio_realizado" name="edit_servicio_realizado"
                    :value="reporteToEdit?.servicio_realizado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_accion_realizada" class="block text-sm font-medium text-gray-700 nunito-bold">Acción
                    Realizada</label>
                <input type="text" id="edit_accion_realizada" name="edit_accion_realizada"
                    :value="reporteToEdit?.accion_realizada"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_orden_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Orden de
                    Servicio</label>
                <input type="text" id="edit_orden_servicio" name="edit_orden_servicio"
                    :value="reporteToEdit?.orden_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación -->
    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteModalOpen" itemToDelete="reporteToDelete"
        message="¿Estás seguro de que quieres eliminar el reporte?" />
</div>