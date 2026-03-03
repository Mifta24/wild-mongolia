<header
    class="flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">

    <div class="flex items-center">
        <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>

        <div class="relative mx-4 lg:mx-0">
            <span class="text-gray-400 lg:hidden">/</span>
            <span class="hidden lg:block text-gray-800 dark:text-white font-semibold capitalize">
                {{ str_replace('.', ' > ', Route::currentRouteName()) }}
            </span>
        </div>
    </div>

    <div class="flex items-center space-x-4">

        <button @click="toggleTheme()"
            class="p-2 rounded-full text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
            <svg x-show="!darkMode" class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                </path>
            </svg>
            <svg x-show="darkMode" x-cloak class="w-5 h-5 text-teal-300" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.293 14.707A8 8 0 019.293 6.707 8.001 8.001 0 1017.293 14.707z"></path>
            </svg>
        </button>

        <div x-data="{ dropdownOpen: false }" class="relative">

            <button @click="dropdownOpen = !dropdownOpen"
                class="flex items-center space-x-2 relative focus:outline-none">
                <div class="w-8 h-8 rounded-full bg-teal-500 flex items-center justify-center text-white font-bold">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ Auth::user()->name ?? 'Admin' }}
                </span>
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700 py-1 z-50">

                <a href="#"
                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Settings</a>

                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                        class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 font-medium">
                        Log Out
                    </a>
                </form>

            </div>
        </div>
    </div>
</header>
