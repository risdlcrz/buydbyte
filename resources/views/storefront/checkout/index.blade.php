@extends('layouts.storefront')

@section('title', 'Checkout')

@section('content')
<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 fw-bold mb-3">Checkout</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('storefront.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Cart</a></li>
                    <li class="breadcrumb-item active">Checkout</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Checkout Form -->
        <div class="col-lg-8">
            <!-- Shipping Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <!-- Shipping Methods -->
                    <div class="mb-4">
                        <h6>Select Shipping Method</h6>
                        @foreach($shippingMethods as $method)
                        <div class="form-check mb-3">
                            <input class="form-check-input shipping-method" type="radio" 
                                   name="shipping_method" id="shipping-{{ $method['id'] }}"
                                   value="{{ $method['id'] }}" data-price="{{ $method['price'] }}"
                                   {{ $loop->first ? 'checked' : '' }}>
                            <label class="form-check-label" for="shipping-{{ $method['id'] }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $method['name'] }}</strong>
                                        <div class="text-muted small">Estimated delivery: {{ $method['estimated_days'] }}</div>
                                    </div>
                                    <strong>{{ currency($method['price']) }}</strong>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <!-- Delivery Address -->
                    <h6>Delivery Address</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->first_name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->last_name }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Street Address</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">ZIP Code</label>
                            <input type="text" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Payment Information</h5>
                </div>
                <div class="card-body">
                    <!-- Stripe Elements Placeholder -->
                    <div id="card-element" class="form-control" style="height: 2.4em; padding-top: .5em;"></div>
                    <div id="card-errors" class="invalid-feedback d-block"></div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <!-- Selected Items -->
                    @foreach($cartItems as $item)
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h6 class="mb-0">{{ $item->product->name }}</h6>
                            <small class="text-muted">Qty: {{ $item->quantity }}</small>
                        </div>
                        <strong>{{ currency($item->total) }}</strong>
                    </div>
                    @endforeach

                    <hr>

                    <!-- Totals -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>{{ currency($total) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <strong id="shipping-cost">{{ currency($shippingMethods[0]['price']) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span>Total:</span>
                        <strong class="text-primary fs-5" id="order-total">
                            {{ currency($total + $shippingMethods[0]['price']) }}
                        </strong>
                    </div>

                    <!-- Place Order Button -->
                    <button type="submit" class="btn btn-primary w-100" id="place-order-btn">
                        Place Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Stripe Elements
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount('#card-element');

    // Handle shipping method changes
    const shippingMethods = document.querySelectorAll('.shipping-method');
    shippingMethods.forEach(method => {
        method.addEventListener('change', updateTotal);
    });

    function updateTotal() {
        const selectedMethod = document.querySelector('.shipping-method:checked');
        const shippingPrice = parseFloat(selectedMethod.dataset.price);
        const subtotal = {{ $total }};
        
        // Update shipping cost and total
        document.getElementById('shipping-cost').textContent = formatCurrency(shippingPrice);
        document.getElementById('order-total').textContent = formatCurrency(subtotal + shippingPrice);
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }

    // Handle order submission
    document.getElementById('place-order-btn').addEventListener('click', async function(e) {
        e.preventDefault();
        // Add your payment processing logic here
        alert('Payment processing would be integrated here');
    });
});
</script>
@endpush