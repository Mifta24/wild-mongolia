<x-layouts.admin.app>
    @php
        $addOnInput = old(
            'add_ons_input',
            collect($product->add_ons ?? [])->map(function ($item) {
                $name = $item['name'] ?? '';
                $price = $item['price'] ?? 0;
                $description = $item['description'] ?? '';

                return trim($name . '|' . $price . '|' . $description, '|');
            })->implode("\n")
        );

        $galleryImageUrls = old('gallery_image_urls', collect($product->gallery_images ?? [])->implode("\n"));
    @endphp

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Product</h1>
        <a href="{{ route('admin.inventory-slots.index', ['product_id' => $product->id]) }}" class="inline-block mt-2 text-sm text-teal-600 hover:text-teal-700">
            Manage inventory slots for this product →
        </a>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" x-data="{ type: '{{ old('type', $product->type) }}' }"
        class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                required>
            @error('name')
                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
            <select name="type" x-model="type"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                required>
                <option value="car" @selected(old('type', $product->type) === 'car')>Car</option>
                <option value="tour" @selected(old('type', $product->type) === 'tour')>Tour</option>
            </select>
            @error('type')
                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div x-show="type === 'car'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Car Model</label>
                <input type="text" name="car_model" value="{{ old('car_model', $product->car_model) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                    :required="type === 'car'">
                @error('car_model')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Passengers</label>
                <input type="number" min="1" name="max_passengers" value="{{ old('max_passengers', $product->max_passengers) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                    :required="type === 'car'">
                @error('max_passengers')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Luggage</label>
                <input type="number" min="0" name="max_luggage" value="{{ old('max_luggage', $product->max_luggage) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                    :required="type === 'car'">
                @error('max_luggage')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div x-show="type === 'tour'" class="space-y-4 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Destination</label>
                    <input type="text" name="destination" value="{{ old('destination', $product->destination) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                        :required="type === 'tour'" placeholder="Bangkok / Phuket / Chiang Mai">
                    @error('destination')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration</label>
                    <input type="text" name="duration" value="{{ old('duration', $product->duration) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                        :required="type === 'tour'" placeholder="Half Day / Full Day / 3D2N">
                    @error('duration')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="inline-flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="includes_lunch" value="1" @checked(old('includes_lunch', $product->includes_lunch))
                        class="rounded dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-teal-600 focus:ring-teal-500">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Includes Lunch</span>
                </label>
                <label class="inline-flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="includes_pickup" value="1" @checked(old('includes_pickup', $product->includes_pickup))
                        class="rounded dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-teal-600 focus:ring-teal-500">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Includes Pickup</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
            <textarea name="description" rows="4"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Detailed Itinerary</label>
            <textarea name="itinerary" rows="5"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                placeholder="One activity per line">{{ old('itinerary', $product->itinerary) }}</textarea>
            @error('itinerary')
                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Add-ons / Options</label>
            <textarea name="add_ons_input" rows="4"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                placeholder="Format: Name|Price|Description">{{ $addOnInput }}</textarea>
            @error('add_ons_input')
                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cancellation Policy</label>
            <textarea name="cancellation_policy" rows="4"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">{{ old('cancellation_policy', $product->cancellation_policy) }}</textarea>
            @error('cancellation_policy')
                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meeting Point Name</label>
                <input type="text" name="meeting_point_name" value="{{ old('meeting_point_name', $product->meeting_point_name) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                    placeholder="Suvarnabhumi Airport Gate 3">
                @error('meeting_point_name')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meeting Point Embed URL (Optional)</label>
                <input type="url" name="meeting_point_embed_url" value="{{ old('meeting_point_embed_url', $product->meeting_point_embed_url) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                    placeholder="https://maps.google.com/...">
                @error('meeting_point_embed_url')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meeting Point Address</label>
            <textarea name="meeting_point_address" rows="2"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">{{ old('meeting_point_address', $product->meeting_point_address) }}</textarea>
            @error('meeting_point_address')
                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meeting Point Latitude</label>
                <input type="number" step="0.0000001" name="meeting_point_lat" value="{{ old('meeting_point_lat', $product->meeting_point_lat) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                    placeholder="13.6900000">
                @error('meeting_point_lat')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meeting Point Longitude</label>
                <input type="number" step="0.0000001" name="meeting_point_lng" value="{{ old('meeting_point_lng', $product->meeting_point_lng) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                    placeholder="100.7501120">
                @error('meeting_point_lng')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Base Price</label>
                <input type="number" step="0.01" name="base_price"
                    value="{{ old('base_price', $product->base_price) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                    required>
                @error('base_price')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Discounted Price</label>
                <input type="number" step="0.01" name="discounted_price"
                    value="{{ old('discounted_price', $product->discounted_price) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
                @error('discounted_price')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Currency</label>
                <input type="text" name="currency" value="{{ old('currency', $product->currency ?? 'THB') }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                    maxlength="3">
                @error('currency')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Image</label>
            @if ($product->image_url)
                <div class="mb-3">
                    @if (str_starts_with($product->image_url, 'http'))
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="max-h-48 rounded-lg object-cover">
                    @else
                        <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}" class="max-h-48 rounded-lg object-cover">
                    @endif
                </div>
            @endif
            <input type="file" name="image" accept="image/*"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500 p-2">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Supported formats: JPEG, PNG, JPG, GIF, WebP. Max
                size: 5MB</p>
            @error('image')
                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Or Image URL</label>
            <input type="text" name="image_url" value="{{ old('image_url', $product->image_url) }}"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave empty if uploading image file</p>
            @error('image_url')
                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo Gallery (Multiple Upload)</label>
            <input type="file" name="gallery_images_upload[]" accept="image/*" multiple
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500 p-2">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">New files will be appended to existing gallery.</p>
            @error('gallery_images_upload')
                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
            @error('gallery_images_upload.*')
                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo Gallery URLs / Paths</label>
            <textarea name="gallery_image_urls" rows="4"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                placeholder="One URL/path per line">{{ $galleryImageUrls }}</textarea>
            @error('gallery_image_urls')
                <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        @if (!empty($product->gallery_images))
            <div>
                <p class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Gallery</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach ($product->gallery_images as $galleryImage)
                        @if (str_starts_with($galleryImage, 'http'))
                            <img src="{{ $galleryImage }}" alt="Gallery" class="h-28 w-full object-cover rounded-lg">
                        @else
                            <img src="{{ asset('storage/' . $galleryImage) }}" alt="Gallery" class="h-28 w-full object-cover rounded-lg">
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="inline-flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))
                        class="rounded dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-teal-600 focus:ring-teal-500">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                </label>
                <label class="inline-flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))
                        class="rounded dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-teal-600 focus:ring-teal-500">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Featured</span>
                </label>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button type="submit"
                class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition">Update</button>
            <a href="{{ route('admin.products.index') }}"
                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Back</a>
        </div>
    </form>
</x-layouts.admin.app>
