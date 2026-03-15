<?php

namespace App\Http\Controllers;

use App\Models\SavingsDeposit;
use App\Models\SavingsGoal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SavingsGoalController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $goals = $user->savingsGoals()
            ->withSum('deposits', 'amount')
            ->orderBy('deadline')
            ->get();

        return view('savings-goals.index', compact('goals'));
    }

    public function create(): View
    {
        return view('savings-goals.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'deadline' => ['required', 'date', 'after_or_equal:today'],
        ]);

        SavingsGoal::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'target_amount' => $validated['target_amount'],
            'deadline' => $validated['deadline'],
        ]);

        return redirect()->route('savings-goals.index')->with('status', 'Đã tạo mục tiêu tiết kiệm.');
    }

    public function edit(SavingsGoal $savingsGoal): View|RedirectResponse
    {
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }

        return view('savings-goals.edit', compact('savingsGoal'));
    }

    public function update(Request $request, SavingsGoal $savingsGoal): RedirectResponse
    {
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'deadline' => ['required', 'date'],
        ]);

        $savingsGoal->update($validated);

        return redirect()->route('savings-goals.index')->with('status', 'Đã cập nhật mục tiêu tiết kiệm.');
    }

    public function destroy(SavingsGoal $savingsGoal): RedirectResponse
    {
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }
        $savingsGoal->delete();

        return redirect()->route('savings-goals.index')->with('status', 'Đã xóa mục tiêu tiết kiệm.');
    }

    public function deposits(SavingsGoal $savingsGoal): View
    {
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $deposits = $savingsGoal->deposits()->orderByDesc('date')->orderByDesc('id')->paginate(15);

        return view('savings-goals.deposits', compact('savingsGoal', 'deposits'));
    }

    public function storeDeposit(Request $request, SavingsGoal $savingsGoal): RedirectResponse
    {
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
        ]);

        $savingsGoal->deposits()->create($validated);

        return redirect()->route('savings-goals.deposits', $savingsGoal)->with('status', 'Đã thêm khoản nạp.');
    }

    public function destroyDeposit(SavingsDeposit $deposit): RedirectResponse
    {
        $goal = $deposit->savingsGoal;
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $deposit->delete();

        return redirect()->route('savings-goals.deposits', $goal)->with('status', 'Đã xóa khoản nạp.');
    }
}
