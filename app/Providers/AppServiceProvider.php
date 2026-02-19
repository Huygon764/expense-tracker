<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            if (Auth::check()) {
                $view->with('unreadNotificationsCount', Auth::user()
                    ->notifications()
                    ->where('is_read', false)
                    ->count());
                $view->with('recentNotifications', Auth::user()
                    ->notifications()
                    ->orderByDesc('created_at')
                    ->limit(8)
                    ->get());
            } else {
                $view->with('unreadNotificationsCount', 0);
                $view->with('recentNotifications', collect());
            }
        });
    }
}
