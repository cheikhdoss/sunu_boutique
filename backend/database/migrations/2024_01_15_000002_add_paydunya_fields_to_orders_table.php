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
            // Ajouter les colonnes PayDunya seulement si elles n'existent pas
            if (!Schema::hasColumn('orders', 'paydunya_invoice_token')) {
                $table->string('paydunya_invoice_token')->nullable();
            }
            if (!Schema::hasColumn('orders', 'paydunya_receipt_url')) {
                $table->string('paydunya_receipt_url')->nullable();
            }
            if (!Schema::hasColumn('orders', 'paydunya_customer_info')) {
                $table->json('paydunya_customer_info')->nullable();
            }
            if (!Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable();
            }
            
            // Index pour améliorer les performances
            if (!Schema::hasIndex('orders', 'orders_paydunya_invoice_token_index')) {
                $table->index('paydunya_invoice_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['paydunya_invoice_token']);
            
            $table->dropColumn([
                'paydunya_invoice_token',
                'paydunya_receipt_url',
                'paydunya_customer_info'
            ]);
        });
    }
};