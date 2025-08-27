@if(!isset($tabKey))
    @php $tabKey = 'spa-tab-' . \Str::slug(\App\Helpers\SpaHelper::getCurrentView() ?? 'unknown-view'); @endphp
@endif

x-init="
    // Restaurar valor guardado, o dejar el valor por defecto
    tab = localStorage.getItem('{{ $tabKey }}') ?? tab;
    // Persistir cambios del tab
    $watch('tab', value => localStorage.setItem('{{ $tabKey }}', value));
"
