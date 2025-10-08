@extends('layouts.storefront')

@section('title', $category->name)

@section('content')
<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('storefront.home') }}">Home</a></li>
                    <li class="breadcrumb-item active">{{ $category->name }}</li>
                </ol>
            </nav>
            
            <div class="d-flex align-items-center mb-3">
                @if($category->image)
                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" 
                         class="me-3 rounded" style="width: 60px; height: 60px; object-fit: cover;">
                @endif
                <div>
                    <h1 class="h2 fw-bold mb-1">{{ $category->name }}</h1>
                    @if($category->description)
                        <p class="text-muted mb-0">{{ $category->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($products->count() > 0)
        <!-- Results Info -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="text-muted mb-0">
                {{ $products->total() }} {{ Str::plural('product', $products->total()) }} found
            </p>
        </div>

        <!-- Products Grid -->
        <div class="row g-4">
            @foreach($products as $product)
            <div class="col-lg-3 col-md-6">
                <div class="card product-card h-100">
                    @if($product->is_on_sale)
                        <div class="badge-sale badge">
                            -{{ $product->discount_percentage }}%
                        </div>
                    @endif
                    
                    @if($product->main_image)
                        <img src="{{ Storage::url($product->main_image) }}" class="card-img-top" alt="{{ $product->name }}">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                            <i class="bi bi-image text-muted display-4"></i>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title">{{ Str::limit($product->name, 50) }}</h6>
                        <p class="card-text text-muted small">{{ Str::limit($product->short_description, 80) }}</p>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    @if($product->is_on_sale)
                                        <span class="price sale-price">{{ currency($product->sale_price) }}</span>
                                        <span class="original-price ms-2">{{ currency($product->price) }}</span>
                                    @else
                                        <span class="price">{{ currency($product->price) }}</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $product->sku }}</small>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('storefront.product', $product) }}" class="btn btn-outline-primary flex-fill">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                @if($product->in_stock)
                                    <form method="POST" action="{{ route('cart.add', $product) }}" class="flex-fill">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-cart-plus"></i> Add
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-secondary flex-fill" disabled>
                                        <i class="bi bi-x-circle"></i> Out of Stock
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $products->links() }}
            </div>
        @endif
    @else
        <!-- No Products Found -->
        <div class="text-center py-5">
            <i class="bi bi-box-seam display-4 text-muted"></i>
            <h4 class="mt-3">No products in this category</h4>
            <p class="text-muted">This category doesn't have any products yet.</p>
            <a href="{{ route('storefront.products') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Browse All Products
            </a>
        </div>
    @endif
</div>
@endsection