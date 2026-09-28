<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RentalPartnerRejectedNotification extends Notification
{
    public function __construct(public ?string $reason = null) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Update on your rental partner application')
            ->greeting("Hi {$notifiable->name},")
            ->line('Unfortunately your rental partner application was not approved at this time.')
            ->when($this->reason, fn ($mail) => $mail->line('Reason: '.$this->reason));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'rental_partner_rejected',
            'message' => 'Your rental partner application was not approved.'.($this->reason ? " Reason: {$this->reason}" : ''),
            'url' => route('rental-partner.dashboard'),
        ];
    }
}
