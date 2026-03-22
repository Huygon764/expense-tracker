@props(['percent' => 0, 'color' => 'auto', 'height' => 'h-2', 'showLabel' => false])

@php
    $pct = min(100, max(0, $percent));

    if ($color === 'auto') {
        if ($pct >= 100) $barColor = 'bg-error';
        elseif ($pct >= 80) $barColor = 'bg-amber-500';
        else $barColor = 'bg-gradient-primary';
    } else {
        $colorMap = [
            'primary' => 'bg-gradient-primary',
            'error' => 'bg-error',
            'tertiary' => 'bg-tertiary',
            'success' => 'bg-emerald-500',
        ];
        $barColor = $colorMap[$color] ?? 'bg-gradient-primary';
    }
@endphp

<div class="w-full">
    @if($showLabel)
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs font-medium text-on-surface-variant">{{ $slot }}</span>
            <span class="text-xs font-semibold text-on-surface">{{ number_format($pct, 0) }}%</span>
        </div>
    @endif
    <div class="w-full {{ $height }} rounded-full bg-surface-container overflow-hidden">
        <div class="{{ $height }} rounded-full {{ $barColor }} transition-all duration-500 ease-out"
             style="width: {{ $pct }}%"></div>
    </div>
</div>
