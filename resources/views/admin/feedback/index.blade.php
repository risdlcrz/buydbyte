@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Customer Feedback</h3>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="feedbackFilterForm" method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search comments or types">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">All types</option>
                        <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>General</option>
                        <option value="order" {{ request('type') === 'order' ? 'selected' : '' }}>Order</option>
                        <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>Product</option>
                        <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Service</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Any status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="min_rating" class="form-select">
                        <option value="">Min rating</option>
                        @for($r = 1; $r <= 5; $r++)
                            <option value="{{ $r }}" {{ request('min_rating') == $r ? 'selected' : '' }}>{{ $r }}+</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <div class="btn-group">
                        <button class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div id="feedback-list-container">
                @include('admin.feedback._list', ['feedback' => $feedback])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('feedbackFilterForm');
    const container = document.getElementById('feedback-list-container');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        fetchList(new URLSearchParams(new FormData(form)).toString());
    });

    // Delegate pagination link clicks
    container.addEventListener('click', function(e) {
        const el = e.target.closest('a');
        if (!el) return;
        const href = el.getAttribute('href');
        if (!href) return;
        if (href.indexOf('?') !== -1 || href.indexOf('/admin/feedback') !== -1) {
            e.preventDefault();
            const query = href.split('?')[1] || '';
            fetchList(query);
        }
    });

    function fetchList(query) {
    const url = {!! json_encode(route('admin.feedback.index')) !!} + (query ? ('?' + query) : '');
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
        })
        .then(r => r.text())
        .then(html => {
            container.innerHTML = html;
            // update URL without reloading
            if (history.pushState) {
                history.pushState(null, '', url);
            }
        })
        .catch(err => console.error(err));
    }
});
</script>
@endpush
