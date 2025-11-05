<x-notification-dropdown :notifications="$notifications" :unread-count="$unreadCount">
    <div class="dropdown">
        <button class="btn btn-link position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-bell text-dark"></i>
            @if($unreadCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $unreadCount }}
                    <span class="visually-hidden">unread notifications</span>
                </span>
            @endif
        </button>
        <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <h6 class="mb-0">Notifications</h6>
                @if($unreadCount > 0)
                    <button class="btn btn-link btn-sm text-decoration-none" onclick="NotificationManager.markAllAsRead()">
                        Mark all as read
                    </button>
                @endif
            </div>
            <div class="notification-list">
                @forelse($notifications as $notification)
                    <div class="dropdown-item notification {{ is_null($notification->read_at) ? 'unread' : '' }}"
                         data-notification-id="{{ $notification->id }}">
                        <div class="d-flex">
                            <div class="notification-icon">
                                @php
                                    $icon = match(class_basename($notification->type)) {
                                        'OrderPlacedNotification' => 'shopping-cart',
                                        'OrderShippedNotification' => 'truck',
                                        'OrderDeliveredNotification' => 'check-circle',
                                        'PaymentReceivedNotification' => 'money-bill',
                                        default => 'bell'
                                    };
                                @endphp
                                <i class="fas fa-{{ $icon }} text-primary"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">{{ $notification->data['title'] }}</div>
                                <div class="notification-message">{{ $notification->data['message'] }}</div>
                                <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @if(isset($notification->data['link']))
                            <a href="{{ $notification->data['link'] }}" class="btn btn-sm btn-primary mt-2">
                                View Details
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="dropdown-item text-center text-muted py-3">
                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                        <p class="mb-0">No notifications</p>
                    </div>
                @endforelse
            </div>
            @if($notifications->hasPages())
                <div class="dropdown-item border-top text-center">
                    <a href="{{ route('notifications.index') }}" class="text-primary text-decoration-none">
                        View All Notifications
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-notification-dropdown>