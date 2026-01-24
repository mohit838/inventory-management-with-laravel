<?php

namespace App\Providers;

use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SubcategoryRepositoryInterface;
use App\Interfaces\UserSettingRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SubcategoryRepository;
use App\Repositories\UserSettingRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(SubcategoryRepositoryInterface::class, SubcategoryRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(UserSettingRepositoryInterface::class, UserSettingRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
