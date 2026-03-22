@extends('layouts.app')

@section('page-title', __('messages.savings_goals'))

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <h1 class="font-display text-2xl font-bold text-on-surface">{{ __('messages.savings_goals') }}</h1>
    <x-btn variant="primary" :href="route('savings-goals.create')" icon="plus">
        {{ __('messages.add_goal') }}
    </x-btn>
</div>

@forelse($goals as $goal)
    @php
        $current = (float) $goal->current_amount;
        $target = (float) $goal->target_amount;
        $pct = $target > 0 ? min(100, max(0, ($current / $target) * 100)) : 0;

        $statusColorMap = [
            'on_track' => 'success',
            'behind' => 'tertiary',
            'achieved' => 'primary',
            'expired' => 'error',
        ];
        $statusColor = $statusColorMap[$goal->status] ?? 'secondary';

        $statusLabelMap = [
            'on_track' => __('messages.on_track'),
            'behind' => __('messages.behind'),
            'achieved' => __('messages.achieved'),
            'expired' => __('messages.expired'),
        ];
        $statusLabel = $statusLabelMap[$goal->status] ?? $goal->status;

        $progressBarColorMap = [
            'on_track' => 'success',
            'behind' => 'tertiary',
            'achieved' => 'primary',
            'expired' => 'error',
        ];
        $progressBarColor = $progressBarColorMap[$goal->status] ?? 'primary';
    @endphp

    @if($loop->first)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @endif

    <x-card class="flex flex-col">
        {{-- Header --}}
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                    <x-icon name="target" class="w-5 h-5 text-primary" />
                </div>
                <div>
                    <h3 class="font-display text-base font-bold text-on-surface">{{ $goal->name }}</h3>
                    <p class="text-xs text-on-surface-variant mt-0.5">
                        <x-icon name="calendar" class="w-3 h-3 inline -mt-0.5" />
                        {{ $goal->deadline->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            <x-badge :color="$statusColor">{{ $statusLabel }}</x-badge>
        </div>

        {{-- Amount --}}
        <div class="mb-4">
            <div class="flex items-baseline justify-between mb-2">
                <span class="text-sm text-on-surface-variant">{{ number_format($current, 0) }}</span>
                <span class="text-sm font-semibold text-on-surface">{{ number_format($target, 0) }}</span>
            </div>
            <x-progress-bar :percent="$pct" :color="$progressBarColor" height="h-2.5" />
            <p class="text-xs text-on-surface-variant mt-1.5 text-right">{{ number_format($pct, 0) }}%</p>
        </div>

        {{-- Monthly needed --}}
        <div class="bg-surface-container-low rounded-xl px-4 py-3 mb-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant">{{ __('messages.monthly_needed') }}</span>
                <span class="text-sm font-bold text-on-surface">
                    @if($goal->monthly_needed === null)
                        &mdash;
                    @elseif($goal->monthly_needed == 0 && $current >= $target)
                        Done
                    @elseif($goal->status === 'expired')
                        Deadline passed
                    @else
                        {{ number_format($goal->monthly_needed, 0) }}
                    @endif
                </span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-auto flex items-center gap-2" style="border-top: 1px solid rgba(191,201,200,0.15); padding-top: 1rem;">
            <x-btn variant="secondary" size="sm" :href="route('savings-goals.deposits', $goal)" icon="dollar-sign">
                {{ __('messages.deposited') }}
            </x-btn>
            <x-btn variant="ghost" size="sm" :href="route('savings-goals.edit', $goal)" icon="edit">
                {{ __('messages.edit') }}
            </x-btn>
            <form method="POST" action="{{ route('savings-goals.destroy', $goal) }}" class="ml-auto" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                @csrf
                @method('DELETE')
                <x-btn variant="ghost" size="sm" type="submit" icon="trash" class="text-error hover:bg-error-container">
                    {{ __('messages.delete') }}
                </x-btn>
            </form>
        </div>
    </x-card>

    @if($loop->last)
        </div>
    @endif

@empty
    <x-card class="text-center py-12">
        <div class="w-16 h-16 rounded-2xl bg-surface-container mx-auto mb-4 flex items-center justify-center">
            <x-icon name="target" class="w-8 h-8 text-on-surface-variant" />
        </div>
        <h3 class="font-display text-lg font-bold text-on-surface mb-1">{{ __('messages.savings_goals') }}</h3>
        <p class="text-sm text-on-surface-variant mb-6">No savings goals yet. Start by creating one.</p>
        <x-btn variant="primary" :href="route('savings-goals.create')" icon="plus">
            {{ __('messages.add_goal') }}
        </x-btn>
    </x-card>
@endforelse
@endsection
