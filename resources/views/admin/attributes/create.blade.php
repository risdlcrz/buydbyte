@extends('layouts.admin')

@section('title', 'Create Attribute Definition')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Create Attribute Definition</h1>
                <a href="{{ route('admin.attributes.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <form method="POST" action="{{ route('admin.attributes.store') }}">
                        @csrf

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
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            <small class="form-text text-muted">Used for internal identification (lowercase, underscores)</small>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                                   id="display_name" name="display_name" value="{{ old('display_name') }}" required>
                                            <small class="form-text text-muted">User-friendly name shown in interface</small>
                                            @error('display_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
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
                                                <option value="">Select Data Type</option>
                                                @foreach($dataTypes as $type)
                                                    <option value="{{ $type }}" {{ old('data_type') == $type ? 'selected' : '' }}>
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
                                                   id="unit" name="unit" value="{{ old('unit') }}" 
                                                   placeholder="e.g., MHz, GB, W">
                                            <small class="form-text text-muted">Unit of measurement (optional)</small>
                                            @error('unit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Possible Values (for select type) -->
                                <div class="mb-3" id="possible-values-section" style="display: none;">
                                    <label for="possible_values" class="form-label">Possible Values</label>
                                    <div id="possible-values-container">
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="possible_values[]" placeholder="Enter option">
                                            <button type="button" class="btn btn-outline-danger remove-value" onclick="removeValue(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
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
                                                <option value="">Select Group</option>
                                                @foreach($groups as $group)
                                                    <option value="{{ $group }}" {{ old('attribute_group') == $group ? 'selected' : '' }}>
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
                                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 100) }}" 
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
                                        @foreach($categories as $category)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input @error('applicable_categories') is-invalid @enderror" 
                                                           type="checkbox" value="{{ $category }}" 
                                                           id="category_{{ $category }}" name="applicable_categories[]"
                                                           {{ (is_array(old('applicable_categories')) && in_array($category, old('applicable_categories'))) ? 'checked' : '' }}>
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
                                                   {{ old('is_filterable') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_filterable">
                                                Filterable
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" 
                                                   id="is_comparable" name="is_comparable"
                                                   {{ old('is_comparable', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_comparable">
                                                Comparable
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" 
                                                   id="is_required" name="is_required"
                                                   {{ old('is_required') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_required">
                                                Required
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" 
                                                   id="is_active" name="is_active"
                                                   {{ old('is_active', true) ? 'checked' : '' }}>
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
                                <i class="bi bi-check-circle"></i> Create Attribute Definition
                            </button>
                            <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Help</h6>
                        </div>
                        <div class="card-body">
                            <h6>Data Types:</h6>
                            <ul class="small">
                                <li><strong>Text:</strong> Free-form text input</li>
                                <li><strong>Number:</strong> Integer values</li>
                                <li><strong>Decimal:</strong> Decimal values</li>
                                <li><strong>Boolean:</strong> Yes/No values</li>
                                <li><strong>Select:</strong> Dropdown with predefined options</li>
                            </ul>

                            <h6 class="mt-3">Groups:</h6>
                            <ul class="small">
                                <li><strong>General:</strong> Basic info (Brand, Model)</li>
                                <li><strong>Performance:</strong> Speed, cores, etc.</li>
                                <li><strong>Physical:</strong> Size, weight, etc.</li>
                                <li><strong>Compatibility:</strong> Sockets, interfaces</li>
                                <li><strong>Connectivity:</strong> Ports, wireless</li>
                                <li><strong>Cooling:</strong> Thermal specs</li>
                            </ul>
                        </div>
                    </div>
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

// Auto-generate slug from name
document.getElementById('display_name').addEventListener('input', function() {
    const nameField = document.getElementById('name');
    if (!nameField.value) {
        nameField.value = this.value.toLowerCase()
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, '_');
    }
});
</script>
@endsection