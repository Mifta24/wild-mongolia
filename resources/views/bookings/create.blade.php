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
                addOns: {{ Js::from($product?->add_ons ?? []) }},
                selectedAddOns: {{ Js::from(old('selected_add_ons', [])) }},
                slotsByDate: {{ Js::from($slotsByDate ?? []) }},
                availabilityCalendar: {{ Js::from($availabilityCalendar ?? []) }},
                selectedSlotId: '{{ old('inventory_slot_id', request('inventory_slot_id')) }}',
                slotDate: '{{ old('service_date', request('date')) }}',
                slotTime: '{{ old('service_time', request('time')) }}',
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
                get hasSlotManagement() {
                    return Object.keys(this.slotsByDate).length > 0;
                },
                get slotsForSelectedDate() {
                    return this.slotsByDate[this.slotDate] || [];
                },
                selectSlot(slot) {
                    this.selectedSlotId = String(slot.id);
                    this.slotDate = this.slotDate || '';
                    this.slotTime = slot.time;
                },
                get addOnsTotal() {
                    return this.addOns.reduce((sum, option, index) => {
                        return this.selectedAddOns.includes(index.toString()) ? sum + Number(option.price || 0) : sum;
                    }, 0);
                },
                get total() { return (this.qty * this.price) + this.addOnsTotal; }
            }" x-init="syncSubtype()">
                @csrf

                <input type="hidden" name="product_id" value="{{ $productId ?? '' }}">
                <input type="hidden" name="product_name" value="{{ $productName }}">
                <input type="hidden" name="base_price" value="{{ $basePrice }}">
                <input type="hidden" name="service_subtype" :value="serviceSubtype">
                <input type="hidden" name="inventory_slot_id" :value="selectedSlotId">

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

                                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
                                        <label class="inline-flex items-start gap-3 cursor-pointer">
                                            <input type="checkbox" name="meeting_point_confirmed" value="1"
                                                @checked(old('meeting_point_confirmed'))
                                                class="mt-1 rounded border-gray-300 dark:border-gray-600 text-teal-600 focus:ring-teal-500">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                                I confirm that I have reviewed and understood the meeting point details for this tour.
                                            </span>
                                        </label>
                                        @error('meeting_point_confirmed')
                                            <p class="text-red-600 dark:text-red-400 text-sm mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4" x-show="!hasSlotManagement">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                        <input type="date" name="service_date" x-model="slotDate"
                                            class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time</label>
                                        <input type="time" name="service_time" x-model="slotTime"
                                            class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                            required>
                                    </div>
                                </div>

                                <div x-show="hasSlotManagement" class="space-y-4">
                                    <div class="rounded-lg border border-teal-200 dark:border-teal-700 bg-teal-50 dark:bg-teal-900/20 p-4">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Availability Calendar</h3>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                            <template x-for="(remaining, date) in availabilityCalendar" :key="date">
                                                <button type="button"
                                                    @click="slotDate = date; selectedSlotId = ''; slotTime = ''"
                                                    class="text-left rounded-md border p-2"
                                                    :class="slotDate === date ? 'border-teal-500 bg-white dark:bg-gray-800' : 'border-gray-200 dark:border-gray-700 bg-white/70 dark:bg-gray-800/50'">
                                                    <p class="text-xs text-gray-500" x-text="date"></p>
                                                    <p class="font-semibold" :class="remaining > 0 ? 'text-teal-600' : 'text-red-500'" x-text="remaining + (type === 'car' ? ' cars' : ' seats')"></p>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Time Slot</label>
                                        <div class="space-y-2" x-show="slotsForSelectedDate.length > 0">
                                            <template x-for="slot in slotsForSelectedDate" :key="slot.id">
                                                <button type="button" @click="selectSlot(slot)"
                                                    class="w-full text-left rounded-lg border px-3 py-2"
                                                    :class="selectedSlotId === String(slot.id) ? 'border-teal-500 bg-teal-50 dark:bg-teal-900/20' : 'border-gray-200 dark:border-gray-700'">
                                                    <div class="flex items-center justify-between">
                                                        <span class="font-medium text-gray-900 dark:text-white" x-text="slot.time"></span>
                                                        <span class="text-sm text-teal-600" x-text="slot.remaining_capacity + (type === 'car' ? ' cars available' : ' seats available')"></span>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1" x-text="'Cutoff: ' + slot.cutoff_at"></p>
                                                </button>
                                            </template>
                                        </div>
                                        <p class="text-sm text-gray-500" x-show="slotDate && slotsForSelectedDate.length === 0">No available slots on selected date.</p>
                                        <p class="text-sm text-gray-500" x-show="!slotDate">Select a date from calendar first.</p>
                                        @error('inventory_slot_id')
                                            <p class="text-red-600 dark:text-red-400 text-sm mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <input type="hidden" name="service_date" :value="slotDate">
                                    <input type="hidden" name="service_time" :value="slotTime">
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

                                @if (!empty($product?->add_ons))
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Add-ons / Options</label>
                                        <div class="space-y-2">
                                            @foreach ($product->add_ons as $index => $option)
                                                <label class="flex items-start justify-between gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer">
                                                    <div class="flex items-start gap-3">
                                                        <input type="checkbox" name="selected_add_ons[]" value="{{ $index }}"
                                                            x-model="selectedAddOns"
                                                            class="mt-1 rounded border-gray-300 dark:border-gray-600 text-teal-600 focus:ring-teal-500">
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $option['name'] ?? '-' }}</p>
                                                            @if (!empty($option['description']))
                                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $option['description'] }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <p class="text-sm font-semibold text-teal-600">THB {{ number_format((float) ($option['price'] ?? 0), 2) }}</p>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

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

                            <div class="flex justify-between mb-4 text-sm" x-show="addOnsTotal > 0">
                                <span class="text-gray-600 dark:text-gray-400">Add-ons</span>
                                <span class="text-gray-900 dark:text-white">THB <span x-text="addOnsTotal.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 })"></span></span>
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
