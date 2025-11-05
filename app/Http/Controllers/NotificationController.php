<?php

namespace App\Http\Controllers;

use App\Notifications\SimpleNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationController extends Controller
{
    /**
     * List notifications for the authenticated user (JSON).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $perPage = (int) $request->query('per_page', 20);
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->paginate($perPage);

        // Return JSON for API requests, Blade view for web requests
        if ($request->wantsJson() || $request->isXmlHttpRequest()) {
            return response()->json($notifications);
        }

        // Only admin or finance should access the web UI
        if (! in_array($user->role, ['admin', 'finance'], true)) {
            abort(403, 'Forbidden');
        }

        $unreadCount = $user->unreadNotifications()->count();
        return view('notifications.center', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Mark a single notification as read for the current user.
     */
    public function markRead(string $id)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        // Only admin or finance may mark notifications via the admin/finance UI
        if (! in_array($user->role, ['admin', 'finance'], true) && request()->is('admin/*') || request()->is('finance/*')) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            abort(403, 'Forbidden');
        }

        $notification = $user->notifications()->where('id', $id)->first();
        if (! $notification) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Notification not found'], 404);
            }
            return redirect()->back()->with('error', 'Notification not found');
        }

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Marked as read']);
        }

        return redirect()->back();
    }

    /**
     * Mark all notifications for the current user as read.
     */
    public function markAllRead()
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Only admin or finance may perform this from admin/finance UI
        if (! in_array($user->role, ['admin', 'finance'], true) && (request()->is('admin/*') || request()->is('finance/*'))) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            abort(403, 'Forbidden');
        }

        $user->unreadNotifications->each(function ($n) {
            $n->markAsRead();
        });

        if (request()->wantsJson()) {
            return response()->json(['message' => 'All notifications marked as read']);
        }

        return redirect()->back();
    }

    /**
     * Create / send a notification.
     * Only users with role 'admin' or 'finance' can send notifications.
     * Payload: title, body, data (optional), target_user_id (optional), target_role (optional)
     */
    public function store(Request $request)
    {
        $sender = Auth::user();
        if (! $sender) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (! in_array($sender->role, ['admin', 'finance'], true)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
            'target_user_id' => 'nullable|string',
            'target_role' => 'nullable|string',
        ]);

        $title = $validated['title'];
        $body = $validated['body'];
        $data = $validated['data'] ?? [];

        $notification = new SimpleNotification($title, $body, $data);

        // Target a specific user
        if (! empty($validated['target_user_id'])) {
            $target = User::where('user_id', $validated['target_user_id'])->first();
            if (! $target) {
                return response()->json(['message' => 'Target user not found'], 404);
            }

            $target->notify($notification);
            return response()->json(['message' => 'Notification sent to user']);
        }

        // Target by role
        if (! empty($validated['target_role'])) {
            $users = User::where('role', $validated['target_role'])->get();
            NotificationFacade::send($users, $notification);
            return response()->json(['message' => 'Notification sent to role', 'count' => $users->count()]);
        }

        // Default: broadcast to all users
        $users = User::all();
        NotificationFacade::send($users, $notification);
        return response()->json(['message' => 'Notification sent to all users', 'count' => $users->count()]);
    }

    /**
     * Clear read notifications for the current user (delete them).
     */
    public function clearRead()
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Delete read notifications for this user
        $deleted = $user->notifications()->whereNotNull('read_at')->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'deleted' => $deleted]);
        }

        return redirect()->back()->with('success', 'Read notifications cleared');
    }
}
