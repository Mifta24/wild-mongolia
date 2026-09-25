<x-layouts.admin.app>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Coupons</h1>
        <a href="{{ route('admin.coupons.create') }}" class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Create Coupon</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-left text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Value</th>
                        <th class="px-4 py-3">Usage</th>
                        <th class="px-4 py-3">Valid Until</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($coupons as $coupon)
                        <tr>
                            <td class="px-4 py-3 font-mono font-semibold">{{ $coupon->code }}</td>
                            <td class="px-4 py-3">{{ $coupon->name }}</td>
                            <td class="px-4 py-3">{{ ucfirst($coupon->type) }}</td>
                            <td class="px-4 py-3">
                                @if($coupon->type === 'fixed')
                                    ₮{{ number_format($coupon->value, 2) }}
                                @else
                                    {{ rtrim(rtrim(number_format($coupon->value,2),'0'),'.') }}%
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $coupon->usage_count }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                            <td class="px-4 py-3">{{ $coupon->valid_until?->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs {{ $coupon->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">Edit</a>
                                    <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon) }}" class="inline">
                                        @csrf
                                        <button class="px-3 py-1 bg-amber-600 text-white rounded hover:bg-amber-700">Toggle</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirm('Delete this coupon?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No coupons found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">{{ $coupons->links() }}</div>
    </div>
</x-layouts.admin.app>
