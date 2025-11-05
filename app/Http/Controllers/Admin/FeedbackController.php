<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\FeedbackRespondedNotification;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the feedback.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Feedback::with(['user', 'order', 'product']);

        // Filters
        if (request()->filled('q')) {
            $q = request('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('comment', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%");
            });
        }

        if (request()->filled('type')) {
            $query->where('type', request('type'));
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('min_rating')) {
            $query->where('rating', '>=', (int) request('min_rating'));
        }

        $feedback = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // If AJAX request, return the rendered list only
        if ($request->ajax()) {
            return view('admin.feedback._list', compact('feedback'))->render();
        }

        return view('admin.feedback.index', compact('feedback'));
    }

    /**
     * Display the specified feedback.
     */
    public function show(Feedback $feedback)
    {
        $feedback->load(['user', 'order', 'product', 'admin']);
        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Respond to feedback (admin action).
     */
    public function respond(Request $request, Feedback $feedback)
    {
        $request->validate([
            'admin_response' => 'required|string|min:3|max:2000',
            'status' => 'required|in:reviewed,resolved'
        ]);

        $feedback->admin_response = $request->admin_response;
        $feedback->status = $request->status;
        $feedback->admin_id = Auth::id();
        $feedback->responded_at = now();
        $feedback->save();

        // Notify customer that their feedback was responded to
        try {
            $feedback->user->notify(new FeedbackRespondedNotification($feedback));
        } catch (\Exception $e) {
            // Don't block admin action if notification fails; log and continue
            Log::error('Failed to notify user about feedback response: ' . $e->getMessage());
        }

        return redirect()->route('admin.feedback.show', $feedback->feedback_id)
            ->with('success', 'Response saved and customer notified.');
    }
}
