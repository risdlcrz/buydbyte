@extends('layouts.admin')

@section('title', 'Product Details')
@section('page-title', 'Product Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">{{ $product->name }}</li>
@endsection

@push('styles')
<style>
    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .image-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
    }
    
    .detail-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }
    
    .detail-label {
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.25rem;
    }
    
    .detail-value {
        color: #1e293b;
        margin-bottom: 1rem;
    }
    
    .price-display {
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .sale-price {
        color: #dc2626;
    }
    
    .original-price {
        color: #64748b;
        text-decoration: line-through;
        font-size: 1rem;
        margin-left: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="row">
    <!-- Product Information -->
    <div class="col-lg-8">
        <!-- Basic Information -->
        <div class="detail-card">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h2 class="mb-2">{{ $product->name }}</h2>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($product->is_featured)
                            <span class="badge bg-warning">Featured</span>
                        @endif
                        <span class="badge bg-info">{{ $product->category->name ?? 'Uncategorized' }}</span>
                    </div>
                </div>
                <div class="text-end">
                    <div class="price-display">
                        @if($product->sale_price && $product->sale_price < $product->price)
                            <span class="sale-price">{{ currency($product->sale_price) }}</span>
                            <span class="original-price">{{ currency($product->price) }}</span>
                        @else
                            <span class="text-success">{{ currency($product->price) }}</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($product->short_description)
                <div class="mb-3">
                    <div class="detail-label">Short Description</div>
                    <div class="detail-value">{{ $product->short_description }}</div>
                </div>
            @endif

            @if($product->description)
                <div class="mb-3">
                    <div class="detail-label">Description</div>
                    <div class="detail-value">{{ $product->description }}</div>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="detail-label">SKU</div>
                    <div class="detail-value">{{ $product->sku }}</div>
                </div>
                <div class="col-md-6">
                    <div class="detail-label">Category</div>
                    <div class="detail-value">{{ $product->category->name ?? 'Uncategorized' }}</div>
                </div>
            </div>
        </div>

        <!-- Inventory Information -->
        <div class="detail-card">
            <h5 class="mb-3">
                <i class="bi bi-box-seam me-2"></i>Inventory
            </h5>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="detail-label">Stock Quantity</div>
                    <div class="detail-value">
                        <span class="badge bg-{{ $product->stock_quantity <= 10 ? 'danger' : 'success' }} fs-6">
                            {{ $product->stock_quantity }} units
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="detail-label">Stock Management</div>
                    <div class="detail-value">
                        <span class="badge bg-{{ $product->manage_stock ? 'primary' : 'secondary' }}">
                            {{ $product->manage_stock ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="detail-label">In Stock</div>
                    <div class="detail-value">
                        <span class="badge bg-{{ $product->in_stock ? 'success' : 'danger' }}">
                            {{ $product->in_stock ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Specifications -->
        <div class="detail-card">
            <h5 class="mb-3">
                <i class="bi bi-info-circle me-2"></i>Specifications
            </h5>
            
            <div class="row">
                @if($product->weight)
                <div class="col-md-6">
                    <div class="detail-label">Weight</div>
                    <div class="detail-value">{{ $product->weight }} kg</div>
                </div>
                @endif
                
                @if($product->dimensions)
                <div class="col-md-6">
                    <div class="detail-label">Dimensions</div>
                    <div class="detail-value">{{ $product->dimensions }}</div>
                </div>
                @endif
                
                @if(!$product->weight && !$product->dimensions)
                <div class="col-12">
                    <p class="text-muted">No specifications available</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Product Images -->
        <div class="detail-card">
            <h5 class="mb-3">
                <i class="bi bi-image me-2"></i>Product Images
            </h5>
            
            @if($product->images && count($product->images) > 0)
                <div class="image-gallery">
                    @foreach($product->images as $image)
                        <div>
                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}" class="product-image">
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">No images uploaded</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="detail-card">
            <h5 class="mb-3">
                <i class="bi bi-lightning me-2"></i>Quick Actions
            </h5>
            
            <div class="d-grid gap-2">
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit Product
                </a>
                
                <a href="{{ route('storefront.product', $product->slug) }}" target="_blank" class="btn btn-outline-info">
                    <i class="bi bi-eye me-2"></i>View on Store
                </a>
                
                <button class="btn btn-outline-warning" onclick="toggleFeatured()">
                    <i class="bi bi-star me-2"></i>
                    {{ $product->is_featured ? 'Remove from Featured' : 'Make Featured' }}
                </button>
                
                <button class="btn btn-outline-secondary" onclick="toggleStatus()">
                    <i class="bi bi-power me-2"></i>
                    {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                </button>
                
                <hr>
                
                <button class="btn btn-outline-danger" onclick="deleteProduct()">
                    <i class="bi bi-trash me-2"></i>Delete Product
                </button>
            </div>
        </div>

        <!-- Product Statistics -->
        <div class="detail-card">
            <h5 class="mb-3">
                <i class="bi bi-graph-up me-2"></i>Statistics
            </h5>
            
            <div class="mb-3">
                <div class="detail-label">Created</div>
                <div class="detail-value">
                    {{ $product->created_at->format('M d, Y') }}
                    <small class="text-muted d-block">{{ $product->created_at->diffForHumans() }}</small>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="detail-label">Last Updated</div>
                <div class="detail-value">
                    {{ $product->updated_at->format('M d, Y') }}
                    <small class="text-muted d-block">{{ $product->updated_at->diffForHumans() }}</small>
                </div>
            </div>
            
            <!-- Future: Add views, sales, etc. -->
            <div class="mb-3">
                <div class="detail-label">Total Sales</div>
                <div class="detail-value">
                    <span class="badge bg-info">Coming Soon</span>
                </div>
            </div>
        </div>

        <!-- Related Category -->
        @if($product->category)
        <div class="detail-card">
            <h5 class="mb-3">
                <i class="bi bi-tag me-2"></i>Category
            </h5>
            
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-1">{{ $product->category->name }}</h6>
                    @if($product->category->description)
                        <small class="text-muted">{{ Str::limit($product->category->description, 50) }}</small>
                    @endif
                </div>
                <a href="{{ route('admin.categories.show', $product->category) }}" class="btn btn-sm btn-outline-primary">
                    View
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $product->name }}</strong>?</p>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Product</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFeatured() {
    // In a real app, this would make an AJAX call to toggle featured status
    if (confirm('Toggle featured status for this product?')) {
        // Create a form to submit the toggle
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.products.update", $product) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        
        const featuredField = document.createElement('input');
        featuredField.type = 'hidden';
        featuredField.name = 'is_featured';
        featuredField.value = {{ $product->is_featured ? 'false' : 'true' }};
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(featuredField);
        document.body.appendChild(form);
        form.submit();
    }
}

function toggleStatus() {
    if (confirm('Toggle active status for this product?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.products.update", $product) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        
        const activeField = document.createElement('input');
        activeField.type = 'hidden';
        activeField.name = 'is_active';
        activeField.value = {{ $product->is_active ? 'false' : 'true' }};
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(activeField);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteProduct() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush