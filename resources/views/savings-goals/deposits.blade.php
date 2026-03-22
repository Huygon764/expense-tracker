@extends('layouts.app')

@section('page-title', __('messages.deposited') . ' — ' . $savingsGoal->name)

@section('content')
<div class="mb-6">
    <x-btn variant="ghost" size="sm" :href="route('savings-goals.index')" icon="arrow-left">
        {{ __('messages.back_to_goals') }}
    </x-btn>
</div>

{{-- Goal summary --}}
<x-card class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                <x-icon name="target" class="w-6 h-6 text-primary" />
            </div>
            <div>
                <h1 class="font-display text-xl font-bold text-on-surface">{{ $savingsGoal->name }}</h1>
                <p class="text-sm text-on-surface-variant mt-0.5">
                    {{ __('messages.target_amount') }}: <span class="font-semibold text-on-surface">{{ number_format($savingsGoal->target_amount, 0) }}</span>
                    &middot;
                    {{ __('messages.deposited') }}: <span class="font-semibold text-on-surface">{{ number_format($savingsGoal->current_amount, 0) }}</span>
                </p>
            </div>
        </div>
        <div class="text-2xl font-bold text-primary font-display">
            {{ number_format($savingsGoal->progress_percentage, 0) }}%
        </div>
    </div>
    <div class="mt-4">
        <x-progress-bar :percent="$savingsGoal->progress_percentage" color="primary" height="h-3" />
    </div>
</x-card>

{{-- Add deposit form --}}
<x-card class="mb-8">
    <h2 class="font-display text-lg font-bold text-on-surface mb-5">{{ __('messages.add_deposit') }}</h2>
    <form method="POST" action="{{ route('savings-goals.deposits.store', $savingsGoal) }}" class="space-y-5">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <x-amount-input name="amount" :label="__('messages.amount')" :required="true" />
            <x-form-input
                name="date"
                type="date"
                :label="__('messages.date')"
                :value="old('date', now()->format('Y-m-d'))"
                :required="true"
            />
        </div>
        <x-form-input
            name="note"
            :label="__('messages.note')"
            :value="old('note')"
            maxlength="255"
            :placeholder="__('messages.note') . ' (' . strtolower(__('messages.cancel')) . ')...'"
        />
        <x-btn variant="primary" type="submit" icon="plus">
            {{ __('messages.add_deposit') }}
        </x-btn>
    </form>
</x-card>

{{-- Deposit history --}}
<div class="flex items-center justify-between mb-4">
    <h2 class="font-display text-lg font-bold text-on-surface">{{ __('messages.deposit_history') }}</h2>
</div>

@forelse($deposits as $deposit)
    @if($loop->first)
        <div class="space-y-3">
    @endif

    <x-card class="!p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4 flex-1 min-w-0">
                <div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center shrink-0">
                    <x-icon name="dollar-sign" class="w-4 h-4 text-on-surface-variant" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-on-surface">{{ number_format($deposit->amount, 0) }}</span>
                        <span class="text-xs text-on-surface-variant">{{ $deposit->date->format('d/m/Y') }}</span>
                    </div>
                    @if($deposit->note)
                        <p class="text-xs text-on-surface-variant mt-0.5 truncate">{{ $deposit->note }}</p>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('savings-goals.deposits.destroy', $deposit) }}" class="shrink-0 ml-3" data-confirm="{{ __('messages.confirm_delete') }}">
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
    <x-card class="text-center py-10">
        <div class="w-12 h-12 rounded-2xl bg-surface-container mx-auto mb-3 flex items-center justify-center">
            <x-icon name="dollar-sign" class="w-6 h-6 text-on-surface-variant" />
        </div>
        <p class="text-sm text-on-surface-variant">No deposits yet.</p>
    </x-card>
@endforelse

@if($deposits->hasPages())
    <div class="mt-6">
        {{ $deposits->links() }}
    </div>
@endif
@endsection
