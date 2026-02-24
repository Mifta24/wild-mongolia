<x-layouts.app>
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-8">
                <div class="mx-auto w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Review & Pay</h1>
                <p class="text-gray-500">Please review your booking details before paying.</p>
            </div>

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if(request()->boolean('cancelled'))
                <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded" role="alert">
                    Payment was cancelled. You can retry checkout anytime.
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden mb-6">
                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex justify-between">
                    <span class="font-medium text-gray-700 dark:text-gray-200">Booking Code</span>
                    <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $booking->booking_code }}</span>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Guest Name</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $booking->guest_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Service</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $booking->product_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Date & Time</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ $booking->service_date->format('d M Y') }} at
                            {{ \Carbon\Carbon::parse($booking->service_time)->format('H:i') }}
                        </span>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 my-4"></div>

                    @if (($booking->add_ons_total ?? 0) > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Service Total</span>
                            <span class="font-medium text-gray-900 dark:text-white">THB {{ number_format($booking->total_price - $booking->add_ons_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Add-ons</span>
                            <span class="font-medium text-gray-900 dark:text-white">THB {{ number_format($booking->add_ons_total, 2) }}</span>
                        </div>
                    @endif

                    @if(!empty($booking->selected_add_ons))
                        <div class="pt-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Selected Add-ons</p>
                            <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
                                @foreach($booking->selected_add_ons as $option)
                                    <li>• {{ $option['name'] ?? '-' }} (THB {{ number_format((float) ($option['price'] ?? 0), 2) }})</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($booking->service_type === 'tour')
                        <div class="pt-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Meeting Point Confirmed</p>
                            <p class="text-sm font-medium {{ $booking->meeting_point_confirmed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $booking->meeting_point_confirmed ? 'Yes' : 'No' }}
                            </p>
                        </div>
                    @endif

                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Total Amount</span>
                        <span class="text-2xl font-bold text-teal-600">THB
                            {{ number_format($booking->total_price) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 mb-8">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Select Payment Method</h3>

                <form action="{{ route('booking.process', $booking->id) }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <label
                            class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                            <input type="radio" name="payment_method" value="credit_card" checked
                                class="h-4 w-4 text-teal-600 focus:ring-teal-500">
                            <span class="ml-3 font-medium text-gray-900 dark:text-white">Credit / Debit Card
                                (Stripe)</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="mt-6 w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition">
                        Pay Securely &rarr;
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="{{ route('booking.create') }}" class="text-sm text-gray-500 hover:underline">Cancel &
                        Return</a>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
