<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <nav class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-lg font-semibold">{{ config('app.name') }}</a>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                    <details class="relative group">
                        <summary class="relative list-none cursor-pointer p-1 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 [&::-webkit-details-marker]:hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m-6 0H9" /></svg>
                            @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-medium text-white">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                            @endif
                        </summary>
                        <div class="absolute right-0 z-50 mt-1 w-80 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-600">
                            <div class="py-1 max-h-96 overflow-y-auto">
                                @forelse(isset($recentNotifications) ? $recentNotifications : [] as $notif)
                                    <div class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                        <p>{{ $notif->message }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                @empty
                                    <p class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">Chưa có thông báo.</p>
                                @endforelse
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 py-1">
                                <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="px-3 py-1">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="block w-full text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 px-2 py-1 rounded">Đánh dấu tất cả đã đọc</button>
                                </form>
                                <a href="{{ route('notifications.index') }}" class="block px-3 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700">Xem tất cả</a>
                            </div>
                        </div>
                    </details>
                    @endauth
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Dashboard</a>
                    <a href="{{ route('expenses.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Expenses</a>
                    <a href="{{ route('budgets.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Budgets</a>
                    <a href="{{ route('recurring-expenses.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Recurring</a>
                    <a href="{{ route('savings-goals.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Savings</a>
                    <a href="{{ route('reports.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Báo cáo</a>
                    <a href="{{ route('statistics.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Thống kê</a>
                    <a href="{{ route('categories.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Categories</a>
                    <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/20 p-4 text-sm text-green-800 dark:text-green-200">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-md bg-red-50 dark:bg-red-900/20 p-4 text-sm text-red-800 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
