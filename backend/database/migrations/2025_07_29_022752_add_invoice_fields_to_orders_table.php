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
            if (!Schema::hasColumn('orders', 'invoice_url')) {
                $table->string('invoice_url')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('orders', 'invoice_generated_at')) {
                $table->timestamp('invoice_generated_at')->nullable()->after('invoice_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'invoice_url')) {
                $table->dropColumn('invoice_url');
            }
            if (Schema::hasColumn('orders', 'invoice_generated_at')) {
                $table->dropColumn('invoice_generated_at');
            }
        });
    }
};