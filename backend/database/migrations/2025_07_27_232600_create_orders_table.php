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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Informations de livraison
            $table->string('shipping_first_name');
            $table->string('shipping_last_name');
            $table->string('shipping_company')->nullable();
            $table->string('shipping_address_line_1');
            $table->string('shipping_address_line_2')->nullable();
            $table->string('shipping_city');
            $table->string('shipping_state')->nullable();
            $table->string('shipping_postal_code');
            $table->string('shipping_country');
            $table->string('shipping_phone')->nullable();
            
            // Informations de facturation
            $table->string('billing_first_name');
            $table->string('billing_last_name');
            $table->string('billing_company')->nullable();
            $table->string('billing_address_line_1');
            $table->string('billing_address_line_2')->nullable();
            $table->string('billing_city');
            $table->string('billing_state')->nullable();
            $table->string('billing_postal_code');
            $table->string('billing_country');
            $table->string('billing_phone')->nullable();
            
            // Montants
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            // Statut et paiement
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['online', 'cash_on_delivery'])->default('cash_on_delivery');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('payment_date')->nullable();
            
            // Dates importantes
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};