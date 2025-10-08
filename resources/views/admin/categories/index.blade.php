@extends('layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Category Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus"></i> Add New Category
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.categories.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Name, description...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary flex-fill">Filter</button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Clear</a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="card-body">
        @if($categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Products</th>
                            <th>Parent</th>
                            <th>Sort Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($category->image)
                                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" 
                                             class="me-3 rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light me-3 rounded d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="bi bi-tag text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $category->name }}</div>
                                        @if($category->description)
                                            <small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $category->products_count }} products</span>
                            </td>
                            <td>
                                @if($category->parent)
                                    <span class="badge bg-secondary">{{ $category->parent->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $category->sort_order }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-outline-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($category->products_count == 0)
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                                              style="display: inline-block;"
                                              onsubmit="return confirm('Are you sure you want to delete this category?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-outline-danger" disabled title="Cannot delete category with products">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $categories->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-tags display-4 text-muted"></i>
                <h4 class="mt-3">No categories found</h4>
                <p class="text-muted">Try adjusting your search criteria or add a new category.</p>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Add First Category
                </a>
            </div>
        @endif
    </div>
</div>
@endsection