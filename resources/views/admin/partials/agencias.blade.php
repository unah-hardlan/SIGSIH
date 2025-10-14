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
  <div class="w-full">
    <div class="overflow-x-auto w-full">
      <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 mt-6 w-full">
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 pb-4 mb-4 border-b dark:border-gray-600 flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
          <h2 class="text-2xl text-gray-800 dark:text-white nunito-bold">Agencias</h2>
          <div class="flex flex-col sm:flex-row gap-2 flex-1 md:ml-6 nunito-bold">
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
          </div>
          <button @click="formAgencia = { id:null, nombre: '', horario: '', direccion_id: '' }; dias.forEach(d=>d.sel=false); horaInicio='08:00'; horaFin='17:00'; composeHorario(); isAgenciaModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nueva agencia</button>
          <a href="/admin/reportes-header?modulo=Agencias&fecha={{ now()->format('d-M-Y') }}" target="_blank"
             class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
              <i class="fas fa-file-alt"></i> Generar Reporte
          </a>
        </div>
        <div class="overflow-x-auto">
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
              <template x-for="ag in agencias" :key="ag.id_agencias_pk">
                <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular last:border-b-0">
                  <td class="py-2 px-4 first:rounded-bl-lg" x-text="ag.nombre_agencia"></td>
                  <td class="py-2 px-4" x-text="ag.horario_agencia"></td>
                  <td class="py-2 px-4" x-text="(ag.direccion && (ag.direccion.direccion_completa || [ag.direccion.calle, ag.direccion.numero, ag.direccion.colonia].filter(Boolean).join(' '))) || '-' "></td>
                  <td class="py-2 px-4" x-text="ag.direccion?.ciudad?.nombre_ciudad || '-' "></td>
                  <td class="py-2 px-4" x-text="ag.direccion?.ciudad?.departamento?.nombre_departamento || '-' "></td>
                  <td class="py-2 px-4" x-text="ag.direccion?.ciudad?.departamento?.pais?.nombre_pais || '-' "></td>
                  <td class="py-2 px-4 flex gap-2 last:rounded-br-lg">
                    <a href="#" @click.prevent="formAgencia = { id: ag.id_agencias_pk, nombre: ag.nombre_agencia, horario: ag.horario_agencia, direccion_id: ag.id_direccion_fk }; cargarRangosDesdeTexto(); isAgenciaModalOpen = true" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                    <a href="#" @click.prevent="isDeleteAgenciaModalOpen = true; agenciaToDelete = { id: ag.id_agencias_pk, nombre: ag.nombre_agencia }" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                  </td>
                </tr>
              </template>
              <tr x-show="!loading && agencias.length === 0">
                <td class="py-3 px-4 text-center text-gray-500" colspan="7">Sin resultados</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

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
          <input type="text" id="nombre_agencia" name="nombre_agencia" x-model="formAgencia.nombre" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2" placeholder="Ej. Agencia Centro">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Horario</label>
          <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2">
              <span class="text-xs text-gray-500">Días:</span>
              <template x-for="d in dias" :key="d.key">
                <label class="inline-flex items-center gap-1 text-sm">
                  <input type="checkbox" x-model="d.sel" @change="composeHorario()" class="rounded text-blue-600 focus:ring-blue-500"> <span x-text="d.key"></span>
                </label>
              </template>
              <button type="button" class="ml-2 text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-600 dark:text-white" @click="dias.forEach((d,i)=> d.sel = (i<5)); composeHorario()">Lun–Vie</button>
              <button type="button" class="text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-600 dark:text-white" @click="dias.forEach(d=> d.sel = true); composeHorario()">Todos</button>
              <button type="button" class="text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-600 dark:text-white" @click="dias.forEach(d=> d.sel = false); composeHorario()">Ninguno</button>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs text-gray-500">Hora:</span>
              <input type="time" x-model="horaInicio" @change="composeHorario()" class="rounded border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
              <span>–</span>
              <input type="time" x-model="horaFin" @change="composeHorario()" class="rounded border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="text-sm text-gray-700 dark:text-gray-200">
              <span class="font-semibold">Horario:</span> <span x-text="formAgencia.horario || '—'" class="italic"></span>
            </div>
          </div>
        </div>
        <div class="md:col-span-2">
          <label for="direccion_agencia" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Dirección</label>
          <select id="direccion_agencia" name="direccion_agencia" x-model.number="formAgencia.direccion_id" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
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
