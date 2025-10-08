@extends('layouts.storefront')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Welcome to BuyDByte</h1>
                <p class="lead mb-4">Discover amazing digital products and electronics at unbeatable prices. Quality guaranteed, fast delivery.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('storefront.products') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-shop"></i> Shop Now
                    </a>
                    <a href="#featured" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-arrow-down"></i> Explore
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <i class="bi bi-cart-check display-1 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
@if($categories->count() > 0)
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 fw-bold mb-3">Shop by Category</h2>
            <p class="lead text-muted">Browse our wide range of product categories</p>
        </div>

        <div class="row g-4">
            @foreach($categories as $category)
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('storefront.category', $category) }}" class="category-card card h-100 text-decoration-none">
                    <div class="card-body text-center p-4">
                        @if($category->image)
                            <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" 
                                 class="img-fluid mb-3" style="height: 80px; object-fit: cover;">
                        @else
                            <i class="bi bi-tag display-4 mb-3"></i>
                        @endif
                        <h5 class="card-title">{{ $category->name }}</h5>
                        <p class="card-text">{{ $category->products_count }} products</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Featured Products Section -->
@if($featured_products->count() > 0)
<section id="featured" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 fw-bold mb-3">Featured Products</h2>
            <p class="lead text-muted">Handpicked products just for you</p>
        </div>

        <div class="row g-4">
            @foreach($featured_products as $product)
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
                                <small class="text-muted">{{ $product->category->name }}</small>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('storefront.product', $product) }}" class="btn btn-outline-primary flex-fill">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <form method="POST" action="{{ route('cart.add', $product) }}" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-cart-plus"></i> Add
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('storefront.products') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-grid"></i> View All Products
            </a>
        </div>
    </div>
</section>
@endif

<!-- Latest Products Section -->
@if($latest_products->count() > 0)
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 fw-bold mb-3">Latest Products</h2>
            <p class="lead text-muted">Check out our newest arrivals</p>
        </div>

        <div class="row g-4">
            @foreach($latest_products as $product)
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
                                <small class="text-muted">{{ $product->category->name }}</small>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('storefront.product', $product) }}" class="btn btn-outline-primary flex-fill">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <form method="POST" action="{{ route('cart.add', $product) }}" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-cart-plus"></i> Add
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="h2 fw-bold mb-3">Ready to start shopping?</h3>
                <p class="lead mb-0">Join thousands of satisfied customers and discover amazing products at great prices.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('storefront.products') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-shop"></i> Start Shopping
                </a>
            </div>
        </div>
    </div>
</section>
@endsection