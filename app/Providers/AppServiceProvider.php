<?php

namespace App\Providers;

use App\Repositories\HomeRepository;
use App\Repositories\WilayahRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;

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
        Paginator::useBootstrapFive();

        View::composer('components.sidebar', function ($view) {
            $reminderCount = app(\App\Repositories\LoketRepository::class)->getReminderEncounter()->count();
            $view->with('reminderCount', $reminderCount);
        });
    }
}
