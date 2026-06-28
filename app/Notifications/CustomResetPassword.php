<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    
    public function __construct(public string $url) {}

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
    public function toMail($notifiable): MailMessage
        {
            return (new MailMessage)
                ->subject('Passwort zurücksetzen')
                ->line('Du erhältst diese E-Mail, weil wir eine Anfrage zum Zurücksetzen des Passworts erhalten haben.')
                ->action('Passwort zurücksetzen', $this->url)
                ->line('Wenn du keine Zurücksetzung angefordert hast, ist keine weitere Aktion erforderlich.');
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
