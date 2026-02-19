<x-layouts.app>
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Secure Booking</h1>
                <div class="flex space-x-2 text-sm">
                    <span class="text-teal-600 font-bold">1. Details</span>
                    <span class="text-gray-400">&rarr;</span>
                    <span class="text-gray-400">2. Payment</span>
                    <span class="text-gray-400">&rarr;</span>
                    <span class="text-gray-400">3. Done</span>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                    <p class="font-semibold mb-2">Please fix the following:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('booking.store') }}" method="POST" x-data="{
                qty: 1,
                price: {{ $basePrice }},
                type: '{{ $serviceType }}',
                serviceSubtype: '{{ old('service_subtype', $serviceSubtype ?? 'airport_transfer') }}',
                syncSubtype() {
                    if (this.type === 'tour') {
                        this.serviceSubtype = 'private_tour';
                        return;
                    }

                    if (!['airport_transfer', 'city_rental_hourly'].includes(this.serviceSubtype)) {
                        this.serviceSubtype = 'airport_transfer';
                    }
                },
                get total() { return this.qty * this.price; }
            }" x-init="syncSubtype()">
                @csrf

                <input type="hidden" name="product_id" value="{{ $productId ?? '' }}">
                <input type="hidden" name="product_name" value="{{ $productName }}">
                <input type="hidden" name="base_price" value="{{ $basePrice }}">
                <input type="hidden" name="service_subtype" :value="serviceSubtype">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div class="md:col-span-2 space-y-6">

                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information
                            </h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full
                                        Name</label>
                                    <input type="text" name="guest_name" value="{{ Auth::user()->name ?? '' }}"
                                        class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                        required>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                        <input type="email" name="guest_email" value="{{ Auth::user()->email ?? '' }}"
                                            class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                            required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone
                                            (WhatsApp)</label>
                                        <input type="text" name="guest_phone"
                                            class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                            required placeholder="+66...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Service Details</h2>
                            <div class="space-y-4">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                    <select x-model="type" @change="syncSubtype()" name="service_type" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700" required>
                                        <option value="car">Private Car / Transfer</option>
                                        <option value="tour">Thailand Tours & Activities</option>
                                    </select>
                                </div>

                                <div x-show="type === 'car'" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service Type</label>
                                        <select x-model="serviceSubtype" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                            <option value="airport_transfer">Airport Transfer</option>
                                            <option value="city_rental_hourly">City Rental (Hourly)</option>
                                        </select>
                                    </div>
                                </div>

                                <div x-show="type === 'tour'" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Destination</label>
                                        <select name="destination" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700" :required="type === 'tour'">
                                            <option value="Bangkok" @selected(($destination ?? old('destination')) === 'Bangkok')>Bangkok</option>
                                            <option value="Phuket & Krabi" @selected(($destination ?? old('destination')) === 'Phuket & Krabi')>Phuket & Krabi</option>
                                            <option value="Chiang Mai" @selected(($destination ?? old('destination')) === 'Chiang Mai')>Chiang Mai</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Experience Type</label>
                                        <select name="experience_type" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700" :required="type === 'tour'">
                                            <option value="temples" @selected(($experienceType ?? old('experience_type')) === 'temples')>Temples</option>
                                            <option value="food" @selected(($experienceType ?? old('experience_type')) === 'food')>Food</option>
                                            <option value="elephant_sanctuary" @selected(($experienceType ?? old('experience_type')) === 'elephant_sanctuary')>Elephant Sanctuary</option>
                                            <option value="island_hopping" @selected(($experienceType ?? old('experience_type')) === 'island_hopping')>Island Hopping</option>
                                            <option value="night_market" @selected(($experienceType ?? old('experience_type')) === 'night_market')>Night Market</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                        <input type="date" name="service_date"
                                            class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time</label>
                                        <input type="time" name="service_time"
                                            class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                            required>
                                    </div>
                                </div>

                                <div x-show="type === 'car'" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Flight
                                            Number (Airport Transfer)</label>
                                        <input type="text" name="flight_number" x-show="serviceSubtype === 'airport_transfer'" :required="type === 'car' && serviceSubtype === 'airport_transfer'"
                                            class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                            placeholder="e.g. TG 678 (required for airport transfer)">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pickup
                                            / Drop-off Details</label>
                                        <textarea name="pickup_location" rows="2"
                                            class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                            placeholder="Hotel name or Address"></textarea>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                        x-text="type === 'car' ? 'Number of Cars' : 'Number of People'"></label>
                                    <input type="number" name="quantity" x-model="qty" min="1"
                                        class="mt-1 w-24 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-center"
                                        required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Special
                                        Request (Optional)</label>
                                    <textarea name="special_request" rows="2"
                                        class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="md:col-span-1">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm sticky top-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Booking Summary</h3>

                            <div class="mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Product</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $productName }}</p>
                            </div>

                            <div class="flex justify-between mb-2 text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Price per unit</span>
                                <span class="text-gray-900 dark:text-white">THB <span x-text="price"></span></span>
                            </div>
                            <div class="flex justify-between mb-4 text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Quantity</span>
                                <span class="text-gray-900 dark:text-white">x <span x-text="qty"></span></span>
                            </div>

                            <div
                                class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center mb-6">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
                                <span class="text-xl font-bold text-teal-600">THB <span
                                        x-text="total.toLocaleString()"></span></span>
                            </div>

                            <button type="submit"
                                class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded-lg shadow transition transform hover:-translate-y-0.5">
                                Proceed to Review
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
