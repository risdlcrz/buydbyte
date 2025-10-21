<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DiscountController extends Controller
{
    /**
     * Apply a discount code
     */
    public function apply(Request $request)
    {
        $request->validate([
            'discount_code' => 'required|string|max:50'
        ]);

        $code = strtoupper(trim($request->discount_code));
        
        // Find active promotion with this discount code
        $promotion = Promotion::active()
                             ->where('discount_code', $code)
                             ->first();

        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired discount code.'
            ]);
        }

        // Store discount in session
        Session::put('applied_discount', [
            'code' => $promotion->discount_code,
            'type' => $promotion->discount_percentage ? 'percentage' : 'amount',
            'value' => $promotion->discount_percentage ?: $promotion->discount_amount,
            'promotion_id' => $promotion->promotion_id,
            'title' => $promotion->title
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Discount code applied successfully!',
            'discount' => [
                'code' => $promotion->discount_code,
                'text' => $promotion->discount_text,
                'type' => $promotion->discount_percentage ? 'percentage' : 'amount',
                'value' => $promotion->discount_percentage ?: $promotion->discount_amount
            ]
        ]);
    }

    /**
     * Remove applied discount
     */
    public function remove()
    {
        Session::forget('applied_discount');
        
        return response()->json([
            'success' => true,
            'message' => 'Discount code removed.'
        ]);
    }

    /**
     * Get current applied discount
     */
    public function current()
    {
        $discount = Session::get('applied_discount');
        
        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'No discount code applied.'
            ]);
        }

        // Verify the promotion is still active
        $promotion = Promotion::active()
                             ->where('promotion_id', $discount['promotion_id'])
                             ->first();

        if (!$promotion) {
            Session::forget('applied_discount');
            return response()->json([
                'success' => false,
                'message' => 'Applied discount code has expired.'
            ]);
        }

        return response()->json([
            'success' => true,
            'discount' => $discount
        ]);
    }
}