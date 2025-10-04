@extends('layouts.auth')

@section('title', 'Register')

@section('additional_css')
<style>
    .auth-card {
        max-width: 550px;
    }
</style>
@endsection

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf
    
    <div class="text-center mb-4">
        <h4 class="fw-bold">Create Account</h4>
        <p class="text-muted">Join BuyDbyte today</p>
    </div>

    <!-- First Name & Last Name -->
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="first_name" class="form-label">
                <i class="bi bi-person"></i> First Name *
            </label>
            <input type="text" 
                   class="form-control @error('first_name') is-invalid @enderror" 
                   id="first_name" 
                   name="first_name" 
                   value="{{ old('first_name') }}" 
                   required 
                   autocomplete="given-name"
                   placeholder="First Name">
            @error('first_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="last_name" class="form-label">
                <i class="bi bi-person"></i> Last Name
            </label>
            <input type="text" 
                   class="form-control @error('last_name') is-invalid @enderror" 
                   id="last_name" 
                   name="last_name" 
                   value="{{ old('last_name') }}" 
                   autocomplete="family-name"
                   placeholder="Last Name">
            @error('last_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Email Address -->
    <div class="mb-3">
        <label for="email" class="form-label">
            <i class="bi bi-envelope"></i> Email Address *
        </label>
        <input type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               id="email" 
               name="email" 
               value="{{ old('email') }}" 
               required 
               autocomplete="email"
               placeholder="Enter your email">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Phone Number -->
    <div class="mb-3">
        <label for="phone_number" class="form-label">
            <i class="bi bi-telephone"></i> Phone Number
        </label>
        <input type="tel" 
               class="form-control @error('phone_number') is-invalid @enderror" 
               id="phone_number" 
               name="phone_number" 
               value="{{ old('phone_number') }}" 
               autocomplete="tel"
               placeholder="09XX XXX XXXX">
        @error('phone_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label">
            <i class="bi bi-lock"></i> Password *
        </label>
        <input type="password" 
               class="form-control @error('password') is-invalid @enderror" 
               id="password" 
               name="password" 
               required 
               autocomplete="new-password"
               placeholder="Create a strong password">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">
            Password must be at least 8 characters with uppercase, lowercase, and numbers.
        </div>
    </div>

    <!-- Confirm Password -->
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">
            <i class="bi bi-lock-fill"></i> Confirm Password *
        </label>
        <input type="password" 
               class="form-control" 
               id="password_confirmation" 
               name="password_confirmation" 
               required 
               autocomplete="new-password"
               placeholder="Confirm your password">
    </div>

    <!-- Terms & Conditions -->
    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="terms" required>
            <label class="form-check-label" for="terms">
                I agree to the <a href="#" class="fw-bold">Terms of Service</a> and 
                <a href="#" class="fw-bold">Privacy Policy</a>
            </label>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
            <span class="btn-text">
                <i class="bi bi-person-plus"></i> Create Account
            </span>
            <span class="btn-loading d-none">
                <div class="spinner-border spinner-border-sm me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                Creating Account...
            </span>
        </button>
    </div>

    <!-- Login Link -->
    <div class="text-center mt-4">
        <p class="text-muted">
            Already have an account? 
            <a href="{{ route('login') }}" class="fw-bold">Sign In</a>
        </p>
    </div>
</form>

@section('additional_js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    
    form.addEventListener('submit', function(e) {
        // Prevent double submission
        if (submitBtn.disabled) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        
        // Re-enable button after 30 seconds in case of timeout
        setTimeout(() => {
            submitBtn.disabled = false;
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
        }, 30000);
    });
});
</script>
@endsection
@endsection