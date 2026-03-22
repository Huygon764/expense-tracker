@props(['color' => 'secondary', 'size' => 'sm'])

@php
    $colorMap = [
        'primary' => 'bg-primary/10 text-primary',
        'secondary' => 'bg-secondary-container text-on-secondary-fixed-variant',
        'error' => 'bg-error-container text-error',
        'tertiary' => 'bg-tertiary-container text-tertiary',
        'success' => 'bg-emerald-50 text-emerald-700',
    ];
    $sizeMap = [
        'xs' => 'px-2 py-0.5 text-[10px]',
        'sm' => 'px-2.5 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
    ];
    $colorClass = $colorMap[$color] ?? $colorMap['secondary'];
    $sizeClass = $sizeMap[$size] ?? $sizeMap['sm'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full font-semibold {$colorClass} {$sizeClass}"]) }}>
    {{ $slot }}
</span>
