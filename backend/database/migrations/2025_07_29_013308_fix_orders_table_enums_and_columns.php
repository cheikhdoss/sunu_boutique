<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Corriger les enums et ajouter les colonnes manquantes
        DB::statement("ALTER TABLE orders ALTER COLUMN status TYPE VARCHAR(50)");
        DB::statement("ALTER TABLE orders ALTER COLUMN payment_status TYPE VARCHAR(50)");
        DB::statement("ALTER TABLE orders ALTER COLUMN payment_method TYPE VARCHAR(50)");
        
        // Mettre à jour les valeurs existantes pour être cohérentes
        DB::statement("UPDATE orders SET status = 'pending' WHERE status IN ('en_attente')");
        DB::statement("UPDATE orders SET status = 'shipped' WHERE status IN ('expediee')");
        DB::statement("UPDATE orders SET status = 'delivered' WHERE status IN ('livree')");
        DB::statement("UPDATE orders SET status = 'cancelled' WHERE status IN ('annulee')");
        
        DB::statement("UPDATE orders SET payment_status = 'pending' WHERE payment_status IN ('en_attente')");
        DB::statement("UPDATE orders SET payment_status = 'paid' WHERE payment_status IN ('paye')");
        DB::statement("UPDATE orders SET payment_status = 'failed' WHERE payment_status IN ('echec')");
        
        DB::statement("UPDATE orders SET payment_method = 'cash_on_delivery' WHERE payment_method IN ('apres_livraison')");
        DB::statement("UPDATE orders SET payment_method = 'online' WHERE payment_method IN ('avant_livraison')");
        
        Schema::table('orders', function (Blueprint $table) {
            // Ajouter les colonnes manquantes pour la compatibilité
            if (!Schema::hasColumn('orders', 'customer_info')) {
                $table->json('customer_info')->nullable();
            }
            if (!Schema::hasColumn('orders', 'delivery_address')) {
                $table->json('delivery_address')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir aux anciens enums
        DB::statement("UPDATE orders SET status = 'en_attente' WHERE status = 'pending'");
        DB::statement("UPDATE orders SET status = 'expediee' WHERE status = 'shipped'");
        DB::statement("UPDATE orders SET status = 'livree' WHERE status = 'delivered'");
        DB::statement("UPDATE orders SET status = 'annulee' WHERE status = 'cancelled'");
        
        DB::statement("UPDATE orders SET payment_status = 'en_attente' WHERE payment_status = 'pending'");
        DB::statement("UPDATE orders SET payment_status = 'paye' WHERE payment_status = 'paid'");
        DB::statement("UPDATE orders SET payment_status = 'echec' WHERE payment_status = 'failed'");
        
        DB::statement("UPDATE orders SET payment_method = 'apres_livraison' WHERE payment_method = 'cash_on_delivery'");
        DB::statement("UPDATE orders SET payment_method = 'avant_livraison' WHERE payment_method = 'online'");
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['customer_info', 'delivery_address']);
        });
    }
};