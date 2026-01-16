<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Interfaces\InvoiceGeneratorInterface::class,
            \App\Services\BasicJsonInvoiceGenerator::class
        );

        $this->app->bind(
            \App\Interfaces\AnalyticsServiceInterface::class,
            \App\Services\SqlAnalyticsService::class
        );

        $this->app->singleton(
            \App\Interfaces\CacheServiceInterface::class,
            \App\Services\RedisCacheService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            // Check if user has permission (via HasPermissions trait)
            // This handles Superadmin bypass (returns true) and direct permission check.
            if (method_exists($user, 'hasPermissionTo')) {
                return $user->hasPermissionTo($ability) ?: null;
            }
        });
    }
}
