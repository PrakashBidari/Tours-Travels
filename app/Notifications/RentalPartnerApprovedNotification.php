<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RentalPartnerApprovedNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your rental partner account has been approved')
            ->greeting("Welcome aboard, {$notifiable->name}!")
            ->line('Your rental partner account has been approved. You can now post hotels, tours and destinations.')
            ->action('Go to your dashboard', route('rental-partner.dashboard'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'rental_partner_approved',
            'message' => 'Your rental partner account has been approved. You can now post listings.',
            'url' => route('rental-partner.dashboard'),
        ];
    }
}
