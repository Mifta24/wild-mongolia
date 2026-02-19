<x-layouts.admin.app>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Cars & Tours</h1>
        <a href="{{ route('admin.products.create') }}"
            class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Create Product</a>
    </div>

    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="w-full md:w-1/4">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                <select name="type"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500 text-sm">
                    <option value="">All</option>
                    <option value="car" @selected(request('type') === 'car')>Car</option>
                    <option value="tour" @selected(request('type') === 'tour')>Tour</option>
                </select>
            </div>
            <div class="w-full md:w-1/4">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Active</label>
                <select name="active"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500 text-sm">
                    <option value="">All</option>
                    <option value="1" @selected(request('active') === '1')>Active</option>
                    <option value="0" @selected(request('active') === '0')>Inactive</option>
                </select>
            </div>
            <div class="w-full md:w-auto flex items-end gap-2">
                <button type="submit"
                    class="px-4 py-2 text-sm bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">Filter</button>
                <a href="{{ route('admin.products.index') }}"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-medium">Active</th>
                    <th class="px-6 py-3 text-left text-xs font-medium">Featured</th>
                    <th class="px-6 py-3 text-right text-xs font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($products as $product)
                    <tr>
                        <td class="px-6 py-3">{{ $product->name }}</td>
                        <td class="px-6 py-3">{{ ucfirst($product->type) }}</td>
                        <td class="px-6 py-3">{{ $product->currency }} {{ number_format($product->final_price, 2) }}
                        </td>
                        <td class="px-6 py-3">{{ number_format($product->average_rating, 1) }} ({{ number_format($product->total_reviews) }})</td>
                        <td class="px-6 py-3">{{ $product->is_active ? 'Yes' : 'No' }}</td>
                        <td class="px-6 py-3">{{ $product->is_featured ? 'Yes' : 'No' }}</td>
                        <td class="px-6 py-3 text-right space-x-2">
                            <a href="{{ route('admin.products.edit', $product) }}"
                                class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                                    onclick="return confirm('Delete this product?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-6 text-center text-gray-500">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
</x-layouts.admin.app>
