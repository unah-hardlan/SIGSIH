{{-- resources/views/admin/partials/mantenimiento-general.blade.php --}}

<div class="container mx-auto py-8 dark:bg-gray-900 min-h-screen"
     x-data='{
        tab: (localStorage.getItem("mantenimientoTab") || "personalizacion"),
        logoUrl: "{{ addslashes($appLogoUrl ?? asset('images/logo.png')) }}",
        nombreSistema: "{{ addslashes($appName ?? 'SIGSIH') }}",
        logoHeight: Number("{{ (int) ($appLogoHeight ?? 96) }}"),
        selectedLogoFile: null,
        savedMessagePersonalizacion: "",
        savedMessageParametros: "",
        async init(){
            try{
                const res = await fetch("/api-web/system-settings", { credentials: "same-origin" });
                if(res.ok){
                    const data = await res.json();
                    this.nombreSistema = data.appName || this.nombreSistema;
                    this.logoUrl = data.logoUrl || this.logoUrl;
                    this.logoHeight = data.logoHeight || this.logoHeight;
                }
            }catch(_){ }
        },
        onLogoSelected(e){
            const file = e.target.files?.[0];
            if(!file) return;
            this.selectedLogoFile = file;
            const reader = new FileReader();
            reader.onload = (ev) => { this.logoUrl = ev.target?.result; };
            reader.readAsDataURL(file);
        },
        async guardarPersonalizacion(){
            const fd = new FormData();
            if(this.nombreSistema) fd.append("app_name", this.nombreSistema);
            if(this.selectedLogoFile) fd.append("logo", this.selectedLogoFile);
            if(this.logoHeight) fd.append("logo_height", String(this.logoHeight));
            try{
                const res = await fetch("/api-web/system-settings", {
                    method: "POST", body: fd, credentials: "same-origin",
                    headers: { "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content") || "" }
                });
                if(!res.ok) throw new Error("bad");
                const data = await res.json();
                this.nombreSistema = data.appName || this.nombreSistema;
                this.logoUrl = data.logoUrl || this.logoUrl;
                this.logoHeight = data.logoHeight || this.logoHeight;
                this.selectedLogoFile = null;
                this.savedMessagePersonalizacion = "Personalización guardada correctamente";
                try{
                    const headerLogo = document.querySelector("header img[alt=\"Logo\"]");
                    if(headerLogo){
                        if(this.logoUrl) headerLogo.src = this.logoUrl;
                        if(this.logoHeight) headerLogo.style.setProperty("--app-logo-max", `${this.logoHeight}px`);
                    }
                    if(this.nombreSistema){ document.title = this.nombreSistema; }
                }catch(_){ }
                setTimeout(() => this.savedMessagePersonalizacion = "", 2500);
            }catch(e){
                this.savedMessagePersonalizacion = "No se pudo guardar";
                setTimeout(() => this.savedMessagePersonalizacion = "", 2500);
            }
        },
        guardarParametros(){
            this.savedMessageParametros = "Parámetros guardados correctamente";
            setTimeout(() => this.savedMessageParametros = "", 2500);
        }
     }' x-init="init()">
    <h1 class="text-2xl font-bold mb-6 nunito-bold text-gray-800 dark:text-white">Personalización del Sistema</h1>

    <div class="flex border-b dark:border-gray-700 mb-6 gap-4">
        <button @click="tab = 'personalizacion'; localStorage.setItem('mantenimientoTab', 'personalizacion')"
            :class="tab === 'personalizacion' ? 'text-blue-600 border-b-2 border-blue-600 dark:text-blue-400 dark:border-blue-400' : 'text-gray-200 dark:text-gray-300'"
            class="px-4 py-2 font-semibold focus:outline-none nunito-regular">Personalización</button>
        <button @click="tab = 'parametros'; localStorage.setItem('mantenimientoTab', 'parametros')"
            :class="tab === 'parametros' ? 'text-blue-600 border-b-2 border-blue-600 dark:text-blue-400 dark:border-blue-400' : 'text-gray-200 dark:text-gray-300'"
            class="px-4 py-2 font-semibold focus:outline-none nunito-regular">Parámetros</button>
    </div>

    <!-- TAB Personalización -->
    <div x-show="tab === 'personalizacion'" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
    <h2 class="text-lg font-semibold mb-4 nunito-bold text-gray-800 dark:text-white">Apariencia e Identidad</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-4 nunito-bold text-gray-700 dark:text-gray-300">Logo del sistema</label>
                <img :src="logoUrl" alt="Logo actual" class="mb-4" :style="'height:' + logoHeight + 'px; width:auto'">
                <input type="file" @change="onLogoSelected($event)" accept="image/*" class="block mb-2 nunito-regular dark:bg-gray-900 dark:text-white dark:border-gray-700">
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Altura del logo (px)</label>
                <input type="number" min="24" max="256" x-model.number="logoHeight" class="border rounded px-3 py-2 w-32 nunito-regular dark:bg-gray-900 dark:text-white dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 nunito-regular">Se aplica globalmente (header, reportes, login).</p>
            </div>

        </div>
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Nombre del sistema</label>
                <input type="text" x-model="nombreSistema" class="border rounded px-3 py-2 w-full nunito-regular dark:bg-gray-900 dark:text-white dark:border-gray-700" placeholder="Nombre del sistema">
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end">
            <span x-show="savedMessagePersonalizacion" x-text="savedMessagePersonalizacion" class="text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900 px-3 py-1 rounded mr-3 text-sm"></span>
            <button @click="guardarPersonalizacion()" type="button" class="px-4 py-2 bg-green-600 dark:bg-green-700 text-white rounded hover:bg-green-700 dark:hover:bg-green-800 transition-colors nunito-regular text-sm">
                Guardar
            </button>
        </div>
    </div>

    <!-- TAB Parámetros -->
    <div x-show="tab === 'parametros'" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold mb-4 nunito-bold text-gray-800 dark:text-white">Parámetros Generales</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Zona horaria</label>
                <select class="border rounded px-3 py-2 w-full nunito-regular dark:bg-gray-900 dark:text-white dark:border-gray-700">
                    <option class="nunito-regular">America/Tegucigalpa</option>
                    <option class="nunito-regular">America/Mexico_City</option>
                    <option class="nunito-regular">UTC</option>
                </select>
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Formato de fecha</label>
                <select class="border rounded px-3 py-2 w-full nunito-regular dark:bg-gray-900 dark:text-white dark:border-gray-700">
                    <option class="nunito-regular" value="d/m/Y">dd/mm/yyyy</option>
                    <option class="nunito-regular" value="m/d/Y">mm/dd/yyyy</option>
                    <option class="nunito-regular" value="Y-m-d">yyyy-mm-dd</option>
                </select>
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Límite de sesiones</label>
                <input type="number" min="1" max="5" value="2" class="border rounded px-3 py-2 w-full nunito-regular dark:bg-gray-900 dark:text-white dark:border-gray-700">
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end">
            <button @click="guardarParametros()" type="button" class="px-4 py-2 bg-green-600 dark:bg-green-700 text-white rounded hover:bg-green-700 dark:hover:bg-green-800 transition-colors nunito-regular">
                Guardar
            </button>
        </div>
    </div>
</div>

<!-- Inline x-data used (no external script execution dependency) -->
