<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        // @role('super_admin') ... @endrole
        Blade::if('role', function (string ...$roles) {
            return auth()->check() && auth()->user()->hasRole(...$roles);
        });
    }
}
