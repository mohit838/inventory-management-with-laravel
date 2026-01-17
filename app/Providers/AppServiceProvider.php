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
        if (! defined('L5_SWAGGER_CONST_HOST')) {
            $host = env('L5_SWAGGER_CONST_HOST', config('app.url'));
            
            // Ensure HTTPS if configured in APP_URL or running in production
            if (str_starts_with(config('app.url'), 'https://') && !str_starts_with($host, 'https://')) {
                $host = str_replace('http://', 'https://', $host);
            }
            
            define('L5_SWAGGER_CONST_HOST', $host);
        }

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
        if (config('app.env') === 'production' || \Illuminate\Support\Facades\Request::header('X-Forwarded-Proto') == 'https') {
             \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            // Check if user has permission (via HasPermissions trait)
            // This handles Superadmin bypass (returns true) and direct permission check.
            if (method_exists($user, 'hasPermissionTo')) {
                return $user->hasPermissionTo($ability) ?: null;
            }
        });
    }
}
