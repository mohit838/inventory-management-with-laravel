<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\ProductController;

Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::middleware(['jwt.auth'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        Route::apiResource('categories', CategoryController::class);
        Route::post('categories/{category}/toggle-active', [CategoryController::class, 'toggleActive']);

        Route::apiResource('subcategories', SubcategoryController::class);
        Route::post('subcategories/{subcategory}/toggle-active', [SubcategoryController::class, 'toggleActive']);

        Route::apiResource('products', ProductController::class);
        Route::post('products/{product}/toggle-active', [ProductController::class, 'toggleActive']);
    });
});
