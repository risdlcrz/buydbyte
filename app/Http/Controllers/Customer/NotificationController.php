<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $unreadCount = $user->unreadNotifications()->count();

        return view('customer.notifications', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        if ($request->has('id')) {
            $notification = $user->notifications()->findOrFail($request->id);
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        
        $user->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}