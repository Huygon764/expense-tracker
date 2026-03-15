@extends('layouts.app')

@section('title', __('messages.expenses'))

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ __('messages.expenses') }}</h1>
    <a href="{{ route('expenses.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
        {{ __('messages.add_expense') }}
    </a>
</div>

<form method="GET" action="{{ route('expenses.index') }}" class="mb-6 flex flex-wrap items-end gap-3">
    <div>
        <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.period') }}</label>
        <select name="period" id="period" class="mt-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 text-sm">
            <option value="">{{ __('messages.all_time') }}</option>
            <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>{{ __('messages.today') }}</option>
            <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>{{ __('messages.this_week') }}</option>
            <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>{{ __('messages.this_month') }}</option>
            <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>{{ __('messages.this_year') }}</option>
        </select>
    </div>
    <div>
        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.search') }}</label>
        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_note_placeholder') }}"
            class="mt-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 text-sm w-48">
    </div>
    <button type="submit" class="rounded-md bg-gray-200 dark:bg-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-500">
        {{ __('messages.filter') }}
    </button>
    @if(request()->hasAny(['period', 'search']))
        <a href="{{ route('expenses.index') }}" class="rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
            {{ __('messages.clear_filter') }}
        </a>
    @endif
</form>

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.date') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.category') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.amount') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.note') }}</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($expenses as $expense)
                <tr>
                    <td class="px-4 py-3 text-sm">{{ $expense->date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-sm">{{ $expense->category?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm font-medium">{{ number_format($expense->amount, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $expense->note ?: '—' }}</td>
                    <td class="px-4 py-3 text-right text-sm">
                        <a href="{{ route('expenses.edit', $expense) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('messages.edit') }}</a>
                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="inline ml-4" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">{{ __('messages.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('messages.no_expenses_yet') }} <a href="{{ route('expenses.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('messages.add_one') }}</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($expenses->hasPages())
    <div class="mt-4">
        {{ $expenses->withQueryString()->links() }}
    </div>
@endif
@endsection
