<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewVendorRegisteredNotification extends Notification
{
    public function __construct(public User $vendor) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $label = $this->roleLabel();

        return (new MailMessage)
            ->subject("New {$label} awaiting approval — ".$this->vendor->company_name)
            ->greeting("A new {$label} has registered")
            ->line("{$this->vendor->name} ({$this->vendor->company_name}) has verified their email and is awaiting approval.")
            ->action("Review {$label}", $this->reviewUrl());
    }

    public function toArray(object $notifiable): array
    {
        $label = $this->roleLabel();

        return [
            'type' => 'new_vendor_registered',
            'vendor_id' => $this->vendor->id,
            'vendor_name' => $this->vendor->name,
            'company_name' => $this->vendor->company_name,
            'message' => "{$this->vendor->name} ({$this->vendor->company_name}) is awaiting {$label} approval.",
            'url' => $this->reviewUrl(),
        ];
    }

    private function roleLabel(): string
    {
        return $this->vendor->isRentalPartner() ? 'rental partner' : 'vendor';
    }

    private function reviewUrl(): string
    {
        return $this->vendor->isRentalPartner()
            ? route('dashboard.rental-partners.index')
            : route('dashboard.vendors.index');
    }
}
