<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'processing' => 'En préparation',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée'
        ];

        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;
        $subject = "Mise à jour de votre commande #{$this->order->order_number}";

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting('Bonjour !')
            ->line("Nous vous informons que le statut de votre commande #{$this->order->order_number} a été mis à jour.")
            ->line("**Nouveau statut :** {$newStatusLabel}");

        // Messages spécifiques selon le statut
        switch ($this->newStatus) {
            case 'confirmed':
                $mailMessage->line('Votre commande a été confirmée et est en cours de préparation.');
                break;

            case 'processing':
                $mailMessage->line('Votre commande est actuellement en cours de préparation dans nos entrepôts.');
                break;

            case 'shipped':
                $mailMessage->line('Bonne nouvelle ! Votre commande a été expédiée et est en route vers vous.');
                if ($this->order->tracking_number) {
                    $mailMessage->line("**Numéro de suivi :** {$this->order->tracking_number}");
                }
                break;

            case 'delivered':
                $mailMessage->line('Votre commande a été livrée avec succès ! Nous espérons que vous êtes satisfait(e) de vos achats.');
                $mailMessage->line('N\'hésitez pas à nous laisser un avis sur votre expérience.');
                break;

            case 'cancelled':
                $mailMessage->line('Votre commande a été annulée. Si vous avez effectué un paiement, le remboursement sera traité dans les plus brefs délais.');
                break;
        }

        // Informations de la commande
        $mailMessage->line('**Détails de la commande :**')
            ->line("- Numéro : {$this->order->order_number}")
            ->line("- Date : " . $this->order->created_at->format('d/m/Y à H:i'))
            ->line("- Montant : " . number_format($this->order->total, 0, ',', ' ') . ' FCFA');

        // Actions selon le statut
        if (in_array($this->newStatus, ['shipped', 'delivered'])) {
            $mailMessage->action('Voir ma commande', url('/profile#orders'));
            
            if ($this->newStatus === 'delivered') {
                $mailMessage->action('Télécharger la facture', url("/api/invoices/{$this->order->id}/download"));
            }
        }

        $mailMessage->line('Merci de votre confiance !')
            ->salutation('L\'équipe SunuBoutique');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}