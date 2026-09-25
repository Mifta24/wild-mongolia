<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Points Dashboard</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Balance, tier, and latest transactions.</p>
                </div>
                <a href="{{ route('user.dashboard') }}" class="text-sm text-teal-600 hover:text-teal-700">← Back</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <p class="text-xs text-gray-500">Current Balance</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['current_balance'] ?? 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <p class="text-xs text-gray-500">Lifetime Earned</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['lifetime_earned'] ?? 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <p class="text-xs text-gray-500">Expiring Soon</p>
                    <p class="text-2xl font-bold text-amber-600">{{ number_format($summary['expiring_soon'] ?? 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <p class="text-xs text-gray-500">Membership Tier</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['tier_label'] ?? '-' }}</p>
                </div>
            </div>

            @php
                $daysUntilYearEnd = now()->startOfDay()->diffInDays(now()->copy()->endOfYear()->startOfDay(), false);
            @endphp
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                <p class="text-sm text-yellow-800 dark:text-yellow-300">
                    Points expire at year-end (31 Dec, Mongolia time).
                    {{ $daysUntilYearEnd >= 0 ? $daysUntilYearEnd . ' day' . ($daysUntilYearEnd === 1 ? '' : 's') . ' left.' : 'Year-end expiry is in progress.' }}
                </p>
            </div>

            @if(!empty($summary['next_tier']))
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Next tier <strong>{{ $summary['next_tier']['label'] }}</strong>: {{ $summary['next_tier']['note'] ?? 'Upgrade available.' }}
                    </p>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Recent Transactions</h2>
                    <a href="{{ route('points.history') }}" class="text-sm text-teal-600 hover:text-teal-700">View all</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-left text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Description</th>
                                <th class="px-4 py-3 text-right">Points</th>
                                <th class="px-4 py-3 text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($recentTransactions as $trx)
                                <tr>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $trx->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-medium {{ in_array($trx->type, ['earned','refunded']) ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">{{ ucfirst($trx->type) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $trx->description ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right font-semibold {{ $trx->points >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $trx->points > 0 ? '+' : '' }}{{ number_format($trx->points) }}</td>
                                    <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">{{ number_format($trx->balance_after) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No points transactions yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
