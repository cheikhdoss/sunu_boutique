<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPaidNotification;
use App\Models\Order;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {order_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test l\'envoi d\'email de confirmation de paiement';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->argument('order_id');
        
        if (!$orderId) {
            // Prendre la dernière commande
            $order = Order::with(['items', 'user'])->latest()->first();
        } else {
            $order = Order::with(['items', 'user'])->find($orderId);
        }

        if (!$order) {
            $this->error('Aucune commande trouvée');
            return 1;
        }

        $this->info("Test d'envoi d'email pour la commande #{$order->order_number}");

        try {
            // Envoyer l'email à votre adresse
            Mail::to('comedie442@gmail.com')->send(new OrderPaidNotification($order));
            
            $this->info('✅ Email envoyé avec succès à comedie442@gmail.com');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'envoi: ' . $e->getMessage());
            return 1;
        }
    }
}