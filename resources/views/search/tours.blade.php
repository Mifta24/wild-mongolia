<x-layouts.app>
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-12">
        <div class="container mx-auto px-4">
            <!-- Search Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Tours & Activities</h1>
                <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                    <div>
                        <span class="font-medium">Destination:</span>
                        <span class="capitalize">{{ str_replace('_', ' ', $destination) }}</span>
                    </div>
                    @if ($experienceType)
                        <div>
                            <span class="font-medium">Experience Type:</span>
                            <span>{{ $experienceType }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if ($tours->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
                    <p class="text-gray-600 dark:text-gray-400 text-lg">No tours available for your search criteria.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($tours as $tour)
                        @php
                            $rating = (float) ($tour->average_rating ?? 0);
                            $fullStars = (int) floor($rating);
                            $emptyStars = max(0, 5 - $fullStars);
                        @endphp

                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                            <div class="relative h-48">
                                @if ($tour->image_url)
                                    @if (str_starts_with($tour->image_url, 'http'))
                                        <img src="{{ $tour->image_url }}" class="w-full h-full object-cover" alt="{{ $tour->name }}">
                                    @else
                                        <img src="{{ asset('storage/' . $tour->image_url) }}" class="w-full h-full object-cover" alt="{{ $tour->name }}">
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100 dark:bg-gray-700">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif

                                @if ($tour->is_featured)
                                    <div class="absolute top-4 right-4 bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                        Bestseller
                                    </div>
                                @endif
                            </div>

                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $tour->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    {{ $tour->duration }}
                                    @if ($tour->includes_lunch)
                                        • Lunch Included
                                    @endif
                                    @if ($tour->includes_pickup)
                                        • Pickup Included
                                    @endif
                                </p>

                                <div class="flex items-center text-yellow-400 text-sm mb-4">
                                    @for ($i = 0; $i < $fullStars; $i++)
                                        <span>★</span>
                                    @endfor
                                    @for ($i = 0; $i < $emptyStars; $i++)
                                        <span class="text-gray-300">★</span>
                                    @endfor
                                    <span class="text-gray-500 ml-2">({{ number_format($tour->total_reviews ?? 0) }} reviews)</span>
                                </div>

                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
                                    <div class="flex items-center justify-between">
                                        @if ($tour->discounted_price)
                                            <div>
                                                <span class="text-gray-400 line-through text-sm">{{ $tour->currency }} {{ number_format($tour->base_price) }}</span>
                                                <span class="text-2xl font-bold text-primary dark:text-teal-400 ml-2">{{ $tour->currency }} {{ number_format($tour->discounted_price) }}</span>
                                            </div>
                                        @else
                                            <span class="text-2xl font-bold text-primary dark:text-teal-400">{{ $tour->currency }} {{ number_format($tour->base_price) }}</span>
                                        @endif
                                    </div>
                                </div>

                                <a href="{{ route('booking.create', ['type' => 'tour', 'service_type' => 'tour', 'service_subtype' => 'private_tour', 'destination' => $destination, 'experience_type' => $experienceType, 'product' => $tour->name, 'price' => $tour->final_price]) }}"
                                    class="block w-full bg-secondary hover:bg-yellow-600 text-white text-center font-bold py-3 rounded-lg transition">
                                    Book This Tour
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
