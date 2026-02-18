<x-layouts.admin.app>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Membership Statistics</h1>
        <a href="{{ route('admin.points.index') }}" class="text-sm text-teal-600 hover:text-teal-700">← Back</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded shadow p-6 text-center">
            <p class="text-sm text-gray-500">Silver</p>
            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($stats['silver'] ?? 0) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded shadow p-6 text-center">
            <p class="text-sm text-gray-500">Gold</p>
            <p class="text-3xl font-bold text-yellow-600">{{ number_format($stats['gold'] ?? 0) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded shadow p-6 text-center">
            <p class="text-sm text-gray-500">Platinum</p>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['platinum'] ?? 0) }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white">Top Members</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-left text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Tier</th>
                        <th class="px-4 py-3 text-right">Points</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($topMembers as $member)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $member->name }}</td>
                            <td class="px-4 py-3">{{ $member->email }}</td>
                            <td class="px-4 py-3">{{ ucfirst($member->membership_tier ?? 'silver') }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($member->points) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No member data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin.app>
