<x-layouts.guest>
    <div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 w-full h-full bg-center bg-cover"
            style="background-image: url('{{ asset('images/hero.jpg') }}');">
            <span class="w-full h-full absolute opacity-25 bg-black dark:opacity-60"></span>
        </div>

        <!-- Register Card -->
        <div class="relative w-full max-w-md">
            <!-- Logo/Title Section -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white drop-shadow-lg mb-2">
                    Join Wild Mongolia Club
                </h1>
                <p class="text-gray-200 drop-shadow-md">
                    Create an account and get <span class="font-bold text-yellow-400">300 Points</span> instantly!
                </p>
            </div>

            <!-- Register Form Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="p-8">
                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf

                        <!-- Name -->
                        <div>
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Full Name
                            </label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                                autofocus autocomplete="name"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary shadow-sm">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email Address
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autocomplete="username"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary shadow-sm">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Password
                            </label>
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary shadow-sm">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Confirm Password
                            </label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                autocomplete="new-password"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary shadow-sm">
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit"
                                class="w-full bg-primary hover:bg-teal-700 text-white font-bold py-3 rounded-lg shadow-lg transition transform hover:-translate-y-0.5">
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Login Link -->
                <div class="px-8 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700">
                    <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                        Already have an account?
                        <a href="{{ route('login') }}"
                            class="font-semibold text-primary dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300">
                            Sign in here
                        </a>
                    </p>
                </div>
            </div>

            <!-- Benefits -->
            <div class="mt-8 space-y-3">
                <div class="flex items-center text-white drop-shadow-md">
                    <svg class="w-5 h-5 mr-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                    </svg>
                    <span class="text-sm">Earn 1-3% cashback points on every booking</span>
                </div>
                <div class="flex items-center text-white drop-shadow-md">
                    <svg class="w-5 h-5 mr-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                    </svg>
                    <span class="text-sm">Priority customer support 24/7</span>
                </div>
                <div class="flex items-center text-white drop-shadow-md">
                    <svg class="w-5 h-5 mr-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                    </svg>
                    <span class="text-sm">Exclusive discounts and early access to tours</span>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>
