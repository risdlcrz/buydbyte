@extends('layouts.storefront')

@section('title', 'Order Details')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 fw-bold mb-3">Order Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('storefront.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customer.orders.index') }}">My Orders</a></li>
                    <li class="breadcrumb-item active">Order #{{ $order->order_number }}</li>
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

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Order Items</h5>
                </div>
                <div class="card-body">
                    @foreach($order->items as $item)
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="me-3">
                                @if($item->product && $item->product->main_image)
                                    <img src="{{ Storage::url($item->product->main_image) }}" 
                                         alt="{{ $item->product_name }}" 
                                         class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $item->product_name }}</h6>
                                <small class="text-muted">Quantity: {{ $item->quantity }}</small>
                            </div>
                            <div class="text-end">
                                <strong>{{ currency($item->price * $item->quantity) }}</strong>
                                <br>
                                <small class="text-muted">{{ currency($item->price) }} each</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Shipping Status -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Shipping Status</h5>
                    <a href="{{ route('customer.orders.track', $order->order_id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-truck me-1"></i>Track Order
                    </a>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @php
                            $statuses = [
                                'pending' => 'Pending', 
                                'processing' => 'Processing', 
                                'shipped' => 'Shipped', 
                                'delivered' => 'Delivered',
                                'received' => 'Received',
                                'completed' => 'Completed'
                            ];
                            $currentStatusIndex = array_search($order->status, array_keys($statuses));
                            if ($currentStatusIndex === false) {
                                $currentStatusIndex = 0;
                            }
                        @endphp

                        @foreach($statuses as $status => $label)
                            @php
                                $statusIndex = array_search($status, array_keys($statuses));
                                $isActive = $statusIndex <= $currentStatusIndex;
                                $isCurrent = $statusIndex === $currentStatusIndex;
                            @endphp
                            <div class="timeline-item d-flex mb-3">
                                <div class="timeline-marker me-3">
                                    <div class="rounded-circle bg-{{ $isActive ? 'success' : 'secondary' }} d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        @if($isActive)
                                            <i class="bi bi-check text-white"></i>
                                        @else
                                            <i class="bi bi-circle text-white"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 {{ $isCurrent ? 'fw-bold text-primary' : '' }}">{{ $label }}</h6>
                                    @if($order->tracking->where('status', $status)->first())
                                        <small class="text-muted">
                                            {{ $order->tracking->where('status', $status)->first()->description }}
                                            @if($order->tracking->where('status', $status)->first()->location)
                                                <br><i class="bi bi-geo-alt me-1"></i>{{ $order->tracking->where('status', $status)->first()->location }}
                                            @endif
                                            <br><small>{{ $order->tracking->where('status', $status)->first()->created_at->format('M d, Y h:i A') }}</small>
                                        </small>
                                    @else
                                        <small class="text-muted">Awaiting update...</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Order Number:</strong><br>
                        <span class="text-muted">{{ $order->order_number }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Order Date:</strong><br>
                        <span class="text-muted">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        <span class="badge bg-{{ 
                            $order->status === 'completed' || $order->status === 'received' || $order->status === 'delivered' ? 'success' : 
                            ($order->status === 'cancelled' ? 'danger' : 'warning') 
                        }} fs-6">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Payment Status:</strong><br>
                        <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'warning') }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Payment Method:</strong><br>
                        <span class="text-muted">{{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Shipping Method:</strong><br>
                        <span class="text-muted">{{ ucfirst(str_replace('_', ' ', $order->shipping_method)) }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Tracking Number:</strong><br>
                        <span class="text-muted">{{ $order->tracking_number }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>{{ currency($order->subtotal) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <strong>{{ currency($order->shipping_cost) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total:</span>
                        <strong class="text-primary fs-5">{{ currency($order->total) }}</strong>
                    </div>

                    @if($order->status === 'delivered')
                        <form action="{{ route('customer.orders.receive', $order->order_id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-check-circle me-2"></i>Confirm Order Received
                            </button>
                        </form>
                        <small class="text-muted d-block text-center">Confirm receipt to proceed with feedback</small>
                    @elseif($order->status === 'received' && !$order->feedback)
                        <form action="{{ route('customer.orders.complete', $order->order_id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-star me-2"></i>Leave Feedback & Complete Order
                            </button>
                        </form>
                    @elseif($order->status === 'completed' || $order->feedback)
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>Order completed! Thank you for your feedback!
                        </div>
                    @endif
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Shipping Address</h5>
                </div>
                <div class="card-body">
                    @if(is_array($order->shipping_address))
                        <p class="mb-0">
                            <strong>{{ $order->shipping_address['full_name'] ?? '' }}</strong><br>
                            {{ $order->shipping_address['street'] ?? '' }}<br>
                            {{ $order->shipping_address['city'] ?? '' }}, 
                            {{ $order->shipping_address['state'] ?? '' }} 
                            {{ $order->shipping_address['postal_code'] ?? '' }}<br>
                            {{ $order->shipping_address['country'] ?? '' }}
                        </p>
                    @else
                        <p class="text-muted">Address not available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline-item {
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 20px;
    top: 50px;
    width: 2px;
    height: calc(100% + 8px);
    background-color: #dee2e6;
}

.timeline-item.active::after {
    background-color: #28a745;
}

.timeline-marker {
    position: relative;
    z-index: 1;
}
</style>
@endpush
@endsection

