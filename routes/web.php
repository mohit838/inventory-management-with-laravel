<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'service' => 'app']);
});

// Guest routes (authentication)
Route::middleware('guest:web')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/', [AuthController::class, 'login']);
    Route::get('/login', [AuthController::class, 'showLoginForm']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
});

// Protected routes (requires web authentication)
Route::middleware(['auth:web'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')
        ->middleware('permission:dashboard.view');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    // Categories
    Route::middleware('permission:categories.view')->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    });

    Route::middleware('permission:categories.create')->group(function () {
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    });

    Route::middleware('permission:categories.edit')->group(function () {
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::post('/categories/{category}/toggle-active', [CategoryController::class, 'toggleActive'])->name('categories.toggle-active');
    });

    Route::middleware('permission:categories.delete')->group(function () {
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    Route::middleware('permission:categories.view')->group(function () {
        Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    });

    // Products
    Route::middleware('permission:products.view')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    });

    Route::middleware('permission:products.create')->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    });

    Route::middleware('permission:products.edit')->group(function () {
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::post('/products/{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('products.toggle-active');
    });

    Route::middleware('permission:products.delete')->group(function () {
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    Route::middleware('permission:products.view')->group(function () {
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    });

    // User Management (Superadmin only)
    Route::middleware(['permission:users.view'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('permission:users.create');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:users.edit');
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active')->middleware('permission:users.edit');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');
    });

    // Settings
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');

    // Profile
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Orders
    Route::middleware('permission:orders.view')->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    });

    Route::middleware('permission:orders.create')->group(function () {
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    });

    Route::middleware('permission:orders.view')->group(function () {
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{id}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    });

    Route::middleware('permission:orders.edit')->group(function () {
        Route::post('/orders/{id}/toggle-active', [OrderController::class, 'toggleActive'])->name('orders.toggle-active');
    });
});
