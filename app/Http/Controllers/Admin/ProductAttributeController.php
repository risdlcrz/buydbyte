<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\AttributeDefinition;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    /**
     * Display product attributes management page
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'attributes.attributeDefinition']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        $products = $query->orderBy('name')->paginate(15);
        
        $categories = \App\Models\Category::orderBy('name')->get();
        
        return view('admin.product-attributes.index', compact('products', 'categories'));
    }

    /**
     * Show product attributes edit form
     */
    public function edit(Product $product)
    {
        $product->load(['category', 'attributes.attributeDefinition']);
        
        // Get applicable attribute definitions for this product's category
        $categorySlug = $product->category ? $product->category->slug : 'all';
        $availableAttributes = AttributeDefinition::forCategory($categorySlug);
        
        // Get current attribute values indexed by attribute ID
        $currentAttributes = $product->attributes->keyBy('attribute_id');
        
        return view('admin.product-attributes.edit', compact('product', 'availableAttributes', 'currentAttributes'));
    }

    /**
     * Update product attributes
     */
    public function update(Request $request, Product $product)
    {
        $categorySlug = $product->category ? $product->category->slug : 'all';
        $availableAttributes = AttributeDefinition::forCategory($categorySlug);
        
        // Build validation rules dynamically
        $rules = [];
        foreach ($availableAttributes as $attribute) {
            $fieldName = "attributes.{$attribute->attribute_id}";
            
            if ($attribute->is_required) {
                $rules[$fieldName] = 'required';
            } else {
                $rules[$fieldName] = 'nullable';
            }
            
            // Add type-specific validation
            switch ($attribute->data_type) {
                case 'number':
                case 'decimal':
                    $rules[$fieldName] .= '|numeric';
                    break;
                case 'boolean':
                    $rules[$fieldName] .= '|boolean';
                    break;
                case 'select':
                    if ($attribute->possible_values) {
                        $values = implode(',', $attribute->possible_values);
                        $rules[$fieldName] .= "|in:{$values}";
                    }
                    break;
                case 'text':
                default:
                    $rules[$fieldName] .= '|string|max:1000';
                    break;
            }
        }
        
        $validated = $request->validate($rules);
        
        // Update or create product attributes
        foreach ($availableAttributes as $attribute) {
            $value = $validated['attributes'][$attribute->attribute_id] ?? null;
            
            if (!empty($value)) {
                $productAttribute = ProductAttribute::firstOrNew([
                    'product_id' => $product->product_id,
                    'attribute_id' => $attribute->attribute_id,
                ]);
                
                $productAttribute->setValue($value, $attribute);
                $productAttribute->save();
            } else {
                // Remove attribute if value is empty
                ProductAttribute::where('product_id', $product->product_id)
                               ->where('attribute_id', $attribute->attribute_id)
                               ->delete();
            }
        }
        
        return redirect()->route('admin.product-attributes.index')
                        ->with('success', "Attributes for '{$product->name}' updated successfully!");
    }

    /**
     * Bulk update attributes for multiple products
     */
    public function bulkEdit(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,product_id',
            'attribute_id' => 'required|exists:attribute_definitions,attribute_id',
            'value' => 'required|string|max:1000',
        ]);
        
        $attribute = AttributeDefinition::findOrFail($validated['attribute_id']);
        
        // Validate the value based on attribute type
        if (!$attribute->validateValue($validated['value'])) {
            return back()->withErrors(['value' => 'Invalid value for this attribute type.']);
        }
        
        $updated = 0;
        foreach ($validated['product_ids'] as $productId) {
            $productAttribute = ProductAttribute::firstOrNew([
                'product_id' => $productId,
                'attribute_id' => $validated['attribute_id'],
            ]);
            
            $productAttribute->setValue($validated['value'], $attribute);
            $productAttribute->save();
            $updated++;
        }
        
        return back()->with('success', "Updated '{$attribute->display_name}' for {$updated} products.");
    }

    /**
     * Get attribute suggestions for autocomplete
     */
    public function suggestions(Request $request)
    {
        $attributeId = $request->get('attribute_id');
        $query = $request->get('query', '');
        
        if (!$attributeId || !$query) {
            return response()->json([]);
        }
        
        $suggestions = ProductAttribute::where('attribute_id', $attributeId)
                                     ->where('value', 'like', "%{$query}%")
                                     ->distinct()
                                     ->pluck('value')
                                     ->take(10);
        
        return response()->json($suggestions);
    }

    /**
     * Copy attributes from one product to another
     */
    public function copyAttributes(Request $request)
    {
        $validated = $request->validate([
            'source_product_id' => 'required|exists:products,product_id',
            'target_product_ids' => 'required|array|min:1',
            'target_product_ids.*' => 'exists:products,product_id',
            'attribute_ids' => 'nullable|array',
            'attribute_ids.*' => 'exists:attribute_definitions,attribute_id',
        ]);
        
        $sourceProduct = Product::findOrFail($validated['source_product_id']);
        $sourceAttributes = $sourceProduct->attributes();
        
        // Filter by specific attributes if provided
        if (!empty($validated['attribute_ids'])) {
            $sourceAttributes->whereIn('attribute_id', $validated['attribute_ids']);
        }
        
        $sourceAttributes = $sourceAttributes->with('attributeDefinition')->get();
        
        $copiedCount = 0;
        foreach ($validated['target_product_ids'] as $targetProductId) {
            foreach ($sourceAttributes as $sourceAttr) {
                $targetAttribute = ProductAttribute::firstOrNew([
                    'product_id' => $targetProductId,
                    'attribute_id' => $sourceAttr->attribute_id,
                ]);
                
                $targetAttribute->setValue($sourceAttr->value, $sourceAttr->attributeDefinition);
                $targetAttribute->save();
                $copiedCount++;
            }
        }
        
        return back()->with('success', "Copied {$copiedCount} attributes successfully!");
    }
}
