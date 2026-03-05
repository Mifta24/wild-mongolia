<x-layouts.admin.app>
    <div class="max-w-5xl mx-auto py-8 space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Settings</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage admin operational settings and system preferences.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <a href="{{ route('admin.settings.checkin-window.edit') }}"
                class="group rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm hover:shadow-md transition">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-300 transition">
                            Check-In Window
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                            Configure how many days before and after the service date QR check-in is allowed.
                        </p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-teal-50 dark:bg-teal-900/30 px-2.5 py-1 text-xs font-medium text-teal-700 dark:text-teal-300">
                        Active
                    </span>
                </div>

                <div class="mt-4 rounded-md border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/40 p-3 text-xs text-gray-600 dark:text-gray-300">
                    Effective rule: H-{{ $daysBefore }} to H+{{ $daysAfter }}
                </div>

                <div class="mt-4 text-sm font-medium text-teal-600 dark:text-teal-300">
                    Open setting ->
                </div>
            </a>

            <a href="{{ route('admin.customers.index', ['status' => 'deactivated']) }}"
                class="group rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm hover:shadow-md transition">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-300 transition">
                            Deactivated Accounts
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                            Review deactivated customers, monitor deletion deadlines, and reactivate accounts when needed.
                        </p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-teal-50 dark:bg-teal-900/30 px-2.5 py-1 text-xs font-medium text-teal-700 dark:text-teal-300">
                        Active
                    </span>
                </div>

                <div class="mt-4 text-sm font-medium text-teal-600 dark:text-teal-300">
                    Open setting ->
                </div>
            </a>

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Booking Rules
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                            Future settings for booking cutoff, auto-cancel policy, and operational constraints.
                        </p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 text-xs font-medium text-amber-700 dark:text-amber-300">
                        Planned
                    </span>
                </div>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">This section is reserved for the next release.</p>
            </div>

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Notifications
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                            Future settings for admin alerts, email preferences, and push notification behavior.
                        </p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 text-xs font-medium text-amber-700 dark:text-amber-300">
                        Planned
                    </span>
                </div>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">This section is reserved for the next release.</p>
            </div>

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            System Preferences
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                            Future settings for defaults, environment-sensitive options, and maintenance controls.
                        </p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 text-xs font-medium text-amber-700 dark:text-amber-300">
                        Planned
                    </span>
                </div>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">This section is reserved for the next release.</p>
            </div>
        </div>
    </div>
</x-layouts.admin.app>
