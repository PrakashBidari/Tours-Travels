<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConfirmRegistrationNotification extends Notification
{
    use Queueable;

    public function __construct(public string $name, public string $verificationUrl) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirm your registration')
            ->greeting("Hi {$this->name},")
            ->line('Please confirm your email address to finish creating your account.')
            ->action('Confirm & create account', $this->verificationUrl)
            ->line('This link expires in 60 minutes. If you did not request this, no further action is required — your account was never created.');
    }
}
