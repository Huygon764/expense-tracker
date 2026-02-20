@extends('layouts.app')

@section('title', 'Thống kê')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Thống kê</h1>

    {{-- Filter: preset + custom --}}
    <form method="GET" action="{{ route('statistics.index') }}" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 flex flex-wrap items-end gap-4">
        <div>
            <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kỳ (preset)</label>
            <select name="period" id="period" class="mt-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 text-sm">
                <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Hôm nay</option>
                <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Tuần này</option>
                <option value="month" {{ request('period') === 'month' || !request('period') ? 'selected' : '' }}>Tháng này</option>
                <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Năm nay</option>
                <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Tùy chọn (từ/đến)</option>
            </select>
        </div>
        <div>
            <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Từ ngày (tùy chỉnh)</label>
            <input type="date" name="date_from" id="date_from" value="{{ $date_from }}"
                class="mt-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 text-sm">
        </div>
        <div>
            <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Đến ngày</label>
            <input type="date" name="date_to" id="date_to" value="{{ $date_to }}"
                class="mt-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 text-sm">
        </div>
        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">Xem</button>
    </form>

    {{-- Total + compare previous period --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tổng chi trong kỳ</h2>
        <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $date_from }} — {{ $date_to }}</p>
        <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($total, 0, '.', ',') }}</p>
        @if($daysInRange > 0)
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Trung bình {{ number_format($avgPerDay, 0, '.', ',') }}/ngày</p>
        @endif
        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-700 dark:text-gray-300">Kỳ này: {{ number_format($total, 0, '.', ',') }} — Kỳ trước: {{ number_format($previousTotal, 0, '.', ',') }}</p>
            <p class="mt-1 text-sm {{ $diffAmount > 0 ? 'text-red-600 dark:text-red-400' : ($diffAmount < 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400') }}">
                Chênh lệch: {{ $diffAmount >= 0 ? '+' : '' }}{{ number_format($diffAmount, 0, '.', ',') }} ({{ $diffPercent >= 0 ? '+' : '' }}{{ $diffPercent }}%)
                @if($diffAmount > 0) ↑ @elseif($diffAmount < 0) ↓ @endif
            </p>
        </div>
    </div>

    {{-- Pie chart --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Chi tiêu theo danh mục</h2>
        @if($pieLabels->isNotEmpty())
            <div class="h-64">
                <canvas id="chart-pie"></canvas>
            </div>
        @else
            <p class="py-8 text-center text-gray-500 dark:text-gray-400">Chưa có dữ liệu</p>
        @endif
    </div>

    {{-- Top 5 categories --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Top 5 danh mục chi nhiều nhất</h2>
        @if(!empty($topCategories))
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Danh mục</th>
                        <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Số tiền</th>
                        <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($topCategories as $row)
                        <tr class="bg-white dark:bg-gray-800">
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $row['category_name'] }}</td>
                            <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($row['total'], 0, '.', ',') }}</td>
                            <td class="px-4 py-2 text-sm text-right text-gray-600 dark:text-gray-400">{{ $row['percent'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="py-4 text-center text-gray-500 dark:text-gray-400">Chưa có dữ liệu</p>
        @endif
    </div>

    {{-- Bar chart (scroll on mobile) --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Chi tiêu theo thời gian</h2>
        <div class="overflow-x-auto">
            <div class="h-64 min-w-[280px]" style="min-width: {{ max(280, $barLabels->count() * 24) }}px;">
                <canvas id="chart-bar"></canvas>
            </div>
        </div>
    </div>

    {{-- Expenses table --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
        <div class="flex justify-between items-center px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Chi tiêu trong kỳ</h2>
            <a href="{{ route('expenses.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Xem tất cả</a>
        </div>
        @if($expenses->isNotEmpty())
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ngày</th>
                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Danh mục</th>
                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Số tiền</th>
                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ghi chú</th>
                        <th scope="col" class="relative px-4 py-2"><span class="sr-only">Edit</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($expenses as $expense)
                        <tr class="bg-white dark:bg-gray-800">
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $expense->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $expense->category?->name ?? '—' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ number_format($expense->amount, 0, '.', ',') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $expense->note ?? '—' }}</td>
                            <td class="px-4 py-2 text-right text-sm">
                                <a href="{{ route('expenses.edit', $expense) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Sửa</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700">
                {{ $expenses->withQueryString()->links() }}
            </div>
        @else
            <p class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Chưa có chi tiêu trong kỳ này.</p>
        @endif
    </div>
</div>

@if($pieLabels->isNotEmpty())
    @push('scripts')
    <script>
        (function() {
            const pieData = {
                labels: @json($pieLabels),
                datasets: [{ data: @json($pieValues), backgroundColor: @json($pieColors) }]
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
        datasets: [{ label: 'Chi tiêu', data: @json($barValues), backgroundColor: '#4F46E5' }]
    };
    const barEl = document.getElementById('chart-bar');
    if (barEl && typeof window.Chart !== 'undefined') {
        new window.Chart(barEl, { type: 'bar', data: barData });
    }
})();
</script>
@endpush
@endsection
