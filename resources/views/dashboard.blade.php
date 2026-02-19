@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Dashboard</h1>

    {{-- Month overview --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tháng này</h2>
        @if($budgetMonthlyAmount !== null)
            <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">
                {{ number_format($spentThisMonth, 0, '.', ',') }} / {{ number_format($budgetMonthlyAmount, 0, '.', ',') }}
            </p>
            @php
                $monthPct = $budgetMonthlyAmount > 0 ? min(100, round($spentThisMonth / $budgetMonthlyAmount * 100)) : 0;
            @endphp
            <div class="mt-2 h-3 w-full rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden">
                <div class="h-full rounded-full bg-indigo-600 dark:bg-indigo-500" style="width: {{ $monthPct }}%"></div>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $monthPct }}%</p>
        @else
            <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">Đã chi tháng này: {{ number_format($spentThisMonth, 0, '.', ',') }}</p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Chưa set ngân sách. <a href="{{ route('budgets.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Set ngay</a>
            </p>
        @endif
    </div>

    {{-- Week overview (if weekly budget) --}}
    @if($budgetWeeklyAmount !== null && $spentThisWeek !== null)
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tuần này</h2>
            <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">
                {{ number_format($spentThisWeek, 0, '.', ',') }} / {{ number_format($budgetWeeklyAmount, 0, '.', ',') }}
            </p>
            @php
                $weekPct = $budgetWeeklyAmount > 0 ? min(100, round($spentThisWeek / $budgetWeeklyAmount * 100)) : 0;
            @endphp
            <div class="mt-2 h-3 w-full rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden">
                <div class="h-full rounded-full bg-indigo-600 dark:bg-indigo-500" style="width: {{ $weekPct }}%"></div>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $weekPct }}%</p>
        </div>
    @endif

    {{-- Single alert --}}
    @if($alertMessage)
        <div class="rounded-md border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-3 text-sm text-amber-800 dark:text-amber-200">
            {{ $alertMessage }}
        </div>
    @endif

    {{-- Compare last month --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">So sánh tháng trước</h2>
        <p class="mt-1 text-gray-900 dark:text-gray-100">
            Tháng này: {{ number_format($spentThisMonth, 0, '.', ',') }} — Tháng trước: {{ number_format($spentLastMonth, 0, '.', ',') }}
        </p>
        @if($spentLastMonth > 0)
            @php
                $diffPct = round((($spentThisMonth - $spentLastMonth) / $spentLastMonth) * 100);
            @endphp
            <p class="mt-1 text-sm {{ $diffPct > 0 ? 'text-red-600 dark:text-red-400' : ($diffPct < 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400') }}">
                {{ $diffPct > 0 ? '+' : '' }}{{ $diffPct }}% so với tháng trước
            </p>
        @endif
    </div>

    {{-- Pie chart --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Chi tiêu theo category (tháng này)</h2>
        @if($pieLabels->isNotEmpty())
            <div class="h-64">
                <canvas id="chart-pie"></canvas>
            </div>
        @else
            <p class="py-8 text-center text-gray-500 dark:text-gray-400">Chưa có chi tiêu nào. Bắt đầu ghi nhận!</p>
        @endif
    </div>

    {{-- Bar chart --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">7 ngày gần nhất</h2>
        <div class="h-64">
            <canvas id="chart-bar"></canvas>
        </div>
    </div>

    {{-- Recent expenses --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
        <div class="flex justify-between items-center px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Chi tiêu gần đây</h2>
            <a href="{{ route('expenses.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Xem tất cả</a>
        </div>
        @if($recentExpenses->isNotEmpty())
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Category</th>
                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Note</th>
                        <th scope="col" class="relative px-4 py-2"><span class="sr-only">Edit</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($recentExpenses as $expense)
                        <tr class="bg-white dark:bg-gray-800">
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $expense->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $expense->category?->name ?? '—' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ number_format($expense->amount, 0, '.', ',') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($expense->note, 30) }}</td>
                            <td class="px-4 py-2 text-right text-sm">
                                <a href="{{ route('expenses.edit', $expense) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Chưa có chi tiêu nào.</p>
        @endif
    </div>
</div>

@if($pieLabels->isNotEmpty())
    @push('scripts')
    <script>
        (function() {
            const pieData = {
                labels: @json($pieLabels),
                datasets: [{
                    data: @json($pieValues),
                    backgroundColor: @json($pieColors),
                }]
            };
            const pieEl = document.getElementById('chart-pie');
            if (pieEl && typeof window.Chart !== 'undefined') {
                new window.Chart(pieEl, { type: 'doughnut', data: pieData });
            }
        })();
    </script>
    @endpush
@endif

@push('scripts')
<script>
    (function() {
        const barData = {
            labels: @json($barLabels),
            datasets: [{
                label: 'Chi tiêu',
                data: @json($barValues),
                backgroundColor: '#4F46E5',
            }]
        };
        const barEl = document.getElementById('chart-bar');
        if (barEl && typeof window.Chart !== 'undefined') {
            new window.Chart(barEl, { type: 'bar', data: barData });
        }
    })();
</script>
@endpush
@endsection
