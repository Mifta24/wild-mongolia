<x-layouts.admin.app>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Reviews Moderation</h1>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Rating</label>
                <select name="rating" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All</option>
                    @for($rate = 5; $rate >= 1; $rate--)
                        <option value="{{ $rate }}" @selected(request('rating') == $rate)>{{ $rate }} Star{{ $rate > 1 ? 's' : '' }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="User, product, comment"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 text-sm bg-teal-600 text-white rounded-lg hover:bg-teal-700">Filter</button>
                <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 rounded-lg">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium">User</th>
                    <th class="px-4 py-3 text-left text-xs font-medium">Product</th>
                    <th class="px-4 py-3 text-left text-xs font-medium">Rating</th>
                    <th class="px-4 py-3 text-left text-xs font-medium">Comment</th>
                    <th class="px-4 py-3 text-left text-xs font-medium">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($reviews as $review)
                    <tr>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium">{{ $review->user->name }}</div>
                            <div class="text-gray-500">{{ $review->user->email }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium">{{ $review->product->name }}</div>
                            <div class="text-gray-500">Booking: {{ $review->booking->booking_code ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-yellow-500">
                            @for($i = 0; $i < $review->rating; $i++)
                                ★
                            @endfor
                        </td>
                        <td class="px-4 py-3 text-sm max-w-sm">
                            <p class="truncate">{{ $review->comment ?: '-' }}</p>
                            @if($review->moderation_notes)
                                <p class="text-xs text-gray-500 mt-1">Note: {{ $review->moderation_notes }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($review->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                @elseif($review->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @endif">
                                {{ ucfirst($review->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}" class="space-y-2">
                                @csrf
                                @method('PUT')

                                <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                                    <option value="approved" @selected($review->status === 'approved')>Approve</option>
                                    <option value="rejected" @selected($review->status === 'rejected')>Reject</option>
                                </select>

                                <input type="text" name="moderation_notes" value="{{ $review->moderation_notes }}" placeholder="Moderation note"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">

                                <button type="submit" class="w-full px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                                    Save
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-gray-500">No reviews found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $reviews->links() }}</div>
</x-layouts.admin.app>
