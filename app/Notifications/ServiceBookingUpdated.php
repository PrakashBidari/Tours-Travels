<?php

namespace App\Notifications;

use App\Models\ServiceBooking;
use App\Services\BookingService;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Emailed to the traveler when staff change their booking or payment status. */
class ServiceBookingUpdated extends Notification
{
    public function __construct(public ServiceBooking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking;

        $mail = (new MailMessage)
            ->subject("Booking {$booking->reference} is now ".ucfirst($booking->status))
            ->greeting("Namaste {$booking->full_name},")
            ->line("There is an update on your booking **{$booking->reference}** ({$booking->title}).")
            ->line('**Booking status:** '.ucfirst($booking->status))
            ->line('**Payment status:** '.ucfirst($booking->payment_status));

        if ($booking->pnr) {
            $mail->line("**PNR:** {$booking->pnr}");
        }

        if ($booking->ticket_number) {
            $mail->line("**Ticket number:** {$booking->ticket_number}");
        }

        return $mail
            ->action('View booking', BookingService::confirmationUrl($booking))
            ->line('Thank you for travelling with '.site('name').'.');
    }
}
