<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification
{
    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New booking received — '.$this->booking->reference)
            ->greeting('You have a new booking!')
            ->line("{$this->booking->user->name} booked \"{$this->booking->property->name}\" for {$this->booking->nights} night(s).")
            ->line('Check-in: '.$this->booking->check_in->format('M d, Y'))
            ->line('Check-out: '.$this->booking->check_out->format('M d, Y'))
            ->line('Your payout: '.$this->booking->currency.' '.number_format((float) $this->booking->vendor_payout_amount, 2))
            ->action('View booking', route('vendor.bookings.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_booking',
            'booking_id' => $this->booking->id,
            'reference' => $this->booking->reference,
            'property_name' => $this->booking->property->name,
            'guest_name' => $this->booking->user->name,
            'message' => "New booking from {$this->booking->user->name} for \"{$this->booking->property->name}\".",
            'url' => route('vendor.bookings.index'),
        ];
    }
}
