<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Debug logging
            \Log::info('Verification check', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'hasVerifiedEmail' => $user->hasVerifiedEmail(),
                'status' => $user->status,
            ]);
            
            if (!$user->hasVerifiedEmail()) {
                \Log::info('Redirecting to verification notice');
                return redirect()->route('verification.notice');
            }
        }

        return $next($request);
    }
}
