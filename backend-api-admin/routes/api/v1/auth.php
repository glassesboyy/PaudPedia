<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Public\TestimonialController;
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
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register-school', [AuthController::class, 'registerSchool'])->name('register.school');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['throttle:6,1'])
        ->name('verification.verify');
});

// Authenticated routes
Route::prefix('auth')->name('auth.')->middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('me'); 
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.change');
    
    // School registration (authenticated upgrade)
    Route::post('/schools/register', [AuthController::class, 'registerSchoolUpgrade'])->name('schools.register');

    // Profile management (FR-UA-06)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
});

// Resend verification email (requires auth but NOT verified!)
Route::prefix('auth')->name('auth.')->middleware('auth:sanctum')->group(function () {
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Other authenticated routes (outside auth prefix)
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // School memberships
    Route::get('/my-memberships', [\App\Http\Controllers\Api\V1\School\SchoolController::class, 'myMemberships'])
        ->name('my-memberships');

    // Testimonials - Submit testimonial (requires authentication)
    Route::post('/testimonials', [TestimonialController::class, 'store'])
        ->name('testimonials.store');
});
