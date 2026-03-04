<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Available Coupons</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">List of coupons you can use.</p>
                </div>
                <a href="{{ route('user.dashboard') }}" class="text-sm text-teal-600 hover:text-teal-700">← Back</a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Amount</label>
                        <input type="number" min="0" step="0.01" name="amount" value="{{ request('amount') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Order amount">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Service Type</label>
                        <select name="service_type" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">All</option>
                            <option value="car" @selected(request('service_type')==='car')>Car</option>
                            <option value="tour" @selected(request('service_type')==='tour')>Tour</option>
                        </select>
                    </div>
                    <input type="hidden" name="source" value="{{ request('source', 'all') }}">
                    <div class="md:col-span-2 flex items-end gap-2">
                        <button class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Apply Filter</button>
                        <a href="{{ route('coupons.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded">Reset</a>
                        <a href="{{ route('coupons.history') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">My Usage History</a>
                    </div>
                </form>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="text-xs text-gray-500 mr-1">Filter source:</span>
                    <a href="{{ route('coupons.index', ['amount' => request('amount'), 'service_type' => request('service_type'), 'source' => 'all']) }}"
                       class="px-3 py-1.5 rounded-full text-sm {{ request('source', 'all') === 'all' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        All
                    </a>
                    <a href="{{ route('coupons.index', ['amount' => request('amount'), 'service_type' => request('service_type'), 'source' => 'welcome']) }}"
                       class="px-3 py-1.5 rounded-full text-sm {{ request('source') === 'welcome' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        Welcome Bonus
                    </a>
                    <a href="{{ route('coupons.index', ['amount' => request('amount'), 'service_type' => request('service_type'), 'source' => 'membership']) }}"
                       class="px-3 py-1.5 rounded-full text-sm {{ request('source') === 'membership' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        Membership Bonus
                    </a>
                    <a href="{{ route('coupons.index', ['amount' => request('amount'), 'service_type' => request('service_type'), 'source' => 'general']) }}"
                       class="px-3 py-1.5 rounded-full text-sm {{ request('source') === 'general' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        General Coupon
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($coupons as $coupon)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $coupon['name'] }}</h3>
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-xs px-2 py-1 rounded bg-teal-100 text-teal-700 dark:bg-teal-900 dark:text-teal-300">{{ strtoupper($coupon['type']) }}</span>
                                <span class="text-xs px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">{{ $coupon['source_label'] ?? 'General Coupon' }}</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ $coupon['description'] ?: '-' }}</p>
                        <div class="space-y-1 text-sm mb-3">
                            <p><span class="text-gray-500">Code:</span> <span class="font-mono font-semibold">{{ $coupon['code'] }}</span></p>
                            <p><span class="text-gray-500">Value:</span> {{ $coupon['type'] === 'fixed' ? 'THB '.number_format($coupon['value'],2) : rtrim(rtrim(number_format($coupon['value'],2), '0'), '.').'%' }}</p>
                            <p><span class="text-gray-500">Min purchase:</span> {{ $coupon['min_purchase'] ? 'THB '.number_format($coupon['min_purchase'],2) : '-' }}</p>
                            <p><span class="text-gray-500">Valid until:</span> {{ $coupon['valid_until'] }}</p>
                            @if(!empty($coupon['discount_preview']))
                                <p class="text-green-600"><span class="text-gray-500">Discount preview:</span> THB {{ number_format($coupon['discount_preview'],2) }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center text-gray-500">
                        No coupons match the selected filter.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
