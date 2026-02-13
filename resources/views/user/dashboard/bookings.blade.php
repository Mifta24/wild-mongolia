<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Bookings</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">View and manage your booking history</p>
                </div>
                <a href="{{ route('user.dashboard') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                    ← Back to Dashboard
                </a>
            </div>

            <!-- Filter Tabs -->
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('user.bookings', ['status' => 'all']) }}"
                       class="@if($status === 'all') border-teal-500 text-teal-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        All Bookings
                    </a>
                    <a href="{{ route('user.bookings', ['status' => 'pending']) }}"
                       class="@if($status === 'pending') border-teal-500 text-teal-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Pending
                    </a>
                    <a href="{{ route('user.bookings', ['status' => 'confirmed']) }}"
                       class="@if($status === 'confirmed') border-teal-500 text-teal-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Confirmed
                    </a>
                    <a href="{{ route('user.bookings', ['status' => 'completed']) }}"
                       class="@if($status === 'completed') border-teal-500 text-teal-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Completed
                    </a>
                    <a href="{{ route('user.bookings', ['status' => 'cancelled']) }}"
                       class="@if($status === 'cancelled') border-teal-500 text-teal-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Cancelled
                    </a>
                </nav>
            </div>

            <!-- Bookings List -->
            @if($bookings->count() > 0)
            <div class="space-y-4">
                @foreach($bookings as $booking)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        @if($booking->service_type === 'car') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                        @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 @endif">
                                        {{ ucfirst($booking->service_type) }}
                                    </span>
                                    <span class="text-sm font-mono text-gray-500 dark:text-gray-400">{{ $booking->booking_code }}</span>
                                    <span class="px-2 py-1 text-xs font-medium rounded
                                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                        @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                        @elseif($booking->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $booking->product_name }}</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $booking->service_date->format('d M Y') }} at {{ \Carbon\Carbon::parse($booking->service_time)->format('H:i') }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        THB {{ number_format($booking->total_price) }}
                                    </div>
                                    @if($booking->pickup_location)
                                    <div class="flex items-center col-span-2">
                                        <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        From: {{ $booking->pickup_location }}
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="ml-4 flex flex-col space-y-2">
                                <a href="{{ route('user.booking.show', $booking->id) }}" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium text-center">
                                    View Details
                                </a>
                                @if($booking->status !== 'cancelled' && $booking->status !== 'completed' && $booking->service_date->isFuture())
                                <button onclick="confirmCancel({{ $booking->id }})" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium">
                                    Cancel
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No bookings found</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    @if($status !== 'all')
                        No {{ $status }} bookings available.
                    @else
                        You haven't made any bookings yet. Start exploring!
                    @endif
                </p>
                <div class="mt-6">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium">
                        Browse Services
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>

    @push('scripts')
    <script>
        function confirmCancel(bookingId) {
            if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
                // Submit cancel form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/user/booking/${bookingId}/cancel`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    @endpush
</x-layouts.app>
