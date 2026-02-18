<x-layouts.admin.app>
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Create Coupon</h1>
            <a href="{{ route('admin.coupons.index') }}" class="text-sm text-teal-600 hover:text-teal-700">← Back</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded shadow p-6">
            <form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-4">
                @csrf
                @include('admin.coupons.partials.form-fields')
                <button class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Save Coupon</button>
            </form>
        </div>
    </div>
</x-layouts.admin.app>
