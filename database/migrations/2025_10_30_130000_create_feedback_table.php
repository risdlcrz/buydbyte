<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->uuid('feedback_id')->primary();
            $table->uuid('user_id');
            $table->uuid('order_id')->nullable(); // Optional: if feedback is related to an order
            $table->uuid('product_id')->nullable(); // Optional: if feedback is related to a product
            $table->string('type'); // 'general', 'order', 'product', 'service'
            $table->integer('rating')->nullable(); // 1-5 star rating
            $table->text('comment');
            $table->string('status')->default('pending'); // pending, reviewed, resolved
            $table->text('admin_response')->nullable();
            $table->uuid('admin_id')->nullable(); // admin who responded
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('order_id')->references('order_id')->on('orders')->nullOnDelete();
            $table->foreign('product_id')->references('product_id')->on('products')->nullOnDelete();
            $table->foreign('admin_id')->references('user_id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};