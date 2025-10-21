@props(['product', 'showCompare' => true])

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
        @if($product->activeAttributes && $product->activeAttributes->count() > 0)
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
            
            @if($showCompare)
                <div class="d-flex gap-2 mb-2">
                    <button type="button" 
                            class="btn btn-outline-secondary btn-sm compare-btn w-100"
                            data-product-id="{{ $product->product_id }}"
                            data-product-slug="{{ $product->slug }}"
                            title="Add to comparison">
                        <i class="bi bi-plus-circle"></i> Compare
                    </button>
                </div>
            @endif
            
            <div class="d-flex gap-2">
                <a href="{{ route('storefront.product', $product) }}" class="btn btn-outline-primary flex-fill">
                    <i class="bi bi-eye"></i> View
                </a>
                @if($product->in_stock)
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="flex-fill">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-cart-plus"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>