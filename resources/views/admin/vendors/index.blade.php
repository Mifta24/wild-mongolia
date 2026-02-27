<x-layouts.admin.app>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Vendors</h1>
        <a href="{{ route('admin.vendors.create') }}"
            class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">Create Vendor</a>
    </div>

    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, code, contact, phone"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Service Type</label>
                <select name="service_type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All</option>
                    <option value="car" {{ request('service_type') === 'car' ? 'selected' : '' }}>Car</option>
                    <option value="tour" {{ request('service_type') === 'tour' ? 'selected' : '' }}>Tour</option>
                    <option value="both" {{ request('service_type') === 'both' ? 'selected' : '' }}>Both</option>
                </select>
            </div>

            <div class="md:col-span-4 flex gap-2 justify-end">
                <button type="submit" class="px-4 py-2 text-sm bg-teal-600 text-white rounded-lg hover:bg-teal-700">Filter</button>
                <a href="{{ route('admin.vendors.index') }}"
                    class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Vendor</th>
                        <th class="px-6 py-3">Contact</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Commission</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vendors as $vendor)
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $vendor->vendor_code }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $vendor->name }}</div>
                                @if ($vendor->notes)
                                    <div class="text-xs text-gray-500 truncate max-w-[220px]">{{ $vendor->notes }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div>{{ $vendor->contact_person ?: '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $vendor->phone ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 uppercase">{{ $vendor->service_type ?: '-' }}</td>
                            <td class="px-6 py-4">{{ $vendor->default_commission_rate !== null ? number_format((float) $vendor->default_commission_rate, 2) . '%' : '-' }}</td>
                            <td class="px-6 py-4">
                                @if ($vendor->is_active)
                                    <span class="px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-xs bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.vendors.edit', $vendor) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Edit</a>
                                <form action="{{ route('admin.vendors.destroy', $vendor) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Delete this vendor?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No vendors found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $vendors->links() }}</div>
</x-layouts.admin.app>
