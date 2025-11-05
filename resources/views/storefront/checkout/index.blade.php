@extends('layouts.storefront')

@section('title', 'Checkout')

@push('styles')
<style>
/* Checkout Page Styles */
.checkout-container {
    max-width: 1200px;
}

/* Address Modal Styles */
#addressModal {
    z-index: 1060;
}

.modal-backdrop {
    z-index: 1050;
}

.address-item {
    transition: all 0.3s ease;
    border: 2px solid #dee2e6;
    margin-bottom: 0.5rem;
    padding: 1rem;
    position: relative;
    cursor: pointer;
}

.address-item:hover {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.address-item.editing {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

.address-item .form-check {
    padding-right: 80px;
}

.address-actions {
    position: absolute;
    right: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    transition: opacity 0.2s ease;
}

.address-item:hover .address-actions {
    opacity: 1;
}

.address-actions .btn {
    margin-left: 0.25rem;
    padding: 0.25rem 0.5rem;
}

/* Shipping Method Styles */
.shipping-method-item {
    border: 2px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    background: white;
}

.shipping-method-item:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

.shipping-method-item.active {
    border-color: #0d6efd;
    background-color: #e7f1ff;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.shipping-method-item input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

/* Payment Method Styles */
.payment-method-container {
    border: 2px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    background: white;
}

.payment-method-container:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

.payment-method-container.active {
    border-color: #0d6efd;
    background-color: #e7f1ff;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.payment-method-container input[type="radio"] {
    margin-right: 0.5rem;
}

/* Selected Address Summary */
#selected-address-summary {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1rem;
    min-height: 60px;
}

#selected-address-text {
    white-space: pre-line;
    line-height: 1.6;
    margin: 0;
}

/* Order Items */
.order-item {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
}

.order-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.order-item-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 0.5rem;
}

/* Toast Notifications */
.toast {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 1050;
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-overlay.show {
    display: flex;
}

/* Form Validation */
.is-invalid {
    border-color: #dc3545 !important;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
</style>
@endpush

@section('content')
<div class="container py-5 checkout-container">
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
        <!-- Left Column: Shipping Info + Items -->
        <div class="col-lg-8">
            <!-- Delivery Address Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Delivery Address</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addressModal">
                        <i class="bi bi-pencil me-1"></i> Change Address
                    </button>
                </div>
                <div class="card-body">
                    <div id="selected-address-summary">
                        @if(!empty($defaultAddress))
                            <div id="selected-address-text" class="mb-0">{{ $defaultAddress->formatted_address }}</div>
                            <input type="hidden" id="selected-address-id" value="{{ $defaultAddress->address_id }}">
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-geo-alt text-muted h3 mb-2"></i>
                                <div id="selected-address-text" class="text-muted">No delivery address selected.</div>
                                <input type="hidden" id="selected-address-id" value="">
                                <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addressModal">
                                    <i class="bi bi-plus-lg me-1"></i> Add Address
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Shipping Method Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Shipping Method</h5>
                </div>
                <div class="card-body">
                    @foreach($shippingMethods as $index => $method)
                    <div class="shipping-method-item {{ $loop->first ? 'active' : '' }}" 
                         data-shipping-id="{{ $method['id'] }}" 
                         data-price="{{ $method['price'] }}"
                         role="button"
                         tabindex="0">
                        <input type="radio" 
                               class="shipping-method" 
                               name="shipping_method" 
                               id="shipping-{{ $method['id'] }}"
                               value="{{ $method['id'] }}" 
                               data-price="{{ $method['price'] }}"
                               {{ $loop->first ? 'checked' : '' }}>
                        <label for="shipping-{{ $method['id'] }}" class="w-100 d-block mb-0" style="cursor: pointer;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi {{ $method['id'] === 'overnight' ? 'bi-lightning-charge-fill' : ($method['id'] === 'express' ? 'bi-truck' : 'bi-box-seam') }} me-2 text-primary"></i>
                                        <strong class="fs-6">{{ $method['name'] }}</strong>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        Estimated delivery: {{ $method['estimated_days'] }}
                                    </small>
                                </div>
                                <div class="text-end ms-3">
                                    <strong class="text-primary fs-5">{{ currency($method['price']) }}</strong>
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Items Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Order Items</h5>
                </div>
                <div class="card-body">
                    @foreach($cartItems as $item)
                    <div class="order-item d-flex align-items-center">
                        <!-- Product Image -->
                        <div class="me-3">
                            @if($item->product->main_image)
                                <img src="{{ Storage::url($item->product->main_image) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="order-item-image">
                            @else
                                <div class="order-item-image bg-light d-flex align-items-center justify-content-center">
                                    <i class="bi bi-image text-muted fs-4"></i>
                                </div>
                            @endif
                        </div>
                        <!-- Product Details -->
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="{{ route('storefront.product', $item->product) }}" 
                                   class="text-decoration-none text-dark fw-bold">
                                    {{ $item->product->name }}
                                </a>
                            </h6>
                            <p class="text-muted small mb-1">{{ $item->product->category->name ?? 'N/A' }}</p>
                            <p class="text-muted small mb-0">SKU: {{ $item->product->sku }}</p>
                        </div>
                        <!-- Quantity and Price -->
                        <div class="text-end ms-3">
                            <div class="mb-2">
                                <small class="text-muted">Quantity: <strong>{{ $item->quantity }}</strong></small>
                            </div>
                            <strong class="text-primary fs-5">{{ currency($item->total) }}</strong>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column: Payment + Order Summary -->
        <div class="col-lg-4">
            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Payment Information</h5>
                </div>
                <div class="card-body">
                    <!-- Credit/Debit Card -->
                    <div class="payment-method-container active" data-method="card" role="button" tabindex="0">
                        <div class="d-flex align-items-start">
                            <input type="radio" 
                                   class="payment-method" 
                                   name="payment_method" 
                                   id="pm-card" 
                                   value="card" 
                                   checked>
                            <label for="pm-card" class="flex-grow-1 mb-0" style="cursor: pointer;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong class="d-block">Credit / Debit Card</strong>
                                        <small class="text-muted">Pay securely with your card</small>
                                    </div>
                                    <i class="bi bi-credit-card fs-3 text-primary"></i>
                                </div>
                                <div id="card-element-container" class="mt-3">
                                    <div id="card-element" style="padding: 0.75rem; border: 1px solid #ced4da; border-radius: 0.375rem; background: white;"></div>
                                    <div id="card-errors" class="invalid-feedback"></div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- GCash -->
                    <div class="payment-method-container" data-method="gcash" role="button" tabindex="0">
                        <div class="d-flex align-items-start">
                            <input type="radio" 
                                   class="payment-method" 
                                   name="payment_method" 
                                   id="pm-gcash" 
                                   value="gcash">
                            <label for="pm-gcash" class="flex-grow-1 mb-0" style="cursor: pointer;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="d-block">GCash</strong>
                                        <small class="text-muted">Pay with your GCash wallet</small>
                                    </div>
                                    <i class="bi bi-wallet2 fs-3 text-success"></i>
                                </div>
                            </label>
                        </div>
                        <div id="gcash-info" class="mt-2 small text-muted" style="display:none;">
                            You'll be redirected to complete your payment with GCash
                        </div>
                    </div>

                    <!-- Cash on Delivery -->
                    <div class="payment-method-container" data-method="cod" role="button" tabindex="0">
                        <div class="d-flex align-items-start">
                            <input type="radio" 
                                   class="payment-method" 
                                   name="payment_method" 
                                   id="pm-cod" 
                                   value="cod">
                            <label for="pm-cod" class="flex-grow-1 mb-0" style="cursor: pointer;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="d-block">Cash on Delivery</strong>
                                        <small class="text-muted">Pay when you receive your items</small>
                                    </div>
                                    <i class="bi bi-cash-stack fs-3 text-warning"></i>
                                </div>
                            </label>
                        </div>
                        <div id="cod-info" class="mt-2 small text-muted" style="display:none;">
                            Have the exact amount ready when your package arrives
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Order Summary</h5>
                    <span class="badge bg-primary">{{ count($cartItems) }} {{ count($cartItems) === 1 ? 'item' : 'items' }}</span>
                </div>
                <form id="place-order-form" method="POST" action="{{ route('checkout.process') }}">
                    @csrf
                    <div class="card-body">
                        <!-- Order Totals -->
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <strong id="order-subtotal">{{ currency($total) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Shipping:</span>
                            <strong id="shipping-cost">{{ currency($shippingMethods[0]['price']) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total:</span>
                            <strong class="text-primary fs-4" id="order-total">
                                {{ currency($total + $shippingMethods[0]['price']) }}
                            </strong>
                        </div>

                        <!-- Hidden Form Fields -->
                        <input type="hidden" name="selected_items" value="{{ session('checkout.selected_items') }}">
                        <input type="hidden" name="shipping_method" id="form-shipping-method" value="{{ $shippingMethods[0]['id'] }}">
                        <input type="hidden" name="address_id" id="form-address-id" value="{{ optional($defaultAddress)->address_id ?? '' }}">
                        <input type="hidden" name="payment_method" id="form-payment-method" value="card">

                        <!-- Place Order Button -->
                        <button type="submit" class="btn btn-primary w-100 btn-lg" id="place-order-btn">
                            <i class="bi bi-check-circle me-2"></i>Place Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addressModalLabel">
                    <i class="bi bi-geo-alt me-2"></i>Delivery Address
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Saved Addresses List -->
                    <div class="col-md-6 border-end">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold">Saved Addresses</h6>
                            <button type="button" class="btn btn-sm btn-primary" id="add-new-address-btn">
                                <i class="bi bi-plus-lg me-1"></i> Add New
                            </button>
                        </div>
                        <div class="address-list" style="max-height: 400px; overflow-y: auto;">
                            @if(!empty($addresses) && $addresses->isNotEmpty())
                                @foreach($addresses as $address)
                                <div class="address-item" 
                                     data-address-id="{{ $address->address_id }}" 
                                     data-fullname="{{ $address->full_name }}" 
                                     data-street="{{ $address->street }}" 
                                     data-city="{{ $address->city }}" 
                                     data-state="{{ $address->state }}" 
                                     data-postal="{{ $address->postal_code }}" 
                                     data-country="{{ $address->country }}">
                                    <div class="form-check">
                                        <input class="form-check-input address-selector" 
                                               type="radio" 
                                               name="selected_address_radio" 
                                               id="address-{{ $address->address_id }}" 
                                               {{ $address->is_default ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="address-{{ $address->address_id }}">
                                            <h6 class="mb-1 fw-bold">{{ $address->full_name }}</h6>
                                            <p class="mb-1 small text-muted">{{ $address->formatted_address }}</p>
                                            @if($address->is_default)
                                            <span class="badge bg-primary">Default</span>
                                            @endif
                                        </label>
                                    </div>
                                    <div class="address-actions">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-secondary edit-address-btn" 
                                                data-address-id="{{ $address->address_id }}" 
                                                title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger delete-address-btn" 
                                                data-address-id="{{ $address->address_id }}" 
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-geo-alt text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-3">No saved addresses yet.</p>
                                    <button type="button" class="btn btn-primary btn-sm" id="add-new-address-btn-empty">
                                        <i class="bi bi-plus-lg me-1"></i> Add Your First Address
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Address Form -->
                    <div class="col-md-6">
                        <div id="address-form-area">
                            <h6 id="address-form-title" class="fw-bold mb-3">Add / Edit Address</h6>
                            <form id="address-form" method="POST" action="{{ route('checkout.address.store') }}">
                                @csrf
                                <input type="hidden" name="_method" id="address-form-method" value="POST">
                                <input type="hidden" name="address_id" id="address-form-id" value="">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                    <input name="full_name" id="addr-full_name" type="text" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Street Address <span class="text-danger">*</span></label>
                                    <input name="street" id="addr-street" type="text" class="form-control" required>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">City <span class="text-danger">*</span></label>
                                        <input name="city" id="addr-city" type="text" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">State</label>
                                        <input name="state" id="addr-state" type="text" class="form-control">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Postal Code</label>
                                        <input name="postal_code" id="addr-postal" type="text" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Country</label>
                                        <input name="country" id="addr-country" type="text" class="form-control" value="Philippines" readonly>
                                    </div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="is_default" value="1" id="addr-is-default">
                                    <label class="form-check-label" for="addr-is-default">Set as default address</label>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill" id="address-form-save">
                                        <i class="bi bi-check-circle me-1"></i>Save Address
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="address-form-cancel">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications -->
<div class="toast" id="error-toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
    <div class="toast-header bg-danger text-white">
        <i class="bi bi-exclamation-circle me-2"></i>
        <strong class="me-auto">Error</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body" id="error-toast-body"></div>
</div>

<div class="toast" id="success-toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
    <div class="toast-header bg-success text-white">
        <i class="bi bi-check-circle me-2"></i>
        <strong class="me-auto">Success</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body" id="success-toast-body"></div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay">
    <div class="text-center">
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div id="loading-message" class="fw-bold">Processing your order...</div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
(function() {
    'use strict';

    // Configuration
    const config = {
        subtotal: {{ $total }},
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
        stripeKey: '{{ config('services.stripe.key') }}'
    };

    // Initialize Stripe
    let stripe, card;
    if (config.stripeKey) {
        stripe = Stripe(config.stripeKey);
        const elements = stripe.elements({
            fonts: [{ cssSrc: 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap' }]
        });
        
        card = elements.create('card', {
            style: {
                base: {
                    fontFamily: 'Inter, system-ui, sans-serif',
                    fontSize: '16px',
                    color: '#1a1a1a',
                    '::placeholder': { color: '#6c757d' }
                },
                invalid: {
                    color: '#dc3545',
                    iconColor: '#dc3545'
                }
            }
        });
        card.mount('#card-element');
        card.addEventListener('change', handleCardChange);
    }

    // ============================================
    // SHIPPING METHOD HANDLING
    // ============================================
    function initShippingMethods() {
        const shippingItems = document.querySelectorAll('.shipping-method-item');
        const shippingRadios = document.querySelectorAll('.shipping-method');

        shippingItems.forEach(item => {
            // Click handler
            item.addEventListener('click', function(e) {
                if (e.target.closest('.address-actions')) return;
                const radio = this.querySelector('.shipping-method');
                if (radio && !radio.checked) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            });

            // Keyboard handler
            item.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const radio = this.querySelector('.shipping-method');
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    }
                }
            });
        });

        shippingRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                updateShippingSelection(this);
                updateOrderSummary();
            });
        });
    }

    function updateShippingSelection(radio) {
        const container = radio.closest('.shipping-method-item');
        document.querySelectorAll('.shipping-method-item').forEach(item => {
            item.classList.remove('active');
        });
        if (container) {
            container.classList.add('active');
            document.getElementById('form-shipping-method').value = radio.value;
        }
    }

    // ============================================
    // PAYMENT METHOD HANDLING
    // ============================================
    function initPaymentMethods() {
        const paymentContainers = document.querySelectorAll('.payment-method-container');
        const paymentRadios = document.querySelectorAll('.payment-method');

        paymentContainers.forEach(container => {
            container.addEventListener('click', function(e) {
                if (e.target.closest('#card-element')) return;
                const radio = this.querySelector('.payment-method');
                if (radio && !radio.checked) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            });

            container.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const radio = this.querySelector('.payment-method');
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    }
                }
            });
        });

        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                updatePaymentMethodUI(this.value);
                document.getElementById('form-payment-method').value = this.value;
            });
        });

        // Set initial state
        const initialPayment = document.querySelector('.payment-method:checked');
        if (initialPayment) {
            updatePaymentMethodUI(initialPayment.value);
        }
    }

    function updatePaymentMethodUI(method) {
        document.querySelectorAll('.payment-method-container').forEach(container => {
            container.classList.remove('active');
            if (container.dataset.method === method) {
                container.classList.add('active');
            }
        });

        const cardContainer = document.getElementById('card-element-container');
        const gcashInfo = document.getElementById('gcash-info');
        const codInfo = document.getElementById('cod-info');

        if (method === 'card') {
            if (cardContainer) cardContainer.style.display = 'block';
            if (gcashInfo) gcashInfo.style.display = 'none';
            if (codInfo) codInfo.style.display = 'none';
        } else if (method === 'gcash') {
            if (cardContainer) cardContainer.style.display = 'none';
            if (gcashInfo) gcashInfo.style.display = 'block';
            if (codInfo) codInfo.style.display = 'none';
        } else if (method === 'cod') {
            if (cardContainer) cardContainer.style.display = 'none';
            if (gcashInfo) gcashInfo.style.display = 'none';
            if (codInfo) codInfo.style.display = 'block';
        }
    }

    function handleCardChange(event) {
        const errorElement = document.getElementById('card-errors');
        if (event.error) {
            errorElement.textContent = event.error.message;
            errorElement.style.display = 'block';
        } else {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }

    // ============================================
    // ORDER SUMMARY UPDATES
    // ============================================
    function updateOrderSummary() {
        const selectedShipping = document.querySelector('.shipping-method:checked');
        let shippingPrice = 0;
        
        if (selectedShipping && selectedShipping.dataset.price) {
            shippingPrice = parseFloat(selectedShipping.dataset.price);
        }

        const subtotal = config.subtotal;
        const total = subtotal + shippingPrice;

        document.getElementById('shipping-cost').textContent = formatCurrency(shippingPrice);
        document.getElementById('order-total').textContent = formatCurrency(total);
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP'
        }).format(amount);
    }

    // ============================================
    // ADDRESS MANAGEMENT
    // ============================================
    function initAddressManagement() {
        const addressList = document.querySelector('.address-list');
        if (!addressList) return;

        // Event delegation for edit/delete buttons
        addressList.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.edit-address-btn');
            const deleteBtn = e.target.closest('.delete-address-btn');
            const addressItem = e.target.closest('.address-item');

            if (editBtn) {
                e.preventDefault();
                e.stopPropagation();
                handleEditAddress(addressItem);
            } else if (deleteBtn) {
                e.preventDefault();
                e.stopPropagation();
                handleDeleteAddress(deleteBtn.dataset.addressId);
            } else if (addressItem && e.target.closest('.address-selector')) {
                selectAddress(addressItem);
            }
        });

        // Address radio selection
        document.querySelectorAll('.address-selector').forEach(radio => {
            radio.addEventListener('change', function() {
                const addressItem = this.closest('.address-item');
                if (addressItem) {
                    selectAddress(addressItem);
                }
            });
        });

        // Add new address button
        document.getElementById('add-new-address-btn')?.addEventListener('click', resetAddressForm);
        document.getElementById('add-new-address-btn-empty')?.addEventListener('click', resetAddressForm);

        // Address form submission
        const addressForm = document.getElementById('address-form');
        if (addressForm) {
            addressForm.addEventListener('submit', handleAddressFormSubmit);
        }

        // Cancel button
        document.getElementById('address-form-cancel')?.addEventListener('click', resetAddressForm);

        // Rebind when modal opens
        const addressModal = document.getElementById('addressModal');
        if (addressModal) {
            addressModal.addEventListener('shown.bs.modal', function() {
                document.querySelectorAll('.address-selector').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const addressItem = this.closest('.address-item');
                        if (addressItem) selectAddress(addressItem);
                    });
                });
            });
        }
    }

    function selectAddress(addressItem) {
        const addressId = addressItem.dataset.addressId;
        const fullname = addressItem.dataset.fullname;
        const street = addressItem.dataset.street;
        const city = addressItem.dataset.city;
        const state = addressItem.dataset.state;
        const postal = addressItem.dataset.postal;
        const country = addressItem.dataset.country;

        const addressParts = [street, city, state, postal, country].filter(Boolean);
        const formattedAddress = [fullname, addressParts.join(', ')].filter(Boolean).join('\n');

        document.getElementById('selected-address-text').textContent = formattedAddress;
        document.getElementById('selected-address-id').value = addressId;
        document.getElementById('form-address-id').value = addressId;
    }

    function handleEditAddress(addressItem) {
        document.querySelectorAll('.address-item').forEach(item => item.classList.remove('editing'));
        addressItem.classList.add('editing');

        document.getElementById('address-form-title').textContent = 'Edit Address';
        document.getElementById('address-form').action = '{{ url('/checkout/address') }}/' + addressItem.dataset.addressId;
        document.getElementById('address-form-method').value = 'PUT';
        document.getElementById('address-form-id').value = addressItem.dataset.addressId;
        document.getElementById('addr-full_name').value = addressItem.dataset.fullname || '';
        document.getElementById('addr-street').value = addressItem.dataset.street || '';
        document.getElementById('addr-city').value = addressItem.dataset.city || '';
        document.getElementById('addr-state').value = addressItem.dataset.state || '';
        document.getElementById('addr-postal').value = addressItem.dataset.postal || '';
        document.getElementById('addr-country').value = addressItem.dataset.country || 'Philippines';
    }

    async function handleDeleteAddress(addressId) {
        if (!confirm('Are you sure you want to delete this address?')) return;

        try {
            const response = await fetch('{{ url('/checkout/address') }}/' + addressId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': config.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                const data = await response.json().catch(() => ({}));
                throw new Error(data.message || 'Failed to delete address');
            }

            const item = document.querySelector(`[data-address-id="${addressId}"]`);
            if (item) {
                item.remove();
                
                // Check if selected address was deleted
                if (document.getElementById('selected-address-id').value === addressId) {
                    document.getElementById('selected-address-id').value = '';
                    document.getElementById('form-address-id').value = '';
                    document.getElementById('selected-address-text').textContent = 'No delivery address selected.';
                }

                // Check if list is empty
                const addressList = document.querySelector('.address-list');
                if (addressList && addressList.querySelectorAll('.address-item').length === 0) {
                    addressList.innerHTML = `
                        <div class="text-center py-5">
                            <i class="bi bi-geo-alt text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No saved addresses yet.</p>
                        </div>
                    `;
                }

                showToast('success', 'Address deleted successfully');
            }
        } catch (error) {
            console.error('Delete address error:', error);
            showToast('error', error.message || 'Failed to delete address');
        }
    }

    function resetAddressForm() {
        document.getElementById('address-form-title').textContent = 'Add / Edit Address';
        document.getElementById('address-form').action = '{{ route('checkout.address.store') }}';
        document.getElementById('address-form-method').value = 'POST';
        document.getElementById('address-form-id').value = '';
        document.getElementById('address-form').reset();
        document.getElementById('addr-country').value = 'Philippines';
        document.querySelectorAll('.address-item').forEach(item => item.classList.remove('editing'));
    }

    async function handleAddressFormSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

            const formData = new FormData(form);
            const method = document.getElementById('address-form-method').value.toUpperCase();

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': config.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!response.ok) {
                const data = await response.json();
                throw new Error(data.message || 'Failed to save address');
            }

            const data = await response.json();
            const address = data.address;
            const addressList = document.querySelector('.address-list');

            // Remove empty state
            const emptyState = addressList.querySelector('.text-center');
            if (emptyState) emptyState.remove();

            // Build address HTML
            const addressParts = [address.street, address.city, address.state, address.postal_code, address.country].filter(Boolean);
            const formattedAddress = addressParts.join(', ');
            const isDefaultBadge = address.is_default ? '<span class="badge bg-primary">Default</span>' : '';

            const addressHtml = `
                <div class="address-item" 
                     data-address-id="${address.address_id}" 
                     data-fullname="${address.full_name}" 
                     data-street="${address.street}" 
                     data-city="${address.city}" 
                     data-state="${address.state || ''}" 
                     data-postal="${address.postal_code || ''}" 
                     data-country="${address.country}">
                    <div class="form-check">
                        <input class="form-check-input address-selector" 
                               type="radio" 
                               name="selected_address_radio" 
                               id="address-${address.address_id}" 
                               ${address.is_default ? 'checked' : ''}>
                        <label class="form-check-label w-100" for="address-${address.address_id}">
                            <h6 class="mb-1 fw-bold">${address.full_name}</h6>
                            <p class="mb-1 small text-muted">${formattedAddress}</p>
                            ${isDefaultBadge}
                        </label>
                    </div>
                    <div class="address-actions">
                        <button type="button" class="btn btn-sm btn-outline-secondary edit-address-btn" 
                                data-address-id="${address.address_id}" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-address-btn" 
                                data-address-id="${address.address_id}" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;

            // Update or add address
            if (method === 'PUT') {
                const existing = addressList.querySelector(`[data-address-id="${address.address_id}"]`);
                if (existing) {
                    const temp = document.createElement('div');
                    temp.innerHTML = addressHtml;
                    existing.replaceWith(temp.firstElementChild);
                }
            } else {
                addressList.insertAdjacentHTML('beforeend', addressHtml);
            }

            // Update selected address if default or first
            if (address.is_default || !document.getElementById('selected-address-id').value) {
                selectAddress(addressList.querySelector(`[data-address-id="${address.address_id}"]`));
            }

            // Reset form
            resetAddressForm();
            
            // Rebind address selectors
            document.querySelectorAll('.address-selector').forEach(radio => {
                radio.addEventListener('change', function() {
                    const addressItem = this.closest('.address-item');
                    if (addressItem) selectAddress(addressItem);
                });
            });

            showToast('success', 'Address saved successfully');
        } catch (error) {
            console.error('Address save error:', error);
            showToast('error', error.message || 'Failed to save address');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    // ============================================
    // ORDER SUBMISSION
    // ============================================
    function initOrderSubmission() {
        const form = document.getElementById('place-order-form');
        if (!form) return;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validate
            const addressId = document.getElementById('form-address-id').value;
            const shippingMethod = document.getElementById('form-shipping-method').value;
            const paymentMethod = document.getElementById('form-payment-method').value;

            const errors = [];
            if (!addressId) errors.push('Please select a delivery address');
            if (!shippingMethod) errors.push('Please select a shipping method');
            if (!paymentMethod) errors.push('Please select a payment method');

            if (errors.length > 0) {
                showToast('error', errors.join('\n'));
                return;
            }

            // Show loading
            document.getElementById('loading-overlay').classList.add('show');
            const submitBtn = document.getElementById('place-order-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

            // Prepare form data
            const formData = new FormData(form);
            
            // Add payment method info if card was used
            if (paymentMethod === 'card' && card) {
                try {
                    const { error, paymentMethod: pm } = await stripe.createPaymentMethod({
                        type: 'card',
                        card: card
                    });
                    if (error) {
                        throw new Error('Card error: ' + error.message);
                    }
                    formData.append('payment_method_id', pm.id);
                } catch (error) {
                    console.error('Payment method creation error:', error);
                    showToast('error', error.message || 'Payment processing failed');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Place Order';
                    document.getElementById('loading-overlay').classList.remove('show');
                    return;
                }
            }

            // Submit form via AJAX
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': config.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                // Check if response is OK
                if (!response.ok) {
                    // Try to parse error message
                    let errorMessage = 'Order processing failed';
                    let errorDetails = null;
                    try {
                        const errorData = await response.json();
                        errorMessage = errorData.error || errorData.message || errorMessage;
                        errorDetails = errorData.message || errorData.details;
                    } catch (e) {
                        // If not JSON, use status text
                        errorMessage = response.statusText || errorMessage;
                    }
                    
                    // Show detailed error in console for debugging
                    console.error('Order processing failed:', {
                        status: response.status,
                        statusText: response.statusText,
                        error: errorMessage,
                        details: errorDetails
                    });
                    
                    throw new Error(errorMessage);
                }

                // Parse JSON response
                const data = await response.json();

                // Success - redirect to order page
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else if (data.order_id) {
                    window.location.href = `/customer/orders/${data.order_id}`;
                } else {
                    window.location.href = '/customer/orders';
                }
            } catch (error) {
                console.error('Order submission error:', error);
                showToast('error', error.message || 'An error occurred while processing your order. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Place Order';
                document.getElementById('loading-overlay').classList.remove('show');
            }
        });
    }

    // ============================================
    // UTILITY FUNCTIONS
    // ============================================
    function showToast(type, message) {
        const toastId = type === 'error' ? 'error-toast' : 'success-toast';
        const toastBody = document.getElementById(toastId + '-body');
        if (toastBody) {
            toastBody.textContent = message;
            const toast = new bootstrap.Toast(document.getElementById(toastId));
            toast.show();
        }
    }

    // ============================================
    // INITIALIZATION
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        initShippingMethods();
        initPaymentMethods();
        initAddressManagement();
        initOrderSubmission();
        updateOrderSummary();
    });
})();
</script>
@endpush