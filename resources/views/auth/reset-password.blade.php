@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="text-center mb-4">
    <div class="text-primary mb-3">
        <i class="bi bi-shield-lock" style="font-size: 4rem;"></i>
    </div>
    <h4 class="fw-bold">Reset Password</h4>
    <p class="text-muted">
        Enter your new password below.
    </p>
</div>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    
    <!-- Hidden Token -->
    <input type="hidden" name="token" value="{{ $token }}">

    <!-- New Password -->
    <div class="mb-3">
        <label for="password" class="form-label">
            <i class="bi bi-lock"></i> New Password
        </label>
        <input type="password" 
               class="form-control @error('password') is-invalid @enderror" 
               id="password" 
               name="password" 
               required 
               autocomplete="new-password"
               placeholder="Enter your new password">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">
            Password must be at least 8 characters with uppercase, lowercase, and numbers.
        </div>
    </div>

    <!-- Confirm New Password -->
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">
            <i class="bi bi-lock-fill"></i> Confirm New Password
        </label>
        <input type="password" 
               class="form-control" 
               id="password_confirmation" 
               name="password_confirmation" 
               required 
               autocomplete="new-password"
               placeholder="Confirm your new password">
    </div>

    <!-- Submit Button -->
    <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-check-circle"></i> Reset Password
        </button>
    </div>

    <!-- Back to Login -->
    <div class="text-center mt-4">
        <p class="text-muted">
            <a href="{{ route('login') }}" class="fw-bold">
                <i class="bi bi-arrow-left"></i> Back to Login
            </a>
        </p>
    </div>
</form>
@endsection