<?php

use App\Http\Controllers\Api\V1\Auth\CartController;
use App\Http\Controllers\Api\V1\Auth\CheckoutController;
use App\Http\Controllers\Api\V1\Auth\LmsController;
use App\Http\Controllers\Api\V1\Auth\LmsQuizController;
use App\Http\Controllers\Api\V1\Auth\UserDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Dashboard Routes (v1)
|--------------------------------------------------------------------------
|
| Authenticated user dashboard endpoints.
| Prefix: /api/v1/user
| Middleware: auth:sanctum
|
*/

Route::prefix('user')->name('user.')->middleware('auth:sanctum')->group(function () {

    // My Courses (FR-UA-08)
    Route::get('/courses', [UserDashboardController::class, 'courses'])->name('courses');

    // My Products (FR-UA-09)
    Route::get('/products', [UserDashboardController::class, 'products'])->name('products');
    Route::get('/products/{id}/download', [UserDashboardController::class, 'downloadProduct'])->name('products.download');

    // My Webinars (FR-UA-10)
    Route::get('/webinars', [UserDashboardController::class, 'webinars'])->name('webinars');

    // My Certificates (FR-UA-11)
    Route::get('/certificates', [UserDashboardController::class, 'certificates'])->name('certificates');

    // Transaction History (FR-UA-12)
    Route::get('/transactions', [UserDashboardController::class, 'transactions'])->name('transactions');
    Route::get('/transactions/{id}', [UserDashboardController::class, 'transactionDetail'])->name('transactions.show');

    // Cart (FR-EC-04)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/items', [CartController::class, 'addItem'])->name('cart.add');
    Route::put('/cart/items', [CartController::class, 'updateItem'])->name('cart.update');
    Route::delete('/cart/items', [CartController::class, 'removeItem'])->name('cart.remove');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

    // Cart — Promo Code Validation (FR-EC-06)
    Route::post('/cart/validate-promo', [CartController::class, 'validatePromo'])->name('cart.validate-promo');

    // Checkout — Create Order + Midtrans Snap (FR-EC-05)
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout');

    // LMS Module
    Route::prefix('/lms/courses/{courseSlug}')->name('lms.courses.')->group(function () {
        Route::get('/', [LmsController::class, 'show'])->name('show');
        Route::get('/progress', [LmsController::class, 'progress'])->name('progress');

        Route::get('/lessons/{lesson}', [LmsController::class, 'lesson'])->name('lessons.show');
        Route::post('/lessons/{lesson}/complete', [LmsController::class, 'markLessonComplete'])->name('lessons.complete');
        Route::get('/lessons/{lesson}/pdf', [LmsController::class, 'lessonPdf'])->name('lessons.pdf');

        Route::get('/quizzes/{quiz}', [LmsQuizController::class, 'show'])->name('quizzes.show');
        Route::post('/quizzes/{quiz}/submit', [LmsQuizController::class, 'submit'])->name('quizzes.submit');

        Route::post('/certificate/generate', [LmsController::class, 'generateCertificate'])->name('certificate.generate');
        Route::get('/certificate/download', [LmsController::class, 'downloadCertificate'])->name('certificate.download');
    });
});
