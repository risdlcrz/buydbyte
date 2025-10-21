@extends('layouts.admin')

@section('title', 'Category Details')
@section('page-title', 'Category Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
    <li class="breadcrumb-item active">{{ $category->name }}</li>
@endsection

@section('content')
<div class="row">
    <!-- Category Information -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $category->name }}</h5>
                    <div>
                        <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($category->description)
                    <div class="mb-3">
                        <h6>Description</h6>
                        <p>{{ $category->description }}</p>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <h6>Slug</h6>
                        <p><code>{{ $category->slug }}</code></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Sort Order</h6>
                        <p>{{ $category->sort_order }}</p>
                    </div>
                </div>

                @if($category->parent)
                    <div class="mb-3">
                        <h6>Parent Category</h6>
                        <a href="{{ route('admin.categories.show', $category->parent) }}" class="text-decoration-none">
                            {{ $category->parent->name }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit Category
                </a>
                <a href="{{ route('storefront.category', $category->slug) }}" target="_blank" class="btn btn-outline-info">
                    <i class="bi bi-eye"></i> View on Store
                </a>
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                      onsubmit="return confirm('Are you sure you want to delete this category?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash"></i> Delete Category
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Statistics</h5>
            </div>
            <div class="card-body">
                <p><strong>Created:</strong> {{ $category->created_at->format('M d, Y') }}</p>
                <p><strong>Updated:</strong> {{ $category->updated_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection