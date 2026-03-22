@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button', 'icon' => null])

@php
    $variantMap = [
        'primary' => 'bg-gradient-primary text-on-primary shadow-editorial-sm hover:shadow-editorial active:scale-[0.98]',
        'secondary' => 'bg-surface-container-high text-on-surface hover:bg-surface-container-highest active:scale-[0.98]',
        'ghost' => 'text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface',
        'danger' => 'bg-error text-on-primary hover:bg-error/90 active:scale-[0.98]',
    ];
    $sizeMap = [
        'sm' => 'px-3 py-1.5 text-xs rounded-lg gap-1.5',
        'md' => 'px-5 py-2.5 text-sm rounded-xl gap-2',
        'lg' => 'px-6 py-3 text-base rounded-xl gap-2',
    ];
    $classes = 'inline-flex items-center justify-center font-semibold transition-all duration-150 ' . ($variantMap[$variant] ?? $variantMap['primary']) . ' ' . ($sizeMap[$size] ?? $sizeMap['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<x-icon :name="$icon" class="w-4 h-4 shrink-0" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<x-icon :name="$icon" class="w-4 h-4 shrink-0" />@endif
        {{ $slot }}
    </button>
@endif
