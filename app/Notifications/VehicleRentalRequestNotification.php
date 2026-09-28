<?php

namespace App\Notifications;

use App\Models\VehicleRentalRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleRentalRequestNotification extends Notification
{
    public function __construct(
        public VehicleRentalRequest $request,
        public string $message,
        public string $url,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Vehicle rental update')
            ->line($this->message)
            ->action('View', $this->url);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'vehicle_rental_request',
            'request_id' => $this->request->id,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }

    /**
     * Without this override, Laravel's default broadcast payload replaces our
     * 'type' => 'vehicle_rental_request' array key with this notification's
     * fully-qualified class name — the frontend listener keys off this value.
     */
    public function broadcastType(): string
    {
        return 'vehicle_rental_request';
    }
}
