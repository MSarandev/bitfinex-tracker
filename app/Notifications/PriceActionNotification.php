<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Env;

class PriceActionNotification extends Notification
{
    use Queueable;

    private string $userConfig;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $userConfig)
    {
        $this->userConfig = $userConfig;
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
        return (new MailMessage)
            ->line('Price action alert!')
            ->line('Your price action alert has triggered.')
            ->line(sprintf('Your config: %s', $this->userConfig))
            ->action('Check the current prices here', Env::get('APP_URL').'/');
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
