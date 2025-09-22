@extends('layouts.auth')

@section('title', 'Dashboard')

@section('additional_css')
<style>
    .auth-container {
        min-height: 100vh;
    }
    .auth-card {
        max-width: 800px;
    }
    .stats-card {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    .welcome-card {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
</style>
@endsection

@section('content')
<div class="welcome-card text-center">
    <h2 class="mb-3">
        <i class="bi bi-house-heart"></i>
        Welcome, {{ Auth::user()->full_name }}!
    </h2>
    <p class="mb-0">
        {{ Auth::user()->role === 'admin' ? 'Admin Dashboard' : 'Customer Dashboard' }}
    </p>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="stats-card text-center">
            <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
            <h5 class="mt-2">Profile</h5>
            <p class="mb-0">Account Status: <span class="badge bg-success">{{ ucfirst(Auth::user()->status) }}</span></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card text-center">
            <i class="bi bi-envelope-check" style="font-size: 2rem;"></i>
            <h5 class="mt-2">Email</h5>
            <p class="mb-0">
                @if(Auth::user()->hasVerifiedEmail())
                    <span class="badge bg-success">Verified</span>
                @else
                    <span class="badge bg-warning">Pending</span>
                @endif
            </p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card text-center">
            <i class="bi bi-shield-check" style="font-size: 2rem;"></i>
            <h5 class="mt-2">Security</h5>
            <p class="mb-0">Role: <span class="badge bg-info">{{ ucfirst(Auth::user()->role) }}</span></p>
        </div>
    </div>
</div>

<div class="card" style="border: none; border-radius: 15px;">
    <div class="card-body">
        <h5 class="card-title">
            <i class="bi bi-gear"></i> Quick Actions
        </h5>
        <div class="row">
            <div class="col-md-6">
                <a href="#" class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-person"></i> Edit Profile
                </a>
                <a href="#" class="btn btn-outline-secondary w-100 mb-2">
                    <i class="bi bi-geo-alt"></i> Manage Addresses
                </a>
            </div>
            <div class="col-md-6">
                <a href="#" class="btn btn-outline-info w-100 mb-2">
                    <i class="bi bi-bag-heart"></i> Browse Products
                </a>
                <a href="#" class="btn btn-outline-success w-100 mb-2">
                    <i class="bi bi-cart"></i> View Cart
                </a>
            </div>
        </div>
    </div>
</div>

<div class="text-center mt-4">
    <form method="POST" action="{{ route('logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-outline-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>
</div>
@endsection