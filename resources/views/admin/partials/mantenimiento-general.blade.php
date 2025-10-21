<div class="container mx-auto py-8 dark:bg-gray-900 min-h-screen" x-data="settingsState()" x-init="init()">
    <h1 class="text-2xl font-bold mb-6 nunito-bold text-gray-800 dark:text-white">Personalización del Sistema</h1>

    <div class="flex flex-wrap border-b dark:border-gray-700 mb-6 gap-2 sm:gap-4">
        <button @click="tab = 'personalizacion'; localStorage.setItem('mantenimientoTab', 'personalizacion')"
            :class="tab === 'personalizacion' ? 'text-blue-600 border-b-2 border-blue-600 dark:text-blue-400 dark:border-blue-400' : 'text-gray-700 dark:text-gray-300'"
            class="px-4 py-2 font-semibold focus:outline-none nunito-regular w-full sm:w-auto text-center">Personalización</button>
        <button @click="tab = 'parametros'; localStorage.setItem('mantenimientoTab', 'parametros')"
            :class="tab === 'parametros' ? 'text-blue-600 border-b-2 border-blue-600 dark:text-blue-400 dark:border-blue-400' : 'text-gray-700 dark:text-gray-300'"
            class="px-4 py-2 font-semibold focus:outline-none nunito-regular w-full sm:w-auto text-center">Parámetros</button>
    </div>

    <!-- TAB Personalización -->
    <div x-show="tab === 'personalizacion'" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4 nunito-bold text-gray-800 dark:text-white">Apariencia e Identidad</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-4 nunito-bold text-gray-700 dark:text-gray-300">Logo del
                    sistema</label>
                <img :src="logoUrl" alt="Logo actual" class="mb-4 max-w-full object-contain"
                    :style="'height:' + logoHeight + 'px; width:auto'">
                <input type="file" @change="onLogoSelected($event)" accept="image/*"
                    class="block w-full max-w-full mb-2 nunito-regular text-sm
                    file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                    dark:file:bg-gray-700 dark:file:text-gray-200 dark:hover:file:bg-gray-600
                    bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-gray-700 rounded-md py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Altura del logo
                    (px)</label>
                <input type="number" min="24" max="256" x-model.number="logoHeight"
                    class="border rounded px-3 py-2 w-full sm:w-32 nunito-regular 
                    bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 nunito-regular">Se aplica globalmente (header,
                    reportes, login).</p>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Nombre del
                    sistema</label>
                <input type="text" x-model="nombreSistema"
                    class="border rounded px-3 py-2 w-full nunito-regular 
                    bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Nombre del sistema">
            </div>
        </div>
        <div class="mt-6 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
            <span x-show="savedMessagePersonalizacion" x-text="savedMessagePersonalizacion"
                class="text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900 px-3 py-1 rounded mr-3 text-sm"></span>
            <button @click="guardarPersonalizacion()" type="button"
                class="px-4 py-2 bg-green-600 dark:bg-green-700 text-white rounded hover:bg-green-700 dark:hover:bg-green-800 transition-colors nunito-regular text-sm">
                Guardar
            </button>
        </div>
    </div>

    <!-- TAB Parámetros -->
    <div x-show="tab === 'parametros'" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4 nunito-bold text-gray-800 dark:text-white">Parámetros Generales</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Zona horaria</label>
                <select
                    class="border rounded px-3 py-2 w-full nunito-regular 
                    bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    x-model="timezone">
                    <option class="nunito-regular" value="America/Tegucigalpa">America/Tegucigalpa</option>
                    <option class="nunito-regular" value="America/Mexico_City">America/Mexico_City</option>
                    <option class="nunito-regular" value="UTC">UTC</option>
                </select>
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Formato de
                    fecha</label>
                <select
                    class="border rounded px-3 py-2 w-full nunito-regular 
                    bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    x-model="dateFormat">
                    <option class="nunito-regular" value="d/m/Y">dd/mm/yyyy</option>
                    <option class="nunito-regular" value="m/d/Y">mm/dd/yyyy</option>
                    <option class="nunito-regular" value="Y-m-d">yyyy-mm-dd</option>
                </select>
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Límite de
                    sesiones</label>
                <input type="number" min="1" max="10" x-model.number="sessionsLimit"
                    class="border rounded px-3 py-2 w-full nunito-regular 
                    bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Requiere verificación
                    de correo</label>
                <div class="flex items-center gap-2">
                    <input id="req-email-verif" type="checkbox" x-model="requireEmailVerification"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-700 rounded">
                    <label for="req-email-verif" class="text-sm text-gray-700 dark:text-gray-300 nunito-regular">Activar
                        verificación obligatoria por correo</label>
                </div>
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Recuperación de
                    contraseña: enfriamiento (minutos)</label>
                <input type="number" min="0" max="120" x-model.number="passwordResetCooldown"
                    class="border rounded px-3 py-2 w-full nunito-regular 
                    bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 nunito-regular">Tiempo mínimo entre solicitudes
                    de reset desde el mismo usuario.</p>
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Recuperación de
                    contraseña: expira en (minutos)</label>
                <input type="number" min="5" max="1440" x-model.number="passwordResetExpire"
                    class="border rounded px-3 py-2 w-full nunito-regular 
                    bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 nunito-regular">Validez del token de
                    recuperación.</p>
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Recuperación de
                    contraseña: máximo por día</label>
                <input type="number" min="1" max="20" x-model.number="passwordResetMaxPerDay"
                    class="border rounded px-3 py-2 w-full nunito-regular 
                    bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Formato DNI</label>
                <input type="text" x-model="dniFormat"
                    class="border rounded px-3 py-2 w-full nunito-regular 
                    bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="0000-0000-00000">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 nunito-regular">Admite solo dígitos (13) o
                    máscaras con guiones, ej. 0000-0000-00000.</p>
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Intentos de inicio de
                    sesion permitidos antes de
                    bloqueo</label>
                <input type="number" min="1" max="10" x-model.number="adminIntentos"
                    class="border rounded px-3 py-2 w-full nunito-regular 
                    bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Estructura de
                    correo</label>
                <input type="email" x-model="adminCorreo"
                    class="border rounded px-3 py-2 w-full nunito-regular 
                    bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="correo@dominio.com">
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Estructura de
                    usuario</label>
                <input type="text" x-model="adminUsuario"
                    class="border rounded px-3 py-2 w-full nunito-regular 
                    bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="USUARIO">
            </div>
            <div>
                <label class="block font-medium mb-1 nunito-bold text-gray-700 dark:text-gray-300">Estructura de
                    contraseña</label>
                <input type="text" x-model="adminPassword"
                    class="border rounded px-3 py-2 w-full nunito-regular 
                    bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="PASSWORD">
            </div>
        </div>
        <div class="mt-6 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
            <span x-show="savedMessageParametros" x-text="savedMessageParametros"
                class="text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900 px-3 py-1 rounded mr-3 text-sm"></span>
            <button @click="guardarParametros()" type="button"
                class="px-4 py-2 bg-green-600 dark:bg-green-700 text-white rounded hover:bg-green-700 dark:hover:bg-green-800 transition-colors nunito-regular text-sm">
                Guardar
            </button>
        </div>
    </div>
</div>

<!-- Inline x-data used (no external script execution dependency) -->