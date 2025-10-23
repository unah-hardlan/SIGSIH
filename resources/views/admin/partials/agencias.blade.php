<div x-data="{
  // listado
  agencias: [], loading: false,
  // filtros
  searchAgencia: '', ciudadFiltro: '', ordenarPor: 'nombre',
  // direcciones para el formulario
  direcciones: [], loadingDirecciones: false,
  // estados de modales
  isAgenciaModalOpen: false, isDeleteAgenciaModalOpen: false,
  // modelos de formulario
  formAgencia: { id: null, nombre: '', horario: '', direccion_id: '' },
  agenciaToDelete: null,
  // builder de horario
  dias: [
    { key: 'Lun', sel: false }, { key: 'Mar', sel: false }, { key: 'Mié', sel: false },
    { key: 'Jue', sel: false }, { key: 'Vie', sel: false }, { key: 'Sáb', sel: false }, { key: 'Dom', sel: false }
  ],
  horaInicio: '08:00', horaFin: '17:00',
  composeHorario() {
    const seleccionados = this.dias.filter(d => d.sel).map(d => d.key);
    if (!seleccionados.length || !this.horaInicio || !this.horaFin) {
      this.formAgencia.horario = '';
      return;
    }
    this.formAgencia.horario = `${this.compactarDias(seleccionados)} ${this.horaInicio}-${this.horaFin}`;
  },
  compactarDias(dias) {
    const orden = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
    const idx = dias.map(d => orden.indexOf(d)).sort((a,b)=>a-b);
    // agrupar consecutivos
    const grupos=[]; let ini=idx[0], ant=idx[0];
    for(let i=1;i<idx.length;i++){ if(idx[i]===ant+1){ ant=idx[i]; } else { grupos.push([ini,ant]); ini=ant=idx[i]; } }
    if(idx.length) grupos.push([ini,ant]);
    return grupos.map(([a,b])=> a===b? orden[a] : `${orden[a]}-${orden[b]}`).join(', ');
  },
  parseHorario(texto) {
    if (!texto) return [];
    const orden = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
    const expandRangoDias = (a,b) => {
      const ia = orden.indexOf(a); const ib = orden.indexOf(b);
      if (ia === -1 || ib === -1) return [];
      const res=[]; for(let i=ia;i<=ib;i++) res.push(orden[i]); return res;
    };
  const rangos = [];
    const partes = String(texto).split(';').map(s=>s.trim()).filter(Boolean);
    for (const seg of partes) {
      const lastSpace = seg.lastIndexOf(' ');
      if (lastSpace === -1) continue;
      const diasPart = seg.substring(0,lastSpace).trim();
      const timePart = seg.substring(lastSpace+1).trim();
      const [inicio, fin] = timePart.split('-');
      if (!inicio || !fin) continue;
      let dias=[];
      diasPart.split(',').map(s=>s.trim()).forEach(tok=>{
        if (tok.includes('-')){
          const [a,b] = tok.split('-').map(s=>s.trim());
          dias = dias.concat(expandRangoDias(a,b));
        } else if (orden.includes(tok)) {
          dias.push(tok);
        }
      });
      if (dias.length) rangos.push({ dias, inicio, fin });
    }
    return rangos;
  },
  cargarRangosDesdeTexto(){
    const r = this.parseHorario(this.formAgencia.horario);
    if (r.length){
      const orden = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
      // Tomamos el primer rango como preview
      const primero = r[0];
      this.dias.forEach(d => d.sel = primero.dias.includes(d.key));
      this.horaInicio = primero.inicio; this.horaFin = primero.fin;
    } else {
      this.dias.forEach(d => d.sel = false); this.horaInicio='08:00'; this.horaFin='17:00';
    }
    this.composeHorario();
  },
  // métodos
  async fetch() { await window.agenciasApiHandlers.fetchAgencias(this); },
  async fetchDirecciones() { await window.paisesApiHandlers.fetchDirecciones(this); },
}"
x-init="fetch(); fetchDirecciones(); $watch('searchAgencia', () => fetch()); $watch('ciudadFiltro', () => fetch()); $watch('ordenarPor', () => fetch());">
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
      <button @click="formAgencia = { id:null, nombre: '', horario: '', direccion_id: '' }; dias.forEach(d=>d.sel=false); horaInicio='08:00'; horaFin='17:00'; composeHorario(); isAgenciaModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nueva agencia</button>
      <a href="/admin/reportes-header?modulo=Agencias&fecha={{ now()->format('d-M-Y') }}" target="_blank"
         class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
        <i class="fas fa-file-alt"></i> Generar Reporte
      </a>
    </x-slot>

    <x-slot name="table">
      <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
        <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
          <tr class="border-0">
            <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 first:rounded-tl-lg border-0">Nombre</th>
            <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Horario</th>
            <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Dirección</th>
            <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Ciudad</th>
            <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Departamento</th>
            <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">País</th>
            <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 last:rounded-tr-lg border-0">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <template x-if="loading">
            <tr>
              <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular">
                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando agencias...
              </td>
            </tr>
          </template>
          <template x-if="!loading && agencias.length === 0">
            <tr>
              <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular">
                No hay agencias registradas
              </td>
            </tr>
          </template>
          <template x-if="!loading && agencias.length > 0">
            <template x-for="(ag, index) in agencias" :key="ag.id_agencias_pk">
              <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                  :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === agencias.length - 1 }">
                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="ag.nombre_agencia"></td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="ag.horario_agencia"></td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="(ag.direccion && (ag.direccion.direccion_completa || [ag.direccion.calle, ag.direccion.numero, ag.direccion.colonia].filter(Boolean).join(' '))) || '-' "></td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="ag.direccion?.ciudad?.nombre_ciudad || '-' "></td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="ag.direccion?.ciudad?.departamento?.nombre_departamento || '-' "></td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="ag.direccion?.ciudad?.departamento?.pais?.nombre_pais || '-' "></td>
                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === agencias.length - 1 }">
                  <a href="#" @click.prevent="formAgencia = { id: ag.id_agencias_pk, nombre: ag.nombre_agencia, horario: ag.horario_agencia, direccion_id: ag.id_direccion_fk }; cargarRangosDesdeTexto(); isAgenciaModalOpen = true" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                  <a href="#" @click.prevent="isDeleteAgenciaModalOpen = true; agenciaToDelete = { id: ag.id_agencias_pk, nombre: ag.nombre_agencia }" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            </template>
          </template>
        </tbody>
      </table>
    </x-slot>

    <x-slot name="cards">
      <template x-if="loading">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
          <i class="fas fa-spinner fa-spin mr-2"></i> Cargando agencias...
        </div>
      </template>
      <template x-if="!loading && agencias.length === 0">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
          No hay agencias registradas
        </div>
      </template>
      <template x-if="!loading && agencias.length > 0">
        <template x-for="ag in agencias" :key="ag.id_agencias_pk">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="ag.nombre_agencia"></h3>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Horario: ' + ag.horario_agencia"></p>
            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Dirección: ' + ((ag.direccion && (ag.direccion.direccion_completa || [ag.direccion.calle, ag.direccion.numero, ag.direccion.colonia].filter(Boolean).join(' '))) || 'No especificada')"></p>
            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Ciudad: ' + (ag.direccion?.ciudad?.nombre_ciudad || 'No especificada')"></p>
            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Departamento: ' + (ag.direccion?.ciudad?.departamento?.nombre_departamento || 'No especificado')"></p>
            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'País: ' + (ag.direccion?.ciudad?.departamento?.pais?.nombre_pais || 'No especificado')"></p>
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
              <button @click.prevent="formAgencia = { id: ag.id_agencias_pk, nombre: ag.nombre_agencia, horario: ag.horario_agencia, direccion_id: ag.id_direccion_fk }; cargarRangosDesdeTexto(); isAgenciaModalOpen = true" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                <i class="fas fa-edit"></i> Editar
              </button>
              <button @click.prevent="isDeleteAgenciaModalOpen = true; agenciaToDelete = { id: ag.id_agencias_pk, nombre: ag.nombre_agencia }" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                <i class="fas fa-trash"></i> Eliminar
              </button>
            </div>
          </div>
        </template>
      </template>
    </x-slot>
  </x-responsive-table>

    <!-- Modal Nueva Agencia -->
    <x-admin.form-modal class="nunito-bold"
      modalName="isAgenciaModalOpen" 
      title="Agencia" 
      submitLabel="Guardar Agencia"
      maxWidth="max-w-3xl"
      formId="form-agencia"
      noScroll="true">
      <div x-effect="composeHorario()"></div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="nombre_agencia" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Nombre de la agencia</label>
          <input type="text" id="nombre_agencia" name="nombre_agencia" x-model="formAgencia.nombre" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white  nunito-regular px-2" placeholder="Ej. Agencia Centro">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold mb-2">Horario</label>
          <div class="space-y-3">
            <div>
              <span class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Días:</span>
              <div class="flex flex-wrap items-center gap-2">
                <template x-for="d in dias" :key="d.key">
                  <label class="inline-flex items-center gap-1 text-sm">
                    <input type="checkbox" x-model="d.sel" @change="composeHorario()" class="rounded text-blue-600 focus:ring-blue-500"> <span x-text="d.key"></span>
                  </label>
                </template>
              </div>
              <div class="flex flex-wrap gap-1 mt-2">
                <button type="button" class="text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-600 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500" @click="dias.forEach((d,i)=> d.sel = (i<5)); composeHorario()">Lun–Vie</button>
                <button type="button" class="text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-600 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500" @click="dias.forEach(d=> d.sel = true); composeHorario()">Todos</button>
                <button type="button" class="text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-600 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500" @click="dias.forEach(d=> d.sel = false); composeHorario()">Ninguno</button>
              </div>
            </div>
            <div>
              <span class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Hora:</span>
              <div class="flex items-center gap-2">
                <input type="time" x-model="horaInicio" @change="composeHorario()" class="rounded border border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-2 py-1 text-sm">
                <span class="text-gray-500">–</span>
                <input type="time" x-model="horaFin" @change="composeHorario()" class="rounded border border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-2 py-1 text-sm">
              </div>
            </div>
          </div>
        </div>
        <div class="md:col-span-2">
          <label for="direccion_agencia" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Dirección</label>
          <select id="direccion_agencia" name="direccion_agencia" x-model.number="formAgencia.direccion_id" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white  nunito-regular px-2">
            <option value="">Seleccione una dirección</option>
            <template x-for="d in direcciones" :key="d.id_direccion_pk">
              <option :value="d.id_direccion_pk" x-text="(d.direccion_completa || [d.calle, d.numero, d.colonia].filter(Boolean).join(' ')) + (d.ciudad ? ' - ' + d.ciudad.nombre_ciudad : '')"></option>
            </template>
          </select>
        </div>
      </div>
    </x-admin.form-modal>

    <!-- Modal Confirmar Eliminación Agencia -->
    <x-admin.confirmation-modal class="nunito-bold"
      modalName="isDeleteAgenciaModalOpen"
      itemToDelete="agenciaToDelete"
      message="¿Estás seguro de que quieres eliminar la agencia?"
    />

    <!-- Listeners en el componente para evitar duplicados y mantener el contexto correcto -->
    <div @modal-submit.window="if($event.detail.formId==='form-agencia'){ formAgencia && formAgencia.id ? window.agenciasApiHandlers.updateAgencia($data) : window.agenciasApiHandlers.createAgencia($data) }"
         @confirm-delete.window="if(agenciaToDelete && agenciaToDelete.id){ window.agenciasApiHandlers.deleteAgencia($data) }"></div>
  </div>
</div>