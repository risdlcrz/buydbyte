<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\AttributeDefinition;
use App\Models\ProductAttribute;
use Illuminate\Support\Str;

class SampleProductAttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample categories if they don't exist
        $cpuCategory = Category::firstOrCreate(
            ['slug' => 'cpu'],
            [
                'category_id' => Str::uuid(),
                'name' => 'CPUs & Processors',
                'description' => 'Computer processors and CPUs',
                'is_active' => true,
            ]
        );

        $gpuCategory = Category::firstOrCreate(
            ['slug' => 'gpu'],
            [
                'category_id' => Str::uuid(),
                'name' => 'Graphics Cards',
                'description' => 'Graphics cards and GPUs',
                'is_active' => true,
            ]
        );

        $ramCategory = Category::firstOrCreate(
            ['slug' => 'ram'],
            [
                'category_id' => Str::uuid(),
                'name' => 'Memory (RAM)',
                'description' => 'Computer memory and RAM',
                'is_active' => true,
            ]
        );

        // Sample CPUs
        $cpu1 = Product::firstOrCreate(
            ['slug' => 'intel-core-i7-13700k'],
            [
                'product_id' => Str::uuid(),
                'name' => 'Intel Core i7-13700K',
                'brand' => 'Intel',
                'model' => 'i7-13700K',
                'description' => 'High-performance desktop processor',
                'price' => 399.99,
                'stock_quantity' => 50,
                'in_stock' => true,
                'is_active' => true,
                'category_id' => $cpuCategory->category_id,
            ]
        );

        $cpu2 = Product::firstOrCreate(
            ['slug' => 'amd-ryzen-7-7700x'],
            [
                'product_id' => Str::uuid(),
                'name' => 'AMD Ryzen 7 7700X',
                'brand' => 'AMD',
                'model' => '7700X',
                'description' => 'High-performance AMD processor',
                'price' => 349.99,
                'stock_quantity' => 30,
                'in_stock' => true,
                'is_active' => true,
                'category_id' => $cpuCategory->category_id,
            ]
        );

        // Sample GPUs
        $gpu1 = Product::firstOrCreate(
            ['slug' => 'nvidia-rtx-4070'],
            [
                'product_id' => Str::uuid(),
                'name' => 'NVIDIA GeForce RTX 4070',
                'brand' => 'NVIDIA',
                'model' => 'RTX 4070',
                'description' => 'High-end graphics card',
                'price' => 599.99,
                'stock_quantity' => 25,
                'in_stock' => true,
                'is_active' => true,
                'category_id' => $gpuCategory->category_id,
            ]
        );

        $gpu2 = Product::firstOrCreate(
            ['slug' => 'amd-rx-7800-xt'],
            [
                'product_id' => Str::uuid(),
                'name' => 'AMD Radeon RX 7800 XT',
                'brand' => 'AMD',
                'model' => 'RX 7800 XT',
                'description' => 'High-performance AMD graphics card',
                'price' => 549.99,
                'stock_quantity' => 20,
                'in_stock' => true,
                'is_active' => true,
                'category_id' => $gpuCategory->category_id,
            ]
        );

        // Sample RAM
        $ram1 = Product::firstOrCreate(
            ['slug' => 'corsair-vengeance-32gb-ddr5'],
            [
                'product_id' => Str::uuid(),
                'name' => 'Corsair Vengeance 32GB DDR5',
                'brand' => 'Corsair',
                'model' => 'Vengeance LPX',
                'description' => 'High-speed DDR5 memory kit',
                'price' => 199.99,
                'stock_quantity' => 40,
                'in_stock' => true,
                'is_active' => true,
                'category_id' => $ramCategory->category_id,
            ]
        );

        // Get attribute definitions
        $attributes = [
            'brand' => AttributeDefinition::where('slug', 'brand')->first(),
            'model' => AttributeDefinition::where('slug', 'model')->first(),
            'cores' => AttributeDefinition::where('slug', 'cores')->first(),
            'threads' => AttributeDefinition::where('slug', 'threads')->first(),
            'base_clock' => AttributeDefinition::where('slug', 'base_clock')->first(),
            'boost_clock' => AttributeDefinition::where('slug', 'boost_clock')->first(),
            'power_consumption' => AttributeDefinition::where('slug', 'power_consumption')->first(),
            'socket' => AttributeDefinition::where('slug', 'socket')->first(),
            'vram' => AttributeDefinition::where('slug', 'vram')->first(),
            'cuda_cores' => AttributeDefinition::where('slug', 'cuda_cores')->first(),
            'memory_type' => AttributeDefinition::where('slug', 'memory_type')->first(),
            'capacity' => AttributeDefinition::where('slug', 'capacity')->first(),
            'memory_speed' => AttributeDefinition::where('slug', 'memory_speed')->first(),
        ];

        // Add attributes for Intel CPU
        $this->addAttribute($cpu1, $attributes['brand'], 'Intel');
        $this->addAttribute($cpu1, $attributes['model'], 'i7-13700K');
        $this->addAttribute($cpu1, $attributes['cores'], '16');
        $this->addAttribute($cpu1, $attributes['threads'], '24');
        $this->addAttribute($cpu1, $attributes['base_clock'], '3400');
        $this->addAttribute($cpu1, $attributes['boost_clock'], '5400');
        $this->addAttribute($cpu1, $attributes['power_consumption'], '125');
        $this->addAttribute($cpu1, $attributes['socket'], 'LGA1700');

        // Add attributes for AMD CPU
        $this->addAttribute($cpu2, $attributes['brand'], 'AMD');
        $this->addAttribute($cpu2, $attributes['model'], '7700X');
        $this->addAttribute($cpu2, $attributes['cores'], '8');
        $this->addAttribute($cpu2, $attributes['threads'], '16');
        $this->addAttribute($cpu2, $attributes['base_clock'], '4500');
        $this->addAttribute($cpu2, $attributes['boost_clock'], '5400');
        $this->addAttribute($cpu2, $attributes['power_consumption'], '105');
        $this->addAttribute($cpu2, $attributes['socket'], 'AM5');

        // Add attributes for NVIDIA GPU
        $this->addAttribute($gpu1, $attributes['brand'], 'NVIDIA');
        $this->addAttribute($gpu1, $attributes['model'], 'RTX 4070');
        $this->addAttribute($gpu1, $attributes['base_clock'], '1920');
        $this->addAttribute($gpu1, $attributes['boost_clock'], '2475');
        $this->addAttribute($gpu1, $attributes['power_consumption'], '200');
        $this->addAttribute($gpu1, $attributes['vram'], '12');
        $this->addAttribute($gpu1, $attributes['cuda_cores'], '5888');
        $this->addAttribute($gpu1, $attributes['memory_type'], 'GDDR6X');

        // Add attributes for AMD GPU
        $this->addAttribute($gpu2, $attributes['brand'], 'AMD');
        $this->addAttribute($gpu2, $attributes['model'], 'RX 7800 XT');
        $this->addAttribute($gpu2, $attributes['base_clock'], '2124');
        $this->addAttribute($gpu2, $attributes['boost_clock'], '2430');
        $this->addAttribute($gpu2, $attributes['power_consumption'], '263');
        $this->addAttribute($gpu2, $attributes['vram'], '16');
        $this->addAttribute($gpu2, $attributes['memory_type'], 'GDDR6');

        // Add attributes for RAM
        $this->addAttribute($ram1, $attributes['brand'], 'Corsair');
        $this->addAttribute($ram1, $attributes['model'], 'Vengeance LPX');
        $this->addAttribute($ram1, $attributes['capacity'], '32');
        $this->addAttribute($ram1, $attributes['memory_speed'], '5600');
        $this->addAttribute($ram1, $attributes['memory_type'], 'DDR5');
    }

    private function addAttribute($product, $attributeDefinition, $value)
    {
        if ($attributeDefinition) {
            ProductAttribute::updateOrCreate(
                [
                    'product_id' => $product->product_id,
                    'attribute_id' => $attributeDefinition->attribute_id,
                ],
                [
                    'value' => $value,
                    'numeric_value' => is_numeric($value) ? $value : null,
                ]
            );
        }
    }
}
