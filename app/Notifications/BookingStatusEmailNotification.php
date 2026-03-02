<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public string $event,
        public string $message
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Update - ' . $this->booking->booking_code)
            ->greeting('Hello ' . ($this->booking->guest_name ?: 'Customer') . ',')
            ->line($this->message)
            ->line('Booking Code: ' . $this->booking->booking_code)
            ->line('Service: ' . $this->booking->product_name)
            ->line('Date: ' . optional($this->booking->service_date)?->format('d M Y'))
            ->line('Status: ' . ucfirst((string) $this->booking->status))
            ->line('Payment Status: ' . ucfirst((string) $this->booking->payment_status));
    }
}
