<?php

namespace App\Providers;

use App\Interfaces\HomeInterface;
use App\Interfaces\WilayahInterface;
use App\Repositories\HomeRepository;
use App\Repositories\WilayahRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            WilayahInterface::class,
            WilayahRepository::class,
            HomeInterface::class,
            HomeRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
