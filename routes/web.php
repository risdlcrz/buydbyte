<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordResetController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Guest routes (authentication)
Route::middleware('guest')->group(function () {
    // Login routes
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Registration routes
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Password reset routes
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// Email verification routes
Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
Route::get('/email/verify/{token}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

// Test route to check verification status
Route::get('/test-verification', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return response()->json([
            'user_id' => $user->user_id,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'hasVerifiedEmail' => $user->hasVerifiedEmail(),
            'status' => $user->status,
        ]);
    }
    return response()->json(['message' => 'Not authenticated']);
});

// Temporary bypass route for testing
Route::get('/dashboard-test', [AuthController::class, 'dashboard'])->middleware('auth')->name('dashboard.test');

// Manual verification route for testing
Route::get('/verify-manual', function () {
    if (Auth::check()) {
        $user = Auth::user();
        $user->update([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        
        // Also update any pending email verifications
        \App\Models\EmailVerification::where('user_id', $user->user_id)
            ->where('verified', false)
            ->update(['verified' => true]);
            
        return redirect()->route('dashboard')->with('message', 'Email verified manually! Welcome to BuyDbyte!');
    }
    return redirect()->route('login');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Product management routes
    Route::get('/products', function () {
        return view('products');
    })->name('products');
    
    Route::get('/products/add', function () {
        return view('add_product');
    })->name('products.add');
    
    Route::get('/products/edit/{id}', function ($id) {
        return view('edit_product', ['id' => $id]);
    })->name('products.edit');
    
    Route::get('/products/delete/{id}', function ($id) {
        return view('delete_product', ['id' => $id]);
    })->name('products.delete');
    
    // User management routes
    Route::get('/users', function () {
        return view('users');
    })->name('users');
    
    Route::get('/users/add', function () {
        return view('add_user');
    })->name('users.add');
    
    Route::get('/users/edit/{id}', function ($id) {
        return view('edit_user', ['id' => $id]);
    })->name('users.edit');
    
    Route::get('/users/delete/{id}', function ($id) {
        return view('delete_user', ['id' => $id]);
    })->name('users.delete');
});

// Authenticated but unverified routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
