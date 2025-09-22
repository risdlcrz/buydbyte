@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<div class="text-center mb-4">
    <div class="text-primary mb-3">
        <i class="bi bi-key" style="font-size: 4rem;"></i>
    </div>
    <h4 class="fw-bold">Forgot Password?</h4>
    <p class="text-muted">
        No problem! Enter your email address and we'll send you a link to reset your password.
    </p>
</div>

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    
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
               placeholder="Enter your email address">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Submit Button -->
    <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-envelope-arrow-up"></i> Send Reset Link
        </button>
    </div>

    <!-- Back to Login -->
    <div class="text-center mt-4">
        <p class="text-muted">
            Remember your password? 
            <a href="{{ route('login') }}" class="fw-bold">
                <i class="bi bi-arrow-left"></i> Back to Login
            </a>
        </p>
    </div>
</form>
@endsection