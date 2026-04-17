<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;

// Google OAuth routes
Route::get('/auth/google/redirect', [AuthenticationController::class, 'redirectToGoogle'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [AuthenticationController::class, 'handleGoogleCallback'])
    ->name('google.callback');
