<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusChangedNotification extends Notification
{
    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = ucfirst($this->booking->status);

        return (new MailMessage)
            ->subject("Booking {$status} — {$this->booking->reference}")
            ->greeting("Your booking is now {$status}")
            ->line("\"{$this->booking->property->name}\" — {$this->booking->check_in->format('M d, Y')} to {$this->booking->check_out->format('M d, Y')}.")
            ->when($this->booking->status === 'cancelled' && $this->booking->cancelled_reason, fn ($mail) => $mail->line('Reason: '.$this->booking->cancelled_reason))
            ->action('View my trips', route('account.trips.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'booking_status_changed',
            'booking_id' => $this->booking->id,
            'reference' => $this->booking->reference,
            'status' => $this->booking->status,
            'property_name' => $this->booking->property->name,
            'message' => "Your booking for \"{$this->booking->property->name}\" is now {$this->booking->status}.",
            'url' => route('account.trips.index'),
        ];
    }
}
