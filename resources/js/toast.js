// Reusable toast system
window.showToast = function(message, type='info', opts={}) {
  const colors = {
    success: 'bg-green-600 text-white',
    error: 'bg-red-600 text-white',
    info: 'bg-blue-600 text-white',
    warning: 'bg-yellow-500 text-white'
  };
  const containerId = 'toast-container';
  let container = document.getElementById(containerId);
  if(!container){
    container = document.createElement('div');
    container.id = containerId;
    container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-3';
    document.body.appendChild(container);
  }
  const el = document.createElement('div');
  el.className = `min-w-[240px] max-w-[360px] px-4 py-3 rounded shadow-lg text-sm font-medium flex items-start gap-3 animate-fade-in translate-y-0 opacity-0 ${colors[type]||colors.info}`;
  el.innerHTML = `<span class='flex-1'>${message}</span><button class='opacity-70 hover:opacity-100' aria-label='Cerrar'>&times;</button>`;
  const closeBtn = el.querySelector('button');
  const remove = ()=>{ el.classList.add('opacity-0','scale-95'); setTimeout(()=> el.remove(), 180); };
  closeBtn.addEventListener('click', remove);
  container.appendChild(el);
  // Force reflow then animate
  requestAnimationFrame(()=>{ el.classList.add('opacity-100'); });
  const ttl = opts.duration ?? 4000;
  if(ttl>0) setTimeout(remove, ttl);
};

// Basic CSS (inject once)
(function(){
  if(document.getElementById('toast-styles')) return;
  const css = `.animate-fade-in{transition:all .25s ease;}`;
  const style = document.createElement('style');
  style.id='toast-styles';
  style.innerHTML = css;
  document.head.appendChild(style);
})();
