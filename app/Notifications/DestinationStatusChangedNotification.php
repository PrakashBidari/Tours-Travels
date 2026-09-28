<?php

namespace App\Notifications;

use App\Models\Destination;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DestinationStatusChangedNotification extends Notification
{
    public function __construct(public Destination $destination) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = ucfirst($this->destination->status);

        return (new MailMessage)
            ->subject("Destination {$status} — {$this->destination->name}")
            ->greeting("Your destination is now {$status}")
            ->line("\"{$this->destination->name}\" has been {$this->destination->status} by our team.")
            ->action('View my destinations', route('vendor.destinations.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'destination_status_changed',
            'destination_id' => $this->destination->id,
            'status' => $this->destination->status,
            'message' => "\"{$this->destination->name}\" has been {$this->destination->status}.",
            'url' => route('vendor.destinations.index'),
        ];
    }
}
