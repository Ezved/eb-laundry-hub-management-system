<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\FacebookAuthController;

Route::middleware('guest')->group(function () {
    Route::get('/auth/facebook/redirect', [FacebookAuthController::class, 'redirectToFacebook'])
        ->name('auth.facebook.redirect');

    Route::get('/auth/facebook/callback', [FacebookAuthController::class, 'handleFacebookCallback'])
        ->name('auth.facebook.callback');

    Route::get('/auth/facebook/email', [FacebookAuthController::class, 'emailForm'])
        ->name('auth.facebook.email.form');

    Route::post('/auth/facebook/email', [FacebookAuthController::class, 'emailStore'])
        ->name('auth.facebook.email.store');
});
