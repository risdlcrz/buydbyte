<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerification;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function notice()
    {
        return view('auth.verify-email');
    }

    /**
     * Verify the email address.
     */
    public function verify(Request $request, $token)
    {
        $verification = EmailVerification::where('token', $token)->first();

        if (!$verification || !$verification->isValid()) {
            AuditLog::createLog('email_verification_failed', null, ['token' => $token]);
            return redirect()->route('login')->withErrors(['email' => 'Invalid or expired verification link.']);
        }

        $user = $verification->user;
        
        // Update user status and email verification
        $user->update([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $verification->update(['verified' => true]);

        AuditLog::createLog('email_verified', $user->user_id);

        return redirect()->route('login')->with('message', 'Email verified successfully! You can now login.');
    }

    /**
     * Resend verification email.
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return back()->withErrors(['email' => 'Email is already verified.']);
        }

        // Check for recent resend attempts to prevent spam
        $recentResend = AuditLog::where('action', 'verification_email_resent')
            ->where('user_id', $user->user_id)
            ->where('created_at', '>', now()->subMinutes(2))
            ->exists();
            
        if ($recentResend) {
            return back()->withErrors(['email' => 'Please wait 2 minutes before requesting another verification email.']);
        }

        try {
            // Invalidate old tokens
            EmailVerification::where('user_id', $user->user_id)->update(['verified' => true]);

            // Create new verification token
            $verificationToken = Str::random(60);
            EmailVerification::create([
                'user_id' => $user->user_id,
                'token' => $verificationToken,
                'expires_at' => now()->addHours(24),
            ]);

            // Send verification email with proper error handling
            try {
                if (config('app.env') === 'local' || config('queue.default') === 'sync') {
                    // Send immediately in development
                    Mail::to($user->email)->send(new \App\Mail\Auth\VerifyEmail($user, $verificationToken));
                } else {
                    // Queue the email for background processing in production
                    Mail::to($user->email)->queue(new \App\Mail\Auth\VerifyEmail($user, $verificationToken));
                }
                
                AuditLog::createLog('verification_email_resent', $user->user_id);
                
                return back()->with('message', 'Verification email sent! Please check your inbox and spam folder.');
                
            } catch (\Exception $e) {
                \Log::error('Failed to send verification email: ' . $e->getMessage());
                return back()->withErrors(['email' => 'Failed to send verification email. Please try again later.']);
            }
            
        } catch (\Exception $e) {
            \Log::error('Resend verification failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Something went wrong. Please try again.']);
        }
    }
}
