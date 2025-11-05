<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public $notifications;
    public $unreadCount;

    public function __construct($notifications = null, $unreadCount = null)
    {
        $user = Auth::user();
        $this->notifications = $notifications ?? $user->notifications()->latest()->take(5)->get();
        $this->unreadCount = $unreadCount ?? $user->unreadNotifications()->count();
    }

    public function render()
    {
        return view('components.notification-dropdown');
    }
}