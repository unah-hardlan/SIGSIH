document.addEventListener("alpine:init",()=>{Alpine.magic("responsive",()=>({isMobile(){return window.innerWidth<768},createCardData(r,t){const i={};return t.forEach(e=>{typeof e=="string"?i[e]=r[e]:i[e.key]={value:r[e.key],label:e.label,format:e.format}}),i},formatValue(r,t="text"){switch(t){case"date":return new Date(r).toLocaleDateString("es-ES");case"status":return r?"Activo":"Inactivo";case"currency":return new Intl.NumberFormat("es-HN",{style:"currency",currency:"HNL"}).format(r);default:return r}}})),Alpine.data("tablaResponsive",(r={})=>({isMobile:window.innerWidth<768,viewMode:"auto",searchTerm:"",sortField:"",sortDirection:"asc",init(){this.updateViewMode(),window.addEventListener("resize",()=>{this.updateViewMode()})},updateViewMode(){this.isMobile=window.innerWidth<768,this.viewMode==="auto"&&(this.currentView=this.isMobile?"cards":"table")},get currentView(){return this.viewMode==="auto"?this.isMobile?"cards":"table":this.viewMode},toggleView(){this.viewMode=this.currentView==="table"?"cards":"table"},get filteredData(){let t=this.data||[];return this.searchTerm&&(t=t.filter(i=>Object.values(i).some(e=>String(e).toLowerCase().includes(this.searchTerm.toLowerCase())))),this.sortField&&t.sort((i,e)=>{const s=i[this.sortField],a=e[this.sortField];return this.sortDirection==="asc"?s>a?1:-1:s<a?1:-1}),t},sortBy(t){this.sortField===t?this.sortDirection=this.sortDirection==="asc"?"desc":"asc":(this.sortField=t,this.sortDirection="asc")}}))});const o=document.createElement("style");o.textContent=`
    .table-responsive-transition {
        transition: all 0.3s ease-in-out;
    }
    
    .mobile-card {
        transform: translateY(0);
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .mobile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .mobile-card-enter {
        opacity: 0;
        transform: translateY(20px);
    }
    
    .mobile-card-enter-active {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 0.3s ease-out, transform 0.3s ease-out;
    }
    
    @media (max-width: 767px) {
        .table-container {
            padding: 0.5rem;
        }
        
        .mobile-card {
            margin-bottom: 1rem;
        }
    }
`;document.head.appendChild(o);
