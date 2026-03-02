<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingPushNotification;
use App\Notifications\BookingStatusEmailNotification;
use Illuminate\Support\Facades\Notification;

class BookingNotificationService
{
    public function __construct(
        private readonly BookingEmailService $bookingEmailService,
        private readonly ExternalMessagingService $externalMessagingService
    ) {
    }

    public function notifyBookingCreated(Booking $booking): void
    {
        $message = 'Your booking request has been received and is pending payment.';

        $this->sendStatusEmail($booking, 'created', $message);
        $this->sendPush($booking, 'created', $message);
        $this->sendSocialAutomation($booking, 'created', $message);
    }

    public function notifyBookingPaid(Booking $booking): void
    {
        $message = 'Payment has been received and your booking is confirmed.';

        $this->bookingEmailService->sendConfirmationIfNeeded($booking);
        $this->sendStatusEmail($booking, 'paid', $message);
        $this->sendPush($booking, 'paid', $message);
        $this->sendSocialAutomation($booking, 'paid', $message);
    }

    public function notifyPaymentRefunded(Booking $booking): void
    {
        $message = 'Your payment has been refunded.';

        $this->sendStatusEmail($booking, 'refunded', $message);
        $this->sendPush($booking, 'refunded', $message);
        $this->sendSocialAutomation($booking, 'refunded', $message);
    }

    public function notifyBookingCancelled(Booking $booking): void
    {
        $message = 'Your booking has been cancelled.';

        $this->sendStatusEmail($booking, 'cancelled', $message);
        $this->sendPush($booking, 'cancelled', $message);
        $this->sendSocialAutomation($booking, 'cancelled', $message);
    }

    public function notifyStatusUpdated(Booking $booking, string $status): void
    {
        $readableStatus = ucfirst($status);
        $message = "Your booking status has been updated to {$readableStatus}.";

        $this->sendStatusEmail($booking, 'status_updated', $message);
        $this->sendPush($booking, 'status_updated', $message);
        $this->sendSocialAutomation($booking, 'status_updated', $message);
    }

    private function sendStatusEmail(Booking $booking, string $event, string $message): void
    {
        if (blank($booking->guest_email)) {
            return;
        }

        Notification::route('mail', $booking->guest_email)
            ->notify(new BookingStatusEmailNotification($booking, $event, $message));
    }

    private function sendPush(Booking $booking, string $event, string $message): void
    {
        if ($booking->user) {
            $booking->user->notify(new BookingPushNotification($booking, $event, $message));
        }

        $admins = User::query()->role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new BookingPushNotification($booking, $event, $message));
        }
    }

    private function sendSocialAutomation(Booking $booking, string $event, string $message): void
    {
        $this->externalMessagingService->sendWhatsAppAutomation($booking, $event, $message);
        $this->externalMessagingService->sendLineAutomation($booking, $event, $message);
    }
}
