<x-layouts.app>
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-10">
        <div class="max-w-3xl mx-auto px-4">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-teal-600 text-white px-6 py-5">
                    <p class="text-xs uppercase tracking-wider opacity-80">Wild Mongolia Travel Voucher</p>
                    <h1 class="text-2xl font-bold mt-1">{{ $booking->product_name }}</h1>
                </div>

                <div class="p-6 grid md:grid-cols-2 gap-6">
                    <div>
                        <div class="space-y-3 text-sm text-gray-700 dark:text-gray-200">
                            <div>
                                <p class="text-xs uppercase text-gray-500 dark:text-gray-400">Booking Code</p>
                                <p class="font-semibold text-base">{{ $booking->booking_code }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-gray-500 dark:text-gray-400">Guest Name</p>
                                <p class="font-semibold text-base">{{ $booking->guest_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-gray-500 dark:text-gray-400">Service Date</p>
                                <p class="font-semibold text-base">{{ \Carbon\Carbon::parse($booking->service_date)->format('D, d M Y') }} {{ $booking->service_time }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-gray-500 dark:text-gray-400">Voucher Token</p>
                                <p class="font-mono text-xs break-all">{{ $booking->voucher_token }}</p>
                            </div>
                        </div>

                        <div class="mt-6 p-3 rounded-lg border border-yellow-200 bg-yellow-50 text-xs text-yellow-800 dark:bg-yellow-900/30 dark:border-yellow-700 dark:text-yellow-200">
                            Present this voucher at check-in. The QR can only be checked in once by staff.
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex p-3 bg-white rounded-lg border border-gray-200">
                            <img src="{{ $qrImageUrl }}" alt="Voucher QR Code" class="w-56 h-56">
                        </div>
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 break-all">{{ $checkInUrl }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('booking.voucher.download', $booking->id) }}"
                    class="flex-1 text-center bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-lg transition">
                    Download Voucher PDF
                </a>
                <a href="{{ route('booking.success', $booking->id) }}"
                    class="flex-1 text-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold py-3 rounded-lg transition">
                    Back to Booking Success
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
