<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Public\TestimonialController;
use App\Http\Controllers\Api\V1\School\SchoolController;
use App\Http\Controllers\Api\V1\School\TeacherController;
use App\Http\Controllers\Api\V1\School\ClassRoomController;
use App\Http\Controllers\Api\V1\School\ParentProfileController;
use App\Http\Controllers\Api\V1\School\StudentController;
use App\Http\Controllers\Api\V1\School\AttendanceController;
use App\Http\Controllers\Api\V1\School\AssessmentController;
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
    // School registration (authenticated upgrade)
    Route::post('/schools/register', [AuthController::class, 'registerSchoolUpgrade'])->name('schools.register');

    // School profile management
    Route::get('/schools/{id}', [SchoolController::class, 'show'])->name('schools.show');
    Route::put('/schools/{id}', [SchoolController::class, 'update'])->name('schools.update');

    // Teacher management
    Route::get('/schools/{id}/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::post('/schools/{id}/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/schools/{id}/teachers/{teacherId}', [TeacherController::class, 'show'])->name('teachers.show');
    Route::delete('/schools/{id}/teachers/{teacherId}', [TeacherController::class, 'destroy'])->name('teachers.destroy');

    // Class management
    Route::get('/schools/{id}/classes', [ClassRoomController::class, 'index'])->name('classes.index');
    Route::post('/schools/{id}/classes', [ClassRoomController::class, 'store'])->name('classes.store');
    Route::get('/schools/{id}/classes/{classId}', [ClassRoomController::class, 'show'])->name('classes.show');
    Route::put('/schools/{id}/classes/{classId}', [ClassRoomController::class, 'update'])->name('classes.update');
    Route::delete('/schools/{id}/classes/{classId}', [ClassRoomController::class, 'destroy'])->name('classes.destroy');

    // Parent management
    Route::get('/schools/{id}/parents', [ParentProfileController::class, 'index'])->name('parents.index');
    Route::post('/schools/{id}/parents', [ParentProfileController::class, 'store'])->name('parents.store');
    Route::get('/schools/{id}/parents/{parentId}', [ParentProfileController::class, 'show'])->name('parents.show');
    Route::put('/schools/{id}/parents/{parentId}', [ParentProfileController::class, 'update'])->name('parents.update');
    Route::delete('/schools/{id}/parents/{parentId}', [ParentProfileController::class, 'destroy'])->name('parents.destroy');

    // Student management
    Route::get('/schools/{id}/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/schools/{id}/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/schools/{id}/students/{studentId}', [StudentController::class, 'show'])->name('students.show');
    Route::put('/schools/{id}/students/{studentId}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/schools/{id}/students/{studentId}', [StudentController::class, 'destroy'])->name('students.destroy');

    // Attendance management
    Route::get('/schools/{id}/classes/{classId}/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/schools/{id}/classes/{classId}/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/schools/{id}/students/{studentId}/attendance/summary', [AttendanceController::class, 'studentSummary'])->name('attendance.summary');

    // Assessment management
    Route::get('/schools/{id}/classes/{classId}/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
    Route::post('/schools/{id}/classes/{classId}/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
    Route::get('/schools/{id}/students/{studentId}/assessments/history', [AssessmentController::class, 'studentHistory'])->name('assessments.history');

    // School memberships
    Route::get('/my-memberships', [SchoolController::class, 'myMemberships'])
        ->name('my-memberships');

    // Testimonials - Submit testimonial (requires authentication)
    Route::post('/testimonials', [TestimonialController::class, 'store'])
        ->name('testimonials.store');
});
