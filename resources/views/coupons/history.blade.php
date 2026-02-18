<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Coupon Usage History</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Riwayat penggunaan coupon kamu.</p>
                </div>
                <a href="{{ route('coupons.index') }}" class="text-sm text-teal-600 hover:text-teal-700">← Back to Coupons</a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-left text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Code</th>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3 text-right">Usage Count</th>
                                <th class="px-4 py-3">Last Used</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($history as $row)
                                <tr>
                                    <td class="px-4 py-3 font-mono font-semibold">{{ $row->code }}</td>
                                    <td class="px-4 py-3">{{ $row->name }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($row->usage_count) }}</td>
                                    <td class="px-4 py-3">{{ $row->last_used_at ? \Carbon\Carbon::parse($row->last_used_at)->format('d M Y H:i') : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada riwayat penggunaan coupon.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
