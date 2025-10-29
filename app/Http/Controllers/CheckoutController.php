<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        // Load selected cart items from session (set by checkout.start)
        $selected = session('checkout.selected_items', null);

        if (empty($selected)) {
            return redirect()->route('cart.index')->with('error', 'No items selected for checkout.');
        }

        $selectedIds = is_array($selected) ? $selected : explode(',', (string) $selected);

        // Ensure we only load items that belong to the authenticated user
        $cartItems = Cart::whereIn('cart_id', $selectedIds)
            ->where('user_id', Auth::id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Selected cart items could not be found.');
        }

        $total = $cartItems->sum('total');

        // Get available shipping methods
        $shippingMethods = $this->getShippingMethods($cartItems);

        return view('storefront.checkout.index', compact('cartItems', 'total', 'shippingMethods'));
    }

    /**
     * Start checkout: accept selected cart ids, store them in session and redirect to checkout page.
     */
    public function start(Request $request)
    {
        $request->validate([
            'selected_items' => 'required|string'
        ]);

        // Store selected cart ids in session for the authenticated user
        $selected = $request->input('selected_items');
        session(['checkout.selected_items' => $selected]);

        return redirect()->route('checkout.index');
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