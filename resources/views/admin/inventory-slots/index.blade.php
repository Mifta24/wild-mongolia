<x-layouts.admin.app>
    @php
        $selectedProduct = $products->firstWhere('id', (int) $selectedProductId);
        $calendarUnitLabel = $selectedProduct?->type === 'car'
            ? 'cars'
            : ($selectedProduct?->type === 'tour' ? 'seats' : 'units');
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inventory & Slot Management</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage slots, capacity, availability, and cutoff settings.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <form method="GET" action="{{ route('admin.inventory-slots.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product</label>
                    <select name="product_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">All Products</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected((string) $selectedProductId === (string) $product->id)>
                                {{ $product->name }} ({{ strtoupper($product->type) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                    <input type="month" name="month" value="{{ $selectedMonth }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">Apply</button>
                    <a href="{{ route('admin.inventory-slots.index') }}"
                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Availability Calendar</h2>
            @if ($calendarSummary->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">No slot availability found for selected filters.</p>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    @foreach ($calendarSummary as $date => $remaining)
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
                            <p class="mt-1 text-lg font-bold {{ $remaining > 0 ? 'text-teal-600' : 'text-red-500' }}">{{ (int) $remaining }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $calendarUnitLabel }} remaining</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Create New Slot</h2>
            <form method="POST" action="{{ route('admin.inventory-slots.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product</label>
                    <select name="product_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id', $selectedProductId) == $product->id)>
                                {{ $product->name }} ({{ strtoupper($product->type) }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                    <input type="date" name="slot_date" value="{{ old('slot_date') }}" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('slot_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Time</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('start_time')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Time (Optional)</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('end_time')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Capacity (cars or seats)</label>
                    <input type="number" min="1" name="capacity" value="{{ old('capacity', 8) }}" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('capacity')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cutoff (minutes before)</label>
                    <input type="number" min="0" name="cutoff_minutes" value="{{ old('cutoff_minutes', 120) }}" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('cutoff_minutes')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ old('notes') }}</textarea>
                </div>
                <div class="md:col-span-3 flex items-center gap-4">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-gray-300 dark:border-gray-600 text-teal-600">
                        Active slot
                    </label>
                    <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">Create Slot</button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Slot List</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/30">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date/Time</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Capacity</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Cutoff</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($slots as $slot)
                            <tr>
                                <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $slot->product->name }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $slot->slot_date->format('d M Y') }}<br>
                                    <span class="text-xs text-gray-500">{{ substr($slot->start_time, 0, 5) }}{{ $slot->end_time ? ' - ' . substr($slot->end_time, 0, 5) : '' }}</span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-semibold">{{ $slot->remaining_capacity }}</span> / {{ $slot->capacity }}
                                    <div class="text-xs text-gray-500">
                                        {{ $slot->product->type === 'car' ? 'Cars' : 'Seats' }} remaining, booked: {{ $slot->booked_quantity }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $slot->cutoff_minutes }} min</td>
                                <td class="px-4 py-4 text-sm">
                                    @if ($slot->is_active)
                                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">Active</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <details>
                                        <summary class="cursor-pointer text-teal-600">Edit</summary>
                                        <form method="POST" action="{{ route('admin.inventory-slots.update', $slot) }}" class="mt-3 space-y-2">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-2 gap-2">
                                                <input type="date" name="slot_date" value="{{ $slot->slot_date->toDateString() }}" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                                <input type="time" name="start_time" value="{{ substr($slot->start_time, 0, 5) }}" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                                <input type="time" name="end_time" value="{{ $slot->end_time ? substr($slot->end_time, 0, 5) : '' }}" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                                <input type="number" min="1" name="capacity" value="{{ $slot->capacity }}" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                                <input type="number" min="0" name="booked_quantity" value="{{ $slot->booked_quantity }}" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                                <input type="number" min="0" name="cutoff_minutes" value="{{ $slot->cutoff_minutes }}" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                            </div>
                                            <textarea name="notes" rows="2" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Notes">{{ $slot->notes }}</textarea>
                                            <label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                                                <input type="checkbox" name="is_active" value="1" @checked($slot->is_active) class="rounded border-gray-300 dark:border-gray-600 text-teal-600">
                                                Active
                                            </label>
                                            <div class="flex items-center gap-2">
                                                <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded">Save</button>
                                            </div>
                                        </form>
                                    </details>

                                    <form method="POST" action="{{ route('admin.inventory-slots.destroy', $slot) }}" class="mt-2"
                                        onsubmit="return confirm('Delete this slot?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No slots found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $slots->links() }}
            </div>
        </div>
    </div>
</x-layouts.admin.app>
