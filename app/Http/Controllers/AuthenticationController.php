<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use App\Mail\VerifyEmail;

// Added for social logins and logging
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthenticationController extends Controller
{
    public function loadRegisterForm()
    {
        return view('auth.register');
    }

    public function registerUser(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email:rfc,dns|max:255|unique:users,email',
            'phone_number' => 'required|string|max:20|unique:users,phone_number',
            'address'      => 'required|string|max:255',
            'password'     => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'address'      => $validated['address'],
            'password'     => Hash::make($validated['password']),
            'role'         => 'user',
            // 'is_blocked'   => true, // only keep this if you really have this column + fillable
        ]);

        // Use PUBLIC_URL (ngrok) so phones can open the link
        $rootUrl = config('app.public_url');

        if ($rootUrl) {
            URL::forceRootUrl($rootUrl);

            $scheme = parse_url($rootUrl, PHP_URL_SCHEME) ?: 'https';
            URL::forceScheme($scheme);
        }

        // Signed verification URL (valid for 60 minutes)
        $verificationUrl = URL::temporarySignedRoute(
            'verify.email',
            Carbon::now()->addMinutes(60),
            ['user' => $user->id]
        );

        // Send verification email
        Mail::to($user->email)->send(new VerifyEmail($user, $verificationUrl));

        return redirect()->route('login')
            ->with('verify_notice', 'Account created. Please check your email to verify your account.');
    }

    /**
     * Called when user clicks the email link: /verify-email/{user}
     * Route: verify.email (already added in routes/web.php)
     */
    public function verifyEmail(Request $request, User $user)
    {
        // 'signed' middleware already checked the URL signature

        // If already verified, just send them to login
        if ($user->email_verified_at) {
            return redirect()->route('login')
                ->with('success', 'Your email is already verified. You can log in.');
        }

        // Mark email as verified
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('login')
            ->with('success', 'Email verified! You can now log in.');
    }

    public function loadLoginForm()
    {
        return view('auth.login');
    }

    public function loginUser(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->filled('remember');

        // 1) Email must exist
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('fail', 'No account found for that email.')
                ->withInput($request->only('email'));
        }

        // 2) Must verify email first
        if (is_null($user->email_verified_at)) {
            return back()->with('fail', 'Please verify your email first. Check your inbox.')
                ->withInput($request->only('email'));
        }

        // 3) Blocked users cannot log in (if you use a "status" column)
        if (($user->status ?? 'active') === 'blocked') {
            return back()->with('fail', 'Your account has been blocked. Please contact support.')
                ->withInput($request->only('email'));
        }

        // 4) Attempt login (only "active" users if you have that status column)
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
            'status'   => 'active', // remove this line if you DON'T have a "status" column
        ];

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')->with('success', 'Login successful! Welcome back Admin.')
                : redirect()->route('user.dashboard')->with('success', 'Login successful! Welcome to your Dashboard.');
        }

        return back()->with('fail', 'Invalid email or password.')
            ->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login')->with('success', 'You have been logged out');
    }

    // Resend verification email
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        $genericMessage = 'If an account with that email exists and is not yet verified, we have resent the verification link.';

        if (!$user) {
            return back()->with('success', $genericMessage)
                ->withInput($request->only('email'));
        }

        if ($user->email_verified_at) {
            return back()->with('success', 'Your email is already verified. You can log in.')
                ->withInput($request->only('email'));
        }

        $rootUrl = config('app.public_url');
        if ($rootUrl) {
            URL::forceRootUrl($rootUrl);
            $scheme = parse_url($rootUrl, PHP_URL_SCHEME) ?: 'https';
            URL::forceScheme($scheme);
        }

        $verificationUrl = URL::temporarySignedRoute(
            'verify.email',
            Carbon::now()->addMinutes(60),
            ['user' => $user->id]
        );

        Mail::to($user->email)->send(new VerifyEmail($user, $verificationUrl));

        return back()->with('success', $genericMessage)
            ->withInput($request->only('email'));
    }

    /**
     * GOOGLE LOGIN: redirect to Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    /**
     * GOOGLE LOGIN: handle callback from Google
     */
    public function handleGoogleCallback()
    {
        try {
            // Get user from Google
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Find or create local user
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name'              => $googleUser->getName() ?: $googleUser->getNickname(),
                    'password'          => Hash::make(Str::random(32)),
                    'phone_number'      => null,
                    'address'           => null,
                    'role'              => 'user',
                    'status'            => 'active',   // assuming you have a "status" column
                    'email_verified_at' => now(),      // trust Google email as verified
                ]
            );

            // If you have an is_blocked column and default it, ensure they aren't blocked
            if (property_exists($user, 'is_blocked')) {
                $user->is_blocked = false;
                $user->save();
            }

            // Log them in (no password)
            Auth::login($user, true);

            return redirect()->route('user.dashboard')
                ->with('success', 'Logged in with Google.');
        } catch (\Exception $e) {
            // This is where the "Google sign-in failed. Please try again." comes from
            Log::error('Google sign-in error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('login')
                ->with('fail', 'Google sign-in failed. Please try again.');
        }
    }
}
