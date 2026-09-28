<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PackageStatusChangedNotification extends Notification
{
    public function __construct(public Property $property) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = ucfirst($this->property->status);

        return (new MailMessage)
            ->subject("Package {$status} — {$this->property->name}")
            ->greeting("Your package is now {$status}")
            ->line("\"{$this->property->name}\" has been {$this->property->status} by our team.")
            ->action('View my packages', route('vendor.packages.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'package_status_changed',
            'property_id' => $this->property->id,
            'status' => $this->property->status,
            'message' => "\"{$this->property->name}\" has been {$this->property->status}.",
            'url' => route('vendor.packages.index'),
        ];
    }
}
