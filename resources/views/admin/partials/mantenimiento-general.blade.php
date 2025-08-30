{{-- resources/views/admin/partials/mantenimiento-general.blade.php --}}

<div class="container mx-auto py-8" x-data="{
    tab: localStorage.getItem('mantenimientoTab') || 'personalizacion',
    logoUrl: '/images/logo.png',
    tema: 'claro',
    nombreSistema: 'HARDLAN',
    colorPrimario: '#0056b3',
    savedMessagePersonalizacion: '',
    savedMessageParametros: '',

    guardarPersonalizacion() {
        // Aquí puedes reemplazar por una llamada fetch() al backend
        this.savedMessagePersonalizacion = 'Personalización guardada correctamente';
        setTimeout(() => this.savedMessagePersonalizacion = '', 2500);
        console.log('Guardar personalización', { nombreSistema: this.nombreSistema, tema: this.tema });
    },

    guardarParametros() {
        // Aquí puedes reemplazar por una llamada fetch() al backend
        this.savedMessageParametros = 'Parámetros guardados correctamente';
        setTimeout(() => this.savedMessageParametros = '', 2500);
        console.log('Guardar parámetros');
    }
}">
    <h1 class="text-2xl font-bold mb-6 nunito-bold">Personalización del Sistema</h1>

    <div class="flex border-b mb-6 gap-4">
        <button @click="tab = 'personalizacion'; localStorage.setItem('mantenimientoTab', 'personalizacion')"
            :class="tab === 'personalizacion' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700'"
            class="px-4 py-2 font-semibold focus:outline-none nunito-regular">Personalización</button>
        <button @click="tab = 'parametros'; localStorage.setItem('mantenimientoTab', 'parametros')"
            :class="tab === 'parametros' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700'"
            class="px-4 py-2 font-semibold focus:outline-none nunito-regular">Parámetros</button>
    </div>

    <!-- TAB Personalización -->
    <div x-show="tab === 'personalizacion'" class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4 nunito-bold">Apariencia e Identidad</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-1 nunito-bold">Logo del sistema</label>
                <img :src="logoUrl" alt="Logo actual" class="h-16 mb-2">
                <input type="file" class="block mb-2 nunito-regular">
            </div>

        </div>
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-1 nunito-bold">Nombre del sistema</label>
                <input type="text" x-model="nombreSistema" class="border rounded px-3 py-2 w-full nunito-regular">
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end">
            <button @click="guardarPersonalizacion()" type="button" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors nunito-regular text-sm">
                Guardar
            </button>
        </div>
    </div>

    <!-- TAB Parámetros -->
    <div x-show="tab === 'parametros'" class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4 nunito-bold">Parámetros Generales</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-1 nunito-bold">Zona horaria</label>
                <select class="border rounded px-3 py-2 w-full nunito-regular">
                    <option class="nunito-regular">America/Tegucigalpa</option>
                    <option class="nunito-regular">America/Mexico_City</option>
                    <option class="nunito-regular">UTC</option>
                </select>
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold">Formato de fecha</label>
                <select class="border rounded px-3 py-2 w-full nunito-regular">
                    <option class="nunito-regular" value="d/m/Y">dd/mm/yyyy</option>
                    <option class="nunito-regular" value="m/d/Y">mm/dd/yyyy</option>
                    <option class="nunito-regular" value="Y-m-d">yyyy-mm-dd</option>
                </select>
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold">Límite de sesiones</label>
                <input type="number" min="1" max="5" value="2" class="border rounded px-3 py-2 w-full nunito-regular">
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end">
            <button @click="guardarParametros()" type="button" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors nunito-regular">
                Guardar
            </button>
        </div>
    </div>
</div>
