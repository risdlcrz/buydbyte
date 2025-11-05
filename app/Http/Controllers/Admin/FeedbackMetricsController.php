<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class FeedbackMetricsController extends Controller
{
    public function index()
    {
        $totalFeedback = Feedback::count();
        $averageRating = round((float) Feedback::avg('rating'), 2) ?? 0.0;
        $pendingCount = Feedback::where('status', 'pending')->count();

        // Top products by average rating (only products with feedback)
        $topProducts = Feedback::select('product_id', DB::raw('AVG(rating) as avg_rating'), DB::raw('COUNT(*) as feedback_count'))
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('avg_rating')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $product = Product::find($row->product_id);
                return [
                    'product_id' => $row->product_id,
                    'name' => $product ? $product->name : 'Unknown product',
                    'avg_rating' => round((float) $row->avg_rating, 2),
                    'feedback_count' => $row->feedback_count,
                ];
            });

        return view('admin.feedback_metrics', compact('totalFeedback', 'averageRating', 'pendingCount', 'topProducts'));
    }
}
