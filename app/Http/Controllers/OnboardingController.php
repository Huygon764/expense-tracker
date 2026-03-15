<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\DefaultCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function step1(): View|RedirectResponse
    {
        if (Auth::user()->onboarding_completed_at !== null) {
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
        $userId = Auth::id();

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
        if (Auth::user()->onboarding_completed_at !== null) {
            return redirect()->route('dashboard');
        }
        return view('onboarding.index', [
            'step' => 2,
        ]);
    }

    public function storeStep2(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:weekly,monthly'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $existing = Budget::where('user_id', $user->id)
            ->where('type', $validated['type'])
            ->exists();

        if ($existing) {
            return back()->withErrors(['type' => 'You already have a total budget for this period.']);
        }

        Budget::create([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'type' => $validated['type'],
        ]);

        $user->update(['onboarding_completed_at' => now()]);

        return redirect()->route('dashboard')->with('status', __('messages.onboarding_completed'));
    }
}
