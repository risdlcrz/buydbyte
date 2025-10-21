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
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->uuid('product_attribute_id')->primary();
            $table->uuid('product_id');
            $table->uuid('attribute_id');
            $table->text('value'); // Store all values as text, will be cast based on attribute definition
            $table->decimal('numeric_value', 15, 4)->nullable(); // For easy sorting/filtering of numeric values
            $table->timestamps();
            
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            $table->foreign('attribute_id')->references('attribute_id')->on('attribute_definitions')->onDelete('cascade');
            
            $table->unique(['product_id', 'attribute_id'], 'unique_product_attribute');
            $table->index(['attribute_id', 'numeric_value']);
            $table->index(['product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
