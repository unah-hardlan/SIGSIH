<div x-data="{
        // UI state
        isModalOpenPersonas: false,
        isEditModalOpenPersonas: false,
        isDeleteModalOpenPersonas: false,
    itemToEdit: {},
        itemToDelete: null,
        // filters
        searchPersonas: '',
        filtroTipoPersona: '', // mantiene etiqueta visual; filtro adicional se aplica en cliente
        filtroGenero: '',
    // orden fijo por nombre asc
    ordenarPor: 'nombre',
    ordenarDir: 'asc',
    showMoreFilters: false,
    // data
        personas: [],
        loading: false,
        error: '',
        // pagination
        page: 1,
        perPage: 10,
        total: 0,
        lastPage: 1,
    // internals to prevent storm of requests
    _suppressWatch: false,
    _abortCtrl: null,
        // forms
        addForm: {
            primer_nombre: '', segundo_nombre: '', primer_apellido: '', segundo_apellido: '',
            dni: '', cargo: ''
        },

        init(){
            // cargar al abrir y observar filtros con debounce
            this.loadPersonas();
            this.$watch('searchPersonas', Alpine.debounce(() => { if(this._suppressWatch) return; this.page = 1; this.loadPersonas(); }, 350));
            // Orden fijo, no watchers para orden
            // No observar perPage automáticamente para evitar loops; se recarga solo desde UI explícita
        },

        apiHeaders(){
            const token = localStorage.getItem('authToken');
            return token ? { 'Authorization': `Bearer ${token}` } : {};
        },

    // helpers de comparación (ignoran mayúsculas/minúsculas y acentos)
    normalizeStr(v){
        try{ return (v ?? '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim(); }catch(_){
            // fallback si normalize no está disponible
            return (v ?? '').toString().toLowerCase().trim();
        }
    },
    equalsNormalized(a,b){
        if(!b) return true; // si filtro vacío, no filtra
        return this.normalizeStr(a) === this.normalizeStr(b);
    },

    async loadPersonas(){
            try{
        this.loading = true; this.error = '';
        // cancel previous request if any
        try { this._abortCtrl?.abort(); } catch(_) {}
        this._abortCtrl = new AbortController();
        const params = new URLSearchParams();
        if (this.searchPersonas) params.set('q', this.searchPersonas);
                params.set('sort', 'nombre');
                params.set('direction', 'asc');
                // traer más filas porque no hay paginación visual
                const perPage = Number(this.perPage) || 100;
                const page = 1;
        params.set('per_page', String(perPage));
        params.set('page', String(page));
        const res = await fetch(`/api/personas?${params.toString()}`, { headers: this.apiHeaders(), signal: this._abortCtrl.signal });
                if(!res.ok){
                    const err = await res.json().catch(()=>({message:'Error cargando personas'}));
                    throw new Error(err.message || 'Error cargando personas');
                }
                const data = await res.json();
                // data is a Laravel resource collection with data[] and meta
                const items = Array.isArray(data?.data) ? data.data : [];
                this.personas = items.map(p => ({
                    ...p,
                    // aplanar campos para la vista
                    tipo_persona: p.tipo_persona?.nombre || '',
                    genero: p.genero?.genero || '',
                    perfil: p.perfil?.nombre || '',
                    usuario: p.id_usuario_fk || ''
                }));
        const meta = data?.meta || {};
        this._suppressWatch = true;
        const nextPage = meta.page || 1;
        const nextPer = meta.per_page || perPage;
        if (this.page !== nextPage) this.page = nextPage;
        if (this.perPage !== nextPer) this.perPage = nextPer;
        this.total = meta.total ?? this.personas.length;
        this.lastPage = meta.last_page || 1;
        this._suppressWatch = false;
            } catch(e){ this.error = e.message || 'Error'; }
            finally { this.loading = false; }
        },

        // CRUD
        openAdd(){
            this.addForm = { primer_nombre: '', segundo_nombre: '', primer_apellido: '', segundo_apellido: '', dni: '', cargo: '' };
            this.isModalOpenPersonas = true;
        },
        async createPersona(){
            try{
                const res = await fetch('/api/personas', {
                    method: 'POST', headers: { 'Content-Type': 'application/json', ...this.apiHeaders() },
                    body: JSON.stringify(this.addForm)
                });
                if(!res.ok){ const err = await res.json().catch(()=>({message:'Error al crear persona'})); throw new Error(err.message); }
                this.isModalOpenPersonas = false; this.loadPersonas();
            } catch(e){ alert(e.message || 'Error al crear persona'); }
        },
        openEdit(persona){ this.itemToEdit = JSON.parse(JSON.stringify(persona)); this.isEditModalOpenPersonas = true; },
        async updatePersona(){
            try{
                const id = this.itemToEdit?.id; if(!id) return;
                // enviar solo campos editables simples
                const payload = (({primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,dni,cargo}) => ({primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,dni,cargo}))(this.itemToEdit);
                const res = await fetch(`/api/personas/${id}`, { method: 'PUT', headers: { 'Content-Type':'application/json', ...this.apiHeaders() }, body: JSON.stringify(payload) });
                if(!res.ok){ const err = await res.json().catch(()=>({message:'Error al actualizar persona'})); throw new Error(err.message); }
                this.isEditModalOpenPersonas = false; this.loadPersonas();
            } catch(e){ alert(e.message || 'Error al actualizar persona'); }
        },
        openDelete(persona){ this.itemToDelete = persona; this.isDeleteModalOpenPersonas = true; },
        async deletePersona(){
            try{
                const id = this.itemToDelete?.id; if(!id) return;
                const res = await fetch(`/api/personas/${id}`, { method:'DELETE', headers: this.apiHeaders() });
                if(!res.ok){ const err = await res.json().catch(()=>({message:'Error al eliminar persona'})); throw new Error(err.message); }
                this.isDeleteModalOpenPersonas = false; this.loadPersonas();
            } catch(e){ alert(e.message || 'Error al eliminar persona'); }
        },

        onModalSubmit(){
            if(this.isModalOpenPersonas) return this.createPersona();
            if(this.isEditModalOpenPersonas) return this.updatePersona();
            if(this.isDeleteModalOpenPersonas) return this.deletePersona();
        }
    }" x-init="init()" @modal-submit.window="onModalSubmit()">
    <x-admin.tabla-crud class="nunito-bold" :titulo="'Gestión de Personas'">
        <x-slot name="filtros">
            <div class="w-full">
                <!-- On mobile: stack vertically; on sm+ keep a single row with filters then buttons at the end -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full">
                    <div class="flex-1 flex items-center gap-2">
                        <!-- Búsqueda mínima visible -->
                        <input type="text" x-model="searchPersonas" placeholder="Buscar..."
                            class="border rounded px-3 py-2 text-sm w-full sm:w-64 nunito-regular" />
                        <button type="button" @click="showMoreFilters = !showMoreFilters"
                            class="px-3 py-2 border rounded text-sm nunito-regular bg-white hover:bg-gray-50">
                            <i class="fas fa-filter mr-1"></i> Más filtros
                        </button>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 items-center mt-2 sm:mt-0 sm:ml-auto">
                        <button @click="openAdd()"
                            class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center">
                            Agregar persona
                        </button>
                        <a :href="`/admin/reportes-header?modulo=Gestion de Personas&fecha={{ now()->format('d-M-Y') }}&q=${encodeURIComponent(searchPersonas||'')}&sort=nombre&direction=asc&tipo=${encodeURIComponent(filtroTipoPersona||'')}&genero=${encodeURIComponent(filtroGenero||'')}`" target="_blank"
                        class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 justify-center">
                            <i class="fas fa-file-alt"></i> Generar Reporte
                        </a>
                    </div>
                </div>

                <!-- Bloque de filtros extendidos -->
                <div x-show="showMoreFilters" x-transition class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                    <select x-model="filtroTipoPersona" class="border rounded px-3 py-2 text-sm w-full nunito-regular">
                        <option class="nunito-regular" value="">Todos los tipo de persona</option>
                        <option class="nunito-regular" value="Técnico">Tecnico</option>
                        <option class="nunito-regular" value="Cliente">Cliente</option>
                        <option class="nunito-regular" value="Administrador">Administrador</option>
                    </select>
                    <select x-model="filtroGenero" class="border rounded px-3 py-2 text-sm w-full nunito-regular">
                        <option class="nunito-regular" value="">Todos los género</option>
                        <option class="nunito-regular">Masculino</option>
                        <option class="nunito-regular">Femenino</option>
                    </select>
                    <!-- Controles de orden y paginación removidos -->
                </div>
            </div>
        </x-slot>

        <!-- Tabla de personas -->
        <div class="overflow-x-auto w-full">
            <div x-show="loading" class="p-3 text-sm text-gray-600 nunito-regular">Cargando…</div>
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
                    <template x-for="persona in personas.filter(p => equalsNormalized(p.tipo_persona, filtroTipoPersona) && equalsNormalized(p.genero, filtroGenero))" :key="persona.id">
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
                            <td class="py-2 px-4 nunito-regular" x-text="persona.usuario || '-' "></td>
                            <td class="py-2 px-4 flex gap-2 nunito-regular">
                                <a href="#" @click="openEdit(persona)"
                                    class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" @click="openDelete(persona)"
                                    class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

    <!-- Paginación removida -->
    </x-admin.tabla-crud>

    <!-- Modal Agregar Persona -->
    <x-admin.form-modal class="nunito-bold" modalName="isModalOpenPersonas" title="Agregar Persona" submitLabel="Guardar"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Primer Nombre</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="addForm.primer_nombre" placeholder="Ej: Juan" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Segundo Nombre</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="addForm.segundo_nombre" placeholder="Ej: Carlos" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Primer Apellido</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="addForm.primer_apellido" placeholder="Ej: Pérez" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Segundo Apellido</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="addForm.segundo_apellido" placeholder="Ej: Gómez" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">DNI</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="addForm.dni" placeholder="Ej: 12345678" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Cargo</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="addForm.cargo" placeholder="Ej: Analista" />
            </div>
            <!-- Campos de catálogos pueden agregarse luego con selects (tipo/género/perfil/usuario) -->
        </div>
    </x-admin.form-modal>
    <!-- Modal Editar Persona -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpenPersonas" title="Editar Persona" itemToEdit="itemToEdit"
        maxWidth="max-w-2xl">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Primer Nombre</label>
        <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="itemToEdit.primer_nombre" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Segundo Nombre</label>
        <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="itemToEdit.segundo_nombre" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Primer Apellido</label>
        <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="itemToEdit.primer_apellido" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Segundo Apellido</label>
        <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="itemToEdit.segundo_apellido" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">DNI</label>
        <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="itemToEdit.dni" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Cargo</label>
        <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="itemToEdit.cargo" />
            </div>
        <!-- Campos de catálogos pueden agregarse luego con selects (tipo/género/perfil/usuario) -->
        </div>
    </x-admin.edit-modal>
    <!-- Modal Eliminar Persona -->
    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteModalOpenPersonas" itemToDelete="itemToDelete"
        message="¿Estás seguro de que deseas eliminar esta persona?" />
</div>