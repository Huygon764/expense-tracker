<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(): View
    {
        $budgets = Budget::where('user_id', auth()->id())
            ->with('category')
            ->orderByRaw('category_id IS NULL DESC')
            ->orderBy('type')
            ->get();

        $budgets->each(function (Budget $budget) {
            $budget->setAttribute('spent', $budget->getSpentInCurrentPeriod());
        });

        return view('budgets.index', compact('budgets'));
    }

    public function create(): View
    {
        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();

        return view('budgets.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->input('category_id') === '') {
            $request->merge(['category_id' => null]);
        }
        $validated = $request->validate([
            'type' => ['required', 'in:weekly,monthly'],
            'amount' => ['required', 'numeric', 'min:0'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('user_id', auth()->id())],
        ]);

        $userId = auth()->id();
        $categoryId = $validated['category_id'] ?? null;
        $type = $validated['type'];

        if ($categoryId === null || $categoryId === '') {
            $exists = Budget::where('user_id', $userId)
                ->whereNull('category_id')
                ->where('type', $type)
                ->exists();
            if ($exists) {
                return back()->withErrors(['type' => 'You already have a total budget for this period.'])->withInput();
            }
        } else {
            $exists = Budget::where('user_id', $userId)
                ->where('category_id', $categoryId)
                ->where('type', $type)
                ->exists();
            if ($exists) {
                return back()->withErrors(['category_id' => 'You already have a budget for this category and period.'])->withInput();
            }
        }

        Budget::create([
            'user_id' => $userId,
            'category_id' => $categoryId ?: null,
            'amount' => $validated['amount'],
            'type' => $type,
        ]);

        return redirect()->route('budgets.index')->with('status', 'Budget created.');
    }

    public function edit(Budget $budget): View|RedirectResponse
    {
        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }
        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();

        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }
        if ($request->input('category_id') === '') {
            $request->merge(['category_id' => null]);
        }
        $validated = $request->validate([
            'type' => ['required', 'in:weekly,monthly'],
            'amount' => ['required', 'numeric', 'min:0'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('user_id', auth()->id())],
        ]);

        $userId = auth()->id();
        $categoryId = isset($validated['category_id']) && $validated['category_id'] !== '' ? $validated['category_id'] : null;
        $type = $validated['type'];

        if ($categoryId === null) {
            $exists = Budget::where('user_id', $userId)
                ->whereNull('category_id')
                ->where('type', $type)
                ->where('id', '!=', $budget->id)
                ->exists();
            if ($exists) {
                return back()->withErrors(['type' => 'You already have a total budget for this period.'])->withInput();
            }
        } else {
            $exists = Budget::where('user_id', $userId)
                ->where('category_id', $categoryId)
                ->where('type', $type)
                ->where('id', '!=', $budget->id)
                ->exists();
            if ($exists) {
                return back()->withErrors(['category_id' => 'You already have a budget for this category and period.'])->withInput();
            }
        }

        $budget->update([
            'category_id' => $categoryId,
            'amount' => $validated['amount'],
            'type' => $type,
        ]);

        return redirect()->route('budgets.index')->with('status', 'Budget updated.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }
        $budget->delete();

        return redirect()->route('budgets.index')->with('status', 'Budget deleted.');
    }
}
