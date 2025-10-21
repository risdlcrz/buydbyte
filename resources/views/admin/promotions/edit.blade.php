@extends('layouts.admin')

@section('title', 'Edit Promotion')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">Promotions</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.promotions.show', $promotion) }}">{{ $promotion->title }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Promotion</h4>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.promotions.update', $promotion) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-bullhorn me-2"></i>
                            Promotion Details
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $promotion->title) }}" 
                                   placeholder="Enter promotion title"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Enter promotion description">{{ old('description', $promotion->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-3">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select promotion type</option>
                                <option value="banner" {{ old('type', $promotion->type) === 'banner' ? 'selected' : '' }}>Banner</option>
                                <option value="popup" {{ old('type', $promotion->type) === 'popup' ? 'selected' : '' }}>Popup</option>
                                <option value="discount" {{ old('type', $promotion->type) === 'discount' ? 'selected' : '' }}>Discount</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Banner Image -->
                        @if($promotion->banner_image)
                        <div class="mb-3">
                            <label class="form-label">Current Banner Image</label>
                            <div class="border rounded p-2" style="max-width: 300px;">
                                <img src="{{ asset('storage/' . $promotion->banner_image) }}" 
                                     alt="Current Banner" 
                                     class="img-fluid rounded">
                            </div>
                        </div>
                        @endif

                        <!-- Banner Image -->
                        <div class="mb-3">
                            <label for="banner_image" class="form-label">{{ $promotion->banner_image ? 'Replace' : 'Upload' }} Banner Image</label>
                            <input type="file" 
                                   class="form-control @error('banner_image') is-invalid @enderror" 
                                   id="banner_image" 
                                   name="banner_image" 
                                   accept="image/*">
                            <div class="form-text">Upload an image (JPEG, PNG, JPG, WebP). Max size: 2MB</div>
                            @error('banner_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Color Settings -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="background_color" class="form-label">Background Color <span class="text-danger">*</span></label>
                                    <input type="color" 
                                           class="form-control form-control-color @error('background_color') is-invalid @enderror" 
                                           id="background_color" 
                                           name="background_color" 
                                           value="{{ old('background_color', $promotion->background_color) }}" 
                                           required>
                                    @error('background_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="text_color" class="form-label">Text Color <span class="text-danger">*</span></label>
                                    <input type="color" 
                                           class="form-control form-control-color @error('text_color') is-invalid @enderror" 
                                           id="text_color" 
                                           name="text_color" 
                                           value="{{ old('text_color', $promotion->text_color) }}" 
                                           required>
                                    @error('text_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="button_color" class="form-label">Button Color <span class="text-danger">*</span></label>
                                    <input type="color" 
                                           class="form-control form-control-color @error('button_color') is-invalid @enderror" 
                                           id="button_color" 
                                           name="button_color" 
                                           value="{{ old('button_color', $promotion->button_color) }}" 
                                           required>
                                    @error('button_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Button Settings -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="button_text" class="form-label">Button Text</label>
                                    <input type="text" 
                                           class="form-control @error('button_text') is-invalid @enderror" 
                                           id="button_text" 
                                           name="button_text" 
                                           value="{{ old('button_text', $promotion->button_text) }}" 
                                           placeholder="e.g., Shop Now, Learn More">
                                    @error('button_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="button_link" class="form-label">Button Link</label>
                                    <input type="url" 
                                           class="form-control @error('button_link') is-invalid @enderror" 
                                           id="button_link" 
                                           name="button_link" 
                                           value="{{ old('button_link', $promotion->button_link) }}" 
                                           placeholder="https://example.com">
                                    @error('button_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Discount Settings -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-percent me-2"></i>
                            Discount Settings
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="discount_percentage" class="form-label">Discount Percentage (%)</label>
                                    <input type="number" 
                                           class="form-control @error('discount_percentage') is-invalid @enderror" 
                                           id="discount_percentage" 
                                           name="discount_percentage" 
                                           value="{{ old('discount_percentage', $promotion->discount_percentage) }}" 
                                           min="0" 
                                           max="100" 
                                           step="0.01"
                                           placeholder="e.g., 10">
                                    @error('discount_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="discount_amount" class="form-label">Discount Amount ($)</label>
                                    <input type="number" 
                                           class="form-control @error('discount_amount') is-invalid @enderror" 
                                           id="discount_amount" 
                                           name="discount_amount" 
                                           value="{{ old('discount_amount', $promotion->discount_amount) }}" 
                                           min="0" 
                                           step="0.01"
                                           placeholder="e.g., 25.00">
                                    @error('discount_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="discount_code" class="form-label">Discount Code</label>
                                    <input type="text" 
                                           class="form-control @error('discount_code') is-invalid @enderror" 
                                           id="discount_code" 
                                           name="discount_code" 
                                           value="{{ old('discount_code', $promotion->discount_code) }}" 
                                           placeholder="e.g., SAVE20"
                                           style="text-transform: uppercase;">
                                    @error('discount_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-text">
                            <i class="mdi mdi-information me-1"></i>
                            You can set either a percentage discount or a fixed amount discount, not both.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Schedule -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-calendar me-2"></i>
                            Schedule
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date', $promotion->start_date->format('Y-m-d\TH:i')) }}" 
                                   required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ old('end_date', $promotion->end_date->format('Y-m-d\TH:i')) }}" 
                                   required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-cog me-2"></i>
                            Settings
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" 
                                   class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" 
                                   name="sort_order" 
                                   value="{{ old('sort_order', $promotion->sort_order) }}" 
                                   min="0">
                            <div class="form-text">Lower numbers appear first</div>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="target_audience" class="form-label">Target Audience <span class="text-danger">*</span></label>
                            <select class="form-select @error('target_audience') is-invalid @enderror" id="target_audience" name="target_audience" required>
                                <option value="">Select target audience</option>
                                <option value="all" {{ old('target_audience', $promotion->target_audience) === 'all' ? 'selected' : '' }}>All Users</option>
                                <option value="new_users" {{ old('target_audience', $promotion->target_audience) === 'new_users' ? 'selected' : '' }}>New Users</option>
                                <option value="returning_users" {{ old('target_audience', $promotion->target_audience) === 'returning_users' ? 'selected' : '' }}>Returning Users</option>
                            </select>
                            @error('target_audience')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Display Pages</label>
                            @php
                                $selectedPages = old('display_pages', $promotion->display_pages ?: []);
                            @endphp
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="display_pages[]" value="all" id="page_all" {{ in_array('all', $selectedPages) ? 'checked' : '' }}>
                                <label class="form-check-label" for="page_all">All Pages</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="display_pages[]" value="homepage" id="page_homepage" {{ in_array('homepage', $selectedPages) ? 'checked' : '' }}>
                                <label class="form-check-label" for="page_homepage">Homepage</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="display_pages[]" value="products" id="page_products" {{ in_array('products', $selectedPages) ? 'checked' : '' }}>
                                <label class="form-check-label" for="page_products">Products Page</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="display_pages[]" value="categories" id="page_categories" {{ in_array('categories', $selectedPages) ? 'checked' : '' }}>
                                <label class="form-check-label" for="page_categories">Categories Page</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="display_pages[]" value="product_detail" id="page_product_detail" {{ in_array('product_detail', $selectedPages) ? 'checked' : '' }}>
                                <label class="form-check-label" for="page_product_detail">Product Detail Pages</label>
                            </div>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <strong>Active</strong>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="mdi mdi-check me-1"></i>
                            Update Promotion
                        </button>
                        <a href="{{ route('admin.promotions.show', $promotion) }}" class="btn btn-outline-secondary btn-lg ms-2">
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-uppercase discount code
    const discountCodeInput = document.getElementById('discount_code');
    if (discountCodeInput) {
        discountCodeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }

    // Handle "All Pages" checkbox
    const allPagesCheck = document.getElementById('page_all');
    const otherPageChecks = document.querySelectorAll('input[name="display_pages[]"]:not(#page_all)');
    
    if (allPagesCheck) {
        allPagesCheck.addEventListener('change', function() {
            if (this.checked) {
                otherPageChecks.forEach(check => {
                    check.checked = false;
                    check.disabled = true;
                });
            } else {
                otherPageChecks.forEach(check => {
                    check.disabled = false;
                });
            }
        });

        // If "All Pages" is initially checked, disable others
        if (allPagesCheck.checked) {
            otherPageChecks.forEach(check => {
                check.disabled = true;
            });
        }
    }

    // Prevent selecting both percentage and amount discounts
    const percentageInput = document.getElementById('discount_percentage');
    const amountInput = document.getElementById('discount_amount');
    
    if (percentageInput && amountInput) {
        percentageInput.addEventListener('input', function() {
            if (this.value) {
                amountInput.disabled = true;
                amountInput.value = '';
            } else {
                amountInput.disabled = false;
            }
        });

        amountInput.addEventListener('input', function() {
            if (this.value) {
                percentageInput.disabled = true;
                percentageInput.value = '';
            } else {
                percentageInput.disabled = false;
            }
        });

        // Check initial state
        if (percentageInput.value) {
            amountInput.disabled = true;
        } else if (amountInput.value) {
            percentageInput.disabled = true;
        }
    }
});
</script>
@endpush
@endsection