@extends('layouts.app')

@section('page-title', __('messages.expenses'))

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="font-display text-2xl font-bold text-on-surface">{{ __('messages.expenses') }}</h1>
    <x-btn variant="primary" icon="plus" :href="route('expenses.create')">
        {{ __('messages.add_expense') }}
    </x-btn>
</div>

{{-- Filter bar --}}
<x-card class="mb-6">
    <form method="GET" action="{{ route('expenses.index') }}" class="flex flex-wrap items-end gap-3">
        <x-form-select name="period" :label="__('messages.period')">
            <option value="">{{ __('messages.all_time') }}</option>
            <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>{{ __('messages.today') }}</option>
            <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>{{ __('messages.this_week') }}</option>
            <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>{{ __('messages.this_month') }}</option>
            <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>{{ __('messages.this_year') }}</option>
        </x-form-select>

        <x-form-input name="search" :label="__('messages.search')" :value="request('search')" :placeholder="__('messages.search_note_placeholder')" icon="search" />

        <div class="flex items-end gap-2">
            <x-btn variant="secondary" type="submit" icon="filter">
                {{ __('messages.filter') }}
            </x-btn>
            @if(request()->hasAny(['period', 'search']))
                <x-btn variant="ghost" :href="route('expenses.index')" icon="x">
                    {{ __('messages.clear_filter') }}
                </x-btn>
            @endif
        </div>
    </form>
</x-card>

{{-- Transaction list --}}
<x-card class="overflow-hidden !p-0">
    {{-- Header row --}}
    <div class="hidden sm:grid sm:grid-cols-12 gap-4 px-6 py-3 bg-surface-container text-xs font-semibold uppercase tracking-widest text-on-surface-variant">
        <div class="col-span-2">{{ __('messages.date') }}</div>
        <div class="col-span-2">{{ __('messages.category') }}</div>
        <div class="col-span-4">{{ __('messages.note') }}</div>
        <div class="col-span-2 text-right">{{ __('messages.amount') }}</div>
        <div class="col-span-2 text-right">{{ __('messages.actions') }}</div>
    </div>

    @forelse($expenses as $index => $expense)
        <div class="{{ $index % 2 === 0 ? 'bg-surface-container-lowest' : 'bg-surface-container-low' }} px-6 py-4 sm:grid sm:grid-cols-12 sm:gap-4 sm:items-center flex flex-col gap-2"
             @if($index > 0) style="border-top: 1px solid rgba(191,201,200,0.15);" @endif>
            {{-- Date --}}
            <div class="sm:col-span-2 text-sm text-on-surface-variant">
                <x-icon name="calendar" class="w-4 h-4 inline-block mr-1 opacity-60 sm:hidden" />
                {{ $expense->date->format('Y-m-d') }}
            </div>
            {{-- Category --}}
            <div class="sm:col-span-2">
                <x-badge color="secondary">{{ $expense->category?->name ?? '—' }}</x-badge>
            </div>
            {{-- Note --}}
            <div class="sm:col-span-4 text-sm text-on-surface-variant truncate">
                {{ $expense->note ?: '—' }}
            </div>
            {{-- Amount --}}
            <div class="sm:col-span-2 text-right font-display font-bold text-on-surface">
                {{ number_format($expense->amount, 2) }}
            </div>
            {{-- Actions --}}
            <div class="sm:col-span-2 flex items-center justify-end gap-2">
                <x-btn variant="ghost" size="sm" icon="edit" :href="route('expenses.edit', $expense)">
                    {{ __('messages.edit') }}
                </x-btn>
                <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
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
            <x-icon name="receipt" class="w-12 h-12 mx-auto mb-4 text-on-surface-variant/30" />
            <p class="text-on-surface-variant text-sm">
                {{ __('messages.no_expenses_yet') }}
                <x-btn variant="primary" size="sm" icon="plus" :href="route('expenses.create')" class="ml-2">
                    {{ __('messages.add_one') }}
                </x-btn>
            </p>
        </div>
    @endforelse
</x-card>

{{-- Pagination --}}
@if($expenses->hasPages())
    <div class="mt-6">
        {{ $expenses->appends(request()->query())->links() }}
    </div>
@endif
@endsection
