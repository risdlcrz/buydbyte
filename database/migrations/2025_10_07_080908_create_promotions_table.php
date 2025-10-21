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
        Schema::create('promotions', function (Blueprint $table) {
            $table->uuid('promotion_id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type', 50)->default('banner'); // banner, popup, discount
            $table->string('banner_image')->nullable();
            $table->string('background_color', 7)->default('#007bff');
            $table->string('text_color', 7)->default('#ffffff');
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->string('button_color', 7)->default('#28a745');
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->string('discount_code', 50)->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('target_audience', 50)->default('all'); // all, new_users, returning_users
            $table->json('display_pages')->nullable(); // which pages to show: homepage, products, etc.
            $table->timestamps();
            
            $table->index(['is_active', 'start_date', 'end_date']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
