const hasGestionUsuariosAccess = () => {
    try {
        const main = document.querySelector('main');
        return (main?.dataset?.canGestionUsuarios || '') === '1';
    } catch (_) {
        return false;
    }
};

document.addEventListener('alpine:init', () => {
    Alpine.data('usuariosCrud', () => ({
        isModalOpen:false,isEditUserModalOpen:false,showDeleteModal:false,
        users:[],loading:false,error:'',formError:'',isSubmitting:false,
        pagination:{page:1,per_page:10,last_page:1,total:0},
    search:'',filtroPerfil:'',ordenarPor:'',ordenDirection:'asc',
        debounceTimer:null,
    roles:[],rolesLoading:false,rolesError:'',
    createForm:{usuario:'',nombre_usuario:'',correo_electronico:'',estado_usuario:'ACTIVO',contrasena:'',id_rol_fk:''},
    editForm:{id:null,usuario:'',nombre_usuario:'',correo_electronico:'',estado_usuario:'ACTIVO',contrasena:'',id_rol_fk:null},
        userToEdit:null,userToInactivate:null,
        apiBase:'/api/usuarios',
        canAccess(){ return hasGestionUsuariosAccess(); },
        notify(msg,type='success'){
            const el=document.createElement('div');
            el.textContent=msg;
            el.className=`fixed top-4 right-4 z-50 px-4 py-2 rounded shadow text-sm text-white ${type==='error'?'bg-red-600':'bg-green-600'}`;
            document.body.appendChild(el);
            setTimeout(()=>{ el.classList.add('opacity-0','transition'); },2500);
            setTimeout(()=> el.remove(),3000);
        },
        init(){
            if(!this.canAccess()){
                this.error='No tienes permisos para ver los usuarios.';
                return;
            }
            window.addEventListener('modal-submit', e=>{ if(e.detail?.formId==='formCrear'){ this.createUser(); } if(e.detail?.formId==='formEditar'){ this.updateUser(); } });
            this.$watch('search', () => this.debounceFetch());
            this.$watch('filtroPerfil', () => { this.pagination.page=1; this.fetchUsers(); });
            this.$watch('ordenarPor', (val,old) => {
                if(old===val){ // toggle
                    this.ordenDirection = this.ordenDirection==='asc' ? 'desc' : 'asc';
                } else {
                    this.ordenDirection = 'asc';
                }
                this.fetchUsers();
            });
            this.$watch('showDeleteModal', (val) => { if(!val) this.userToInactivate = null; });
            // Compatibilidad: antes el modal emitía 'inactivar-user'; ahora emite 'confirm-delete'
            window.addEventListener('inactivar-user', () => { if(this.userToInactivate) this.inactivarUser(this.userToInactivate); });
            window.addEventListener('confirm-delete', () => { if(this.userToInactivate) this.inactivarUser(this.userToInactivate); });
            this.fetchUsers();
            this.fetchRoles();
        },
        async fetchRoles(){
            if(!this.canAccess()){
                this.rolesLoading=false; this.roles=[]; this.rolesError='No tienes permisos para listar roles.'; return;
            }
            try{ this.rolesLoading=true; this.rolesError='';
                const r=await fetch('/api/roles?all=1',{headers:this.authHeaders(),credentials:'include'});
                if(r.status===403){ this.rolesError='No tienes permisos para listar roles.'; this.roles=[]; return; }
                if(!r.ok) throw await r.json();
                const data=await r.json();
                this.roles = Array.isArray(data?.data)?data.data:data;
            }catch(e){ this.rolesError = e.error||e.message||'Error roles'; }
            finally{ this.rolesLoading=false; }
        },
        debounceFetch(){ clearTimeout(this.debounceTimer); this.debounceTimer=setTimeout(()=>{ this.pagination.page=1; this.fetchUsers(); },400); },
        getToken(){ const t=localStorage.getItem('token'); if(t) return t; const m=document.cookie.match(/auth_token=([^;]+)/); return m?decodeURIComponent(m[1]):''; },
        authHeaders(){ return { 'Authorization':'Bearer '+this.getToken(),'Content-Type':'application/json','Accept':'application/json' }; },
        async fetchUsers(){
            if(!this.canAccess()){
                this.loading=false; this.error='No tienes permisos para ver los usuarios.'; this.users=[]; return;
            }
            this.loading=true; this.error='';
            const params=new URLSearchParams({per_page:this.pagination.per_page,page:this.pagination.page});
            if(this.search) params.append('q',this.search);
            if(this.filtroPerfil){
                params.append('estado',this.filtroPerfil);
            } else {
                // Mostrar todos los estados cuando no se selecciona filtro explícito
                params.append('all','1');
            }
            if(this.ordenarPor){
                params.append('sort',this.ordenarPor);
                params.append('direction',this.ordenDirection);
            }
            try {
                const r=await fetch(`${this.apiBase}?${params.toString()}`,{headers:this.authHeaders(),credentials:'include'});
                if(r.status===403){ this.error='No tienes permisos para ver los usuarios.'; this.users=[]; return; }
                if(r.status===401){ this.error='Sesión expirada. Inicia sesión.'; this.users=[]; return; }
                if(!r.ok) throw await r.json();
                const data=await r.json();
                this.users=data.data;
                if(data.meta){ Object.assign(this.pagination,{page:data.meta.page,per_page:data.meta.per_page,total:data.meta.total,last_page:data.meta.last_page}); }
            } catch(e){
                this.error=e.error||e.message||'Error';
            } finally { this.loading=false; }
        },
    userRole(u){ return (u && u.rol) ? u.rol : '-'; },
        changePage(p){ if(p>=1 && p<=this.pagination.last_page){ this.pagination.page=p; this.fetchUsers(); } },
    openCreate(){ if(!this.canAccess()||this.isSubmitting)return; this.createForm={usuario:'',nombre_usuario:'',correo_electronico:'',estado_usuario:'ACTIVO',contrasena:'',id_rol_fk:''}; this.formError=''; this.isModalOpen=true; if(!this.roles.length) this.fetchRoles(); },
        async createUser(){ if(!this.canAccess()||this.isSubmitting) return; this.isSubmitting=true; this.formError='';
            if(!this.createForm.id_rol_fk){ this.formError='Debe seleccionar un rol'; this.isSubmitting=false; return; }
            try{ const payload={...this.createForm}; const r=await fetch(this.apiBase,{method:'POST',headers:this.authHeaders(),body:JSON.stringify(payload)}); if(!r.ok) throw await r.json(); const data=await r.json(); this.isModalOpen=false; if(this.pagination.page===1 && !this.ordenarPor){ this.users.unshift(data.data||data); if(this.users.length>this.pagination.per_page){ this.users.pop(); } this.pagination.total+=1; } else { this.fetchUsers(); } this.notify('Usuario creado'); }catch(e){ this.formError=(e.errors && Object.values(e.errors).flat().join('\n'))||e.error||'Error creando'; this.notify(this.formError,'error'); } finally { this.isSubmitting=false; } },
    openEdit(u){ if(!this.canAccess()||this.isSubmitting)return; this.editForm={id:u.id,usuario:u.usuario,nombre_usuario:u.nombre_usuario,correo_electronico:u.correo_electronico,estado_usuario:u.estado_usuario,contrasena:'',id_rol_fk:u.id_rol_fk||null}; this.formError=''; this.userToEdit=u; this.isEditUserModalOpen=true; if(!this.roles.length) this.fetchRoles(); },
    async updateUser(){ if(!this.canAccess()||this.isSubmitting) return; this.isSubmitting=true; this.formError=''; const payload={nombre_usuario:this.editForm.nombre_usuario,correo_electronico:this.editForm.correo_electronico,estado_usuario:this.editForm.estado_usuario}; if(this.editForm.contrasena) payload.contrasena=this.editForm.contrasena; if(this.editForm.id_rol_fk) payload.id_rol_fk=this.editForm.id_rol_fk; try{ const r=await fetch(`${this.apiBase}/${this.editForm.id}`,{method:'PUT',headers:this.authHeaders(),body:JSON.stringify(payload)}); if(!r.ok) throw await r.json(); const data=await r.json(); this.isEditUserModalOpen=false; const idx=this.users.findIndex(x=>x.id===this.editForm.id); if(idx>-1){ this.users[idx]=data.data||data; } if(this.ordenarPor) this.fetchUsers(); this.notify('Usuario actualizado'); }catch(e){ this.formError=(e.errors && Object.values(e.errors).flat().join('\n'))||e.error||'Error actualizando'; this.notify(this.formError,'error'); } finally { this.isSubmitting=false; } },
        openInactivar(user){ if(!this.canAccess()) return; this.userToInactivate=user; this.showDeleteModal=true; },
        openReporte(){
            if(!this.canAccess()) return;
            const params=new URLSearchParams();
            params.append('modulo','usuarios');
            if(this.search) params.append('q',this.search);
            if(this.filtroPerfil) params.append('estado',this.filtroPerfil); else params.append('all','1');
            if(this.ordenarPor){ params.append('sort',this.ordenarPor); params.append('direction',this.ordenDirection); }
            const url=`/admin/reportes-header?${params.toString()}`;
            window.open(url,'_blank');
        },
        async inactivarUser(user){ if(!user || !user.id) return; try{ const resp=await fetch(`${this.apiBase}/${user.id}`,{method:'DELETE',headers:this.authHeaders(),credentials:'include'}); if(!resp.ok){ const data=await resp.json().catch(()=>({})); throw new Error(data.message||'Error al inactivar usuario'); } const idx=this.users.findIndex(x=>x.id===user.id); if(idx>-1){ this.users[idx].estado_usuario='INACTIVO'; if(this.filtroPerfil==='ACTIVO'){ this.users.splice(idx,1); this.pagination.total=Math.max(0,this.pagination.total-1); if(this.users.length < this.pagination.per_page && this.pagination.page < this.pagination.last_page){ this.fetchUsers(); } } } if(this.ordenarPor) this.fetchUsers(); this.notify('Usuario inactivado'); }catch(e){ console.error(e); this.notify(e.message||'Error al inactivar','error'); } },
    }));
});
