<?php

use App\Http\Controllers\Api\V1\Public\ArticleController;
use App\Http\Controllers\Api\V1\Public\CategoryController;
use App\Http\Controllers\Api\V1\Public\ContactController;
use App\Http\Controllers\Api\V1\Public\CourseController;
use App\Http\Controllers\Api\V1\Public\LandingPageController;
use App\Http\Controllers\Api\V1\Public\MentorController;
use App\Http\Controllers\Api\V1\Public\ProductController;
use App\Http\Controllers\Api\V1\Public\SiteSettingController;
use App\Http\Controllers\Api\V1\Public\TestimonialController;
use App\Http\Controllers\Api\V1\Public\WebinarController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes (v1)
|--------------------------------------------------------------------------
|
| These routes are accessible without authentication.
| All routes are prefixed with /api/v1/
|
*/

/**
 * Landing Page
 * GET /api/v1/landing - Get all landing page data
 * GET /api/v1/landing/hero - Get hero section data
 * GET /api/v1/landing/statistics - Get platform statistics
 */
Route::prefix('landing')->name('landing.')->group(function () {
    Route::get('/', [LandingPageController::class, 'index'])->name('index');
    Route::get('/hero', [LandingPageController::class, 'hero'])->name('hero');
    Route::get('/statistics', [LandingPageController::class, 'statistics'])->name('statistics');
});

/**
 * Site Settings
 * GET /api/v1/settings - Get all public site settings
 * GET /api/v1/settings/{key} - Get specific setting by key
 */
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SiteSettingController::class, 'index'])->name('index');
    Route::get('/{key}', [SiteSettingController::class, 'show'])->name('show');
});

/**
 * Contact
 * GET /api/v1/contact - Get contact information
 */
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');

/**
 * Categories
 * GET /api/v1/categories - Get all categories
 * GET /api/v1/categories/courses - Get course categories
 * GET /api/v1/categories/products - Get product categories
 * GET /api/v1/categories/articles - Get article categories
 */
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/courses', [CategoryController::class, 'courses'])->name('courses');
    Route::get('/products', [CategoryController::class, 'products'])->name('products');
    Route::get('/articles', [CategoryController::class, 'articles'])->name('articles');
});

/**
 * Courses
 * GET /api/v1/courses - Get list of courses (paginated)
 * GET /api/v1/courses/featured - Get featured courses
 * GET /api/v1/courses/category/{categorySlug} - Get courses by category
 * GET /api/v1/courses/{slug} - Get course detail by slug
 */
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/featured', [CourseController::class, 'featured'])->name('featured');
    Route::get('/category/{categorySlug}', [CourseController::class, 'byCategory'])->name('by-category');
    Route::get('/{slug}', [CourseController::class, 'show'])->name('show');
});

/**
 * Webinars
 * GET /api/v1/webinars - Get list of webinars (paginated)
 * GET /api/v1/webinars/featured - Get featured/upcoming webinars
 * GET /api/v1/webinars/upcoming - Get upcoming webinars (paginated)
 * GET /api/v1/webinars/{slug} - Get webinar detail by slug
 */
Route::prefix('webinars')->name('webinars.')->group(function () {
    Route::get('/', [WebinarController::class, 'index'])->name('index');
    Route::get('/featured', [WebinarController::class, 'featured'])->name('featured');
    Route::get('/upcoming', [WebinarController::class, 'upcoming'])->name('upcoming');
    Route::get('/{slug}', [WebinarController::class, 'show'])->name('show');
});

/**
 * Products
 * GET /api/v1/products - Get list of products (paginated)
 * GET /api/v1/products/featured - Get featured products
 * GET /api/v1/products/category/{categorySlug} - Get products by category
 * GET /api/v1/products/{slug} - Get product detail by slug
 */
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/featured', [ProductController::class, 'featured'])->name('featured');
    Route::get('/category/{categorySlug}', [ProductController::class, 'byCategory'])->name('by-category');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
});

/**
 * Articles
 * GET /api/v1/articles - Get list of articles (paginated)
 * GET /api/v1/articles/featured - Get featured articles
 * GET /api/v1/articles/popular - Get popular articles
 * GET /api/v1/articles/recent - Get recent articles
 * GET /api/v1/articles/category/{categorySlug} - Get articles by category
 * GET /api/v1/articles/tag/{tag} - Get articles by tag
 * GET /api/v1/articles/{slug} - Get article detail by slug
 */
Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('index');
    Route::get('/featured', [ArticleController::class, 'featured'])->name('featured');
    Route::get('/popular', [ArticleController::class, 'popular'])->name('popular');
    Route::get('/recent', [ArticleController::class, 'recent'])->name('recent');
    Route::get('/category/{categorySlug}', [ArticleController::class, 'byCategory'])->name('by-category');
    Route::get('/tag/{tag}', [ArticleController::class, 'byTag'])->name('by-tag');
    Route::get('/{slug}', [ArticleController::class, 'show'])->name('show');
});

/**
 * Testimonials
 * GET /api/v1/testimonials - Get list of testimonials (paginated)
 * GET /api/v1/testimonials/featured - Get featured testimonials
 * Note: POST /api/v1/testimonials - Submit new testimonial (requires authentication, see auth routes)
 */
Route::prefix('testimonials')->name('testimonials.')->group(function () {
    Route::get('/', [TestimonialController::class, 'index'])->name('index');
    Route::get('/featured', [TestimonialController::class, 'featured'])->name('featured');
});

/**
 * Mentors
 * GET /api/v1/mentors - Get list of mentors (paginated)
 * GET /api/v1/mentors/featured - Get featured mentors
 * GET /api/v1/mentors/{id} - Get mentor detail by ID
 */
Route::prefix('mentors')->name('mentors.')->group(function () {
    Route::get('/', [MentorController::class, 'index'])->name('index');
    Route::get('/featured', [MentorController::class, 'featured'])->name('featured');
    Route::get('/{id}', [MentorController::class, 'show'])->name('show')->where('id', '[0-9]+');
});
