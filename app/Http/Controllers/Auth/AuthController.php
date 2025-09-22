<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSession;
use App\Models\EmailVerification;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Log the login attempt
        AuditLog::createLog('login_attempt', null, ['email' => $credentials['email']]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Check if user is active
            if (!$user->isActive()) {
                Auth::logout();
                AuditLog::createLog('login_failed_inactive', $user->user_id);
                return back()->withErrors([
                    'email' => 'Your account is not active. Please contact support.',
                ]);
            }

            // Create user session record
            UserSession::create([
                'user_id' => $user->user_id,
                'session_token' => session()->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'login_time' => now(),
            ]);

            AuditLog::createLog('login_success', $user->user_id);

            return redirect()->intended(route('dashboard'));
        }

        AuditLog::createLog('login_failed', null, ['email' => $credentials['email']]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Create the user
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'status' => 'pending_verification',
        ]);

        // Create email verification token
        $verificationToken = Str::random(60);
        EmailVerification::create([
            'user_id' => $user->user_id,
            'token' => $verificationToken,
            'expires_at' => now()->addHours(24),
        ]);

        // Send verification email (you'll need to create the mailable)
        Mail::to($user->email)->send(new \App\Mail\Auth\VerifyEmail($user, $verificationToken));

        AuditLog::createLog('user_registered', $user->user_id);

        return redirect()->route('verification.notice')->with('message', 'Registration successful! Please check your email to verify your account.');
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Update user session
        UserSession::where('session_token', session()->getId())
            ->where('user_id', $user->user_id)
            ->update(['logout_time' => now()]);

        AuditLog::createLog('logout', $user->user_id);

        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Show the dashboard.
     */
    public function dashboard()
    {
        return view('dashboard');
    }
}
