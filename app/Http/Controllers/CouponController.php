<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * Display available coupons
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $coupons = $this->couponService->getAvailableCoupons(
            $user,
            $request->input('amount', 0),
            $request->input('service_type')
        );

        return view('coupons.index', compact('coupons'));
    }

    /**
     * Validate a coupon code (API)
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'service_type' => 'nullable|in:car,tour',
        ]);

        $result = $this->couponService->validateCoupon(
            $request->code,
            Auth::user(),
            $request->amount,
            $request->service_type
        );

        return response()->json($result);
    }

    /**
     * Get available coupons (API)
     */
    public function available(Request $request)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'service_type' => 'nullable|in:car,tour',
        ]);

        $coupons = $this->couponService->getAvailableCoupons(
            Auth::user(),
            $request->input('amount', 0),
            $request->input('service_type')
        );

        return response()->json([
            'success' => true,
            'data' => $coupons,
        ]);
    }

    /**
     * Show user's coupon usage history
     */
    public function history()
    {
        $user = Auth::user();
        $history = $this->couponService->getUserCouponHistory($user);

        return view('coupons.history', compact('history'));
    }
}
