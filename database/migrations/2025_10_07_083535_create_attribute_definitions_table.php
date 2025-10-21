<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attribute_definitions', function (Blueprint $table) {
            $table->uuid('attribute_id')->primary();
            $table->string('name'); // e.g., "Brand", "Power Consumption", "Socket Type"
            $table->string('slug')->unique(); // e.g., "brand", "power_consumption", "socket_type"
            $table->string('display_name'); // e.g., "Brand", "Power Consumption (W)", "Socket Type"
            $table->text('description')->nullable();
            $table->enum('data_type', ['text', 'number', 'decimal', 'boolean', 'select', 'multiselect'])->default('text');
            $table->string('unit')->nullable(); // e.g., "W", "MHz", "GB", "inches"
            $table->json('validation_rules')->nullable(); // Store validation rules as JSON
            $table->json('possible_values')->nullable(); // For select/multiselect types
            $table->json('applicable_categories')->nullable(); // Which product categories this applies to
            $table->string('attribute_group')->default('general'); // e.g., "general", "performance", "physical", "connectivity"
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_filterable')->default(false); // Can be used in product filters
            $table->boolean('is_comparable')->default(true); // Show in comparison tables
            $table->boolean('is_required')->default(false); // Required for products
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
            $table->index(['attribute_group', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_definitions');
    }
};
