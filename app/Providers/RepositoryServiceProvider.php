<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Auto-wiring will handle class injection.
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
