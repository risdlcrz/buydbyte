@extends('layouts.admin')

@section('title', 'Edit Product Attributes - ' . $product->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Edit Product Attributes</h1>
                <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <form method="POST" action="{{ route('admin.product-attributes.update', $product) }}">
                        @csrf
                        @method('PUT')

                        <!-- Product Info -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Product Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        @if($product->main_image)
                                            <img src="{{ asset('storage/' . $product->main_image) }}" 
                                                 alt="{{ $product->name }}" class="img-thumbnail">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center img-thumbnail"
                                                 style="height: 100px;">
                                                <i class="bi bi-image text-muted display-6"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-10">
                                        <h4>{{ $product->name }}</h4>
                                        <p class="text-muted mb-2">
                                            @if($product->brand)<strong>Brand:</strong> {{ $product->brand }} @endif
                                            @if($product->model)<strong>Model:</strong> {{ $product->model }} @endif
                                        </p>
                                        <p class="text-muted mb-2">
                                            <strong>Category:</strong> 
                                            @if($product->category)
                                                {{ $product->category->name }}
                                            @else
                                                <span class="text-danger">No category assigned</span>
                                            @endif
                                        </p>
                                        <p class="text-muted">
                                            <strong>SKU:</strong> {{ $product->sku }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($availableAttributes->count() > 0)
                            <!-- Attributes by Group -->
                            @php
                                $attributeGroups = $availableAttributes->groupBy('attribute_group');
                            @endphp

                            @foreach($attributeGroups as $groupName => $attributes)
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            {{ ucwords(str_replace('_', ' ', $groupName)) }} Attributes
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($attributes as $attribute)
                                                @php
                                                    $currentValue = $currentAttributes->get($attribute->attribute_id);
                                                    $fieldName = "attributes.{$attribute->attribute_id}";
                                                @endphp
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="attr_{{ $attribute->attribute_id }}" class="form-label">
                                                        {{ $attribute->display_name }}
                                                        @if($attribute->is_required)
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                        @if($attribute->unit)
                                                            <small class="text-muted">({{ $attribute->unit }})</small>
                                                        @endif
                                                    </label>
                                                    
                                                    @if($attribute->description)
                                                        <small class="form-text text-muted d-block mb-2">{{ $attribute->description }}</small>
                                                    @endif

                                                    @switch($attribute->data_type)
                                                        @case('select')
                                                            <select class="form-select @error($fieldName) is-invalid @enderror" 
                                                                    id="attr_{{ $attribute->attribute_id }}" 
                                                                    name="{{ $fieldName }}"
                                                                    {{ $attribute->is_required ? 'required' : '' }}>
                                                                <option value="">Select {{ $attribute->display_name }}</option>
                                                                @if($attribute->possible_values)
                                                                    @foreach($attribute->possible_values as $value)
                                                                        <option value="{{ $value }}" 
                                                                            {{ old($fieldName, $currentValue?->value) == $value ? 'selected' : '' }}>
                                                                            {{ $value }}
                                                                        </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            @break

                                                        @case('boolean')
                                                            <select class="form-select @error($fieldName) is-invalid @enderror" 
                                                                    id="attr_{{ $attribute->attribute_id }}" 
                                                                    name="{{ $fieldName }}">
                                                                <option value="">Select</option>
                                                                <option value="1" {{ old($fieldName, $currentValue?->value) == '1' ? 'selected' : '' }}>Yes</option>
                                                                <option value="0" {{ old($fieldName, $currentValue?->value) == '0' ? 'selected' : '' }}>No</option>
                                                            </select>
                                                            @break

                                                        @case('number')
                                                        @case('decimal')
                                                            <input type="number" 
                                                                   class="form-control @error($fieldName) is-invalid @enderror" 
                                                                   id="attr_{{ $attribute->attribute_id }}" 
                                                                   name="{{ $fieldName }}"
                                                                   value="{{ old($fieldName, $currentValue?->value) }}"
                                                                   {{ $attribute->data_type == 'decimal' ? 'step=0.01' : '' }}
                                                                   {{ $attribute->is_required ? 'required' : '' }}>
                                                            @break

                                                        @default
                                                            <input type="text" 
                                                                   class="form-control @error($fieldName) is-invalid @enderror" 
                                                                   id="attr_{{ $attribute->attribute_id }}" 
                                                                   name="{{ $fieldName }}"
                                                                   value="{{ old($fieldName, $currentValue?->value) }}"
                                                                   {{ $attribute->is_required ? 'required' : '' }}>
                                                    @endswitch

                                                    @error($fieldName)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror

                                                    @if($currentValue)
                                                        <small class="text-success">
                                                            <i class="bi bi-check-circle"></i> Current: {{ $attribute->formatValue($currentValue->value) }}
                                                        </small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Update Product Attributes
                                </button>
                                <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        @else
                            <div class="card">
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-info-circle display-1 text-muted"></i>
                                    <h4>No Attributes Available</h4>
                                    <p class="text-muted">
                                        No attribute definitions are available for this product's category
                                        @if($product->category)
                                            ({{ $product->category->name }}).
                                        @else
                                            (No category assigned).
                                        @endif
                                    </p>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> Create Attribute Definition
                                        </a>
                                        @if(!$product->category)
                                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Assign Category
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>

                <div class="col-lg-4">
                    <!-- Current Attributes Summary -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Current Attributes Summary</h6>
                        </div>
                        <div class="card-body">
                            @if($currentAttributes->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($currentAttributes as $attr)
                                        <div class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ $attr->attributeDefinition->display_name }}</h6>
                                                    <p class="mb-0 text-muted">{{ $attr->attributeDefinition->formatValue($attr->value) }}</p>
                                                </div>
                                                <small class="text-muted">{{ ucwords($attr->attributeDefinition->attribute_group) }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No attributes set for this product.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pencil"></i> Edit Product Details
                                </a>
                                <a href="{{ route('storefront.product', $product) }}" class="btn btn-outline-success btn-sm" target="_blank">
                                    <i class="bi bi-eye"></i> View on Storefront
                                </a>
                                @if($product->category)
                                    <a href="{{ route('admin.product-attributes.index', ['category' => $product->category->slug]) }}" 
                                       class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-collection"></i> Other {{ $product->category->name }} Products
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Help -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Tips</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small">
                                <li><i class="bi bi-lightbulb text-warning"></i> Required attributes are marked with <span class="text-danger">*</span></li>
                                <li><i class="bi bi-lightbulb text-warning"></i> Numeric values will be formatted with their units</li>
                                <li><i class="bi bi-lightbulb text-warning"></i> Empty values will remove the attribute from the product</li>
                                <li><i class="bi bi-lightbulb text-warning"></i> Changes take effect immediately after saving</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-save draft functionality could go here
// Form validation enhancements
// Attribute suggestions/autocomplete
</script>
@endsection