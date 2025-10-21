<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorefrontController extends Controller
{
    public function index()
    {
        // Get active promotions for homepage
        $promotions = Promotion::active()
                              ->forPage('homepage')
                              ->forAudience(Auth::user())
                              ->ordered()
                              ->get();

        $featured_products = Product::with(['category', 'activeAttributes.attributeDefinition'])
            ->active()
            ->featured()
            ->inStock()
            ->take(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $latest_products = Product::with(['category', 'activeAttributes.attributeDefinition'])
            ->active()
            ->inStock()
            ->latest()
            ->take(8)
            ->get();

        return view('storefront.index', compact('featured_products', 'categories', 'latest_products', 'promotions'));
    }

    public function products(Request $request)
    {
        // Get active promotions for products page
        $promotions = Promotion::active()
                              ->forPage('products')
                              ->forAudience(Auth::user())
                              ->ordered()
                              ->get();

        $query = Product::with(['category', 'activeAttributes.attributeDefinition'])->active()->inStock();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('storefront.products.index', compact('products', 'categories', 'promotions'));
    }

    public function product(Product $product)
    {
        if (!$product->is_active || !$product->in_stock) {
            abort(404);
        }

        // Get active promotions for product detail pages
        $promotions = Promotion::active()
                              ->forPage('product_detail')
                              ->forAudience(Auth::user())
                              ->ordered()
                              ->get();

        $product->load(['category', 'activeAttributes.attributeDefinition']);
        
        $related_products = Product::with(['category', 'activeAttributes.attributeDefinition'])
            ->where('category_id', $product->category_id)
            ->where('product_id', '!=', $product->product_id)
            ->active()
            ->inStock()
            ->take(4)
            ->get();

        return view('storefront.products.show', compact('product', 'related_products', 'promotions'));
    }

    public function category(Category $category)
    {
        if (!$category->is_active) {
            abort(404);
        }

        // Get active promotions for category pages
        $promotions = Promotion::active()
                              ->forPage('categories')
                              ->forAudience(Auth::user())
                              ->ordered()
                              ->get();

        $products = Product::with(['category', 'activeAttributes.attributeDefinition'])
            ->where('category_id', $category->category_id)
            ->active()
            ->inStock()
            ->paginate(12);

        return view('storefront.category', compact('category', 'products', 'promotions'));
    }
}
