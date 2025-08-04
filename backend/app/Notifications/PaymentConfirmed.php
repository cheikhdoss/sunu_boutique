<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmed extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public Order $order;

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
        $actionUrl = config('app.frontend_url') . '/mes-commandes/' . $this->order->id;
        $logoUrl = config('app.logo_url');

        return (new MailMessage)
            ->subject('Confirmation de paiement pour votre commande #' . $this->order->order_number)
            ->view('emails.payment_confirmed', [
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
            //
        ];
    }
}
