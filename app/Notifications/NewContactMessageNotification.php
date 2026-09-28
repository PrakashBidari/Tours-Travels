<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification
{
    public function __construct(public ContactMessage $contactMessage) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New contact message from {$this->contactMessage->name}")
            ->greeting('New contact form submission')
            ->line("Name: {$this->contactMessage->name}")
            ->line("Email: {$this->contactMessage->email}")
            ->line("Phone: {$this->contactMessage->phone}")
            ->line('Message:')
            ->line($this->contactMessage->description)
            ->action('View in dashboard', route('dashboard.contact-messages.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_contact_message',
            'contact_message_id' => $this->contactMessage->id,
            'message' => "New contact message from {$this->contactMessage->name}.",
            'url' => route('dashboard.contact-messages.index'),
        ];
    }
}
