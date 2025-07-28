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
            // Ajouter les colonnes manquantes si elles n'existent pas
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->unique()->after('id');
            }
            if (!Schema::hasColumn('orders', 'delivery_address_id')) {
                $table->foreignId('delivery_address_id')->nullable()->constrained()->after('user_id');
            }
            if (!Schema::hasColumn('orders', 'status')) {
                $table->enum('status', ['en_attente', 'expediee', 'livree', 'annulee'])->default('en_attente')->after('delivery_address_id');
            }
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['en_attente', 'paye', 'echec'])->default('en_attente')->after('status');
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->enum('payment_method', ['avant_livraison', 'apres_livraison'])->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('total');
            }
            if (!Schema::hasColumn('orders', 'invoice_url')) {
                $table->string('invoice_url')->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_number', 
                'delivery_address_id', 
                'status', 
                'payment_status', 
                'payment_method', 
                'notes', 
                'invoice_url'
            ]);
        });
    }
};