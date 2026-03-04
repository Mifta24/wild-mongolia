<?php

namespace App\Http\Controllers\User;

use App\Enums\MembershipTier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\CouponService;
use App\Services\StripePaymentService;

class DashboardController extends Controller
{
    /**
     * Display user dashboard with overview
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->syncMembershipStatus();

        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'upcoming_bookings' => $user->bookings()
                ->where('service_date', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->count(),
            'points' => $user->points,
            'available_coupons' => $user->availableCoupons()->count(),
        ];

        $upcomingBookings = $user->bookings()
            ->where('service_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('service_date', 'asc')
            ->limit(3)
            ->get();

        $recentNotifications = $user->notifications()
            ->latest()
            ->limit(5)
            ->get();

        $unreadNotificationsCount = $user->unreadNotifications()->count();

        return view('user.dashboard.index', compact(
            'user',
            'stats',
            'upcomingBookings',
            'recentNotifications',
            'unreadNotificationsCount'
        ));
    }

    public function markNotificationRead(string $notificationId)
    {
        /** @var User $user */
        $user = Auth::user();

        $notification = $user
            ->notifications()
            ->whereKey($notificationId)
            ->firstOrFail();

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return back();
    }

    public function markAllNotificationsRead()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();

        return back();
    }

    /**
     * Display user's booking history
     */
    public function bookings(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $status = $request->get('status', 'all');

        $query = $user->bookings()->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(10);

        return view('user.dashboard.bookings', compact('bookings', 'status'));
    }

    /**
     * Show booking details
     */
    public function showBooking($id)
    {
        /** @var User $user */
        $user = Auth::user();
        $booking = $user->bookings()->with(['product', 'review'])->findOrFail($id);

        return view('user.dashboard.booking-detail', compact('booking'));
    }

    /**
     * Cancel booking request
     */
    public function cancelBooking(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $booking = $user->bookings()->findOrFail($id);

        // Check if cancellation is allowed
        if ($booking->status === 'cancelled' || $booking->status === 'completed') {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        // Check cancellation policy (24 hours before)
        $hoursUntilService = now()->diffInHours($booking->service_date, false);

        if ($hoursUntilService < 24) {
            return back()->with('error', 'Cancellation is only allowed 24 hours before the service date.');
        }

        if ($booking->payment_status === 'paid' && filled($booking->stripe_payment_intent_id)) {
            try {
                app(StripePaymentService::class)->refundBooking($booking);
            } catch (\Throwable $exception) {
                report($exception);

                return back()->with('error', 'Unable to process Stripe refund right now. Please contact support.');
            }
        }

        $booking->update([
            'status' => 'cancelled',
            'payment_status' => $booking->payment_status === 'paid' ? 'refunded' : $booking->payment_status,
            'refunded_at' => $booking->payment_status === 'paid' ? now() : $booking->refunded_at,
            'refund_amount' => $booking->payment_status === 'paid' ? $booking->total_price : $booking->refund_amount,
            'admin_notes' => 'Cancelled by user at ' . now()->format('Y-m-d H:i:s')
        ]);

        return back()->with('success', 'Booking cancelled successfully. Refund will be processed within 3-5 business days.');
    }

    /**
     * Display user's points and history
     */
    public function points()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->syncMembershipStatus();

        $pointHistory = $user->pointLedgers()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $expiringPoints = $user->pointLedgers()
            ->where('type', 'earned')
            ->where('expires_at', '<=', now()->addMonths(3))
            ->where('expires_at', '>', now())
            ->sum('points');

        return view('user.dashboard.points', compact('pointHistory', 'expiringPoints'));
    }

    /**
     * Display user's coupons
     */
    public function coupons(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $user->syncMembershipStatus();

        $source = (string) $request->input('source', 'all');

        $applySourceFilter = function ($query) use ($source) {
            if ($source === 'welcome') {
                $query->where('coupons.code', 'like', 'WELCOME-%');
                return;
            }

            if ($source === 'membership') {
                $query->where(function ($subQuery) {
                    $subQuery->where('coupons.code', 'like', 'GOLDNEW-%')
                        ->orWhere('coupons.code', 'like', 'GOLDRNW-%')
                        ->orWhere('coupons.code', 'like', 'PLATINUM_NEW-%')
                        ->orWhere('coupons.code', 'like', 'PLATINUM_RNW-%');
                });
                return;
            }

            if ($source === 'general') {
                $query->where('coupons.code', 'not like', 'WELCOME-%')
                    ->where('coupons.code', 'not like', 'GOLDNEW-%')
                    ->where('coupons.code', 'not like', 'GOLDRNW-%')
                    ->where('coupons.code', 'not like', 'PLATINUM_NEW-%')
                    ->where('coupons.code', 'not like', 'PLATINUM_RNW-%');
            }
        };

        $availableCouponsQuery = $user->availableCoupons();
        if (in_array($source, ['welcome', 'membership', 'general'], true)) {
            $applySourceFilter($availableCouponsQuery);
        }
        $availableCoupons = $availableCouponsQuery->get();

        $usedCouponsQuery = $user->coupons()
            ->wherePivot('usage_count', '>', 0)
            ->orderBy('user_coupons.last_used_at', 'desc');

        if (in_array($source, ['welcome', 'membership', 'general'], true)) {
            $applySourceFilter($usedCouponsQuery);
        }

        $usedCoupons = $usedCouponsQuery->get();

        return view('user.dashboard.coupons', compact('availableCoupons', 'usedCoupons', 'source'));
    }

    /**
     * Display user profile settings
     */
    public function profile()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->syncMembershipStatus();
        return view('user.dashboard.profile', compact('user'));
    }

    public function subscribeMembership(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $user->syncMembershipStatus();

        $validated = $request->validate([
            'tier' => 'required|in:gold,platinum',
            'action' => 'nullable|in:subscribe,renew',
        ]);

        $tier = MembershipTier::from($validated['tier']);

        if ($tier === MembershipTier::PLATINUM) {
            return back()->with('info', 'Platinum is a custom plan. Please contact support for activation.');
        }

        try {
            $session = app(StripePaymentService::class)
                ->createMembershipCheckoutSession($user, $tier, $validated['action'] ?? 'subscribe');

            return redirect($session->url);
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'Unable to initiate membership payment. Please try again.');
        }
    }

    public function membershipSuccess(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $user->syncMembershipStatus();

        $request->validate([
            'session_id' => 'required|string',
        ]);

        try {
            $session = app(StripePaymentService::class)
                ->retrieveCheckoutSession($request->string('session_id')->toString());

            if (($session->payment_status ?? null) !== 'paid') {
                return redirect()->route('membership')->with('error', 'Membership payment is not completed yet.');
            }

            if (data_get($session, 'metadata.purpose') !== 'membership_subscription') {
                return redirect()->route('membership')->with('error', 'Invalid membership payment session.');
            }

            if ((string) data_get($session, 'metadata.user_id') !== (string) $user->id) {
                return redirect()->route('membership')->with('error', 'This payment session does not belong to your account.');
            }

            $alreadyProcessed = DB::table('membership_payments')
                ->where('stripe_checkout_session_id', (string) $session->id)
                ->exists();

            if ($alreadyProcessed) {
                return redirect()->route('membership')->with('success', 'Membership payment already processed successfully.');
            }

            $tier = MembershipTier::from((string) data_get($session, 'metadata.tier', MembershipTier::SILVER->value));
            $action = (string) data_get($session, 'metadata.action', 'subscribe');

            if (!$tier->isPaidPlan()) {
                return redirect()->route('membership')->with('error', 'Invalid membership tier for subscription.');
            }

            $startsAt = now(config('app.timezone'));

            if (
                $action === 'renew'
                && $user->membership_tier === $tier->value
                && $user->membership_expires_at
                && $user->membership_expires_at->isFuture()
            ) {
                $startsAt = $user->membership_expires_at->copy()->addSecond();
            }

            DB::transaction(function () use ($user, $tier, $action, $session, $startsAt) {
                $user->activateMembership($tier, $startsAt);

                DB::table('membership_payments')->insert([
                    'user_id' => $user->id,
                    'tier' => $tier->value,
                    'action' => $action,
                    'stripe_checkout_session_id' => (string) $session->id,
                    'stripe_payment_intent_id' => (string) ($session->payment_intent ?? ''),
                    'amount_thb' => (int) (((int) ($session->amount_total ?? 0)) / 100),
                    'processed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            if ($tier === MembershipTier::GOLD) {
                app(CouponService::class)->issueGoldMembershipCoupon($user->fresh(), $action);
            }

            return redirect()->route('membership')->with(
                'success',
                sprintf(
                    '%s activated successfully. Valid until %s.',
                    $tier->label(),
                    optional($user->fresh()->membership_expires_at)?->format('d M Y H:i')
                )
            );
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('membership')->with('error', 'Unable to verify membership payment. Please contact support.');
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }
}
