<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewFeedbackNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function create()
    {
        $orders = Auth::user()->orders()
            ->whereDoesntHave('feedback')
            ->latest()
            ->get();

        return view('customer.feedback.create', compact('orders'));
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

        return redirect()
            ->route('customer.feedback.index')
            ->with('success', 'Thank you for your feedback! We will review it shortly.');
    }

    public function show(Feedback $feedback)
    {
        $this->authorize('view', $feedback);
        return view('customer.feedback.show', compact('feedback'));
    }
}