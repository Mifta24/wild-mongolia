<x-layouts.admin.app>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Vendor</h1>
    </div>

    <form action="{{ route('admin.vendors.store') }}" method="POST"
        class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        @csrf
        @include('admin.vendors.partials.form-fields', ['vendor' => null])

        <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition">Save</button>
            <a href="{{ route('admin.vendors.index') }}"
                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancel</a>
        </div>
    </form>
</x-layouts.admin.app>
