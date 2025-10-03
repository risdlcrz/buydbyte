<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerification;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

        // Invalidate old tokens
        EmailVerification::where('user_id', $user->user_id)->update(['verified' => true]);

        // Create new verification token
        $verificationToken = Str::random(60);
        EmailVerification::create([
            'user_id' => $user->user_id,
            'token' => $verificationToken,
            'expires_at' => now()->addHours(24),
        ]);

        // Send verification email (you'll need to create the mailable)
        Mail::to($user->email)->send(new \App\Mail\Auth\VerifyEmail($user, $verificationToken));

        AuditLog::createLog('verification_email_resent', $user->user_id);

        return back()->with('message', 'Verification email sent! Please check your inbox.');
    }
}
