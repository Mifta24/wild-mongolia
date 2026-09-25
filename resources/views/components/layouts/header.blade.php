@props(['transparent' => false])
@php
    $linkBase = 'transition text-sm tracking-wide ';
@endphp
<nav x-data="{ scrolled: false, searchOpen: false }" @keydown.escape.window="searchOpen = false" @scroll.window="scrolled = window.scrollY > 40"
    @if ($transparent)
    :class="scrolled ? 'bg-gray-900/90 backdrop-blur-md shadow-md' : 'bg-gradient-to-b from-black/50 to-transparent'"
    class="fixed top-0 w-full z-50 transition-all duration-300 text-white"
    @else
    class="sticky top-0 w-full z-50 transition-all duration-300 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md shadow-sm border-b border-gray-200 dark:border-gray-800"
    @endif>
    <div class="{{ $transparent ? 'max-w-none px-4 sm:px-6 lg:px-10' : 'max-w-7xl px-4 sm:px-6 lg:px-8' }} mx-auto">
        <div class="flex justify-between {{ $transparent ? 'h-24' : 'h-16' }} items-center">
            <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                <x-brand-logo :light="$transparent" />
            </a>

            <div class="hidden md:flex space-x-5 lg:space-x-9 items-center">
                <a href="{{ route('home') }}" class="{{ $linkBase }} {{ $transparent ? 'text-white/95 hover:text-white' : 'hover:text-primary dark:hover:text-teal-400' }} {{ request()->routeIs('home') ? 'border-b-2 border-amber-300 pb-1' : '' }}">{{ __('site.home') }}</a>
                <a href="{{ route('tours') }}" class="{{ $linkBase }} {{ $transparent ? 'text-white/95 hover:text-white' : 'hover:text-primary dark:hover:text-teal-400' }} {{ request()->routeIs('tours') ? 'border-b-2 border-amber-300 pb-1' : '' }}">{{ __('site.tours') }}</a>
                <a href="{{ route('cars') }}" class="{{ $linkBase }} {{ $transparent ? 'text-white/95 hover:text-white' : 'hover:text-primary dark:hover:text-teal-400' }} {{ request()->routeIs('cars') ? 'border-b-2 border-amber-300 pb-1' : '' }}">{{ __('site.cars') }}</a>
                <a href="{{ route('membership') }}" class="{{ $linkBase }} {{ $transparent ? 'text-white/95 hover:text-white' : 'hover:text-primary dark:hover:text-teal-400' }} {{ request()->routeIs('membership') ? 'border-b-2 border-amber-300 pb-1' : '' }}">{{ __('site.membership') }}</a>
                <a href="{{ route('faq') }}" class="{{ $linkBase }} {{ $transparent ? 'text-white/95 hover:text-white' : 'hover:text-primary dark:hover:text-teal-400' }} {{ request()->routeIs('faq') ? 'border-b-2 border-amber-300 pb-1' : '' }}">{{ __('site.travel_info') }}</a>
                <a href="{{ route('contact') }}" class="{{ $linkBase }} {{ $transparent ? 'text-white/95 hover:text-white' : 'hover:text-primary dark:hover:text-teal-400' }} {{ request()->routeIs('contact') ? 'border-b-2 border-amber-300 pb-1' : '' }}">{{ __('site.contact') }}</a>

                <button @click="toggleTheme()"
                    class="{{ $transparent ? 'hidden' : '' }} p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition">
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


                @if ($transparent)
                    <button type="button" @click="searchOpen = true; $nextTick(() => $refs.searchInput.focus())"
                        class="hidden lg:block hover:text-amber-200 transition" aria-label="{{ __('site.search') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                    </button>
                    <div class="hidden lg:flex items-center gap-2 relative" x-data="{ open: false }" @click.away="open = false">
                        <button type="button" @click="open = !open" class="flex items-center gap-2 text-sm hover:text-amber-200 transition" aria-label="{{ __('site.language') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 21a9 9 0 100-18 9 9 0 000 18zM3.6 9h16.8M3.6 15h16.8M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/></svg>
                            <span>{{ strtoupper(app()->getLocale()) }}</span> <span>&#8964;</span>
                        </button>
                        <div x-show="open" x-cloak x-transition
                            class="absolute right-0 top-full mt-3 w-44 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 shadow-xl border border-gray-100 dark:border-gray-700 py-1">
                            @foreach (config('app.available_locales') as $code => $label)
                                <a href="{{ route('lang.switch', $code) }}"
                                    class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === $code ? 'font-semibold text-primary' : '' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
                <a href="{{ route('contact') }}"
                    class=" hidden lg:inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-200 to-amber-400 text-gray-900 text-sm font-semibold px-5 py-2.5 shadow hover:brightness-105 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l9 6 9-6M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/></svg>
                    {{ __('site.consult') }} &rarr;
                </a>

                @auth
                    <div x-data="{ dropdownOpen: false }" class="relative">

                        <button @click="dropdownOpen = !dropdownOpen"
                            class="flex items-center space-x-2 relative focus:outline-none">
                            <div
                                class="w-8 h-8 rounded-full bg-teal-500 flex items-center justify-center text-white font-bold">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                            <span class="hidden md:block text-sm font-medium {{ $transparent ? 'text-white' : 'text-gray-700 dark:text-gray-200' }}">
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
                        class="px-4 py-2 rounded-full text-sm transition {{ $transparent ? 'border border-white/70 text-white hover:bg-white hover:text-gray-900' : 'bg-primary hover:bg-teal-700 text-white' }}">{{ __('site.login') }}</a>
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
                    class="{{ $transparent ? 'text-white' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }} focus:outline-none">
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
                <a href="{{ route('home') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary dark:hover:text-teal-400 transition">{{ __('site.home') }}</a>
                <a href="{{ route('cars') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary dark:hover:text-teal-400 transition">{{ __('site.cars') }}</a>
                <a href="{{ route('tours') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary dark:hover:text-teal-400 transition">{{ __('site.tours') }}</a>
                <a href="{{ route('membership') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary dark:hover:text-teal-400 transition">{{ __('site.membership') }}</a>
                <a href="{{ route('faq') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary dark:hover:text-teal-400 transition">{{ __('site.travel_info') }}</a>
                <a href="{{ route('contact') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary dark:hover:text-teal-400 transition">{{ __('site.contact') }}</a>

                <div class="flex flex-wrap gap-2 px-3 py-2">
                    @foreach (config('app.available_locales') as $code => $label)
                        <a href="{{ route('lang.switch', $code) }}"
                            class="px-3 py-1 rounded-full text-sm border {{ app()->getLocale() === $code ? 'bg-primary text-white border-primary' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300' }}">{{ $label }}</a>
                    @endforeach
                </div>

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
                            class="block px-3 py-2 rounded-md text-base font-medium bg-primary text-white hover:bg-teal-700 transition text-center">{{ __('site.login') }}</a>
                    </div>
                @endguest
            </div>
        </div>
    </div>
    @if ($transparent)
        <div x-show="searchOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[60] bg-black/70 backdrop-blur-sm flex items-start justify-center pt-32 px-4"
            @click.self="searchOpen = false">
            <form method="GET" action="{{ route('search.tours') }}"
                class="w-full max-w-2xl flex items-center gap-3 bg-white dark:bg-gray-800 rounded-full shadow-2xl pl-6 pr-2 py-2 text-gray-900 dark:text-white">
                <input type="hidden" name="destination" value="all">
                <input x-ref="searchInput" type="text" name="experience_type" placeholder="{{ __('site.search_placeholder') }}"
                    class="flex-1 bg-transparent border-0 focus:ring-0 focus:outline-none text-base">
                <button type="submit" class="rounded-full bg-gradient-to-r from-amber-200 to-amber-400 text-gray-900 font-semibold px-6 py-2.5">{{ __('site.search') }}</button>
            </form>
        </div>
    @endif
</nav>
