@extends('layouts.admin')

@section('title', 'Edit Category')
@section('page-title', 'Edit Category')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.show', $category) }}">{{ $category->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Category: {{ $category->name }}</h5>
            </div>
            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                               id="slug" name="slug" value="{{ old('slug', $category->slug) }}" 
                               placeholder="auto-generated if left blank">
                        <div class="form-text">URL-friendly version of the name. Leave blank to auto-generate.</div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Parent Category</label>
                        <select class="form-select @error('parent_id') is-invalid @enderror" 
                                id="parent_id" name="parent_id">
                            <option value="">None (Top Level Category)</option>
                            @foreach($parentCategories as $cat)
                                @if($cat->category_id !== $category->category_id)
                                    <option value="{{ $cat->category_id }}" 
                                        {{ old('parent_id', $category->parent_id) === $cat->category_id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" 
                                   value="{{ old('sort_order', $category->sort_order) }}" min="0">
                            <div class="form-text">Categories with lower numbers appear first.</div>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Update Category
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9 -]/g, '') // Remove special characters
        .replace(/\s+/g, '-')        // Replace spaces with hyphens
        .replace(/-+/g, '-')         // Replace multiple hyphens with single
        .trim('-');                  // Remove leading/trailing hyphens
    
    // Only update if slug field is empty or matches the original
    const slugField = document.getElementById('slug');
    if (!slugField.value || slugField.value === '{{ $category->slug }}') {
        slugField.value = slug;
    }
});
</script>
@endsection