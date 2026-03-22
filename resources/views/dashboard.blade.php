@extends('layouts.app')

@section('page-title', __('messages.dashboard'))

@section('content')
<div class="space-y-8">

    {{-- Header: Greeting + AI Analysis button --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            @php
                $hour = (int) now()->format('H');
                if ($hour < 12) {
                    $greeting = __('messages.good_morning');
                } elseif ($hour < 18) {
                    $greeting = __('messages.good_afternoon');
                } else {
                    $greeting = __('messages.good_evening');
                }
            @endphp
            <h1 class="font-display text-2xl font-bold text-on-surface">{{ $greeting }}, {{ Auth::user()->name }}</h1>
        </div>
        <x-btn variant="primary" size="md" icon="sparkles" id="ai-analyze-btn">
            {{ __('messages.ai_analysis') }}
        </x-btn>
    </div>

    {{-- AI Analysis modal --}}
    <dialog id="ai-modal" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 rounded-2xl bg-surface-container-lowest shadow-editorial backdrop:bg-black/50 p-0 w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4" style="border-bottom: 1px solid rgba(191,201,200,0.15);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <x-icon name="sparkles" class="w-5 h-5" />
                </div>
                <h2 class="font-display text-lg font-bold text-on-surface">{{ __('messages.ai_analysis') }}</h2>
            </div>
            <button type="button" id="ai-modal-close" class="w-9 h-9 rounded-xl text-on-surface-variant hover:bg-surface-container-low flex items-center justify-center transition-colors" aria-label="{{ __('messages.close') }}">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>
        <div class="px-6 py-5 overflow-y-auto max-h-[calc(90vh-5rem)]">
            <div id="ai-loading" class="hidden flex items-center gap-3 text-on-surface-variant">
                <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span>{{ __('messages.ai_analyzing') }}</span>
            </div>
            <div id="ai-error" class="hidden rounded-2xl bg-error-container/50 p-4 text-sm text-error"></div>
            <div id="ai-tips" class="hidden mt-3 rounded-2xl bg-tertiary-container/50 p-4 text-sm text-tertiary"></div>
            <div id="ai-result" class="prose prose-sm max-w-none text-on-surface"></div>
        </div>
    </dialog>

    {{-- Budget alert banner --}}
    @if($alertMessage)
        <div class="flex items-center gap-3 bg-error-container/50 rounded-2xl p-4">
            <div class="w-9 h-9 rounded-xl bg-error/10 text-error flex items-center justify-center shrink-0">
                <x-icon name="alert-triangle" class="w-5 h-5" />
            </div>
            <p class="text-sm font-medium text-error">{{ $alertMessage }}</p>
        </div>
    @endif

    {{-- Budget Overview Section --}}
    <div>
        <h2 class="font-display text-lg font-bold text-on-surface mb-4">{{ __('messages.budget_overview') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Monthly budget card --}}
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <x-icon name="calendar" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant">{{ __('messages.this_month') }}</p>
                            @if($budgetMonthlyAmount !== null)
                                <p class="text-lg font-display font-bold text-on-surface">
                                    {{ number_format($spentThisMonth, 0, '.', ',') }} / {{ number_format($budgetMonthlyAmount, 0, '.', ',') }}
                                </p>
                            @else
                                <p class="text-lg font-display font-bold text-on-surface">{{ number_format($spentThisMonth, 0, '.', ',') }}</p>
                            @endif
                        </div>
                    </div>
                    @if($budgetMonthlyAmount !== null)
                        @php
                            $monthPct = $budgetMonthlyAmount > 0 ? min(100, round($spentThisMonth / $budgetMonthlyAmount * 100)) : 0;
                        @endphp
                        @if($monthPct >= 100)
                            <x-badge color="error" size="sm">{{ $monthPct }}%</x-badge>
                        @elseif($monthPct >= 80)
                            <x-badge color="tertiary" size="sm">{{ $monthPct }}%</x-badge>
                        @elseif($monthPct >= 50)
                            <x-badge color="secondary" size="sm">{{ $monthPct }}%</x-badge>
                        @else
                            <x-badge color="success" size="sm">{{ $monthPct }}%</x-badge>
                        @endif
                    @endif
                </div>
                @if($budgetMonthlyAmount !== null)
                    <x-progress-bar :percent="$monthPct" color="auto" height="h-2.5" :showLabel="false" />
                    @if($budgetMonthlyAmount > $spentThisMonth)
                        <p class="mt-2 text-xs text-on-surface-variant">{{ __('messages.remaining') }}: {{ number_format($budgetMonthlyAmount - $spentThisMonth, 0, '.', ',') }}</p>
                    @endif
                @else
                    <p class="text-sm text-on-surface-variant">
                        {{ __('messages.no_budget_set') }}
                        <a href="{{ route('budgets.create') }}" class="text-primary font-semibold hover:text-primary-container transition-colors">{{ __('messages.set_budget_now') }}</a>
                    </p>
                @endif
            </x-card>

            {{-- Weekly budget card --}}
            @if($budgetWeeklyAmount !== null && $spentThisWeek !== null)
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center">
                                <x-icon name="wallet" class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant">{{ __('messages.this_week') }}</p>
                                <p class="text-lg font-display font-bold text-on-surface">
                                    {{ number_format($spentThisWeek, 0, '.', ',') }} / {{ number_format($budgetWeeklyAmount, 0, '.', ',') }}
                                </p>
                            </div>
                        </div>
                        @php
                            $weekPct = $budgetWeeklyAmount > 0 ? min(100, round($spentThisWeek / $budgetWeeklyAmount * 100)) : 0;
                        @endphp
                        @if($weekPct >= 100)
                            <x-badge color="error" size="sm">{{ $weekPct }}%</x-badge>
                        @elseif($weekPct >= 80)
                            <x-badge color="tertiary" size="sm">{{ $weekPct }}%</x-badge>
                        @elseif($weekPct >= 50)
                            <x-badge color="secondary" size="sm">{{ $weekPct }}%</x-badge>
                        @else
                            <x-badge color="success" size="sm">{{ $weekPct }}%</x-badge>
                        @endif
                    </div>
                    <x-progress-bar :percent="$weekPct" color="auto" height="h-2.5" :showLabel="false" />
                    @if($budgetWeeklyAmount > $spentThisWeek)
                        <p class="mt-2 text-xs text-on-surface-variant">{{ __('messages.remaining') }}: {{ number_format($budgetWeeklyAmount - $spentThisWeek, 0, '.', ',') }}</p>
                    @endif
                </x-card>
            @else
                {{-- Compare last month card (when no weekly budget) --}}
                <x-card>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center">
                            <x-icon name="trending-up" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant">{{ __('messages.compare_last_month') }}</p>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-4">
                        <div>
                            <p class="text-xs text-on-surface-variant">{{ __('messages.this_month') }}</p>
                            <p class="text-lg font-display font-bold text-on-surface">{{ number_format($spentThisMonth, 0, '.', ',') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant">{{ __('messages.last_month') }}</p>
                            <p class="text-lg font-display font-bold text-on-surface">{{ number_format($spentLastMonth, 0, '.', ',') }}</p>
                        </div>
                    </div>
                    @if($spentLastMonth > 0)
                        @php
                            $diffPct = round((($spentThisMonth - $spentLastMonth) / $spentLastMonth) * 100);
                        @endphp
                        <div class="mt-3 flex items-center gap-1.5">
                            @if($diffPct > 0)
                                <x-icon name="trending-up" class="w-4 h-4 text-error" />
                                <span class="text-sm font-semibold text-error">+{{ $diffPct }}%</span>
                            @elseif($diffPct < 0)
                                <x-icon name="trending-down" class="w-4 h-4 text-emerald-600" />
                                <span class="text-sm font-semibold text-emerald-600">{{ $diffPct }}%</span>
                            @else
                                <span class="text-sm font-semibold text-on-surface-variant">0%</span>
                            @endif
                            <span class="text-xs text-on-surface-variant">{{ __('messages.percent_vs_last_month') }}</span>
                        </div>
                    @endif
                </x-card>
            @endif
        </div>
    </div>

    {{-- If we have weekly budget, show compare section separately --}}
    @if($budgetWeeklyAmount !== null && $spentThisWeek !== null)
        <x-card>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center">
                    <x-icon name="trending-up" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant">{{ __('messages.compare_last_month') }}</p>
                </div>
            </div>
            <div class="flex items-baseline gap-6">
                <div>
                    <p class="text-xs text-on-surface-variant">{{ __('messages.this_month') }}</p>
                    <p class="text-lg font-display font-bold text-on-surface">{{ number_format($spentThisMonth, 0, '.', ',') }}</p>
                </div>
                <div>
                    <p class="text-xs text-on-surface-variant">{{ __('messages.last_month') }}</p>
                    <p class="text-lg font-display font-bold text-on-surface">{{ number_format($spentLastMonth, 0, '.', ',') }}</p>
                </div>
            </div>
            @if($spentLastMonth > 0)
                @php
                    $diffPct = round((($spentThisMonth - $spentLastMonth) / $spentLastMonth) * 100);
                @endphp
                <div class="mt-3 flex items-center gap-1.5">
                    @if($diffPct > 0)
                        <x-icon name="trending-up" class="w-4 h-4 text-error" />
                        <span class="text-sm font-semibold text-error">+{{ $diffPct }}%</span>
                    @elseif($diffPct < 0)
                        <x-icon name="trending-down" class="w-4 h-4 text-emerald-600" />
                        <span class="text-sm font-semibold text-emerald-600">{{ $diffPct }}%</span>
                    @else
                        <span class="text-sm font-semibold text-on-surface-variant">0%</span>
                    @endif
                    <span class="text-xs text-on-surface-variant">{{ __('messages.percent_vs_last_month') }}</span>
                </div>
            @endif
        </x-card>
    @endif

    {{-- Stats Grid --}}
    @php
        $daysInMonth = (int) now()->format('j');
        $dailyAvg = $daysInMonth > 0 ? round($spentThisMonth / $daysInMonth) : 0;

        $topCategoryName = '---';
        if ($pieLabels->isNotEmpty() && $pieValues->isNotEmpty()) {
            $maxIdx = $pieValues->search($pieValues->max());
            $topCategoryName = $pieLabels->get($maxIdx, '---');
        }
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card
            :label="__('messages.monthly_spending')"
            :value="number_format($spentThisMonth, 0, '.', ',')"
            icon="dollar-sign"
            color="primary"
        />
        <x-stat-card
            :label="__('messages.top_category')"
            :value="$topCategoryName"
            icon="tag"
            color="tertiary"
        />
        <x-stat-card
            :label="__('messages.daily_average')"
            :value="number_format($dailyAvg, 0, '.', ',')"
            icon="chart-bar"
            color="secondary"
        />
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Doughnut chart: Category breakdown --}}
        <x-card>
            <h2 class="font-display text-base font-bold text-on-surface mb-4">{{ __('messages.spending_by_category') }}</h2>
            @if($pieLabels->isNotEmpty())
                <div class="h-64 flex items-center justify-center">
                    <canvas id="chart-pie"></canvas>
                </div>
            @else
                <div class="h-64 flex items-center justify-center">
                    <div class="text-center">
                        <div class="w-12 h-12 rounded-2xl bg-surface-container mx-auto mb-3 flex items-center justify-center">
                            <x-icon name="receipt" class="w-6 h-6 text-on-surface-variant" />
                        </div>
                        <p class="text-sm text-on-surface-variant">{{ __('messages.no_expenses_start') }}</p>
                    </div>
                </div>
            @endif
        </x-card>

        {{-- Bar chart: Last 7 days --}}
        <x-card>
            <h2 class="font-display text-base font-bold text-on-surface mb-4">{{ __('messages.last_7_days') }}</h2>
            <div class="h-64 flex items-center justify-center">
                <canvas id="chart-bar" class="w-full"></canvas>
            </div>
        </x-card>
    </div>

    {{-- Recent Expenses --}}
    <x-card class="!p-0 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4" style="border-bottom: 1px solid rgba(191,201,200,0.15);">
            <h2 class="font-display text-base font-bold text-on-surface">{{ __('messages.recent_expenses') }}</h2>
            <x-btn variant="ghost" size="sm" :href="route('expenses.index')">
                {{ __('messages.view_all') }}
            </x-btn>
        </div>
        @if($recentExpenses->isNotEmpty())
            <div class="divide-y divide-surface-container">
                @foreach($recentExpenses as $expense)
                    <div class="flex items-center gap-4 px-6 py-3.5 hover:bg-surface-container-low/50 transition-colors">
                        {{-- Category icon --}}
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-lg"
                             style="background-color: {{ ($expense->category?->color ?? '#B8B8B8') }}15;">
                            @if($expense->category?->icon)
                                <span>{{ $expense->category->icon }}</span>
                            @else
                                <x-icon name="receipt" class="w-5 h-5" style="color: {{ $expense->category?->color ?? '#B8B8B8' }};" />
                            @endif
                        </div>
                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-on-surface truncate">
                                {{ $expense->category?->name ?? __('messages.other_category') }}
                            </p>
                            <p class="text-xs text-on-surface-variant">
                                {{ $expense->date->format('d/m/Y') }}
                                @if($expense->note)
                                    &middot; {{ Str::limit($expense->note, 30) }}
                                @endif
                            </p>
                        </div>
                        {{-- Amount + edit --}}
                        <div class="text-right shrink-0">
                            <p class="text-sm font-semibold text-on-surface">{{ number_format($expense->amount, 0, '.', ',') }}</p>
                            <a href="{{ route('expenses.edit', $expense) }}" class="text-xs text-primary hover:text-primary-container transition-colors">{{ __('messages.edit') }}</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-surface-container mx-auto mb-3 flex items-center justify-center">
                    <x-icon name="receipt" class="w-6 h-6 text-on-surface-variant" />
                </div>
                <p class="text-sm text-on-surface-variant">{{ __('messages.no_expenses_yet') }}</p>
            </div>
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
                        datasets: [{
                            data: @json($pieValues),
                            backgroundColor: @json($pieColors),
                            borderWidth: 0,
                            hoverOffset: 6,
                        }]
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
                                        font: { family: 'Inter', size: 12 },
                                        color: '#3f4948'
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
                        borderSkipped: false,
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
                                ticks: {
                                    font: { family: 'Inter', size: 11 },
                                    color: '#3f4948'
                                }
                            },
                            y: {
                                grid: { color: 'rgba(191,201,200,0.15)' },
                                ticks: {
                                    font: { family: 'Inter', size: 11 },
                                    color: '#3f4948'
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }
        initBar();
    })();
</script>
<script>
window.__aiI18n = {
    error_generic: @json(__('messages.error_generic')),
    cannot_analyze: @json(__('messages.cannot_analyze_now')),
    connection_error: @json(__('messages.connection_error')),
    generic_tips: @json(__('messages.generic_tips'))
};
(function() {
    const modal = document.getElementById('ai-modal');
    const btn = document.getElementById('ai-analyze-btn');
    const closeBtn = document.getElementById('ai-modal-close');
    const loadingEl = document.getElementById('ai-loading');
    const errorEl = document.getElementById('ai-error');
    const tipsEl = document.getElementById('ai-tips');
    const resultEl = document.getElementById('ai-result');
    const i18n = window.__aiI18n || {};

    function showLoading() {
        loadingEl.classList.remove('hidden');
        errorEl.classList.add('hidden');
        tipsEl.classList.add('hidden');
        resultEl.innerHTML = '';
    }
    function showResult(html) {
        loadingEl.classList.add('hidden');
        errorEl.classList.add('hidden');
        tipsEl.classList.add('hidden');
        resultEl.innerHTML = html;
    }
    function showError(err, tips) {
        loadingEl.classList.add('hidden');
        errorEl.classList.remove('hidden');
        errorEl.textContent = err || i18n.error_generic || 'Error';
        if (tips) {
            tipsEl.classList.remove('hidden');
            tipsEl.textContent = tips;
        } else {
            tipsEl.classList.add('hidden');
        }
        resultEl.innerHTML = '';
    }

    if (btn && modal) {
        btn.addEventListener('click', function() {
            modal.showModal();
            showLoading();
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch('{{ route("ai.analyze") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token || ''
                }
            })
            .then(function(res) { return res.json().then(function(data) { return { ok: res.ok, status: res.status, data: data }; }); })
            .then(function(r) {
                if (r.ok && r.data.analysis) {
                    var content = r.data.analysis;
                    if (typeof window.marked !== 'undefined') {
                        var p = window.marked.parse(content);
                        if (p && typeof p.then === 'function') {
                            p.then(function(html) { showResult(html); });
                        } else {
                            showResult(p);
                        }
                    } else {
                        showResult(content.replace(/\n/g, '<br>'));
                    }
                } else {
                    showError(r.data.error || i18n.cannot_analyze, r.data.tips);
                }
            })
            .catch(function() {
                showError(i18n.connection_error, i18n.generic_tips);
            });
        });
    }
    if (closeBtn && modal) {
        closeBtn.addEventListener('click', function() { modal.close(); });
    }
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) modal.close();
        });
    }
})();
</script>
@endpush
@endsection
