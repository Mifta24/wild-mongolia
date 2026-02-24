<x-layouts.app>
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center py-12">
        <div class="max-w-md w-full bg-white dark:bg-gray-800 shadow-2xl rounded-2xl p-8 text-center">

            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            @if($booking->payment_status === 'paid')
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Booking Confirmed!</h1>
                <p class="text-gray-500 mb-8">Thank you for your order. We have sent the confirmation email to <span
                        class="font-semibold">{{ $booking->guest_email }}</span>.</p>
            @else
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Payment Processing</h1>
                <p class="text-gray-500 mb-8">Your booking is created. Payment is still being verified, please refresh this page in a moment.</p>
            @endif

            <div
                class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-500 mb-8">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Booking Reference</p>
                <p class="text-2xl font-mono font-bold text-gray-900 dark:text-white">{{ $booking->booking_code }}</p>
            </div>

            <div class="text-left bg-gray-50 dark:bg-gray-700/40 border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-6">
                <p class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Booking Summary</p>
                <p class="text-sm text-gray-700 dark:text-gray-300">Service: {{ $booking->product_name }}</p>
                @if(($booking->add_ons_total ?? 0) > 0)
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">Add-ons Total: THB {{ number_format($booking->add_ons_total, 2) }}</p>
                    @if(!empty($booking->selected_add_ons))
                        <ul class="mt-2 space-y-1 text-xs text-gray-600 dark:text-gray-400 list-disc list-inside">
                            @foreach($booking->selected_add_ons as $option)
                                <li>{{ $option['name'] ?? '-' }} (THB {{ number_format((float) ($option['price'] ?? 0), 2) }})</li>
                            @endforeach
                        </ul>
                    @endif
                @endif

                @if($booking->service_type === 'tour')
                    <p class="text-sm mt-2 {{ $booking->meeting_point_confirmed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        Meeting Point Confirmed: {{ $booking->meeting_point_confirmed ? 'Yes' : 'No' }}
                    </p>
                @endif
            </div>

            <div class="space-y-3">
                @if(in_array($booking->payment_status, ['paid', 'refunded'], true))
                    <a href="{{ route('booking.invoice', $booking->id) }}"
                        class="block w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 rounded-lg transition">
                        Download Invoice (PDF)
                    </a>
                @endif
                <a href="/"
                    class="block w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-bold py-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Back to Home
                </a>
            </div>

        </div>
    </div>
</x-layouts.app>
