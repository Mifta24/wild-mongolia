<x-layouts.admin.app>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Points Management</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.points.membership-stats') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Membership Stats</a>
            <form action="{{ route('admin.points.expire') }}" method="POST" onsubmit="return confirm('Jalankan expiry points sekarang?')">
                @csrf
                <button class="px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700">Run Expiry</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded shadow p-4"><p class="text-xs text-gray-500">Issued</p><p class="text-xl font-bold">{{ number_format($stats['total_points_issued'] ?? 0) }}</p></div>
        <div class="bg-white dark:bg-gray-800 rounded shadow p-4"><p class="text-xs text-gray-500">Used</p><p class="text-xl font-bold">{{ number_format($stats['total_points_used'] ?? 0) }}</p></div>
        <div class="bg-white dark:bg-gray-800 rounded shadow p-4"><p class="text-xs text-gray-500">Expired</p><p class="text-xl font-bold">{{ number_format($stats['total_points_expired'] ?? 0) }}</p></div>
        <div class="bg-white dark:bg-gray-800 rounded shadow p-4"><p class="text-xs text-gray-500">Active Balance</p><p class="text-xl font-bold">{{ number_format($stats['total_active_balance'] ?? 0) }}</p></div>
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
                <label class="block text-xs text-gray-500 mb-1">User ID</label>
                <input type="number" name="user_id" value="{{ request('user_id') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="e.g 1">
            </div>
            <div class="md:col-span-2 flex items-end gap-2">
                <button class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Filter</button>
                <a href="{{ route('admin.points.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-left text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Source</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3 text-right">Points</th>
                        <th class="px-4 py-3 text-right">Balance</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($transactions as $trx)
                        <tr>
                            <td class="px-4 py-3">{{ $trx->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $trx->user?->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500">#{{ $trx->user_id }}</div>
                            </td>
                            <td class="px-4 py-3">{{ ucfirst($trx->type) }}</td>
                            <td class="px-4 py-3">{{ $trx->source ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $trx->description ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $trx->points >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $trx->points > 0 ? '+' : '' }}{{ number_format($trx->points) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($trx->balance_after) }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.points.adjust.form', $trx->user_id) }}" class="text-teal-600 hover:text-teal-700">Adjust</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No transactions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">{{ $transactions->links() }}</div>
    </div>
</x-layouts.admin.app>
