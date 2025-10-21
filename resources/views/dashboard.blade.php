@extends('layouts.storefront')

@section('title', 'My Account')

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('storefront.home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Account</li>
        </ol>
    </nav>

    <!-- Welcome Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h2 mb-2">
                                <i class="bi bi-person-circle me-2"></i>
                                Welcome back, {{ Auth::user()->first_name }}!
                            </h1>
                            <p class="mb-0 opacity-90">
                                Manage your account, track your activity, and discover amazing products.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex flex-column text-center text-md-end">
                                <small class="opacity-75">Member since</small>
                                <strong>{{ Auth::user()->created_at->format('F Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-cart text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="card-title">Cart Items</h5>
                    <h2 class="text-primary mb-0" id="cart-items-count">
                        {{ Auth::user()->cartItems()->count() }}
                    </h2>
                    <small class="text-muted">Items in cart</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-bar-chart text-info" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="card-title">Comparisons</h5>
                    <h2 class="text-info mb-0" id="comparison-count-dash">
                        {{ Auth::user()->productComparisons()->count() }}
                    </h2>
                    <small class="text-muted">Products compared</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-shield-check text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="card-title">Account Status</h5>
                    <span class="badge bg-success fs-6 mb-2">{{ ucfirst(Auth::user()->status) }}</span>
                    <br>
                    <small class="text-muted">Account is active</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-envelope-check text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="card-title">Email Status</h5>
                    @if(Auth::user()->hasVerifiedEmail())
                        <span class="badge bg-success fs-6 mb-2">Verified</span>
                        <br>
                        <small class="text-muted">Email confirmed</small>
                    @else
                        <span class="badge bg-warning fs-6 mb-2">Pending</span>
                        <br>
                        <small class="text-muted">
                            <a href="{{ route('verification.notice') }}" class="text-decoration-none">Verify email</a>
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-4">
                    <h4 class="mb-0">
                        <i class="bi bi-lightning-charge me-2"></i>
                        Quick Actions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('storefront.products') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-shop me-2"></i>
                                Browse Products
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="bi bi-cart me-2"></i>
                                View Cart
                                <span class="badge bg-success ms-2" id="cart-badge">{{ Auth::user()->cartItems()->count() }}</span>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('compare.index') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="bi bi-bar-chart me-2"></i>
                                Compare Products
                                <span class="badge bg-info ms-2" id="compare-badge">{{ Auth::user()->productComparisons()->count() }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information & Recent Activity -->
    <div class="row">
        <!-- Account Information -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-4">
                    <h5 class="mb-0">
                        <i class="bi bi-person-gear me-2"></i>
                        Account Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Name:</strong>
                        </div>
                        <div class="col-8">
                            {{ Auth::user()->full_name }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Email:</strong>
                        </div>
                        <div class="col-8">
                            {{ Auth::user()->email }}
                            @if(Auth::user()->hasVerifiedEmail())
                                <i class="bi bi-patch-check-fill text-success ms-1" title="Verified"></i>
                            @else
                                <i class="bi bi-exclamation-triangle-fill text-warning ms-1" title="Unverified"></i>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Phone:</strong>
                        </div>
                        <div class="col-8">
                            {{ Auth::user()->phone ?: 'Not provided' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Member Since:</strong>
                        </div>
                        <div class="col-8">
                            {{ Auth::user()->created_at->format('F j, Y') }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <strong>User ID:</strong>
                        </div>
                        <div class="col-8">
                            <code class="text-muted small">{{ Auth::user()->user_id }}</code>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="alert('Profile editing feature coming soon!')">
                            <i class="bi bi-pencil me-2"></i>
                            Edit Profile
                        </button>
                        <button class="btn btn-outline-secondary" onclick="alert('Address management feature coming soon!')">
                            <i class="bi bi-geo-alt me-2"></i>
                            Manage Addresses
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-4">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $recentCartItems = Auth::user()->cartItems()->with('product')->latest()->take(3)->get();
                        $recentComparisons = Auth::user()->productComparisons()->with('product')->latest()->take(2)->get();
                    @endphp
                    
                    @if($recentCartItems->count() > 0)
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-cart-plus me-1"></i>
                            Recent Cart Additions
                        </h6>
                        @foreach($recentCartItems as $cartItem)
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded bg-primary bg-opacity-10 p-2 me-3">
                                    <i class="bi bi-box text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $cartItem->product->name }}</div>
                                    <small class="text-muted">Qty: {{ $cartItem->quantity }} • {{ $cartItem->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                        <hr class="my-3">
                    @endif
                    
                    @if($recentComparisons->count() > 0)
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-bar-chart me-1"></i>
                            Recent Comparisons
                        </h6>
                        @foreach($recentComparisons as $comparison)
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded bg-info bg-opacity-10 p-2 me-3">
                                    <i class="bi bi-graph-up text-info"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $comparison->product->name }}</div>
                                    <small class="text-muted">{{ $comparison->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    
                    @if($recentCartItems->count() === 0 && $recentComparisons->count() === 0)
                        <div class="text-center py-4">
                            <i class="bi bi-clock-history display-4 text-muted opacity-50"></i>
                            <p class="text-muted mt-3">No recent activity</p>
                            <a href="{{ route('storefront.products') }}" class="btn btn-sm btn-outline-primary">
                                Start Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Security & Settings -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-4">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>
                        Security & Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <button class="btn btn-outline-warning w-100 py-3" onclick="alert('Password change feature coming soon!')">
                                <i class="bi bi-key me-2"></i>
                                Change Password
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-info w-100 py-3" onclick="alert('Privacy settings feature coming soon!')">
                                <i class="bi bi-eye-slash me-2"></i>
                                Privacy Settings
                            </button>
                        </div>
                        <div class="col-md-4">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100 py-3">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update cart and comparison counts dynamically
    updateDashboardCounts();
});

function updateDashboardCounts() {
    // Update comparison count
    fetch('{{ route("compare.count") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        const comparisonCount = document.getElementById('comparison-count-dash');
        const compareBadge = document.getElementById('compare-badge');
        if (comparisonCount) comparisonCount.textContent = data.count;
        if (compareBadge) compareBadge.textContent = data.count;
    })
    .catch(error => console.error('Error updating comparison count:', error));
}
</script>
@endpush
@endsection