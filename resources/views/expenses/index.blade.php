@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Expenses</h1>
    <a href="{{ route('expenses.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
        Add expense
    </a>
</div>

<form method="GET" action="{{ route('expenses.index') }}" class="mb-6 flex flex-wrap items-end gap-3">
    <div>
        <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Period</label>
        <select name="period" id="period" class="mt-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 text-sm">
            <option value="">All time</option>
            <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Today</option>
            <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>This week</option>
            <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>This month</option>
            <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>This year</option>
        </select>
    </div>
    <div>
        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search note</label>
        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search in notes..."
            class="mt-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 text-sm w-48">
    </div>
    <button type="submit" class="rounded-md bg-gray-200 dark:bg-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-500">
        Filter
    </button>
</form>

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Category</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Note</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($expenses as $expense)
                <tr>
                    <td class="px-4 py-3 text-sm">{{ $expense->date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-sm">{{ $expense->category?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm font-medium">{{ number_format($expense->amount, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $expense->note ?? '—' }}</td>
                    <td class="px-4 py-3 text-right text-sm">
                        <a href="{{ route('expenses.edit', $expense) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="inline ml-4" onsubmit="return confirm('Delete this expense?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No expenses yet. <a href="{{ route('expenses.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Add one</a>.</td>
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
