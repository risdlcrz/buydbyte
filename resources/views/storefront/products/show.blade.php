@extends('layouts.storefront')

@section('title', $product->name)

@section('content')
<div class="container py-5">
    <!-- Promotional Banners -->
    @include('components.promotions.banner', ['promotions' => $promotions, 'page' => 'product_detail'])

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('storefront.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('storefront.products') }}">Products</a></li>
            <li class="breadcrumb-item"><a href="{{ route('storefront.category', $product->category) }}">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="position-relative">
                    @if($product->is_on_sale)
                        <div class="badge-sale badge position-absolute" style="top: 15px; right: 15px; z-index: 10;">
                            -{{ $product->discount_percentage }}% OFF
                        </div>
                    @endif
                    
                    @if($product->main_image)
                        <img src="{{ Storage::url($product->main_image) }}" 
                             class="card-img-top" alt="{{ $product->name }}" 
                             style="height: 400px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                             style="height: 400px;">
                            <i class="bi bi-image text-muted display-1"></i>
                            <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-dark bg-opacity-75 text-white">
                                <small>No image available</small>
                            </div>
                        </div>
                    @endif
                </div>
                
                @if($product->images && count($product->images) > 1)
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach($product->images as $image)
                                <div class="col-3">
                                    <img src="{{ Storage::url($image) }}" 
                                         class="img-fluid rounded border" 
                                         alt="{{ $product->name }}" 
                                         style="height: 80px; object-fit: cover; cursor: pointer;"
                                         onclick="changeMainImage('{{ Storage::url($image) }}')">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="mb-4">
                <h1 class="h2 fw-bold mb-2">{{ $product->name }}</h1>
                <p class="text-muted mb-3">
                    <span class="badge bg-light text-dark me-2">{{ $product->category->name }}</span>
                    SKU: {{ $product->sku }}
                </p>

                <!-- Price -->
                <div class="mb-4">
                    @if($product->is_on_sale)
                        <div class="d-flex align-items-center gap-3">
                            <span class="h3 text-danger fw-bold mb-0">{{ currency($product->sale_price) }}</span>
                            <span class="h5 text-muted text-decoration-line-through mb-0">{{ currency($product->price) }}</span>
                            <span class="badge bg-danger">Save {{ currency($product->price - $product->sale_price) }}</span>
                        </div>
                    @else
                        <span class="h3 text-primary fw-bold">{{ currency($product->price) }}</span>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="mb-4">
                    @if($product->in_stock)
                        @if($product->manage_stock)
                            @if($product->stock_quantity <= 10)
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Only {{ $product->stock_quantity }} left in stock
                                </span>
                            @else
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i>
                                    In Stock ({{ $product->stock_quantity }} available)
                                </span>
                            @endif
                        @else
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i>
                                In Stock
                            </span>
                        @endif
                    @else
                        <span class="badge bg-danger">
                            <i class="bi bi-x-circle"></i>
                            Out of Stock
                        </span>
                    @endif
                </div>

                <!-- Short Description -->
                @if($product->short_description)
                    <p class="lead">{{ $product->short_description }}</p>
                @endif

                <!-- Add to Cart Form -->
                @if($product->in_stock)
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="mb-4">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" 
                                       value="1" min="1" 
                                       @if($product->manage_stock) max="{{ $product->stock_quantity }}" @endif>
                            </div>
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-cart-plus"></i>
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <button class="btn btn-secondary btn-lg w-100" disabled>
                        <i class="bi bi-x-circle"></i>
                        Out of Stock
                    </button>
                @endif

                <!-- Product Info -->
                <div class="border rounded p-3 bg-light">
                    <div class="row g-2 small">
                        @if($product->weight)
                            <div class="col-6">
                                <strong>Weight:</strong> {{ $product->weight }} kg
                            </div>
                        @endif
                        @if($product->dimensions)
                            <div class="col-6">
                                <strong>Dimensions:</strong> {{ $product->dimensions }}
                            </div>
                        @endif
                        <div class="col-6">
                            <strong>Category:</strong> {{ $product->category->name }}
                        </div>
                        <div class="col-6">
                            <strong>SKU:</strong> {{ $product->sku }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description -->
    @if($product->description)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Product Description</h5>
                    </div>
                    <div class="card-body">
                        <div class="product-description">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Product Specifications -->
    @if($product->activeAttributes->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Specifications
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @php 
                                $sortedAttributes = $product->activeAttributes->sortBy(function($attr) {
                                    return $attr->attributeDefinition->sort_order ?? 999;
                                });
                                $groupedAttributes = $sortedAttributes->groupBy('attributeDefinition.attribute_group'); 
                            @endphp
                            
                            @foreach($groupedAttributes as $group => $attributes)
                                <div class="col-md-6">
                                    @if($group && $group !== 'general')
                                        <h6 class="text-primary border-bottom pb-2 mb-3">{{ ucwords(str_replace('_', ' ', $group)) }}</h6>
                                    @endif
                                    
                                    <div class="specifications-list">
                                        @foreach($attributes as $attribute)
                                            <div class="d-flex justify-content-between py-2 border-bottom border-light">
                                                <strong class="text-muted">{{ $attribute->attributeDefinition->name }}:</strong>
                                                <span>
                                                    @if($attribute->attributeDefinition->unit)
                                                        {{ $attribute->value }} {{ $attribute->attributeDefinition->unit }}
                                                    @else
                                                        {{ $attribute->value }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Related Products -->
    @if($related_products->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="h4 mb-4">Related Products</h3>
                <div class="row g-4">
                    @foreach($related_products as $related_product)
                        <div class="col-lg-3 col-md-6">
                            <x-product-card :product="$related_product" :showCompare="false" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function changeMainImage(src) {
    document.querySelector('.card-img-top').src = src;
}
</script>
@endpush
@endsection