<div x-data="{
  agencias: [], loading: false,
  clientes: [], loadingClientes: false,
  
  numbersAgencias: [],
  currentPageAgencias: 1,
  perPageAgencias: 10,

  searchAgencia: '', ciudadFiltro: '', ordenarPor: 'nombre',
  direcciones: [], loadingDirecciones: false,
  isAgenciaModalOpen: false, isDeleteAgenciaModalOpen: false,
  formAgencia: { id: null, nombre: '', horario: '', direccion_id: '', clients: [], _touched: {} },
  agenciaToDelete: null,

  paginatedAgencias() {
    return this.agencias.slice(
        (this.currentPageAgencias - 1) * this.perPageAgencias, 
        this.currentPageAgencias * this.perPageAgencias
    );
  },
  totalPagesAgencias() {
      return Math.ceil(this.agencias.length / this.perPageAgencias);
  },
  nextPageAgencias() {
      if (this.currentPageAgencias < this.totalPagesAgencias()) {
          this.currentPageAgencias++;
      }
  },
  prevPageAgencias() {
      if (this.currentPageAgencias > 1) {
          this.currentPageAgencias--;
      }
  },

  dias: [ { key: 'Lun', sel: false }, { key: 'Mar', sel: false }, { key: 'Mié', sel: false }, { key: 'Jue', sel: false }, { key: 'Vie', sel: false }, { key: 'Sáb', sel: false }, { key: 'Dom', sel: false } ],
  horaInicio: '08:00', horaFin: '17:00',
  composeHorario() { /* ... sin cambios ... */ },
  compactarDias(dias) { /* ... sin cambios ... */ },
  parseHorario(texto) { /* ... sin cambios ... */ },
  cargarRangosDesdeTexto(){ /* ... sin cambios ... */ },
  
  async fetch() { 
      await window.agenciasApiHandlers.fetchAgencias(this);
      this.numbersAgencias = this.agencias;
  },
  async fetchDirecciones() { await window.paisesApiHandlers.fetchDirecciones(this); },
  async fetchClientes() { await window.agenciasApiHandlers.fetchClientes(this); },
  async store() {
      await window.agenciasApiHandlers.createAgencia(this);
      this.fetch();
  },
  async update() {
      await window.agenciasApiHandlers.updateAgencia(this);
      this.fetch();
  },
  async remove() {
      await window.agenciasApiHandlers.deleteAgencia(this);
      this.fetch();
  },
  
  handleModalSubmit(e) {
      if (e.detail.formId === 'form-agencia') {
          this.formAgencia.id ? this.update() : this.store();
      }
  },
  handleDelete() {
      if (this.agenciaToDelete) {
          this.remove();
      }
  },

  selectedAvailable: [],
  selectedAssociated: [],
  availableClients() { return this.clientes.filter(c => !this.formAgencia.clients.includes(c.id)); },
  associatedClients() { return (this.formAgencia.clients || []).map(id => this.clientes.find(c => c.id == id)).filter(Boolean); },
  moveToAssociated(selected) {
    if (!Array.isArray(selected) || !selected.length) return;
    selected.forEach(id => { if (!this.formAgencia.clients.includes(id)) this.formAgencia.clients.push(id); });
    this.selectedAvailable = [];
  },
  moveToAvailable(selected) {
    if (!Array.isArray(selected) || !selected.length) return;
    this.formAgencia.clients = (this.formAgencia.clients || []).filter(id => !selected.includes(id));
    this.selectedAssociated = [];
  },
  moveAllToAssociated() { 
    this.formAgencia.clients = this.clientes.map(c => c.id); 
  },
  moveAllToAvailable() { 
    this.formAgencia.clients = []; 
  },
}" x-init="
    fetch(); 
    fetchDirecciones();
    fetchClientes();
    $watch('searchAgencia', () => { currentPageAgencias = 1; fetch(); }); 
    $watch('ciudadFiltro', () => { currentPageAgencias = 1; fetch(); }); 
    $watch('ordenarPor', () => { currentPageAgencias = 1; fetch(); });
" @modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="handleDelete()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Agencias</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'searchAgencia',
            'filtrosSelect' => [
            'ciudadFiltro' => [
            'label' => 'Ciudades',
            'options' => ['Tegucigalpa', 'San Pedro Sula']
            ]
            ],
            'ordenarOptions' => [
            'nombre' => 'Nombre',
            'ciudad' => 'Ciudad',
            'departamento' => 'Departamento',
            'pais' => 'País'
            ]
            ])
        </x-slot>

        <x-slot name="actions">
            @perm(['Agencias'], 'insercion')
            <button
                @click="formAgencia = { id:null, nombre: '', horario: '', direccion_id: '', clients: [], _touched: {} }; dias.forEach(d=>d.sel=false); horaInicio='08:00'; horaFin='17:00'; composeHorario(); isAgenciaModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nueva
                agencia</button>
            @else
            <button disabled title="Sin permiso para crear"
                class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm cursor-not-allowed">Nueva
                agencia</button>
            @endperm
            <a href="/admin/reportes-header?modulo=Agencias&fecha={{ \App\Helpers\DateHelper::nowFormatted('d/m/Y') }}" target="_blank"
                class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                <i class="fas fa-file-alt"></i> Generar Reporte
            </a>
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr class="border-0">
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 first:rounded-tl-lg border-0">
                            Nombre</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Horario</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Dirección</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Ciudad</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Departamento</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">País</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Clientes</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 last:rounded-tr-lg border-0">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 nunito-regular"><i
                                    class="fas fa-spinner fa-spin mr-2"></i> Cargando agencias...</td>
                        </tr>
                    </template>
                    <template x-if="!loading && agencias.length === 0">
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 nunito-regular">No hay agencias
                                registradas</td>
                        </tr>
                    </template>
                    <template x-if="!loading && agencias.length > 0">
                        <template x-for="(ag, index) in paginatedAgencias()" :key="ag.id_agencias_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'last:border-b-0': index === paginatedAgencias().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="ag.nombre_agencia"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="ag.horario_agencia"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200"
                                    x-text="(ag.direccion && (ag.direccion.direccion_completa || [ag.direccion.calle, ag.direccion.numero, ag.direccion.colonia].filter(Boolean).join(' '))) || '-' ">
                                </td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200"
                                    x-text="ag.direccion?.ciudad?.nombre_ciudad || '-' "></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200"
                                    x-text="ag.direccion?.ciudad?.departamento?.nombre_departamento || '-' "></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200"
                                    x-text="ag.direccion?.ciudad?.departamento?.pais?.nombre_pais || '-' "></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200"
                                    x-html="ag.clientes && ag.clientes.length ? (ag.clientes.slice(0,2).map(c=>c.nombre).join(', ') + (ag.clientes.length>2 ? ' +' + (ag.clientes.length-2) : '')) : '-' ">
                                </td>
                                <td class="py-2 px-4 flex gap-2">
                                    @perm(['Agencias'], 'actualizacion')
                                    <a href="#"
                                        @click.prevent="formAgencia = { id: ag.id_agencias_pk, nombre: ag.nombre_agencia, horario: ag.horario_agencia, direccion_id: ag.id_direccion_fk, clients: (ag.clientes ? ag.clientes.map(c=>c.id) : []), _touched: {} }; cargarRangosDesdeTexto(); isAgenciaModalOpen = true"
                                        class="text-blue-500 hover:text-blue-700" title="Editar"><i
                                            class="fas fa-edit"></i></a>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para editar"><i
                                            class="fas fa-edit"></i></span>
                                    @endperm
                                    @perm(['Agencias'], 'eliminacion')
                                    <a href="#"
                                        @click.prevent="isDeleteAgenciaModalOpen = true; agenciaToDelete = { id: ag.id_agencias_pk, nombre: ag.nombre_agencia }"
                                        class="text-red-500 hover:text-red-700" title="Eliminar"><i
                                            class="fas fa-trash"></i></a>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para eliminar"><i
                                            class="fas fa-trash"></i></span>
                                    @endperm
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loading">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando agencias...
                </div>
            </template>
            <template x-if="!loading && agencias.length === 0">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay agencias registradas</div>
            </template>
            <template x-if="!loading && agencias.length > 0">
                {{-- 3. Se eliminó la vista de tarjetas duplicada. --}}
                <template x-for="ag in paginatedAgencias()" :key="ag.id_agencias_pk">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold"
                                x-text="ag.nombre_agencia"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular"
                            x-text="'Horario: ' + ag.horario_agencia"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular"
                            x-text="'Dirección: ' + ((ag.direccion && (ag.direccion.direccion_completa || [ag.direccion.calle, ag.direccion.numero, ag.direccion.colonia].filter(Boolean).join(' '))) || 'No especificada')">
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular"
                            x-text="'Ciudad: ' + (ag.direccion?.ciudad?.nombre_ciudad || 'No especificada')"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular"
                            x-text="'Departamento: ' + (ag.direccion?.ciudad?.departamento?.nombre_departamento || 'No especificado')">
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular"
                            x-text="'País: ' + (ag.direccion?.ciudad?.departamento?.pais?.nombre_pais || 'No especificado')">
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular"
                            x-text="'Clientes: ' + (ag.clientes && ag.clientes.length ? (ag.clientes.slice(0,2).map(c=>c.nombre).join(', ') + (ag.clientes.length>2 ? ' +' + (ag.clientes.length-2) : '')) : 'Ninguno')">
                        </p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            @perm(['Agencias'], 'actualizacion')
                            <button
                                @click.prevent="formAgencia = { id: ag.id_agencias_pk, nombre: ag.nombre_agencia, horario: ag.horario_agencia, direccion_id: ag.id_direccion_fk, clients: (ag.clientes ? ag.clientes.map(c=>c.id) : []), _touched: {} }; cargarRangosDesdeTexto(); isAgenciaModalOpen = true"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular"><i
                                    class="fas fa-edit"></i> Editar</button>
                            @else
                            <button
                                class="px-3 py-1 text-xs bg-gray-300 text-gray-600 rounded cursor-not-allowed flex items-center gap-1 nunito-regular"
                                disabled title="Sin permiso para editar"><i class="fas fa-edit"></i> Editar</button>
                            @endperm
                            @perm(['Agencias'], 'eliminacion')
                            <button
                                @click.prevent="isDeleteAgenciaModalOpen = true; agenciaToDelete = { id: ag.id_agencias_pk, nombre: ag.nombre_agencia }"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular"><i
                                    class="fas fa-trash"></i> Eliminar</button>
                            @else
                            <button
                                class="px-3 py-1 text-xs bg-red-600/50 text-white rounded cursor-not-allowed flex items-center gap-1 nunito-regular"
                                disabled title="Sin permiso para eliminar"><i class="fas fa-trash"></i>
                                Eliminar</button>
                            @endperm
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <div x-show="totalPagesAgencias() > 1"
        class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <div class="mb-2">
            <span
                class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white"
                    x-text="(currentPageAgencias - 1) * perPageAgencias + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white"
                    x-text="Math.min(currentPageAgencias * perPageAgencias, agencias.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="agencias.length"></strong>
                resultados
            </span>
        </div>
        <div
            class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageAgencias()" :disabled="currentPageAgencias === 1"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Anterior</span>
            </button>
            <div class="flex items-center gap-1">
                <template
                    x-for="page in Array.from({length: totalPagesAgencias()}, (_, i) => i + 1).slice(Math.max(0, currentPageAgencias - 3), currentPageAgencias + 2)"
                    :key="page">
                    <button @click="currentPageAgencias = page"
                        class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-blue-900 hover:text-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        :class="page === currentPageAgencias ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>
            <button @click="nextPageAgencias()" :disabled="currentPageAgencias === totalPagesAgencias()"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <x-admin.form-modal class="nunito-bold" modalName="isAgenciaModalOpen" title="Agencia" submitLabel="Guardar Agencia"
        maxWidth="max-w-7xl" formId="form-agencia">
        <div x-effect="composeHorario()"></div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="nombre_agencia"
                    class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Nombre de la
                    agencia</label>
                <input type="text" id="nombre_agencia" name="nombre_agencia" x-model="formAgencia.nombre"
                    maxlength="100" @input="formAgencia._touched.nombre = true"
                    @blur="formAgencia._touched.nombre = true"
                    class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white  nunito-regular px-2"
                    :class="formAgencia._touched && formAgencia._touched.nombre && (formAgencia.nombre === '' || formAgencia.nombre.length >= 100) ? 'border-red-500' : ''"
                    placeholder="Ej. Agencia Centro">
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formAgencia._touched && formAgencia._touched.nombre && (formAgencia.nombre === '' || formAgencia.nombre.length >= 100) ? 'text-red-500' : ''">Requerido.
                    Máximo 100 caracteres.</small>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold mb-2">Horario</label>
                <div class="space-y-3">
                    <div>
                        <span class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Días:</span>
                        <div class="flex flex-wrap items-center gap-2">
                            <template x-for="d in dias" :key="d.key">
                                <label class="inline-flex items-center gap-1 text-sm"><input type="checkbox"
                                        x-model="d.sel" @change="composeHorario()"
                                        class="rounded text-blue-600 focus:ring-blue-500"> <span
                                        x-text="d.key"></span></label>
                            </template>
                        </div>
                        <div class="flex flex-wrap gap-1 mt-2">
                            <button type="button"
                                class="text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-600 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500"
                                @click="dias.forEach((d,i)=> d.sel = (i<5)); composeHorario()">Lun–Vie</button>
                            <button type="button"
                                class="text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-600 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500"
                                @click="dias.forEach(d=> d.sel = true); composeHorario()">Todos</button>
                            <button type="button"
                                class="text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-600 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500"
                                @click="dias.forEach(d=> d.sel = false); composeHorario()">Ninguno</button>
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Hora:</span>
                        <div class="flex items-center gap-2">
                            <input type="time" x-model="horaInicio" @change="composeHorario()"
                                class="rounded border border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-2 py-1 text-sm">
                            <span class="text-gray-500">–</span>
                            <input type="time" x-model="horaFin" @change="composeHorario()"
                                class="rounded border border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-2 py-1 text-sm">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-2">
                <label for="direccion_agencia"
                    class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Dirección</label>
                <select id="direccion_agencia" name="direccion_agencia" x-model.number="formAgencia.direccion_id"
                    @change="formAgencia._touched.direccion = true"
                    class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white  nunito-regular px-2"
                    :class="formAgencia._touched && formAgencia._touched.direccion && !formAgencia.direccion_id ? 'border-red-500' : ''">
                    <option value="">Seleccione una dirección</option>
                    <template x-for="d in direcciones" :key="d.id_direccion_pk">
                        <option :value="d.id_direccion_pk"
                            x-text="(d.direccion_completa || [d.calle, d.numero, d.colonia].filter(Boolean).join(' ')) + (d.ciudad ? ' - ' + d.ciudad.nombre_ciudad : '')">
                        </option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formAgencia._touched && formAgencia._touched.direccion && !formAgencia.direccion_id ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Clientes
                    asociados</label>
                <div class="mt-2 grid grid-cols-3 gap-3 items-center">
                    <div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-2 nunito-regular">Clientes disponibles
                        </div>
                        <ul class="h-24 overflow-auto border rounded p-2 bg-white dark:bg-gray-800 text-sm">
                            <template x-if="loadingClientes">
                                <li class="text-gray-500 nunito-regular p-1">Cargando clientes...</li>
                            </template>
                            <template x-if="!loadingClientes">
                                <template x-for="c in availableClients()" :key="c.id">
                                    <li @click="selectedAvailable.includes(c.id) ? selectedAvailable = selectedAvailable.filter(i=>i!==c.id) : selectedAvailable.push(c.id)"
                                        @dblclick="moveToAssociated([c.id])"
                                        :class="selectedAvailable.includes(c.id) ? 'bg-gray-200 dark:bg-gray-700' : ''"
                                        class="p-1 cursor-pointer nunito-regular" x-text="c.nombre"></li>
                                </template>
                            </template>
                        </ul>
                    </div>
                    <div class="flex flex-col items-center justify-center gap-2">
                        <button type="button" class="px-3 py-1 bg-blue-600 text-white rounded"
                            @click.prevent="moveToAssociated(selectedAvailable)">→</button>
                        <button type="button" class="px-3 py-1 bg-blue-600 text-white rounded"
                            @click.prevent="moveToAvailable(selectedAssociated)">←</button>
                        <button type="button" class="px-3 py-1 bg-gray-600 text-white rounded text-xs"
                            @click.prevent="moveAllToAssociated()">Todos →</button>
                        <button type="button" class="px-3 py-1 bg-gray-600 text-white rounded text-xs"
                            @click.prevent="moveAllToAvailable()">← Todos</button>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-2 nunito-regular">Clientes asociados
                        </div>
                        <ul class="h-24 overflow-auto border rounded p-2 bg-white dark:bg-gray-800 text-sm">
                            <template x-if="loadingClientes">
                                <li class="text-gray-500 nunito-regular p-1">Cargando...</li>
                            </template>
                            <template x-if="!loadingClientes">
                                <template x-for="c in associatedClients()" :key="c.id">
                                    <li @click="selectedAssociated.includes(c.id) ? selectedAssociated = selectedAssociated.filter(i=>i!==c.id) : selectedAssociated.push(c.id)"
                                        @dblclick="moveToAvailable([c.id])"
                                        :class="selectedAssociated.includes(c.id) ? 'bg-gray-200 dark:bg-gray-700' : ''"
                                        class="p-1 cursor-pointer nunito-regular" x-text="c.nombre"></li>
                                </template>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </x-admin.form-modal>

    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteAgenciaModalOpen" itemToDelete="agenciaToDelete"
        message="¿Estás seguro de que quieres eliminar la agencia?" />

</div>