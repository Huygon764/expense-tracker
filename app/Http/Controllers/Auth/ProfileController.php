<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('auth.profile', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'email_notification' => ['boolean'],
            'monthly_income' => ['nullable', 'numeric', 'min:0'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'email_notification' => $request->boolean('email_notification'),
            'monthly_income' => $validated['monthly_income'] ?? null,
        ]);

        return back()->with('status', 'Đã cập nhật hồ sơ.');
    }
}
