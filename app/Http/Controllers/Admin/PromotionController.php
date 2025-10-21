<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promotions = Promotion::orderBy('sort_order')
                              ->orderBy('created_at', 'desc')
                              ->paginate(20);
        
        return view('admin.promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.promotions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:banner,popup,discount',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'background_color' => 'required|string|max:7',
            'text_color' => 'required|string|max:7',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|url',
            'button_color' => 'required|string|max:7',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_code' => 'nullable|string|max:50|unique:promotions',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'target_audience' => 'required|in:all,new_users,returning_users',
            'display_pages' => 'array',
            'display_pages.*' => 'in:all,homepage,products,categories,product_detail',
        ]);

        // Handle image upload
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('promotions', 'public');
        }

        Promotion::create($validated);

        return redirect()->route('admin.promotions.index')
                        ->with('success', 'Promotion created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promotion)
    {
        return view('admin.promotions.show', compact('promotion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion)
    {
        return view('admin.promotions.edit', compact('promotion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:banner,popup,discount',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'background_color' => 'required|string|max:7',
            'text_color' => 'required|string|max:7',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|url',
            'button_color' => 'required|string|max:7',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_code' => 'nullable|string|max:50|unique:promotions,discount_code,' . $promotion->promotion_id . ',promotion_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'target_audience' => 'required|in:all,new_users,returning_users',
            'display_pages' => 'array',
            'display_pages.*' => 'in:all,homepage,products,categories,product_detail',
        ]);

        // Handle image upload
        if ($request->hasFile('banner_image')) {
            // Delete old image if exists
            if ($promotion->banner_image) {
                Storage::disk('public')->delete($promotion->banner_image);
            }
            $validated['banner_image'] = $request->file('banner_image')->store('promotions', 'public');
        }

        $promotion->update($validated);

        return redirect()->route('admin.promotions.index')
                        ->with('success', 'Promotion updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        // Delete associated image
        if ($promotion->banner_image) {
            Storage::disk('public')->delete($promotion->banner_image);
        }

        $promotion->delete();

        return redirect()->route('admin.promotions.index')
                        ->with('success', 'Promotion deleted successfully.');
    }
}