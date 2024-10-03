<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Blog\BlogController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Contact\ContactController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/mylogin', [AuthController::class, 'login']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);
Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);


// Project routes
Route::prefix('projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::get('/{slug}', [ProjectController::class, 'show']);
    Route::get('/{slug}/related', [ProjectController::class, 'getRelatedProjects']);
});

// Blog routes
Route::prefix('blogs')->group(function () {
    Route::get('/', [BlogController::class, 'index']);  // Fetch all blogs
    Route::get('/{slug}', [BlogController::class, 'show']);  // Fetch a single blog by slug
    Route::get('/{slug}/related', [BlogController::class, 'getRelatedBlogs']);  // Fetch related blogs
});





Route::post('/contact', [ContactController::class, 'sendContactMessage']);






// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [ProfileController::class, 'index']);

    // Grouped project routes with prefix 'projects'
    Route::prefix('projects')->group(function () {
        Route::post('/', [ProjectController::class, 'store']);         // Create a new project
        Route::put('/{slug}', [ProjectController::class, 'update']);   // Update a project by slug
        Route::delete('/{slug}', [ProjectController::class, 'destroy']); // Delete a project by slug
    });

    // Grouped blog routes with prefix 'blogs'
    Route::prefix('blogs')->group(function () {
        Route::post('/', [BlogController::class, 'store']);          // Create a new blog
        Route::put('/{slug}', [BlogController::class, 'update']);    // Update a blog by slug
        Route::delete('/{slug}', [BlogController::class, 'destroy']); // Delete a blog by slug
    });
});
