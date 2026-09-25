<x-layouts.admin.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Dashboard Overview</h1>

            @if ($deactivatedAccounts > 0)
                <div class="mb-8 rounded-xl border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 p-5">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-amber-900 dark:text-amber-200">Deactivated Accounts Monitoring</h2>
                            <p class="text-sm text-amber-800 dark:text-amber-300 mt-1">
                                {{ $deactivatedAccounts }} deactivated account(s) total, with {{ $accountsPendingDeletionSoon }} account(s) reaching permanent deletion in the next 7 days.
                            </p>
                        </div>
                        <a href="{{ route('admin.customers.index', ['status' => 'deactivated']) }}"
                            class="inline-flex items-center px-3 py-2 rounded-md bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium transition">
                            Review Deactivated Accounts
                        </a>
                    </div>

                    @if ($upcomingDeactivatedUsers->isNotEmpty())
                        <div class="mt-4 overflow-x-auto">
                            <table class="w-full text-sm text-left text-amber-900 dark:text-amber-200">
                                <thead>
                                    <tr class="text-xs uppercase text-amber-700 dark:text-amber-300">
                                        <th class="py-2 pr-4">Name</th>
                                        <th class="py-2 pr-4">Email</th>
                                        <th class="py-2 pr-4">Deletion Deadline</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($upcomingDeactivatedUsers as $account)
                                        <tr class="border-t border-amber-200 dark:border-amber-700/50">
                                            <td class="py-2 pr-4 font-medium">{{ $account->name }}</td>
                                            <td class="py-2 pr-4">{{ $account->email }}</td>
                                            <td class="py-2 pr-4">{{ $account->scheduled_for_deletion_at?->format('d M Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-teal-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-teal-100 dark:bg-teal-900 text-teal-600 dark:text-teal-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">MNT
                                {{ number_format($totalRevenue) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Actions</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $newBookings }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Today's Orders</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $todaysBookings }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Members</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalMembers }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Bookings</h3>
                    <a href="{{ route('admin.bookings.index') }}"
                        class="text-sm text-teal-600 hover:text-teal-800 font-medium">View All &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Booking Code</th>
                                <th class="px-6 py-3">Customer</th>
                                <th class="px-6 py-3">Service Date</th>
                                <th class="px-6 py-3">Amount</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                                <tr
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        #{{ $booking->booking_code }}</td>
                                    <td class="px-6 py-4">{{ $booking->guest_name }}</td>
                                    <td class="px-6 py-4">{{ $booking->service_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4">MNT {{ number_format($booking->total_price) }}</td>
                                    <td class="px-6 py-4">
                                        @if ($booking->status == 'pending')
                                            <span
                                                class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Pending</span>
                                        @elseif($booking->status == 'confirmed')
                                            <span
                                                class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Confirmed</span>
                                        @elseif($booking->status == 'completed')
                                            <span
                                                class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Completed</span>
                                        @else
                                            <span
                                                class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Cancelled</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">No recent bookings.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-layouts.admin.app>
