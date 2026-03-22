@props(['label', 'value', 'icon' => null, 'color' => 'primary', 'subtitle' => null])

@php
    $colorMap = [
        'primary' => 'bg-primary/10 text-primary',
        'error' => 'bg-error/10 text-error',
        'tertiary' => 'bg-tertiary/10 text-tertiary',
        'secondary' => 'bg-secondary/10 text-secondary',
    ];
    $borderMap = [
        'primary' => 'border-l-primary',
        'error' => 'border-l-error',
        'tertiary' => 'border-l-tertiary',
        'secondary' => 'border-l-secondary',
    ];
    $iconColor = $colorMap[$color] ?? $colorMap['primary'];
    $borderColor = $borderMap[$color] ?? $borderMap['primary'];
@endphp

<div class="bg-surface-container-lowest rounded-2xl shadow-editorial-sm p-5 border-l-4 {{ $borderColor }}">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant">{{ $label }}</p>
            <p class="mt-2 text-2xl font-display font-bold text-on-surface">{{ $value }}</p>
            @if($subtitle)
                <p class="mt-1 text-sm text-on-surface-variant">{{ $subtitle }}</p>
            @endif
        </div>
        @if($icon)
            <div class="w-10 h-10 rounded-xl {{ $iconColor }} flex items-center justify-center shrink-0 ml-4">
                <x-icon :name="$icon" class="w-5 h-5" />
            </div>
        @endif
    </div>
</div>
