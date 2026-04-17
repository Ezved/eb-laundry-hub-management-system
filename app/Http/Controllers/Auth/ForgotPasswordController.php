<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\User;


class ForgotPasswordController extends Controller
{
  // Show the forgot password form
  public function showForm()
  {
    // resources/views/auth/forgot_password.blade.php
    return view('auth.forgot_password');
  }

  // Handle "Continue" button: validate + send email
  public function sendChangePasswordEmail(Request $request)
  {
    // 1) Validate the email field from your form
    $request->validate([
      'email' => 'required|email',
    ]);

    // 2) Look up the user
    $user = User::where('email', $request->email)->first();

    if (! $user) {
      // No account found for this email
      return back()
        ->withErrors(['email' => 'We could not find an account with that email address.'])
        ->withInput();
    }

    // 3) Create a reset token and store it (hashed) with timestamp
    $plainToken = Str::random(64);

    DB::table('password_reset_tokens')->updateOrInsert(
      ['email' => $user->email],
      [
        'token'      => Hash::make($plainToken),
        'created_at' => now(),
      ]
    );

    // 4) Build the reset URL (works on laptop + iPhone)
    //    Includes email + token as query parameters
    $resetPath = route('password.change.page', [
      'email' => $user->email,
      'token' => $plainToken,
    ], false); // e.g. "/enter-new-password?email=...&token=..."

    $baseUrl  = rtrim(env('PUBLIC_URL', config('app.url')), '/');
    $resetUrl = $baseUrl . $resetPath;

    // 5) Send the email using your Blade view
    Mail::send('emails.change_password_link', [
      'user'     => $user,
      'resetUrl' => $resetUrl,
    ], function ($message) use ($user) {
      $message->to($user->email, $user->name ?? null)
        ->subject('Change your password • E&B Laundry Hub');
    });

    // 6) Redirect back with a success message
    return back()->with('status', 'We sent a password change link to your email. The link will expire in 10 minutes.');
  }


  // Show the "Enter new password" page (GET)
  public function showEnterNewPasswordForm(Request $request)
  {
    $email = $request->query('email');
    $token = $request->query('token');

    if (! $email || ! $token) {
      return redirect()
        ->route('password.request')
        ->withErrors(['email' => 'This password reset link is invalid or has expired. Please request a new one.']);
    }

    $record = DB::table('password_reset_tokens')->where('email', $email)->first();

    if (! $record) {
      return redirect()
        ->route('password.request')
        ->withErrors(['email' => 'This password reset link has expired. Please request a new one.']);
    }

    // Created time from DB and exact expiry = created_at + 10 minutes
    $createdAt = Carbon::parse($record->created_at);
    $expiresAt = $createdAt->copy()->addMinutes(10);

    // If current time is already past expiry → reject
    if (now()->greaterThan($expiresAt)) {
      return redirect()
        ->route('password.request')
        ->withErrors(['email' => 'This password reset link has expired. Please request a new one.']);
    }

    // Check token hash matches
    if (! Hash::check($token, $record->token)) {
      return redirect()
        ->route('password.request')
        ->withErrors(['email' => 'This password reset link is invalid. Please request a new one.']);
    }

    // Pass email, token, and the exact expiry timestamp (in ms) to the view
    return view('auth.enter_new_password', [
      'email'       => $email,
      'token'       => $token,
      'expiresAtMs' => $expiresAt->getTimestamp() * 1000, // JS uses ms
    ]);
  }

  // Handle "Update password" button (POST)
  public function updatePassword(Request $request)
  {
    // 1) Validate input
    $request->validate([
      'email'    => 'required|email',
      'token'    => 'required',
      'password' => 'required|string|min:8|confirmed',
    ]);

    $email = $request->email;
    $token = $request->token;

    // 2) Look up reset record
    $record = DB::table('password_reset_tokens')->where('email', $email)->first();

    if (! $record) {
      return redirect()
        ->route('password.request')
        ->withErrors(['email' => 'This password reset link has expired. Please request a new one.']);
    }

    // Check 10-minute expiry
    $createdAt = Carbon::parse($record->created_at);
    if ($createdAt->lt(now()->subMinutes(10))) {
      return redirect()
        ->route('password.request')
        ->withErrors(['email' => 'This password reset link has expired. Please request a new one.']);
    }

    // Check token hash
    if (! Hash::check($token, $record->token)) {
      return redirect()
        ->route('password.request')
        ->withErrors(['email' => 'This password reset link is invalid. Please request a new one.']);
    }

    // 3) Find user
    $user = User::where('email', $email)->first();

    if (! $user) {
      return redirect()
        ->route('password.request')
        ->withErrors(['email' => 'We could not find an account for this email. Please request a new link.']);
    }

    // 4) Update password in the database (HASHED)
    $user->password = Hash::make($request->password);
    $user->save();

    // 5) Double-check that the password was actually updated
    $user->refresh();
    if (! Hash::check($request->password, $user->password)) {
      return back()
        ->withErrors(['password' => 'We could not update your password. Please try again.'])
        ->withInput($request->except('password', 'password_confirmation'));
    }

    // 6) Delete the reset record so the link can't be reused
    DB::table('password_reset_tokens')->where('email', $email)->delete();

    // 7) Send password change notification email
    $changedAt    = now()->setTimezone(config('app.timezone', 'Asia/Manila'))->format('M d, Y h:i A');
    $supportEmail = config('mail.from.address', 'support@example.com');

    Mail::send('emails.password_change_notification', [
      'userName'     => $user->name,
      'changedAt'    => $changedAt,
      'supportEmail' => $supportEmail,
    ], function ($message) use ($user) {
      $message->to($user->email, $user->name ?? null)
        ->subject('Your password was updated • E&B Laundry Hub');
    });

    // 8) Redirect to login with success message
    return redirect()
      ->route('login')
      ->with('status', 'Your password has been updated. We sent you a confirmation email.');
  }
}
