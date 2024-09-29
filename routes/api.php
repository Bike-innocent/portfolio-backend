<?php
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\profile\ProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use App\Http\Controllers\project\ProjectController;



// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);
Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);


Route::prefix('projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index']);  
    Route::get('/{id}', [ProjectController::class, 'show']);          

});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [ProfileController::class, 'index']);
    
    // Grouped project routes with prefix 'projects'
    Route::prefix('projects')->group(function () {
   
        Route::post('/', [ProjectController::class, 'store']);         // Create a new project
        Route::put('/{id}', [ProjectController::class, 'update']);     // Update a project by ID
        Route::delete('/{id}', [ProjectController::class, 'destroy']); // Delete a project by ID
    });
    
});
