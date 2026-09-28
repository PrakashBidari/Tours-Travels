<?php

namespace App\Notifications;

use App\Models\ServiceBooking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Alerts super admins about a new tour / flight / bus / car / visa booking. */
class NewServiceBookingNotification extends Notification
{
    public function __construct(public ServiceBooking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New {$this->booking->service_label} booking — {$this->booking->reference}")
            ->greeting('New booking received!')
            ->line("{$this->booking->full_name} ({$this->booking->phone}) booked \"{$this->booking->title}\".")
            ->line('Total: '.npr($this->booking->total))
            ->action('Open booking', route('dashboard.service-bookings.show', $this->booking));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_service_booking',
            'booking_id' => $this->booking->id,
            'reference' => $this->booking->reference,
            'message' => "New {$this->booking->service_label} booking from {$this->booking->full_name}: \"{$this->booking->title}\".",
            'url' => route('dashboard.service-bookings.show', $this->booking),
        ];
    }
}
