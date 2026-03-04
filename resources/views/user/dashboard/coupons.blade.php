<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Coupons</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your discount coupons</p>
                </div>
                <a href="{{ route('user.dashboard') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                    ← Back to Dashboard
                </a>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-5 mb-8">
                <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2">How to use coupons</h3>
                <ul class="text-xs text-blue-800 dark:text-blue-200 space-y-1">
                    <li>• Apply coupon code at checkout/payment before confirming the booking.</li>
                    <li>• Coupon must be active, within validity date, and match service type/minimum purchase.</li>
                    <li>• Each coupon has usage limits (per user and/or global usage).</li>
                    <li>• "Days left" shows remaining validity until the coupon expires.</li>
                </ul>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs text-gray-500 mr-1">Filter source:</span>
                    <a href="{{ route('user.coupons', ['source' => 'all']) }}"
                       class="px-3 py-1.5 rounded-full text-sm {{ ($source ?? 'all') === 'all' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        All
                    </a>
                    <a href="{{ route('user.coupons', ['source' => 'welcome']) }}"
                       class="px-3 py-1.5 rounded-full text-sm {{ ($source ?? 'all') === 'welcome' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        Welcome Bonus
                    </a>
                    <a href="{{ route('user.coupons', ['source' => 'membership']) }}"
                       class="px-3 py-1.5 rounded-full text-sm {{ ($source ?? 'all') === 'membership' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        Membership Bonus
                    </a>
                    <a href="{{ route('user.coupons', ['source' => 'general']) }}"
                       class="px-3 py-1.5 rounded-full text-sm {{ ($source ?? 'all') === 'general' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        General Coupon
                    </a>
                </div>
            </div>

            <!-- Available Coupons -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Available Coupons</h2>

                @if($availableCoupons->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($availableCoupons as $coupon)
                    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden border-2 border-teal-500">
                        <!-- Coupon Design -->
                        <div class="absolute top-0 right-0 w-24 h-24 bg-teal-500 transform rotate-12 translate-x-8 -translate-y-8"></div>
                        <div class="absolute top-4 right-4 text-white font-bold text-xs bg-teal-600 px-2 py-1 rounded">
                            ACTIVE
                        </div>

                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $coupon->name }}</h3>
                                    @php
                                        $codeUpper = strtoupper((string) $coupon->code);
                                        $sourceLabel = match (true) {
                                            str_starts_with($codeUpper, 'WELCOME-') => 'Welcome Bonus',
                                            str_starts_with($codeUpper, 'GOLDNEW-') => 'Gold Activation',
                                            str_starts_with($codeUpper, 'GOLDRNW-') => 'Gold Renewal',
                                            str_starts_with($codeUpper, 'PLATINUM_NEW-') => 'Platinum Activation',
                                            str_starts_with($codeUpper, 'PLATINUM_RNW-') => 'Platinum Renewal',
                                            default => 'General Coupon',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 mb-2">{{ $sourceLabel }}</span>
                                    @if($coupon->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $coupon->description }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Coupon Value -->
                            <div class="mb-4">
                                <div class="inline-flex items-baseline bg-teal-100 dark:bg-teal-900 px-4 py-2 rounded-lg">
                                    <span class="text-3xl font-bold text-teal-600 dark:text-teal-400">
                                        @if($coupon->type === 'fixed')
                                            THB {{ number_format($coupon->value) }}
                                        @else
                                            {{ $coupon->value }}%
                                        @endif
                                    </span>
                                    <span class="ml-2 text-sm text-teal-700 dark:text-teal-300">OFF</span>
                                </div>
                            </div>

                            <!-- Coupon Code -->
                            <div class="mb-4">
                                <label class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide">Coupon Code</label>
                                <div class="flex items-center mt-1">
                                    <code class="flex-1 bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded font-mono text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $coupon->code }}
                                    </code>
                                    <button onclick="copyCoupon('{{ $coupon->code }}')" class="ml-2 px-3 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 rounded text-sm">
                                        Copy
                                    </button>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                                @php
                                    $couponDaysLeft = now()->startOfDay()->diffInDays($coupon->valid_until->copy()->startOfDay(), false);
                                @endphp
                                @if($coupon->min_purchase)
                                <p>• Min. purchase: THB {{ number_format($coupon->min_purchase) }}</p>
                                @endif
                                @if($coupon->max_discount)
                                <p>• Max. discount: THB {{ number_format($coupon->max_discount) }}</p>
                                @endif
                                @if($coupon->applicable_to !== 'all')
                                <p>• Applicable to: {{ ucfirst($coupon->applicable_to) }} services only</p>
                                @endif
                                <p>• Valid until: {{ $coupon->valid_until->format('d M Y') }}</p>
                                <p>• Expires in:
                                    @if($couponDaysLeft > 1)
                                        {{ $couponDaysLeft }} days
                                    @elseif($couponDaysLeft === 1)
                                        1 day
                                    @elseif($couponDaysLeft === 0)
                                        today
                                    @else
                                        expired
                                    @endif
                                </p>
                                @php
                                    $userCoupon = $coupon->users->find(Auth::id());
                                    $remaining = $coupon->usage_per_user - ($userCoupon ? $userCoupon->pivot->usage_count : 0);
                                @endphp
                                <p>• Remaining uses: {{ $remaining }} time{{ $remaining > 1 ? 's' : '' }}</p>
                            </div>
                        </div>

                        <!-- Perforated Edge (decorative) -->
                        <div class="absolute left-0 top-1/2 transform -translate-y-1/2 -ml-3 w-6 h-6 bg-gray-50 dark:bg-gray-900 rounded-full"></div>
                        <div class="absolute right-0 top-1/2 transform -translate-y-1/2 -mr-3 w-6 h-6 bg-gray-50 dark:bg-gray-900 rounded-full"></div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No available coupons</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Check back later for special offers!</p>
                </div>
                @endif
            </div>

            <!-- Used Coupons -->
            @if($usedCoupons->count() > 0)
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Used Coupons</h2>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($usedCoupons as $coupon)
                    <div class="p-6 opacity-60">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $coupon->name }}</h3>
                                    @php
                                        $usedCodeUpper = strtoupper((string) $coupon->code);
                                        $usedSourceLabel = match (true) {
                                            str_starts_with($usedCodeUpper, 'WELCOME-') => 'Welcome Bonus',
                                            str_starts_with($usedCodeUpper, 'GOLDNEW-') => 'Gold Activation',
                                            str_starts_with($usedCodeUpper, 'GOLDRNW-') => 'Gold Renewal',
                                            str_starts_with($usedCodeUpper, 'PLATINUM_NEW-') => 'Platinum Activation',
                                            str_starts_with($usedCodeUpper, 'PLATINUM_RNW-') => 'Platinum Renewal',
                                            default => 'General Coupon',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 rounded">
                                        {{ $usedSourceLabel }}
                                    </span>
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                                        USED
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Code: <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded font-mono">{{ $coupon->code }}</code>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                    Last used: {{ $coupon->pivot->last_used_at ? \Carbon\Carbon::parse($coupon->pivot->last_used_at)->format('d M Y, H:i') : 'N/A' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Used {{ $coupon->pivot->usage_count }} time{{ $coupon->pivot->usage_count > 1 ? 's' : '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

    @push('scripts')
    <script>
        function copyCoupon(code) {
            navigator.clipboard.writeText(code).then(function() {
                // Show success message
                alert('Coupon code copied: ' + code);
            }, function(err) {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
    @endpush
</x-layouts.app>
