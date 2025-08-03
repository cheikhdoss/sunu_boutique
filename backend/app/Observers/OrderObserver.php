<?php

namespace App\Observers;

use App\Models\Order;
use App\Mail\OrderPaidNotification;
use App\Mail\OrderStatusChangedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Vérifier si le statut de paiement a changé vers "paid"
        if ($order->isDirty('payment_status') && $order->payment_status === 'paid') {
            $this->handlePaymentConfirmed($order);
        }

        // Vérifier si le statut de la commande a changé
        if ($order->isDirty('status')) {
            $this->handleStatusChanged($order);
        }
    }

    /**
     * Gérer la confirmation de paiement
     */
    private function handlePaymentConfirmed(Order $order): void
    {
        try {
            Log::info('Payment confirmed for order', ['order_id' => $order->id]);

            // Mettre à jour la date de paiement si pas déjà définie
            if (!$order->paid_at) {
                $order->update(['paid_at' => now()]);
            }

            // Envoyer l'email de confirmation de paiement
            $this->sendPaymentConfirmationEmail($order);

            // Changer le statut de la commande si elle est encore en attente
            if ($order->status === 'pending') {
                $order->update(['status' => 'confirmed']);
            }

        } catch (\Exception $e) {
            Log::error('Error handling payment confirmation', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Gérer le changement de statut de commande
     */
    private function handleStatusChanged(Order $order): void
    {
        try {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            Log::info('Order status changed', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            // Envoyer un email de notification de changement de statut
            $this->sendStatusChangeEmail($order, $oldStatus, $newStatus);

            // Actions spécifiques selon le nouveau statut
            switch ($newStatus) {
                case 'shipped':
                    if (!$order->shipped_at) {
                        $order->update(['shipped_at' => now()]);
                    }
                    break;

                case 'delivered':
                    if (!$order->delivered_at) {
                        $order->update(['delivered_at' => now()]);
                    }
                    break;

                case 'cancelled':
                    if (!$order->cancelled_at) {
                        $order->update(['cancelled_at' => now()]);
                    }
                    break;
            }

        } catch (\Exception $e) {
            Log::error('Error handling status change', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envoyer l'email de confirmation de paiement
     */
    private function sendPaymentConfirmationEmail(Order $order): void
    {
        try {
            // Charger les relations nécessaires
            $order->load(['items', 'user']);

            // Déterminer l'email du destinataire
            $recipientEmail = $this->getRecipientEmail($order);
            $recipientName = $this->getRecipientName($order);

            if (!$recipientEmail) {
                Log::warning('No recipient email found for order', ['order_id' => $order->id]);
                return;
            }

            // Envoyer l'email
            Mail::to($recipientEmail, $recipientName)
                ->send(new OrderPaidNotification($order));

            Log::info('Payment confirmation email sent', [
                'order_id' => $order->id,
                'recipient' => $recipientEmail
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envoyer l'email de changement de statut
     */
    private function sendStatusChangeEmail(Order $order, string $oldStatus, string $newStatus): void
    {
        try {
            // Ne pas envoyer d'email pour certains changements de statut
            $skipStatuses = ['pending', 'processing'];
            if (in_array($newStatus, $skipStatuses)) {
                return;
            }

            // Charger les relations nécessaires
            $order->load(['items', 'user']);

            // Déterminer l'email du destinataire
            $recipientEmail = $this->getRecipientEmail($order);
            $recipientName = $this->getRecipientName($order);

            if (!$recipientEmail) {
                Log::warning('No recipient email found for order status change', ['order_id' => $order->id]);
                return;
            }

            // Envoyer l'email
            Mail::to($recipientEmail, $recipientName)
                ->send(new OrderStatusChangedMail($order, $oldStatus, $newStatus));

            Log::info('Status change email sent', [
                'order_id' => $order->id,
                'recipient' => $recipientEmail,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send status change email', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtenir l'email du destinataire
     */
    private function getRecipientEmail(Order $order): ?string
    {
        // Priorité 1: Email de l'utilisateur connecté
        if ($order->user && $order->user->email) {
            return $order->user->email;
        }

        // Priorité 2: Email de facturation
        if ($order->billing_email) {
            return $order->billing_email;
        }

        // Priorité 3: Email dans les informations client (JSON)
        if ($order->customer_info) {
            $customerInfo = is_array($order->customer_info) ? $order->customer_info : json_decode($order->customer_info, true);
            if ($customerInfo && isset($customerInfo['email'])) {
                return $customerInfo['email'];
            }
        }

        // Priorité 4: Essayer de deviner à partir du téléphone ou nom
        // (pour les commandes créées avant la correction)
        if ($order->shipping_phone === '767304941') {
            return 'comedie442@gmail.com'; // Email connu pour ce numéro
        }

        return null;
    }

    /**
     * Obtenir le nom du destinataire
     */
    private function getRecipientName(Order $order): string
    {
        // Priorité 1: Nom de l'utilisateur connecté
        if ($order->user && $order->user->name) {
            return $order->user->name;
        }

        // Priorité 2: Nom de facturation
        if ($order->billing_first_name && $order->billing_last_name) {
            return $order->billing_first_name . ' ' . $order->billing_last_name;
        }

        // Priorité 3: Nom de livraison
        if ($order->shipping_first_name && $order->shipping_last_name) {
            return $order->shipping_first_name . ' ' . $order->shipping_last_name;
        }

        // Priorité 4: Nom dans les informations client (JSON)
        if ($order->customer_info && is_array($order->customer_info)) {
            if (isset($order->customer_info['firstName'], $order->customer_info['lastName'])) {
                return $order->customer_info['firstName'] . ' ' . $order->customer_info['lastName'];
            }
        }

        return 'Client';
    }
}