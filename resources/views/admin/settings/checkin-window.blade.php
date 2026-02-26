<x-layouts.admin.app>
    <div class="max-w-3xl mx-auto py-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Check-In Window Settings</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure how many days before/after service date QR check-in is allowed.</p>
            </div>

            <form action="{{ route('admin.settings.checkin-window.update') }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="days_before" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Days Before Service Date</label>
                        <input id="days_before" name="days_before" type="number" min="0" max="30" value="{{ old('days_before', $daysBefore) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-teal-500 focus:border-teal-500">
                        @error('days_before')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="days_after" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Days After Service Date</label>
                        <input id="days_after" name="days_after" type="number" min="0" max="30" value="{{ old('days_after', $daysAfter) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-teal-500 focus:border-teal-500">
                        @error('days_after')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/40 border border-gray-200 dark:border-gray-600 rounded-md p-3 text-xs text-gray-600 dark:text-gray-300">
                    Current effective rule: check-in allowed from H-{{ $daysBefore }} to H+{{ $daysAfter }}.
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                        Save Settings
                    </button>
                    <form action="{{ route('admin.settings.checkin-window.reset') }}" method="POST"
                        onsubmit="return confirm('Reset check-in window settings to default values?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 px-4 py-2 rounded-md text-sm font-medium transition border border-gray-300 dark:border-gray-600">
                            Reset to Default
                        </button>
                    </form>
                    <a href="{{ route('admin.bookings.index') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        Back to Bookings
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin.app>
