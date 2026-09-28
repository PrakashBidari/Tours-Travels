<?php

namespace App\Notifications;

use App\Models\ServiceBooking;
use App\Services\BookingService;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Emailed to the traveler right after they submit any booking. */
class ServiceBookingConfirmation extends Notification
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
            ->subject("Booking received — {$booking->reference} | ".site('name'))
            ->greeting("Namaste {$booking->full_name},")
            ->line("Thank you for booking with ".site('name').". We have received your {$booking->service_label} request.")
            ->line("**Booking ID:** {$booking->reference}")
            ->line("**Service:** {$booking->title}");

        if ($booking->travel_date) {
            $mail->line('**Travel date:** '.$booking->travel_date->format('D, M d, Y'));
        }

        if ($booking->total > 0) {
            $mail->line('**Total:** '.npr($booking->total));
        }

        return $mail
            ->line('Our team will verify availability and confirm your booking shortly. You can view your booking, pay online and download the invoice any time:')
            ->action('View my booking', BookingService::confirmationUrl($booking))
            ->line('Questions? Call '.site('phone').' or WhatsApp '.site('mobile').'.');
    }
}
