<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\ProfileSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // One profile query per request, shared by every view.
        $this->app->scoped(ProfileSetting::class, fn () => ProfileSetting::current());
    }

    public function boot(): void
    {
        Paginator::defaultView('pagination.custom');
        Paginator::defaultSimpleView('pagination.custom');

        View::composer('*', function ($view) {
            $view->with('profile', app(ProfileSetting::class));
        });

        View::composer('layouts.admin', function ($view) {
            $view->with('unreadCount', Message::unread()->count());
        });
    }
}
