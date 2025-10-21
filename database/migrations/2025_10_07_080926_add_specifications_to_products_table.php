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
        Schema::table('products', function (Blueprint $table) {
            $table->json('specifications')->nullable()->after('dimensions');
            $table->json('key_features')->nullable()->after('specifications');
            $table->string('brand')->nullable()->after('name');
            $table->string('model')->nullable()->after('brand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['specifications', 'key_features', 'brand', 'model']);
        });
    }
};
