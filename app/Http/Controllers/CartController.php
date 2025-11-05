<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $total = $cartItems->sum('total');

        return view('storefront.cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        // Validate quantity with manual check to avoid Brick\Math internal class issues
        $quantity = $request->input('quantity');
        
        if (!isset($quantity) || !is_numeric($quantity)) {
            return back()->with('error', 'Quantity is required and must be a number.');
        }
        
        $quantity = (int) $quantity;
        
        if ($quantity < 1) {
            return back()->with('error', 'Quantity must be at least 1.');
        }

        if (!$product->is_active || !$product->in_stock) {
            return back()->with('error', 'Product is not available.');
        }

        if ($product->manage_stock && $quantity > $product->stock_quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        $cartData = [
            'product_id' => $product->product_id,
            'quantity' => $quantity,
            'price' => $product->current_price,
        ];

        if (Auth::check()) {
            $cartData['user_id'] = Auth::user()->user_id;
            
            // Check if item already exists in cart
            $existingCart = Cart::where('user_id', Auth::user()->user_id)
                ->where('product_id', $product->product_id)
                ->first();

            if ($existingCart) {
                $newQuantity = $existingCart->quantity + $quantity;
                
                if ($product->manage_stock && $newQuantity > $product->stock_quantity) {
                    return back()->with('error', 'Not enough stock available.');
                }
                
                $existingCart->update(['quantity' => $newQuantity]);
            } else {
                Cart::create($cartData);
            }
        } else {
            $cartData['session_id'] = session()->getId();
            
            // Check if item already exists in cart
            $existingCart = Cart::where('session_id', session()->getId())
                ->where('product_id', $product->product_id)
                ->first();

            if ($existingCart) {
                $newQuantity = $existingCart->quantity + $quantity;
                
                if ($product->manage_stock && $newQuantity > $product->stock_quantity) {
                    return back()->with('error', 'Not enough stock available.');
                }
                
                $existingCart->update(['quantity' => $newQuantity]);
            } else {
                Cart::create($cartData);
            }
        }

        return back()->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request, Cart $cart)
    {
        // Validate quantity with manual check to avoid Brick\Math internal class issues
        $quantity = $request->input('quantity');
        
        if (!isset($quantity) || !is_numeric($quantity)) {
            return back()->with('error', 'Quantity is required and must be a number.');
        }
        
        $quantity = (int) $quantity;
        
        if ($quantity < 1) {
            return back()->with('error', 'Quantity must be at least 1.');
        }

        // Verify ownership
        if (Auth::check() && $cart->user_id !== Auth::user()->user_id) {
            abort(403);
        } elseif (!Auth::check() && $cart->session_id !== session()->getId()) {
            abort(403);
        }

        $product = $cart->product;
        
        if (!$product) {
            return back()->with('error', 'Product not found.');
        }
        
        if ($product->manage_stock && $quantity > $product->stock_quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        $cart->update(['quantity' => $quantity]);

        return back()->with('success', 'Cart updated successfully!');
    }

    public function remove(Cart $cart)
    {
        // Verify ownership
        if (Auth::check() && $cart->user_id !== Auth::user()->user_id) {
            abort(403);
        } elseif (!Auth::check() && $cart->session_id !== session()->getId()) {
            abort(403);
        }

        $cart->delete();

        return back()->with('success', 'Item removed from cart!');
    }

    public function clear()
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::user()->user_id)->delete();
        } else {
            Cart::where('session_id', session()->getId())->delete();
        }

        return back()->with('success', 'Cart cleared successfully!');
    }

    public function count()
    {
        $count = 0;
        if (Auth::check()) {
            $count = Cart::where('user_id', Auth::user()->user_id)->sum('quantity');
        } else {
            $count = Cart::where('session_id', session()->getId())->sum('quantity');
        }
        return response()->json(['count' => $count]);
    }

    private function getCartItems()
    {
        if (Auth::check()) {
            return Cart::with('product.category')
                ->where('user_id', Auth::user()->user_id)
                ->get();
        } else {
            return Cart::with('product.category')
                ->where('session_id', session()->getId())
                ->get();
        }
    }

    public function getCartCount()
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::user()->user_id)->sum('quantity');
        } else {
            return Cart::where('session_id', session()->getId())->sum('quantity');
        }
    }
}
