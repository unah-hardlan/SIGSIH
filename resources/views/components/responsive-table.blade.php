@props([
    'title' => null,
    'description' => null,
])

@php
    $hasFilters = isset($filters) && !$filters->isEmpty();
    $hasActions = isset($actions) && !$actions->isEmpty();
    $hasTable = isset($table) && !$table->isEmpty();
    $hasCards = isset($cards) && !$cards->isEmpty();
    $hasHeader = $title || $description || $hasFilters || $hasActions;
@endphp

<div {{ $attributes->class(['space-y-6']) }}>
    @if($hasHeader)
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="space-y-1">
                @if($title)
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white nunito-bold">{{ $title }}</h2>
                @endif
                @if($description)
                    <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular">{{ $description }}</p>
                @endif
                @if($hasFilters)
                    <div class="flex flex-wrap gap-2">{!! $filters !!}</div>
                @endif
            </div>
            @if($hasActions)
                <div class="flex flex-wrap gap-2 md:justify-end">{!! $actions !!}</div>
            @endif
        </div>
    @endif

    @if($hasTable || isset($slot))
        <div class="hidden md:block">
            <div class="overflow-x-auto">
                {{ $hasTable ? $table : $slot }}
            </div>
        </div>
    @endif

    @if($hasCards)
        <div class="md:hidden space-y-4">
            {{ $cards }}
        </div>
    @endif
</div>
