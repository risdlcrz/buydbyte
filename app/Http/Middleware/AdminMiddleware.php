<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            // Store the intended URL for redirect after login (for admin pages)
            $request->session()->put('url.intended', $request->url());
            return redirect()->route('login')->with('error', 'Please login to access admin area.');
        }

        if (!Auth::user()->isAdmin()) {
            // For non-admin users trying to access admin area, redirect to homepage
            return redirect()->route('storefront.home')->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
