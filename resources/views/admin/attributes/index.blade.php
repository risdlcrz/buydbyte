@extends('layouts.admin')

@section('title', 'Attribute Definitions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Attribute Definitions</h1>
                <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create New Attribute
                </a>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search by name or description">
                        </div>
                        <div class="col-md-3">
                            <label for="group" class="form-label">Group</label>
                            <select class="form-select" id="group" name="group">
                                <option value="">All Groups</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $group)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                            <a href="{{ route('admin.attributes.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Attributes Table -->
            <div class="card">
                <div class="card-body">
                    @if($attributes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Display Name</th>
                                        <th>Type</th>
                                        <th>Group</th>
                                        <th>Categories</th>
                                        <th>Status</th>
                                        <th>Usage</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attributes as $attribute)
                                        <tr>
                                            <td>
                                                <strong>{{ $attribute->name }}</strong>
                                                @if($attribute->unit)
                                                    <small class="text-muted">({{ $attribute->unit }})</small>
                                                @endif
                                            </td>
                                            <td>{{ $attribute->display_name }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $attribute->data_type }}</span>
                                                @if($attribute->is_required)
                                                    <span class="badge bg-warning">Required</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ ucwords(str_replace('_', ' ', $attribute->attribute_group)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @foreach($attribute->applicable_categories as $category)
                                                    <span class="badge bg-light text-dark me-1">{{ $category }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($attribute->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $attribute->productAttributes->count() }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.attributes.show', $attribute) }}" 
                                                       class="btn btn-outline-primary" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.attributes.edit', $attribute) }}" 
                                                       class="btn btn-outline-secondary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.attributes.toggle-status', $attribute) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-warning" 
                                                                title="{{ $attribute->is_active ? 'Deactivate' : 'Activate' }}">
                                                            <i class="bi bi-{{ $attribute->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.attributes.destroy', $attribute) }}" 
                                                          style="display: inline;"
                                                          onsubmit="return confirm('Are you sure you want to delete this attribute?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $attributes->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-collection display-1 text-muted"></i>
                            <h4>No Attribute Definitions Found</h4>
                            <p class="text-muted">Create your first attribute definition to get started.</p>
                            <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Create New Attribute
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection