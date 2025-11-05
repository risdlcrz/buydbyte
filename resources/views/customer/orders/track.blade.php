@extends('layouts.storefront')

@section('title', 'Track Order')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 fw-bold mb-3">Track Order</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('storefront.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customer.orders.index') }}">My Orders</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customer.orders.show', $order->order_id) }}">Order #{{ $order->order_number }}</a></li>
                    <li class="breadcrumb-item active">Track</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Order Tracking</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="fw-bold">Order Number:</h6>
                        <p class="mb-0">{{ $order->order_number }}</p>
                    </div>
                    <div class="mb-4">
                        <h6 class="fw-bold">Tracking Number:</h6>
                        <p class="mb-0">{{ $order->tracking_number }}</p>
                    </div>
                    <div class="mb-4">
                        <h6 class="fw-bold">Current Status:</h6>
                        <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }} fs-6">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3">Tracking History</h6>
                    <div class="timeline">
                        @forelse($order->tracking->sortByDesc('created_at') as $track)
                            <div class="timeline-item d-flex mb-4">
                                <div class="timeline-marker me-3">
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-check text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ ucfirst($track->status) }}</h6>
                                    <p class="mb-1">{{ $track->description }}</p>
                                    @if($track->location)
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $track->location }}
                                        </small>
                                        <br>
                                    @endif
                                    <small class="text-muted">{{ $track->created_at->format('M d, Y h:i A') }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3">No tracking information available yet.</p>
                            </div>
                        @endforelse
                    </div>
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
    height: calc(100% + 16px);
    background-color: #dee2e6;
}

.timeline-marker {
    position: relative;
    z-index: 1;
}
</style>
@endpush
@endsection

