<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Authentication
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
    
    // Invitation-based Registration
    Route::get('/register/{token}', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

// Protected UI Routes (Require Auth & 2FA)
Route::middleware(['auth', '2fa'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Invitations
    Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create')->middleware('permission:create_invitations');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store')->middleware('permission:create_invitations');

    // Users Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('permission:view_users');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:delete_users');
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore')->middleware('permission:delete_users');

    // Settings & Permissions
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings')->middleware('permission:view_settings');
    Route::get('/settings/permissions', [RolePermissionController::class, 'index'])->name('settings.permissions')->middleware('permission:manage_permissions');
    Route::put('/settings/permissions', [RolePermissionController::class, 'update'])->name('settings.permissions.update')->middleware('permission:manage_permissions');

    // Profile & Password
    Route::get('/profile/password', [ProfileController::class, 'showPasswordForm'])->name('password.change');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update.profile');
});

// 2FA Setup & Verification (Authenticated but 2FA not yet required/verified for these specific endpoints)
Route::middleware(['auth'])->group(function () {
    // 2FA Setup
    Route::get('/2fa/setup', [TwoFactorController::class, 'showSetupForm'])->name('two-factor.setup');
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');

    // 2FA Verification (during login)
    Route::get('/2fa/verify', [TwoFactorController::class, 'showVerifyForm'])->name('two-factor.verify');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify']);
});

// Forgot Password Flow (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', function () {
        return view('auth.passwords.email');
    })->name('password.request');
    
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.passwords.reset', ['token' => $token]);
    })->name('password.reset');

    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});
