@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Admin Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@push('styles')
<style>
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.2);
    }
    
    .recent-activity-item {
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        transition: background-color 0.2s ease;
    }
    
    .recent-activity-item:hover {
        background-color: #f8fafc;
    }
    
    .recent-activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-avatar {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<!-- Welcome Message -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="mb-1">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                        <p class="text-muted mb-0">Here's what's happening with your store today.</p>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">{{ now()->format('l, F j, Y') }}</div>
                        <div class="text-muted small">{{ now()->format('g:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card users h-100" onclick="window.location.href='{{ route('admin.users.index') }}'">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="h3 mb-1">{{ $stats['total_users'] }}</div>
                        <div class="fw-medium">Total Users</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 d-flex align-items-center">
                    <small class="text-white-50">
                        <i class="bi bi-person me-1"></i>{{ $stats['total_customers'] }} customers
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card products h-100" onclick="window.location.href='{{ route('admin.products.index') }}'">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="h3 mb-1">{{ $stats['total_products'] }}</div>
                        <div class="fw-medium">Total Products</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-box-seam fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 d-flex align-items-center">
                    <small class="text-white-50">
                        <i class="bi bi-check-circle me-1"></i>{{ $stats['active_products'] }} active
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card categories h-100" onclick="window.location.href='{{ route('admin.categories.index') }}'">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="h3 mb-1">{{ $stats['total_categories'] }}</div>
                        <div class="fw-medium">Categories</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-tags fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 d-flex align-items-center">
                    <small class="text-white-50">
                        <i class="bi bi-grid me-1"></i>Product organization
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="h3 mb-1">{{ $stats['low_stock_products'] }}</div>
                        <div class="fw-medium">Low Stock</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-exclamation-triangle fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 d-flex align-items-center">
                    <small class="text-white-50">
                        <i class="bi bi-arrow-down me-1"></i>≤ 10 items remaining
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Metrics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card p-3">
            <h6 class="mb-1">Total Feedback</h6>
            <h3 class="mb-0">{{ $totalFeedback ?? 0 }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <h6 class="mb-1">Average Rating</h6>
            <h3 class="mb-0">{{ $averageRating ?? '0.00' }} / 5</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <h6 class="mb-1">Pending Feedback</h6>
            <h3 class="mb-0">{{ $pendingFeedback ?? 0 }}</h3>
        </div>
    </div>
</div>

<!-- Activity Overview -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>Today's Activity
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 text-primary mb-1">{{ $stats['new_users_today'] ?? 0 }}</div>
                            <small class="text-muted">New Users</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 text-success mb-1">{{ $stats['new_products_today'] ?? 0 }}</div>
                            <small class="text-muted">Products Added</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up me-2"></i>Quick Stats
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 text-info mb-1">{{ $stats['active_users'] ?? 0 }}</div>
                            <small class="text-muted">Active Users</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 text-warning mb-1">{{ $stats['draft_products'] ?? 0 }}</div>
                            <small class="text-muted">Draft Products</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Management Overview -->
<div class="row">
    <!-- Recent Users -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-people me-2"></i>Recent Users
                </h5>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recent_users->count() > 0)
                    @foreach($recent_users as $user)
                    <div class="recent-activity-item">
                        <div class="d-flex align-items-center">
                            <div class="activity-avatar bg-primary text-white me-3">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $user->name }}</h6>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'primary' : 'secondary' }} mb-1">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-3 text-muted">No users yet</h6>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus me-1"></i>Add First User
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Products -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-box-seam me-2"></i>Recent Products
                </h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-primary">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recent_products->count() > 0)
                    @foreach($recent_products as $product)
                    <div class="recent-activity-item">
                        <div class="d-flex align-items-center">
                            <div class="activity-avatar bg-success text-white me-3">
                                <i class="bi bi-box"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ Str::limit($product->name, 25) }}</h6>
                                        <small class="text-muted">{{ $product->category->name ?? 'Uncategorized' }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success">{{ currency($product->current_price) }}</div>
                                        <small class="text-muted">
                                            <span class="badge bg-{{ $product->stock_quantity <= 10 ? 'danger' : 'success' }} bg-opacity-10 text-{{ $product->stock_quantity <= 10 ? 'danger' : 'success' }}">
                                                {{ $product->stock_quantity }} in stock
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-3 text-muted">No products yet</h6>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus me-1"></i>Add First Product
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- System Status -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-speedometer2 me-2"></i>System Overview
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-3 text-center">
                        <div class="border rounded p-3">
                            <i class="bi bi-database text-primary mb-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-1">Database</h6>
                            <small class="text-success">
                                <i class="bi bi-check-circle me-1"></i>Connected
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-3 text-center">
                        <div class="border rounded p-3">
                            <i class="bi bi-server text-info mb-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-1">Server</h6>
                            <small class="text-success">
                                <i class="bi bi-check-circle me-1"></i>Running
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-3 text-center">
                        <div class="border rounded p-3">
                            <i class="bi bi-shield-check text-warning mb-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-1">Security</h6>
                            <small class="text-success">
                                <i class="bi bi-check-circle me-1"></i>Secure
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-3 text-center">
                        <div class="border rounded p-3">
                            <i class="bi bi-graph-up text-success mb-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-1">Performance</h6>
                            <small class="text-success">
                                <i class="bi bi-check-circle me-1"></i>Optimal
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add cursor pointer to clickable stats cards
    document.querySelectorAll('.stats-card').forEach(function(card) {
        card.style.cursor = 'pointer';
    });
    
    // Add loading state to stats cards when clicked
    document.querySelectorAll('.stats-card').forEach(function(card) {
        card.addEventListener('click', function() {
            if (this.onclick) {
                const icon = this.querySelector('.stat-icon i');
                if (icon) {
                    const originalClass = icon.className;
                    icon.className = 'bi bi-arrow-clockwise fs-3';
                    icon.style.animation = 'spin 0.5s linear infinite';
                    
                    setTimeout(function() {
                        icon.className = originalClass;
                        icon.style.animation = '';
                    }, 500);
                }
            }
        });
    });
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endpush