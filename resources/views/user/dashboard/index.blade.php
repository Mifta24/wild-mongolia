<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Welcome back, {{ $user->name }}!</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-teal-100 dark:bg-teal-900 rounded-md p-3">
                            <svg class="h-6 w-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Bookings</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_bookings'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-md p-3">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Upcoming</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['upcoming_bookings'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-md p-3">
                            <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Points Balance</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['points']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-md p-3">
                            <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Coupons</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['available_coupons'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="{{ route('user.bookings') }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition p-6 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-600">My Bookings</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View and manage your bookings</p>
                        </div>
                        <svg class="h-8 w-8 text-gray-400 group-hover:text-teal-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('user.points') }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition p-6 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-600">My Points</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View points history and balance</p>
                        </div>
                        <svg class="h-8 w-8 text-gray-400 group-hover:text-teal-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('user.coupons') }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition p-6 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-600">My Coupons</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage your discount coupons</p>
                        </div>
                        <svg class="h-8 w-8 text-gray-400 group-hover:text-teal-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Upcoming Bookings -->
            @if($upcomingBookings->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Upcoming Bookings</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($upcomingBookings as $booking)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        @if($booking->service_type === 'car') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                        @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 @endif">
                                        {{ ucfirst($booking->service_type) }}
                                    </span>
                                    <span class="text-sm font-mono text-gray-500 dark:text-gray-400">{{ $booking->booking_code }}</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ $booking->product_name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $booking->service_date->format('d M Y') }} at {{ \Carbon\Carbon::parse($booking->service_time)->format('H:i') }}
                                </p>
                            </div>
                            <a href="{{ route('user.booking.show', $booking->id) }}" class="ml-4 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium">
                                View Details
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700">
                    <a href="{{ route('user.bookings') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                        View all bookings →
                    </a>
                </div>
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No upcoming bookings</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Start exploring our services and book your next adventure!</p>
                <div class="mt-6">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium">
                        Browse Services
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-layouts.app>
