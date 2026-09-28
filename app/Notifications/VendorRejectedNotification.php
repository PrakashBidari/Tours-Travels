<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorRejectedNotification extends Notification
{
    public function __construct(public ?string $reason = null) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Update on your vendor application')
            ->greeting("Hi {$notifiable->name},")
            ->line('Unfortunately your vendor application was not approved at this time.')
            ->when($this->reason, fn ($mail) => $mail->line('Reason: '.$this->reason));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'vendor_rejected',
            'message' => 'Your vendor application was not approved.'.($this->reason ? " Reason: {$this->reason}" : ''),
            'url' => route('vendor.dashboard'),
        ];
    }
}
