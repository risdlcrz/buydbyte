@extends('layouts.auth')

@section('title', 'Verify Email')

@section('content')
<div class="text-center mb-4">
    <div class="text-primary mb-3">
        <i class="bi bi-envelope-check" style="font-size: 4rem;"></i>
    </div>
    <h4 class="fw-bold">Check Your Email</h4>
    <p class="text-muted">
        We've sent a verification link to your email address. 
        Please check your inbox and click the link to verify your account.
    </p>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    <strong>Didn't receive the email?</strong> 
    Check your spam folder or request a new verification email below.
</div>

<!-- Resend Verification Form -->
<form method="POST" action="{{ route('verification.resend') }}">
    @csrf
    
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
               placeholder="Enter your email">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-outline-primary">
            <i class="bi bi-arrow-clockwise"></i> Resend Verification Email
        </button>
    </div>
</form>

<!-- Back to Login -->
<div class="text-center mt-4">
    <p class="text-muted">
        <a href="{{ route('login') }}" class="fw-bold">
            <i class="bi bi-arrow-left"></i> Back to Login
        </a>
    </p>
</div>
@endsection