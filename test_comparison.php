<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\ProductAttribute;

echo "=== DYNAMIC ATTRIBUTES COMPARISON DEMO ===\n\n";

// Get CPUs for comparison
$cpus = Product::where('name', 'like', '%Core%')
    ->orWhere('name', 'like', '%Ryzen%')
    ->get();

if ($cpus->count() >= 2) {
    $cpu1 = $cpus->first();
    $cpu2 = $cpus->last();
    
    echo "Comparing: {$cpu1->name} vs {$cpu2->name}\n";
    echo str_repeat("=", 60) . "\n";
    
    $productIds = [$cpu1->product_id, $cpu2->product_id];
    $attributes = ProductAttribute::getComparableAttributes($productIds);
    
    foreach ($attributes as $slug => $attributeData) {
        $definition = $attributeData->first()->attributeDefinition;
        
        echo "\n{$definition->display_name}:\n";
        
        $cpu1Attr = $attributeData->where('product_id', $cpu1->product_id)->first();
        $cpu2Attr = $attributeData->where('product_id', $cpu2->product_id)->first();
        
        $cpu1Value = $cpu1Attr ? $definition->formatValue($cpu1Attr->value) : 'N/A';
        $cpu2Value = $cpu2Attr ? $definition->formatValue($cpu2Attr->value) : 'N/A';
        
        echo "  {$cpu1->name}: {$cpu1Value}\n";
        echo "  {$cpu2->name}: {$cpu2Value}\n";
    }
}

echo "\n\n=== BENEFITS OF DYNAMIC ATTRIBUTES ===\n";
echo "✅ No hardcoded specification fields\n";
echo "✅ Works across ALL product categories (CPU, GPU, RAM, etc.)\n";
echo "✅ Easy to add new attributes without code changes\n";
echo "✅ Proper data types and formatting (numbers, units, selections)\n";
echo "✅ Filterable and comparable attributes\n";
echo "✅ Consistent comparison interface for any product type\n";