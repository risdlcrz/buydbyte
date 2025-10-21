<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StoreIntendedUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only store the intended URL for GET requests to avoid storing form submissions
        // Also exclude AJAX requests and requests that expect JSON responses
        if ($request->isMethod('GET') && 
            !Auth::check() && 
            !$request->expectsJson() && 
            !$request->ajax()) {
            // Don't store auth-related URLs, API endpoints, or AJAX endpoints
            $excludedPaths = [
                'login',
                'register',
                'password',
                'email/verify',
                'logout',
                'api/',
                '_debugbar',
                'compare/count',      // AJAX endpoint for comparison count
                'cart/count',         // AJAX endpoint for cart count (if exists)
                'compare/add',        // AJAX endpoint for adding to comparison
                'compare/remove',     // AJAX endpoint for removing from comparison
                'cart/add',           // AJAX endpoint for adding to cart
                'cart/update',        // AJAX endpoint for updating cart
                'cart/remove',        // AJAX endpoint for removing from cart
            ];

            $currentPath = $request->path();
            $shouldStore = true;

            foreach ($excludedPaths as $excludedPath) {
                if (str_starts_with($currentPath, $excludedPath)) {
                    $shouldStore = false;
                    break;
                }
            }

            if ($shouldStore) {
                $request->session()->put('intended_url', $request->fullUrl());
            }
        }

        return $next($request);
    }
}