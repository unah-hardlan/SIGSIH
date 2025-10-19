<div class="max-w-4xl mx-auto py-8 dark:bg-gray-900 min-h-screen" x-data="{
        tab: localStorage.getItem('dbTab') || 'respaldo',
    showModal: false,
    modalMsg: '',
    openModal(msg) { this.modalMsg = msg; this.showModal = true; document.documentElement.classList.add('overflow-hidden'); },
    closeModal() { this.showModal = false; document.documentElement.classList.remove('overflow-hidden'); },
        estadoConexion: 'inicial',
       
        // Ruta sugerida por defecto (puedes cambiarla antes de respaldar)
        path: '',
    isBackingUp: false,
        backupMsg: '',
        downloadUrl: '',
    driver: 'mysql',
        respaldoExitoso: false,
        mensajeRespaldo: '',
    confirmPassword: '',
    lastBackupAt: null,
        init(){
            // Construir nombre con fecha-hora: YYYYMMDD-HHMMSS
            const pad = n => String(n).padStart(2,'0');
            const d = new Date();
            const ts = `${d.getFullYear()}${pad(d.getMonth()+1)}${pad(d.getDate())}-${pad(d.getHours())}${pad(d.getMinutes())}${pad(d.getSeconds())}`;
            if(!this.path){ this.path = 'C\\backups\\backup-' + ts + '.sql'; }
        },
        async doBackup(){
            try{
                this.isBackingUp = true; this.backupMsg='';
                // Para seguridad, usamos la conexión configurada en Laravel; estos campos son informativos
                const body = { path: this.path, confirm_password: this.confirmPassword };
                const r = await fetch('/api/db/backup', { method: 'POST', headers: { 'Content-Type':'application/json','Accept':'application/json' }, credentials:'include', body: JSON.stringify(body) });
                const data = await r.json().catch(()=>({}));
                if(!r.ok){ throw new Error(data.message || data.error || 'Fallo realizando respaldo'); }
                this.backupMsg = `Respaldo listo: ${data.path || ''}`;
                this.respaldoExitoso = true;
                this.mensajeRespaldo = 'Respaldo exitoso';
                this.lastBackupAt = new Date();
                if (data.download_url) { this.downloadUrl = data.download_url; }
            }catch(e){
                this.backupMsg = e.message || 'Error';
                this.respaldoExitoso = false;
                this.mensajeRespaldo = 'Error al respaldar';
            }
            finally{ this.isBackingUp = false; }
        }
     }">

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-0 overflow-hidden mb-6">
        <!-- Header con icono y etiqueta -->
        <div class="px-6 pt-6 pb-4 flex items-center gap-3">
            <div
                class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow">
                <!-- Icono de base de datos -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3c4.97 0 9 1.567 9 3.5S16.97 10 12 10 3 8.433 3 6.5 7.03 3 12 3Zm9 7.5c0 1.933-4.03 3.5-9 3.5s-9-1.567-9-3.5M21 14.5c0 1.933-4.03 3.5-9 3.5s-9-1.567-9-3.5M21 18.5C21 20.433 16.97 22 12 22S3 20.433 3 18.5" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white nunito-bold">Gestión de Base de Datos</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular">Respaldo en formato .sql con rutinas
                    y eventos</p>
            </div>
            <span
                class="ml-auto inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200">
                MySQL
            </span>
        </div>

        <!-- Cuerpo con CTA principal -->
        <div class="px-6 pb-6">
            <div
                class="rounded-lg p-5 bg-gradient-to-r from-blue-50/60 to-indigo-50/60 dark:from-blue-900/10 dark:to-indigo-900/10 border border-gray-200/60 dark:border-gray-700/50">
                <div class="flex flex-col gap-4">
                    <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed nunito-regular">
                        Genera un respaldo lógico antes de cambios importantes. El archivo se guarda en el servidor y
                        podrás descargarlo al finalizar.
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Botón principal -->
                        <button @click="openModal('¿Deseas confirmar el respaldo de la base de datos?')"
                            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-semibold transition nunito-regular text-sm disabled:opacity-60 disabled:cursor-not-allowed"
                            :disabled="isBackingUp">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M3 3a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-.293.707l-6 6a1 1 0 01-1.414 0l-6-6A1 1 0 013 9V3zm4 2a1 1 0 100 2h6a1 1 0 100-2H7z" />
                            </svg>
                            <span x-show="!isBackingUp">Respaldar ahora</span>
                            <span x-show="isBackingUp" class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                Procesando...
                            </span>
                        </button>

                        <!-- Estado pequeño -->
                        <template x-if="mensajeRespaldo">
                            <span class="text-sm px-2.5 py-1 rounded-full"
                                :class="respaldoExitoso ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-200' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200'"
                                x-text="mensajeRespaldo"></span>
                        </template>

                        <!-- Descargar -->
                        <template x-if="downloadUrl">
                            <button @click="window.open(downloadUrl,'_blank')"
                                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded nunito-regular text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M3 14a2 2 0 012-2h2v2H5v2h10v-2h-2v-2h2a2 2 0 012 2v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3z" />
                                    <path d="M7 10V3h6v7h2l-5 5-5-5h2z" />
                                </svg>
                                Descargar
                            </button>
                        </template>
                    </div>

                    <!-- Checklist sutil para ocupar espacio -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs text-gray-600 dark:text-gray-400">
                        <div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-7.5 7.5a1 1 0 01-1.414 0l-3-3A1 1 0 015.293 9.793L8 12.5l6.793-6.793a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>Incluye rutinas y eventos</div>
                        <div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-7.5 7.5a1 1 0 01-1.414 0l-3-3A1 1 0 015.293 9.793L8 12.5l6.793-6.793a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>Transacción única (sin locks)</div>
                        <div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-7.5 7.5a1 1 0 01-1.414 0l-3-3A1 1 0 015.293 9.793L8 12.5l6.793-6.793a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>Descarga al finalizar</div>
                    </div>

                    <!-- Meta post respaldo -->
                    <template x-if="lastBackupAt">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Último respaldo: <span
                                x-text="new Intl.DateTimeFormat('es-ES', { dateStyle: 'medium', timeStyle: 'short' }).format(lastBackupAt)"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL -->
    <div x-show="showModal" x-cloak x-transition.opacity @keydown.window.escape="closeModal()"
        class="fixed inset-0 z-50 flex items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-[4px]" @click="closeModal()"></div>
        <!-- Panel -->
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-md w-full">
            <div class="text-gray-800 dark:text-white text-lg font-semibold mb-4 nunito-bold" x-text="modalMsg"></div>
            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2 nunito-regular">Confirma la contraseña de
                la base de datos (.env)</label>
            <input type="password" x-model="confirmPassword"
                class="w-full border rounded px-3 py-2 mb-4 dark:bg-gray-900 dark:text-white dark:border-gray-700"
                placeholder="Contraseña" />
            <div class="flex justify-end gap-2 mt-4">
                <button @click="closeModal()"
                    class="bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 rounded nunito-regular">Cancelar</button>
                <button @click="closeModal(); doBackup()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded nunito-regular">Aceptar</button>
            </div>
        </div>
    </div>
</div>