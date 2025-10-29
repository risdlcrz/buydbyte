<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PromotionController;

// Public Storefront Routes
Route::get('/', [StorefrontController::class, 'index'])->name('storefront.home');
Route::get('/products', [StorefrontController::class, 'products'])->name('storefront.products');
Route::get('/product/{product}', [StorefrontController::class, 'product'])->name('storefront.product');
Route::get('/category/{category}', [StorefrontController::class, 'category'])->name('storefront.category');

// Cart Routes (for both guests and authenticated users)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{cart}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{cart}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});

// Separate cart count route to avoid route group conflicts
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

Route::prefix('checkout')->name('checkout.')->middleware(['auth'])->group(function () {
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
});

// Product Comparison Routes (for both guests and authenticated users)
Route::prefix('compare')->name('compare.')->group(function () {
    Route::get('/', [ComparisonController::class, 'index'])->name('index');
    Route::post('/add/{product}', [ComparisonController::class, 'add'])->name('add');
    Route::delete('/remove/{product}', [ComparisonController::class, 'remove'])->name('remove');
    Route::delete('/clear', [ComparisonController::class, 'clear'])->name('clear');
    Route::get('/count', [ComparisonController::class, 'count'])->name('count');
});

// Discount Routes (for both guests and authenticated users)
Route::prefix('discount')->name('discount.')->group(function () {
    Route::post('/apply', [\App\Http\Controllers\DiscountController::class, 'apply'])->name('apply');
    Route::delete('/remove', [\App\Http\Controllers\DiscountController::class, 'remove'])->name('remove');
    Route::get('/current', [\App\Http\Controllers\DiscountController::class, 'current'])->name('current');
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

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/account', [AuthController::class, 'dashboard'])->name('account'); // Alternative route for customer dashboard
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Authenticated but unverified routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', UserController::class);
    
    // Category Management
    Route::resource('categories', CategoryController::class);
    
    // Product Management
    Route::resource('products', ProductController::class);
    
    // Promotion Management
    Route::resource('promotions', PromotionController::class);
    
    // Attribute Definition Management
    Route::resource('attributes', \App\Http\Controllers\Admin\AttributeDefinitionController::class);
    Route::post('attributes/{attribute}/toggle-status', [\App\Http\Controllers\Admin\AttributeDefinitionController::class, 'toggleStatus'])
        ->name('attributes.toggle-status');
    
    // Product Attributes Management
    Route::get('product-attributes', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'index'])
        ->name('product-attributes.index');
    Route::get('product-attributes/{product}/edit', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'edit'])
        ->name('product-attributes.edit');
    Route::put('product-attributes/{product}', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'update'])
        ->name('product-attributes.update');
    Route::post('product-attributes/bulk-edit', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'bulkEdit'])
        ->name('product-attributes.bulk-edit');
    Route::get('product-attributes/suggestions', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'suggestions'])
        ->name('product-attributes.suggestions');
    Route::post('product-attributes/copy', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'copyAttributes'])
        ->name('product-attributes.copy');
});
