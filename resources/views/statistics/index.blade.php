@extends('layouts.app')

@section('page-title', __('messages.statistics'))

@section('content')
<div class="space-y-8">

    {{-- Page header --}}
    <div>
        <h1 class="text-3xl font-display font-bold text-on-surface">{{ __('messages.statistics') }}</h1>
    </div>

    {{-- Period filter --}}
    <x-card>
        <form method="GET" action="{{ route('statistics.index') }}" class="space-y-4">
            {{-- Pill buttons --}}
            <div class="flex flex-wrap gap-2">
                @php
                    $periods = [
                        'today' => __('messages.today'),
                        'week' => __('messages.this_week'),
                        'month' => __('messages.this_month'),
                        'year' => __('messages.this_year'),
                        'custom' => __('messages.custom'),
                    ];
                    $currentPeriod = $period ?? request('period', 'month');
                @endphp
                @foreach($periods as $key => $label)
                    <button type="submit"
                            name="period"
                            value="{{ $key }}"
                            class="px-4 py-2 rounded-full text-sm font-semibold transition-all duration-150
                                {{ $currentPeriod === $key
                                    ? 'bg-primary text-on-primary shadow-editorial-sm'
                                    : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Custom date range --}}
            <div id="custom-date-range" class="{{ $currentPeriod === 'custom' ? '' : 'hidden' }}">
                <div class="flex flex-wrap items-end gap-4 pt-4" style="border-top: 1px solid rgba(191,201,200,0.15);">
                    <x-form-input name="date_from" :label="__('messages.date_from_custom')" type="date" :value="$date_from" />
                    <x-form-input name="date_to" :label="__('messages.date_to')" type="date" :value="$date_to" />
                    <x-btn type="submit" variant="primary" icon="search" name="period" value="custom">{{ __('messages.view_btn') }}</x-btn>
                </div>
            </div>
        </form>
    </x-card>

    {{-- Hero stat: Total spending --}}
    <div class="bg-gradient-primary rounded-2xl shadow-editorial p-8 text-on-primary">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-on-primary/70">{{ __('messages.total_in_period') }}</p>
                <p class="mt-1 text-sm text-on-primary/70">{{ $date_from }} — {{ $date_to }}</p>
                <p class="mt-3 text-4xl md:text-5xl font-display font-bold">{{ number_format($total, 0, '.', ',') }}</p>
                @if($daysInRange > 0)
                    <p class="mt-2 text-sm text-on-primary/70">{{ __('messages.avg_per_day_label', ['amount' => number_format($avgPerDay, 0, '.', ',')]) }}</p>
                @endif
            </div>
            <div class="flex flex-col items-start md:items-end gap-2">
                {{-- Comparison to previous period --}}
                <div class="bg-white/15 backdrop-blur-sm rounded-xl px-5 py-3">
                    <p class="text-xs text-on-primary/70 uppercase tracking-wider font-semibold">{{ __('messages.prev_period') }}</p>
                    <p class="text-lg font-display font-bold text-on-primary">{{ number_format($previousTotal, 0, '.', ',') }}</p>
                </div>
                <div class="flex items-center gap-2 px-2">
                    @if($diffAmount > 0)
                        <x-icon name="trending-up" class="w-5 h-5 text-on-primary" />
                    @elseif($diffAmount < 0)
                        <x-icon name="trending-down" class="w-5 h-5 text-on-primary" />
                    @endif
                    <span class="text-sm font-semibold text-on-primary">
                        {{ $diffAmount >= 0 ? '+' : '' }}{{ number_format($diffAmount, 0, '.', ',') }}
                        ({{ $diffPercent >= 0 ? '+' : '' }}{{ $diffPercent }}%)
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Doughnut chart (category breakdown) --}}
        <x-card>
            <h2 class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant mb-4">{{ __('messages.spending_by_category') }}</h2>
            @if($pieLabels->isNotEmpty())
                <div class="h-72 flex items-center justify-center">
                    <canvas id="chart-pie"></canvas>
                </div>
            @else
                <div class="h-72 flex items-center justify-center">
                    <p class="text-sm text-on-surface-variant">{{ __('messages.no_data') }}</p>
                </div>
            @endif
        </x-card>

        {{-- Bar chart (period) --}}
        <x-card>
            <h2 class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant mb-4">{{ __('messages.spending_by_time') }}</h2>
            <div class="overflow-x-auto">
                <div class="h-72 min-w-[280px]" style="min-width: {{ max(280, $barLabels->count() * 24) }}px;">
                    <canvas id="chart-bar"></canvas>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Top 5 categories --}}
    <x-card>
        <h2 class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant mb-5">{{ __('messages.top_5_categories') }}</h2>
        @if(!empty($topCategories))
            <div class="space-y-4">
                @foreach($topCategories as $row)
                    <div class="flex items-center gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-semibold text-on-surface truncate">{{ $row['category_name'] }}</span>
                                <span class="text-sm font-semibold text-on-surface ml-2 shrink-0">{{ number_format($row['total'], 0, '.', ',') }}</span>
                            </div>
                            <x-progress-bar :percent="$row['percent']" color="primary" height="h-2" />
                        </div>
                        <span class="text-xs font-semibold text-on-surface-variant w-10 text-right shrink-0">{{ $row['percent'] }}%</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="py-6 text-center text-sm text-on-surface-variant">{{ __('messages.no_data') }}</p>
        @endif
    </x-card>

    {{-- Expenses table --}}
    <x-card class="!p-0 overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4" style="border-bottom: 1px solid rgba(191,201,200,0.15);">
            <h2 class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant">{{ __('messages.expenses_in_period') }}</h2>
            <x-btn variant="ghost" size="sm" :href="route('expenses.index')">{{ __('messages.view_all') }}</x-btn>
        </div>
        @if($expenses->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-surface-container-low">
                            <th scope="col" class="px-6 py-3 text-left text-[10px] font-semibold uppercase tracking-widest text-on-surface-variant">{{ __('messages.date') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-[10px] font-semibold uppercase tracking-widest text-on-surface-variant">{{ __('messages.category') }}</th>
                            <th scope="col" class="px-6 py-3 text-right text-[10px] font-semibold uppercase tracking-widest text-on-surface-variant">{{ __('messages.amount') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-[10px] font-semibold uppercase tracking-widest text-on-surface-variant">{{ __('messages.note') }}</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">{{ __('messages.edit') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                            <tr class="transition-colors hover:bg-surface-container-low" style="border-top: 1px solid rgba(191,201,200,0.15);">
                                <td class="px-6 py-3.5 text-sm text-on-surface">{{ $expense->date->format('d/m/Y') }}</td>
                                <td class="px-6 py-3.5 text-sm text-on-surface">
                                    @if($expense->category?->name)
                                        <x-badge color="secondary">{{ $expense->category->name }}</x-badge>
                                    @else
                                        <span class="text-on-surface-variant">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-sm text-right font-semibold text-on-surface">{{ number_format($expense->amount, 0, '.', ',') }}</td>
                                <td class="px-6 py-3.5 text-sm text-on-surface-variant max-w-xs truncate">{{ $expense->note ?? '—' }}</td>
                                <td class="px-6 py-3.5 text-right">
                                    <x-btn variant="ghost" size="sm" :href="route('expenses.edit', $expense)" icon="edit">{{ __('messages.edit') }}</x-btn>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4" style="border-top: 1px solid rgba(191,201,200,0.15);">
                {{ $expenses->withQueryString()->links() }}
            </div>
        @else
            <p class="px-6 py-12 text-center text-sm text-on-surface-variant">{{ __('messages.no_expenses_in_period') }}</p>
        @endif
    </x-card>
</div>

@if($pieLabels->isNotEmpty())
    @push('scripts')
    <script>
        (function() {
            function initPie() {
                if (typeof window.Chart === 'undefined') {
                    setTimeout(initPie, 50);
                    return;
                }
                var pieEl = document.getElementById('chart-pie');
                if (pieEl) {
                    var pieData = {
                        labels: @json($pieLabels),
                        datasets: [{ data: @json($pieValues), backgroundColor: @json($pieColors), borderWidth: 0 }]
                    };
                    new window.Chart(pieEl, {
                        type: 'doughnut',
                        data: pieData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 16,
                                        usePointStyle: true,
                                        pointStyleWidth: 8,
                                        font: { family: 'Inter', size: 12 }
                                    }
                                }
                            }
                        }
                    });
                }
            }
            initPie();
        })();
    </script>
    @endpush
@endif

@push('scripts')
<script>
(function() {
    function initBar() {
        if (typeof window.Chart === 'undefined') {
            setTimeout(initBar, 50);
            return;
        }
        var barEl = document.getElementById('chart-bar');
        if (barEl) {
            var barData = {
                labels: @json($barLabels),
                datasets: [{
                    label: @json(__('messages.expenses')),
                    data: @json($barValues),
                    backgroundColor: '#2170e4',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            };
            new window.Chart(barEl, {
                type: 'bar',
                data: barData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', size: 11 } }
                        },
                        y: {
                            grid: { color: 'rgba(191,201,200,0.15)' },
                            ticks: { font: { family: 'Inter', size: 11 } }
                        }
                    }
                }
            });
        }
    }
    initBar();

    // Toggle custom date range visibility
    var pills = document.querySelectorAll('button[name="period"]');
    var customRange = document.getElementById('custom-date-range');
    pills.forEach(function(pill) {
        pill.addEventListener('click', function(e) {
            if (this.value === 'custom') {
                e.preventDefault();
                customRange.classList.remove('hidden');
            } else {
                customRange.classList.add('hidden');
            }
        });
    });
})();
</script>
@endpush
@endsection
