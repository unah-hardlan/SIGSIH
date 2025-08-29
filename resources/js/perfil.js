// resources/js/perfil.js
function perfilPage(){
    return {
        success: false,
        saving: false,
        hasChanges: false,
        originalForm: {},
        originalAvatar: '',
        passwordForm: {
            current_password: '',
            password: '',
            password_confirmation: ''
        },
        changingPassword: false,
        passwordSuccess: false,
        form: {
            primer_nombre: '',
            segundo_nombre: '',
            primer_apellido: '',
            segundo_apellido: '',
            dni: '',
            cargo: '',
            id_tipo_persona_fk: '',
            id_genero_fk: '',
            id_perfil_fk: '',
        },
        email: '',
        displayName: 'Mi Perfil',
        cargo: '',
        avatarUrl: '',
        personaAvatar: '',
        removing: false,

        init(){
            this.$nextTick(() => {
                this.initializeProfileData();
            });
        },

        get displayNameComputed(){
            const n1 = (this.form?.primer_nombre || '').trim();
            const a1 = (this.form?.primer_apellido || '').trim();
            const fallback = (this.displayName || '').trim() || 'Mi Perfil';
            const name = [n1, a1].filter(Boolean).join(' ').trim();
            return name || fallback;
        },

        initializeProfileData(){
            if (!window.Alpine || !window.Alpine.store) {
                setTimeout(() => this.initializeProfileData(), 100);
                return;
            }
            try {
                const store = Alpine.store('perfil');
                if (store?.user) {
                    this.displayName = store.persona?.primer_nombre ? (store.persona.primer_nombre + ' ' + (store.persona.primer_apellido || '')) : (store.user.nombre_usuario || store.user.usuario || 'Mi Perfil');
                    this.email = store.user.correo_electronico || '';
                }
                if (store?.persona) {
                    const p = store.persona;
                    this.form = {
                        primer_nombre: p.primer_nombre || '',
                        segundo_nombre: p.segundo_nombre || '',
                        primer_apellido: p.primer_apellido || '',
                        segundo_apellido: p.segundo_apellido || '',
                        dni: p.dni || '',
                        cargo: p.cargo || '',
                        id_tipo_persona_fk: p.id_tipo_persona_fk || '',
                        id_genero_fk: p.id_genero_fk || '',
                        id_perfil_fk: p.id_perfil_fk || '',
                    };
                    this.personaAvatar = p.avatar_path ? (p.avatar_path.startsWith('http') ? p.avatar_path : (window.location.origin + '/storage/' + p.avatar_path)) : '';
                } else {
                    const cachedPersona = JSON.parse(localStorage.getItem('authPersona') || 'null');
                    if (cachedPersona) {
                        this.form.primer_nombre = cachedPersona.primer_nombre || '';
                        this.form.segundo_nombre = cachedPersona.segundo_nombre || '';
                        this.form.primer_apellido = cachedPersona.primer_apellido || '';
                        this.form.segundo_apellido = cachedPersona.segundo_apellido || '';
                        this.form.dni = cachedPersona.dni || '';
                        this.form.cargo = cachedPersona.cargo || '';
                        this.form.id_tipo_persona_fk = cachedPersona.id_tipo_persona_fk || '';
                        this.form.id_genero_fk = cachedPersona.id_genero_fk || '';
                        this.form.id_perfil_fk = cachedPersona.id_perfil_fk || '';
                        this.personaAvatar = cachedPersona.avatar_path ? (cachedPersona.avatar_path.startsWith('http') ? cachedPersona.avatar_path : (window.location.origin + '/storage/' + cachedPersona.avatar_path)) : '';
                    }
                }
            } catch(e) {
                // noop
            }
            this.cargarDatos();
        },

        async cargarDatos(){
            try{
                const token = localStorage.getItem('authToken');
                const res = await fetch('/api/me', { headers: token ? { 'Authorization': `Bearer ${token}` } : {} });
                if(res.ok){
                    const data = await res.json();
                    this.displayName = (data?.persona?.primer_nombre ? (data.persona.primer_nombre + ' ' + (data.persona.primer_apellido || '')) : (data?.usuario?.nombre_usuario || 'Mi Perfil'));
                    this.email = data?.usuario?.correo_electronico || '';
                    if(data?.persona){
                        this.form = {
                            primer_nombre: data.persona.primer_nombre || '',
                            segundo_nombre: data.persona.segundo_nombre || '',
                            primer_apellido: data.persona.primer_apellido || '',
                            segundo_apellido: data.persona.segundo_apellido || '',
                            dni: data.persona.dni || '',
                            cargo: data.persona.cargo || '',
                            id_tipo_persona_fk: data.persona.id_tipo_persona_fk || '',
                            id_genero_fk: data.persona.id_genero_fk || '',
                            id_perfil_fk: data.persona.id_perfil_fk || '',
                        };
                        this.personaAvatar = data.persona.avatar_path ? (data.persona.avatar_path.startsWith('http') ? data.persona.avatar_path : (window.location.origin + '/storage/' + data.persona.avatar_path)) : '';
                        // Baselines
                        this.originalForm = JSON.parse(JSON.stringify(this.form));
                        this.originalAvatar = this.personaAvatar || '';
                        this.checkForChanges();
                    }
                }
            } catch(e){ /* noop */ }
        },

        async guardar(){
            try{
                this.saving = true;
                const token = localStorage.getItem('authToken');
                const res = await fetch('/api/perfil/persona', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                    },
                    body: JSON.stringify(this.form)
                });
                if(!res.ok){
                    const err = await res.json().catch(()=>({message:'Error'}));
                    alert(err.message || 'Error al guardar');
                    return;
                }
                const data = await res.json();
                const store = Alpine.store('perfil');
                store.firstTime = false;
                store.persona = data?.persona || store.persona;
                try { localStorage.setItem('authPersona', JSON.stringify(store.persona)); } catch(_){ }
                this.success = true;
                // reset baselines
                this.originalForm = JSON.parse(JSON.stringify(this.form));
                this.originalAvatar = this.personaAvatar || '';
                this.hasChanges = false;
                setTimeout(() => { this.success = false; }, 2000);
            } catch(e){
                alert('Error al guardar');
            } finally { this.saving = false; }
        },

        async removeAvatar(){
            try{
                if(this.removing) return;
                if (!confirm('¿Está seguro de que desea eliminar la foto de perfil? Esta acción no se puede deshacer.')) {
                    return;
                }
                this.removing = true;
                const token = localStorage.getItem('authToken');
                const res = await fetch('/api/perfil/avatar', {
                    method: 'DELETE',
                    headers: token ? { 'Authorization': `Bearer ${token}` } : {}
                });
                if(res.ok){
                    this.avatarUrl = '';
                    this.personaAvatar = '';
                    const store = Alpine.store('perfil');
                    if (store.persona) {
                        store.persona.avatar_path = null;
                        try { localStorage.setItem('authPersona', JSON.stringify(store.persona)); } catch(_){ }
                    }
                    // baseline update
                    this.originalAvatar = '';
                    this.checkForChanges();
                    this.hasChanges = false;
                }
            } finally { this.removing = false; }
        },

        async onAvatarChange(ev){
            const file = ev.target.files?.[0];
            if(!file) return;
            if (this.avatarUrl || this.personaAvatar) {
                if (!confirm('¿Desea cambiar la foto de perfil actual? La imagen anterior será reemplazada.')) {
                    ev.target.value = '';
                    return;
                }
            }
            const reader = new FileReader();
            reader.onload = (e) => { this.avatarUrl = e.target.result; };
            reader.readAsDataURL(file);

            const fd = new FormData();
            fd.append('avatar', file);
            const token = localStorage.getItem('authToken');
            const res = await fetch('/api/perfil/avatar', {
                method: 'POST',
                headers: token ? { 'Authorization': `Bearer ${token}` } : {},
                body: fd
            });
            if(res.ok){
                const data = await res.json();
                const finalUrl = data?.url || this.avatarUrl;
                this.avatarUrl = finalUrl;
                const store = Alpine.store('perfil');
                if (!store.persona) store.persona = {};
                if (data?.path) store.persona.avatar_path = data.path;
                try { localStorage.setItem('authPersona', JSON.stringify(store.persona)); } catch(_){ }
                // update personaAvatar and baseline (server accepted)
                this.personaAvatar = finalUrl;
                this.originalAvatar = finalUrl;
                this.checkForChanges();
                this.hasChanges = false;
            }
        },

        checkForChanges(){
            const currentForm = JSON.stringify(this.form);
            const originalForm = JSON.stringify(this.originalForm);
            const hasFormChanges = currentForm !== originalForm;
            let hasAvatarChanges = false;
            if (this.avatarUrl && this.avatarUrl !== (this.personaAvatar || '')) {
                hasAvatarChanges = true;
            }
            if (!this.personaAvatar && (this.originalAvatar || '') !== '') {
                hasAvatarChanges = true;
            }
            this.hasChanges = hasFormChanges || hasAvatarChanges;
        },

        async cambiarPassword(){
            try{
                if (this.passwordForm.password !== this.passwordForm.password_confirmation) {
                    alert('Las contraseñas no coinciden');
                    return;
                }
                if (this.passwordForm.password.length < 8) {
                    alert('La nueva contraseña debe tener al menos 8 caracteres');
                    return;
                }
                this.changingPassword = true;
                const token = localStorage.getItem('authToken');
                const res = await fetch('/api/perfil/password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                    },
                    body: JSON.stringify({
                        current_password: this.passwordForm.current_password,
                        password: this.passwordForm.password,
                        password_confirmation: this.passwordForm.password_confirmation
                    })
                });
                if(!res.ok){
                    const err = await res.json().catch(()=>({message:'Error'}));
                    alert(err.message || 'Error al cambiar la contraseña');
                    return;
                }
                this.passwordForm = { current_password: '', password: '', password_confirmation: '' };
                this.passwordSuccess = true;
                setTimeout(() => { this.passwordSuccess = false; }, 3000);
            } catch(e){
                alert('Error al cambiar la contraseña');
            } finally {
                this.changingPassword = false;
            }
        },

        onFormChange(){ this.checkForChanges(); }
    }
}

// Hacer la función disponible globalmente
window.perfilPage = perfilPage;
