@extends('layouts.app')

@section('title', __('messages.budgets'))

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ __('messages.budgets') }}</h1>
    <a href="{{ route('budgets.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
        {{ __('messages.add_budget') }}
    </a>
</div>

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.budget_type') }}</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.amount') }}</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.spent') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.progress') }}</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($budgets as $budget)
                @php
                    $spent = $budget->spent ?? $budget->getSpentInCurrentPeriod();
                    $amount = (float) $budget->amount;
                    $pct = $amount > 0 ? min(100, ($spent / $amount) * 100) : 0;
                    $overBudget = $amount > 0 && $spent > $amount;
                @endphp
                <tr>
                    <td class="px-4 py-3 text-sm font-medium">
                        {{ ucfirst($budget->type) }}
                    </td>
                    <td class="px-4 py-3 text-sm text-right font-medium">{{ number_format($budget->amount, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right">{{ number_format($spent, 2) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 max-w-xs rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden">
                                <div class="h-full rounded-full {{ $overBudget ? 'bg-red-500' : ($pct >= 80 ? 'bg-amber-500' : 'bg-indigo-600') }}"
                                    style="width: {{ min(100, $pct) }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                @if($amount > 0)
                                    {{ number_format($pct, 0) }}%
                                    @if($overBudget)
                                        <span class="text-red-600 dark:text-red-400">({{ __('messages.over') }})</span>
                                    @endif
                                @else
                                    —
                                @endif
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right text-sm">
                        <a href="{{ route('budgets.edit', $budget) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('messages.edit') }}</a>
                        <form method="POST" action="{{ route('budgets.destroy', $budget) }}" class="inline ml-4" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">{{ __('messages.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('messages.no_budgets_yet') }} <a href="{{ route('budgets.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('messages.add_one') }}</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
