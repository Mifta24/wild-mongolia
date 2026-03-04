<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Points History</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">History of all points transactions.</p>
                </div>
                <a href="{{ route('points.index') }}" class="text-sm text-teal-600 hover:text-teal-700">← Back to Dashboard</a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Type</label>
                        <select name="type" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="all">All</option>
                            @foreach(['earned','used','expired','refunded'] as $t)
                                <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">From</label>
                        <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">To</label>
                        <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="flex items-end gap-2">
                        <button class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Filter</button>
                        <a href="{{ route('points.history') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded">Reset</a>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-left text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Source</th>
                                <th class="px-4 py-3">Description</th>
                                <th class="px-4 py-3">Expires</th>
                                <th class="px-4 py-3 text-right">Points</th>
                                <th class="px-4 py-3 text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($transactions as $trx)
                                <tr>
                                    <td class="px-4 py-3">{{ $trx->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($trx->type) }}</td>
                                    <td class="px-4 py-3">{{ $trx->source ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $trx->description ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $trx->expires_at ? $trx->expires_at->format('d M Y') : '-' }}</td>
                                    <td class="px-4 py-3 text-right font-semibold {{ $trx->points >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $trx->points > 0 ? '+' : '' }}{{ number_format($trx->points) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($trx->balance_after) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No data found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">{{ $transactions->links() }}</div>
            </div>
        </div>
    </div>
</x-layouts.app>
