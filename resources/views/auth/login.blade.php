@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    
    <div class="text-center mb-4">
        <h4 class="fw-bold">Welcome Back!</h4>
        <p class="text-muted">Sign in to your account</p>
    </div>

    <!-- Email Address -->
    <div class="mb-3">
        <label for="email" class="form-label">
            <i class="bi bi-envelope"></i> Email Address
        </label>
        <input type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               id="email" 
               name="email" 
               value="{{ old('email') }}" 
               required 
               autocomplete="email" 
               autofocus
               placeholder="Enter your email">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label">
            <i class="bi bi-lock"></i> Password
        </label>
        <input type="password" 
               class="form-control @error('password') is-invalid @enderror" 
               id="password" 
               name="password" 
               required 
               autocomplete="current-password"
               placeholder="Enter your password">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Remember Me & Forgot Password -->
    <div class="row mb-3">
        <div class="col">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">
                    Remember me
                </label>
            </div>
        </div>
        <div class="col text-end">
            <a href="{{ route('password.request') }}" class="text-decoration-none">
                Forgot Password?
            </a>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-box-arrow-in-right"></i> Sign In
        </button>
    </div>

    <!-- Register Link -->
    <div class="text-center mt-4">
        <p class="text-muted">
            Don't have an account? 
            <a href="{{ route('register') }}" class="fw-bold">Create Account</a>
        </p>
    </div>
</form>
@endsection