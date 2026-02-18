<x-layouts.admin.app>
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Adjust User Points</h1>
            <a href="{{ route('admin.points.index') }}" class="text-sm text-teal-600 hover:text-teal-700">← Back</a>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded shadow p-4">
                <p class="text-xs text-gray-500">User</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $user->name }} (#{{ $user->id }})</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded shadow p-4">
                <p class="text-xs text-gray-500">Current Balance</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ number_format($summary['current_balance'] ?? 0) }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded shadow p-6">
            <form method="POST" action="{{ route('admin.points.adjust', $user) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Points (+ tambah / - kurangi)</label>
                    <input type="number" name="points" value="{{ old('points') }}" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="contoh: 100 atau -100">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <input type="text" name="description" value="{{ old('description') }}" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Alasan adjustment">
                </div>
                <button class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Submit Adjustment</button>
            </form>
        </div>
    </div>
</x-layouts.admin.app>
