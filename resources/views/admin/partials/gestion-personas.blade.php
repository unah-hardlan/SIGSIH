<div x-data="{
        // UI state
        isModalOpenPersonas: false,
        isEditModalOpenPersonas: false,
        isDeleteModalOpenPersonas: false,
    itemToEdit: {},
        itemToDelete: null,
        // filters
        searchPersonas: '',
    filtroGenero: '',
    // catálogos dinámicos
    catalogoGeneros: [],      // [{id, genero}]
    catalogoUsuarios: [],     // [{id, usuario}]
    catalogosError: '',
    _catalogosPromise: null,
    catalogosTTLms: 300000, // 5 min (ya no se usa localStorage)
    // feedback (usa patrón de parámetros.js)
    notify(msg,type='success'){
        const el=document.createElement('div');
        el.textContent=msg;
        el.className=`fixed top-4 right-4 z-50 px-4 py-2 rounded shadow text-sm text-white ${type==='error'?'bg-red-600':'bg-green-600'}`;
        document.body.appendChild(el);
        setTimeout(()=>{ el.classList.add('opacity-0','transition'); },2500);
        setTimeout(()=> el.remove(),3000);
    },
    _usuariosById: {},
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
    perPage: 5000,
        total: 0,
        lastPage: 1,
    // internals to prevent storm of requests
    _suppressWatch: false,
    _abortCtrl: null,
        // forms
        addForm: {
            primer_nombre: '', segundo_nombre: '', primer_apellido: '', segundo_apellido: '',
            dni: '', id_genero_fk: '', id_usuario_fk: ''
        },

        init(){
            // cargar al abrir y observar filtros con debounce
            this.loadCatalogos().then(()=> this.loadPersonas());
            this.$watch('searchPersonas', Alpine.debounce(() => { if(this._suppressWatch) return; this.page = 1; this.loadPersonas(); }, 350));
            this.$watch('ordenarPor', (val, old) => {
                if(old === val){ this.ordenarDir = this.ordenarDir === 'asc' ? 'desc' : 'asc'; } else { this.ordenarDir='asc'; }
                this.sortLocal();
            });
            this.$watch('ordenarDir', () => { this.sortLocal(); });
            // Orden fijo, no watchers para orden
            // No observar perPage automáticamente para evitar loops; se recarga solo desde UI explícita
        },

        async loadCatalogos(){
            // Evitar llamadas duplicadas / tormenta
            if(this.catalogoGeneros.length && this.catalogoUsuarios.length) return;
            if(this._catalogosPromise) return this._catalogosPromise;

            this._catalogosPromise = (async ()=>{
                try{
                    this.catalogosError='';

                    // intento con reintentos leves si 429
                    const fetchWithRetry = async (url, tries=3)=>{
                        for(let i=0;i<tries;i++){
                            const res = await fetch(url,{ credentials: 'same-origin' });
                            if(res.status!==429) return res;
                            const wait = 400*(i+1);
                            await new Promise(r=>setTimeout(r, wait));
                        }
                        return fetch(url,{ credentials: 'same-origin' });
                    };
                    const [gRes, uRes] = await Promise.all([
                        // Endpoint de catálogo estable que devuelve { data: [{id, genero}] }
                        fetchWithRetry('/api/catalogos/generos'),
                        // Traer catálogo de usuarios (solo id y usuario si el backend lo permite)
                        fetchWithRetry('/api/usuarios?per_page=5000')
                    ]);
                    const bad = [gRes, uRes].find(r=>!r.ok);
                    if(bad){ throw new Error('Error catálogos ('+bad.status+')'); }
                    const [gData, uData] = await Promise.all([ gRes.json(), uRes.json() ]);
                    this.catalogoGeneros = (gData.data||[]).map(x=>({id:x.id, genero:x.genero}));
                    const usuariosArr = Array.isArray(uData?.data) ? uData.data : (Array.isArray(uData) ? uData : []);
                    this.catalogoUsuarios = usuariosArr.map(u=>({ id: u.id ?? u.id_usuario_pk ?? u.id_user ?? u.usuario_id ?? '', usuario: u.usuario ?? u.username ?? u.nombre_usuario ?? '' })).filter(x=>x.id!=='' && x.usuario);
                    // índice para lookup O(1)
                    this._usuariosById = Object.fromEntries(this.catalogoUsuarios.map(u=>[String(u.id), u.usuario]));
                }catch(e){ this.catalogosError = e.message || 'Error catálogos'; this.notify(this.catalogosError,'error'); }
                finally { this._catalogosPromise=null; }
            })();
            return this._catalogosPromise;
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
    const res = await fetch(`/api/personas?${params.toString()}`, { credentials: 'same-origin', signal: this._abortCtrl.signal });
                if(!res.ok){
                    const err = await res.json().catch(()=>({message:'Error cargando personas'}));
                    throw new Error(err.message || 'Error cargando personas');
                }
                const data = await res.json();
                // data is a Laravel resource collection with data[] and meta
            const items = Array.isArray(data?.data) ? data.data : [];
                this.personas = items.map(p => this.mapPersona(p));
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
        mapPersona(p){
            return {
                ...p,
                genero_nombre: p.genero?.genero || '',
                // preferir string si backend lo envía, si no, mapear por catálogo
                usuario: p.usuario?.usuario || p.usuario || this._usuariosById[String(p.id_usuario_fk||'')] || ''
            };
        },
        usuarioNombreById(id){ try{ return this._usuariosById[String(id||'')] || ''; }catch(_){ return ''; } },
        sortLocal(){
            const map = { nombre: 'primer_nombre', dni: 'dni' };
            const key = map[this.ordenarPor] || 'primer_nombre';
            const dir = this.ordenarDir === 'desc' ? -1 : 1;
            this.personas.sort((a,b)=>{
                const av = (a[key] ?? '').toString().toLowerCase();
                const bv = (b[key] ?? '').toString().toLowerCase();
                if(av < bv) return -1 * dir;
                if(av > bv) return 1 * dir;
                return 0;
            });
        },
        insertarOrdenado(persona){
            // orden actual fijo por primer_nombre asc
            const nombre = (persona.primer_nombre||'').toLowerCase();
            let i=0; for(; i < this.personas.length; i++){ const cmp=(this.personas[i].primer_nombre||'').toLowerCase(); if(nombre < cmp){ break; } }
            this.personas.splice(i,0,persona);
        },
        usuariosSinPersona(){
            try{
                const usados = new Set(this.personas.map(p=>String(p.id_usuario_fk||'')));
                return this.catalogoUsuarios.filter(u=>!usados.has(String(u.id)));
            }catch(_){ return this.catalogoUsuarios; }
        },
        openAdd(){
            this.addForm = { primer_nombre: '', segundo_nombre: '', primer_apellido: '', segundo_apellido: '', dni: '', id_genero_fk: '', id_usuario_fk: '' };
            this.isModalOpenPersonas = true;
        },
        async createPersona(){
            try{
                if(!this.addForm.id_usuario_fk){ this.notify('Seleccione un usuario','error'); return; }
                const res = await fetch('/api/personas', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify(this.addForm)
                });
                if(!res.ok){
                    const err = await res.json().catch(()=>({message:'Error al crear persona'}));
                    if(res.status===422 && err.errors){
                        const all = Object.values(err.errors).map(v=>Array.isArray(v)?v[0]:String(v)).filter(Boolean);
                        throw new Error(all.join('\n') || err.message || 'Datos inválidos');
                    }
                    throw new Error(err.message || 'Error al crear persona');
                }
                const data = await res.json();
                const p = data.data || data;
                const nuevo = this.mapPersona(p);
                this.personas.push(nuevo);
                this.sortLocal();
                this.total = this.personas.length; // ajustar meta local
                this.isModalOpenPersonas = false; this.notify('Persona creada');
            } catch(e){ this.notify(e.message || 'Error al crear persona','error'); }
        },
        openEdit(persona){ this.itemToEdit = JSON.parse(JSON.stringify(persona)); this.isEditModalOpenPersonas = true; },
        async updatePersona(){
            try{
                const id = this.itemToEdit?.id; if(!id) return;
                // enviar solo campos editables simples
                const payload = (({primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,dni,id_genero_fk}) => ({primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,dni,id_genero_fk}))(this.itemToEdit);
                const res = await fetch(`/api/personas/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type':'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload)
                });
                if(!res.ok){
                    const err = await res.json().catch(()=>({message:'Error al actualizar persona'}));
                    if(res.status===422 && err.errors){
                        const all = Object.values(err.errors).map(v=>Array.isArray(v)?v[0]:String(v)).filter(Boolean);
                        throw new Error(all.join('\n') || err.message || 'Datos inválidos');
                    }
                    throw new Error(err.message || 'Error al actualizar persona');
                }
                const data = await res.json();
                const actualizado = this.mapPersona(data.data || data);
                const idx = this.personas.findIndex(p=>p.id===id);
                if(idx>-1){ this.personas.splice(idx,1,actualizado); }
                this.sortLocal();
                this.isEditModalOpenPersonas = false; this.notify('Persona actualizada');
            } catch(e){ this.notify(e.message || 'Error al actualizar persona','error'); }
        },
        openDelete(persona){ this.itemToDelete = persona; this.isDeleteModalOpenPersonas = true; },
        async deletePersona(){
            try{
                const id = this.itemToDelete?.id; if(!id) return;
                const res = await fetch(`/api/personas/${id}`, { method:'DELETE', credentials: 'same-origin' });
                if(!res.ok){ const err = await res.json().catch(()=>({message:'Error al eliminar persona'})); throw new Error(err.message); }
                const idx = this.personas.findIndex(p=>p.id===id);
                if(idx>-1){ this.personas.splice(idx,1); this.total = this.personas.length; }
                this.isDeleteModalOpenPersonas = false; this.notify('Persona eliminada');
            } catch(e){ this.notify(e.message || 'Error al eliminar persona','error'); }
        },

        onModalSubmit(){
            if(this.isModalOpenPersonas) return this.createPersona();
            if(this.isEditModalOpenPersonas) return this.updatePersona();
            if(this.isDeleteModalOpenPersonas) return this.deletePersona();
    },

    // Generar URL de reporte dinámica con filtros actuales
    reportUrl(){
        try{
            const p = new URLSearchParams();
            p.set('modulo','Gestion de Personas');
            if(this.searchPersonas) p.set('q', this.searchPersonas);
            const map = { nombre: 'nombre', dni: 'dni' };
            const sort = map[this.ordenarPor] || 'nombre';
            p.set('sort', sort);
            p.set('direction', (this.ordenarDir==='desc' ? 'desc' : 'asc'));
            if(this.filtroGenero) p.set('genero', this.filtroGenero);
            return `/admin/reportes-header?${p.toString()}`;
        }catch(_){ return '/admin/reportes-header?modulo=Gestion%20de%20Personas'; }
    }
    }" x-init="init()" @modal-submit.window="onModalSubmit()">
    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4" title="Gestión de Personas">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'searchPersonas',
            // Usaremos un select dinámico para Género a partir del catálogo cargado por fetch
            'filtrosSelect' => [],
            'ordenarOptions' => [
            'nombre' => 'Nombre',
            'dni' => 'DNI'
            ]
            ])
            <select x-model="filtroGenero"
                class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 md:w-64 sm:min-w-[14rem] md:min-w-[16rem] shrink-0 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                <option value="">Todos los géneros</option>
                <template x-for="op in catalogoGeneros" :key="'filtro-genero-'+op.id">
                    <option :value="op.genero" x-text="op.genero"></option>
                </template>
            </select>
        </x-slot>

        <x-slot name="actions">
            <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                <button @click="openAdd()"
                    class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Agregar
                    Persona</button>
                <a :href="reportUrl()" target="_blank"
                    class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-file-alt"></i> Generar Reporte
                </a>
            </div>
        </x-slot>

        <x-slot name="table">
            <table
                class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse table-white-dividers">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left">Primer Nombre</th>
                        <th class="py-2 px-4 text-left">Segundo Nombre</th>
                        <th class="py-2 px-4 text-left">Primer Apellido</th>
                        <th class="py-2 px-4 text-left">Segundo Apellido</th>
                        <th class="py-2 px-4 text-left">DNI</th>
                        <th class="py-2 px-4 text-left">Género</th>
                        <th class="py-2 px-4 text-left">Usuario</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 nunito-regular"><i
                                    class="fas fa-spinner fa-spin mr-2"></i> Cargando…</td>
                        </tr>
                    </template>
                    <template
                        x-if="!loading && personas.filter(p => equalsNormalized(p.genero_nombre, filtroGenero)).length===0">
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 nunito-regular">No hay personas</td>
                        </tr>
                    </template>
                    <template x-for="persona in personas.filter(p => equalsNormalized(p.genero_nombre, filtroGenero))"
                        :key="persona.id">
                        <tr class="border-b dark:border-gray-700 nunito-regular">
                            <td class="py-2 px-4" x-text="persona.primer_nombre || '-' "></td>
                            <td class="py-2 px-4" x-text="persona.segundo_nombre || '-' "></td>
                            <td class="py-2 px-4" x-text="persona.primer_apellido || '-' "></td>
                            <td class="py-2 px-4" x-text="persona.segundo_apellido || '-' "></td>
                            <td class="py-2 px-4" x-text="persona.dni || '-' "></td>
                            <td class="py-2 px-4" x-text="persona.genero_nombre || '-' "></td>
                            <td class="py-2 px-4"
                                x-text="usuarioNombreById(persona.id_usuario_fk) || persona.usuario || '-' "></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click.prevent="openEdit(persona)"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click.prevent="openDelete(persona)"
                                    class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loading">
                <div class="p-8 text-center text-gray-500 nunito-regular"><i class="fas fa-spinner fa-spin mr-2"></i>
                    Cargando…</div>
            </template>
            <template
                x-if="!loading && personas.filter(p => equalsNormalized(p.genero_nombre, filtroGenero)).length===0">
                <div class="p-8 text-center text-gray-500 nunito-regular">No hay personas</div>
            </template>
            <template x-for="p in personas.filter(p => equalsNormalized(p.genero_nombre, filtroGenero))"
                :key="'card-p-'+p.id">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white"
                                x-text="[p.primer_nombre,p.segundo_nombre,p.primer_apellido,p.segundo_apellido].filter(Boolean).join(' ')">
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="p.dni"></p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                        <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Primer Nombre:</span> <span
                                x-text="p.primer_nombre || '-'"></span></div>
                        <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Segundo Nombre:</span> <span
                                x-text="p.segundo_nombre || '-'"></span></div>
                        <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Primer Apellido:</span> <span
                                x-text="p.primer_apellido || '-'"></span></div>
                        <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Segundo Apellido:</span> <span
                                x-text="p.segundo_apellido || '-'"></span></div>
                        <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Género:</span> <span
                                x-text="p.genero_nombre || '-'"></span></div>
                        <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Usuario:</span> <span
                                x-text="usuarioNombreById(p.id_usuario_fk) || p.usuario || '-'"></span></div>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <button @click="openEdit(p)"
                            class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1"><i
                                class="fas fa-edit"></i> Editar</button>
                        <button @click="openDelete(p)"
                            class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1"><i
                                class="fas fa-trash"></i> Eliminar</button>
                    </div>
                </div>
            </template>
        </x-slot>
    </x-responsive-table>

    <!-- Modal Agregar Persona -->
    <x-admin.form-modal class="nunito-bold" modalName="isModalOpenPersonas" title="Agregar Persona"
        submitLabel="Guardar" maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Primer Nombre</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular"
                    x-model="addForm.primer_nombre" placeholder="Ej: Juan" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Segundo Nombre</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular"
                    x-model="addForm.segundo_nombre" placeholder="Ej: Carlos" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Primer Apellido</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular"
                    x-model="addForm.primer_apellido" placeholder="Ej: Pérez" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Segundo Apellido</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular"
                    x-model="addForm.segundo_apellido" placeholder="Ej: Gómez" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">DNI</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="addForm.dni"
                    placeholder="Ej: 0000-0000-00000 o 0000000000000" />
            </div>


            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Género</label>
                <select class="w-full border rounded px-3 py-2 nunito-regular" x-model="addForm.id_genero_fk">
                    <option value="">Seleccione</option>
                    <template x-for="op in catalogoGeneros" :key="op.id">
                        <option :value="op.id" x-text="op.genero"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Usuario</label>
                <select class="w-full border rounded px-3 py-2 nunito-regular" x-model="addForm.id_usuario_fk">
                    <option value="">Seleccione</option>
                    <template x-for="u in usuariosSinPersona()" :key="'u-add-'+u.id">
                        <option :value="u.id" x-text="u.usuario"></option>
                    </template>
                </select>
            </div>

        </div>
    </x-admin.form-modal>
    <!-- Modal Editar Persona -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpenPersonas" title="Editar Persona"
        itemToEdit="itemToEdit" maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Primer Nombre</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular"
                    x-model="itemToEdit.primer_nombre" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Segundo Nombre</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular"
                    x-model="itemToEdit.segundo_nombre" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Primer Apellido</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular"
                    x-model="itemToEdit.primer_apellido" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Segundo Apellido</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular"
                    x-model="itemToEdit.segundo_apellido" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">DNI</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" x-model="itemToEdit.dni"
                    placeholder="Ej: 0000-0000-00000 o 0000000000000" />
            </div>


            <div>
                <label class="block text-sm font-medium mb-1 nunito-bold">Género</label>
                <select class="w-full border rounded px-3 py-2 nunito-regular" x-model="itemToEdit.id_genero_fk">
                    <option value="">Seleccione</option>
                    <template x-for="op in catalogoGeneros" :key="'edit-genero-'+op.id">
                        <option :value="op.id" x-text="op.genero"></option>
                    </template>
                </select>
            </div>

        </div>
    </x-admin.edit-modal>
    <!-- Modal Eliminar Persona -->
    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteModalOpenPersonas" itemToDelete="itemToDelete"
        message="¿Estás seguro de que deseas eliminar esta persona?" />
</div>