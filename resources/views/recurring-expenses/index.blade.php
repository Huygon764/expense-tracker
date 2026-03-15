@extends('layouts.app')

@section('title', __('messages.recurring_expenses'))

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ __('messages.recurring_expenses') }}</h1>
    <a href="{{ route('recurring-expenses.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
        {{ __('messages.add_recurring') }}
    </a>
</div>

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.title') }}</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.amount') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.category') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.frequency') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.day') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.next_run') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.status') }}</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($recurringExpenses as $r)
                @php
                    $dayLabels = [0 => __('messages.sunday'), 1 => __('messages.monday'), 2 => __('messages.tuesday'), 3 => __('messages.wednesday'), 4 => __('messages.thursday'), 5 => __('messages.friday'), 6 => __('messages.saturday')];
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
                                {{ $r->is_active ? __('messages.active') : __('messages.inactive') }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right text-sm">
                        <a href="{{ route('recurring-expenses.edit', $r) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('messages.edit') }}</a>
                        <form method="POST" action="{{ route('recurring-expenses.destroy', $r) }}" class="inline ml-4" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">{{ __('messages.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('messages.no_recurring_yet') }} <a href="{{ route('recurring-expenses.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('messages.add_one') }}</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
