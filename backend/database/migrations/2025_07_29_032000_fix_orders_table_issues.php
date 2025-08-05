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
            // Ajouter la colonne paid_at si elle n'existe pas déjà
            if (!Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_date');
            }
            
            // Modifier user_id pour permettre les valeurs NULL (commandes invités)
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Supprimer la colonne paid_at
            $table->dropColumn('paid_at');
            
            // Remettre user_id comme NOT NULL
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};