<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewFeedbackNotification;
use App\Notifications\OrderCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedback = Auth::user()->feedback()
            ->with(['order', 'product'])
            ->latest()
            ->paginate(10);

        return view('customer.feedback.index', compact('feedback'));
    }

    public function create(Request $request)
    {
        $orderId = $request->query('order_id');
        
        $orders = Auth::user()->orders()
            ->whereDoesntHave('feedback')
            ->whereIn('status', ['delivered', 'received'])
            ->latest()
            ->get();

        $selectedOrder = null;
        if ($orderId) {
            $selectedOrder = $orders->firstWhere('order_id', $orderId);
        }

        return view('customer.feedback.create', compact('orders', 'selectedOrder'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:general,order,product,service',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
            'order_id' => 'nullable|exists:orders,order_id',
            'product_id' => 'nullable|exists:products,product_id'
        ]);

        $feedback = new Feedback($request->all());
        $feedback->user_id = Auth::id();
        $feedback->status = 'pending';
        $feedback->save();

        // Notify admins about new feedback
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewFeedbackNotification($feedback));
        }

        // If feedback is for an order, mark the order as completed
        if ($request->has('order_id') && $request->order_id) {
            $order = Order::where('order_id', $request->order_id)
                ->where('user_id', Auth::id())
                ->where('status', 'received')
                ->first();
            
            if ($order) {
                try {
                    $order->status = 'completed';
                    $order->save();

                    // Create tracking entry
                    $tracking = new \App\Models\OrderTracking();
                    $tracking->order_id = $order->order_id;
                    $tracking->status = 'completed';
                    $tracking->description = 'Order completed by customer with feedback';
                    $tracking->location = 'Customer';
                    $tracking->save();

                    // Send completion notification
                    $order->user->notify(new OrderCompletedNotification($order));
                } catch (\Exception $e) {
                    \Log::warning('Failed to mark order as completed: ' . $e->getMessage());
                }
            }
        }

        return redirect()
            ->route('customer.feedback.index')
            ->with('success', 'Thank you for your feedback! Your order has been marked as completed.');
    }

    public function show(Feedback $feedback)
    {
        $this->authorize('view', $feedback);
        return view('customer.feedback.show', compact('feedback'));
    }
}