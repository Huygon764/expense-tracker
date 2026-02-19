<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\RecurringExpense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RecurringExpenseController extends Controller
{
    public function index(): View
    {
        $recurringExpenses = RecurringExpense::where('user_id', auth()->id())
            ->with('category')
            ->orderBy('title')
            ->get();

        return view('recurring-expenses.index', compact('recurringExpenses'));
    }

    public function create(): View
    {
        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();

        return view('recurring-expenses.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->input('category_id') === '') {
            $request->merge(['category_id' => null]);
        }
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('user_id', auth()->id())],
            'type' => ['required', 'in:weekly,monthly'],
            'day_of_week' => ['required_if:type,weekly', 'nullable', 'integer', 'between:0,6'],
            'day_of_month' => ['required_if:type,monthly', 'nullable', 'integer', 'between:1,31'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validated['type'] === 'weekly') {
            $validated['day_of_month'] = null;
        } else {
            $validated['day_of_week'] = null;
        }
        $validated['category_id'] = $validated['category_id'] ?? null;
        $validated['user_id'] = auth()->id();
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        RecurringExpense::create($validated);

        return redirect()->route('recurring-expenses.index')->with('status', 'Recurring expense created.');
    }

    public function edit(RecurringExpense $recurringExpense): View|RedirectResponse
    {
        if ($recurringExpense->user_id !== auth()->id()) {
            abort(403);
        }
        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();

        return view('recurring-expenses.edit', compact('recurringExpense', 'categories'));
    }

    public function update(Request $request, RecurringExpense $recurringExpense): RedirectResponse
    {
        if ($recurringExpense->user_id !== auth()->id()) {
            abort(403);
        }
        if ($request->input('category_id') === '') {
            $request->merge(['category_id' => null]);
        }
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('user_id', auth()->id())],
            'type' => ['required', 'in:weekly,monthly'],
            'day_of_week' => ['required_if:type,weekly', 'nullable', 'integer', 'between:0,6'],
            'day_of_month' => ['required_if:type,monthly', 'nullable', 'integer', 'between:1,31'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validated['type'] === 'weekly') {
            $validated['day_of_month'] = null;
        } else {
            $validated['day_of_week'] = null;
        }
        $validated['category_id'] = $validated['category_id'] ?? null;
        $validated['is_active'] = $request->boolean('is_active');

        $recurringExpense->update($validated);

        return redirect()->route('recurring-expenses.index')->with('status', 'Recurring expense updated.');
    }

    public function destroy(RecurringExpense $recurringExpense): RedirectResponse
    {
        if ($recurringExpense->user_id !== auth()->id()) {
            abort(403);
        }
        $recurringExpense->delete();

        return redirect()->route('recurring-expenses.index')->with('status', 'Recurring expense deleted.');
    }

    public function toggle(RecurringExpense $recurringExpense): RedirectResponse
    {
        if ($recurringExpense->user_id !== auth()->id()) {
            abort(403);
        }
        $recurringExpense->update(['is_active' => ! $recurringExpense->is_active]);

        return back()->with('status', $recurringExpense->is_active ? 'Recurring expense activated.' : 'Recurring expense paused.');
    }
}
