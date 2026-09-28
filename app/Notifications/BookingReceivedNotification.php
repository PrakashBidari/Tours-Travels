<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingReceivedNotification extends Notification
{
    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking confirmation — '.$this->booking->reference)
            ->greeting("Thanks for booking, {$notifiable->name}!")
            ->line("Your booking for \"{$this->booking->property->name}\" has been received and is pending confirmation from the host.")
            ->line('Check-in: '.$this->booking->check_in->format('M d, Y'))
            ->line('Check-out: '.$this->booking->check_out->format('M d, Y'))
            ->line('Total: '.$this->booking->currency.' '.number_format((float) $this->booking->total_price, 2))
            ->action('View my trips', route('account.trips.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'booking_received',
            'booking_id' => $this->booking->id,
            'reference' => $this->booking->reference,
            'property_name' => $this->booking->property->name,
            'message' => "Your booking for \"{$this->booking->property->name}\" has been received.",
            'url' => route('account.trips.index'),
        ];
    }
}
