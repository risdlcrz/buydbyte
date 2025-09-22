<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordReset;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot password form.
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle forgot password request.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        // Invalidate old tokens
        PasswordReset::where('user_id', $user->user_id)->update(['used' => true]);

        // Create new reset token
        $token = Str::random(60);
        PasswordReset::create([
            'user_id' => $user->user_id,
            'token' => $token,
            'expires_at' => now()->addHour(),
            'used' => false,
        ]);

        // Send reset email (you'll need to create the mailable)
        Mail::to($user->email)->send(new \App\Mail\Auth\ResetPassword($user, $token));

        AuditLog::createLog('password_reset_requested', $user->user_id);

        return back()->with('message', 'Password reset link sent to your email!');
    }

    /**
     * Handle password reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $passwordReset = PasswordReset::where('token', $request->token)->first();

        if (!$passwordReset || !$passwordReset->isValid()) {
            AuditLog::createLog('password_reset_failed', null, ['token' => $request->token]);
            return back()->withErrors(['token' => 'Invalid or expired reset token.']);
        }

        $user = $passwordReset->user;
        
        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Mark token as used
        $passwordReset->update(['used' => true]);

        AuditLog::createLog('password_reset_success', $user->user_id);

        return redirect()->route('login')->with('message', 'Password updated successfully! You can now login with your new password.');
    }
}
