<x-layouts.admin.app>
    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 p-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Voucher Check-In</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Scan result for booking voucher token.</p>

                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Booking Code:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $booking->booking_code }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Guest:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $booking->guest_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Service:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $booking->product_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Date:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($booking->service_date)->format('D, d M Y') }} {{ $booking->service_time }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Payment Status:</span>
                        <span class="font-semibold {{ $booking->payment_status === 'paid' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ strtoupper($booking->payment_status) }}</span>
                    </div>
                </div>

                @if($booking->checked_in_at)
                    <div class="mt-6 p-4 rounded-lg border border-green-200 bg-green-50 text-green-800 dark:bg-green-900/20 dark:border-green-700 dark:text-green-200">
                        Already checked in at {{ $booking->checked_in_at->format('d M Y H:i') }}
                        @if($booking->checkedInBy)
                            by {{ $booking->checkedInBy->name }}.
                        @endif
                    </div>
                @elseif(!$isEligible)
                    <div class="mt-6 p-4 rounded-lg border border-red-200 bg-red-50 text-red-800 dark:bg-red-900/20 dark:border-red-700 dark:text-red-200">
                        Voucher is not eligible for check-in. Ensure booking is paid, not cancelled, and check-in is within H-1 to H+1 of service date.
                    </div>
                @else
                    <form action="{{ route('admin.bookings.checkin.process', $booking->voucher_token) }}" method="POST" class="mt-6"
                        onsubmit="return confirm('Confirm check-in for this voucher?')">
                        @csrf
                        <button type="submit"
                            class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-lg transition">
                            Confirm Check-In
                        </button>
                    </form>
                @endif

                <a href="{{ route('admin.bookings.show', $booking->id) }}"
                    class="mt-4 inline-flex text-sm text-teal-600 hover:text-teal-700">
                    Open booking detail
                </a>
            </div>
        </div>
    </div>
</x-layouts.admin.app>
