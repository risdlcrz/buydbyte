@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit User: {{ $user->name }}</h5>
            </div>
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <!-- Basic Information -->
                    <h6 class="fw-bold mb-3">Basic Information</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" 
                                   id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Password -->
                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Password</h6>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" 
                               placeholder="Leave blank to keep current password">
                        <div class="form-text">Only fill this if you want to change the user's password.</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" 
                               placeholder="Confirm new password">
                    </div>

                    <!-- Role & Status -->
                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Account Settings</h6>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" name="role" required>
                                <option value="customer" {{ old('role', $user->role) === 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="pending_verification" {{ old('status', $user->status) === 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email Verification</label>
                            <div class="mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_verified" name="email_verified" 
                                           value="1" {{ $user->email_verified_at ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_verified">
                                        Email Verified
                                    </label>
                                </div>
                            </div>
                            @if($user->email_verified_at)
                                <small class="text-muted">
                                    Verified {{ $user->email_verified_at->diffForHumans() }}
                                </small>
                            @endif
                        </div>
                    </div>

                    <!-- Account Information -->
                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Account Information</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>Account Created:</strong> {{ $user->created_at->format('M d, Y H:i') }}
                            </div>
                            <div class="mb-2">
                                <strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y H:i') }}
                            </div>
                            @if($user->email_verified_at)
                            <div class="mb-2">
                                <strong>Email Verified:</strong> {{ $user->email_verified_at->format('M d, Y H:i') }}
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>User ID:</strong> <code>{{ $user->user_id }}</code>
                            </div>
                            <div class="mb-2">
                                <strong>Addresses:</strong> {{ $user->addresses ? $user->addresses->count() : 0 }}
                            </div>
                            <div class="mb-2">
                                <strong>Cart Items:</strong> {{ $user->cartItems ? $user->cartItems->count() : 0 }}
                            </div>
                        </div>
                    </div>

                    @if($user->user_id === auth()->id())
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Note:</strong> You are editing your own account. Be careful when changing role or status.
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Update User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Password confirmation validation
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const confirmation = document.getElementById('password_confirmation');
    
    if (password && !confirmation.value) {
        confirmation.required = true;
    } else if (!password) {
        confirmation.required = false;
    }
});

document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmation = this.value;
    
    if (password && password !== confirmation) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

// Role change warning
document.getElementById('role').addEventListener('change', function() {
    const isOwnAccount = {{ $user->user_id === auth()->id() ? 'true' : 'false' }};
    const currentRole = '{{ $user->role }}';
    const newRole = this.value;
    
    if (isOwnAccount && currentRole === 'admin' && newRole !== 'admin') {
        if (!confirm('Warning: You are removing admin privileges from your own account. You will lose access to the admin area. Are you sure?')) {
            this.value = currentRole;
        }
    }
});

// Status change warning
document.getElementById('status').addEventListener('change', function() {
    const isOwnAccount = {{ $user->user_id === auth()->id() ? 'true' : 'false' }};
    const currentStatus = '{{ $user->status }}';
    const newStatus = this.value;
    
    if (isOwnAccount && currentStatus === 'active' && newStatus !== 'active') {
        if (!confirm('Warning: You are changing your own account status. This may affect your ability to access the system. Are you sure?')) {
            this.value = currentStatus;
        }
    }
});
</script>
@endpush
@endsection