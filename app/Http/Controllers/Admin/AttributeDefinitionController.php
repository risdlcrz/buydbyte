<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttributeDefinition;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttributeDefinitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AttributeDefinition::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filter by group
        if ($request->filled('group')) {
            $query->where('attribute_group', $request->group);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $attributes = $query->orderBy('attribute_group')
                           ->orderBy('sort_order')
                           ->orderBy('name')
                           ->paginate(20);
        
        $groups = AttributeDefinition::distinct()
                                   ->pluck('attribute_group')
                                   ->filter()
                                   ->sort();
        
        return view('admin.attributes.index', compact('attributes', 'groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dataTypes = ['text', 'number', 'decimal', 'boolean', 'select'];
        $groups = ['general', 'performance', 'physical', 'compatibility', 'connectivity', 'cooling'];
        $categories = ['all', 'cpu', 'gpu', 'ram', 'ssd', 'hdd', 'motherboard', 'psu', 'case', 'cpu_cooler', 'peripherals'];
        
        return view('admin.attributes.create', compact('dataTypes', 'groups', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:attribute_definitions,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'data_type' => 'required|in:text,number,decimal,boolean,select',
            'unit' => 'nullable|string|max:50',
            'possible_values' => 'nullable|array',
            'possible_values.*' => 'string|max:255',
            'applicable_categories' => 'required|array|min:1',
            'applicable_categories.*' => 'string|max:100',
            'attribute_group' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_filterable' => 'boolean',
            'is_comparable' => 'boolean',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ]);
        
        // Clean up possible values for non-select types
        if ($validated['data_type'] !== 'select') {
            $validated['possible_values'] = null;
        }
        
        // Set default sort order
        if (empty($validated['sort_order'])) {
            $maxSort = AttributeDefinition::where('attribute_group', $validated['attribute_group'])
                                         ->max('sort_order') ?? 0;
            $validated['sort_order'] = $maxSort + 10;
        }
        
        AttributeDefinition::create($validated);
        
        return redirect()->route('admin.attributes.index')
                        ->with('success', 'Attribute definition created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(AttributeDefinition $attribute)
    {
        $attribute->load(['productAttributes.product']);
        
        return view('admin.attributes.show', compact('attribute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttributeDefinition $attribute)
    {
        $dataTypes = ['text', 'number', 'decimal', 'boolean', 'select'];
        $groups = ['general', 'performance', 'physical', 'compatibility', 'connectivity', 'cooling'];
        $categories = ['all', 'cpu', 'gpu', 'ram', 'ssd', 'hdd', 'motherboard', 'psu', 'case', 'cpu_cooler', 'peripherals'];
        
        return view('admin.attributes.edit', compact('attribute', 'dataTypes', 'groups', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttributeDefinition $attribute)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('attribute_definitions', 'name')->ignore($attribute->attribute_id, 'attribute_id')],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'data_type' => 'required|in:text,number,decimal,boolean,select',
            'unit' => 'nullable|string|max:50',
            'possible_values' => 'nullable|array',
            'possible_values.*' => 'string|max:255',
            'applicable_categories' => 'required|array|min:1',
            'applicable_categories.*' => 'string|max:100',
            'attribute_group' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_filterable' => 'boolean',
            'is_comparable' => 'boolean',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ]);
        
        // Clean up possible values for non-select types
        if ($validated['data_type'] !== 'select') {
            $validated['possible_values'] = null;
        }
        
        $attribute->update($validated);
        
        return redirect()->route('admin.attributes.index')
                        ->with('success', 'Attribute definition updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttributeDefinition $attribute)
    {
        // Check if attribute is being used by products
        $usageCount = $attribute->productAttributes()->count();
        
        if ($usageCount > 0) {
            return redirect()->route('admin.attributes.index')
                           ->with('error', "Cannot delete attribute '{$attribute->display_name}' because it's used by {$usageCount} products.");
        }
        
        $attribute->delete();
        
        return redirect()->route('admin.attributes.index')
                        ->with('success', 'Attribute definition deleted successfully!');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(AttributeDefinition $attribute)
    {
        $attribute->update(['is_active' => !$attribute->is_active]);
        
        $status = $attribute->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
                        ->with('success', "Attribute '{$attribute->display_name}' {$status} successfully!");
    }
}
