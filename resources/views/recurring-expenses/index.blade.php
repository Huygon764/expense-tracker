@extends('layouts.app')

@section('page-title', __('messages.recurring_expenses'))

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="font-display text-2xl font-bold text-on-surface">{{ __('messages.recurring_expenses') }}</h1>
    <x-btn variant="primary" icon="plus" :href="route('recurring-expenses.create')">
        {{ __('messages.add_recurring') }}
    </x-btn>
</div>

<x-card class="overflow-hidden !p-0">
    {{-- Header row --}}
    <div class="hidden sm:grid sm:grid-cols-12 gap-4 px-6 py-3 bg-surface-container text-xs font-semibold uppercase tracking-widest text-on-surface-variant">
        <div class="col-span-2">{{ __('messages.title') }}</div>
        <div class="col-span-1 text-right">{{ __('messages.amount') }}</div>
        <div class="col-span-2">{{ __('messages.category') }}</div>
        <div class="col-span-1">{{ __('messages.frequency') }}</div>
        <div class="col-span-1">{{ __('messages.day') }}</div>
        <div class="col-span-2">{{ __('messages.next_run') }}</div>
        <div class="col-span-1">{{ __('messages.status') }}</div>
        <div class="col-span-2 text-right">{{ __('messages.actions') }}</div>
    </div>

    @forelse($recurringExpenses as $index => $r)
        @php
            $dayLabels = [0 => __('messages.sunday'), 1 => __('messages.monday'), 2 => __('messages.tuesday'), 3 => __('messages.wednesday'), 4 => __('messages.thursday'), 5 => __('messages.friday'), 6 => __('messages.saturday')];
            $dayDisplay = $r->type === 'weekly' ? ($dayLabels[$r->day_of_week] ?? $r->day_of_week) : 'Day ' . $r->day_of_month;
        @endphp
        <div class="{{ $index % 2 === 0 ? 'bg-surface-container-lowest' : 'bg-surface-container-low' }} px-6 py-4 sm:grid sm:grid-cols-12 sm:gap-4 sm:items-center flex flex-col gap-2"
             @if($index > 0) style="border-top: 1px solid rgba(191,201,200,0.15);" @endif>
            {{-- Title --}}
            <div class="sm:col-span-2 text-sm font-semibold text-on-surface">
                {{ $r->title }}
            </div>
            {{-- Amount --}}
            <div class="sm:col-span-1 text-right font-display font-bold text-on-surface">
                {{ number_format($r->amount, 2) }}
            </div>
            {{-- Category --}}
            <div class="sm:col-span-2">
                <x-badge color="secondary">{{ $r->category?->name ?? '---' }}</x-badge>
            </div>
            {{-- Frequency --}}
            <div class="sm:col-span-1">
                <x-badge color="{{ $r->type === 'weekly' ? 'tertiary' : 'primary' }}">{{ ucfirst($r->type) }}</x-badge>
            </div>
            {{-- Day --}}
            <div class="sm:col-span-1 text-sm text-on-surface-variant">
                {{ $dayDisplay }}
            </div>
            {{-- Next run --}}
            <div class="sm:col-span-2 text-sm text-on-surface-variant">
                <x-icon name="calendar" class="w-4 h-4 inline-block mr-1 opacity-60" />
                {{ $r->getNextRunDate()->format('d/m/Y') }}
            </div>
            {{-- Status toggle --}}
            <div class="sm:col-span-1">
                <form method="POST" action="{{ route('recurring-expenses.toggle', $r) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-semibold transition-colors">
                        @if($r->is_active)
                            <x-icon name="toggle-right" class="w-5 h-5 text-primary" />
                            <span class="text-primary">{{ __('messages.active') }}</span>
                        @else
                            <x-icon name="toggle-left" class="w-5 h-5 text-on-surface-variant" />
                            <span class="text-on-surface-variant">{{ __('messages.inactive') }}</span>
                        @endif
                    </button>
                </form>
            </div>
            {{-- Actions --}}
            <div class="sm:col-span-2 flex items-center justify-end gap-2">
                <x-btn variant="ghost" size="sm" icon="edit" :href="route('recurring-expenses.edit', $r)">
                    {{ __('messages.edit') }}
                </x-btn>
                <form method="POST" action="{{ route('recurring-expenses.destroy', $r) }}" class="inline" data-confirm="{{ __('messages.confirm_delete') }}">
                    @csrf
                    @method('DELETE')
                    <x-btn variant="danger" size="sm" icon="trash" type="submit">
                        {{ __('messages.delete') }}
                    </x-btn>
                </form>
            </div>
        </div>
    @empty
        <div class="px-6 py-16 text-center">
            <x-icon name="repeat" class="w-12 h-12 mx-auto mb-4 text-on-surface-variant/30" />
            <p class="text-on-surface-variant text-sm">
                {{ __('messages.no_recurring_yet') }}
                <x-btn variant="primary" size="sm" icon="plus" :href="route('recurring-expenses.create')" class="ml-2">
                    {{ __('messages.add_one') }}
                </x-btn>
            </p>
        </div>
    @endforelse
</x-card>
@endsection
