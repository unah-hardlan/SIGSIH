<div x-data="{
    // Tabla y filtros
    items:[], loading:false, search:'', sort:'id', direction:'asc',
    // Modales
    isItemModalOpen:false, isEditItemModalOpen:false, isDeleteItemModalOpen:false,
    itemToEdit:null, itemToDelete:null,
    // Form
    formItem:{ descripcion:'', precio_unitario:0, cantidad:1, impuesto:0, id_cotizacion_fk:'' },
    errors:{},
    apiHeaders(){ return { 'Content-Type':'application/json', 'Accept':'application/json' }; },
    showToast(msg,type='ok'){ let d=document.createElement('div'); d.className='fixed top-4 right-4 z-50 px-3 py-2 rounded text-sm shadow '+(type==='error'?'bg-red-600 text-white':'bg-green-600 text-white'); d.textContent=msg; document.body.appendChild(d); setTimeout(()=>d.remove(),3000); },
    fmt(n){ if(n==null) return ''; return Number(n).toFixed(2); },
    calcTotalObj(o){ const pu=Number(o.precio_unitario||0); const c=Number(o.cantidad||0); const imp=Number(o.impuesto||0); return +(pu*c+imp).toFixed(2); },
    async fetchItems(){
        this.loading=true;
        try{
            const p=new URLSearchParams();
            if(this.search) p.set('q',this.search);
            p.set('per_page','100');
            const r=await fetch('/api/items-cotizacion?'+p.toString(),{
                headers:{ 'Accept':'application/json' }
            });
            if(!r.ok) throw new Error();
            const j=await r.json();
            const data=j.data||j||[];
            this.items=data.map(it=>({
                id:it.id_item_cotizacion_pk,
                descripcion:it.descripcion,
                precio_unitario:it.precio_unitario,
                cantidad:it.cantidad,
                impuesto:it.impuesto,
                total:it.total,
                id_cotizacion_fk:it.id_cotizacion_fk
            }));
        }catch(e){
            this.showToast('Error cargando items','error');
        } finally {
            this.loading=false;
        }
    },
    openCreate(){ this.formItem={ descripcion:'', precio_unitario:0, cantidad:1, impuesto:0, id_cotizacion_fk:'' }; this.errors={}; this.isItemModalOpen=true; },
    async submitCreate(){ try{ const payload={ ...this.formItem, total:this.calcTotalObj(this.formItem) }; const r=await fetch('/api/items-cotizacion',{ method:'POST', headers:this.apiHeaders(), body:JSON.stringify(payload) }); if(r.status===422){ this.errors=await r.json(); throw new Error('valid'); } if(!r.ok) throw new Error(); this.isItemModalOpen=false; this.fetchItems(); this.showToast('Item creado'); }catch(e){ this.showToast('No se creó','error'); } },
    openEdit(it){ this.itemToEdit={ ...it }; this.formItem={ descripcion:it.descripcion, precio_unitario:it.precio_unitario, cantidad:it.cantidad, impuesto:it.impuesto, id_cotizacion_fk:it.id_cotizacion_fk }; this.isEditItemModalOpen=true; },
    async submitEdit(){ if(!this.itemToEdit) return; try{ const payload={ ...this.formItem, total:this.calcTotalObj(this.formItem) }; const r=await fetch('/api/items-cotizacion/'+this.itemToEdit.id,{ method:'PUT', headers:this.apiHeaders(), body:JSON.stringify(payload) }); if(r.status===422){ this.errors=await r.json(); throw new Error('valid'); } if(!r.ok) throw new Error(); this.isEditItemModalOpen=false; this.fetchItems(); this.showToast('Item actualizado'); }catch(e){ this.showToast('No se actualizó','error'); } },
    openDelete(it){ this.itemToDelete=it; this.isDeleteItemModalOpen=true; },
    async confirmDelete(){ if(!this.itemToDelete) return; try{ const r=await fetch('/api/items-cotizacion/'+this.itemToDelete.id,{ method:'DELETE', headers:this.apiHeaders() }); if(!r.ok) throw new Error(); this.items=this.items.filter(x=>x.id!==this.itemToDelete.id); this.showToast('Item eliminado'); }catch(e){ this.showToast('No se eliminó','error'); } finally{ this.isDeleteItemModalOpen=false; this.itemToDelete=null; } },
    init(){ this.fetchItems(); const deb=(fn,ms=400)=>{ let h; return (...a)=>{ clearTimeout(h); h=setTimeout(()=>fn(...a),ms); }; }; this.$watch('search', deb(()=>this.fetchItems())); }
}">
    <x-admin.tabla-crud class="nunito-bold">
        <x-slot name="titulo">
            <h2 class="text-2xl text-gray-800 dark:text-gray-200 nunito-bold">Items de Cotización</h2>
        </x-slot>
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
            'searchModel' => 'search',
            'filtrosSelect' => [],
            'ordenarOptions' => [
            'id' => 'ID',
            'descripcion' => 'Descripción',
            'precio_unitario' => 'Precio Unit.',
            'cantidad' => 'Cantidad',
            ]
            ])
        </x-slot>
        <x-slot name="boton">
            <button @click="openCreate()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo Item</button>
        </x-slot>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left">ID</th>
                        <th class="py-2 px-4 text-left">Descripción</th>
                        <th class="py-2 px-4 text-right">Precio Unit.</th>
                        <th class="py-2 px-4 text-right">Cantidad</th>
                        <th class="py-2 px-4 text-right">Impuesto</th>
                        <th class="py-2 px-4 text-right">Total</th>
                        <th class="py-2 px-4 text-left">ID Cotización</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <tr>
                            <td colspan="8" class="py-4 px-4 text-center text-gray-600 dark:text-gray-300"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando items...</td>
                        </tr>
                    </template>
                    <template x-if="!loading && items.length===0">
                        <tr>
                            <td colspan="8" class="py-4 px-4 text-center text-gray-600 dark:text-gray-300">No se encontraron items.</td>
                        </tr>
                    </template>
                    <template x-for="it in items" :key="it.id">
                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 nunito-regular">
                            <td class="py-2 px-4" x-text="it.id"></td>
                            <td class="py-2 px-4" x-text="it.descripcion"></td>
                            <td class="py-2 px-4 text-right" x-text="fmt(it.precio_unitario)"></td>
                            <td class="py-2 px-4 text-right" x-text="fmt(it.cantidad)"></td>
                            <td class="py-2 px-4 text-right" x-text="fmt(it.impuesto)"></td>
                            <td class="py-2 px-4 text-right" x-text="fmt(it.total)"></td>
                            <td class="py-2 px-4" x-text="it.id_cotizacion_fk"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click.prevent="openEdit(it)" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click.prevent="openDelete(it)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>

    <!-- Modal Crear Item -->
    <x-admin.form-modal class="nunito-bold" modalName="isItemModalOpen" title="Nuevo Item" submitLabel="Guardar" formId="item-form" maxWidth="max-w-2xl"
        @modal-submit.window="if($event.detail.formId==='item-form'){ submitCreate(); }">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm nunito-bold">Descripción</label>
                <input type="text" x-model="formItem.descripcion" class="mt-1 w-full rounded-md border border-gray-400 p-1" />
            </div>
            <div>
                <label class="block text-sm nunito-bold">ID Cotización</label>
                <input type="number" x-model="formItem.id_cotizacion_fk" class="mt-1 w-full rounded-md border border-gray-400 p-1" />
            </div>
            <div>
                <label class="block text-sm nunito-bold">Precio Unitario</label>
                <input type="number" step="0.01" x-model.number="formItem.precio_unitario" class="mt-1 w-full rounded-md border border-gray-400 p-1" />
            </div>
            <div>
                <label class="block text-sm nunito-bold">Cantidad</label>
                <input type="number" step="0.01" x-model.number="formItem.cantidad" class="mt-1 w-full rounded-md border border-gray-400 p-1" />
            </div>
            <div>
                <label class="block text-sm nunito-bold">Impuesto</label>
                <input type="number" step="0.01" x-model.number="formItem.impuesto" class="mt-1 w-full rounded-md border border-gray-400 p-1" />
            </div>
            <div>
                <label class="block text-sm nunito-bold">Total</label>
                <input type="number" :value="calcTotalObj(formItem)" disabled class="mt-1 w-full rounded-md border border-gray-200 bg-gray-100 p-1" />
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Item -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditItemModalOpen" title="Editar Item" itemToEdit="itemToEdit" formId="item-edit-form" maxWidth="max-w-2xl"
        @modal-submit.window="if($event.detail.formId==='item-edit-form'){ submitEdit(); }">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm nunito-bold">Descripción</label>
                <input type="text" x-model="formItem.descripcion" class="mt-1 w-full rounded-md border border-gray-400 p-1" />
            </div>
            <div>
                <label class="block text-sm nunito-bold">ID Cotización</label>
                <input type="number" x-model="formItem.id_cotizacion_fk" class="mt-1 w-full rounded-md border border-gray-400 p-1" />
            </div>
            <div>
                <label class="block text-sm nunito-bold">Precio Unitario</label>
                <input type="number" step="0.01" x-model.number="formItem.precio_unitario" class="mt-1 w-full rounded-md border border-gray-400 p-1" />
            </div>
            <div>
                <label class="block text-sm nunito-bold">Cantidad</label>
                <input type="number" step="0.01" x-model.number="formItem.cantidad" class="mt-1 w-full rounded-md border border-gray-400 p-1" />
            </div>
            <div>
                <label class="block text-sm nunito-bold">Impuesto</label>
                <input type="number" step="0.01" x-model.number="formItem.impuesto" class="mt-1 w-full rounded-md border border-gray-400 p-1" />
            </div>
            <div>
                <label class="block text-sm nunito-bold">Total</label>
                <input type="number" :value="calcTotalObj(formItem)" disabled class="mt-1 w-full rounded-md border border-gray-200 bg-gray-100 p-1" />
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Confirmar Eliminación -->
    <x-admin.confirmation-modal modal-name="isDeleteItemModalOpen" title="Eliminar Item" item-to-delete="itemToDelete" item-name-property="descripcion" message="¿Estás seguro de eliminar el item" />
</div>