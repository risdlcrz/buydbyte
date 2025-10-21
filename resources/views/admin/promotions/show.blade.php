@extends('layouts.admin')

@section('title', 'Promotion Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">Promotions</a></li>
                        <li class="breadcrumb-item active">{{ $promotion->title }}</li>
                    </ol>
                </div>
                <h4 class="page-title">Promotion Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Promotion Info -->
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="mdi mdi-bullhorn me-2"></i>
                                {{ $promotion->title }}
                            </h4>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group">
                                <a href="{{ route('admin.promotions.edit', $promotion) }}" class="btn btn-primary">
                                    <i class="mdi mdi-pencil me-1"></i>
                                    Edit
                                </a>
                                <form action="{{ route('admin.promotions.destroy', $promotion) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this promotion?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="mdi mdi-delete me-1"></i>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Preview -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="mdi mdi-eye me-2"></i>
                                Preview
                            </h5>
                            <div class="border rounded p-3" 
                                 style="background-color: {{ $promotion->background_color }}; color: {{ $promotion->text_color }};">
                                @if($promotion->banner_image)
                                    <div class="text-center mb-3">
                                        <img src="{{ asset('storage/' . $promotion->banner_image) }}" 
                                             alt="Banner" 
                                             class="img-fluid rounded"
                                             style="max-height: 200px;">
                                    </div>
                                @endif
                                <div class="text-center">
                                    <h4 class="mb-2">{{ $promotion->title }}</h4>
                                    @if($promotion->description)
                                        <p class="mb-3">{{ $promotion->description }}</p>
                                    @endif
                                    @if($promotion->discount_text)
                                        <div class="mb-3">
                                            <span class="fs-2 fw-bold">{{ $promotion->discount_text }}</span>
                                            @if($promotion->discount_code)
                                                <br><span class="fs-6">Use code: <strong>{{ $promotion->discount_code }}</strong></span>
                                            @endif
                                        </div>
                                    @endif
                                    @if($promotion->button_text && $promotion->button_link)
                                        <a href="{{ $promotion->button_link }}" 
                                           class="btn btn-lg" 
                                           style="background-color: {{ $promotion->button_color }}; color: white; border: none;"
                                           target="_blank">
                                            {{ $promotion->button_text }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="mdi mdi-information me-2"></i>
                                Basic Information
                            </h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Title:</strong></td>
                                    <td>{{ $promotion->title }}</td>
                                </tr>
                                @if($promotion->description)
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td>{{ $promotion->description }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $promotion->type === 'banner' ? 'info' : ($promotion->type === 'popup' ? 'warning' : 'success') }}">
                                            {{ ucfirst($promotion->type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
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
                                </tr>
                                <tr>
                                    <td><strong>Sort Order:</strong></td>
                                    <td>{{ $promotion->sort_order ?? 0 }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="mdi mdi-calendar me-2"></i>
                                Schedule & Targeting
                            </h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Start Date:</strong></td>
                                    <td>{{ $promotion->start_date->format('M d, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>End Date:</strong></td>
                                    <td>{{ $promotion->end_date->format('M d, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Duration:</strong></td>
                                    <td>{{ $promotion->start_date->diffInDays($promotion->end_date) }} days</td>
                                </tr>
                                <tr>
                                    <td><strong>Target Audience:</strong></td>
                                    <td>
                                        <span class="badge bg-outline-primary">
                                            {{ ucfirst(str_replace('_', ' ', $promotion->target_audience)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Display Pages:</strong></td>
                                    <td>
                                        @if($promotion->display_pages && count($promotion->display_pages) > 0)
                                            @foreach($promotion->display_pages as $page)
                                                <span class="badge bg-light text-dark me-1">{{ ucfirst($page) }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No specific pages</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @if($promotion->discount_percentage || $promotion->discount_amount || $promotion->discount_code)
            <!-- Discount Information -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-percent me-2"></i>
                        Discount Information
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($promotion->discount_percentage)
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <h3 class="text-success mb-2">{{ $promotion->discount_percentage }}%</h3>
                                <p class="text-muted mb-0">Percentage Discount</p>
                            </div>
                        </div>
                        @endif
                        @if($promotion->discount_amount)
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <h3 class="text-success mb-2">${{ number_format($promotion->discount_amount, 2) }}</h3>
                                <p class="text-muted mb-0">Fixed Amount Discount</p>
                            </div>
                        </div>
                        @endif
                        @if($promotion->discount_code)
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <h3 class="text-primary mb-2">{{ $promotion->discount_code }}</h3>
                                <p class="text-muted mb-0">Discount Code</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if($promotion->button_text || $promotion->button_link)
            <!-- Button Settings -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-gesture-tap-button me-2"></i>
                        Button Settings
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Button Text:</strong>
                            <p>{{ $promotion->button_text ?: 'No button text' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Button Link:</strong>
                            <p>
                                @if($promotion->button_link)
                                    <a href="{{ $promotion->button_link }}" target="_blank" class="text-break">
                                        {{ $promotion->button_link }}
                                        <i class="mdi mdi-external-link ms-1"></i>
                                    </a>
                                @else
                                    No button link
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-chart-line me-2"></i>
                        Quick Stats
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="mb-1">
                                    @if($promotion->start_date <= now())
                                        {{ $promotion->start_date->diffInDays(now()) }}
                                    @else
                                        0
                                    @endif
                                </h4>
                                <p class="text-muted mb-0">Days Running</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-1">
                                @if($promotion->end_date >= now())
                                    {{ now()->diffInDays($promotion->end_date) }}
                                @else
                                    0
                                @endif
                            </h4>
                            <p class="text-muted mb-0">Days Remaining</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Color Scheme -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-palette me-2"></i>
                        Color Scheme
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="mb-2">
                                <div class="rounded" 
                                     style="width: 50px; height: 50px; background-color: {{ $promotion->background_color }}; margin: 0 auto;"></div>
                            </div>
                            <small class="text-muted">Background<br>{{ $promotion->background_color }}</small>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <div class="rounded" 
                                     style="width: 50px; height: 50px; background-color: {{ $promotion->text_color }}; margin: 0 auto; border: 1px solid #dee2e6;"></div>
                            </div>
                            <small class="text-muted">Text<br>{{ $promotion->text_color }}</small>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <div class="rounded" 
                                     style="width: 50px; height: 50px; background-color: {{ $promotion->button_color }}; margin: 0 auto;"></div>
                            </div>
                            <small class="text-muted">Button<br>{{ $promotion->button_color }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-clock me-2"></i>
                        Timestamps
                    </h4>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Created:</strong></td>
                            <td>{{ $promotion->created_at->format('M d, Y g:i A') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Updated:</strong></td>
                            <td>{{ $promotion->updated_at->format('M d, Y g:i A') }}</td>
                        </tr>
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td><code>{{ $promotion->promotion_id }}</code></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body text-center">
                    <a href="{{ route('admin.promotions.edit', $promotion) }}" class="btn btn-primary btn-lg mb-2">
                        <i class="mdi mdi-pencil me-1"></i>
                        Edit Promotion
                    </a>
                    <br>
                    <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i>
                        Back to Promotions
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection