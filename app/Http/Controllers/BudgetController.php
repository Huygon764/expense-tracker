<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(): View
    {
        $budgets = Budget::where('user_id', Auth::id())
            ->orderBy('type')
            ->get();

        $budgets->each(function (Budget $budget) {
            $budget->setAttribute('spent', $budget->getSpentInCurrentPeriod());
        });

        return view('budgets.index', compact('budgets'));
    }

    public function create(): View
    {
        return view('budgets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:weekly,monthly'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $userId = Auth::id();

        $exists = Budget::where('user_id', $userId)
            ->where('type', $validated['type'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['type' => 'You already have a total budget for this period.'])->withInput();
        }

        Budget::create([
            'user_id' => $userId,
            'amount' => $validated['amount'],
            'type' => $validated['type'],
        ]);

        return redirect()->route('budgets.index')->with('status', 'Đã tạo ngân sách.');
    }

    public function edit(Budget $budget): View|RedirectResponse
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        return view('budgets.edit', compact('budget'));
    }

    public function update(Request $request, Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => ['required', 'in:weekly,monthly'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $exists = Budget::where('user_id', Auth::id())
            ->where('type', $validated['type'])
            ->where('id', '!=', $budget->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['type' => 'You already have a total budget for this period.'])->withInput();
        }

        $budget->update([
            'amount' => $validated['amount'],
            'type' => $validated['type'],
        ]);

        return redirect()->route('budgets.index')->with('status', 'Đã cập nhật ngân sách.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }
        $budget->delete();

        return redirect()->route('budgets.index')->with('status', 'Đã xóa ngân sách.');
    }
}
