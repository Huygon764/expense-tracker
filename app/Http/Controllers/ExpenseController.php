<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Expense::where('user_id', auth()->id())
            ->with('category')
            ->orderBy('date', 'desc');

        $period = $request->string('period')->toString();
        if ($period !== '') {
            $range = $this->periodToDateRange($period);
            if ($range) {
                $query->whereBetween('date', $range);
            }
        }

        $search = $request->string('search')->trim();
        if ($search !== '') {
            $query->where('note', 'like', '%'.$search.'%');
        }

        $expenses = $query->paginate(15)->withQueryString();

        return view('expenses.index', compact('expenses'));
    }

    public function create(): View
    {
        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();

        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('user_id', auth()->id())],
            'note' => ['nullable', 'string', 'max:65535'],
            'date' => ['required', 'date'],
        ]);

        Expense::create([
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'note' => $validated['note'] ?? null,
            'date' => $validated['date'],
        ]);

        return redirect()->route('expenses.index')->with('status', 'Đã tạo chi tiêu.');
    }

    public function edit(Expense $expense): View|RedirectResponse
    {
        if ($expense->user_id !== auth()->id()) {
            abort(403);
        }
        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        if ($expense->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('user_id', auth()->id())],
            'note' => ['nullable', 'string', 'max:65535'],
            'date' => ['required', 'date'],
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('status', 'Đã cập nhật chi tiêu.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        if ($expense->user_id !== auth()->id()) {
            abort(403);
        }
        $expense->delete();

        return redirect()->route('expenses.index')->with('status', 'Đã xóa chi tiêu.');
    }

    /**
     * @return array{0: string, 1: string}|null [start, end] Y-md or null if invalid
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
}
