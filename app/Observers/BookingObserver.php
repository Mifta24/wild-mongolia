<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\InventorySlot;
use App\Services\PointService;
use App\Services\CouponService;
use App\Services\BookingNotificationService;

class BookingObserver
{
    protected $pointService;
    protected $couponService;
    protected $bookingNotificationService;

    public function __construct(
        PointService $pointService,
        CouponService $couponService,
        BookingNotificationService $bookingNotificationService
    )
    {
        $this->pointService = $pointService;
        $this->couponService = $couponService;
        $this->bookingNotificationService = $bookingNotificationService;
    }

    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        $this->bookingNotificationService->notifyBookingCreated($booking);
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Award points when booking is paid
        if ($booking->wasChanged('payment_status') && $booking->payment_status === 'paid') {
            $this->pointService->awardBookingPoints($booking);

            // Update membership tier
            if ($booking->user) {
                $this->pointService->updateMembershipTier($booking->user);
            }

            $this->bookingNotificationService->notifyBookingPaid($booking);
        }

        if ($booking->wasChanged('payment_status') && $booking->payment_status === 'refunded') {
            $this->bookingNotificationService->notifyPaymentRefunded($booking);
        }

        // Refund points when booking is cancelled
        if ($booking->wasChanged('status') && $booking->status === 'cancelled') {
            if (in_array($booking->payment_status, ['paid', 'refunded'], true) && $booking->user) {
                $this->pointService->refundBookingPoints($booking);
                $this->pointService->updateMembershipTier($booking->user);
            }

            if ($booking->inventory_slot_id) {
                $this->releaseInventoryCapacity($booking->inventory_slot_id, (int) $booking->quantity);
            }

            $this->bookingNotificationService->notifyBookingCancelled($booking);
        }

        if (
            $booking->wasChanged('status')
            && in_array($booking->status, ['confirmed', 'completed'], true)
            && !($booking->status === 'confirmed' && $booking->wasChanged('payment_status') && $booking->payment_status === 'paid')
        ) {
            $this->bookingNotificationService->notifyStatusUpdated($booking, $booking->status);
        }
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        if ($booking->inventory_slot_id && $booking->status !== 'cancelled') {
            $this->releaseInventoryCapacity($booking->inventory_slot_id, (int) $booking->quantity);
        }
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

    private function releaseInventoryCapacity(int $slotId, int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        $slot = InventorySlot::query()->find($slotId);

        if (!$slot) {
            return;
        }

        $slot->update([
            'booked_quantity' => max(0, (int) $slot->booked_quantity - $quantity),
        ]);
    }
}
