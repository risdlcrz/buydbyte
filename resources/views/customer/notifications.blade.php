@extends('layouts.storefront')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h4 class="mb-0">
                <i class="bi bi-bell text-primary me-2"></i>My Notifications
                @if($unreadCount > 0)
                    <span id="page-unread-count" class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                    <button id="mark-all-read-btn" class="btn btn-sm btn-outline-secondary ms-3">Mark all as read</button>
                @endif
            </h4>
        </div>
        <div class="card-body">
            @if($notifications->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <p class="mb-0 text-muted">No notifications yet</p>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        <div class="list-group-item {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">
                                    @php
                                        $icon = match(class_basename($notification->type)) {
                                            'OrderPlacedNotification' => 'cart',
                                            'OrderShippedNotification' => 'truck',
                                            'OrderDeliveredNotification' => 'check-circle',
                                            'PaymentReceivedNotification' => 'currency-dollar',
                                            default => 'bell'
                                        };
                                    @endphp
                                    <i class="bi bi-{{ $icon }} text-primary me-2"></i>
                                    {{ $notification->data['title'] ?? $notification->data['message'] ?? 'Notification' }}
                                </h6>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ $notification->data['message'] ?? '' }}</p>
                            @if(isset($notification->data['link']))
                                <a href="{{ $notification->data['link'] }}" class="btn btn-sm btn-primary mt-2">
                                    View Details
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('mark-all-read-btn');
    if (!btn) return;

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        btn.disabled = true;

        fetch('{{ route("customer.notifications.markAsRead") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            // Update UI in-place without reload
            try {
                // Hide/update page unread badge
                const pageBadge = document.getElementById('page-unread-count');
                if (pageBadge) {
                    pageBadge.textContent = '0';
                    pageBadge.style.display = 'none';
                }

                // Update header/bell badge if present
                const bellBadge = document.getElementById('notification-count');
                if (bellBadge) {
                    bellBadge.textContent = '0';
                    bellBadge.style.display = 'none';
                }

                // Remove 'bg-light' (unread highlight) from list items and remove New badges
                document.querySelectorAll('.list-group-item').forEach(function(item) {
                    item.classList.remove('bg-light');
                    // remove small 'New' badges inside
                    item.querySelectorAll('.badge.bg-primary').forEach(function(b) { b.remove(); });
                });

                // Optionally show a small success alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
                alertDiv.innerHTML = 'All notifications marked as read <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                document.body.appendChild(alertDiv);
                setTimeout(() => { alertDiv.remove(); }, 3000);
            } catch (e) {
                console.error('Error updating UI after marking read', e);
            }
        })
        .catch(err => {
            console.error('Failed to mark notifications as read', err);
            btn.disabled = false;
        });
    });
});
</script>
@endpush