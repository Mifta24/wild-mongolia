<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\PointService;
use App\Services\CouponService;

class BookingObserver
{
    protected $pointService;
    protected $couponService;

    public function __construct(PointService $pointService, CouponService $couponService)
    {
        $this->pointService = $pointService;
        $this->couponService = $couponService;
    }

    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        // Points will be awarded when payment is confirmed
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Award points when booking is paid
        if ($booking->isDirty('payment_status') && $booking->payment_status === 'paid') {
            $this->pointService->awardBookingPoints($booking);

            // Update membership tier
            if ($booking->user) {
                $this->pointService->updateMembershipTier($booking->user);
            }
        }

        // Refund points when booking is cancelled
        if ($booking->isDirty('status') && $booking->status === 'cancelled') {
            if (in_array($booking->payment_status, ['paid', 'refunded'], true) && $booking->user) {
                $this->pointService->refundBookingPoints($booking);
                $this->pointService->updateMembershipTier($booking->user);
            }
        }
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        //
    }
}
