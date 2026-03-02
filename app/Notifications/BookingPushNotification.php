<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BookingPushNotification extends Notification implements ShouldQueue
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
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $actionUrl = method_exists($notifiable, 'hasRole') && $notifiable->hasRole('admin')
            ? route('admin.bookings.show', $this->booking->id)
            : route('user.booking.show', $this->booking->id);

        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'event' => $this->event,
            'title' => 'Booking Update',
            'message' => $this->message,
            'status' => $this->booking->status,
            'payment_status' => $this->booking->payment_status,
            'service_date' => optional($this->booking->service_date)?->toDateString(),
            'action_url' => $actionUrl,
        ];
    }
}
