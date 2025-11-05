<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Feedback;
use App\Notifications\OrderReceivedNotification;
use App\Notifications\OrderCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()
            ->with(['items.product', 'tracking'])
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show($orderId)
    {
        $order = Order::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'tracking', 'feedback'])
            ->firstOrFail();

        return view('customer.orders.show', compact('order'));
    }

    public function receive(Request $request, $orderId)
    {
        $order = Order::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Check if order can be received (must be delivered)
        if ($order->status !== 'delivered') {
            return back()->with('error', 'Order must be delivered before you can confirm receipt.');
        }

        // Check if already received
        if ($order->status === 'received') {
            return back()->with('info', 'Order has already been marked as received.');
        }

        try {
            DB::beginTransaction();

            // Update order status to received
            $order->status = 'received';
            $order->save();

            // Create tracking entry
            $tracking = new OrderTracking();
            $tracking->order_id = $order->order_id;
            $tracking->status = 'received';
            $tracking->description = 'Order received and confirmed by customer';
            $tracking->location = 'Customer';
            $tracking->save();

            DB::commit();

            // Send notification
            try {
                $order->user->notify(new OrderReceivedNotification($order));
            } catch (\Exception $e) {
                \Log::warning('Notification failed: ' . $e->getMessage());
            }

            return redirect()->route('customer.orders.show', $order->order_id)
                ->with('success', 'Order received confirmed! Please leave feedback to complete your order.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order receive error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while confirming order receipt.');
        }
    }

    public function complete(Request $request, $orderId)
    {
        $order = Order::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->with('feedback')
            ->firstOrFail();

        // Check if order can be completed (must be received and no feedback yet)
        if ($order->status !== 'received') {
            return back()->with('error', 'Please confirm order receipt first before completing.');
        }

        if ($order->feedback) {
            return back()->with('error', 'Order has already been completed.');
        }

        // Redirect to feedback form
        return redirect()->route('customer.feedback.create', ['order_id' => $order->order_id])
            ->with('success', 'Please provide feedback to complete your order.');
    }

    public function markCompleted(Request $request, $orderId)
    {
        // This is called after feedback is submitted
        $order = Order::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->with('feedback')
            ->firstOrFail();

        // Check if order has feedback and is received
        if (!$order->feedback) {
            return back()->with('error', 'Please provide feedback first.');
        }

        if ($order->status !== 'received') {
            return back()->with('error', 'Order must be received first.');
        }

        try {
            DB::beginTransaction();

            // Update order status to completed
            $order->status = 'completed';
            $order->save();

            // Create tracking entry
            $tracking = new OrderTracking();
            $tracking->order_id = $order->order_id;
            $tracking->status = 'completed';
            $tracking->description = 'Order completed by customer with feedback';
            $tracking->location = 'Customer';
            $tracking->save();

            DB::commit();

            // Send notification
            try {
                $order->user->notify(new OrderCompletedNotification($order));
            } catch (\Exception $e) {
                \Log::warning('Notification failed: ' . $e->getMessage());
            }

            return redirect()->route('customer.orders.show', $order->order_id)
                ->with('success', 'Order completed! Thank you for your feedback.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order completion error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while completing the order.');
        }
    }

    public function track($orderId)
    {
        $order = Order::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->with(['tracking' => function($query) {
                $query->latest();
            }])
            ->firstOrFail();

        return view('customer.orders.track', compact('order'));
    }
}
