@extends('layouts.app')

@section('title', 'Recurring expenses')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold">Recurring expenses</h1>
    <a href="{{ route('recurring-expenses.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
        Add recurring
    </a>
</div>

<div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Title</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Category</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Day</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Next run</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Active</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($recurringExpenses as $r)
                @php
                    $dayLabels = [0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];
                    $dayDisplay = $r->type === 'weekly' ? ($dayLabels[$r->day_of_week] ?? $r->day_of_week) : 'Day ' . $r->day_of_month;
                @endphp
                <tr>
                    <td class="px-4 py-3 text-sm font-medium">{{ $r->title }}</td>
                    <td class="px-4 py-3 text-sm text-right">{{ number_format($r->amount, 2) }}</td>
                    <td class="px-4 py-3 text-sm">{{ $r->category?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm capitalize">{{ $r->type }}</td>
                    <td class="px-4 py-3 text-sm">{{ $dayDisplay }}</td>
                    <td class="px-4 py-3 text-sm">{{ $r->getNextRunDate()->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm">
                        <form method="POST" action="{{ route('recurring-expenses.toggle', $r) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded px-2 py-1 text-xs {{ $r->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-400' }}">
                                {{ $r->is_active ? 'Yes' : 'No' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right text-sm">
                        <a href="{{ route('recurring-expenses.edit', $r) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('recurring-expenses.destroy', $r) }}" class="inline ml-4" onsubmit="return confirm('Delete this recurring expense?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No recurring expenses yet. <a href="{{ route('recurring-expenses.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Add one</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
