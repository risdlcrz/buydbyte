<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('storefront.checkout.index');
    }

    public function process(Request $request)
    {
        $selectedItems = explode(',', $request->selected_items);
        
        // Verify cart items belong to user
        $cartItems = Cart::whereIn('cart_id', $selectedItems)
            ->where('user_id', auth()->id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'No items selected for checkout');
        }

        $total = $cartItems->sum('total');

        // Get available shipping methods (mock API call)
        $shippingMethods = $this->getShippingMethods($cartItems);

        return view('storefront.checkout.index', compact('cartItems', 'total', 'shippingMethods'));
    }

    private function getShippingMethods($cartItems)
    {
        // Mock shipping methods - in production, this would call a real shipping API
        return [
            [
                'id' => 'standard',
                'name' => 'Standard Shipping',
                'price' => 5.99,
                'estimated_days' => '5-7 business days'
            ],
            [
                'id' => 'express',
                'name' => 'Express Shipping',
                'price' => 15.99,
                'estimated_days' => '2-3 business days'
            ],
            [
                'id' => 'overnight',
                'name' => 'Overnight Shipping',
                'price' => 29.99,
                'estimated_days' => '1 business day'
            ]
        ];
    }
}