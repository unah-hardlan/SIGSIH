document.addEventListener('alpine:init', () => {
    Alpine.data('parametrosCrud', () => ({
        isModalOpen:false,isEditModalOpen:false,showDeleteModal:false,
        parametros:[],loading:false,error:'',formError:'',isSubmitting:false,
        pagination:{page:1,per_page:10,last_page:1,total:0},
        search:'',ordenarPor:'',ordenDirection:'asc',debounceTimer:null,
        createForm:{parametro:'',valor:''},
        editForm:{id:null,parametro:'',valor:''},
        parametroToEdit:null,parametroToDelete:null,
        apiBase:'/api/parametros',
        notify(msg,type='success'){ const el=document.createElement('div'); el.textContent=msg; el.className=`fixed top-4 right-4 z-50 px-4 py-2 rounded shadow text-sm text-white ${type==='error'?'bg-red-600':'bg-green-600'}`; document.body.appendChild(el); setTimeout(()=>{ el.classList.add('opacity-0','transition'); },2500); setTimeout(()=> el.remove(),3000); },
        init(){
            window.addEventListener('modal-submit', e=>{ if(e.detail?.formId==='formCrearParametro'){ this.createParametro(); } if(e.detail?.formId==='formEditarParametro'){ this.updateParametro(); } });
            this.$watch('search', () => this.debounceFetch());
            this.$watch('ordenarPor', (val,old)=>{ if(old===val){ this.ordenDirection=this.ordenDirection==='asc'?'desc':'asc'; } else { this.ordenDirection='asc'; } this.fetchParametros(); });
            window.addEventListener('confirm-delete', ()=>{ if(this.parametroToDelete) this.deleteParametro(this.parametroToDelete); });
            this.fetchParametros();
        },
        debounceFetch(){ clearTimeout(this.debounceTimer); this.debounceTimer=setTimeout(()=>{ this.pagination.page=1; this.fetchParametros(); },400); },
    getToken(){ return null; },
    authHeaders(){ return { 'Content-Type':'application/json','Accept':'application/json' }; },
        async fetchParametros(){ this.loading=true; this.error=''; const params=new URLSearchParams({per_page:this.pagination.per_page,page:this.pagination.page}); if(this.search) params.append('q',this.search); if(this.ordenarPor){ params.append('sort',this.ordenarPor); params.append('direction',this.ordenDirection);} try{ const r=await fetch(`${this.apiBase}?${params.toString()}`,{headers:this.authHeaders(),credentials:'include'}); if(r.status===401){ this.error='Sesión expirada'; this.parametros=[]; return;} if(!r.ok) throw await r.json(); const data=await r.json(); this.parametros=data.data; if(data.meta){ Object.assign(this.pagination,{page:data.meta.page,per_page:data.meta.per_page,total:data.meta.total,last_page:data.meta.last_page}); } }catch(e){ this.error=e.error||e.message||'Error'; } finally{ this.loading=false; } },
        changePage(p){ if(p>=1 && p<=this.pagination.last_page){ this.pagination.page=p; this.fetchParametros(); } },
        openCreate(){ if(this.isSubmitting)return; this.createForm={parametro:'',valor:''}; this.formError=''; this.isModalOpen=true; },
        async createParametro(){ if(this.isSubmitting)return; this.isSubmitting=true; this.formError=''; try{ const r=await fetch(this.apiBase,{method:'POST',headers:this.authHeaders(),body:JSON.stringify(this.createForm)}); if(!r.ok) throw await r.json(); const data=await r.json(); this.isModalOpen=false; if(this.pagination.page===1 && !this.ordenarPor){ this.parametros.unshift(data.data||data); if(this.parametros.length>this.pagination.per_page){ this.parametros.pop(); } this.pagination.total+=1; } else { this.fetchParametros(); } this.notify('Parámetro creado'); }catch(e){ this.formError=(e.errors && Object.values(e.errors).flat().join('\n'))||e.error||'Error creando'; this.notify(this.formError,'error'); } finally{ this.isSubmitting=false; } },
        openEdit(p){ if(this.isSubmitting)return; this.editForm={id:p.id,parametro:p.parametro,valor:p.valor}; this.parametroToEdit=p; this.formError=''; this.isEditModalOpen=true; },
        async updateParametro(){ if(this.isSubmitting)return; this.isSubmitting=true; this.formError=''; const payload={valor:this.editForm.valor}; try{ const r=await fetch(`${this.apiBase}/${this.editForm.id}`,{method:'PUT',headers:this.authHeaders(),body:JSON.stringify(payload)}); if(!r.ok) throw await r.json(); const data=await r.json(); this.isEditModalOpen=false; const idx=this.parametros.findIndex(x=>x.id===this.editForm.id); if(idx>-1){ this.parametros[idx]=data.data||data; } if(this.ordenarPor) this.fetchParametros(); this.notify('Parámetro actualizado'); }catch(e){ this.formError=(e.errors && Object.values(e.errors).flat().join('\n'))||e.error||'Error actualizando'; this.notify(this.formError,'error'); } finally{ this.isSubmitting=false; } },
        openDelete(p){ this.parametroToDelete=p; this.showDeleteModal=true; },
        async deleteParametro(p){
            if(!p||!p.id) return;
            try{
                const resp=await fetch(`${this.apiBase}/${p.id}`,{method:'DELETE',headers:this.authHeaders(),credentials:'include'});
                if(resp.status===404){
                    // Ya no existe: lo removemos localmente y consideramos éxito silencioso
                    const idx404=this.parametros.findIndex(x=>x.id===p.id);
                    if(idx404>-1){ this.parametros.splice(idx404,1); this.pagination.total=Math.max(0,this.pagination.total-1); }
                    this.notify('Parámetro ya inexistente, lista actualizada');
                    return;
                }
                if(!resp.ok){
                    let data={};
                    try{ data=await resp.json(); }catch(_){ }
                    throw new Error(data.message||data.error||'Error al eliminar');
                }
                const idx=this.parametros.findIndex(x=>x.id===p.id);
                if(idx>-1){ this.parametros.splice(idx,1); this.pagination.total=Math.max(0,this.pagination.total-1); }
                this.notify('Parámetro eliminado');
            }catch(e){
                console.error('Delete parametro error:',e);
                this.notify(e.message||'Error','error');
            }
        },
        openReporte(){ const params=new URLSearchParams(); params.append('modulo','parametros'); if(this.search) params.append('q',this.search); if(this.ordenarPor){ params.append('sort',this.ordenarPor); params.append('direction',this.ordenDirection);} const url=`/admin/reportes-header?${params.toString()}`; window.open(url,'_blank'); }
    }));
});
