<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.app_name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface text-on-surface font-sans">
    {{-- Desktop Sidebar --}}
    <aside id="sidebar" class="hidden md:flex md:flex-col fixed inset-y-0 left-0 w-64 bg-surface-container z-40">
        {{-- Logo --}}
        <div class="flex items-center h-16 px-6">
            <a href="{{ route('dashboard') }}" class="font-display text-xl font-bold text-primary tracking-tight">
                {{ __('messages.app_name') }}
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @php
                $navItems = [
                    ['route' => 'dashboard', 'label' => __('messages.dashboard'), 'icon' => 'home'],
                    ['route' => 'expenses.index', 'label' => __('messages.expenses'), 'icon' => 'receipt'],
                    ['route' => 'categories.index', 'label' => __('messages.categories'), 'icon' => 'tag'],
                    ['route' => 'budgets.index', 'label' => __('messages.budgets'), 'icon' => 'wallet'],
                    ['route' => 'recurring-expenses.index', 'label' => __('messages.recurring_expenses'), 'icon' => 'repeat'],
                    ['route' => 'savings-goals.index', 'label' => __('messages.savings_goals'), 'icon' => 'piggy-bank'],
                    ['route' => 'statistics.index', 'label' => __('messages.statistics'), 'icon' => 'chart-bar'],
                    ['route' => 'reports.index', 'label' => __('messages.reports'), 'icon' => 'file-text'],
                ];
            @endphp

            @foreach($navItems as $item)
                @php
                    $isActive = request()->routeIs($item['route'] . '*') || request()->routeIs(str_replace('.index', '.*', $item['route']));
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ $isActive
                              ? 'bg-surface-container-lowest text-primary shadow-editorial-sm'
                              : 'text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface' }}">
                    <x-icon :name="$item['icon']" class="w-5 h-5 shrink-0" />
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach

            @auth
            @if(Auth::user()->isAdmin())
                <div class="pt-4 mt-4" style="border-top: 1px solid rgba(191,201,200,0.15);">
                    <p class="px-4 mb-2 text-xs font-semibold uppercase tracking-widest text-on-surface-variant">{{ __('messages.admin') }}</p>
                    @php
                        $adminItems = [
                            ['route' => 'admin.users.index', 'label' => __('messages.manage_users'), 'icon' => 'shield'],
                            ['route' => 'admin.categories.index', 'label' => __('messages.manage_categories'), 'icon' => 'settings'],
                        ];
                    @endphp
                    @foreach($adminItems as $item)
                        @php $isActive = request()->routeIs($item['route'] . '*'); @endphp
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors
                                  {{ $isActive
                                      ? 'bg-surface-container-lowest text-primary shadow-editorial-sm'
                                      : 'text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface' }}">
                            <x-icon :name="$item['icon']" class="w-5 h-5 shrink-0" />
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
            @endauth
        </nav>

        {{-- User section at bottom --}}
        @auth
        <div class="px-3 py-4" style="border-top: 1px solid rgba(191,201,200,0.15);">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-on-primary text-xs font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-on-surface truncate">{{ Auth::user()->name }}</p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">
                    <x-icon name="log-out" class="w-5 h-5 shrink-0" />
                    <span>{{ __('messages.logout') }}</span>
                </button>
            </form>
        </div>
        @endauth
    </aside>

    {{-- Desktop Header --}}
    <header class="hidden md:flex fixed top-0 left-64 right-0 h-16 bg-glass z-30 items-center justify-between px-8">
        <h1 class="font-display text-lg font-semibold text-on-surface">@yield('page-title')</h1>
        <div class="flex items-center gap-4">
            {{-- Language switcher --}}
            <div class="flex items-center rounded-full bg-surface-container p-0.5">
                <a href="{{ route('language.switch', 'en') }}"
                   class="px-3 py-1 text-xs font-semibold rounded-full transition-colors {{ app()->getLocale() === 'en' ? 'bg-surface-container-lowest text-primary shadow-editorial-sm' : 'text-on-surface-variant hover:text-on-surface' }}">EN</a>
                <a href="{{ route('language.switch', 'vi') }}"
                   class="px-3 py-1 text-xs font-semibold rounded-full transition-colors {{ app()->getLocale() === 'vi' ? 'bg-surface-container-lowest text-primary shadow-editorial-sm' : 'text-on-surface-variant hover:text-on-surface' }}">VI</a>
            </div>

            {{-- Notifications --}}
            @auth
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="relative p-2 rounded-xl text-on-surface-variant hover:bg-surface-container-low transition-colors">
                    <x-icon name="bell" class="w-5 h-5" />
                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                        <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-error text-[10px] font-bold text-on-primary">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                    @endif
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
                     class="absolute right-0 mt-2 w-80 rounded-2xl bg-surface-container-lowest shadow-editorial z-50 overflow-hidden" style="display: none;">
                    <div class="py-2 max-h-96 overflow-y-auto">
                        @forelse(isset($recentNotifications) ? $recentNotifications : [] as $notif)
                            <div class="px-4 py-3 hover:bg-surface-container-low transition-colors">
                                <p class="text-sm text-on-surface">{{ $notif->message }}</p>
                                <p class="text-xs text-on-surface-variant mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <p class="px-4 py-6 text-sm text-on-surface-variant text-center">{{ __('messages.no_notifications') }}</p>
                        @endforelse
                    </div>
                    <div class="px-4 py-3 bg-surface-container-low">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('notifications.index') }}" class="text-sm font-medium text-primary hover:text-primary-container transition-colors">{{ __('messages.view_all') }}</a>
                            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-sm text-on-surface-variant hover:text-on-surface transition-colors">{{ __('messages.mark_all_read') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </header>

    {{-- Mobile Header --}}
    <header class="md:hidden flex fixed top-0 left-0 right-0 h-14 bg-glass z-30 items-center justify-between px-4">
        <a href="{{ route('dashboard') }}" class="font-display text-lg font-bold text-primary tracking-tight">
            {{ __('messages.app_name') }}
        </a>
        <div class="flex items-center gap-2">
            {{-- Language switcher mobile --}}
            <div class="flex items-center rounded-full bg-surface-container p-0.5">
                <a href="{{ route('language.switch', 'en') }}"
                   class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ app()->getLocale() === 'en' ? 'bg-surface-container-lowest text-primary' : 'text-on-surface-variant' }}">EN</a>
                <a href="{{ route('language.switch', 'vi') }}"
                   class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ app()->getLocale() === 'vi' ? 'bg-surface-container-lowest text-primary' : 'text-on-surface-variant' }}">VI</a>
            </div>

            @auth
            <a href="{{ route('notifications.index') }}" class="relative p-2 text-on-surface-variant">
                <x-icon name="bell" class="w-5 h-5" />
                @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                    <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-error text-[10px] font-bold text-on-primary">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                @endif
            </a>
            @endauth
        </div>
    </header>

    {{-- Main Content --}}
    <main class="md:ml-64 pt-14 md:pt-16 pb-20 md:pb-8">
        <div class="max-w-7xl mx-auto px-4 md:px-8 py-6">
            @if (session('status'))
                <div class="mb-6 rounded-2xl bg-tertiary-container/50 p-4 text-sm text-on-surface">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('success'))
                <div class="mb-6 rounded-2xl bg-tertiary-container/50 p-4 text-sm text-on-surface">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 rounded-2xl bg-error-container/50 p-4 text-sm text-error">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    {{-- Mobile Bottom Tab Bar --}}
    @auth
    <nav class="md:hidden fixed bottom-0 left-0 right-0 h-16 bg-glass z-40 flex items-center justify-around px-2" style="padding-bottom: env(safe-area-inset-bottom);">
        @php
            $tabs = [
                ['route' => 'dashboard', 'label' => __('messages.dashboard'), 'icon' => 'home'],
                ['route' => 'expenses.index', 'label' => __('messages.expenses'), 'icon' => 'receipt'],
                ['route' => 'budgets.index', 'label' => __('messages.budgets'), 'icon' => 'wallet'],
                ['route' => 'statistics.index', 'label' => __('messages.statistics'), 'icon' => 'chart-bar'],
            ];
        @endphp

        @foreach($tabs as $tab)
            @php $isActive = request()->routeIs($tab['route'] . '*') || request()->routeIs(str_replace('.index', '.*', $tab['route'])); @endphp
            <a href="{{ route($tab['route']) }}"
               class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-colors
                      {{ $isActive ? 'text-primary' : 'text-on-surface-variant' }}">
                <x-icon :name="$tab['icon']" class="w-5 h-5" :solid="$isActive" />
                <span class="text-[10px] font-medium">{{ $tab['label'] }}</span>
            </a>
        @endforeach

        {{-- More menu --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl text-on-surface-variant transition-colors">
                <x-icon name="menu" class="w-5 h-5" />
                <span class="text-[10px] font-medium">{{ __('messages.more') ?? 'More' }}</span>
            </button>

            {{-- More sheet --}}
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
                 @click.away="open = false"
                 class="absolute bottom-full right-0 mb-2 w-56 rounded-2xl bg-surface-container-lowest shadow-editorial-lg overflow-hidden" style="display: none;">
                <div class="py-2">
                    @php
                        $moreItems = [
                            ['route' => 'categories.index', 'label' => __('messages.categories'), 'icon' => 'tag'],
                            ['route' => 'recurring-expenses.index', 'label' => __('messages.recurring_expenses'), 'icon' => 'repeat'],
                            ['route' => 'savings-goals.index', 'label' => __('messages.savings_goals'), 'icon' => 'piggy-bank'],
                            ['route' => 'reports.index', 'label' => __('messages.reports'), 'icon' => 'file-text'],
                            ['route' => 'notifications.index', 'label' => __('messages.notifications') ?? 'Notifications', 'icon' => 'bell'],
                            ['route' => 'profile.edit', 'label' => __('messages.profile'), 'icon' => 'user'],
                        ];
                    @endphp
                    @foreach($moreItems as $item)
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low transition-colors">
                            <x-icon :name="$item['icon']" class="w-5 h-5" />
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach

                    @if(Auth::user()->isAdmin())
                        <div class="my-1 mx-4" style="border-top: 1px solid rgba(191,201,200,0.15);"></div>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low transition-colors">
                            <x-icon name="shield" class="w-5 h-5" />
                            <span>{{ __('messages.manage_users') }}</span>
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low transition-colors">
                            <x-icon name="settings" class="w-5 h-5" />
                            <span>{{ __('messages.manage_categories') }}</span>
                        </a>
                    @endif

                    <div class="my-1 mx-4" style="border-top: 1px solid rgba(191,201,200,0.15);"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low transition-colors">
                            <x-icon name="log-out" class="w-5 h-5" />
                            <span>{{ __('messages.logout') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <x-confirm-modal />

    @stack('scripts')
    <script>
    (function() {
        // Custom confirm modal: intercept forms with data-confirm attribute
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (!form || form.tagName !== 'FORM') return;

            var confirmMsg = form.getAttribute('data-confirm');
            if (confirmMsg) {
                e.preventDefault();
                window.dispatchEvent(new CustomEvent('confirm-modal', {
                    detail: { message: confirmMsg, form: form }
                }));
                return;
            }

            // Loading state for POST forms
            if (form.method && form.method.toLowerCase() === 'post') {
                var btn = form.querySelector('button[type="submit"]');
                if (btn && !btn.disabled) {
                    btn.disabled = true;
                    btn.style.opacity = '0.6';
                }
            }
        }, true);

        // Also handle buttons with data-confirm via onclick
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('[data-confirm]');
            if (!btn) return;
            var form = btn.closest('form');
            if (!form) return;
            var confirmMsg = btn.getAttribute('data-confirm') || form.getAttribute('data-confirm');
            if (confirmMsg) {
                e.preventDefault();
                window.dispatchEvent(new CustomEvent('confirm-modal', {
                    detail: { message: confirmMsg, form: form }
                }));
            }
        }, true);
    })();
    </script>
</body>
</html>
