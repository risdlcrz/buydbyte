@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Feedback Details</h3>
        <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ ucfirst($feedback->type) }} feedback</h5>
            <p class="text-muted">From: {{ $feedback->user?->full_name ?? 'Guest' }} &middot; {{ $feedback->created_at->diffForHumans() }}</p>
            <div class="mb-3">
                <strong>Rating:</strong>
                <span class="text-warning">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $feedback->rating)
                            <i class="fas fa-star"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                </span>
            </div>

            <p>{{ $feedback->comment }}</p>

            @if($feedback->order)
                <p><strong>Order:</strong> #{{ $feedback->order->order_number }}</p>
            @endif

            @if($feedback->product)
                <p><strong>Product:</strong> {{ $feedback->product->name }}</p>
            @endif

            @if($feedback->admin_response)
                <hr>
                <h6>Admin Response</h6>
                <p>{{ $feedback->admin_response }}</p>
                <small class="text-muted">Responded {{ $feedback->responded_at?->diffForHumans() }}</small>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Respond to feedback</h5>
            <form method="POST" action="{{ route('admin.feedback.respond', $feedback->feedback_id) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Response</label>
                    <textarea name="admin_response" class="form-control" rows="4">{{ old('admin_response', $feedback->admin_response) }}</textarea>
                    @error('admin_response')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="reviewed" {{ old('status', $feedback->status) === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="resolved" {{ old('status', $feedback->status) === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                    @error('status')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                <button class="btn btn-primary">Save & Notify Customer</button>
            </form>
        </div>
    </div>
</div>
@endsection
