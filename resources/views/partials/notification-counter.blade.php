@php
    $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
@endphp

@if(auth()->check() && in_array(auth()->user()->role, ['admin', 'finance']))
    <li class="nav-item dropdown">
        <a class="nav-link" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-bell"></i>
            @if($unreadCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $unreadCount }}
                    <span class="visually-hidden">unread notifications</span>
                </span>
            @endif
        </a>
        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown" style="width: 300px; max-height: 400px; overflow-y: auto;">
            <li>
                <div class="dropdown-header d-flex justify-content-between align-items-center">
                    <span>Notifications</span>
                    @if($unreadCount > 0)
                        <form method="POST" action="{{ route(auth()->user()->role . '.notifications.markAllAsRead') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm p-0 text-primary">Mark all read</button>
                        </form>
                    @endif
                </div>
            </li>
            @forelse(auth()->user()->unreadNotifications()->take(5)->get() as $notification)
                <li>
                    <a class="dropdown-item notification-item" href="{{ auth()->user()->role === 'finance' ? route('finance.notifications.index') : route('admin.notifications.index') }}">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
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
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-0 font-weight-bold">{{ $notification->data['title'] }}</p>
                                <p class="mb-0 small text-muted">{{ Str::limit($notification->data['message'], 100) }}</p>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li><div class="dropdown-item text-center text-muted">No new notifications</div></li>
            @endforelse
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-center" href="{{ auth()->user()->role === 'finance' ? route('finance.notifications.index') : route('admin.notifications.index') }}">
                    View all notifications
                </a>
            </li>
        </ul>
    </li>
@endif