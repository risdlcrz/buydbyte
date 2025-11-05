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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number')->unique()->nullable()->after('order_id');
            $table->string('payment_status')->default('pending')->after('payment_intent_id'); // pending, processing, paid, failed, refunded
            $table->string('payment_method')->nullable()->after('payment_status'); // card, gcash, cod
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_number', 'payment_status', 'payment_method']);
        });
    }
};
