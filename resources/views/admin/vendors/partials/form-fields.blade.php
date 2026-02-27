<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vendor Name</label>
        <input type="text" name="name" value="{{ old('name', $vendor?->name) }}"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500"
            required>
        @error('name')
            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vendor Code (optional)</label>
        <input type="text" name="vendor_code" value="{{ old('vendor_code', $vendor?->vendor_code) }}"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
        @error('vendor_code')
            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Person</label>
        <input type="text" name="contact_person" value="{{ old('contact_person', $vendor?->contact_person) }}"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $vendor?->phone) }}"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
        <input type="email" name="email" value="{{ old('email', $vendor?->email) }}"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
        @error('email')
            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service Type</label>
        <select name="service_type"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
            <option value="">- Select -</option>
            <option value="car" {{ old('service_type', $vendor?->service_type) === 'car' ? 'selected' : '' }}>Car</option>
            <option value="tour" {{ old('service_type', $vendor?->service_type) === 'tour' ? 'selected' : '' }}>Tour</option>
            <option value="both" {{ old('service_type', $vendor?->service_type) === 'both' ? 'selected' : '' }}>Both</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">LINE ID</label>
        <input type="text" name="line_id" value="{{ old('line_id', $vendor?->line_id) }}"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">WhatsApp Number</label>
        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $vendor?->whatsapp_number) }}"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default Commission (%)</label>
        <input type="number" step="0.01" min="0" max="100" name="default_commission_rate"
            value="{{ old('default_commission_rate', $vendor?->default_commission_rate) }}"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
        @error('default_commission_rate')
            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-end">
        <label class="inline-flex items-center space-x-3 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $vendor?->is_active ?? true))
                class="rounded dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-teal-600 focus:ring-teal-500">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Vendor</span>
        </label>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
        <textarea name="address" rows="2"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">{{ old('address', $vendor?->address) }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
        <textarea name="notes" rows="3"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">{{ old('notes', $vendor?->notes) }}</textarea>
    </div>
</div>
