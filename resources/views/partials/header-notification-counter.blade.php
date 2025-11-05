@auth
    <a href="{{ route('customer.notifications.index') }}" class="text-decoration-none position-relative">
        <i class="fas fa-bell text-dark"></i>
        @if(Auth::user()->unreadNotifications->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ Auth::user()->unreadNotifications->count() }}
                <span class="visually-hidden">unread notifications</span>
            </span>
        @endif
    </a>
@endauth