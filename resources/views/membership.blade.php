<x-layouts.app>
    <section class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            @if(session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                    {{ session('info') }}
                </div>
            @endif

            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <p class="text-sm uppercase tracking-wide text-primary font-semibold mb-2">Loyalty Program</p>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">Membership Wild Mongolia
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">Get exclusive benefits for business trips
                        and regular vacations: special pricing, priority support, and vehicle upgrades.</p>
                    <div class="flex flex-wrap gap-3 mb-6">
                        <span class="px-3 py-2 bg-teal-50 text-primary rounded-full text-sm">Member discounts</span>
                        <span class="px-3 py-2 bg-teal-50 text-primary rounded-full text-sm">Priority support 24/7</span>
                        <span class="px-3 py-2 bg-teal-50 text-primary rounded-full text-sm">Point rewards</span>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="/contact"
                            class="px-5 py-3 bg-primary text-white rounded-lg hover:bg-teal-700 transition">Join
                            now</a>
                        <a href="/faq"
                            class="px-5 py-3 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 transition">View
                            FAQ</a>
                    </div>
                </div>
                <div
                    class="bg-gray-50 dark:bg-gray-800/70 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Membership plans</h2>
                    @auth
                        @php
                            /** @var \App\Models\User $authUser */
                            $authUser = auth()->user();
                            $currentTier = $authUser->membership_tier;
                            $currentExpiry = $authUser->membership_expires_at;
                            $renewalWindowDays = 14;

                            $hasActivePaidMembership = in_array($currentTier, ['gold', 'platinum'], true)
                                && $currentExpiry
                                && $currentExpiry->isFuture();

                            $activeGold = $hasActivePaidMembership && $currentTier === 'gold';
                            $activePlatinum = $hasActivePaidMembership && $currentTier === 'platinum';

                            $showRenewGold = $activeGold
                                && $currentExpiry->lte(now(config('app.timezone'))->addDays($renewalWindowDays));

                            $showRenewPlatinum = $activePlatinum
                                && $currentExpiry->lte(now(config('app.timezone'))->addDays($renewalWindowDays));
                        @endphp
                    @endauth
                    <div class="space-y-4">
                        <div
                            class="p-4 rounded-xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-semibold text-gray-900 dark:text-white">Silver</p>
                                <span class="text-sm text-primary font-semibold">Free</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Default tier for all users with 1x point multiplier and access to standard promos.</p>
                        </div>
                        <div
                            class="p-4 rounded-xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-semibold text-gray-900 dark:text-white">Gold</p>
                                <span class="text-sm text-primary font-semibold">THB 1,500 / year</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Paid annual subscription with 1.5x point multiplier, redemption bonus, and priority support.</p>
                            @auth
                                @if(!$hasActivePaidMembership)
                                    <form method="POST" action="{{ route('user.membership.subscribe') }}" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="tier" value="gold">
                                        <input type="hidden" name="action" value="subscribe">
                                        <button type="submit" class="w-full px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium">
                                            Subscribe Gold (Pay with Stripe)
                                        </button>
                                    </form>
                                @elseif($showRenewGold)
                                    <form method="POST" action="{{ route('user.membership.subscribe') }}" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="tier" value="gold">
                                        <input type="hidden" name="action" value="renew">
                                        <button type="submit" class="w-full px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium">
                                            Renew Gold (Pay with Stripe)
                                        </button>
                                    </form>
                                @endif

                                @if($activeGold)
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Active until {{ $currentExpiry->format('d M Y H:i') }}
                                    </p>
                                    @if(!$showRenewGold)
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Renewal button will appear {{ $renewalWindowDays }} days before expiry.
                                        </p>
                                    @endif
                                @elseif($hasActivePaidMembership)
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Hidden while {{ ucfirst($currentTier) }} membership is still active.
                                    </p>
                                @endif
                            @else
                                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Login required to subscribe.</p>
                            @endauth
                        </div>
                        <div
                            class="p-4 rounded-xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-semibold text-gray-900 dark:text-white">Platinum</p>
                                <span class="text-sm text-primary font-semibold">THB 3,500 / year</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Premium annual plan with 2x point multiplier, highest redemption bonus, and dedicated support.</p>
                            @auth
                                @if(!$hasActivePaidMembership)
                                    <form method="POST" action="{{ route('user.membership.subscribe') }}" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="tier" value="platinum">
                                        <input type="hidden" name="action" value="subscribe">
                                        <button type="submit" class="w-full px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium">
                                            Subscribe Platinum (Pay with Stripe)
                                        </button>
                                    </form>
                                @elseif($showRenewPlatinum)
                                    <form method="POST" action="{{ route('user.membership.subscribe') }}" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="tier" value="platinum">
                                        <input type="hidden" name="action" value="renew">
                                        <button type="submit" class="w-full px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium">
                                            Renew Platinum (Pay with Stripe)
                                        </button>
                                    </form>
                                @endif

                                @if($activePlatinum)
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Active until {{ $currentExpiry->format('d M Y H:i') }}
                                    </p>
                                    @if(!$showRenewPlatinum)
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Renewal button will appear {{ $renewalWindowDays }} days before expiry.
                                        </p>
                                    @endif
                                @elseif($hasActivePaidMembership)
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Hidden while {{ ucfirst($currentTier) }} membership is still active.
                                    </p>
                                @endif
                            @else
                                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Login required to subscribe.</p>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Key benefits</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div
                    class="p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Consistent discounts</p>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">Savings across all services, with no weekly limits.
                    </p>
                </div>
                <div
                    class="p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Priority support</p>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">Fast-track assistance for urgent schedule
                        changes.</p>
                </div>
                <div
                    class="p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Credit & rewards</p>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">Collect points for vehicle upgrades or
                        complimentary tours.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Points & Coupon Rules</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="p-5 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Points usage</p>
                    <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                        <li>• 1 point = THB 1 discount.</li>
                        <li>• New members receive 300 welcome points after registration.</li>
                        <li>• Points can be used at checkout for the next booking payment.</li>
                        <li>• All earned points expire at year-end (31 Dec, Mongolia time).</li>
                        <li>• Gold and Platinum plans are valid for 1 year from activation date.</li>
                    </ul>
                </div>
                <div class="p-5 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Coupon usage</p>
                    <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                        <li>• Enter coupon code during checkout before payment confirmation.</li>
                        <li>• Coupon is valid only within its date range and usage limits.</li>
                        <li>• Some coupons require minimum purchase or specific service type.</li>
                        <li>• The system shows remaining days before coupon expiry.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
