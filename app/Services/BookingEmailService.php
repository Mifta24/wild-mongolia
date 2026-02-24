<?php

namespace App\Services;

use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

class BookingEmailService
{
    public function sendConfirmationIfNeeded(Booking $booking): void
    {
        $this->sendConfirmation($booking, false);
    }

    public function resendConfirmation(Booking $booking): void
    {
        $this->sendConfirmation($booking, true);
    }

    private function sendConfirmation(Booking $booking, bool $force): void
    {
        if ($booking->payment_status !== 'paid') {
            return;
        }

        if (!$force && $booking->booking_confirmation_emailed_at) {
            return;
        }

        if (blank($booking->guest_email)) {
            return;
        }

        Mail::to($booking->guest_email)->send(new BookingConfirmationMail($booking));

        $booking->forceFill([
            'booking_confirmation_emailed_at' => now(),
        ])->save();
    }
}
