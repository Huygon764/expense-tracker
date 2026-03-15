<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $query = User::where('id', '!=', Auth::id())
            ->where('role', '!=', 'admin');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $totalUsers = User::where('role', '!=', 'admin')->count();
        $activeUsers = User::where('role', '!=', 'admin')->where('is_active', true)->count();
        $disabledUsers = $totalUsers - $activeUsers;

        return view('admin.users.index', compact('users', 'totalUsers', 'activeUsers', 'disabledUsers', 'search'));
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', __('messages.admin_cannot_toggle_self'));
        }

        if ($user->role === 'admin') {
            return back()->with('error', __('messages.admin_cannot_toggle_admin'));
        }

        $user->update(['is_active' => ! $user->is_active]);

        $action = $user->is_active ? __('messages.admin_status_enabled') : __('messages.admin_status_disabled');

        return back()->with('success', __('messages.admin_user_status_changed', ['name' => $user->name, 'action' => $action]));
    }
}
