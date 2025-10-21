<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSession;
use App\Models\EmailVerification;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

            // Determine where to redirect the user based on role and intended URL
            return $this->getPostLoginRedirect($request, $user);
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
        // Check for recent registration attempts from same IP to prevent spam
        $recentAttempt = AuditLog::where('action', 'user_registered')
            ->where('ip_address', $request->ip())
            ->where('created_at', '>', now()->subMinutes(2))
            ->exists();
            
        if ($recentAttempt) {
            return back()->withErrors([
                'email' => 'Please wait a moment before creating another account.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        try {
            DB::beginTransaction();

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

            // Log the registration
            AuditLog::createLog('user_registered', $user->user_id);

            DB::commit();

            // Send verification email immediately in development, queue in production
            try {
                if (config('app.env') === 'local' || config('queue.default') === 'sync') {
                    // Send immediately in development
                    Mail::to($user->email)->send(new \App\Mail\Auth\VerifyEmail($user, $verificationToken));
                } else {
                    // Queue the email for background processing in production
                    Mail::to($user->email)->queue(new \App\Mail\Auth\VerifyEmail($user, $verificationToken));
                }
            } catch (\Exception $e) {
                // Log email error but don't fail registration
                \Log::error('Failed to send verification email: ' . $e->getMessage());
            }

            return redirect()->route('verification.notice')->with('message', 'Registration successful! Please check your email to verify your account.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Registration failed: ' . $e->getMessage());
            
            return back()->withErrors([
                'email' => 'Registration failed. Please try again.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }
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
        $user = Auth::user();
        
        // Redirect admin users to admin dashboard
        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('dashboard');
    }

    /**
     * Determine where to redirect user after successful login.
     */
    protected function getPostLoginRedirect(Request $request, User $user)
    {
        // For admin users, check if they were trying to access an admin page
        if ($user->isAdmin()) {
            $intendedUrl = $request->session()->get('url.intended');
            if ($intendedUrl && str_contains($intendedUrl, '/admin')) {
                return redirect($intendedUrl);
            }
            // Default admin redirect
            return redirect()->route('admin.dashboard');
        }

        // For regular customers, check for intended URL from our middleware
        $intendedUrl = $request->session()->get('intended_url');
        
        // Clear the intended URL from session
        $request->session()->forget('intended_url');
        $request->session()->forget('url.intended');

        if ($intendedUrl) {
            // Make sure the intended URL is safe and belongs to our domain
            $parsedUrl = parse_url($intendedUrl);
            $currentDomain = $request->getHost();
            
            if (!isset($parsedUrl['host']) || $parsedUrl['host'] === $currentDomain) {
                // Exclude certain paths from redirect (like auth pages and AJAX endpoints)
                $excludedPaths = [
                    '/login',
                    '/register', 
                    '/password',
                    '/email/verify',
                    '/admin',
                    '/dashboard',
                    '/compare/count',    // AJAX endpoint
                    '/cart/count',       // AJAX endpoint (if exists)
                ];
                
                $path = $parsedUrl['path'] ?? '/';
                $shouldRedirect = true;
                
                foreach ($excludedPaths as $excludedPath) {
                    if (str_starts_with($path, $excludedPath)) {
                        $shouldRedirect = false;
                        break;
                    }
                }
                
                if ($shouldRedirect) {
                    return redirect($intendedUrl);
                }
            }
        }

        // Default redirect for customers: homepage instead of dashboard
        return redirect()->route('storefront.home');
    }
}
