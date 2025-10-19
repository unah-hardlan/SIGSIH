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
        init(){
            // Construir nombre con fecha-hora: YYYYMMDD-HHMMSS
            const pad = n => String(n).padStart(2,'0');
            const d = new Date();
            const ts = `${d.getFullYear()}${pad(d.getMonth()+1)}${pad(d.getDate())}-${pad(d.getHours())}${pad(d.getMinutes())}${pad(d.getSeconds())}`;
            if(!this.path){ this.path = 'C\\\\backups\\\\backup-' + ts + '.sql'; }
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
                if (data.download_url) { this.downloadUrl = data.download_url; }
            }catch(e){
                this.backupMsg = e.message || 'Error';
                this.respaldoExitoso = false;
                this.mensajeRespaldo = 'Error al respaldar';
            }
            finally{ this.isBackingUp = false; }
        }
     }">

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2 nunito-bold">Gestión de Base de Datos</h2>

        <div class="flex border-b dark:border-gray-700 mb-4 text-base">
            <span class="pb-2 px-2 border-b-2 border-blue-600 text-blue-600 nunito-regular">Respaldo</span>
        </div>

        <!-- RESPALDO -->
        <div x-show="tab === 'respaldo'" class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2 nunito-bold">Respaldar base de datos
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">




            </div>

            <div class="flex justify-end gap-3 mt-4 items-center">


                <div class="flex items-center gap-3">
                    <button @click="openModal('¿Deseas confirmar el respaldo de la base de datos?')"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold transition nunito-regular text-sm"
                        :disabled="isBackingUp">
                        Respaldar
                    </button>
                    <span class="text-sm" :class="respaldoExitoso ? 'text-green-500' : 'text-red-400'"
                        x-text="mensajeRespaldo">
                    </span>
                    <template x-if="downloadUrl">
                        <button @click="window.open(downloadUrl,'_blank')"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded nunito-regular text-sm">
                            Descargar
                        </button>
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
            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2 nunito-regular">Confirma la contraseña de la base de datos (.env)</label>
            <input type="password" x-model="confirmPassword" class="w-full border rounded px-3 py-2 mb-4 dark:bg-gray-900 dark:text-white dark:border-gray-700" placeholder="Contraseña" />
            <div class="flex justify-end gap-2 mt-4">
                <button @click="closeModal()"
                    class="bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 rounded nunito-regular">Cancelar</button>
                <button @click="closeModal(); doBackup()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded nunito-regular">Aceptar</button>
            </div>
        </div>
    </div>
</div>