<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Points</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Track your points balance and history</p>
                </div>
                <a href="{{ route('user.dashboard') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                    ← Back to Dashboard
                </a>
            </div>

            <!-- Points Balance Card -->
            <div class="bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl shadow-xl p-8 mb-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-teal-100 text-sm font-medium mb-2">Available Points</p>
                        <p class="text-5xl font-bold">{{ number_format(Auth::user()->points) }}</p>
                        <p class="text-teal-100 text-sm mt-2">Worth ~THB {{ number_format(Auth::user()->points) }} (1 point = THB 1)</p>
                    </div>
                    <div class="h-24 w-24 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Expiring Points Alert -->
            @if($expiringPoints > 0)
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        @php
                            $daysUntilYearEnd = now()->startOfDay()->diffInDays(now()->copy()->endOfYear()->startOfDay(), false);
                        @endphp
                        <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-400">Points Expiring Soon</h4>
                        <p class="text-xs text-yellow-700 dark:text-yellow-500 mt-1">
                            You have {{ number_format($expiringPoints) }} points expiring before the year changes.
                            {{ $daysUntilYearEnd >= 0 ? $daysUntilYearEnd . ' day' . ($daysUntilYearEnd === 1 ? '' : 's') . ' left until 31 Dec.' : 'Year-end expiry is in progress.' }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- How Points Work -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-400 mb-3">How Points Work</h3>
                <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-300">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Earn points from every paid booking (base: 1 point per THB 100, tier multiplier applies)</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Redeem points directly as discount at checkout</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>All earned points expire at year-end (31 Dec, 23:59 Mongolia time)</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>1 point = THB 1 discount (tier bonus may increase redemption value)</span>
                    </li>
                </ul>
            </div>

            <!-- Points History -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Transaction History</h2>
                </div>

                @if($pointHistory->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($pointHistory as $transaction)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-1">
                                    @if($transaction->type === 'earned')
                                    <span class="flex-shrink-0 h-8 w-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                        <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </span>
                                    @else
                                    <span class="flex-shrink-0 h-8 w-8 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                                        <svg class="h-4 w-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </span>
                                    @endif
                                    <div>
                                        @php
                                            $sourceLabel = match ((string) $transaction->source) {
                                                'booking' => 'Booking Reward',
                                                'booking_discount' => 'Booking Redemption',
                                                'booking_refund' => 'Booking Refund Adjustment',
                                                'membership_cashback' => 'Membership Cashback',
                                                'signup_bonus' => 'Welcome Bonus',
                                                'admin_adjustment' => 'Admin Adjustment',
                                                'expiry' => 'Points Expiry',
                                                default => ucfirst(str_replace('_', ' ', (string) $transaction->source)),
                                            };
                                        @endphp
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ ucfirst($transaction->type) }} Points
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $transaction->description ?? $sourceLabel }}
                                        </p>
                                    </div>
                                </div>
                                <div class="ml-11 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $transaction->created_at->format('d M Y, H:i') }}
                                    @if($transaction->expires_at)
                                    @php
                                        $daysLeft = now()->startOfDay()->diffInDays($transaction->expires_at->copy()->startOfDay(), false);
                                    @endphp
                                    · Expires: {{ $transaction->expires_at->format('d M Y') }}
                                    @if($daysLeft >= 0)
                                        ({{ $daysLeft }} day{{ $daysLeft === 1 ? '' : 's' }} left)
                                    @else
                                        (expired)
                                    @endif
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4 text-right">
                                @php
                                    $pointsColorClass = $transaction->type === 'earned'
                                        ? 'text-green-600 dark:text-green-400'
                                        : 'text-red-600 dark:text-red-400';
                                @endphp
                                <p class="text-lg font-bold {{ $pointsColorClass }}">
                                    {{ $transaction->type === 'earned' ? '+' : '' }}{{ number_format($transaction->points) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Balance: {{ number_format($transaction->balance_after) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700">
                    {{ $pointHistory->links() }}
                </div>
                @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No transaction history yet</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Start booking to earn points!</p>
                </div>
                @endif
            </div>

        </div>
    </div>
</x-layouts.app>
