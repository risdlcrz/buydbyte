@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-4x text-primary"></i>
                    </div>
                    <h5 class="card-title">{{ $user->full_name }}</h5>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                    <p class="text-muted">{{ ucfirst($user->role) }}</p>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action {{ Request::routeIs('profile.edit') ? 'active' : '' }}">
                        <i class="fas fa-user-edit me-2"></i>Edit Profile
                    </a>
                    <a href="{{ route('profile.password') }}" class="list-group-item list-group-item-action {{ Request::routeIs('profile.password') ? 'active' : '' }}">
                        <i class="fas fa-key me-2"></i>Change Password
                    </a>
                    <a href="{{ route('profile.addresses') }}" class="list-group-item list-group-item-action {{ Request::routeIs('profile.addresses') ? 'active' : '' }}">
                        <i class="fas fa-map-marker-alt me-2"></i>Addresses
                    </a>
                    <a href="{{ route('profile.notifications') }}" class="list-group-item list-group-item-action {{ Request::routeIs('profile.notifications') ? 'active' : '' }}">
                        <i class="fas fa-bell me-2"></i>Notifications
                        @if($unreadNotifications > 0)
                            <span class="badge bg-danger float-end">{{ $unreadNotifications }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            @yield('profile-content')
        </div>
    </div>
</div>
@endsection