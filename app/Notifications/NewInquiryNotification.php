<?php

namespace App\Notifications;

use App\Models\Inquiry;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewInquiryNotification extends Notification
{
    public function __construct(public Inquiry $inquiry) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New {$this->inquiry->type_label} from {$this->inquiry->name}")
            ->line("{$this->inquiry->name} ({$this->inquiry->email}, {$this->inquiry->phone}) sent a {$this->inquiry->type_label}.")
            ->line((string) $this->inquiry->message)
            ->action('Open inquiry', route('dashboard.inquiries.show', $this->inquiry));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_inquiry',
            'inquiry_id' => $this->inquiry->id,
            'message' => "New {$this->inquiry->type_label} from {$this->inquiry->name}.",
            'url' => route('dashboard.inquiries.show', $this->inquiry),
        ];
    }
}
