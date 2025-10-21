@extends('layouts.storefront')

@section('title', 'Compare Products')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">Compare Products</h1>
                @if($products->count() > 0)
                <form method="POST" action="{{ route('compare.clear') }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm" 
                            onclick="return confirm('Are you sure you want to clear all comparisons?')">
                        <i class="bi bi-trash"></i> Clear All
                    </button>
                </form>
                @endif
            </div>

            @if($products->count() === 0)
            <div class="text-center py-5">
                <i class="bi bi-compare display-1 text-muted mb-3"></i>
                <h3>No Products to Compare</h3>
                <p class="text-muted">Add products to comparison from product pages to see them here.</p>
                <a href="{{ route('storefront.products') }}" class="btn btn-primary">
                    <i class="bi bi-shop"></i> Browse Products
                </a>
            </div>
            @else
            <div class="comparison-table-container">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <td class="fw-bold" style="width: 200px;">Product</td>
                                @foreach($products as $product)
                                <td class="text-center position-relative" style="min-width: 250px;">
                                    <form method="POST" action="{{ route('compare.remove', $product) }}" 
                                          class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove from comparison">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </form>
                                    
                                    @if($product->main_image)
                                        <img src="{{ Storage::url($product->main_image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="img-fluid mb-2" 
                                             style="height: 150px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center mb-2" 
                                             style="height: 150px;">
                                            <i class="bi bi-image text-muted display-6"></i>
                                        </div>
                                    @endif
                                    
                                    <h6 class="mb-2">{{ $product->name }}</h6>
                                    <div class="mb-2">
                                        @if($product->is_on_sale)
                                            <span class="fw-bold text-primary">{{ currency($product->sale_price) }}</span>
                                            <span class="text-decoration-line-through text-muted small ms-1">{{ currency($product->price) }}</span>
                                        @else
                                            <span class="fw-bold">{{ currency($product->price) }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('storefront.product', $product) }}" 
                                           class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <form method="POST" action="{{ route('cart.add', $product) }}" class="flex-fill">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                <i class="bi bi-cart-plus"></i> Add
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Basic Information -->
                            <tr>
                                <td class="fw-bold bg-light">Brand</td>
                                @foreach($products as $product)
                                <td class="text-center">{{ $product->brand ?? 'N/A' }}</td>
                                @endforeach
                            </tr>
                            
                            <tr>
                                <td class="fw-bold bg-light">Model</td>
                                @foreach($products as $product)
                                <td class="text-center">{{ $product->model ?? 'N/A' }}</td>
                                @endforeach
                            </tr>
                            
                            <tr>
                                <td class="fw-bold bg-light">Category</td>
                                @foreach($products as $product)
                                <td class="text-center">{{ $product->category->name ?? 'N/A' }}</td>
                                @endforeach
                            </tr>
                            
                            <tr>
                                <td class="fw-bold bg-light">SKU</td>
                                @foreach($products as $product)
                                <td class="text-center"><code>{{ $product->sku }}</code></td>
                                @endforeach
                            </tr>

                            <!-- Key Features -->
                            @if($products->whereNotNull('key_features')->count() > 0)
                            <tr>
                                <td class="fw-bold bg-light">Key Features</td>
                                @foreach($products as $product)
                                <td class="text-start">
                                    @if($product->key_features)
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($product->key_features as $feature)
                                                <li><i class="bi bi-check-circle text-success me-1"></i>{{ $feature }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endif

                            <!-- Dynamic Attributes -->
                            @if(count($attributes) > 0)
                                @foreach($attributes as $attributeSlug => $attributeData)
                                    @php
                                        $definition = $attributeData->first()->attributeDefinition;
                                    @endphp
                                    <tr>
                                        <td class="fw-bold bg-light">{{ $definition->display_name }}</td>
                                        @foreach($products as $product)
                                            @php
                                                $productAttribute = $attributeData->where('product_id', $product->product_id)->first();
                                            @endphp
                                            <td class="text-center">
                                                @if($productAttribute)
                                                    {{ $productAttribute->formattedValue }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endif

                            <!-- Legacy Specifications (fallback for products without dynamic attributes) -->
                            @if($products->whereNotNull('specifications')->count() > 0)
                                @php
                                    $allSpecs = collect();
                                    foreach($products as $product) {
                                        if($product->specifications) {
                                            $allSpecs = $allSpecs->merge(array_keys($product->specifications));
                                        }
                                    }
                                    $allSpecs = $allSpecs->unique()->sort();
                                @endphp
                                
                                @foreach($allSpecs as $specKey)
                                <tr>
                                    <td class="fw-bold bg-light">{{ ucwords(str_replace('_', ' ', $specKey)) }}</td>
                                    @foreach($products as $product)
                                    <td class="text-center">
                                        {{ $product->specifications[$specKey] ?? 'N/A' }}
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            @endif

                            <!-- Physical Properties -->
                            <tr>
                                <td class="fw-bold bg-light">Weight</td>
                                @foreach($products as $product)
                                <td class="text-center">{{ $product->weight ? $product->weight . ' kg' : 'N/A' }}</td>
                                @endforeach
                            </tr>
                            
                            <tr>
                                <td class="fw-bold bg-light">Dimensions</td>
                                @foreach($products as $product)
                                <td class="text-center">{{ $product->dimensions ?? 'N/A' }}</td>
                                @endforeach
                            </tr>
                            
                            <!-- Stock Status -->
                            <tr>
                                <td class="fw-bold bg-light">Stock Status</td>
                                @foreach($products as $product)
                                <td class="text-center">
                                    @if($product->in_stock)
                                        <span class="badge bg-success">In Stock</span>
                                        @if($product->manage_stock && $product->stock_quantity)
                                            <small class="d-block text-muted">{{ $product->stock_quantity }} available</small>
                                        @endif
                                    @else
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($products->count() < 4 && $products->count() > 0)
<div class="container pb-5">
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        You can compare up to 4 products. Add more products from the product pages to compare.
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update comparison count in navigation
    updateComparisonCount();
});

function updateComparisonCount() {
    fetch('{{ route("compare.count") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.comparison-count');
            if (badge) {
                badge.textContent = data.count;
                badge.style.display = data.count > 0 ? 'inline' : 'none';
            }
        });
}
</script>
@endsection