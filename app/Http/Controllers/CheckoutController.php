<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Address;
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

        // Load user addresses for the modal
        $user = Auth::user();
        $addresses = $user ? $user->addresses()->get() : collect();
        $defaultAddress = $user ? $user->defaultAddress()->first() : null;

        return view('storefront.checkout.index', compact('cartItems', 'total', 'shippingMethods', 'addresses', 'defaultAddress'));
    }

    /**
     * Store a new address for the authenticated user (AJAX expected)
     */
    public function storeAddress(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'street' => 'required|string|max:1024',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        $address = new Address();
        $address->user_id = $user->user_id;
        $address->full_name = $request->input('full_name');
        $address->street = $request->input('street');
        $address->city = $request->input('city');
        $address->state = $request->input('state');
        $address->postal_code = $request->input('postal_code');
        $address->country = $request->input('country', 'Philippines');
        $address->is_default = $request->has('is_default') ? boolval($request->input('is_default')) : false;
        $address->save();

        // If marked default, clear others
        if ($address->is_default) {
            Address::where('user_id', $user->user_id)->where('address_id', '!=', $address->address_id)->update(['is_default' => false]);
        }

        return response()->json(['message' => 'Address saved', 'address' => $address], 201);
    }

    /**
     * Update an existing address (AJAX expected)
     */
    public function updateAddress(Request $request, $addressId)
    {
        $user = Auth::user();
        $address = Address::where('address_id', $addressId)->where('user_id', $user->user_id)->firstOrFail();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'street' => 'required|string|max:1024',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:255',
        ]);

        $address->full_name = $request->input('full_name');
        $address->street = $request->input('street');
        $address->city = $request->input('city');
        $address->state = $request->input('state');
        $address->postal_code = $request->input('postal_code');
        $address->country = $request->input('country', 'Philippines');
        $address->is_default = $request->has('is_default') ? boolval($request->input('is_default')) : $address->is_default;
        $address->save();

        if ($address->is_default) {
            Address::where('user_id', $user->user_id)->where('address_id', '!=', $address->address_id)->update(['is_default' => false]);
        }

        return response()->json(['message' => 'Address updated', 'address' => $address], 200);
    }

    /**
     * Destroy / delete an address belonging to the user
     */
    public function destroyAddress(Request $request, $addressId)
    {
        $user = Auth::user();
        $address = Address::where('address_id', $addressId)->where('user_id', $user->user_id)->firstOrFail();
        $address->delete();
        return response()->json(['message' => 'Address deleted'], 200);
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

        // Load addresses for the modal
        $user = Auth::user();
        $addresses = $user ? $user->addresses()->get() : collect();
        $defaultAddress = $user ? $user->defaultAddress()->first() : null;

        return view('storefront.checkout.index', compact('cartItems', 'total', 'shippingMethods', 'addresses', 'defaultAddress'));
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