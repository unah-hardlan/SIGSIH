<div 
  x-data="{

    tickets: [],
    loading: false,

    // Modal state
    isModalOpen: false,
    isEditModalOpen: false,
    isDeleteModalOpen: false,
    ticketToEdit: null,
    ticketToDelete: null,

    estadosTicket: [],
    personas: [],
    clientes: [],
    tecnicos: [],
    numbersTickets: [],
   
    search: '',
    filtroEstado: '',
    filtroTecnico: '',
    filtroCliente: '',
    desde: '',
    hasta: '',
    ordenarPor: 'fecha',
    ordenarDirection: 'desc',
    currentPageTickets: 1,   
    perPageTickets: 10,      


    new_fecha_creacion: '',
    new_descripcion_ticket: '',
    new_id_estado_ticket_fk: '',
    new_id_tecnico_fk: '',
    new_id_cliente_fk: '',

    edit_fecha_creacion: '',
    edit_descripcion_ticket: '',
    edit_id_estado_ticket_fk: '',
    edit_id_tecnico_fk: '',
    edit_id_cliente_fk: '',

    paginatedTickets() {
        return this.tickets.slice(
            (this.currentPageTickets - 1) * this.perPageTickets,
            this.currentPageTickets * this.perPageTickets
        );
    },
    totalPagesTickets() {
        return Math.ceil(this.tickets.length / this.perPageTickets);
    },
    nextPageTickets() {
        if (this.currentPageTickets < this.totalPagesTickets()) {
            this.currentPageTickets++;
        }
    },
    prevPageTickets() {
        if (this.currentPageTickets > 1) {
            this.currentPageTickets--;
        }
    },


    // --- API Methods ---
    async fetchCatalogs() {
      await window.ticketsApiHandlers.fetchCatalogs(this);
    },
    async fetchTickets() {
      await window.ticketsApiHandlers.fetchTickets(this);
      this.numbersTickets = this.tickets;
    },
    async store() {
      await window.ticketsApiHandlers.store(this);
      this.numbersTickets = this.tickets;
    },
    async update() {
      await window.ticketsApiHandlers.update(this);
      this.numbersTickets = this.tickets;
    },
    async remove() {
      await window.ticketsApiHandlers.remove(this);
      this.numbersTickets = this.tickets;
    },

    // --- Event Handlers ---
    handleModalSubmit(e) {
      if (e.detail.formId === 'form-ticket-add') this.store();
      if (e.detail.formId === 'form-ticket-edit') this.update();
    },
    handleDelete() {
      if (this.isDeleteModalOpen) this.remove();
    },

    // --- UI Actions (Open Modals) ---
    openAdd() {
      // Default fecha to now in local timezone for convenience
      try {
        const d = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        const local = ${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())};
        this.new_fecha_creacion = local;
      } catch (_) {
        this.new_fecha_creacion = '';
      }
      this.isModalOpen = true;
    },
    openEdit(item) {
      this.ticketToEdit = { ...item };
      this.edit_fecha_creacion = item.fecha_creacion ? item.fecha_creacion.replace(' ', 'T').slice(0, 16) : '';
      this.edit_descripcion_ticket = item.descripcion_ticket || '';
      this.edit_id_estado_ticket_fk = item.id_estado_ticket_fk || '';
      this.edit_id_tecnico_fk = item.id_tecnico_fk || '';
      this.edit_id_cliente_fk = item.id_cliente_fk || '';
      this.isEditModalOpen = true;
    },
    openDelete(item) {
      this.ticketToDelete = item;
      this.isDeleteModalOpen = true;
    },
  }"

  x-init="
    // Initial data load
    await fetchCatalogs();
    await fetchTickets();

    // Watchers to refetch data on any filter/sort change
    $watch('search', () => { fetchTickets(); currentPageTickets = 1; });
    $watch('filtroEstado', () => { fetchTickets(); currentPageTickets = 1; });
    $watch('filtroTecnico', () => { fetchTickets(); currentPageTickets = 1; });

$watch('filtroCliente', () => { fetchTickets(); currentPageTickets = 1; });
    $watch('desde', () => { fetchTickets(); currentPageTickets = 1; });
    $watch('hasta', () => { fetchTickets(); currentPageTickets = 1; });
    $watch('ordenarPor', () => { fetchTickets(); currentPageTickets = 1; });
    $watch('ordenarDirection', () => { fetchTickets(); currentPageTickets = 1; });
  "

  @keydown.escape.window="isModalOpen = false; isEditModalOpen = false; isDeleteModalOpen = false;"
  @modal-submit.window="handleModalSubmit($event)"
  @confirm-delete.window="handleDelete()"
  class="overflow-x-auto">

  <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4" title="Gestión de Tickets">
    <x-slot name="filters">
      @include('partials.filtros-generales', [
      'searchModel' => 'search',
      'ordenarOptions' => [ 'id' => 'ID', 'cliente' => 'Cliente', 'fecha' => 'Fecha', 'estado' => 'Estado' ]
      ])
      <select x-model="filtroEstado"
        class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
        <option value="">Todos los estados</option>
        <template x-for="e in estadosTicket" :key="e.id_estado_ticket_pk">
          <option :value="e.id_estado_ticket_pk" x-text="e.nombre"></option>
        </template>
      </select>
      <select x-model="filtroTecnico"
        class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
        <option value="">Todos los técnicos</option>
          <template x-for="p in tecnicos" :key="p.id">
          <option :value="p.id"
            x-text="(p.primer_nombre ? [p.primer_nombre,p.primer_apellido].filter(Boolean).join(' ') : (p.nombre||('ID '+p.id)))">
          </option>
        </template>
      </select>
      <select x-model="filtroCliente"
        class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
        <option value="">Todos los clientes</option>
        <template x-for="c in clientes" :key="c.id">
          <option :value="c.id" x-text="c.nombre"></option>
        </template>
      </select>
      <input type="date" x-model="desde" placeholder="Desde"
        class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-48 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />
      <input type="date" x-model="hasta" placeholder="Hasta"
        class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-48 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />
    </x-slot>

    <x-slot name="actions">
      <button @click="openAdd()"
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo
        ticket</button>
      <a href="/admin/reportes-header?modulo=Tickets&fecha={{ now()->format('d-M-Y') }}" target="_blank"
        class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
        <i class="fas fa-file-alt"></i> Generar Reporte
      </a>
    </x-slot>

    <x-slot name="table">
      <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
        <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
          <tr>
            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Cliente</th>
            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Fecha</th>

<th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Estado</th>
            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <template x-if="loading">
            <tr>
              <td colspan="4" class="py-8 text-center text-gray-500 nunito-regular"><i
                  class="fas fa-spinner fa-spin mr-2"></i> Cargando tickets...</td>
            </tr>
          </template>
          <template x-if="!loading && tickets.length===0">
            <tr>
              <td colspan="4" class="py-8 text-center text-gray-500 nunito-regular">No hay tickets</td>
            </tr>
          </template>
          <template x-if="!loading && tickets.length>0">
            <template x-for="(t, index) in paginatedTickets()" :key="t.id_ticket_pk">
              <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                  :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedTickets().length - 1 }">
                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="t.cliente_nombre"></td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="t.fecha_creacion"></td>
                <td class="py-2 px-4">
                  <span class="px-2 py-1 rounded nunito-regular text-xs" :class="{
                          'bg-yellow-100 text-yellow-700 dark:bg-yellow-600 dark:text-yellow-100': (t.estado_nombre||'').toLowerCase().includes('pend'),
                          'bg-green-100 text-green-700 dark:bg-green-600 dark:text-green-100': (t.estado_nombre||'').toLowerCase().includes('proce'),
                          'bg-blue-100 text-blue-700 dark:bg-blue-600 dark:text-blue-100': (t.estado_nombre||'').toLowerCase().includes('final')
                        }" x-text="t.estado_nombre"></span>
                </td>
                <td class="py-2 px-4 flex items-center gap-2">
                  <a href="#" @click.prevent="openEdit(t)"
                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                  <a href="#" @click.prevent="openDelete(t)"
                    class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
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
          class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-8 text-center text-gray-500 nunito-regular">
          <i class="fas fa-spinner fa-spin mr-2"></i> Cargando tickets...
        </div>
      </template>
      <template x-if="!loading && tickets.length===0">
        <div
          class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-8 text-center text-gray-500 nunito-regular">
          No hay tickets</div>
      </template>
      <template x-if="!loading && tickets.length>0">
        <template x-for="t in paginatedTickets()" :key="t.id_ticket_pk">
          <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex justify-between items-start mb-2">
              <div class="space-y-1">
                <div class="text-sm text-gray-600 dark:text-gray-300 nunito-bold">ID</div>
                <div class="nunito-regular text-gray-900 dark:text-gray-100" x-text="t.id_ticket_pk">
                </div>
              </div>
              <span class="px-2 py-1 rounded text-xs" :class="{
                      'bg-yellow-100 text-yellow-700 dark:bg-yellow-600 dark:text-yellow-100': (t.estado_nombre||'').toLowerCase().includes('pend'),

'bg-green-100 text-green-700 dark:bg-green-600 dark:text-green-100': (t.estado_nombre||'').toLowerCase().includes('proce'),
                      'bg-blue-100 text-blue-700 dark:bg-blue-600 dark:text-blue-100': (t.estado_nombre||'').toLowerCase().includes('final')
                    }" x-text="t.estado_nombre"></span>
            </div>
            <div class="space-y-1 text-sm">
              <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Cliente:</span>
                <span class="text-gray-900 dark:text-gray-200 nunito-regular"
                  x-text="t.cliente_nombre"></span>
              </div>
              <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Fecha:</span>
                <span class="text-gray-900 dark:text-gray-200 nunito-regular"
                  x-text="t.fecha_creacion"></span>
              </div>
              <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Técnico:</span>
                <span class="text-gray-900 dark:text-gray-200 nunito-regular"
                  x-text="t.tecnico_nombre"></span>
              </div>
              <div><span
                  class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span>
                <span class="text-gray-900 dark:text-gray-200 nunito-regular"
                  x-text="t.descripcion_ticket"></span>
              </div>
            </div>
            <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
              <button @click="openEdit(t)"
                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                <i class="fas fa-edit"></i> Editar
              </button>
              <button @click="openDelete(t)"
                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                <i class="fas fa-trash"></i> Eliminar
              </button>
            </div>
          </div>
        </template>
      </template>
    </x-slot>
  </x-responsive-table>

  <!-- Paginación para Tickets -->
  <div x-show="numbersTickets.length > perPageTickets" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
    <div class="mb-2">
      <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
        Mostrando
        <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageTickets - 1) * perPageTickets + 1"></strong>
        a
        <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageTickets * perPageTickets, numbersTickets.length)"></strong>
        de
        <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="numbersTickets.length"></strong>
        resultados
      </span>
    </div>
    <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
      <button @click="prevPageTickets()" :disabled="currentPageTickets === 1"
              class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        <span>Anterior</span>
      </button>
      <div class="flex items-center gap-1">
        <template x-for="page in Array.from({length: totalPagesTickets()}, (_, i) => i + 1).slice(Math.max(0, currentPageTickets - 3), currentPageTickets + 2)" :key="page">

<button @click="currentPageTickets = page"
                  class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                  :class="page === currentPageTickets ? 'bg-blue-600 text-white' : ''">
            <span x-text="page"></span>
          </button>
        </template>
      </div>
      <button @click="nextPageTickets()" :disabled="currentPageTickets === totalPagesTickets()"
              class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
        <span>Siguiente</span>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      </button>
    </div>
  </div>

  <!-- Modal Nuevo Ticket -->
  <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Nuevo Ticket" submitLabel="Guardar Ticket"
    maxWidth="max-w-2xl" formId="form-ticket-add">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
        <input type="datetime-local" x-model="new_fecha_creacion"
          class="mt-1 w-full border border-gray-500 rounded px-3 py-2 nunito-regular dark:bg-gray-800 dark:text-gray-200" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 nunito-bold">Estado</label>
        <select x-model="new_id_estado_ticket_fk" class="mt-1 w-full border border-gray-500 rounded px-3 py-2 nunito-regular dark:bg-gray-800 dark:text-gray-200">
          <option value="">Seleccione...</option>
          <template x-for="e in estadosTicket" :key="e.id_estado_ticket_pk">
            <option :value="e.id_estado_ticket_pk" x-text="e.nombre"></option>
          </template>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 nunito-bold">Técnico</label>
        <select x-model="new_id_tecnico_fk" class="mt-1 w-full border border-gray-500 rounded px-3 py-2 nunito-regular dark:bg-gray-800 dark:text-gray-200">
          <option value="">Seleccione...</option>
            <template x-for="p in tecnicos" :key="p.id">
            <option :value="p.id"
              x-text="(p.primer_nombre ? [p.primer_nombre,p.primer_apellido].filter(Boolean).join(' ') : (p.nombre||('ID '+p.id)))">
            </option>
          </template>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 nunito-bold">Cliente</label>
        <select x-model="new_id_cliente_fk" class="mt-1 w-full border border-gray-500 rounded px-3 py-2 nunito-regular dark:bg-gray-800 dark:text-gray-200">
          <option value="">Seleccione...</option>
          <template x-for="c in clientes" :key="c.id">
            <option :value="c.id" x-text="c.nombre"></option>
          </template>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
        <textarea x-model="new_descripcion_ticket" rows="2"
          class="mt-1 w-full border border-gray-500 rounded px-3 py-2 nunito-regular dark:bg-gray-800 dark:text-gray-200"></textarea>
      </div>
    </div>
  </x-admin.form-modal>

  <!-- Modal Editar Ticket -->
  <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Ticket" itemToEdit="ticketToEdit"
    maxWidth="max-w-2xl" formId="form-ticket-edit">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>

<input type="datetime-local" x-model="edit_fecha_creacion"
          class="mt-1 w-full border border-gray-500 rounded px-3 py-2 nunito-regular dark:bg-gray-800 dark:text-gray-200" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 nunito-bold">Estado</label>
        <select x-model="edit_id_estado_ticket_fk" class="mt-1 w-full border border-gray-500 rounded px-3 py-2 nunito-regular dark:bg-gray-800 dark:text-gray-200">
          <option value="">Seleccione...</option>
          <template x-for="e in estadosTicket" :key="e.id_estado_ticket_pk">
            <option :value="e.id_estado_ticket_pk" x-text="e.nombre"></option>
          </template>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 nunito-bold">Técnico</label>
        <select x-model="edit_id_tecnico_fk" class="mt-1 w-full border border-gray-500 rounded px-3 py-2 nunito-regular dark:bg-gray-800 dark:text-gray-200">
          <option value="">Seleccione...</option>
            <template x-for="p in tecnicos" :key="p.id">
            <option :value="p.id"
              x-text="(p.primer_nombre ? [p.primer_nombre,p.primer_apellido].filter(Boolean).join(' ') : (p.nombre||('ID '+p.id)))">
            </option>
          </template>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 nunito-bold">Cliente</label>
        <select x-model="edit_id_cliente_fk" class="mt-1 w-full border border-gray-500 rounded px-3 py-2 nunito-regular dark:bg-gray-800 dark:text-gray-200">
          <option value="">Seleccione...</option>
          <template x-for="c in clientes" :key="c.id">
            <option :value="c.id" x-text="c.nombre"></option>
          </template>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
        <textarea x-model="edit_descripcion_ticket" rows="2"
          class="mt-1 w-full border border-gray-500 rounded px-3 py-2 nunito-regular dark:bg-gray-800 dark:text-gray-200"></textarea>
      </div>
    </div>
  </x-admin.edit-modal>

  <!-- Modal Confirmar Eliminación -->
  <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteModalOpen" itemToDelete="ticketToDelete"
    message="¿Estás seguro de que quieres eliminar el ticket?" />

</div>