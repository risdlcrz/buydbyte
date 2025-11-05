@extends('layouts.storefront')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Give Feedback</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.feedback.store') }}" method="POST" id="feedbackForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Type of Feedback</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="type" id="type-general" value="general" {{ $selectedOrder ? '' : 'checked' }}>
                                <label class="btn btn-outline-primary" for="type-general">
                                    <i class="fas fa-comment me-2"></i>General
                                </label>

                                <input type="radio" class="btn-check" name="type" id="type-order" value="order" {{ $selectedOrder ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="type-order">
                                    <i class="fas fa-shopping-cart me-2"></i>Order
                                </label>

                                <input type="radio" class="btn-check" name="type" id="type-product" value="product">
                                <label class="btn btn-outline-primary" for="type-product">
                                    <i class="fas fa-box me-2"></i>Product
                                </label>

                                <input type="radio" class="btn-check" name="type" id="type-service" value="service">
                                <label class="btn btn-outline-primary" for="type-service">
                                    <i class="fas fa-headset me-2"></i>Service
                                </label>
                            </div>
                        </div>

                        <div id="orderSelect" class="mb-4 d-none">
                            <label for="order_id" class="form-label">Select Order</label>
                            <select class="form-select" name="order_id" id="order_id">
                                <option value="">Select an order</option>
                                @foreach($orders as $order)
                                    <option value="{{ $order->order_id }}" {{ $selectedOrder && $selectedOrder->order_id === $order->order_id ? 'selected' : '' }}>
                                        Order #{{ $order->order_number }} - {{ $order->created_at->format('M d, Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="productSelect" class="mb-4 d-none">
                            <label for="product_id" class="form-label">Select Product</label>
                            <select class="form-select" name="product_id" id="product_id">
                                <option value="">Loading products...</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Rating</label>
                            <div class="star-rating">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" />
                                    <label for="star{{ $i }}" title="{{ $i }} stars">
                                        <i class="bi bi-star star-icon"></i>
                                    </label>
                                @endfor
                            </div>
                            @error('rating')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="comment" class="form-label">Your Feedback</label>
                            <textarea class="form-control" id="comment" name="comment" rows="5" 
                                    placeholder="Share your experience with us...">{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Submit Feedback</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.star-rating {
    display: flex;
    flex-direction: row-reverse;
    gap: 0.5rem;
    justify-content: flex-end;
    align-items: center;
}

.star-rating input {
    display: none;
}

.star-rating label {
    cursor: pointer;
    margin: 0;
    padding: 0;
    line-height: 1;
}

.star-rating label .star-icon {
    font-size: 2rem;
    color: #ddd;
    transition: all 0.2s ease;
    display: block;
}

.star-rating input:checked ~ label .star-icon,
.star-rating label:hover .star-icon,
.star-rating label:hover ~ label .star-icon {
    color: #ffd700;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const orderSelect = document.getElementById('orderSelect');
    const productSelect = document.getElementById('productSelect');
    
    // Show order select if order type is selected or if selectedOrder exists
    const selectedType = document.querySelector('input[name="type"]:checked');
    if (selectedType && selectedType.value === 'order') {
        orderSelect.classList.remove('d-none');
    }
    
    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            orderSelect.classList.add('d-none');
            productSelect.classList.add('d-none');
            
            if (this.value === 'order') {
                orderSelect.classList.remove('d-none');
            } else if (this.value === 'product') {
                productSelect.classList.remove('d-none');
                loadProducts();
            }
        });
    });

    function loadProducts() {
        fetch('/api/products')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('product_id');
                select.innerHTML = '<option value="">Select a product</option>';
                data.forEach(product => {
                    select.innerHTML += `<option value="${product.product_id}">${product.name}</option>`;
                });
            });
    }

    // Star rating functionality
    const starInputs = document.querySelectorAll('.star-rating input[type="radio"]');
    const starLabels = document.querySelectorAll('.star-rating label');
    
    function updateStars(selectedValue) {
        starLabels.forEach((label, index) => {
            const starIcon = label.querySelector('.star-icon');
            const starValue = 6 - index; // Reverse order (5, 4, 3, 2, 1)
            
            if (starValue <= selectedValue) {
                starIcon.classList.remove('bi-star');
                starIcon.classList.add('bi-star-fill');
            } else {
                starIcon.classList.remove('bi-star-fill');
                starIcon.classList.add('bi-star');
            }
        });
    }
    
    starInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateStars(parseInt(this.value));
        });
        
        input.addEventListener('mouseenter', function() {
            const value = parseInt(this.value);
            updateStars(value);
        });
    });
    
    // Reset stars on mouse leave (if nothing is selected)
    const starRating = document.querySelector('.star-rating');
    starRating.addEventListener('mouseleave', function() {
        const checked = document.querySelector('.star-rating input:checked');
        if (checked) {
            updateStars(parseInt(checked.value));
        } else {
            updateStars(0);
        }
    });
    
    // Initialize stars if there's a previously selected value
    const checkedStar = document.querySelector('.star-rating input:checked');
    if (checkedStar) {
        updateStars(parseInt(checkedStar.value));
    }
});
</script>
@endpush
@endsection