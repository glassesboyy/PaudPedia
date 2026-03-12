<?php

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
});
