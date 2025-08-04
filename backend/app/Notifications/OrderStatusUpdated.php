<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public Order $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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
        // L'URL doit pointer vers la page de commande de votre application frontend (Angular)
        $actionUrl = config('app.frontend_url') . '/mes-commandes/' . $this->order->id;

        // Vous pouvez définir l'URL de votre logo dans le fichier .env
        $logoUrl = config('app.logo_url'); // Exemple: APP_LOGO_URL=https://votresite.com/logo.png

        return (new MailMessage)
            ->subject('Mise à jour concernant votre commande #' . $this->order->order_number)
            ->view('emails.order_status_updated', [
                'order' => $this->order,
                'recipientName' => $notifiable->name,
                'actionUrl' => $actionUrl,
                'logoUrl' => $logoUrl,
            ]);
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
            'status' => $this->order->status,
        ];
    }
}
