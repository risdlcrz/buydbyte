<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_categories' => Category::count(),
            'low_stock_products' => Product::where('stock_quantity', '<=', 10)->count(),
        ];

        $recent_users = User::where('role', 'customer')
            ->latest()
            ->take(5)
            ->get();

        $recent_products = Product::with('category')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_users', 'recent_products'));
    }
}
