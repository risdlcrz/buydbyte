@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>My Feedback</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('customer.feedback.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Give Feedback
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        @forelse($feedback as $item)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1">{{ ucfirst($item->type) }} Feedback</h5>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $item->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <span class="badge bg-{{ $item->status === 'pending' ? 'warning' : ($item->status === 'resolved' ? 'success' : 'info') }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>

                        <p class="card-text">{{ Str::limit($item->comment, 150) }}</p>

                        @if($item->order)
                            <p class="mb-1 text-muted">
                                <i class="fas fa-shopping-cart me-2"></i>Order #{{ $item->order->order_number }}
                            </p>
                        @endif

                        @if($item->product)
                            <p class="mb-1 text-muted">
                                <i class="fas fa-box me-2"></i>{{ $item->product->name }}
                            </p>
                        @endif

                        <div class="mt-3">
                            <small class="text-muted">Submitted {{ $item->created_at->diffForHumans() }}</small>
                        </div>

                        @if($item->admin_response)
                            <hr>
                            <div class="admin-response">
                                <h6 class="mb-2">Admin Response:</h6>
                                <p class="mb-1">{{ $item->admin_response }}</p>
                                <small class="text-muted">Responded {{ $item->responded_at->diffForHumans() }}</small>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('customer.feedback.show', $item->feedback_id) }}" class="btn btn-sm btn-outline-primary">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <h5>No Feedback Yet</h5>
                    <p class="text-muted">Share your thoughts about our products and services!</p>
                    <a href="{{ route('customer.feedback.create') }}" class="btn btn-primary">
                        Give Feedback
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $feedback->links() }}
    </div>
</div>
@endsection