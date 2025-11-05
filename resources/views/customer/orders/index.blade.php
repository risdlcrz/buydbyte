@extends('layouts.storefront')

@section('title', 'My Orders')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 fw-bold mb-3">My Orders</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('storefront.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Orders</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @forelse($orders as $order)
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <h6 class="mb-1 fw-bold">Order #{{ $order->order_number }}</h6>
                        <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                    </div>
                    <div class="col-md-2">
                        <span class="badge bg-{{ 
                            $order->status === 'completed' || $order->status === 'received' || $order->status === 'delivered' ? 'success' : 
                            ($order->status === 'cancelled' ? 'danger' : 'warning') 
                        }} fs-6">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="col-md-2">
                        <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'warning') }}">
                            Payment: {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                    <div class="col-md-2 text-end">
                        <strong class="text-primary">{{ currency($order->total) }}</strong>
                    </div>
                    <div class="col-md-3 text-end">
                        <a href="{{ route('customer.orders.show', $order->order_id) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye me-1"></i>View Details
                        </a>
                        @if($order->status === 'delivered')
                            <form action="{{ route('customer.orders.receive', $order->order_id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-check-circle me-1"></i>Order Received
                                </button>
                            </form>
                        @elseif($order->status === 'received' && !$order->feedback)
                            <form action="{{ route('customer.orders.complete', $order->order_id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-star me-1"></i>Leave Feedback
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <small class="text-muted">
                            <strong>Items:</strong> {{ $order->items->count() }} item(s) | 
                            <strong>Shipping:</strong> {{ ucfirst(str_replace('_', ' ', $order->shipping_method)) }} | 
                            <strong>Tracking:</strong> {{ $order->tracking_number }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3">No orders yet</h5>
                <p class="text-muted">You haven't placed any orders yet.</p>
                <a href="{{ route('storefront.products') }}" class="btn btn-primary">
                    <i class="bi bi-bag me-2"></i>Start Shopping
                </a>
            </div>
        </div>
    @endforelse

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection

