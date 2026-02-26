<x-layouts.admin.app>
    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <a href="{{ route('admin.bookings.index') }}"
                class="flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 mb-6">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Bookings
            </a>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Booking #{{ $booking->booking_code }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Placed on
                        {{ $booking->created_at->format('d M Y, H:i') }}</p>
                </div>

                <div
                    class="mt-4 md:mt-0 bg-white dark:bg-gray-800 p-2 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                    <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST"
                        class="flex items-center space-x-2">
                        @csrf
                        @method('PUT')
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Update Status:</label>
                        <select name="status"
                            class="text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-teal-500">
                            <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed
                            </option>
                            <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                        <button type="submit"
                            class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium px-4 py-2 rounded-md transition">
                            Update
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
                            Service Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs text-gray-500 uppercase">Service Type</span>
                                <span
                                    class="text-base font-medium text-gray-900 dark:text-white">{{ $booking->service_type }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-500 uppercase">Product Name</span>
                                <span
                                    class="text-base font-medium text-gray-900 dark:text-white">{{ $booking->product_name }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-500 uppercase">Service Date</span>
                                <span
                                    class="text-base font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($booking->service_date)->format('l, d F Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-500 uppercase">Time Slot / Pickup</span>
                                <span
                                    class="text-base font-medium text-gray-900 dark:text-white">{{ $booking->service_time ?? '09:00 AM' }}</span>
                            </div>
                            @if ($booking->service_type === 'tour')
                                <div>
                                    <span class="block text-xs text-gray-500 uppercase">Meeting Point Confirmed</span>
                                    <span class="text-base font-medium {{ $booking->meeting_point_confirmed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $booking->meeting_point_confirmed ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        @if ($booking->flight_number || $booking->pickup_location)
                            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <h4 class="text-sm font-semibold mb-3">Logistics</h4>
                                <ul class="text-sm space-y-2 text-gray-700 dark:text-gray-300">
                                    @if ($booking->flight_number)
                                        <li><span class="font-medium">Flight No:</span> {{ $booking->flight_number }}
                                        </li>
                                    @endif
                                    @if ($booking->pickup_location)
                                        <li><span class="font-medium">Pickup:</span> {{ $booking->pickup_location }}
                                        </li>
                                    @endif
                                    @if ($booking->special_request)
                                        <li><span class="font-medium">Notes:</span> {{ $booking->special_request }}
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif

                        @if (!empty($booking->selected_add_ons))
                            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <h4 class="text-sm font-semibold mb-3">Selected Add-ons</h4>
                                <ul class="text-sm space-y-2 text-gray-700 dark:text-gray-300">
                                    @foreach ($booking->selected_add_ons as $option)
                                        <li><span class="font-medium">{{ $option['name'] ?? '-' }}</span> - THB {{ number_format((float) ($option['price'] ?? 0), 2) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
                            Payment Summary</h3>

                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600 dark:text-gray-400">Service Total</span>
                            <span class="text-gray-900 dark:text-white">THB
                                {{ number_format($booking->total_price - ($booking->add_ons_total ?? 0), 2) }}</span>
                        </div>
                        @if(($booking->add_ons_total ?? 0) > 0)
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600 dark:text-gray-400">Add-ons</span>
                                <span class="text-gray-900 dark:text-white">THB
                                    {{ number_format($booking->add_ons_total, 2) }}</span>
                            </div>
                        @endif
                        <div
                            class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">Total Paid</span>
                            <span class="text-lg font-bold text-teal-600">THB
                                {{ number_format($booking->total_price) }}</span>
                        </div>

                        <div class="mt-4">
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Payment Status:
                                {{ $booking->payment_status ?? 'Paid' }}</span>
                        </div>

                        @if($booking->stripe_payment_intent_id)
                            <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                Stripe Payment Intent: {{ $booking->stripe_payment_intent_id }}
                            </div>
                        @endif

                        @if($booking->payment_status === 'paid')
                            <form action="{{ route('admin.bookings.refund', $booking->id) }}" method="POST" class="mt-4"
                                onsubmit="return confirm('Process full refund via Stripe for this booking?')">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-md transition">
                                    Refund via Stripe
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Customer Info</h3>
                        <div class="flex items-center space-x-3 mb-4">
                            <div
                                class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                {{ substr($booking->user->name ?? 'G', 0, 1) }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $booking->user->name ?? 'Guest User' }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->user->email ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <div>Phone: {{ $booking->user->phone ?? 'N/A' }}</div>
                            <div class="mt-2">
                                <a href="mailto:{{ $booking->user->email ?? '' }}"
                                    class="text-teal-600 hover:underline">Send Email</a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border-l-4 border-yellow-400">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Operations</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Assign a driver or vendor for this
                            booking.</p>

                        <div class="mb-4 p-3 rounded border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/40 text-xs text-gray-700 dark:text-gray-300">
                            <div class="font-semibold mb-1">Voucher Check-In</div>
                            @if($booking->checked_in_at)
                                <div class="text-green-600 dark:text-green-400">Checked in at {{ $booking->checked_in_at->format('d M Y H:i') }}</div>
                            @elseif(blank($booking->voucher_token))
                                <div class="text-yellow-600 dark:text-yellow-400">Voucher token is not generated yet.</div>
                            @else
                                <div class="text-gray-600 dark:text-gray-300">Ready for QR check-in.</div>
                                <a href="{{ route('admin.bookings.checkin.show', $booking->voucher_token) }}"
                                    class="inline-block mt-2 text-teal-600 hover:text-teal-700 font-medium">
                                    Open Check-In Screen
                                </a>
                            @endif
                        </div>

                        <button
                            class="w-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 font-medium py-2 rounded border border-gray-300 dark:border-gray-600">
                            Assign Driver (Coming Soon)
                        </button>

                        <a href="https://wa.me/?text=Hello%20{{ $booking->user->name ?? '' }},%20regarding%20booking%20{{ $booking->booking_code }}"
                            target="_blank"
                            class="block text-center mt-3 w-full bg-green-500 hover:bg-green-600 text-white font-medium py-2 rounded">
                            Chat on WhatsApp
                        </a>

                        <form action="{{ route('admin.bookings.resend-confirmation', $booking->id) }}" method="POST" class="mt-3"
                            onsubmit="return confirm('Resend booking confirmation email to {{ $booking->guest_email }}?')">
                            @csrf
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md transition"
                                {{ $booking->payment_status !== 'paid' || blank($booking->guest_email) ? 'disabled' : '' }}>
                                Resend Confirmation Email
                            </button>
                        </form>

                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Last sent:
                            {{ $booking->booking_confirmation_emailed_at ? $booking->booking_confirmation_emailed_at->format('d M Y H:i') : 'Not sent yet' }}
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.admin.app>
