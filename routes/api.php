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

        Route::get('categories/dropdown', [CategoryController::class, 'dropdown']);
        Route::apiResource('categories', CategoryController::class);
        Route::post('categories/{category}/toggle-active', [CategoryController::class, 'toggleActive']);

        Route::get('subcategories/dropdown', [SubcategoryController::class, 'dropdown']);
        Route::apiResource('subcategories', SubcategoryController::class);
        Route::post('subcategories/{subcategory}/toggle-active', [SubcategoryController::class, 'toggleActive']);

        Route::get('products/dropdown', [ProductController::class, 'dropdown']);
        Route::apiResource('products', ProductController::class);
        Route::post('products/{product}/toggle-active', [ProductController::class, 'toggleActive']);
        Route::get('settings', [UserSettingController::class, 'index']);
        Route::post('settings', [UserSettingController::class, 'update']);
    });
});
