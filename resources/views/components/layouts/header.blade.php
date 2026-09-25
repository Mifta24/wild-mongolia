<nav
    class="sticky w-full z-50 transition-all duration-300 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md shadow-sm border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                <span class="text-2xl font-bold text-primary dark:text-teal-400">Wild<span
                        class="text-secondary">Mongolia</span></span>
            </a>

            <div class="hidden md:flex space-x-8 items-center">
                <a href="{{ route('cars') }}" class="hover:text-primary dark:hover:text-teal-400 transition">Cars</a>
                <a href="{{ route('tours') }}" class="hover:text-primary dark:hover:text-teal-400 transition">Tours</a>
                <a href="{{ route('membership') }}"
                    class="hover:text-primary dark:hover:text-teal-400 transition">Membership</a>

                <button @click="toggleTheme()"
                    class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg x-show="!darkMode" class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <svg x-show="darkMode" x-cloak class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.293 14.707A8 8 0 019.293 6.707 8.001 8.001 0 1017.293 14.707z">
                        </path>
                    </svg>
                </button>


                @auth
                    <div x-data="{ dropdownOpen: false }" class="relative">

                        <button @click="dropdownOpen = !dropdownOpen"
                            class="flex items-center space-x-2 relative focus:outline-none">
                            <div
                                class="w-8 h-8 rounded-full bg-teal-500 flex items-center justify-center text-white font-bold">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                            <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ Auth::user()->name ?? 'Admin' }}
                            </span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>

                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak
                            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700 py-1 z-50">

                            @role('user')
                            <a href="{{ route('user.dashboard') }}"
                                class="block px-4 py-2 text-sm font-medium {{ request()->routeIs('user.dashboard') ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                                My Dashboard
                            </a>
                            <a href="{{ route('user.bookings') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('user.bookings*') ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">My Bookings</a>
                            <a href="{{ route('points.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('points.*') ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">My Points</a>
                            <a href="{{ route('coupons.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('coupons.*') ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">My Coupons</a>

                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                            @endrole

                            @role('admin')
                            <a href="{{ route('admin.dashboard') }}"
                                class="block px-4 py-2 text-sm text-teal-600 dark:text-teal-400 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Admin Panel
                            </a>

                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                            @endrole

                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('profile.*') ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">Profile Settings</a>

                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 font-medium">
                                    Log Out
                                </a>
                            </form>

                        </div>
                    </div>
                @endauth
                @guest
                    <a href="/login"
                        class="px-4 py-2 bg-primary hover:bg-teal-700 text-white rounded-lg transition">Login</a>
                @endguest
            </div>

            <div class="md:hidden flex items-center space-x-2">
                <button @click="toggleTheme()"
                    class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg x-show="!darkMode" class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <svg x-show="darkMode" x-cloak class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.293 14.707A8 8 0 019.293 6.707 8.001 8.001 0 1017.293 14.707z">
                        </path>
                    </svg>
                </button>

                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline-none">
                    <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2" @click.away="mobileMenuOpen = false" x-cloak
            class="md:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('cars') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary dark:hover:text-teal-400 transition">Cars</a>
                <a href="{{ route('tours') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary dark:hover:text-teal-400 transition">Tours</a>
                <a href="{{ route('membership') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary dark:hover:text-teal-400 transition">Membership</a>

                @auth
                    <div class="border-t border-gray-200 dark:border-gray-700 mt-2 pt-2">
                        @role('user')
                        <a href="{{ route('user.dashboard') }}"
                            class="block px-3 py-2 rounded-md text-base font-semibold {{ request()->routeIs('user.dashboard') ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300' : 'text-teal-600 dark:text-teal-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            My Dashboard
                        </a>
                        <a href="{{ route('user.bookings') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium transition {{ request()->routeIs('user.bookings*') ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">My Bookings</a>
                        <a href="{{ route('points.index') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium transition {{ request()->routeIs('points.*') ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">My Points</a>
                        <a href="{{ route('coupons.index') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium transition {{ request()->routeIs('coupons.*') ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">My Coupons</a>
                        @endrole

                        @role('admin')
                        <a href="{{ route('admin.dashboard') }}"
                            class="block px-3 py-2 rounded-md text-base font-semibold text-teal-600 dark:text-teal-400 hover:bg-gray-100 dark:hover:bg-gray-800">
                            Admin Panel
                        </a>
                        @endrole

                        <a href="{{ route('profile.edit') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium transition {{ request()->routeIs('profile.*') ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">Profile Settings</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="block px-3 py-2 rounded-md text-base font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30">
                                Log Out
                            </a>
                        </form>
                    </div>
                @endauth

                @guest
                    <div class="border-t border-gray-200 dark:border-gray-700 mt-2 pt-2">
                        <a href="/login"
                            class="block px-3 py-2 rounded-md text-base font-medium bg-primary text-white hover:bg-teal-700 transition text-center">Login</a>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</nav>
