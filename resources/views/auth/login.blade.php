<x-layouts.guest>
    <div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 w-full h-full bg-center bg-cover"
            style="background-image: url('{{ asset('images/hero.jpg') }}');">
            <span class="w-full h-full absolute opacity-25 bg-black dark:opacity-60"></span>
        </div>

        <!-- Login Card -->
        <div class="relative w-full max-w-md">
            <!-- Logo/Title Section -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white drop-shadow-lg mb-2">
                    Welcome Back
                </h1>
                <p class="text-gray-200 drop-shadow-md">
                    Sign in to continue your Mongolian adventure
                </p>
            </div>

            <!-- Login Form Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="p-8">
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email Address
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autofocus autocomplete="username"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary shadow-sm">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Password
                            </label>
                            <input id="password" type="password" name="password" required
                                autocomplete="current-password"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary shadow-sm">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" name="remember"
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary shadow-sm focus:ring-primary dark:focus:ring-primary dark:focus:ring-offset-gray-800">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="text-sm text-primary dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 font-medium"
                                    href="{{ route('password.request') }}">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit"
                                class="w-full bg-primary hover:bg-teal-700 text-white font-bold py-3 rounded-lg shadow-lg transition transform hover:-translate-y-0.5">
                                Sign In
                            </button>
                        </div>

                        <a href="{{ url('auth/google') }}"
                            class="flex items-center justify-center gap-2 w-full sm:w-auto px-6 py-3 
          bg-white border border-gray-300 rounded-lg shadow-md 
          hover:bg-gray-100 transition duration-200">
                            <!-- Logo Google -->
                            <svg class="w-5 h-5" viewBox="0 0 533.5 544.3">
                                <path fill="#4285F4"
                                    d="M533.5 278.4c0-17.4-1.5-34.1-4.3-50.4H272v95.4h147.5c-6.4 34.7-25.6 64.1-54.6 83.7v69.4h88.4c51.7-47.6 80.2-117.7 80.2-197.9z" />
                                <path fill="#34A853"
                                    d="M272 544.3c73.7 0 135.6-24.4 180.8-66.2l-88.4-69.4c-24.5 16.5-55.9 26.1-92.4 26.1-70.9 0-131-47.9-152.5-112.1H28.1v70.7c45.2 89.2 138.1 150.9 243.9 150.9z" />
                                <path fill="#FBBC05"
                                    d="M119.5 322.7c-10.9-32.6-10.9-67.8 0-100.4V151.6H28.1c-39.6 78.9-39.6 172 0 250.9l91.4-79.8z" />
                                <path fill="#EA4335"
                                    d="M272 107.7c38.9 0 73.8 13.4 101.3 39.6l75.9-75.9C407.6 24.4 345.7 0 272 0 166.2 0 73.3 61.7 28.1 151.6l91.4 70.7c21.5-64.2 81.6-112.1 152.5-112.1z" />
                            </svg>
                            <span class="text-gray-700 font-medium">Login with Google</span>
                        </a>


                    </form>
                </div>

                <!-- Register Link -->
                <div class="px-8 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700">
                    <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                        Don't have an account?
                        <a href="{{ route('register') }}"
                            class="font-semibold text-primary dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300">
                            Sign up for free
                        </a>
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-layouts.guest>
