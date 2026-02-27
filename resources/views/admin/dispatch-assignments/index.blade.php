<x-layouts.admin.app>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Dispatch Assignments</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Booking, customer, driver, vendor"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Dispatch Status</label>
                <select name="dispatch_status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All</option>
                    @foreach (['pending', 'assigned', 'on_route', 'completed', 'cancelled'] as $status)
                        <option value="{{ $status }}" {{ request('dispatch_status') === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Settlement</label>
                <select name="settlement_status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All</option>
                    @foreach (['unpaid', 'partially_paid', 'paid'] as $status)
                        <option value="{{ $status }}" {{ request('settlement_status') === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Vendor</label>
                <select name="vendor_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All</option>
                    @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ (string) request('vendor_id') === (string) $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-5 flex justify-end gap-2">
                <button type="submit" class="px-4 py-2 text-sm bg-teal-600 text-white rounded-lg hover:bg-teal-700">Filter</button>
                <a href="{{ route('admin.dispatch-assignments.index') }}" class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Booking</th>
                        <th class="px-6 py-3">Vendor / Driver</th>
                        <th class="px-6 py-3">Dispatch</th>
                        <th class="px-6 py-3">Commission</th>
                        <th class="px-6 py-3">Settlement</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignments as $assignment)
                        <tr class="border-b border-gray-200 dark:border-gray-700 align-top">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">#{{ $assignment->booking->booking_code }}</div>
                                <div class="text-xs text-gray-500">{{ $assignment->booking->guest_name }}</div>
                                <div class="text-xs text-gray-500">THB {{ number_format((float) $assignment->booking->total_price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $assignment->vendor->name }}</div>
                                <div class="text-xs text-gray-500">Driver: {{ $assignment->driver_name }}</div>
                                <div class="text-xs text-gray-500">{{ $assignment->driver_phone ?: '-' }} @if($assignment->vehicle_plate) | {{ $assignment->vehicle_plate }} @endif</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-800">{{ ucfirst(str_replace('_', ' ', $assignment->dispatch_status)) }}</span>
                                <div class="text-xs text-gray-500 mt-1">{{ optional($assignment->assigned_at)->format('d M Y H:i') ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs">Type: {{ ucfirst($assignment->commission_type) }}</div>
                                <div class="text-xs">Commission: THB {{ number_format((float) $assignment->commission_amount, 2) }}</div>
                                <div class="text-xs">Payout: THB {{ number_format((float) $assignment->vendor_payout_amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">{{ ucfirst(str_replace('_', ' ', $assignment->settlement_status)) }}</span>
                                <div class="text-xs text-gray-500 mt-1">{{ optional($assignment->settled_at)->format('d M Y H:i') ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.bookings.show', $assignment->booking_id) }}" class="text-teal-600 hover:text-teal-800 font-medium">Open Booking</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No dispatch assignments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $assignments->links() }}</div>
</x-layouts.admin.app>
