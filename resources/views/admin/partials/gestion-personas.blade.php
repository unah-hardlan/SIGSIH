<div x-data="{
        isModalOpenPersonas: false,
        isEditModalOpenPersonas: false,
        isDeleteModalOpenPersonas: false,
        itemToEdit: null,
        itemToDelete: null,
        searchPersonas: '',
        filtroTipoPersona: '',
        filtroGenero: '',
        ordenarPor: '',
        personas: [
            {
                id: '001',
                primer_nombre: 'Juan',
                segundo_nombre: 'Carlos',
                primer_apellido: 'Pérez',
                segundo_apellido: 'Gómez',
                dni: '12345678',
                cargo: 'Analista',
                tipo_persona: 'Administrador',
                genero: 'Masculino',
                perfil: 'Administrador',
                usuario: 'jgomez'
            }
        ]
    }">
    <x-admin.tabla-crud class="nunito-bold" :titulo="'Gestión de Personas'">
        <x-slot name="filtros">
            <div class="w-full">
                <!-- On mobile: stack vertically; on sm+ keep a single row with filters then buttons at the end -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full">
                    <div class="flex-1">
                        <div class="grid grid-cols-1 sm:flex sm:flex-wrap sm:items-center gap-2 w-full">
                            <!-- Búsqueda -->
                            <input type="text" x-model="searchPersonas" placeholder="Buscar..."
                                class="border rounded px-3 py-2 text-sm w-full sm:w-48 nunito-regular" />
                            
                            <!-- Filtros -->
                            <select x-model="filtroTipoPersona" class="border rounded px-3 py-2 text-sm w-full sm:w-40 nunito-regular">
                                <option class="nunito-regular" value="">Todos los tipo de persona</option>
                                <option class="nunito-regular">Empleado</option>
                                <option class="nunito-regular">Cliente</option>
                                <option class="nunito-regular">Administrador</option>
                            </select>
                            
                            <select x-model="filtroGenero" class="border rounded px-3 py-2 text-sm w-full sm:w-40 nunito-regular">
                                <option class="nunito-regular" value="">Todos los género</option>
                                <option class="nunito-regular">Masculino</option>
                                <option class="nunito-regular">Femenino</option>
                            </select>
                            
                            <select x-model="ordenarPor" class="border rounded px-3 py-2 text-sm w-full sm:w-40 nunito-regular">
                                <option class="nunito-regular" value="">Ordenar por Primer Nombre</option>
                                <option class="nunito-regular" value="primer_apellido">Ordenar por Primer Apellido</option>
                                <option class="nunito-regular" value="cargo">Ordenar por Cargo</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 items-center mt-2 sm:mt-0 sm:ml-auto">
                        <button @click="isModalOpenPersonas = true"
                            class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center">
                            Agregar persona
                        </button>
                        <a href="/admin/reportes-header?modulo=Gestion de Personas&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                        class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 justify-center">
                            <i class="fas fa-file-alt"></i> Generar Reporte
                        </a>
                    </div>
                </div>
            </div>
        </x-slot>

        <!-- Tabla de personas -->
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 nunito-bold">
                        <th class="py-2 px-4 text-left nunito-bold">ID</th>
                        <th class="py-2 px-4 text-left nunito-bold">Primer Nombre</th>
                        <th class="py-2 px-4 text-left nunito-bold">Segundo Nombre</th>
                        <th class="py-2 px-4 text-left nunito-bold">Primer Apellido</th>
                        <th class="py-2 px-4 text-left nunito-bold">Segundo Apellido</th>
                        <th class="py-2 px-4 text-left nunito-bold">DNI</th>
                        <th class="py-2 px-4 text-left nunito-bold">Cargo</th>
                        <th class="py-2 px-4 text-left nunito-bold">Tipo</th>
                        <th class="py-2 px-4 text-left nunito-bold">Género</th>
                        <th class="py-2 px-4 text-left nunito-bold">Perfil</th>
                        <th class="py-2 px-4 text-left nunito-bold">Usuario</th>
                        <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="persona in personas
                        .filter(p => 
                            (!searchPersonas || 
                                p.primer_nombre.toLowerCase().includes(searchPersonas.toLowerCase()) ||
                                p.primer_apellido.toLowerCase().includes(searchPersonas.toLowerCase())
                            ) &&
                            (!filtroTipoPersona || p.tipo_persona === filtroTipoPersona) &&
                            (!filtroGenero || p.genero === filtroGenero)
                        )
                        .sort((a, b) => {
                            if(ordenarPor === 'primer_apellido') return a.primer_apellido.localeCompare(b.primer_apellido);
                            if(ordenarPor === 'cargo') return a.cargo.localeCompare(b.cargo);
                            return a.primer_nombre.localeCompare(b.primer_nombre);
                        })" :key="persona.id">
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4 nunito-regular" x-text="persona.id"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="persona.primer_nombre"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="persona.segundo_nombre"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="persona.primer_apellido"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="persona.segundo_apellido"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="persona.dni"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="persona.cargo"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="persona.tipo_persona"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="persona.genero"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="persona.perfil"></td>
                            <td class="py-2 px-4 nunito-regular" x-text="persona.usuario"></td>
                            <td class="py-2 px-4 flex gap-2 nunito-regular">
                                <a href="#" @click="isEditModalOpenPersonas = true; itemToEdit = {...persona}"
                                    class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" @click="isDeleteModalOpenPersonas = true; itemToDelete = {...persona}"
                                    class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>

    <!-- Modal Agregar Persona -->
    <x-admin.form-modal class="nunito-bold" modalName="isModalOpenPersonas" title="Agregar Persona" submitLabel="Guardar"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Primer Nombre</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Juan" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Segundo Nombre</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Carlos" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Primer Apellido</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Pérez" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Segundo Apellido</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Gómez" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">DNI</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: 12345678" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Cargo</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Analista" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Tipo de Persona</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Empleado o Cliente" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Género</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Masculino o Femenino" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Perfil</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Administrador" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Usuario</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: jgomez" />
            </div>
        </div>
    </x-admin.form-modal>
    <!-- Modal Editar Persona -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpenPersonas" title="Editar Persona" itemToEdit="itemToEdit"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Primer Nombre</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.primer_nombre" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Segundo Nombre</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.segundo_nombre" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Primer Apellido</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.primer_apellido" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Segundo Apellido</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.segundo_apellido" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">DNI</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.dni" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Cargo</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.cargo" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Tipo de Persona</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.tipo_persona" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Género</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.genero" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Perfil</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.perfil" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Usuario</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.usuario" />
            </div>
        </div>
    </x-admin.edit-modal>
    <!-- Modal Eliminar Persona -->
    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteModalOpenPersonas" itemToDelete="itemToDelete"
        message="¿Estás seguro de que deseas eliminar esta persona?" />
</div>