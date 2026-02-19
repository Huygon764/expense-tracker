<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\DefaultCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function step1(): View|RedirectResponse
    {
        if (auth()->user()->onboarding_completed_at !== null) {
            return redirect()->route('dashboard');
        }
        $defaultCategories = DefaultCategory::orderBy('sort_order')->get();

        return view('onboarding.index', [
            'step' => 1,
            'defaultCategories' => $defaultCategories,
        ]);
    }

    public function storeStep1(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'default_category_ids' => ['required', 'array', 'min:1'],
            'default_category_ids.*' => ['required', 'integer', 'exists:default_categories,id'],
        ]);

        $defaults = DefaultCategory::whereIn('id', $validated['default_category_ids'])->orderBy('sort_order')->get();
        $userId = auth()->id();

        foreach ($defaults as $default) {
            Category::create([
                'user_id' => $userId,
                'name' => $default->name,
                'icon' => $default->icon,
                'color' => $default->color,
            ]);
        }

        return redirect()->route('onboarding.step2');
    }

    public function step2(): View|RedirectResponse
    {
        if (auth()->user()->onboarding_completed_at !== null) {
            return redirect()->route('dashboard');
        }
        return view('onboarding.index', [
            'step' => 2,
            'user' => auth()->user(),
        ]);
    }

    public function storeStep2(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'monthly_income' => ['nullable', 'numeric', 'min:0'],
        ]);

        auth()->user()->update([
            'monthly_income' => $validated['monthly_income'] ?? null,
        ]);

        return redirect()->route('onboarding.step3');
    }

    public function step3(): View|RedirectResponse
    {
        if (auth()->user()->onboarding_completed_at !== null) {
            return redirect()->route('dashboard');
        }
        return view('onboarding.index', [
            'step' => 3,
        ]);
    }

    public function storeStep3(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:weekly,monthly'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $user = auth()->user();
        $existing = Budget::where('user_id', $user->id)
            ->whereNull('category_id')
            ->where('type', $validated['type'])
            ->exists();

        if ($existing) {
            return back()->withErrors(['type' => 'You already have a total budget for this period.']);
        }

        Budget::create([
            'user_id' => $user->id,
            'category_id' => null,
            'amount' => $validated['amount'],
            'type' => $validated['type'],
        ]);

        $user->update(['onboarding_completed_at' => now()]);

        return redirect()->route('dashboard')->with('status', 'Onboarding completed. Welcome!');
    }
}
