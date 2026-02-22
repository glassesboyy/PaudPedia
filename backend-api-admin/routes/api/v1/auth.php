<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth API Routes (v1)
|--------------------------------------------------------------------------
|
| Authentication routes for B2C users.
| Prefix: /api/v1/auth
|
*/

// Guest routes (no authentication required)
Route::prefix('auth')->name('auth.')->group(function () {
    // FR-UA-01: Pendaftaran pengguna
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    
    // FR-UA-02: Login pengguna
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    
    // FR-UA-04: Reset kata sandi (forgot password)
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    
    // FR-UA-03: Verifikasi email (via signed URL)
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});

// Authenticated routes
Route::prefix('auth')->name('auth.')->middleware('auth:sanctum')->group(function () {
    // Get current user
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    
    // FR-UA-06: Logout pengguna
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // FR-UA-05: Ubah password
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.change');
    
    // FR-UA-03: Resend verification email
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});
