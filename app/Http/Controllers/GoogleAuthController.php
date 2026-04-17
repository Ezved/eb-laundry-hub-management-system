<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\GoogleAccountAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleAuthController extends Controller
{
    /**
     * Send the user to Google’s consent screen.
     * - Uses the current host so it works for 127.0.0.1, localhost, or ngrok/iPhone.
     */
    public function redirect(Request $request)
    {
        // e.g. "http://127.0.0.1:8000" OR "https://feasibly-sunniest-ralph.ngrok-free.dev"
        $baseUrl     = $request->getSchemeAndHttpHost();
        $callbackUrl = $baseUrl . route('google.callback', [], false); // "/auth/google/callback"

        $driver = Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirectUrl($callbackUrl);

        return $driver->redirect();
    }

    /**
     * Handle Google’s callback and log the user in.
     */
    public function callback(Request $request)
    {
        // Use the SAME callback URL as in redirect()
        $baseUrl     = $request->getSchemeAndHttpHost();
        $callbackUrl = $baseUrl . route('google.callback', [], false);

        try {
            // Normal callback with state check
            $googleUser = Socialite::driver('google')
                ->redirectUrl($callbackUrl)
                ->user();
        } catch (InvalidStateException $e) {
            // iOS / cookie issues: fall back to stateless
            try {
                $googleUser = Socialite::driver('google')
                    ->redirectUrl($callbackUrl)
                    ->stateless()
                    ->user();
            } catch (\Throwable $e2) {
                Log::error('Google callback failed (stateless)', [
                    'message' => $e2->getMessage(),
                ]);

                return redirect()
                    ->route('login')
                    ->with('fail', 'Google sign-in failed. Please try again.');
            }
        } catch (\Throwable $e) {
            Log::error('Google callback failed', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->with('fail', 'Google sign-in failed. Please try again.');
        }

        $provider      = 'google';
        $providerId    = $googleUser->getId();
        $providerEmail = $googleUser->getEmail();
        $providerName  = $googleUser->getName() ?: 'Google User';
        $avatar        = $googleUser->getAvatar();

        // If this Google account is already linked, log that user in
        $link = GoogleAccountAuth::where([
            'provider'    => $provider,
            'provider_id' => $providerId,
        ])->first();

        if ($link) {
            $link->update([
                'provider_email' => $providerEmail,
                'avatar'         => $avatar,
            ]);

            $user = $link->user;
        } else {
            // Not linked yet: try to find an existing user with the same email
            $user = null;
            if ($providerEmail) {
                $user = User::where('email', $providerEmail)->first();
            }

            // No existing user? Create one with role "user"
            if (!$user) {
                $user = User::create([
                    'name'              => $providerName,
                    'email'             => $providerEmail,
                    'password'          => Str::random(32),
                    'role'              => User::ROLE_USER,
                    'status'            => 'active',
                    'is_hidden'         => false,
                    'email_verified_at' => now(),
                ]);
            }

            // Make sure status is active for Google users
            if ($user->status !== 'active') {
                $user->status = 'active';
                $user->save();
            }

            // Create the Google link record if it doesn’t exist yet
            if (!$link) {
                GoogleAccountAuth::create([
                    'user_id'        => $user->id,
                    'provider'       => $provider,
                    'provider_id'    => $providerId,
                    'provider_email' => $providerEmail,
                    'avatar'         => $avatar,
                ]);
            }
        }

        // Blocked users still can’t log in
        if (method_exists($user, 'isBlocked') && $user->isBlocked()) {
            return redirect()
                ->route('login')
                ->with('fail', 'Your account has been blocked. Please contact support.');
        }

        // Log in and regenerate session (same idea as Auth::attempt)
        Auth::login($user, true);
        $request->session()->regenerate();

        // Redirect EXACTLY like your email/password login
        if ($user->role === User::ROLE_ADMIN) {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Login successful! Welcome back Admin.');
        }

        return redirect()
            ->route('user.dashboard')
            ->with('success', 'Login successful! Welcome to your Dashboard.');
    }
}
