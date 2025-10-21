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
        Schema::create('product_comparisons', function (Blueprint $table) {
            $table->uuid('comparison_id')->primary();
            $table->string('session_id')->nullable(); // For guest users
            $table->uuid('user_id')->nullable(); // For authenticated users
            $table->uuid('product_id');
            $table->timestamps();
            
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            
            $table->unique(['session_id', 'product_id'], 'unique_session_product');
            $table->unique(['user_id', 'product_id'], 'unique_user_product');
            $table->index(['session_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_comparisons');
    }
};
