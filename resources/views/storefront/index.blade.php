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

<!-- Promotional Banners -->
@include('components.promotions.banner', ['promotions' => $promotions, 'page' => 'homepage'])

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
                <x-product-card :product="$product" />
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
                <x-product-card :product="$product" />
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