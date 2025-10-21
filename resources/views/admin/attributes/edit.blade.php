@extends('layouts.admin')

@section('title', 'Edit Attribute Definition')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Edit Attribute Definition</h1>
                <div>
                    <a href="{{ route('admin.attributes.show', $attribute) }}" class="btn btn-outline-info">
                        <i class="bi bi-eye"></i> View
                    </a>
                    <a href="{{ route('admin.attributes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <form method="POST" action="{{ route('admin.attributes.update', $attribute) }}">
                        @csrf
                        @method('PUT')

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Basic Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $attribute->name) }}" required>
                                            <small class="form-text text-muted">Used for internal identification</small>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                                   id="display_name" name="display_name" value="{{ old('display_name', $attribute->display_name) }}" required>
                                            <small class="form-text text-muted">User-friendly name</small>
                                            @error('display_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description', $attribute->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="data_type" class="form-label">Data Type <span class="text-danger">*</span></label>
                                            <select class="form-select @error('data_type') is-invalid @enderror" 
                                                    id="data_type" name="data_type" required>
                                                @foreach($dataTypes as $type)
                                                    <option value="{{ $type }}" 
                                                        {{ old('data_type', $attribute->data_type) == $type ? 'selected' : '' }}>
                                                        {{ ucfirst($type) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('data_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="unit" class="form-label">Unit</label>
                                            <input type="text" class="form-control @error('unit') is-invalid @enderror" 
                                                   id="unit" name="unit" value="{{ old('unit', $attribute->unit) }}" 
                                                   placeholder="e.g., MHz, GB, W">
                                            @error('unit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Possible Values (for select type) -->
                                <div class="mb-3" id="possible-values-section" 
                                     style="display: {{ old('data_type', $attribute->data_type) === 'select' ? 'block' : 'none' }}">
                                    <label for="possible_values" class="form-label">Possible Values</label>
                                    <div id="possible-values-container">
                                        @php
                                            $possibleValues = old('possible_values', $attribute->possible_values ?? []);
                                        @endphp
                                        @if(is_array($possibleValues) && count($possibleValues) > 0)
                                            @foreach($possibleValues as $value)
                                                <div class="input-group mb-2">
                                                    <input type="text" class="form-control" name="possible_values[]" 
                                                           value="{{ $value }}" placeholder="Enter option">
                                                    <button type="button" class="btn btn-outline-danger remove-value" onclick="removeValue(this)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" name="possible_values[]" placeholder="Enter option">
                                                <button type="button" class="btn btn-outline-danger remove-value" onclick="removeValue(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addPossibleValue()">
                                        <i class="bi bi-plus"></i> Add Option
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Configuration</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="attribute_group" class="form-label">Group <span class="text-danger">*</span></label>
                                            <select class="form-select @error('attribute_group') is-invalid @enderror" 
                                                    id="attribute_group" name="attribute_group" required>
                                                @foreach($groups as $group)
                                                    <option value="{{ $group }}" 
                                                        {{ old('attribute_group', $attribute->attribute_group) == $group ? 'selected' : '' }}>
                                                        {{ ucwords(str_replace('_', ' ', $group)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('attribute_group')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sort_order" class="form-label">Sort Order</label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $attribute->sort_order) }}" 
                                                   min="0" max="9999">
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Applicable Categories <span class="text-danger">*</span></label>
                                    <div class="row">
                                        @php
                                            $selectedCategories = old('applicable_categories', $attribute->applicable_categories ?? []);
                                        @endphp
                                        @foreach($categories as $category)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input @error('applicable_categories') is-invalid @enderror" 
                                                           type="checkbox" value="{{ $category }}" 
                                                           id="category_{{ $category }}" name="applicable_categories[]"
                                                           {{ (is_array($selectedCategories) && in_array($category, $selectedCategories)) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="category_{{ $category }}">
                                                        {{ ucwords(str_replace(['_', '-'], ' ', $category)) }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('applicable_categories')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" 
                                                   id="is_filterable" name="is_filterable"
                                                   {{ old('is_filterable', $attribute->is_filterable) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_filterable">
                                                Filterable
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" 
                                                   id="is_comparable" name="is_comparable"
                                                   {{ old('is_comparable', $attribute->is_comparable) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_comparable">
                                                Comparable
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" 
                                                   id="is_required" name="is_required"
                                                   {{ old('is_required', $attribute->is_required) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_required">
                                                Required
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" 
                                                   id="is_active" name="is_active"
                                                   {{ old('is_active', $attribute->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Active
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Attribute Definition
                            </button>
                            <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Usage Statistics</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Products using this attribute:</strong> {{ $attribute->productAttributes->count() }}</p>
                            <p><strong>Created:</strong> {{ $attribute->created_at->format('M j, Y g:i A') }}</p>
                            <p><strong>Last Updated:</strong> {{ $attribute->updated_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>

                    @if($attribute->productAttributes->count() > 0)
                    <div class="alert alert-warning mt-3">
                        <strong>Note:</strong> This attribute is currently being used by {{ $attribute->productAttributes->count() }} products. 
                        Changing the data type or removing possible values may affect existing data.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('data_type').addEventListener('change', function() {
    const possibleValuesSection = document.getElementById('possible-values-section');
    if (this.value === 'select') {
        possibleValuesSection.style.display = 'block';
    } else {
        possibleValuesSection.style.display = 'none';
    }
});

function addPossibleValue() {
    const container = document.getElementById('possible-values-container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control" name="possible_values[]" placeholder="Enter option">
        <button type="button" class="btn btn-outline-danger remove-value" onclick="removeValue(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

function removeValue(button) {
    button.closest('.input-group').remove();
}
</script>
@endsection