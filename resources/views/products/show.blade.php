<x-layouts.app>
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-10">
        <div class="container mx-auto px-4 max-w-6xl">
            @php
                $images = $product->all_image_urls;
                $addOns = $product->add_ons ?? [];
                $itineraryItems = collect(preg_split('/\r\n|\r|\n/', (string) $product->itinerary))
                    ->map(fn($item) => trim((string) $item))
                    ->filter();
                $mapUrl = $product->meeting_point_map_embed_url;
                $bookingParams = [
                    'type' => $product->type,
                    'service_type' => $product->type,
                    'service_subtype' => $product->type === 'car' ? 'airport_transfer' : 'private_tour',
                    'product_id' => $product->id,
                    'product' => $product->name,
                    'price' => $product->final_price,
                ];
            @endphp

            <div class="mb-6">
                <a href="{{ url()->previous() }}" class="text-sm text-primary dark:text-teal-400 hover:underline">&larr; Back</a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 p-2">
                            @forelse ($images as $image)
                                <div class="h-52 md:h-60 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                                    @if (str_starts_with($image, 'http'))
                                        <img src="{{ $image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ asset('storage/' . ltrim($image, '/')) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                            @empty
                                <div class="h-60 bg-gray-100 dark:bg-gray-700 rounded-lg col-span-full flex items-center justify-center text-gray-400">
                                    No photo available
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $product->name }}</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            {{ strtoupper($product->type) }}
                            @if ($product->type === 'car' && $product->car_model)
                                • {{ $product->car_model }}
                            @endif
                            @if ($product->type === 'tour' && $product->destination)
                                • {{ $product->destination }}
                            @endif
                        </p>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $product->description }}</p>
                    </div>

                    @if ($itineraryItems->isNotEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Detailed Itinerary</h2>
                            <ol class="space-y-2 list-decimal list-inside text-gray-700 dark:text-gray-300">
                                @foreach ($itineraryItems as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    @if (!empty($addOns))
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Add-ons & Options</h2>
                            <div class="space-y-3">
                                @foreach ($addOns as $option)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                        <div class="flex justify-between gap-4">
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $option['name'] ?? '-' }}</p>
                                            <p class="font-semibold text-primary dark:text-teal-400">
                                                {{ $product->currency }} {{ number_format((float) ($option['price'] ?? 0), 2) }}
                                            </p>
                                        </div>
                                        @if (!empty($option['description']))
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $option['description'] }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($product->cancellation_policy)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Cancellation Policy</h2>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $product->cancellation_policy }}</p>
                        </div>
                    @endif

                    @if ($product->meeting_point_name || $product->meeting_point_address || $mapUrl)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Meeting Point</h2>
                            @if ($product->meeting_point_name)
                                <p class="font-medium text-gray-900 dark:text-white">{{ $product->meeting_point_name }}</p>
                            @endif
                            @if ($product->meeting_point_address)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $product->meeting_point_address }}</p>
                            @endif

                            @if ($mapUrl)
                                <div class="mt-4 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                                    <iframe src="{{ $mapUrl }}" class="w-full h-72" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 sticky top-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Price</p>
                        <p class="text-3xl font-bold text-primary dark:text-teal-400 mt-1">
                            {{ $product->currency }} {{ number_format($product->final_price, 2) }}
                        </p>

                        @if ($product->discounted_price)
                            <p class="text-sm text-gray-400 line-through mt-1">
                                {{ $product->currency }} {{ number_format($product->base_price, 2) }}
                            </p>
                        @endif

                        <div class="mt-6 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            @if ($product->type === 'car')
                                <p>Passengers: {{ $product->max_passengers ?? '-' }}</p>
                                <p>Luggage: {{ $product->max_luggage ?? '-' }}</p>
                            @else
                                <p>Duration: {{ $product->duration ?? '-' }}</p>
                                <p>Destination: {{ $product->destination ?? '-' }}</p>
                            @endif
                        </div>

                        <a href="{{ route('booking.create', $bookingParams) }}"
                            class="mt-6 block w-full bg-primary hover:bg-teal-700 text-white text-center font-bold py-3 rounded-lg transition">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
