<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Booking Details</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $booking->booking_code }}</p>
                </div>
                <a href="{{ route('user.bookings') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                    ← Back to Bookings
                </a>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Booking Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Status</h2>
                            <span class="px-3 py-1 text-sm font-medium rounded
                                @if($booking->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                @elseif($booking->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <p>Booked on: {{ $booking->created_at->format('d M Y, H:i') }}</p>
                            <p>Payment: <span class="font-medium">{{ ucfirst($booking->payment_status) }}</span></p>
                        </div>
                    </div>

                    <!-- Service Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Service Details</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-600 dark:text-gray-400">Service Type</label>
                                <p class="font-medium text-gray-900 dark:text-white">{{ ucfirst($booking->service_type) }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 dark:text-gray-400">Service Name</label>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $booking->product_name }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-600 dark:text-gray-400">Date</label>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $booking->service_date->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 dark:text-gray-400">Time</label>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($booking->service_time)->format('H:i') }}</p>
                                </div>
                            </div>

                            @if($booking->pickup_location)
                            <div>
                                <label class="text-sm text-gray-600 dark:text-gray-400">Pickup Location</label>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $booking->pickup_location }}</p>
                            </div>
                            @endif

                            @if($booking->dropoff_location)
                            <div>
                                <label class="text-sm text-gray-600 dark:text-gray-400">Drop-off Location</label>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $booking->dropoff_location }}</p>
                            </div>
                            @endif

                            @if($booking->flight_number)
                            <div>
                                <label class="text-sm text-gray-600 dark:text-gray-400">Flight Number</label>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $booking->flight_number }}</p>
                            </div>
                            @endif

                            @if($booking->special_request)
                            <div>
                                <label class="text-sm text-gray-600 dark:text-gray-400">Special Request</label>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $booking->special_request }}</p>
                            </div>
                            @endif

                            @if(!empty($booking->selected_add_ons))
                            <div>
                                <label class="text-sm text-gray-600 dark:text-gray-400">Selected Add-ons</label>
                                <ul class="mt-1 space-y-1 text-sm text-gray-900 dark:text-white">
                                    @foreach($booking->selected_add_ons as $option)
                                        <li>• {{ $option['name'] ?? '-' }} (MNT {{ number_format((float) ($option['price'] ?? 0), 2) }})</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            @if($booking->service_type === 'tour')
                            <div>
                                <label class="text-sm text-gray-600 dark:text-gray-400">Meeting Point Confirmed</label>
                                <p class="font-medium {{ $booking->meeting_point_confirmed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $booking->meeting_point_confirmed ? 'Yes' : 'No' }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h2>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm text-gray-600 dark:text-gray-400">Name</label>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $booking->guest_name }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 dark:text-gray-400">Email</label>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $booking->guest_email }}</p>
                            </div>
                            @if($booking->guest_phone)
                            <div>
                                <label class="text-sm text-gray-600 dark:text-gray-400">Phone</label>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $booking->guest_phone }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Review & Rating -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Review & Rating</h2>

                        @if(!$booking->product_id || !$booking->product)
                            <p class="text-sm text-gray-600 dark:text-gray-400">Review is unavailable because this booking is not linked to a product.</p>
                        @elseif($booking->status !== 'completed')
                            <p class="text-sm text-gray-600 dark:text-gray-400">You can submit a review after this booking is marked as completed.</p>
                        @elseif($booking->review)
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-yellow-400">
                                        @for($i = 0; $i < $booking->review->rating; $i++)
                                            <span>★</span>
                                        @endfor
                                        @for($i = 0; $i < 5 - $booking->review->rating; $i++)
                                            <span class="text-gray-300 dark:text-gray-600">★</span>
                                        @endfor
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($booking->review->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                        @elseif($booking->review->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @endif">
                                        {{ ucfirst($booking->review->status) }}
                                    </span>
                                </div>

                                @if($booking->review->comment)
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $booking->review->comment }}</p>
                                @endif

                                @if($booking->review->status !== 'approved')
                                    <form action="{{ route('user.booking.review.store', $booking->id) }}" method="POST" class="space-y-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                        @csrf
                                        <div>
                                            <label for="rating" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Update Rating</label>
                                            <select id="rating" name="rating" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                                                @for($rate = 5; $rate >= 1; $rate--)
                                                    <option value="{{ $rate }}" @selected(old('rating', $booking->review->rating) == $rate)>{{ $rate }} Star{{ $rate > 1 ? 's' : '' }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div>
                                            <label for="comment" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Comment</label>
                                            <textarea id="comment" name="comment" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ old('comment', $booking->review->comment) }}</textarea>
                                        </div>
                                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium">Resubmit for Moderation</button>
                                    </form>
                                @endif
                            </div>
                        @else
                            <form action="{{ route('user.booking.review.store', $booking->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="rating" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Rating</label>
                                    <select id="rating" name="rating" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                                        <option value="">Select rating</option>
                                        @for($rate = 5; $rate >= 1; $rate--)
                                            <option value="{{ $rate }}" @selected(old('rating') == $rate)>{{ $rate }} Star{{ $rate > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label for="comment" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Comment</label>
                                    <textarea id="comment" name="comment" rows="4" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Tell us about your experience...">{{ old('comment') }}</textarea>
                                </div>
                                <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium">Submit Review</button>
                            </form>
                        @endif
                    </div>

                </div>

                <!-- Sidebar -->
                <div class="space-y-6">

                    <!-- Payment Summary -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Quantity</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $booking->quantity }}</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                @if(($booking->add_ons_total ?? 0) > 0)
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-600 dark:text-gray-400">Add-ons</span>
                                    <span class="font-medium text-gray-900 dark:text-white">MNT {{ number_format($booking->add_ons_total, 2) }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                                    <span class="font-bold text-teal-600 text-lg">MNT {{ number_format($booking->total_price) }}</span>
                                </div>
                            </div>
                            @if(in_array($booking->payment_status, ['paid', 'refunded'], true))
                            <a href="{{ route('booking.invoice', $booking->id) }}"
                               class="block w-full text-center mt-3 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium">
                                Download Invoice (PDF)
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    @if($booking->status !== 'cancelled' && $booking->status !== 'completed')
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-400 mb-2">Need to cancel?</h4>
                        <p class="text-xs text-yellow-700 dark:text-yellow-500 mb-3">Free cancellation up to 24 hours before service</p>
                        @if($booking->service_date->isFuture() && now()->diffInHours($booking->service_date, false) > 24)
                        <form action="{{ route('user.booking.cancel', $booking->id) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">
                                Cancel Booking
                            </button>
                        </form>
                        @else
                        <p class="text-xs text-red-600 dark:text-red-400 font-medium">Cannot cancel within 24 hours of service</p>
                        @endif
                    </div>
                    @endif

                    <!-- Support -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Need Help?</h4>
                        <a href="{{ route('support.chat') }}" class="block w-full px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium text-center">
                            Contact Support
                        </a>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-layouts.app>
