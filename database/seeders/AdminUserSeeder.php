<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@buydbyte.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create sample customer
        $customer = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create sample categories
        $electronics = Category::create([
            'name' => 'Electronics',
            'description' => 'Electronic devices and gadgets',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $computers = Category::create([
            'name' => 'Computers',
            'description' => 'Laptops, desktops, and computer accessories',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $accessories = Category::create([
            'name' => 'Accessories',
            'description' => 'Various electronic accessories',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Create sample products
        Product::create([
            'name' => 'Gaming Laptop Pro',
            'description' => 'High-performance gaming laptop with latest graphics card and fast processor.',
            'short_description' => 'Ultimate gaming laptop for professionals',
            'sku' => 'LAPTOP-001',
            'price' => 1299.99,
            'sale_price' => 1199.99,
            'stock_quantity' => 15,
            'category_id' => $computers->category_id,
            'is_active' => true,
            'is_featured' => true,
        ]);

        Product::create([
            'name' => 'Wireless Bluetooth Headphones',
            'description' => 'Premium wireless headphones with noise cancellation and long battery life.',
            'short_description' => 'Premium wireless headphones',
            'sku' => 'HEADPHONES-001',
            'price' => 199.99,
            'stock_quantity' => 50,
            'category_id' => $accessories->category_id,
            'is_active' => true,
            'is_featured' => true,
        ]);

        Product::create([
            'name' => 'Smartphone 5G',
            'description' => 'Latest 5G smartphone with advanced camera and all-day battery.',
            'short_description' => 'Latest 5G smartphone',
            'sku' => 'PHONE-001',
            'price' => 899.99,
            'sale_price' => 799.99,
            'stock_quantity' => 25,
            'category_id' => $electronics->category_id,
            'is_active' => true,
            'is_featured' => true,
        ]);

        Product::create([
            'name' => 'USB-C Hub',
            'description' => 'Multi-port USB-C hub with HDMI, USB 3.0, and charging ports.',
            'short_description' => 'Multi-port USB-C hub',
            'sku' => 'HUB-001',
            'price' => 49.99,
            'stock_quantity' => 100,
            'category_id' => $accessories->category_id,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Desktop Computer',
            'description' => 'Powerful desktop computer for work and creativity.',
            'short_description' => 'Powerful desktop computer',
            'sku' => 'DESKTOP-001',
            'price' => 899.99,
            'stock_quantity' => 8,
            'category_id' => $computers->category_id,
            'is_active' => true,
        ]);
    }
}
