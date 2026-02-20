<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(Request $request): View
    {
        $userId = Auth::id();
        [$dateFrom, $dateTo, $periodType] = $this->resolveDateRange($request);

        $total = (float) Expense::where('user_id', $userId)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->sum('amount');

        [$previousFrom, $previousTo] = $this->previousPeriodRange($dateFrom, $dateTo, $periodType);
        $previousTotal = (float) Expense::where('user_id', $userId)
            ->whereBetween('date', [$previousFrom, $previousTo])
            ->sum('amount');

        $diffAmount = $total - $previousTotal;
        $diffPercent = $previousTotal > 0
            ? round(($diffAmount / $previousTotal) * 100)
            : ($total > 0 ? 100 : 0);

        $pieRows = Expense::where('user_id', $userId)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->get();

        $categoryIds = $pieRows->pluck('category_id')->unique()->filter()->values()->all();
        $categories = $categoryIds ? Category::whereIn('id', $categoryIds)->get()->keyBy('id') : collect();

        $byCategory = $pieRows->map(function ($row) use ($categories) {
            $name = $row->category_id
                ? ($categories->get($row->category_id)?->name ?? 'Khác')
                : 'Khác';
            $color = $row->category_id
                ? ($categories->get($row->category_id)?->color ?? '#B8B8B8')
                : '#B8B8B8';
            return [
                'category_name' => $name,
                'total' => (float) $row->total,
                'color' => $color,
            ];
        })->sortByDesc('total')->values();

        $pieLabels = $byCategory->pluck('category_name')->values();
        $pieValues = $byCategory->pluck('total')->values();
        $pieColors = $byCategory->pluck('color')->values();

        $topCategories = $byCategory->take(5)->map(function ($row) use ($total) {
            $percent = $total > 0 ? round($row['total'] / $total * 100) : 0;
            return [
                'category_name' => $row['category_name'],
                'total' => $row['total'],
                'percent' => $percent,
            ];
        })->values()->all();

        [$barLabels, $barValues] = $this->buildBarData($userId, $dateFrom, $dateTo, $periodType);

        $expenses = Expense::where('user_id', $userId)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->with('category')
            ->orderBy('date', 'desc')
            ->paginate(15)
            ->withQueryString();

        $daysInRange = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1;
        $avgPerDay = $daysInRange > 0 ? $total / $daysInRange : 0;

        return view('statistics.index', [
            'period' => $periodType,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'total' => $total,
            'previousTotal' => $previousTotal,
            'diffAmount' => $diffAmount,
            'diffPercent' => $diffPercent,
            'pieLabels' => $pieLabels,
            'pieValues' => $pieValues,
            'pieColors' => $pieColors,
            'topCategories' => $topCategories,
            'barLabels' => $barLabels,
            'barValues' => $barValues,
            'expenses' => $expenses,
            'avgPerDay' => $avgPerDay,
            'daysInRange' => $daysInRange,
        ]);
    }

    /**
     * @return array{0: string, 1: string, 2: string} [date_from, date_to, periodType]
     */
    private function resolveDateRange(Request $request): array
    {
        $period = $request->query('period', 'month');
        if (in_array($period, ['today', 'week', 'month', 'year'], true)) {
            $range = $this->periodToDateRange($period);
            if ($range) {
                return [$range[0], $range[1], $period];
            }
        }

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        if ($dateFrom && $dateTo) {
            $from = Carbon::parse($dateFrom);
            $to = Carbon::parse($dateTo);
            if ($from->lte($to)) {
                return [$from->format('Y-m-d'), $to->format('Y-m-d'), 'custom'];
            }
        }

        $default = $this->periodToDateRange('month');
        return [$default[0], $default[1], 'month'];
    }

    /**
     * @return array{0: string, 1: string}|null
     */
    private function periodToDateRange(string $period): ?array
    {
        $today = Carbon::today();
        return match ($period) {
            'today' => [$today->format('Y-m-d'), $today->format('Y-m-d')],
            'week' => [
                $today->copy()->startOfWeek()->format('Y-m-d'),
                $today->copy()->endOfWeek()->format('Y-m-d'),
            ],
            'month' => [
                $today->copy()->startOfMonth()->format('Y-m-d'),
                $today->copy()->endOfMonth()->format('Y-m-d'),
            ],
            'year' => [
                $today->copy()->startOfYear()->format('Y-m-d'),
                $today->copy()->endOfYear()->format('Y-m-d'),
            ],
            default => null,
        };
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function previousPeriodRange(string $dateFrom, string $dateTo, string $periodType): array
    {
        $from = Carbon::parse($dateFrom);
        $to = Carbon::parse($dateTo);
        $days = $from->diffInDays($to) + 1;

        if ($periodType === 'custom') {
            $prevEnd = $from->copy()->subDay();
            $prevStart = $prevEnd->copy()->subDays($days - 1);
            return [$prevStart->format('Y-m-d'), $prevEnd->format('Y-m-d')];
        }

        return match ($periodType) {
            'today' => [
                $from->copy()->subDay()->format('Y-m-d'),
                $from->copy()->subDay()->format('Y-m-d'),
            ],
            'week' => [
                $from->copy()->subWeek()->format('Y-m-d'),
                $to->copy()->subWeek()->format('Y-m-d'),
            ],
            'month' => [
                $from->copy()->subMonth()->format('Y-m-d'),
                $to->copy()->subMonth()->format('Y-m-d'),
            ],
            'year' => [
                $from->copy()->subYear()->format('Y-m-d'),
                $to->copy()->subYear()->format('Y-m-d'),
            ],
            default => [
                $from->copy()->subDays($days)->format('Y-m-d'),
                $from->copy()->subDay()->format('Y-m-d'),
            ],
        };
    }

    /**
     * @return array{0: \Illuminate\Support\Collection, 1: \Illuminate\Support\Collection}
     */
    private function buildBarData(int $userId, string $dateFrom, string $dateTo, string $periodType): array
    {
        $from = Carbon::parse($dateFrom);
        $to = Carbon::parse($dateTo);
        $dayNames = ['T8', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];

        if ($periodType === 'today') {
            $total = (float) Expense::where('user_id', $userId)
                ->where('date', $from->toDateString())
                ->sum('amount');
            return [collect(['Hôm nay']), collect([$total])];
        }

        if ($periodType === 'week') {
            $labels = collect();
            $values = collect();
            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                $labels->push($d->format('d/m') . ' (' . $dayNames[$d->dayOfWeek] . ')');
                $values->push((float) Expense::where('user_id', $userId)
                    ->where('date', $d->toDateString())
                    ->sum('amount'));
            }
            return [$labels, $values];
        }

        if ($periodType === 'month' || ($periodType === 'custom' && $from->diffInDays($to) < 31)) {
            $labels = collect();
            $values = collect();
            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                $labels->push($d->format('d'));
                $values->push((float) Expense::where('user_id', $userId)
                    ->where('date', $d->toDateString())
                    ->sum('amount'));
            }
            return [$labels, $values];
        }

        if ($periodType === 'year') {
            $labels = collect();
            $values = collect();
            $year = $from->year;
            for ($m = 1; $m <= 12; $m++) {
                $start = Carbon::createFromDate($year, $m, 1)->startOfMonth();
                $end = Carbon::createFromDate($year, $m, 1)->endOfMonth();
                if ($start->lt($from)) {
                    $start = $from->copy();
                }
                if ($end->gt($to)) {
                    $end = $to->copy();
                }
                $labels->push('T' . $m);
                $values->push((float) Expense::where('user_id', $userId)
                    ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                    ->sum('amount'));
            }
            return [$labels, $values];
        }

        // custom long range: by day, max 31 points (e.g. last 31 days)
        $days = $from->diffInDays($to) + 1;
        if ($days > 31) {
            $step = (int) ceil($days / 31);
            $labels = collect();
            $values = collect();
            for ($i = 0; $i < $days; $i += $step) {
                $d = $from->copy()->addDays($i);
                if ($d->gt($to)) {
                    break;
                }
                $labels->push($d->format('d/m'));
                $values->push((float) Expense::where('user_id', $userId)
                    ->where('date', $d->toDateString())
                    ->sum('amount'));
            }
            return [$labels, $values];
        }

        $labels = collect();
        $values = collect();
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $labels->push($d->format('d/m'));
            $values->push((float) Expense::where('user_id', $userId)
                ->where('date', $d->toDateString())
                ->sum('amount'));
        }
        return [$labels, $values];
    }
}
