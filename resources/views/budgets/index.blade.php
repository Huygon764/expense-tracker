@extends('layouts.app')

@section('page-title', __('messages.budgets'))

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="font-display text-2xl font-bold text-on-surface">{{ __('messages.budgets') }}</h1>
    <x-btn variant="primary" icon="plus" :href="route('budgets.create')">
        {{ __('messages.add_budget') }}
    </x-btn>
</div>

@forelse($budgets as $budget)
    @if($loop->first)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @endif

    @php
        $spent = $budget->spent ?? $budget->getSpentInCurrentPeriod();
        $amount = (float) $budget->amount;
        $pct = $amount > 0 ? min(100, ($spent / $amount) * 100) : 0;
        $overBudget = $amount > 0 && $spent > $amount;
    @endphp

    <x-card>
        <div class="flex items-center justify-between mb-4">
            <x-badge color="{{ $budget->type === 'weekly' ? 'secondary' : 'primary' }}">
                {{ ucfirst($budget->type) }}
            </x-badge>
            @if($overBudget)
                <x-badge color="error">{{ __('messages.over') }}</x-badge>
            @endif
        </div>

        <div class="mb-4">
            <span class="font-display text-2xl font-bold text-on-surface">{{ number_format($budget->amount, 2) }}</span>
        </div>

        <div class="flex items-center justify-between text-sm text-on-surface-variant mb-3">
            <span>{{ __('messages.spent') }}</span>
            <span class="font-semibold text-on-surface">{{ number_format($spent, 2) }}</span>
        </div>

        <x-progress-bar :percent="$pct" color="auto" />

        <div class="flex items-center justify-between mt-1.5 mb-5">
            <span class="text-xs text-on-surface-variant">
                @if($amount > 0)
                    {{ number_format($pct, 0) }}%
                @else
                    &mdash;
                @endif
            </span>
        </div>

        <div class="flex items-center gap-2" style="border-top: 1px solid rgba(191,201,200,0.15); padding-top: 1rem;">
            <x-btn variant="ghost" size="sm" icon="edit" :href="route('budgets.edit', $budget)">
                {{ __('messages.edit') }}
            </x-btn>
            @if($budget->type === 'weekly')
            <form method="POST" action="{{ route('budgets.destroy', $budget) }}" class="inline" data-confirm="{{ __('messages.confirm_delete') }}">
                @csrf
                @method('DELETE')
                <x-btn variant="danger" size="sm" icon="trash" type="submit">
                    {{ __('messages.delete') }}
                </x-btn>
            </form>
            @endif
        </div>
    </x-card>

    @if($loop->last)
        </div>
    @endif
@empty
    <x-card>
        <div class="py-10 text-center">
            <x-icon name="wallet" class="w-12 h-12 mx-auto mb-4 text-on-surface-variant/30" />
            <p class="text-on-surface-variant text-sm">
                {{ __('messages.no_budgets_yet') }}
                <x-btn variant="primary" size="sm" icon="plus" :href="route('budgets.create')" class="ml-2">
                    {{ __('messages.add_one') }}
                </x-btn>
            </p>
        </div>
    </x-card>
@endforelse
@endsection
