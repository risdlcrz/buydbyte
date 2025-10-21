@extends('layouts.storefront')

@section('title', $category->name)

@section('content')
<div class="container py-5">
    <!-- Promotional Banners -->
    @include('components.promotions.banner', ['promotions' => $promotions, 'page' => 'categories'])

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
                <x-product-card :product="$product" :showCompare="false" />
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