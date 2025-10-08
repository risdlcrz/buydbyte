@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@push('styles')
<style>
    .detail-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }
    
    .detail-label {
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }
    
    .detail-value {
        color: #1e293b;
        margin-bottom: 1rem;
    }
    
    .user-avatar {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.5rem;
        color: white;
    }
    
    .address-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .cart-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
    
    .activity-item {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('content')
<div class="row">
    <!-- User Information -->
    <div class="col-lg-8">
        <!-- Profile Information -->
        <div class="detail-card">
            <div class="d-flex align-items-start justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <div class="user-avatar bg-primary me-3">
                        {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="mb-1">{{ $user->name }}</h3>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-{{ $user->role === 'admin' ? 'primary' : 'secondary' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                            <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                            @if($user->email_verified_at)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Verified
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Unverified
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="detail-label">Email Address</div>
                    <div class="detail-value">{{ $user->email }}</div>
                </div>
                <div class="col-md-6">
                    <div class="detail-label">Phone Number</div>
                    <div class="detail-value">{{ $user->phone_number ?? 'Not provided' }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="detail-label">First Name</div>
                    <div class="detail-value">{{ $user->first_name }}</div>
                </div>
                <div class="col-md-6">
                    <div class="detail-label">Last Name</div>
                    <div class="detail-value">{{ $user->last_name ?? 'Not provided' }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="detail-label">Account Created</div>
                    <div class="detail-value">
                        {{ $user->created_at->format('M d, Y') }}
                        <small class="text-muted d-block">{{ $user->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-label">Last Updated</div>
                    <div class="detail-value">
                        {{ $user->updated_at->format('M d, Y') }}
                        <small class="text-muted d-block">{{ $user->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>

            @if($user->email_verified_at)
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-label">Email Verified</div>
                        <div class="detail-value">
                            {{ $user->email_verified_at->format('M d, Y') }}
                            <small class="text-muted d-block">{{ $user->email_verified_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Addresses -->
        <div class="detail-card">
            <h5 class="mb-3">
                <i class="bi bi-geo-alt me-2"></i>Addresses
            </h5>
            
            @if($user->addresses && $user->addresses->count() > 0)
                @foreach($user->addresses as $address)
                    <div class="address-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">
                                    {{ $address->type ?? 'Address' }}
                                    @if($address->is_default)
                                        <span class="badge bg-primary ms-1">Default</span>
                                    @endif
                                </h6>
                                <div class="text-muted">
                                    {{ $address->street_address }}<br>
                                    @if($address->apartment)
                                        {{ $address->apartment }}<br>
                                    @endif
                                    {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                    {{ $address->country }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="bi bi-geo-alt text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">No addresses on file</p>
                </div>
            @endif
        </div>

        <!-- Cart Items -->
        <div class="detail-card">
            <h5 class="mb-3">
                <i class="bi bi-cart me-2"></i>Current Cart
                @if($user->cartItems && $user->cartItems->count() > 0)
                    <span class="badge bg-info ms-2">{{ $user->cartItems->count() }} items</span>
                @endif
            </h5>
            
            @if($user->cartItems && $user->cartItems->count() > 0)
                @foreach($user->cartItems as $cartItem)
                    <div class="cart-item">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $cartItem->product->name ?? 'Product not found' }}</h6>
                            <div class="text-muted small">
                                Quantity: {{ $cartItem->quantity }} × {{ currency($cartItem->price) }}
                                = <strong>{{ currency($cartItem->quantity * $cartItem->price) }}</strong>
                            </div>
                            <small class="text-muted">Added {{ $cartItem->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @endforeach
                
                <div class="mt-3 p-3 bg-light rounded">
                    <div class="d-flex justify-content-between">
                        <strong>Total Cart Value:</strong>
                        <strong>{{ currency($user->cartItems->sum(function($item) { return $item->quantity * $item->price; })) }}</strong>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-cart text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Cart is empty</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="detail-card">
            <h5 class="mb-3">
                <i class="bi bi-lightning me-2"></i>Quick Actions
            </h5>
            
            <div class="d-grid gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit User
                </a>
                
                @if(!$user->email_verified_at)
                    <button class="btn btn-outline-success" onclick="verifyEmail()">
                        <i class="bi bi-check-circle me-2"></i>Mark as Verified
                    </button>
                @endif
                
                <button class="btn btn-outline-warning" onclick="toggleStatus()">
                    <i class="bi bi-power me-2"></i>
                    {{ $user->status === 'active' ? 'Deactivate User' : 'Activate User' }}
                </button>
                
                @if($user->role !== 'admin')
                    <button class="btn btn-outline-info" onclick="toggleRole()">
                        <i class="bi bi-shield me-2"></i>
                        {{ $user->role === 'admin' ? 'Make Customer' : 'Make Admin' }}
                    </button>
                @endif
                
                <hr>
                
                @if($user->user_id !== auth()->id())
                    <button class="btn btn-outline-danger" onclick="deleteUser()">
                        <i class="bi bi-trash me-2"></i>Delete User
                    </button>
                @else
                    <div class="alert alert-info small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        You cannot delete your own account.
                    </div>
                @endif
            </div>
        </div>

        <!-- Account Statistics -->
        <div class="detail-card">
            <h5 class="mb-3">
                <i class="bi bi-graph-up me-2"></i>Account Statistics
            </h5>
            
            <div class="mb-3">
                <div class="detail-label">Account Age</div>
                <div class="detail-value">{{ $user->created_at->diffForHumans() }}</div>
            </div>
            
            <div class="mb-3">
                <div class="detail-label">Addresses</div>
                <div class="detail-value">{{ $user->addresses ? $user->addresses->count() : 0 }} saved</div>
            </div>
            
            <div class="mb-3">
                <div class="detail-label">Cart Items</div>
                <div class="detail-value">{{ $user->cartItems ? $user->cartItems->count() : 0 }} items</div>
            </div>
            
            <!-- Future: Add orders, reviews, etc. -->
            <div class="mb-3">
                <div class="detail-label">Total Orders</div>
                <div class="detail-value">
                    <span class="badge bg-info">Coming Soon</span>
                </div>
            </div>
        </div>

        <!-- Account Security -->
        <div class="detail-card">
            <h5 class="mb-3">
                <i class="bi bi-shield-check me-2"></i>Security
            </h5>
            
            <div class="mb-3">
                <div class="detail-label">Email Verification</div>
                <div class="detail-value">
                    @if($user->email_verified_at)
                        <span class="text-success">
                            <i class="bi bi-check-circle me-1"></i>Verified
                        </span>
                    @else
                        <span class="text-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>Unverified
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="mb-3">
                <div class="detail-label">Account Status</div>
                <div class="detail-value">
                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'warning' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="detail-label">Password</div>
                <div class="detail-value">
                    <button class="btn btn-sm btn-outline-secondary" onclick="resetPassword()">
                        <i class="bi bi-key me-1"></i>Reset Password
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $user->name }}</strong>?</p>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                <p>This will also delete:</p>
                <ul>
                    <li>All user addresses</li>
                    <li>Cart items</li>
                    <li>User sessions</li>
                    <li>Any associated data</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleStatus() {
    if (confirm('Toggle user status?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.users.update", $user) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        
        const statusField = document.createElement('input');
        statusField.type = 'hidden';
        statusField.name = 'status';
        statusField.value = '{{ $user->status === "active" ? "inactive" : "active" }}';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(statusField);
        document.body.appendChild(form);
        form.submit();
    }
}

function toggleRole() {
    if (confirm('Change user role?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.users.update", $user) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        
        const roleField = document.createElement('input');
        roleField.type = 'hidden';
        roleField.name = 'role';
        roleField.value = '{{ $user->role === "admin" ? "customer" : "admin" }}';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(roleField);
        document.body.appendChild(form);
        form.submit();
    }
}

function verifyEmail() {
    if (confirm('Mark this user as email verified?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.users.update", $user) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        
        const verifiedField = document.createElement('input');
        verifiedField.type = 'hidden';
        verifiedField.name = 'email_verified_at';
        verifiedField.value = new Date().toISOString();
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(verifiedField);
        document.body.appendChild(form);
        form.submit();
    }
}

function resetPassword() {
    // In a real application, this would trigger a password reset email
    if (confirm('Send password reset email to this user?')) {
        alert('Password reset functionality would be implemented here.\n\nThis would typically:\n1. Generate a reset token\n2. Send email to user\n3. Log the action in audit logs');
    }
}

function deleteUser() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush