<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SitemapController;

use App\Http\Controllers\Product\ReviewController;
use App\Http\Controllers\Product\PaymentController;

// Route to generate the sitemap
Route::get('/generate-sitemap', [SitemapController::class, 'generateSitemap']);

// Home route
Route::get('/', function () {
    return view('welcome');
});



Route::get('/reviews/approve/{token}', [ReviewController::class, 'approve'])->name('reviews.approve');
Route::get('/reviews/delete/{token}', [ReviewController::class, 'delete'])->name('reviews.delete');



Route::get('/download/{template}', [PaymentController::class, 'downloadTemplate'])->name('download.template');

// Route::get('/paystack/callback', [PaymentController::class, 'verifyPayment'])->name('paystack.callback');
