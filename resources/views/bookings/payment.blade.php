<x-layouts.app>
    @php
        $discountLocked = (bool) ($discountInfo['discount_locked'] ?? false);
        $initialCouponCode = (string) ($discountInfo['coupon_code'] ?? '');
        $initialCouponDiscount = (float) ($discountInfo['coupon_discount'] ?? 0);
        $initialPointsUsed = (int) ($booking->points_used ?? 0);
        $initialPointsDiscount = (float) ($discountInfo['points_discount'] ?? 0);
    @endphp

    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-12">
        <div
            class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8"
            x-data="checkoutPaymentState({
                baseAmount: {{ (float) $baseAmount }},
                availablePoints: {{ (int) $availablePoints }},
                pointRedeemMultiplier: {{ (float) $pointRedeemMultiplier }},
                coupons: {{ Js::from($availableCoupons) }},
                couponValidateUrl: '{{ route('coupons.validate') }}',
                csrfToken: '{{ csrf_token() }}',
                serviceType: '{{ $booking->service_type }}',
                discountLocked: {{ $discountLocked ? 'true' : 'false' }},
                initialCouponCode: '{{ $initialCouponCode }}',
                initialCouponDiscount: {{ $initialCouponDiscount }},
                initialPointsUsed: {{ $initialPointsUsed }},
                initialPointsDiscount: {{ $initialPointsDiscount }}
            })"
        >

            <div class="text-center mb-8">
                <div class="mx-auto w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Review & Pay</h1>
                <p class="text-gray-500">Please review your booking details before paying.</p>
            </div>

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if(request()->boolean('cancelled'))
                <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded" role="alert">
                    Payment was cancelled. You can retry checkout anytime.
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden mb-6">
                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex justify-between">
                    <span class="font-medium text-gray-700 dark:text-gray-200">Booking Code</span>
                    <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $booking->booking_code }}</span>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Guest Name</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $booking->guest_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Service</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $booking->product_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Date & Time</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ $booking->service_date->format('d M Y') }} at
                            {{ \Carbon\Carbon::parse($booking->service_time)->format('H:i') }}
                        </span>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 my-4"></div>

                    @if (($booking->add_ons_total ?? 0) > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Service Total</span>
                            <span class="font-medium text-gray-900 dark:text-white">MNT {{ number_format($baseAmount - $booking->add_ons_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Add-ons</span>
                            <span class="font-medium text-gray-900 dark:text-white">MNT {{ number_format($booking->add_ons_total, 2) }}</span>
                        </div>
                    @endif

                    @if(!empty($booking->selected_add_ons))
                        <div class="pt-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Selected Add-ons</p>
                            <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
                                @foreach($booking->selected_add_ons as $option)
                                    <li>• {{ $option['name'] ?? '-' }} (MNT {{ number_format((float) ($option['price'] ?? 0), 2) }})</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($booking->service_type === 'tour')
                        <div class="pt-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Meeting Point Confirmed</p>
                            <p class="text-sm font-medium {{ $booking->meeting_point_confirmed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $booking->meeting_point_confirmed ? 'Yes' : 'No' }}
                            </p>
                        </div>
                    @endif

                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Total Amount</span>
                        <div class="text-right">
                            <span class="text-base text-gray-500 line-through" x-show="hasAnyDiscount()">MNT <span x-text="formatMoney(baseAmount)"></span></span>
                            <span
                                class="text-2xl font-bold text-teal-600 block transition duration-300"
                                :class="flashPayable ? 'scale-105 text-emerald-600' : ''"
                            >MNT <span x-text="formatMoney(payableNow())"></span></span>
                        </div>
                    </div>

                    <div class="mt-4 border-t border-gray-100 dark:border-gray-700 pt-4 space-y-2 text-sm" x-show="hasAnyDiscount()">
                        <div class="rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-3 py-2" x-show="hasAnyDiscount()">
                            <p class="text-xs font-semibold text-green-700 dark:text-green-300">
                                You save MNT <span x-text="formatMoney(totalSavings())"></span>
                            </p>
                        </div>

                        <div class="flex justify-between" x-show="appliedCouponDiscount > 0">
                            <span class="text-gray-500">Coupon Discount</span>
                            <span class="font-medium text-green-600">- MNT <span x-text="formatMoney(appliedCouponDiscount)"></span></span>
                        </div>

                        <div class="flex justify-between" x-show="pointsDiscount() > 0">
                            <span class="text-gray-500">Points Discount (<span x-text="pointsToUse()"></span> pts)</span>
                            <span class="font-medium text-green-600">- MNT <span x-text="formatMoney(pointsDiscount())"></span></span>
                        </div>

                        <div class="flex justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                            <span class="font-semibold text-gray-900 dark:text-white">Payable Now</span>
                            <span class="font-bold text-teal-600">MNT <span x-text="formatMoney(payableNow())"></span></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 mb-8">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Select Payment Method</h3>

                <form action="{{ route('booking.process', $booking->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="points_to_use" :value="pointsToUse()">
                    <input type="hidden" name="coupon_code" :value="appliedCouponCode">

                    <div class="mb-5 rounded-lg border border-gray-200 dark:border-gray-600 p-4 space-y-4">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Use Points & Coupon</p>

                        <div class="flex items-start justify-between gap-3 rounded-lg border border-gray-200 dark:border-gray-600 p-3">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Use all points</p>
                                <p class="mt-1 text-xs text-gray-500">
                                    Available points: {{ number_format($availablePoints) }}
                                    (x {{ number_format((float) $pointRedeemMultiplier, 2) }} redemption factor)
                                </p>
                            </div>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" x-model="useAllPoints" :disabled="discountLocked || availablePoints === 0">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:bg-teal-600 after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Coupon code</label>
                            <div class="mt-1 flex gap-2">
                                <input type="text"
                                x-model="couponCodeInput"
                                :disabled="discountLocked"
                                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 uppercase disabled:opacity-60"
                                placeholder="e.g. WELCOME-AB12CD">
                                <button
                                    type="button"
                                    @click="applyCouponCode()"
                                    :disabled="discountLocked || couponApplying || !couponCodeInput.trim()"
                                    class="px-4 py-2 rounded-md bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700 disabled:opacity-60"
                                >
                                    <span x-show="!couponApplying">Apply</span>
                                    <span x-show="couponApplying">Applying...</span>
                                </button>
                                <button
                                    type="button"
                                    @click="clearCoupon()"
                                    :disabled="discountLocked || !appliedCouponCode"
                                    class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm font-semibold text-gray-700 dark:text-gray-200 disabled:opacity-60"
                                >
                                    Clear
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Coupon will be validated against amount, validity period, and service type.</p>
                            <p class="mt-2 text-xs text-green-600" x-show="couponMessage && !couponError" x-text="couponMessage"></p>
                            <p class="mt-2 text-xs text-red-600" x-show="couponError" x-text="couponError"></p>
                        </div>

                        @if(!empty($availableCoupons))
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Available coupons</p>
                                <div class="space-y-2 max-h-36 overflow-y-auto pr-1">
                                    @foreach($availableCoupons as $coupon)
                                        <button type="button"
                                            @click="chooseCoupon('{{ $coupon['code'] }}')"
                                            :disabled="discountLocked"
                                            class="w-full text-left rounded-md border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">
                                            <div class="flex justify-between gap-2">
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $coupon['code'] }}</span>
                                                <span class="text-teal-600">{{ $coupon['type'] === 'fixed' ? 'MNT ' . number_format((float) $coupon['value'], 2) : rtrim(rtrim(number_format((float) $coupon['value'], 2), '0'), '.') . '%' }}</span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">{{ $coupon['name'] }}</p>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($discountLocked)
                            <p class="text-xs text-amber-600">Discount is locked for this booking. Continue the same payment session to keep applied discounts.</p>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <label
                            class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                            <input type="radio" name="payment_method" value="credit_card" checked
                                class="h-4 w-4 text-teal-600 focus:ring-teal-500">
                            <span class="ml-3 font-medium text-gray-900 dark:text-white">Credit / Debit Card
                                (Stripe)</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="mt-6 w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition">
                        <span x-show="payableNow() > 0">Pay Securely -></span>
                        <span x-show="payableNow() <= 0">Confirm Booking (No Payment Needed)</span>
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="{{ route('booking.create') }}" class="text-sm text-gray-500 hover:underline">Cancel &
                        Return</a>
                </div>
            </div>

        </div>
    </div>

    <script>
        function checkoutPaymentState(config) {
            return {
                baseAmount: Number(config.baseAmount || 0),
                availablePoints: Number(config.availablePoints || 0),
                pointRedeemMultiplier: Number(config.pointRedeemMultiplier || 1),
                coupons: Array.isArray(config.coupons) ? config.coupons : [],
                couponValidateUrl: config.couponValidateUrl || '',
                csrfToken: config.csrfToken || '',
                serviceType: config.serviceType || '',
                discountLocked: !!config.discountLocked,
                useAllPoints: Number(config.initialPointsUsed || 0) > 0,
                couponCodeInput: String(config.initialCouponCode || ''),
                appliedCouponCode: String(config.initialCouponCode || ''),
                appliedCouponDiscount: Number(config.initialCouponDiscount || 0),
                couponMessage: '',
                couponError: '',
                couponApplying: false,
                flashPayable: false,
                payableSnapshot: Number(config.baseAmount || 0),

                init() {
                    this.$watch('useAllPoints', () => this.triggerPayableFlashIfChanged());
                    this.$watch('appliedCouponDiscount', () => this.triggerPayableFlashIfChanged());
                },

                pointsToUse() {
                    return this.useAllPoints ? this.availablePoints : 0;
                },

                pointsDiscount() {
                    const rawDiscount = this.pointsToUse() * this.pointRedeemMultiplier;
                    return Math.min(rawDiscount, this.amountAfterCoupon());
                },

                amountAfterCoupon() {
                    return Math.max(0, this.baseAmount - this.appliedCouponDiscount);
                },

                payableNow() {
                    return Math.max(0, this.baseAmount - this.appliedCouponDiscount - this.pointsDiscount());
                },

                totalSavings() {
                    return Math.max(0, this.baseAmount - this.payableNow());
                },

                hasAnyDiscount() {
                    return this.appliedCouponDiscount > 0 || this.pointsDiscount() > 0;
                },

                formatMoney(amount) {
                    return Number(amount || 0).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    });
                },

                clearCoupon() {
                    if (this.discountLocked) {
                        return;
                    }

                    this.couponCodeInput = '';
                    this.appliedCouponCode = '';
                    this.appliedCouponDiscount = 0;
                    this.couponMessage = '';
                    this.couponError = '';
                    this.triggerPayableFlashIfChanged();
                },

                chooseCoupon(code) {
                    if (this.discountLocked) {
                        return;
                    }

                    this.couponCodeInput = code;
                    this.applyCouponCode();
                },

                async applyCouponCode() {
                    if (this.discountLocked) {
                        return;
                    }

                    const code = this.couponCodeInput.trim().toUpperCase();
                    if (!code) {
                        this.clearCoupon();
                        return;
                    }

                    this.couponApplying = true;
                    this.couponError = '';
                    this.couponMessage = '';

                    try {
                        const response = await fetch(this.couponValidateUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                code,
                                amount: this.baseAmount,
                                service_type: this.serviceType,
                            }),
                        });

                        const result = await response.json();
                        if (!response.ok || !result.valid) {
                            this.appliedCouponCode = '';
                            this.appliedCouponDiscount = 0;
                            this.couponError = result.message || 'Coupon is invalid.';
                            return;
                        }

                        this.appliedCouponCode = code;
                        this.appliedCouponDiscount = Number(result.discount || 0);
                        this.couponMessage = `Coupon ${code} applied.`;
                        this.triggerPayableFlashIfChanged();
                    } catch (_error) {
                        this.appliedCouponCode = '';
                        this.appliedCouponDiscount = 0;
                        this.couponError = 'Unable to validate coupon right now.';
                        this.triggerPayableFlashIfChanged();
                    } finally {
                        this.couponApplying = false;
                    }
                },

                triggerPayableFlashIfChanged() {
                    const currentPayable = this.payableNow();
                    if (Math.abs(currentPayable - this.payableSnapshot) < 0.0001) {
                        return;
                    }

                    this.payableSnapshot = currentPayable;
                    this.flashPayable = false;

                    requestAnimationFrame(() => {
                        this.flashPayable = true;
                        setTimeout(() => {
                            this.flashPayable = false;
                        }, 320);
                    });
                },
            };
        }
    </script>
</x-layouts.app>
