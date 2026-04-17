<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;

// Show the "Forgot Password" page
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])
  ->name('password.request');

// Handle the Continue button
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendChangePasswordEmail'])
  ->name('password.email');

// Page where the email button lands (GET)
Route::get('/enter-new-password', [ForgotPasswordController::class, 'showEnterNewPasswordForm'])
  ->name('password.change.page');

// Handle "Update password" button (POST)
Route::post('/enter-new-password', [ForgotPasswordController::class, 'updatePassword'])
  ->name('password.update');
