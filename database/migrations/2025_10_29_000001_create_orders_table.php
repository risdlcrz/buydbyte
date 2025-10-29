<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('order_id')->primary();
            $table->uuid('user_id');
            $table->string('status');
            $table->string('shipping_method');
            $table->decimal('shipping_cost', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('tracking_number')->nullable();
            $table->json('shipping_address');
            $table->string('payment_intent_id')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('order_item_id')->primary();
            $table->uuid('order_id');
            $table->uuid('product_id');
            $table->string('product_name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->timestamps();
            
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });

        Schema::create('order_tracking', function (Blueprint $table) {
            $table->uuid('tracking_id')->primary();
            $table->uuid('order_id');
            $table->string('status');
            $table->text('description');
            $table->string('location')->nullable();
            $table->timestamps();
            
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_tracking');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};