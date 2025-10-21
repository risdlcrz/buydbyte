@extends('layouts.admin')

@section('title', 'Promotions Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Promotions</li>
                    </ol>
                </div>
                <h4 class="page-title">Promotions Management</h4>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="mdi mdi-bullhorn me-2"></i>
                                All Promotions
                            </h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus me-1"></i>
                                Create New Promotion
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if($promotions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Preview</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Discount</th>
                                        <th>Status</th>
                                        <th>Duration</th>
                                        <th>Sort Order</th>
                                        <th>Target</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($promotions as $promotion)
                                        <tr>
                                            <td>
                                                @if($promotion->banner_image)
                                                    <img src="{{ asset('storage/' . $promotion->banner_image) }}" 
                                                         alt="Banner" 
                                                         class="rounded" 
                                                         style="width: 60px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="d-inline-block rounded text-center" 
                                                         style="width: 60px; height: 40px; line-height: 40px; background-color: {{ $promotion->background_color }}; color: {{ $promotion->text_color }}; font-size: 10px;">
                                                        {{ substr($promotion->title, 0, 8) }}...
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $promotion->title }}</div>
                                                @if($promotion->description)
                                                    <small class="text-muted">{{ Str::limit($promotion->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $promotion->type === 'banner' ? 'info' : ($promotion->type === 'popup' ? 'warning' : 'success') }}">
                                                    {{ ucfirst($promotion->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($promotion->discount_text)
                                                    <span class="fw-bold text-danger">{{ $promotion->discount_text }}</span>
                                                    @if($promotion->discount_code)
                                                        <br><small class="text-muted">Code: {{ $promotion->discount_code }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No discount</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($promotion->is_currently_active)
                                                    <span class="badge bg-success">
                                                        <i class="mdi mdi-check-circle me-1"></i>
                                                        Active
                                                    </span>
                                                @elseif($promotion->is_active && $promotion->start_date > now())
                                                    <span class="badge bg-warning">
                                                        <i class="mdi mdi-clock me-1"></i>
                                                        Scheduled
                                                    </span>
                                                @elseif($promotion->is_active && $promotion->end_date < now())
                                                    <span class="badge bg-secondary">
                                                        <i class="mdi mdi-clock-end me-1"></i>
                                                        Expired
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="mdi mdi-close-circle me-1"></i>
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="small">
                                                    <strong>Start:</strong> {{ $promotion->start_date->format('M d, Y') }}<br>
                                                    <strong>End:</strong> {{ $promotion->end_date->format('M d, Y') }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $promotion->sort_order ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-outline-primary">{{ ucfirst(str_replace('_', ' ', $promotion->target_audience)) }}</span>
                                                @if($promotion->display_pages && count($promotion->display_pages) > 0)
                                                    <br><small class="text-muted">{{ implode(', ', $promotion->display_pages) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.promotions.show', $promotion) }}" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       data-bs-toggle="tooltip" 
                                                       title="View Details">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.promotions.edit', $promotion) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       data-bs-toggle="tooltip" 
                                                       title="Edit">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('admin.promotions.destroy', $promotion) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this promotion?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                data-bs-toggle="tooltip" 
                                                                title="Delete">
                                                            <i class="mdi mdi-delete"></i>
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
                        @if($promotions->hasPages())
                            <div class="mt-4 d-flex justify-content-center">
                                {{ $promotions->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="mdi mdi-bullhorn-outline display-4 text-muted"></i>
                            </div>
                            <h5 class="text-muted">No Promotions Found</h5>
                            <p class="text-muted mb-4">You haven't created any promotions yet. Create your first promotion to start engaging with your customers.</p>
                            <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus me-1"></i>
                                Create Your First Promotion
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
@endsection