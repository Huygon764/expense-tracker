@extends('layouts.app')

@section('title', 'Deposits — ' . $savingsGoal->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('savings-goals.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Back to goals</a>
</div>

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $savingsGoal->name }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Target: {{ number_format($savingsGoal->target_amount, 2) }} &middot;
            Deposited: {{ number_format($savingsGoal->current_amount, 2) }} &middot;
            {{ number_format($savingsGoal->progress_percentage, 0) }}%
        </p>
    </div>
</div>

<div class="max-w-md mb-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Add deposit</h2>
    <form method="POST" action="{{ route('savings-goals.deposits.store', $savingsGoal) }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="0.01" step="0.01"
                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('amount')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                <input type="date" name="date" id="date" value="{{ old('date', now()->format('Y-m-d')) }}" required
                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('date')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div>
            <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Note (optional)</label>
            <input type="text" name="note" id="note" value="{{ old('note') }}" maxlength="255"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            @error('note')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
            Add deposit
        </button>
    </form>
</div>

<h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Deposit history</h2>

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Note</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($deposits as $deposit)
                <tr>
                    <td class="px-4 py-3 text-sm">{{ $deposit->date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm text-right font-medium">{{ number_format($deposit->amount, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $deposit->note ?: '—' }}</td>
                    <td class="px-4 py-3 text-right text-sm">
                        <form method="POST" action="{{ route('savings-goals.deposits.destroy', $deposit) }}" class="inline" onsubmit="return confirm('Delete this deposit?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No deposits yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($deposits->hasPages())
    <div class="mt-4">
        {{ $deposits->links() }}
    </div>
@endif
@endsection
