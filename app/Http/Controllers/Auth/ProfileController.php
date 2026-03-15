<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('auth.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email_notification' => ['boolean'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email_notification' => $request->boolean('email_notification'),
        ]);

        return back()->with('status', __('messages.profile_updated'));
    }
}
