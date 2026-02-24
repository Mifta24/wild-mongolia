<x-layouts.admin.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">All Bookings</h1>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm mb-6">
                <form action="{{ route('admin.bookings.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">

                    <div class="w-full md:w-1/4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Filter by Status</label>
                        <select name="status" onchange="this.form.submit()"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500 text-sm">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed
                            </option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                    </div>

                    <div class="w-full md:w-2/4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search by Booking Code, Guest Name..."
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white pl-10 focus:ring-teal-500 focus:border-teal-500 text-sm">
                        </div>
                    </div>

                    <div class="w-full md:w-1/4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Meeting Point (Tour)</label>
                        <select name="meeting_point" onchange="this.form.submit()"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500 text-sm">
                            <option value="">All</option>
                            <option value="yes" {{ request('meeting_point') == 'yes' ? 'selected' : '' }}>Confirmed</option>
                            <option value="no" {{ request('meeting_point') == 'no' ? 'selected' : '' }}>Not Confirmed</option>
                        </select>
                    </div>

                    <div class="w-full md:w-auto flex items-end">
                        <a href="{{ route('admin.bookings.index') }}"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 transition">Reset</a>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Booking Code</th>
                                <th class="px-6 py-3">Customer Info</th>
                                <th class="px-6 py-3">Service</th>
                                <th class="px-6 py-3">Meeting Point</th>
                                <th class="px-6 py-3">Total</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                            class="hover:underline hover:text-teal-600">
                                            #{{ $booking->booking_code }}
                                        </a>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-white">
                                            {{ $booking->guest_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $booking->guest_email }}</div>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($booking->service_type == 'car')
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300">
                                                🚗 Car
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300">
                                                🏝️ Tour
                                            </span>
                                        @endif
                                        <div class="mt-1 text-xs truncate max-w-[150px]"
                                            title="{{ $booking->product_name }}">
                                            {{ $booking->product_name }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($booking->service_type === 'tour')
                                            @if ($booking->meeting_point_confirmed)
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300 border border-green-200">Yes</span>
                                            @else
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300 border border-red-200">No</span>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 font-mono">
                                        THB {{ number_format($booking->total_price) }}
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ $booking->service_date->format('d M Y') }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($booking->status == 'pending')
                                            <span
                                                class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300 border border-yellow-200">Pending</span>
                                        @elseif($booking->status == 'confirmed')
                                            <span
                                                class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 border border-blue-200">Confirmed</span>
                                        @elseif($booking->status == 'completed')
                                            <span
                                                class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300 border border-green-200">Completed</span>
                                        @else
                                            <span
                                                class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300 border border-red-200">Cancelled</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                            class="text-teal-600 dark:text-teal-400 hover:text-teal-900 font-medium">Manage</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            <p>No bookings found matching your search.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    {{ $bookings->links() }}
                </div>
            </div>

        </div>
    </div>
</x-layouts.admin.app>
