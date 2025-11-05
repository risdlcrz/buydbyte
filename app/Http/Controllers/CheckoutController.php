<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTracking;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\PaymentStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
            ->where('user_id', Auth::user()->user_id)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Selected cart items could not be found.');
        }

        // Validate that all products are still available
        foreach ($cartItems as $cartItem) {
            if (!$cartItem->product) {
                return redirect()->route('cart.index')->with('error', 'One or more products are no longer available.');
            }
            
            if (!$cartItem->product->is_active || !$cartItem->product->in_stock) {
                return redirect()->route('cart.index')
                    ->with('error', "Product {$cartItem->product->name} is no longer available.");
            }
            
            if ($cartItem->product->manage_stock && $cartItem->quantity > $cartItem->product->stock_quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "Insufficient stock for {$cartItem->product->name}");
            }
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
        try {
            $request->validate([
                'selected_items' => 'required|string',
                'shipping_method' => 'required|string',
                'address_id' => 'required|string',
                'payment_method' => 'required|string|in:card,gcash,cod',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'message' => $e->getMessage(),
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $selectedItems = explode(',', $request->selected_items);
        
        // Verify cart items belong to user
        $cartItems = Cart::whereIn('cart_id', $selectedItems)
            ->where('user_id', Auth::user()->user_id)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'No items selected for checkout'], 400);
            }
            return back()->with('error', 'No items selected for checkout');
        }

        // Validate that all products are still available
        foreach ($cartItems as $cartItem) {
            if (!$cartItem->product) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['error' => "Product not found for cart item {$cartItem->cart_id}"], 400);
                }
                return back()->with('error', 'One or more products are no longer available.');
            }
            
            if (!$cartItem->product->is_active || !$cartItem->product->in_stock) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['error' => "Product {$cartItem->product->name} is no longer available"], 400);
                }
                return back()->with('error', "Product {$cartItem->product->name} is no longer available.");
            }
            
            if ($cartItem->product->manage_stock && $cartItem->quantity > $cartItem->product->stock_quantity) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['error' => "Insufficient stock for {$cartItem->product->name}"], 400);
                }
                return back()->with('error', "Insufficient stock for {$cartItem->product->name}");
            }
        }

        // Get shipping method details
        $shippingMethods = $this->getShippingMethods($cartItems);
        $selectedShipping = collect($shippingMethods)->firstWhere('id', $request->shipping_method);
        
        if (!$selectedShipping) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Invalid shipping method selected'], 400);
            }
            return back()->with('error', 'Invalid shipping method selected');
        }

        // Get address
        $address = Address::where('address_id', $request->address_id)
            ->where('user_id', Auth::user()->user_id)
            ->first();
        
        if (!$address) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Delivery address not found'], 404);
            }
            return back()->with('error', 'Delivery address not found');
        }

        // Calculate totals
        $subtotal = $cartItems->sum('total');
        $shippingCost = $selectedShipping['price'];
        $total = $subtotal + $shippingCost;

        // Process payment
        $paymentStatus = $this->processPayment($request->payment_method, $total);
        
        if ($paymentStatus === 'failed') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Payment processing failed. Please try again.'], 400);
            }
            return back()->with('error', 'Payment processing failed. Please try again.');
        }

        // Create order in a transaction
        try {
            DB::beginTransaction();

            // Create order
            $order = new Order();
            $order->user_id = Auth::user()->user_id;
            $order->status = 'pending';
            $order->shipping_method = $request->shipping_method;
            $order->shipping_cost = $shippingCost;
            $order->subtotal = $subtotal;
            $order->total = $total;
            $order->payment_status = $paymentStatus;
            $order->payment_method = $request->payment_method;
            $order->shipping_address = [
                'full_name' => isset($address->full_name) ? $address->full_name : '',
                'street' => isset($address->street) ? $address->street : '',
                'city' => isset($address->city) ? $address->city : '',
                'state' => isset($address->state) ? $address->state : '',
                'postal_code' => isset($address->postal_code) ? $address->postal_code : '',
                'country' => isset($address->country) ? $address->country : 'Philippines',
            ];
            
            // Generate payment intent ID for card payments
            if ($request->payment_method === 'card' && $paymentStatus === 'paid') {
                $order->payment_intent_id = 'pi_' . strtoupper(\Illuminate\Support\Str::random(24));
            }
            
            $order->save();

            // Create order items
            foreach ($cartItems as $cartItem) {
                if (!$cartItem->product) {
                    throw new \Exception("Product not found for cart item {$cartItem->cart_id}");
                }
                
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->order_id;
                $orderItem->product_id = $cartItem->product_id;
                $orderItem->product_name = isset($cartItem->product->name) ? $cartItem->product->name : 'Unknown Product';
                $orderItem->price = $cartItem->price;
                $orderItem->quantity = $cartItem->quantity;
                $orderItem->save();
            }

            // Create initial tracking entry
            $tracking = new OrderTracking();
            $tracking->order_id = $order->order_id;
            $tracking->status = 'pending';
            $tracking->description = 'Order placed and awaiting processing';
            $tracking->location = 'Warehouse';
            $tracking->save();

            // Remove cart items
            Cart::whereIn('cart_id', $selectedItems)->delete();

            // Clear checkout session
            session()->forget('checkout.selected_items');

            DB::commit();

            // Refresh order to get relationships
            $order->refresh();
            $order->load('user');

            // Send notifications (wrapped in try-catch to prevent notification failures from breaking order)
            try {
                if (isset($order->user) && $order->user) {
                    $order->user->notify(new OrderPlacedNotification($order));
                    
                    if ($paymentStatus === 'paid') {
                        $order->user->notify(new PaymentStatusNotification($order, 'paid', $total, $request->payment_method));
                    }
                }
            } catch (\Exception $notificationError) {
                \Log::warning('Notification failed for order ' . $order->order_id . ': ' . $notificationError->getMessage());
                // Don't fail the order if notification fails
            }

            // Handle AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully! Order #' . $order->order_number,
                    'order_id' => $order->order_id,
                    'order_number' => $order->order_number,
                    'redirect' => route('customer.orders.show', $order->order_id)
                ]);
            }

            return redirect()->route('customer.orders.show', $order->order_id)
                ->with('success', 'Order placed successfully! Order #' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order processing error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $errorMessage = config('app.debug') 
                ? $e->getMessage() 
                : 'An error occurred while processing your order. Please try again.';
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'error' => $errorMessage,
                    'message' => config('app.debug') ? $e->getMessage() : 'Order processing failed'
                ], 500);
            }
            
            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Simulate payment processing
     */
    private function processPayment($paymentMethod, $amount)
    {
        // Simulate payment processing
        // In production, this would integrate with actual payment gateways
        
        switch ($paymentMethod) {
            case 'card':
                // Simulate card payment - 90% success rate
                return (rand(1, 10) <= 9) ? 'paid' : 'failed';
            
            case 'gcash':
                // GCash payments are pending until confirmed
                return 'processing';
            
            case 'cod':
                // COD payments are pending until delivery
                return 'pending';
            
            default:
                return 'failed';
        }
    }

    // NOTE: Payment processing usually happens in a separate controller/action or via webhook from the payment gateway.
    // Below are examples/placeholders showing where to trigger payment-related notifications:
    // - When an order is created but payment is pending -> send PaymentReminderNotification
    // - When payment is confirmed (webhook/admin confirms) -> send PaymentStatusNotification
    // Example (commented):
    // use App\Notifications\PaymentReminderNotification;
    // $order = // create order here
    // $order->user->notify(new PaymentReminderNotification($order, now()->addDays(3), $order->total));

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