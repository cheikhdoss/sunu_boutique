<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer l'utilisateur client de test
        $client = User::where('email', 'client@sunuboutique.sn')->first();
        
        if (!$client) {
            $this->command->error('Utilisateur client non trouvé. Veuillez d\'abord exécuter UserSeeder.');
            return;
        }

        // Récupérer quelques produits
        $products = Product::take(3)->get();
        
        if ($products->count() === 0) {
            $this->command->error('Aucun produit trouvé. Veuillez d\'abord exécuter ProductSeeder.');
            return;
        }

        // Créer la première commande (livrée)
        $order1 = Order::create([
            'user_id' => $client->id,
            'shipping_first_name' => 'Client',
            'shipping_last_name' => 'Test',
            'shipping_address_line_1' => '123 Rue de la Paix',
            'shipping_city' => 'Dakar',
            'shipping_postal_code' => '12000',
            'shipping_country' => 'Sénégal',
            'shipping_phone' => '+221 77 987 65 43',
            'billing_first_name' => 'Client',
            'billing_last_name' => 'Test',
            'billing_address_line_1' => '123 Rue de la Paix',
            'billing_city' => 'Dakar',
            'billing_postal_code' => '12000',
            'billing_country' => 'Sénégal',
            'billing_phone' => '+221 77 987 65 43',
            'subtotal' => 125000,
            'tax_amount' => 0,
            'shipping_amount' => 5000,
            'total' => 130000,
            'status' => 'delivered',
            'payment_method' => 'online',
            'payment_status' => 'paid',
            'payment_date' => now()->subDays(5),
            'delivered_at' => now()->subDays(2),
        ]);

        // Ajouter des articles à la première commande
        if ($products->count() > 0) {
            OrderItem::create([
                'order_id' => $order1->id,
                'product_id' => $products[0]->id,
                'product_name' => $products[0]->name,
                'product_description' => $products[0]->description,
                'unit_price' => $products[0]->price,
                'quantity' => 2,
                'total_price' => $products[0]->price * 2,
            ]);
        }

        if ($products->count() > 1) {
            OrderItem::create([
                'order_id' => $order1->id,
                'product_id' => $products[1]->id,
                'product_name' => $products[1]->name,
                'product_description' => $products[1]->description,
                'unit_price' => $products[1]->price,
                'quantity' => 1,
                'total_price' => $products[1]->price,
            ]);
        }

        // Créer la deuxième commande (en cours)
        $order2 = Order::create([
            'user_id' => $client->id,
            'shipping_first_name' => 'Client',
            'shipping_last_name' => 'Test',
            'shipping_address_line_1' => '456 Avenue de l\'Indépendance',
            'shipping_city' => 'Thiès',
            'shipping_postal_code' => '21000',
            'shipping_country' => 'Sénégal',
            'shipping_phone' => '+221 77 987 65 43',
            'billing_first_name' => 'Client',
            'billing_last_name' => 'Test',
            'billing_address_line_1' => '456 Avenue de l\'Indépendance',
            'billing_city' => 'Thiès',
            'billing_postal_code' => '21000',
            'billing_country' => 'Sénégal',
            'billing_phone' => '+221 77 987 65 43',
            'subtotal' => 89000,
            'tax_amount' => 0,
            'shipping_amount' => 3000,
            'total' => 92000,
            'status' => 'shipped',
            'payment_method' => 'cash_on_delivery',
            'payment_status' => 'pending',
            'shipped_at' => now()->subDays(1),
        ]);

        // Ajouter des articles à la deuxième commande
        if ($products->count() > 2) {
            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $products[2]->id,
                'product_name' => $products[2]->name,
                'product_description' => $products[2]->description,
                'unit_price' => $products[2]->price,
                'quantity' => 1,
                'total_price' => $products[2]->price,
            ]);
        }

        // Créer une troisième commande (en attente)
        $order3 = Order::create([
            'user_id' => $client->id,
            'shipping_first_name' => 'Client',
            'shipping_last_name' => 'Test',
            'shipping_address_line_1' => '789 Boulevard du Centenaire',
            'shipping_city' => 'Dakar',
            'shipping_postal_code' => '12500',
            'shipping_country' => 'Sénégal',
            'shipping_phone' => '+221 77 987 65 43',
            'billing_first_name' => 'Client',
            'billing_last_name' => 'Test',
            'billing_address_line_1' => '789 Boulevard du Centenaire',
            'billing_city' => 'Dakar',
            'billing_postal_code' => '12500',
            'billing_country' => 'Sénégal',
            'billing_phone' => '+221 77 987 65 43',
            'subtotal' => 45000,
            'tax_amount' => 0,
            'shipping_amount' => 2500,
            'total' => 47500,
            'status' => 'pending',
            'payment_method' => 'online',
            'payment_status' => 'pending',
        ]);

        // Ajouter des articles à la troisième commande
        if ($products->count() > 0) {
            OrderItem::create([
                'order_id' => $order3->id,
                'product_id' => $products[0]->id,
                'product_name' => $products[0]->name,
                'product_description' => $products[0]->description,
                'unit_price' => $products[0]->price,
                'quantity' => 1,
                'total_price' => $products[0]->price,
            ]);
        }

        $this->command->info('Commandes de test créées avec succès !');
        $this->command->info('- Commande 1: ' . $order1->order_number . ' (livrée)');
        $this->command->info('- Commande 2: ' . $order2->order_number . ' (expédiée)');
        $this->command->info('- Commande 3: ' . $order3->order_number . ' (en attente)');
    }
}