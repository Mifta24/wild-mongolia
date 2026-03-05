<x-layouts.admin.app>
    <div class="max-w-4xl mx-auto py-8 space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">My Profile</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update your account details and password.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Profile Information</h2>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Keep your admin identity information up to date.</p>

            <form method="POST" action="{{ route('admin.profile.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-md transition">Save Profile</button>
                    @if (session('status') === 'profile-updated')
                        <p class="text-sm text-green-600 dark:text-green-400">Profile updated.</p>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Update Password</h2>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Use a strong password to secure admin access.</p>

            <form method="POST" action="{{ route('password.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
                    <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
                    @if ($errors->updatePassword->has('current_password'))
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $errors->updatePassword->first('current_password') }}</p>
                    @endif
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
                    @if ($errors->updatePassword->has('password'))
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $errors->updatePassword->first('password') }}</p>
                    @endif
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
                    @if ($errors->updatePassword->has('password_confirmation'))
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-md transition">Update Password</button>
                    @if (session('status') === 'password-updated')
                        <p class="text-sm text-green-600 dark:text-green-400">Password updated.</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin.app>
