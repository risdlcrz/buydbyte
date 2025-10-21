@extends('layouts.admin')

@section('title', 'Attribute Definition - ' . $attribute->display_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Attribute Definition Details</h1>
                <div>
                    <a href="{{ route('admin.attributes.edit', $attribute) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('admin.attributes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Name:</dt>
                                        <dd class="col-sm-8"><code>{{ $attribute->name }}</code></dd>
                                        
                                        <dt class="col-sm-4">Display Name:</dt>
                                        <dd class="col-sm-8">{{ $attribute->display_name }}</dd>
                                        
                                        <dt class="col-sm-4">Data Type:</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge bg-info">{{ ucfirst($attribute->data_type) }}</span>
                                        </dd>
                                        
                                        <dt class="col-sm-4">Unit:</dt>
                                        <dd class="col-sm-8">{{ $attribute->unit ?: 'None' }}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Group:</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge bg-secondary">
                                                {{ ucwords(str_replace('_', ' ', $attribute->attribute_group)) }}
                                            </span>
                                        </dd>
                                        
                                        <dt class="col-sm-4">Sort Order:</dt>
                                        <dd class="col-sm-8">{{ $attribute->sort_order }}</dd>
                                        
                                        <dt class="col-sm-4">Status:</dt>
                                        <dd class="col-sm-8">
                                            @if($attribute->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </dd>
                                        
                                        <dt class="col-sm-4">Created:</dt>
                                        <dd class="col-sm-8">{{ $attribute->created_at->format('M j, Y g:i A') }}</dd>
                                    </dl>
                                </div>
                            </div>
                            
                            @if($attribute->description)
                                <div class="mt-3">
                                    <strong>Description:</strong>
                                    <p class="text-muted">{{ $attribute->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Configuration -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Configuration</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Applicable Categories</h6>
                                    @if($attribute->applicable_categories)
                                        @foreach($attribute->applicable_categories as $category)
                                            <span class="badge bg-light text-dark me-1">{{ $category }}</span>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6>Properties</h6>
                                    @if($attribute->is_filterable)
                                        <span class="badge bg-primary me-1">Filterable</span>
                                    @endif
                                    @if($attribute->is_comparable)
                                        <span class="badge bg-success me-1">Comparable</span>
                                    @endif
                                    @if($attribute->is_required)
                                        <span class="badge bg-warning me-1">Required</span>
                                    @endif
                                </div>
                            </div>

                            @if($attribute->data_type === 'select' && $attribute->possible_values)
                                <div class="mt-3">
                                    <h6>Possible Values</h6>
                                    <div>
                                        @foreach($attribute->possible_values as $value)
                                            <span class="badge bg-outline-primary me-1">{{ $value }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Usage Examples -->
                    @if($attribute->productAttributes->count() > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Product Values</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Value</th>
                                                <th>Formatted</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($attribute->productAttributes->take(10) as $productAttr)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.product-attributes.edit', $productAttr->product) }}">
                                                            {{ $productAttr->product->name }}
                                                        </a>
                                                    </td>
                                                    <td><code>{{ $productAttr->value }}</code></td>
                                                    <td>{{ $attribute->formatValue($productAttr->value) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <!-- Statistics -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Usage Statistics</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <div class="display-4 text-primary">{{ $attribute->productAttributes->count() }}</div>
                                <p class="text-muted">Products using this attribute</p>
                            </div>
                            
                            @if($attribute->productAttributes->count() > 0)
                                <hr>
                                <div class="small">
                                    <div class="d-flex justify-content-between">
                                        <span>Most recent update:</span>
                                        <span>{{ $attribute->productAttributes->sortByDesc('updated_at')->first()?->updated_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Unique values:</span>
                                        <span>{{ $attribute->productAttributes->unique('value')->count() }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.attributes.edit', $attribute) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil"></i> Edit Attribute
                                </a>
                                
                                <form method="POST" action="{{ route('admin.attributes.toggle-status', $attribute) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-{{ $attribute->is_active ? 'warning' : 'success' }} btn-sm w-100">
                                        <i class="bi bi-{{ $attribute->is_active ? 'pause' : 'play' }}"></i>
                                        {{ $attribute->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                
                                @if($attribute->productAttributes->count() === 0)
                                    <form method="POST" action="{{ route('admin.attributes.destroy', $attribute) }}" 
                                          onsubmit="return confirm('Are you sure you want to delete this attribute definition?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm w-100">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-collection"></i> Manage Product Attributes
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($attribute->productAttributes->count() > 0)
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle"></i>
                            <strong>Note:</strong> This attribute is being used by products and cannot be deleted. 
                            You can deactivate it to hide it from new product forms.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection