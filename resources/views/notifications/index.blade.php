@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 rounded-top-4" style="padding: 1.5rem 2rem 1rem 2rem;">
            <h4 class="mb-0 fw-bold text-success">
                <i class="fas fa-bell me-2"></i>
                @if($role === 'finance')
                    Payment Notifications
                @else
                    Order Notifications
                @endif
                @if($unreadCount > 0)
                    <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                @endif
            </h4>
            <div>
                <form method="POST" action="{{ route($role.'.notifications.markAllAsRead') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm me-2">Mark All as Read</button>
                </form>
                <form method="POST" action="{{ route($role.'.notifications.clearRead') }}" class="d-inline" id="clearReadForm">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">Clear Read Notifications</button>
                </form>
            </div>
        </div>
        <div class="card-body p-4 bg-light rounded-bottom-4">
            @if($notifications->isEmpty())
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                    <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                    <span class="fw-semibold text-muted" style="font-size:1.2em;">
                        @if($role === 'finance')
                            No payment notifications found.
                        @else
                            No notifications found.
                        @endif
                    </span>
                </div>
            @else
                <ul class="list-group list-group-flush" id="notificationList">
                    @foreach($notifications as $notification)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-3 notification-item @if(is_null($notification->read_at)) fw-bold bg-white @else bg-light @endif" 
                            style="border-radius: 1rem; margin-bottom: 0.5rem; border: none; cursor:pointer;" 
                            data-id="{{ $notification->id }}" 
                            data-read="{{ $notification->read_at ? '1' : '0' }}">
                            <div class="w-100">
                                <div class="mb-1">
                                    @php
                                        $icon = match(class_basename($notification->type)) {
                                            'OrderPlacedNotification' => 'shopping-cart',
                                            'OrderShippedNotification' => 'truck',
                                            'OrderDeliveredNotification' => 'check-circle',
                                            'PaymentReceivedNotification' => 'money-bill',
                                            default => 'bell'
                                        };
                                    @endphp
                                    <i class="fas fa-{{ $icon }} me-2 text-{{ is_null($notification->read_at) ? 'warning' : 'secondary' }}"></i>
                                    {{ $notification->data['title'] }}
                                </div>
                                <div class="mb-1 small text-muted">{{ $notification->created_at->diffForHumans() }}</div>
                                <div class="mb-2">{{ $notification->data['message'] }}</div>
                                
                                @if(isset($notification->data['amount']))
                                    <div class="mb-2">
                                        <span class="badge bg-success">₦{{ number_format($notification->data['amount'], 2) }}</span>
                                    </div>
                                @endif
                                
                                @if(isset($notification->data['link']))
                                    <a href="{{ $notification->data['link'] }}" class="badge bg-primary text-white">View Details</a>
                                @endif
                            </div>
                            @if(is_null($notification->read_at))
                                <span class="badge bg-warning text-dark ms-2">Unread</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
                
                {{ $notifications->links() }}
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.card {
    border-radius: 1.25rem;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    border: none;
    margin-bottom: 2rem;
}
.card-header {
    background: #fff;
    border-radius: 1.25rem 1.25rem 0 0;
    border-bottom: none;
    padding: 1.5rem 2rem 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.card-body {
    background: #f8fafc;
    border-radius: 0 0 1.25rem 1.25rem;
}
.list-group-item {
    border: none;
    border-radius: 1rem !important;
    margin-bottom: 0.5rem;
    background: #fff;
    transition: box-shadow 0.2s;
}
.list-group-item.bg-light {
    background: #f8fafc !important;
}
.list-group-item.bg-white {
    background: #fff !important;
    box-shadow: 0 2px 8px #38b6ff11;
}
.list-group-item:hover {
    box-shadow: 0 4px 16px #38b6ff22;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const role = '{{ $role }}';
    
    // Mark notification as read on click
    document.querySelectorAll('.notification-item').forEach(function(item) {
        item.addEventListener('click', function(e) {
            // Don't trigger if clicking a link
            if (e.target.tagName === 'A') return;
            
            var id = this.getAttribute('data-id');
            var isRead = this.getAttribute('data-read') === '1';
            if (!isRead) {
                fetch(`/${role}/notifications/${id}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                }).then(res => {
                    if (res.ok) {
                        this.classList.remove('fw-bold', 'bg-white');
                        this.classList.add('bg-light');
                        this.setAttribute('data-read', '1');
                        var badge = this.querySelector('.badge.bg-warning');
                        if (badge) badge.remove();
                        
                        // Update unread count in header
                        var countBadge = document.querySelector('.card-header .badge.bg-danger');
                        if (countBadge) {
                            var count = parseInt(countBadge.textContent) - 1;
                            if (count <= 0) {
                                countBadge.remove();
                            } else {
                                countBadge.textContent = count;
                            }
                        }
                    }
                });
            }
        });
    });

    // Clear read notifications
    var clearForm = document.getElementById('clearReadForm');
    if (clearForm) {
        clearForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    document.querySelectorAll('.notification-item[data-read="1"]').forEach(function(item) {
                        item.remove();
                    });
                }
            });
        });
    }
});
</script>
@endpush
@endsection