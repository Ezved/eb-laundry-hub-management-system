<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FacebookAccountAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class FacebookAuthController extends Controller
{
  public function redirectToFacebook()
  {
    // Ask for email and re-prompt if previously declined
    return Socialite::driver('facebook')
      ->scopes(['email'])
      ->with(['auth_type' => 'rerequest'])
      ->redirectUrl(config('services.facebook.redirect'))
      ->redirect();
  }

  public function handleFacebookCallback(Request $request)
  {
    try {
      // stateless() avoids "Invalid state" when coming from ngrok/https
      $fbUser = Socialite::driver('facebook')
        ->stateless()
        ->fields(['name', 'first_name', 'last_name', 'email'])
        ->user();

      $fbId    = $fbUser->getId();
      $name    = $fbUser->getName() ?: 'Facebook User';
      $email   = $fbUser->getEmail();     // can be null
      $avatar  = $fbUser->getAvatar();

      // 1) If this FB account is already linked, log that user in.
      $link = FacebookAccountAuth::where('provider_id', $fbId)->first();
      if ($link && $link->user) {
        // keep avatar/email in sync (optional)
        $link->update([
          'provider_email' => $email,
          'avatar'         => $avatar,
        ]);
        Auth::login($link->user, true);
        return $this->redirectAfterLogin($link->user);
      }

      // 2) No existing link.
      if ($email) {
        // If there is a user with this email, attach FB to that user.
        $user = User::where('email', $email)->first();

        if (!$user) {
          // Create a new local user
          $user = User::create([
            'name'              => $name,
            'email'             => $email,
            'password'          => Hash::make(Str::random(40)), // random password
            'role'              => User::ROLE_USER,              // default to user
            'status'            => 'active',
            'is_hidden'         => false,
            'email_verified_at' => now(),                        // social sign-in
          ]);
        }

        // Create the link record
        FacebookAccountAuth::create([
          'user_id'        => $user->id,
          'provider'       => 'facebook',
          'provider_id'    => $fbId,
          'provider_email' => $email,
          'avatar'         => $avatar,
        ]);

        Auth::login($user, true);
        return $this->redirectAfterLogin($user);
      }

      // 3) Email is missing. Ask the user to supply an email once.
      // Stash FB info in session for the next step.
      $request->session()->put('fb_pending', [
        'provider'    => 'facebook',
        'provider_id' => $fbId,
        'name'        => $name,
        'avatar'      => $avatar,
      ]);

      return redirect()->route('auth.facebook.email.form');
    } catch (\Throwable $e) {
      Log::error('Facebook Login Error: ' . $e->getMessage());
      return redirect('/login')->with('error', 'Facebook login failed. Please try again.');
    }
  }

  public function emailForm(Request $request)
  {
    if (!$request->session()->has('fb_pending')) {
      return redirect('/login')->with('error', 'Facebook session expired. Please try again.');
    }

    // Show a simple form asking for an email
    return view('auth.facebook_email');
  }

  public function emailStore(Request $request)
  {
    $pending = $request->session()->get('fb_pending');
    if (!$pending) {
      return redirect('/login')->with('error', 'Facebook session expired. Please try again.');
    }

    // Allow existing emails or new ones
    $request->validate([
      'email' => ['required', 'email', 'max:255'],
    ]);

    $email  = strtolower(trim($request->input('email')));
    $name   = $pending['name'] ?? 'Facebook User';
    $fbId   = $pending['provider_id'];
    $avatar = $pending['avatar'] ?? null;

    // If user exists, link to it; else create a new one
    $user = \App\Models\User::where('email', $email)->first();
    if (!$user) {
      $user = \App\Models\User::create([
        'name'              => $name,
        'email'             => $email,
        'password'          => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(40)),
        'role'              => \App\Models\User::ROLE_USER,
        'status'            => 'active',
        'is_hidden'         => false,
        'email_verified_at' => now(),
      ]);
    }

    // Upsert the FB link (handles re-link safely)
    \App\Models\FacebookAccountAuth::updateOrCreate(
      ['provider_id' => $fbId],
      [
        'user_id'        => $user->id,
        'provider'       => 'facebook',
        'provider_email' => $email,
        'avatar'         => $avatar,
      ]
    );

    // Clean up + login
    $request->session()->forget('fb_pending');
    \Illuminate\Support\Facades\Auth::login($user, true);

    return $this->redirectAfterLogin($user);
  }


  protected function redirectAfterLogin(User $user)
  {
    // Send admins to admin dashboard, everyone else to user dashboard
    $fallback = $user->isAdmin()
      ? route('admin.dashboard')
      : route('user.dashboard');

    return redirect()->intended($fallback);
  }
}