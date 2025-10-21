@extends('layouts.admin')

@section('title', 'Manage Product Attributes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Manage Product Attributes</h1>
                <div>
                    <a href="{{ route('admin.attributes.index') }}" class="btn btn-outline-info">
                        <i class="bi bi-gear"></i> Manage Attribute Definitions
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label for="search" class="form-label">Search Products</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search by name, brand, or model">
                        </div>
                        <div class="col-md-4">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                            <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card">
                <div class="card-body">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 60px">
                                            <input type="checkbox" id="select-all" class="form-check-input">
                                        </th>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Attributes Count</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input product-checkbox" 
                                                       value="{{ $product->product_id }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($product->main_image)
                                                        <img src="{{ asset('storage/' . $product->main_image) }}" 
                                                             alt="{{ $product->name }}" class="me-2" 
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light me-2 d-flex align-items-center justify-content-center"
                                                             style="width: 40px; height: 40px;">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $product->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            @if($product->brand){{ $product->brand }}@endif
                                                            @if($product->brand && $product->model) - @endif
                                                            @if($product->model){{ $product->model }}@endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($product->category)
                                                    <span class="badge bg-secondary">{{ $product->category->name }}</span>
                                                @else
                                                    <span class="text-muted">No category</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $product->attributes->count() }}</span>
                                                @if($product->attributes->count() > 0)
                                                    <small class="text-muted d-block">
                                                        {{ $product->attributes->take(3)->pluck('attributeDefinition.display_name')->join(', ') }}
                                                        @if($product->attributes->count() > 3)
                                                            <span>+{{ $product->attributes->count() - 3 }} more</span>
                                                        @endif
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.product-attributes.edit', $product) }}" 
                                                       class="btn btn-outline-primary" title="Edit Attributes">
                                                        <i class="bi bi-pencil"></i> Edit Attributes
                                                    </a>
                                                    <a href="{{ route('admin.products.show', $product) }}" 
                                                       class="btn btn-outline-secondary" title="View Product">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $products->appends(request()->query())->links() }}
                        </div>

                        <!-- Bulk Actions -->
                        <div class="card mt-3" id="bulk-actions" style="display: none;">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Bulk Actions</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.product-attributes.bulk-edit') }}" id="bulk-edit-form">
                                    @csrf
                                    <input type="hidden" name="product_ids" id="selected-products" value="">
                                    
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="bulk_attribute_id" class="form-label">Attribute</label>
                                            <select class="form-select" id="bulk_attribute_id" name="attribute_id" required>
                                                <option value="">Select Attribute</option>
                                                @foreach(\App\Models\AttributeDefinition::where('is_active', true)->orderBy('display_name')->get() as $attr)
                                                    <option value="{{ $attr->attribute_id }}" data-type="{{ $attr->data_type }}">
                                                        {{ $attr->display_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="bulk_value" class="form-label">Value</label>
                                            <input type="text" class="form-control" id="bulk_value" name="value" 
                                                   placeholder="Enter value for selected products" required>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary">Apply to <span id="selected-count">0</span> products</button>
                                        </div>
                                    </div>
                                </form>

                                <!-- Copy Attributes Form -->
                                <hr>
                                <form method="POST" action="{{ route('admin.product-attributes.copy') }}" id="copy-form">
                                    @csrf
                                    <input type="hidden" name="target_product_ids" id="copy-target-products" value="">
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="source_product_id" class="form-label">Copy from Product</label>
                                            <select class="form-select" id="source_product_id" name="source_product_id" required>
                                                <option value="">Select source product</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->product_id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Copy Options</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="copy_all" checked>
                                                <label class="form-check-label" for="copy_all">
                                                    Copy all attributes
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-success">Copy to <span id="copy-count">0</span> products</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-box display-1 text-muted"></i>
                            <h4>No Products Found</h4>
                            <p class="text-muted">No products match your current filters.</p>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Create New Product
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    const copyCount = document.getElementById('copy-count');

    function updateBulkActions() {
        const selected = Array.from(productCheckboxes).filter(cb => cb.checked);
        const count = selected.length;
        
        if (count > 0) {
            bulkActions.style.display = 'block';
            selectedCount.textContent = count;
            copyCount.textContent = count;
            
            // Update hidden inputs
            const selectedIds = selected.map(cb => cb.value);
            document.getElementById('selected-products').value = JSON.stringify(selectedIds);
            document.getElementById('copy-target-products').value = JSON.stringify(selectedIds);
        } else {
            bulkActions.style.display = 'none';
        }
    }

    selectAll.addEventListener('change', function() {
        productCheckboxes.forEach(cb => {
            cb.checked = this.checked;
        });
        updateBulkActions();
    });

    productCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkActions);
    });

    // Handle bulk edit form submission
    document.getElementById('bulk-edit-form').addEventListener('submit', function(e) {
        if (!confirm('Are you sure you want to update the selected attribute for all selected products?')) {
            e.preventDefault();
        }
    });

    // Handle copy form submission
    document.getElementById('copy-form').addEventListener('submit', function(e) {
        if (!confirm('Are you sure you want to copy attributes to the selected products? This will overwrite existing values.')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection