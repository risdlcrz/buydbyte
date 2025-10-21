@extends('layouts.storefront')

@section('title', 'Products')

@section('content')
<div class="container py-5">
    <!-- Promotional Banners -->
    @include('components.promotions.banner', ['promotions' => $promotions, 'page' => 'products'])

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 fw-bold mb-3">All Products</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('storefront.home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Products</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-funnel"></i> Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('storefront.products') }}">
                        <!-- Search -->
                        <div class="mb-4">
                            <label for="search" class="form-label fw-bold">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search products...">
                        </div>

                        <!-- Categories -->
                        @if($categories->count() > 0)
                        <div class="mb-4">
                            <label for="category" class="form-label fw-bold">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" 
                                            {{ request('category') === $category->slug ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Price Range -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Price Range</label>
                            <div class="row g-2">
                                <div class="col">
                                    <input type="number" class="form-control" name="min_price" 
                                           placeholder="Min" value="{{ request('min_price') }}" min="0" step="0.01">
                                </div>
                                <div class="col">
                                    <input type="number" class="form-control" name="max_price" 
                                           placeholder="Max" value="{{ request('max_price') }}" min="0" step="0.01">
                                </div>
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="mb-4">
                            <label for="sort" class="form-label fw-bold">Sort By</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name A-Z</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        
                        @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'sort']))
                            <a href="{{ route('storefront.products') }}" class="btn btn-outline-secondary w-100 mt-2">
                                Clear Filters
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            @if($products->count() > 0)
                <!-- Results Info -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="text-muted mb-0">
                        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} 
                        of {{ $products->total() }} results
                    </p>
                </div>

                <!-- Products Grid -->
                <div class="row g-4">
                    @foreach($products as $product)
                    <div class="col-lg-4 col-md-6">
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
                                
                                <!-- Key Attributes Preview -->
                                @if($product->activeAttributes->count() > 0)
                                    <div class="mb-2">
                                        @php 
                                            $sortedAttributes = $product->activeAttributes->sortBy(function($attr) {
                                                return $attr->attributeDefinition->sort_order ?? 999;
                                            });
                                            $keyAttributes = $sortedAttributes->take(2);
                                        @endphp
                                        @foreach($keyAttributes as $attribute)
                                            <small class="badge bg-light text-dark me-1">
                                                {{ $attribute->attributeDefinition->name }}: {{ $attribute->value }}
                                                @if($attribute->attributeDefinition->unit){{ $attribute->attributeDefinition->unit }}@endif
                                            </small>
                                        @endforeach
                                        @if($product->activeAttributes->count() > 2)
                                            <small class="text-muted">+{{ $product->activeAttributes->count() - 2 }} more</small>
                                        @endif
                                    </div>
                                @endif
                                
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
                                    
                                    <div class="d-flex gap-2 mb-2">
                                        <button type="button" 
                                                class="btn btn-outline-secondary btn-sm compare-btn"
                                                data-product-id="{{ $product->product_id }}"
                                                data-product-slug="{{ $product->slug }}"
                                                title="Add to comparison">
                                            <i class="bi bi-plus-circle"></i> Compare
                                        </button>
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

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <!-- No Products Found -->
                <div class="text-center py-5">
                    <i class="bi bi-search display-4 text-muted"></i>
                    <h4 class="mt-3">No products found</h4>
                    <p class="text-muted">Try adjusting your search criteria or browse different categories.</p>
                    <a href="{{ route('storefront.products') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> View All Products
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection