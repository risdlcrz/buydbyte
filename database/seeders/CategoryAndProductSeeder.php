<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryAndProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories
        $categories = [
            [
                'name' => 'Laptops',
                'description' => 'Portable computers for every need',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Desktop PCs',
                'description' => 'Powerful desktop computers',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Components',
                'description' => 'Computer parts and components',
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Peripherals',
                'description' => 'Computer peripherals and accessories',
                'is_active' => true,
                'sort_order' => 4
            ]
        ];

        foreach ($categories as $categoryData) {
            Category::create([
                'category_id' => Str::uuid()->toString(),
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'is_active' => $categoryData['is_active'],
                'sort_order' => $categoryData['sort_order']
            ]);
        }

        // Get category IDs
        $laptopsCat = Category::where('slug', 'laptops')->first();
        $desktopsCat = Category::where('slug', 'desktop-pcs')->first();
        $componentsCat = Category::where('slug', 'components')->first();
        $peripheralsCat = Category::where('slug', 'peripherals')->first();

        // Create Products
        $products = [
            // Laptops
            [
                'name' => 'MacBook Pro 16"',
                'brand' => 'Apple',
                'model' => 'M2 Pro',
                'description' => 'Powerful MacBook Pro with M2 Pro chip',
                'price' => 2499.99,
                'category_id' => $laptopsCat->category_id,
                'is_featured' => true
            ],
            [
                'name' => 'Dell XPS 15',
                'brand' => 'Dell',
                'model' => 'XPS 15 9530',
                'description' => 'Premium Windows laptop with stunning display',
                'price' => 1999.99,
                'category_id' => $laptopsCat->category_id,
                'is_featured' => true
            ],
            // Desktop PCs
            [
                'name' => 'Gaming PC Pro',
                'brand' => 'Custom',
                'model' => 'GPC-2025',
                'description' => 'High-end gaming desktop computer',
                'price' => 2999.99,
                'category_id' => $desktopsCat->category_id,
                'is_featured' => true
            ],
            // Components
            [
                'name' => 'NVIDIA RTX 4090',
                'brand' => 'NVIDIA',
                'model' => 'RTX 4090',
                'description' => 'High-end graphics card',
                'price' => 1599.99,
                'category_id' => $componentsCat->category_id,
                'is_featured' => true
            ],
            [
                'name' => 'Intel Core i9-13900K',
                'brand' => 'Intel',
                'model' => 'i9-13900K',
                'description' => 'High-performance CPU',
                'price' => 599.99,
                'category_id' => $componentsCat->category_id,
                'is_featured' => true
            ],
            // Peripherals
            [
                'name' => 'Logitech G Pro X',
                'brand' => 'Logitech',
                'model' => 'G Pro X',
                'description' => 'Gaming keyboard with mechanical switches',
                'price' => 149.99,
                'category_id' => $peripheralsCat->category_id,
                'is_featured' => false
            ],
            [
                'name' => 'Samsung Odyssey G9',
                'brand' => 'Samsung',
                'model' => 'Odyssey G9',
                'description' => '49" Ultra-wide gaming monitor',
                'price' => 1299.99,
                'category_id' => $peripheralsCat->category_id,
                'is_featured' => true
            ]
        ];

        foreach ($products as $productData) {
            Product::create([
                'product_id' => Str::uuid()->toString(),
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'brand' => $productData['brand'],
                'model' => $productData['model'],
                'description' => $productData['description'],
                'short_description' => substr($productData['description'], 0, 100),
                'price' => $productData['price'],
                'stock_quantity' => 100,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => $productData['is_featured'],
                'category_id' => $productData['category_id'],
                'images' => ['placeholder.jpg'],
                'specifications' => [
                    'brand' => $productData['brand'],
                    'model' => $productData['model']
                ]
            ]);
        }
    }
}