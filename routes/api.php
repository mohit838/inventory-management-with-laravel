<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserSettingController;

Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::middleware(['jwt.auth'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        // Categories
        Route::get('categories/dropdown', [CategoryController::class, 'dropdown'])->middleware('permission:categories.view');
        Route::get('categories', [CategoryController::class, 'index'])->middleware('permission:categories.view');
        Route::post('categories', [CategoryController::class, 'store'])->middleware('permission:categories.create');
        Route::get('categories/{category}', [CategoryController::class, 'show'])->middleware('permission:categories.view');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->middleware('permission:categories.edit');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->middleware('permission:categories.delete');
        Route::post('categories/{category}/toggle-active', [CategoryController::class, 'toggleActive'])->middleware('permission:categories.edit');

        // Subcategories
        Route::get('subcategories/dropdown', [SubcategoryController::class, 'dropdown'])->middleware('permission:categories.view');
        Route::get('subcategories', [SubcategoryController::class, 'index'])->middleware('permission:categories.view');
        Route::post('subcategories', [SubcategoryController::class, 'store'])->middleware('permission:categories.create');
        Route::get('subcategories/{subcategory}', [SubcategoryController::class, 'show'])->middleware('permission:categories.view');
        Route::put('subcategories/{subcategory}', [SubcategoryController::class, 'update'])->middleware('permission:categories.edit');
        Route::delete('subcategories/{subcategory}', [SubcategoryController::class, 'destroy'])->middleware('permission:categories.delete');
        Route::post('subcategories/{subcategory}/toggle-active', [SubcategoryController::class, 'toggleActive'])->middleware('permission:categories.edit');

        // Products
        Route::get('products/dropdown', [ProductController::class, 'dropdown'])->middleware('permission:products.view');
        Route::get('products', [ProductController::class, 'index'])->middleware('permission:products.view');
        Route::post('products', [ProductController::class, 'store'])->middleware('permission:products.create');
        Route::get('products/{product}', [ProductController::class, 'show'])->middleware('permission:products.view');
        Route::put('products/{product}', [ProductController::class, 'update'])->middleware('permission:products.edit');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->middleware('permission:products.delete');
        Route::post('products/{product}/toggle-active', [ProductController::class, 'toggleActive'])->middleware('permission:products.edit');
        
        Route::post('/uploads/public', [\App\Http\Controllers\Api\PublicUploadController::class, 'store']); // Public? Or restrict? User asked for public bucket but authenticated upload. Let's keep it just Auth for now, maybe 'permission:products.create' if it's for products.
        
        Route::get('settings', [UserSettingController::class, 'index'])->middleware('permission:settings.manage');
        Route::post('settings', [UserSettingController::class, 'update'])->middleware('permission:settings.manage');
    });
});
