@if(!isset($tabKey))
    @php $tabKey = 'spa-tab-' . \Str::slug(\App\Helpers\SpaHelper::getCurrentView() ?? 'unknown-view'); @endphp
@endif

x-init="
    tab = localStorage.getItem('{{ $tabKey }}') ?? tab;
    $watch('tab', value => localStorage.setItem('{{ $tabKey }}', value));
"
