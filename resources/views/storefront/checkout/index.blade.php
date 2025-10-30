@extends('layouts.storefront')

@section('title', 'Checkout')

@push('styles')
<style>
/* Modal styles */
#addressModal {
    z-index: 1060;
}
.modal-backdrop {
    z-index: 1050;
}
.modal.show {
    display: block !important;
}
.modal-dialog {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
.modal-header, .modal-footer {
    background-color: #f8f9fa;
}
.address-item.editing {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

/* Error toast styles */
#error-toast {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 1050;
}

/* Form validation styles */
.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.invalid-feedback {
    display: none;
    color: #dc3545;
    font-size: 0.875em;
    margin-top: 0.25rem;
}

/* Loading spinner styles */
.form-loading {
    position: relative;
    pointer-events: none;
}

.form-loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

/* Enhanced address list styles */
.address-list .address-item {
    transition: all 0.3s ease;
    border: 2px solid #dee2e6;
    margin-bottom: 0.5rem;
    padding: 1rem;
    position: relative;
    overflow: hidden;
}

.address-list .address-item:hover {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.address-list .address-item .form-check {
    padding-right: 80px; /* Space for action buttons */
}

.address-actions {
    opacity: 0;
    transition: opacity 0.2s ease;
    pointer-events: none; /* prevent clicks when hidden */
}

.address-item:hover .address-actions {
    opacity: 1;
    pointer-events: auto;
}

.address-actions .btn {
    margin-left: 0.25rem;
}

/* Payment method styles */
.payment-method-container {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.payment-method-container.active {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

/* Enhanced shipping method styles */
.shipping-method-item {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 0.5rem;
    transition: all 0.2s ease;
}

.shipping-method-item:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

.shipping-method-item.active {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

/* Loading spinner */
.spinner-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-indicator {
    display: none;
}

.loading .loading-indicator {
    display: inline-block;
}

/* Selected address summary */
#selected-address-summary {
    background-color: #f8f9fa;
    transition: all 0.2s ease;
}

#selected-address-summary:hover {
    background-color: #e9ecef;
}
</style>
@endpush

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
            <!-- Delivery Address -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Delivery Address</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addressModal">
                        <i class="bi bi-pencil me-1"></i> Change Address
                    </button>
                </div>
                <div class="card-body">
                    <div id="selected-address-summary" class="p-3 border rounded bg-light">
                        @if(!empty($defaultAddress))
                            <div id="selected-address-text" class="mb-0">{{ $defaultAddress->formatted_address }}</div>
                            <input type="hidden" id="selected-address-id" name="selected_address_id" value="{{ $defaultAddress->address_id }}">
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-geo-alt text-muted h3 mb-2"></i>
                                <div id="selected-address-text" class="text-muted">No delivery address selected.</div>
                                <input type="hidden" id="selected-address-id" name="selected_address_id" value="">
                                <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addressModal">
                                    <i class="bi bi-plus-lg me-1"></i> Add Address
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Hidden delivery fields (populated when address selected) -->
                    <div id="hidden-delivery-fields" style="display:none;">
                        <input id="delivery-full-name" name="full_name" type="hidden" value="">
                        <input id="delivery-street" name="street" type="hidden" value="">
                        <input id="delivery-city" name="city" type="hidden" value="">
                        <input id="delivery-state" name="state" type="hidden" value="">
                        <input id="delivery-postal" name="postal_code" type="hidden" value="">
                    </div>
                </div>
            </div>

            <!-- Shipping Method -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Method</h5>
                </div>
                <div class="card-body">
                    @foreach($shippingMethods as $method)
                    <div class="shipping-method-item {{ $loop->first ? 'active' : '' }} mb-3" data-shipping-id="{{ $method['id'] }}" data-price="{{ $method['price'] }}">
                        <input class="form-check-input shipping-method" type="radio" 
                               name="shipping_method" id="shipping-{{ $method['id'] }}"
                               value="{{ $method['id'] }}" data-price="{{ $method['price'] }}"
                               {{ $loop->first ? 'checked' : '' }} style="display: none;">
                        <label class="form-check-label w-100" for="shipping-{{ $method['id'] }}" style="cursor: pointer;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="bi {{ $method['id'] === 'overnight' ? 'bi-lightning-charge' : ($method['id'] === 'express' ? 'bi-truck' : 'bi-box') }} me-2"></i>
                                        <strong>{{ $method['name'] }}</strong>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        Estimated delivery: {{ $method['estimated_days'] }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <strong class="text-primary h5 mb-0">{{ currency($method['price']) }}</strong>
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

                    <!-- Address Modal -->
                    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header border-bottom">
                                    <h5 class="modal-title" id="addressModalLabel">
                                        <i class="bi bi-geo-alt me-2"></i>Delivery Address
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Saved Addresses</h6>
                                                <button type="button" class="btn btn-sm btn-primary" id="add-new-address-btn">
                                                    <i class="bi bi-plus"></i> Add New
                                                </button>
                                            </div>
                                            <div class="list-group address-list">
                                                @if(!empty($addresses) && $addresses->isNotEmpty())
                                                    @foreach($addresses as $address)
                                                    <div class="list-group-item address-item position-relative" data-address-id="{{ $address->address_id }}" data-fullname="{{ $address->full_name }}" data-street="{{ $address->street }}" data-city="{{ $address->city }}" data-state="{{ $address->state }}" data-postal="{{ $address->postal_code }}" data-country="{{ $address->country }}">
                                                        <div class="form-check">
                                                            <input class="form-check-input address-selector" type="radio" name="selected_address_radio" id="address-{{ $address->address_id }}" {{ $address->is_default ? 'checked' : '' }}>
                                                            <label class="form-check-label w-100" for="address-{{ $address->address_id }}">
                                                                <h6 class="mb-1">{{ $address->full_name }}</h6>
                                                                <p class="mb-1 small text-muted">{{ $address->formatted_address }}</p>
                                                                @if($address->is_default)
                                                                <span class="badge bg-primary">Default</span>
                                                                @endif
                                                            </label>
                                                        </div>
                                                        <div class="address-actions position-absolute end-0 top-50 translate-middle-y me-2">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary edit-address-btn" data-address-id="{{ $address->address_id }}" title="Edit">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-address-btn" data-address-id="{{ $address->address_id }}" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-center py-4">
                                                        <i class="bi bi-geo-alt text-muted h3"></i>
                                                        <p class="text-muted mt-2">No saved addresses yet.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div id="address-form-area">
                                                <h6 id="address-form-title">Add / Edit Address</h6>
                                                <form id="address-form" method="POST" action="{{ route('checkout.address.store') }}">
                                                    @csrf
                                                    <input type="hidden" name="_method" id="address-form-method" value="POST">
                                                    <input type="hidden" name="address_id" id="address-form-id" value="">
                                                    <div class="mb-2">
                                                        <label class="form-label">Full Name</label>
                                                        <input name="full_name" id="addr-full_name" type="text" class="form-control" required>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Street</label>
                                                        <input name="street" id="addr-street" type="text" class="form-control" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">City</label>
                                                            <input name="city" id="addr-city" type="text" class="form-control" required>
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="form-label">State</label>
                                                            <input name="state" id="addr-state" type="text" class="form-control">
                                                        </div>
                                                        <div class="col-md-2 mb-2">
                                                            <label class="form-label">ZIP</label>
                                                            <input name="postal_code" id="addr-postal" type="text" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Country</label>
                                                        <input name="country" id="addr-country" type="text" class="form-control" value="Philippines">
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="addr-is-default">
                                                        <label class="form-check-label" for="addr-is-default">Set as default</label>
                                                    </div>
                                                    <div class="d-flex">
                                                        <button type="submit" class="btn btn-primary me-2" id="address-form-save">Save</button>
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
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <!-- Credit/Debit Card -->
                        <div class="payment-method-container mb-3" data-method="card">
                            <div class="form-check">
                                <input class="form-check-input payment-method" type="radio" name="payment_method" id="pm-card" value="card" checked>
                                <label class="form-check-label w-100" for="pm-card">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <strong>Credit / Debit Card</strong>
                                            <small class="text-muted d-block">Pay securely with your card</small>
                                        </div>
                                        <div>
                                            <i class="bi bi-credit-card fs-4"></i>
                                        </div>
                                    </div>
                                    <!-- Stripe Elements Placeholder -->
                                    <div id="card-element-container" class="mt-3">
                                        <div id="card-element" class="form-control" style="height: 2.4em; padding-top: .5em;"></div>
                                        <div id="card-errors" class="invalid-feedback d-block"></div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- GCash -->
                        <div class="payment-method-container mb-3" data-method="gcash">
                            <div class="form-check">
                                <input class="form-check-input payment-method" type="radio" name="payment_method" id="pm-gcash" value="gcash">
                                <label class="form-check-label w-100" for="pm-gcash">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>GCash</strong>
                                            <small class="text-muted d-block">Pay with your GCash wallet</small>
                                        </div>
                                        <div>
                                            <i class="bi bi-wallet2 fs-4"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div id="gcash-info" class="mt-2 small text-muted" style="display:none;">
                                You'll be redirected to complete your payment with GCash
                            </div>
                        </div>

                        <!-- Cash on Delivery -->
                        <div class="payment-method-container" data-method="cod">
                            <div class="form-check">
                                <input class="form-check-input payment-method" type="radio" name="payment_method" id="pm-cod" value="cod">
                                <label class="form-check-label w-100" for="pm-cod">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Cash on Delivery</strong>
                                            <small class="text-muted d-block">Pay when you receive your items</small>
                                        </div>
                                        <div>
                                            <i class="bi bi-cash-stack fs-4"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div id="cod-info" class="mt-2 small text-muted" style="display:none;">
                                Have the exact amount ready when your package arrives
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Toast -->
        <div class="toast" id="error-toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="toast-header bg-danger text-white">
                <i class="bi bi-exclamation-circle me-2"></i>
                <strong class="me-auto">Error</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="error-toast-body"></div>
        </div>

        <!-- Loading Overlay -->
        <div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 d-none" style="background: rgba(255,255,255,0.8); z-index: 1050;">
            <div class="position-absolute top-50 start-50 translate-middle text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2" id="loading-message">Processing your order...</div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order Summary</h5>
                    <span class="badge bg-primary" id="items-count">{{ count($cartItems) }} items</span>
                </div>
                <form id="place-order-form" method="POST" action="{{ route('checkout.process') }}" novalidate>
                    @csrf
                    <div class="card-body" id="order-summary" data-subtotal="{{ $total }}">
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

                        <!-- Hidden fields sent to server -->
                        <input type="hidden" name="selected_items" value="{{ session('checkout.selected_items') }}">
                        <input type="hidden" name="shipping_method" id="form-shipping-method" value="{{ $shippingMethods[0]['id'] }}">
                        <input type="hidden" name="address_id" id="form-address-id" value="{{ optional($defaultAddress)->address_id }}">
                        <input type="hidden" name="payment_method" id="form-payment-method" value="card">

                        <!-- Place Order Button -->
                        <button type="submit" class="btn btn-primary w-100" id="place-order-btn">
                            Place Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Stripe Elements with better styling
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const elements = stripe.elements({
        fonts: [
            {
                cssSrc: 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap',
            },
        ],
    });
    
    const style = {
        base: {
            fontFamily: 'Inter, system-ui, sans-serif',
            fontSize: '16px',
            color: '#1a1a1a',
            '::placeholder': {
                color: '#6c757d',
            },
        },
        invalid: {
            color: '#dc3545',
            iconColor: '#dc3545'
        }
    };
    
    const card = elements.create('card', { style });
    card.mount('#card-element');

    // Shipping methods
    const shippingMethods = document.querySelectorAll('.shipping-method');
    const orderSummaryEl = document.getElementById('order-summary');
    const subtotal = parseFloat(orderSummaryEl ? orderSummaryEl.dataset.subtotal : {{ json_encode($total) }});

    // Initialize shipping method handling
    const shippingOptions = document.querySelectorAll('.shipping-method-item');
    const shippingRadios = document.querySelectorAll('.shipping-method');
    
    // Handle clicks on shipping method container
    shippingOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            // Don't handle if clicking on the radio input directly
            if (e.target.classList.contains('shipping-method')) {
                return;
            }
            
            // Find and check the radio input
            const radio = this.querySelector('.shipping-method');
            if (radio) {
                radio.checked = true;
                
                // Update UI and totals
                updateShippingSelection(this);
                updateTotal();
            }
        });
    });

    // Handle direct radio button changes
    shippingRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateShippingSelection(this.closest('.shipping-method-item'));
            updateTotal();
        });
    });

    function updateShippingSelection(selectedOption) {
        // Remove active class from all options
        shippingOptions.forEach(opt => opt.classList.remove('active'));
        
        // Add active class to selected option
        selectedOption.classList.add('active');
        
        // Update hidden form field
        const shippingId = selectedOption.dataset.shippingId;
        document.getElementById('form-shipping-method').value = shippingId;
    }
    
    // Keep shipping radio change handling in the same scope
    shippingMethods.forEach(method => {
        method.addEventListener('change', function() {
            const container = this.closest('.shipping-method-item');
            if (container) updateShippingSelection(container);
            updateTotal();
            // update hidden form field
            const formShipping = document.getElementById('form-shipping-method');
            if (formShipping) formShipping.value = this.value;
        });
    });

    function updateTotal() {
        const selectedMethod = document.querySelector('.shipping-method:checked');
        let shippingPrice = 0;
        if (selectedMethod && selectedMethod.dataset && selectedMethod.dataset.price) {
            shippingPrice = parseFloat(String(selectedMethod.dataset.price).replace(/[^0-9.-]+/g, ''));
            if (isNaN(shippingPrice)) shippingPrice = 0;
        }

        // Ensure subtotal is a number (strip any formatting)
        let parsedSubtotal = 0;
        try {
            parsedSubtotal = parseFloat(String(subtotal).replace(/[^0-9.-]+/g, ''));
            if (isNaN(parsedSubtotal)) parsedSubtotal = 0;
        } catch (err) {
            parsedSubtotal = 0;
        }

        const shippingCostEl = document.getElementById('shipping-cost');
        const orderTotalEl = document.getElementById('order-total');
        console.debug('Updating totals:', {
            subtotal: parsedSubtotal,
            shippingMethod: selectedMethod ? selectedMethod.value : null,
            shippingPrice: shippingPrice,
            total: parsedSubtotal + shippingPrice
        });
        if (shippingCostEl) shippingCostEl.textContent = formatCurrency(shippingPrice);
        if (orderTotalEl) orderTotalEl.textContent = formatCurrency(parsedSubtotal + shippingPrice);

        // Update hidden field with current shipping method
        const formShippingEl = document.getElementById('form-shipping-method');
        if (formShippingEl && selectedMethod) formShippingEl.value = selectedMethod.value;
    }

    // Run on load
    updateTotal();

    // Address modal interactions (AJAX-enabled)
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';

    function buildAddressHtml(address) {
        const isDefaultBadge = address.is_default ? '<span class="badge bg-primary">Default</span>' : '';
        return `
            <div class="list-group-item address-item position-relative" data-address-id="${address.address_id}" data-fullname="${address.full_name}" data-street="${address.street}" data-city="${address.city}" data-state="${address.state}" data-postal="${address.postal_code}" data-country="${address.country}">
                <div class="form-check">
                    <input class="form-check-input address-selector" type="radio" name="selected_address_radio" id="address-${address.address_id}" ${address.is_default ? 'checked' : ''}>
                    <label class="form-check-label w-100" for="address-${address.address_id}">
                        <h6 class="mb-1">${address.full_name}</h6>
                        <p class="mb-1 small text-muted">${address.formatted_address}</p>
                        ${isDefaultBadge}
                    </label>
                </div>
                <div class="address-actions position-absolute end-0 top-50 translate-middle-y me-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary edit-address-btn" data-address-id="${address.address_id}" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger delete-address-btn" data-address-id="${address.address_id}" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }

    function bindAddressButtons() {
        document.querySelectorAll('.select-address-btn').forEach(btn => {
            btn.removeEventListener('click', selectAddressHandler);
            btn.addEventListener('click', selectAddressHandler);
        });
        document.querySelectorAll('.edit-address-btn').forEach(btn => {
            btn.removeEventListener('click', editAddressHandler);
            btn.addEventListener('click', editAddressHandler);
        });
        document.querySelectorAll('.delete-address-btn').forEach(btn => {
            btn.removeEventListener('click', deleteAddressHandler);
            btn.addEventListener('click', deleteAddressHandler);
        });
    }

    function selectAddressFromItem(parent) {
        if (!parent) return;
        const id = parent.dataset.addressId || '';
        const fullname = parent.dataset.fullname || '';
        const street = parent.dataset.street || '';
        const city = parent.dataset.city || '';
        const state = parent.dataset.state || '';
        const postal = parent.dataset.postal || '';

        // Populate summary and hidden fields
        const summaryText = [fullname, street, city, state, postal].filter(Boolean).join(', ');
        const selectedTextEl = document.getElementById('selected-address-text');
        if (selectedTextEl) selectedTextEl.textContent = summaryText;
        const selectedIdEl = document.getElementById('selected-address-id');
        if (selectedIdEl) selectedIdEl.value = id;
        const formAddressEl = document.getElementById('form-address-id');
        if (formAddressEl) formAddressEl.value = id;

        // populate hidden delivery fields
        if (document.getElementById('delivery-full-name')) document.getElementById('delivery-full-name').value = fullname;
        if (document.getElementById('delivery-street')) document.getElementById('delivery-street').value = street;
        if (document.getElementById('delivery-city')) document.getElementById('delivery-city').value = city;
        if (document.getElementById('delivery-state')) document.getElementById('delivery-state').value = state;
        if (document.getElementById('delivery-postal')) document.getElementById('delivery-postal').value = postal;

        // Update summary without closing the modal - let user explicitly close when done
        document.getElementById('selected-address-summary').classList.add('border-primary');
    }

    function selectAddressHandler(e) {
        const btn = e.currentTarget;
        const parent = btn.closest('.address-item');
        if (!parent) return;

        // Visual feedback
        document.querySelectorAll('.address-item').forEach(item => {
            item.classList.remove('border-primary', 'bg-light');
        });
        parent.classList.add('border-primary', 'bg-light');

        selectAddressFromItem(parent);
    }

    function editAddressHandler(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const btn = e.currentTarget;
        const id = btn.dataset.addressId;
        const parent = btn.closest('.address-item');
        if (!parent) return;

        // Visual feedback for edit mode
        document.querySelectorAll('.address-item').forEach(item => {
            item.classList.remove('editing');
        });
        parent.classList.add('editing');
        
        // Scroll form into view on mobile
        const formArea = document.getElementById('address-form-area');
        if (window.innerWidth < 768) {
            formArea.scrollIntoView({ behavior: 'smooth' });
        }

        document.getElementById('address-form-title').textContent = 'Edit Address';
        document.getElementById('address-form').action = '/checkout/address/' + id;
        document.getElementById('address-form-method').value = 'PUT';
        document.getElementById('addr-full_name').value = parent.dataset.fullname || '';
        document.getElementById('addr-street').value = parent.dataset.street || '';
        document.getElementById('addr-city').value = parent.dataset.city || '';
        document.getElementById('addr-state').value = parent.dataset.state || '';
        document.getElementById('addr-postal').value = parent.dataset.postal || '';
        document.getElementById('addr-country').value = parent.dataset.country || 'Philippines';
        document.getElementById('address-form-id').value = id;
    }

    async function deleteAddressHandler(e) {
        const btn = e.currentTarget;
        const id = btn.dataset.addressId;
        if (!confirm('Delete this address?')) return;
        try {
            const res = await fetch('/checkout/address/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) throw new Error('Delete failed');
            // Remove from DOM
            const item = document.querySelector('.address-item[data-address-id="' + id + '"]');
            if (item) item.remove();
        } catch (err) {
            console.error('Delete address error', err);
            alert('Failed to delete address');
        }
    }

    // Bind handlers initially
    bindAddressButtons();

    // Bind address radio selection (choose an address by clicking the radio/label)
    document.querySelectorAll('.address-selector').forEach(radio => {
        radio.removeEventListener('change', function(){});
        radio.addEventListener('change', function() {
            const parent = this.closest('.address-item');
            selectAddressFromItem(parent);
        });
    });

    // Add New button handler
    document.getElementById('add-new-address-btn').addEventListener('click', function() {
        // Reset form to add new state
        document.getElementById('address-form-title').textContent = 'Add New Address';
        document.getElementById('address-form').action = '{{ route('checkout.address.store') }}';
        document.getElementById('address-form-method').value = 'POST';
        document.getElementById('address-form-id').value = '';
        document.getElementById('address-form').reset();
        document.getElementById('addr-country').value = 'Philippines';
    });

    // Initialize modal - prevent backdrop click and ESC from closing to avoid accidental toggles
    const addressModal = new bootstrap.Modal(document.getElementById('addressModal'), {
        backdrop: 'static', // Prevent closing when clicking outside
        keyboard: false
    });
    
    // Handle address form submission via AJAX
    const addressForm = document.getElementById('address-form');
    if (addressForm) {
        addressForm.addEventListener('submit', async function(ev) {
            ev.preventDefault();
            ev.stopPropagation();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            try {
                // Disable form submission
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                
                // Prepare form data
                const formData = new FormData(this);
                const method = this.querySelector('input[name="_method"]').value || 'POST';
                if (method.toUpperCase() === 'PUT') {
                    formData.append('_method', 'PUT');
                }
            try {
                // Submit form
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to save address');
                }
                const address = data.address;
                
                // If no addresses yet, clear the empty state message
                const emptyState = document.querySelector('.address-list .text-center');
                if (emptyState) {
                    emptyState.remove();
                }
                
                // Update the address list
                const addressList = document.querySelector('.address-list');
                const newAddressHtml = buildAddressHtml(address);
                
                if (method.toUpperCase() === 'PUT') {
                    // Update existing address in the list
                    const existingAddress = addressList.querySelector(`[data-address-id="${address.address_id}"]`);
                    if (existingAddress) {
                        existingAddress.outerHTML = newAddressHtml;
                    }
                } else {
                    // Add new address to the list
                    addressList.insertAdjacentHTML('beforeend', newAddressHtml);
                }
                
                // If address is default or there's no selected address yet, update summary
                if (address.is_default || !document.getElementById('selected-address-id').value) {
                    document.getElementById('selected-address-text').textContent = address.formatted_address;
                    document.getElementById('selected-address-id').value = address.address_id;
                    document.getElementById('form-address-id').value = address.address_id;
                }
                
                // reset form but keep modal open
                document.getElementById('address-form-title').textContent = 'Add / Edit Address';
                addressForm.action = '{{ route('checkout.address.store') }}';
                document.getElementById('address-form-method').value = 'POST';
                addressForm.reset();
                document.getElementById('addr-country').value = 'Philippines';
            } catch (err) {
                console.error('Address save error:', err);
                alert(err.message || 'Failed to save address');
            } finally {
                // Re-enable form submission
                const submitBtn = document.getElementById('address-form-save');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Save';
            }
        });
    }

    // Payment method toggles with improved focus handling
    const paymentMethods = document.querySelectorAll('.payment-method');
    const paymentContainers = document.querySelectorAll('.payment-method-container');
    const cardContainer = document.getElementById('card-element-container');
    const gcashInfo = document.getElementById('gcash-info');
    const codInfo = document.getElementById('cod-info');
    const placeOrderBtn = document.getElementById('place-order-btn');
    
    // Track form validation state
    const formState = {
        addressValid: false,
        shippingValid: false,
        paymentValid: false,
        cardComplete: false
    };

    // Listen for card element changes
    card.addEventListener('change', function(event) {
        formState.cardComplete = event.complete;
        const errorElement = document.getElementById('card-errors');
        if (event.error) {
            errorElement.textContent = event.error.message;
            errorElement.style.display = 'block';
        } else {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
        updateFormState();
    });

    function updatePaymentMethodUI(selectedMethod) {
        // Update containers
        paymentContainers.forEach(container => {
            if (container.dataset.method === selectedMethod) {
                container.classList.add('active');
            } else {
                container.classList.remove('active');
            }
        });

        // Update method-specific elements
        document.getElementById('form-payment-method').value = selectedMethod;
        
        if (selectedMethod === 'card') {
            if (cardContainer) cardContainer.style.display = 'block';
            if (gcashInfo) gcashInfo.style.display = 'none';
            if (codInfo) codInfo.style.display = 'none';
        } else if (selectedMethod === 'gcash') {
            if (cardContainer) cardContainer.style.display = 'none';
            if (gcashInfo) gcashInfo.style.display = 'block';
            if (codInfo) codInfo.style.display = 'none';
        } else if (selectedMethod === 'cod') {
            if (cardContainer) cardContainer.style.display = 'none';
            if (gcashInfo) gcashInfo.style.display = 'none';
            if (codInfo) codInfo.style.display = 'block';
        }
    }

    // Handle Cancel button in address form - just reset the form
    document.getElementById('address-form-cancel').addEventListener('click', function() {
        document.getElementById('address-form-title').textContent = 'Add / Edit Address';
        document.getElementById('address-form').action = '{{ route('checkout.address.store') }}';
        document.getElementById('address-form-method').value = 'POST';
        document.getElementById('address-form-id').value = '';
        document.getElementById('address-form').reset();
        document.getElementById('addr-country').value = 'Philippines';
        
        // Remove editing highlight from all address items
        document.querySelectorAll('.address-item').forEach(item => {
            item.classList.remove('editing');
        });
    });

    function updateFormState() {
        const addressId = document.getElementById('form-address-id').value;
        const shippingMethod = document.getElementById('form-shipping-method').value;
        const paymentMethod = document.getElementById('form-payment-method').value;
        
        formState.addressValid = !!addressId;
        formState.shippingValid = !!shippingMethod;
        formState.paymentValid = !!paymentMethod && (paymentMethod !== 'card' || formState.cardComplete);
        
        // Update place order button state
        if (placeOrderBtn) {
            const isValid = formState.addressValid && formState.shippingValid && formState.paymentValid;
            placeOrderBtn.disabled = !isValid;
            placeOrderBtn.title = isValid ? '' : 'Please complete all required fields';
        }
    }

    // Handle payment method selection with improved validation
    paymentMethods.forEach(pm => {
        pm.addEventListener('change', function() {
            updatePaymentMethodUI(this.value);
            updateFormState();
            
            // Focus card element when credit card is selected
            if (this.value === 'card' && cardContainer) {
                setTimeout(() => card.focus(), 100);
            }
        });
    });

    // Enhanced container clicks with keyboard accessibility
    paymentContainers.forEach(container => {
        container.setAttribute('tabindex', '0');
        container.setAttribute('role', 'button');
        
        const handleSelection = (e) => {
            if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') return;
            if (e.type === 'keydown') e.preventDefault();
            
            const radio = container.querySelector('.payment-method');
            if (radio && !e.target.closest('#card-element')) {
                radio.checked = true;
                updatePaymentMethodUI(radio.value);
                updateFormState();
            }
        };

        container.addEventListener('click', handleSelection);
        container.addEventListener('keydown', handleSelection);
    });

    // Set initial state for payment UI
    const initialPayment = document.querySelector('.payment-method:checked');
    if (initialPayment) updatePaymentMethodUI(initialPayment.value);

    // Ensure form hidden shipping and payment fields are correct on load
    const initialShipping = document.querySelector('.shipping-method:checked');
    if (initialShipping) {
        const container = initialShipping.closest('.shipping-method-item');
        if (container) updateShippingSelection(container);
        const formShipping = document.getElementById('form-shipping-method');
        if (formShipping) formShipping.value = initialShipping.value;
    }
    if (initialPayment) {
        document.getElementById('form-payment-method').value = initialPayment.value;
    }

    function formatCurrency(amount) {
        // Format as Philippine Peso to match server-side display
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP'
        }).format(amount);
    }

    // Improved order submission with better error handling and UX
    document.getElementById('place-order-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const selectedAddressId = document.getElementById('form-address-id').value;
        const paymentMethod = document.getElementById('form-payment-method').value;
        const shippingMethod = document.getElementById('form-shipping-method').value;

        // Reset any previous error states
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');

        // Enhanced validation with visual feedback
        let isValid = true;
        const errors = [];

        if (!selectedAddressId) {
            isValid = false;
            errors.push('Please select a delivery address');
            const addressSection = document.getElementById('selected-address-summary');
            if (addressSection) {
                addressSection.classList.add('is-invalid');
                addressSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        if (!shippingMethod) {
            isValid = false;
            errors.push('Please select a shipping method');
            const shippingSection = document.querySelector('.shipping-method-item');
            if (shippingSection) {
                shippingSection.classList.add('is-invalid');
                if (!selectedAddressId) {
                    setTimeout(() => shippingSection.scrollIntoView({ behavior: 'smooth', block: 'center' }), 500);
                }
            }
        }

        if (!paymentMethod) {
            isValid = false;
            errors.push('Please select a payment method');
            const paymentSection = document.querySelector('.payment-method-container');
            if (paymentSection) {
                paymentSection.classList.add('is-invalid');
                if (!selectedAddressId && !shippingMethod) {
                    setTimeout(() => paymentSection.scrollIntoView({ behavior: 'smooth', block: 'center' }), 1000);
                }
            }
        }

        // Card validation for credit card payment
        if (paymentMethod === 'card' && !formState.cardComplete) {
            isValid = false;
            errors.push('Please complete the card information');
            const cardElement = document.getElementById('card-element');
            if (cardElement) {
                cardElement.classList.add('is-invalid');
            }
        }

        if (!isValid) {
            // Show error toast or modal instead of alert
            const errorMessage = errors.join('\\n');
            const errorToast = new bootstrap.Toast(document.getElementById('error-toast'));
            document.getElementById('error-toast-body').textContent = errorMessage;
            errorToast.show();
            return;
        }

        // Handle payment based on selected method
        if (paymentMethod === 'card') {
            const { error } = await stripe.createPaymentMethod({
                type: 'card',
                card: card
            });

            if (error) {
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;
                return;
            }
        }

        // All validations passed, enable loading state
        const submitBtn = document.getElementById('place-order-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        try {
            // Submit the form
            this.submit();
        } catch (err) {
            console.error('Order submission error:', err);
            alert('An error occurred while processing your order. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});
</script>
@endpush