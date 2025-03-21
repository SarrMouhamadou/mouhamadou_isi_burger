<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderReadyNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $pdfPath;

    public function __construct($order, $pdfPath)
    {
        $this->order = $order;
        $this->pdfPath = $pdfPath;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre commande est prête !')
            ->line('Bonjour ' . $this->order->user->name . ',')
            ->line('Votre commande #' . $this->order->id . ' est prête.')
            ->line('Vous trouverez la facture en pièce jointe.')
            ->attach($this->pdfPath, [
                'as' => 'facture-commande-' . $this->order->id . '.pdf',
                'mime' => 'application/pdf',
            ])
            ->action('Voir la commande', url('/'));
    }
}
