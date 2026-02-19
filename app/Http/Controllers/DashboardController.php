<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();
        $lastMonthStart = Carbon::now()->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->copy()->subMonth()->endOfMonth();

        $budgetMonthly = Budget::where('user_id', $userId)
            ->whereNull('category_id')
            ->where('type', 'monthly')
            ->first();

        $spentThisMonth = (float) Expense::where('user_id', $userId)
            ->whereBetween('date', [$startMonth, $endMonth])
            ->sum('amount');

        $spentLastMonth = (float) Expense::where('user_id', $userId)
            ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        $budgetWeekly = null;
        $spentThisWeek = null;
        $budgetWeeklyAmount = null;
        $budgetWeekly = Budget::where('user_id', $userId)
            ->whereNull('category_id')
            ->where('type', 'weekly')
            ->first();
        if ($budgetWeekly) {
            $budgetWeeklyAmount = (float) $budgetWeekly->amount;
            $startWeek = Carbon::now()->startOfWeek();
            $endWeek = Carbon::now()->endOfWeek();
            $spentThisWeek = (float) Expense::where('user_id', $userId)
                ->whereBetween('date', [$startWeek, $endWeek])
                ->sum('amount');
        }

        $alertMessage = null;
        if ($budgetMonthly && $budgetMonthly->amount > 0) {
            $ratio = $spentThisMonth / (float) $budgetMonthly->amount;
            if ($ratio >= 1) {
                $alertMessage = 'Đã vượt ngân sách!';
            } elseif ($ratio >= 0.8) {
                $alertMessage = 'Đã dùng 80% ngân sách';
            } elseif ($ratio >= 0.5) {
                $alertMessage = 'Đã dùng 50% ngân sách';
            }
        }

        $pieRows = Expense::where('user_id', $userId)
            ->whereBetween('date', [$startMonth, $endMonth])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->get();

        $categoryIds = $pieRows->pluck('category_id')->unique()->filter()->values()->all();
        $categories = $categoryIds ? Category::whereIn('id', $categoryIds)->get()->keyBy('id') : collect();

        $pieLabels = $pieRows->map(fn ($row) => $row->category_id
            ? ($categories->get($row->category_id)?->name ?? 'Khác')
            : 'Khác');
        $pieValues = $pieRows->pluck('total')->map(fn ($v) => (float) $v)->values();
        $pieColors = $pieRows->map(fn ($row) => $row->category_id
            ? ($categories->get($row->category_id)?->color ?? '#B8B8B8')
            : '#B8B8B8')->values();

        $barDays = collect();
        for ($i = 6; $i >= 0; $i--) {
            $barDays->push(Carbon::today()->subDays($i));
        }
        $dayNames = ['T8', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
        $barLabels = $barDays->map(fn ($d) => $d->format('d/m') . ' (' . $dayNames[$d->dayOfWeek] . ')')->values();
        $barValues = $barDays->map(function ($d) use ($userId) {
            return (float) Expense::where('user_id', $userId)
                ->where('date', $d->toDateString())
                ->sum('amount');
        })->values();

        $recentExpenses = Expense::where('user_id', $userId)
            ->with('category')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', [
            'budgetMonthly' => $budgetMonthly,
            'budgetMonthlyAmount' => $budgetMonthly ? (float) $budgetMonthly->amount : null,
            'spentThisMonth' => $spentThisMonth,
            'spentLastMonth' => $spentLastMonth,
            'budgetWeekly' => $budgetWeekly,
            'budgetWeeklyAmount' => $budgetWeeklyAmount,
            'spentThisWeek' => $spentThisWeek,
            'alertMessage' => $alertMessage,
            'pieLabels' => $pieLabels,
            'pieValues' => $pieValues,
            'pieColors' => $pieColors,
            'barLabels' => $barLabels,
            'barValues' => $barValues,
            'recentExpenses' => $recentExpenses,
        ]);
    }
}
