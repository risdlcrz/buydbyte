<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\OrderDeliveredNotification;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\OrderShippedNotification;
use App\Notifications\PaymentReceivedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Spatie\Activitylog\Facades\LogActivity;

class OrderController extends Controller
{
    public function updateStatus(Order $order, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();

        // Send notifications based on status changes
        switch ($request->status) {
            case 'shipped':
                $order->user->notify(new OrderShippedNotification($order));
                break;
            case 'delivered':
                $order->user->notify(new OrderDeliveredNotification($order));
                break;
        }

        // Log the status change
        LogActivity::log("Order #{$order->order_number} status changed from {$oldStatus} to {$request->status}");

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order
        ]);
    }

    public function confirmPayment(Order $order)
    {
        $order->payment_status = 'paid';
        $order->save();

        // Notify customer and finance team
        $order->user->notify(new PaymentReceivedNotification($order, $order->total_amount));
        
        // Get finance users and notify them
        $financeUsers = \App\Models\User::where('role', 'finance')->get();
        Notification::send($financeUsers, new PaymentReceivedNotification($order, $order->total_amount));

        return response()->json([
            'message' => 'Payment confirmed successfully',
            'order' => $order
        ]);
    }
}