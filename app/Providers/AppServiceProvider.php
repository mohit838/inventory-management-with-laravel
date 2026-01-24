<?php

namespace App\Providers;

use App\Interfaces\AnalyticsServiceInterface;
use App\Interfaces\CacheServiceInterface;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Observers\CategoryObserver;
use App\Observers\ProductObserver;
use App\Observers\SubcategoryObserver;
use App\Services\RedisCacheService;
use App\Services\SqlAnalyticsService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
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
            AnalyticsServiceInterface::class,
            SqlAnalyticsService::class
        );

        $this->app->singleton(
            CacheServiceInterface::class,
            RedisCacheService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || Request::header('X-Forwarded-Proto') == 'https') {
            URL::forceScheme('https');
        }

        Product::observe(ProductObserver::class);
        Category::observe(CategoryObserver::class);
        Subcategory::observe(SubcategoryObserver::class);

        Gate::before(function ($user, $ability) {
            // Check if user has permission (via HasPermissions trait)
            // This handles Superadmin bypass (returns true) and direct permission check.
            if (method_exists($user, 'hasPermissionTo')) {
                return $user->hasPermissionTo($ability) ?: null;
            }
        });
    }
}
