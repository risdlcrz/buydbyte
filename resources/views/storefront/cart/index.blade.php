@extends('layouts.storefront')

@section('title', 'Shopping Cart')

@section('content')
<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 fw-bold mb-3">Shopping Cart</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('storefront.home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Cart</li>
                </ol>
            </nav>
        </div>
    </div>

    @if($cartItems->count() > 0)
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Cart Items ({{ $cartItems->count() }})</h5>
                        <form method="POST" action="{{ route('cart.clear') }}" 
                              onsubmit="return confirm('Are you sure you want to clear your cart?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i> Clear Cart
                            </button>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        @foreach($cartItems as $item)
                        <div class="d-flex align-items-center p-4 border-bottom">
                            <!-- Product Image -->
                            <div class="me-4">
                                @if($item->product->main_image)
                                    <img src="{{ Storage::url($item->product->main_image) }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                         style="width: 80px; height: 80px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Details -->
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="{{ route('storefront.product', $item->product) }}" 
                                       class="text-decoration-none text-dark">
                                        {{ $item->product->name }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-1">{{ $item->product->category->name }}</p>
                                <p class="text-muted small mb-0">SKU: {{ $item->product->sku }}</p>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="me-4">
                                <form method="POST" action="{{ route('cart.update', $item) }}" class="d-flex align-items-center">
                                    @csrf
                                    @method('PATCH')
                                    <button type="button" class="btn btn-outline-secondary btn-sm" 
                                            onclick="decreaseQuantity(this)">-</button>
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" 
                                           min="1" max="{{ $item->product->stock_quantity }}" 
                                           class="form-control text-center mx-2" style="width: 60px;"
                                           onchange="this.form.submit()">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" 
                                            onclick="increaseQuantity(this)">+</button>
                                </form>
                            </div>

                            <!-- Price -->
                            <div class="me-4 text-center">
                                <div class="fw-bold">{{ currency($item->price) }}</div>
                                <small class="text-muted">each</small>
                            </div>

                            <!-- Total -->
                            <div class="me-4 text-center">
                                <div class="fw-bold text-primary">{{ currency($item->total) }}</div>
                                <small class="text-muted">total</small>
                            </div>

                            <!-- Remove Button -->
                            <div>
                                <form method="POST" action="{{ route('cart.remove', $item) }}" 
                                      onsubmit="return confirm('Remove this item from cart?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Continue Shopping -->
                <div class="mt-4">
                    <a href="{{ route('storefront.products') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <!-- Discount Code Section -->
                        <div class="mb-4">
                            @include('components.discount-code', ['size' => 'sm'])
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal ({{ $cartItems->sum('quantity') }} items):</span>
                            <span class="fw-bold" id="cart-subtotal">{{ currency($total) }}</span>
                        </div>
                        
                        <!-- Discount Line (hidden by default) -->
                        <div class="d-flex justify-content-between mb-3 text-success d-none" id="discount-line">
                            <span>Discount (<span id="discount-code-applied"></span>):</span>
                            <span class="fw-bold" id="discount-amount">-{{ currency(0) }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping:</span>
                            <span class="text-success">Free</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tax:</span>
                            <span>Calculated at checkout</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5">Total:</span>
                            <span class="h5 text-primary" id="cart-total">{{ currency($total) }}</span>
                        </div>

                        @auth
                            <button class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-credit-card"></i> Proceed to Checkout
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-person"></i> Login to Checkout
                            </a>
                            <p class="text-center small text-muted">
                                Don't have an account? 
                                <a href="{{ route('register') }}">Sign up here</a>
                            </p>
                        @endauth

                        <!-- Security Info -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <small class="text-muted">
                                <i class="bi bi-shield-check text-success"></i>
                                Secure SSL encryption
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Recently Viewed or Recommendations could go here -->
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h3 class="mt-4">Your cart is empty</h3>
            <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
            <a href="{{ route('storefront.products') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-shop"></i> Start Shopping
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function increaseQuantity(button) {
    const input = button.previousElementSibling;
    const max = parseInt(input.getAttribute('max'));
    const current = parseInt(input.value);
    if (current < max) {
        input.value = current + 1;
        input.form.submit();
    }
}

function decreaseQuantity(button) {
    const input = button.nextElementSibling;
    const current = parseInt(input.value);
    if (current > 1) {
        input.value = current - 1;
        input.form.submit();
    }
}

// Handle discount updates
document.addEventListener('DOMContentLoaded', function() {
    const subtotalElement = document.getElementById('cart-subtotal');
    const totalElement = document.getElementById('cart-total');
    const discountLine = document.getElementById('discount-line');
    const discountAmount = document.getElementById('discount-amount');
    const discountCodeApplied = document.getElementById('discount-code-applied');
    
    const originalSubtotal = {{ $total }};
    
    // Listen for discount events
    window.addEventListener('discountApplied', function(event) {
        const discount = event.detail;
        updateCartTotals(discount);
    });
    
    window.addEventListener('discountRemoved', function() {
        resetCartTotals();
    });
    
    function updateCartTotals(discount) {
        let discountValue = 0;
        
        if (discount.type === 'percentage') {
            discountValue = (originalSubtotal * discount.value) / 100;
        } else {
            discountValue = Math.min(discount.value, originalSubtotal);
        }
        
        const newTotal = Math.max(0, originalSubtotal - discountValue);
        
        // Update display
        discountCodeApplied.textContent = discount.code;
        discountAmount.textContent = '-' + formatCurrency(discountValue);
        totalElement.textContent = formatCurrency(newTotal);
        discountLine.classList.remove('d-none');
    }
    
    function resetCartTotals() {
        totalElement.textContent = formatCurrency(originalSubtotal);
        discountLine.classList.add('d-none');
    }
    
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }
});
</script>
@endpush
@endsection