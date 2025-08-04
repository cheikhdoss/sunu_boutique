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
            $table->string('slug')->unique()->nullable()->after('name');
            $table->string('sku')->unique()->nullable()->after('slug');
            $table->renameColumn('stock', 'quantity');
            $table->json('images')->nullable()->after('image');
            $table->boolean('is_visible')->default(false)->after('images');
            $table->boolean('is_featured')->default(false)->after('is_visible');
            $table->enum('type', ['physical', 'digital'])->default('physical')->after('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['slug', 'sku', 'images', 'is_visible', 'is_featured', 'type']);
            $table->renameColumn('quantity', 'stock');
        });
    }
};
