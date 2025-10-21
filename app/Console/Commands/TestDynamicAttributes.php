<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductAttribute;

class TestDynamicAttributes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:attributes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the dynamic attributes functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Dynamic Attributes System');
        $this->info('=====================================');
        
        // Get products with attributes
        $products = Product::with(['attributes.attributeDefinition'])->get();
        
        $this->info("Found {$products->count()} products");
        
        foreach ($products as $product) {
            $this->info("\nProduct: {$product->name}");
            $this->info("Brand: {$product->brand}");
            
            $attributes = $product->attributes()->with('attributeDefinition')->get();
            $this->info("Attributes count: {$attributes->count()}");
            
            foreach ($attributes as $attribute) {
                $definition = $attribute->attributeDefinition;
                if ($definition) {
                    $formattedValue = $definition->formatValue($attribute->value);
                    $this->info("  - {$definition->display_name}: {$formattedValue}");
                }
            }
        }
        
        // Test comparison functionality
        $this->info("\n\nTesting Comparison Functionality");
        $this->info("=================================");
        
        $productIds = $products->take(2)->pluck('product_id')->toArray();
        
        if (count($productIds) >= 2) {
            $comparableAttributes = ProductAttribute::getComparableAttributes($productIds);
            
            $this->info("Comparing products: {$productIds[0]} and {$productIds[1]}");
            $this->info("Comparable attributes found: " . count($comparableAttributes));
            
            foreach ($comparableAttributes as $slug => $attributeData) {
                $definition = $attributeData->first()->attributeDefinition;
                $this->info("\nAttribute: {$definition->display_name}");
                
                foreach ($attributeData as $attr) {
                    $product = $attr->product;
                    $formattedValue = $definition->formatValue($attr->value);
                    $this->info("  {$product->name}: {$formattedValue}");
                }
            }
        }
        
        $this->info("\nTest completed successfully!");
        return 0;
    }
}
