<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\SavingsGoal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavingsGoalController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $goals = $user->savingsGoals()->orderBy('deadline')->get();

        $expensesByMonth = Expense::where('user_id', $user->id)
            ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(amount) as total')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->get()
            ->keyBy(fn ($e) => "{$e->year}-{$e->month}");

        foreach ($goals as $goal) {
            $goal->setAttribute('current_amount', $goal->computeCurrentAmountFromMap($expensesByMonth));
        }

        $showIncomeWarning = $user->monthly_income === null;

        return view('savings-goals.index', compact('goals', 'showIncomeWarning'));
    }

    public function create(): View
    {
        return view('savings-goals.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'deadline' => ['required', 'date', 'after_or_equal:today'],
        ]);

        SavingsGoal::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'target_amount' => $validated['target_amount'],
            'deadline' => $validated['deadline'],
        ]);

        return redirect()->route('savings-goals.index')->with('status', 'Đã tạo mục tiêu tiết kiệm.');
    }

    public function edit(SavingsGoal $savingsGoal): View|RedirectResponse
    {
        if ($savingsGoal->user_id !== auth()->id()) {
            abort(403);
        }

        return view('savings-goals.edit', compact('savingsGoal'));
    }

    public function update(Request $request, SavingsGoal $savingsGoal): RedirectResponse
    {
        if ($savingsGoal->user_id !== auth()->id()) {
            abort(403);
        }
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'deadline' => ['required', 'date'],
        ]);

        $savingsGoal->update($validated);

        return redirect()->route('savings-goals.index')->with('status', 'Đã cập nhật mục tiêu tiết kiệm.');
    }

    public function destroy(SavingsGoal $savingsGoal): RedirectResponse
    {
        if ($savingsGoal->user_id !== auth()->id()) {
            abort(403);
        }
        $savingsGoal->delete();

        return redirect()->route('savings-goals.index')->with('status', 'Đã xóa mục tiêu tiết kiệm.');
    }
}
