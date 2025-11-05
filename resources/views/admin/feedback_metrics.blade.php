@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <div class="card p-3">
                <h6 class="mb-1">Total Feedback</h6>
                <h3 class="mb-0">{{ $totalFeedback }}</h3>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card p-3">
                <h6 class="mb-1">Average Rating</h6>
                <h3 class="mb-0">{{ $averageRating }} / 5</h3>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card p-3">
                <h6 class="mb-1">Pending Feedback</h6>
                <h3 class="mb-0">{{ $pendingCount }}</h3>
            </div>
        </div>

        <div class="col-12 mt-3">
            <div class="card p-3">
                <h6 class="mb-3">Top Products (by average rating)</h6>
                @if($topProducts->isEmpty())
                    <p class="text-muted">No product feedback yet.</p>
                @else
                    <div class="list-group">
                        @foreach($topProducts as $p)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $p['name'] }}</strong>
                                    <div class="text-muted small">{{ $p['feedback_count'] }} review(s)</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">{{ $p['avg_rating'] }} / 5</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
