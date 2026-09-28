<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorApprovedNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your vendor account has been approved')
            ->greeting("Welcome aboard, {$notifiable->name}!")
            ->line('Your vendor account has been approved. You can now post hotels, tours and destinations.')
            ->action('Go to your dashboard', route('vendor.dashboard'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'vendor_approved',
            'message' => 'Your vendor account has been approved. You can now post listings.',
            'url' => route('vendor.dashboard'),
        ];
    }
}
