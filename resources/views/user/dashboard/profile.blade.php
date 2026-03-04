<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Profile Settings</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your account information</p>
                </div>
                <a href="{{ route('user.dashboard') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                    ← Back to Dashboard
                </a>
            </div>

            <!-- Success Message -->
            @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            <!-- Profile Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Personal Information</h2>
                </div>
                <form action="{{ route('user.profile.update') }}" method="POST" class="p-6">
                    @csrf

                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Full Name
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-teal-500">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email Address
                            </label>
                            <input type="email" id="email" value="{{ $user->email }}" disabled
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-gray-100 cursor-not-allowed">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email cannot be changed</p>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Phone Number
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-teal-500"
                                   placeholder="+66 12 345 6789">
                            @error('phone')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Membership Tier
                            </label>
                            @php
                                $isPaidActive = in_array($user->membership_tier, ['gold', 'platinum'], true)
                                    && $user->membership_expires_at
                                    && $user->membership_expires_at->isFuture();
                            @endphp
                            <div class="flex items-center space-x-3">
                                <span class="px-4 py-2 rounded-lg font-medium text-sm
                                    @if($user->membership_tier === 'silver') bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @elseif($user->membership_tier === 'gold') bg-yellow-200 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @else bg-purple-200 text-purple-800 dark:bg-purple-900 dark:text-purple-300 @endif">
                                    {{ ucfirst($user->membership_tier) }} Member
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $user->points }} points
                                </span>
                            </div>
                            @if($user->membership_started_at || $user->membership_expires_at)
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                @if($user->membership_started_at)
                                    Started: {{ $user->membership_started_at->format('d M Y H:i') }} ·
                                @endif
                                @if($user->membership_expires_at)
                                    Expires: {{ $user->membership_expires_at->format('d M Y H:i') }}
                                    ({{ $isPaidActive ? 'active' : 'expired' }})
                                @endif
                            </p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Account Statistics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Account Statistics</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Member Since</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->created_at->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Bookings</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->bookings()->count() }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Points Balance</p>
                            <p class="text-lg font-semibold text-teal-600">{{ number_format($user->points) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Available Coupons</p>
                            <p class="text-lg font-semibold text-teal-600">{{ $user->availableCoupons()->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Password & Security</h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Want to change your password or update security settings?
                    </p>
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-medium transition">
                        Go to Security Settings
                    </a>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="px-6 py-4 border-b border-red-200 dark:border-red-800">
                    <h2 class="text-xl font-bold text-red-900 dark:text-red-400">Danger Zone</h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-red-800 dark:text-red-300 mb-4">
                        Once you delete your account, there is no going back. Please be certain.
                    </p>
                    <form action="{{ route('profile.destroy') }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                            Delete Account
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
