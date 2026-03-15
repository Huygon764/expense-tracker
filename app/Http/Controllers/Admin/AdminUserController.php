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
            return back()->with('error', 'Không thể thay đổi trạng thái tài khoản của chính mình.');
        }

        if ($user->role === 'admin') {
            return back()->with('error', 'Không thể thay đổi trạng thái tài khoản admin.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'kích hoạt' : 'vô hiệu hóa';

        return back()->with('success', "Đã {$status} tài khoản {$user->name}.");
    }
}
