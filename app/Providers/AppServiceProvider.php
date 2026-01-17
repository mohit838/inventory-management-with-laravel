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

            // Ensure schema matches APP_URL to avoid mixed content
            $appUrlScheme = parse_url(config('app.url'), PHP_URL_SCHEME);
            if ($appUrlScheme && !str_starts_with($host, $appUrlScheme . '://')) {
                $host = preg_replace('/^https?:\/\//', '', $host);
                $host = $appUrlScheme . '://' . $host;
            }

            define('L5_SWAGGER_CONST_HOST', $host);
        }

        // Since OrderService uses InvoiceService directly, we don't need these non-existent bindings.

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
