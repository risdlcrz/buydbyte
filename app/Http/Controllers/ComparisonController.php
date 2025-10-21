<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductComparison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComparisonController extends Controller
{
    /**
     * Display the comparison page
     */
    public function index()
    {
        $sessionId = session()->getId();
        $userId = Auth::id();
        
        $comparisons = ProductComparison::getComparisonList($sessionId, $userId);
        $products = $comparisons->map(function($comparison) {
            return $comparison->product;
        });
        
        // Get comparable attributes for all products
        $attributes = [];
        if ($products->count() > 0) {
            $productIds = $products->pluck('product_id')->toArray();
            $attributes = \App\Models\ProductAttribute::getComparableAttributes($productIds);
        }
        
        return view('storefront.comparison.index', compact('products', 'comparisons', 'attributes'));
    }

    /**
     * Add product to comparison
     */
    public function add(Request $request, Product $product)
    {
        try {
            $sessionId = session()->getId();
            $userId = Auth::id();
            
            // Check if already in comparison
            $exists = ProductComparison::where('product_id', $product->product_id)
                                     ->where(function($query) use ($userId, $sessionId) {
                                         if ($userId) {
                                             $query->where('user_id', $userId);
                                         } else {
                                             $query->where('session_id', $sessionId);
                                         }
                                     })
                                     ->exists();
            
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is already in comparison list'
                ]);
            }
            
            // Check comparison limit (max 4 products)
            $count = ProductComparison::getComparisonCount($sessionId, $userId);
            if ($count >= 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can compare maximum 4 products at a time'
                ]);
            }
            
            // Add to comparison
            ProductComparison::create([
                'session_id' => $userId ? null : $sessionId,
                'user_id' => $userId,
                'product_id' => $product->product_id,
            ]);
            
            $newCount = $count + 1;
            
            return response()->json([
                'success' => true,
                'message' => 'Product added to comparison successfully!',
                'count' => $newCount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Comparison add error: ' . $e->getMessage(), [
                'product_id' => $product->product_id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to comparison. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove product from comparison
     */
    public function remove(Request $request, Product $product)
    {
        $sessionId = session()->getId();
        $userId = Auth::id();
        
        $deleted = ProductComparison::where('product_id', $product->product_id)
                                  ->where(function($query) use ($userId, $sessionId) {
                                      if ($userId) {
                                          $query->where('user_id', $userId);
                                      } else {
                                          $query->where('session_id', $sessionId);
                                      }
                                  })
                                  ->delete();
        
        $newCount = ProductComparison::getComparisonCount($sessionId, $userId);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product removed from comparison',
                'count' => $newCount
            ]);
        }
        
        return back()->with('success', 'Product removed from comparison');
    }

    /**
     * Clear all comparisons
     */
    public function clear(Request $request)
    {
        $sessionId = session()->getId();
        $userId = Auth::id();
        
        ProductComparison::where(function($query) use ($userId, $sessionId) {
                             if ($userId) {
                                 $query->where('user_id', $userId);
                             } else {
                                 $query->where('session_id', $sessionId);
                             }
                         })
                         ->delete();
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comparison list cleared',
                'count' => 0
            ]);
        }
        
        return back()->with('success', 'Comparison list cleared');
    }

    /**
     * Get comparison count (for AJAX)
     */
    public function count()
    {
        $sessionId = session()->getId();
        $userId = Auth::id();
        
        $count = ProductComparison::getComparisonCount($sessionId, $userId);
        
        return response()->json(['count' => $count]);
    }
}
