@extends('layouts.app')

@section('title', 'Savings goals')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold">Savings goals</h1>
    <a href="{{ route('savings-goals.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
        Add goal
    </a>
</div>

<p class="mb-4 text-sm text-gray-500 dark:text-gray-400">Tiết kiệm được tính dựa trên thu nhập hiện tại. Nếu thu nhập thay đổi, con số có thể không chính xác.</p>

@if($showIncomeWarning)
    <div class="mb-4 rounded-md bg-amber-50 dark:bg-amber-900/20 p-4 text-sm text-amber-800 dark:text-amber-200">
        Set monthly income để theo dõi tiến độ.
    </div>
@endif

<div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Target</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Deadline</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Current</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Progress</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Monthly needed</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($goals as $goal)
                @php
                    $current = (float) $goal->current_amount;
                    $target = (float) $goal->target_amount;
                    $displayCurrent = max(0, $current);
                    $pct = $showIncomeWarning ? 0 : ($target > 0 ? min(100, ($displayCurrent / $target) * 100) : 0);
                    $isNegative = $current < 0;
                @endphp
                <tr>
                    <td class="px-4 py-3 text-sm font-medium">{{ $goal->name }}</td>
                    <td class="px-4 py-3 text-sm text-right">{{ number_format($target, 2) }}</td>
                    <td class="px-4 py-3 text-sm">{{ $goal->deadline->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm text-right">{{ number_format($current, 2) }}</td>
                    <td class="px-4 py-3">
                        @if($isNegative)
                            <span class="text-xs text-amber-600 dark:text-amber-400">Bạn đang chi tiêu nhiều hơn thu nhập</span>
                        @else
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 max-w-xs rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden">
                                    <div class="h-full rounded-full {{ $goal->status === 'expired' ? 'bg-red-500' : ($goal->status === 'behind' ? 'bg-amber-500' : 'bg-indigo-600') }}"
                                        style="width: {{ min(100, $pct) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $showIncomeWarning ? '0' : number_format($pct, 0) }}%
                                </span>
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($goal->status === 'achieved')
                            <span class="rounded px-2 py-1 text-xs bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">Achieved</span>
                        @elseif($goal->status === 'expired')
                            <span class="rounded px-2 py-1 text-xs bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">Expired</span>
                        @elseif($goal->status === 'on_track')
                            <span class="rounded px-2 py-1 text-xs bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">On track</span>
                        @else
                            <span class="rounded px-2 py-1 text-xs bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200">Behind</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-right">
                        @if($goal->monthly_needed === null)
                            —
                        @elseif($goal->monthly_needed == 0 && $current >= $target)
                            Done
                        @elseif($goal->status === 'expired')
                            Deadline passed
                        @else
                            {{ number_format($goal->monthly_needed, 2) }}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right text-sm">
                        <a href="{{ route('savings-goals.edit', $goal) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('savings-goals.destroy', $goal) }}" class="inline ml-4" onsubmit="return confirm('Delete this savings goal?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No savings goals yet. <a href="{{ route('savings-goals.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Add one</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
