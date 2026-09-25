<x-layouts.app :transparent-nav="true">
    <div class="relative pt-28 lg:pt-32 pb-6 flex flex-col justify-between min-h-[100vh] overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-[68%_center] md:bg-center"
            style="background-image: url('{{ asset('images/wild-mongolia2.png') }}');">
            <span class="absolute inset-0 bg-gradient-to-r from-black/45 via-black/5 to-transparent"></span>
            <span class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/50 to-transparent"></span>
        </div>

        <div class="container max-w-none relative mx-auto px-6 lg:px-16 flex-1 flex items-center">
            <div class="max-w-3xl text-white drop-shadow-lg py-8">
                <p class="font-serif text-lg md:text-3xl tracking-[0.08em]">{{ __('site.eyebrow') }}</p>
                <h1 style="font-family: 'Playfair Display', serif" class="mt-2 font-bold text-6xl md:text-8xl lg:text-[7rem] leading-[1.05] tracking-tight">Wild Mongolia</h1>
                <div class="relative inline-block -mt-2 -rotate-3 origin-left pb-4">
                    <span class="font-script text-6xl md:text-8xl text-amber-200">{{ __('site.sub') }}</span>
                    <svg class="absolute left-0 bottom-0 w-[92%] h-5 text-amber-300" viewBox="0 0 400 20" preserveAspectRatio="none" fill="none" aria-hidden="true">
                        <path d="M2 16C90 6 230 4 398 7" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                    </svg>
                </div>
                <p class="font-serif mt-6 max-w-xl text-sm md:text-lg text-white/95 leading-loose">
                    {!! implode('<br>', array_map('e', __('site.desc'))) !!}
                </p>
                <div class="mt-7 flex flex-wrap items-center gap-6">
                    <a href="{{ route('search.tours') }}"
                        class="font-serif inline-flex items-center gap-3 bg-gradient-to-r from-amber-100 to-amber-300 text-gray-900 font-semibold text-xl px-12 py-4 rounded-full shadow-xl hover:brightness-105 transition">
                        {{ __('site.cta') }} <span>&rarr;</span>
                    </a>
                    @php
                        $videoUrl = config('services.hero_video');
                        preg_match('~(?:youtu\.be/|v=|embed/)([\w-]{11})~', (string) $videoUrl, $m);
                        $videoId = $m[1] ?? null;
                    @endphp
                    <div x-data="{ videoOpen: false }" class="contents">
                        <button type="button" @if ($videoId) @click="videoOpen = true" @else onclick="document.getElementById('book').scrollIntoView({behavior: 'smooth'})" @endif
                            class="font-serif inline-flex items-center gap-3 text-white text-base">
                            <span class="h-14 w-14 rounded-full border-2 border-white/80 flex items-center justify-center hover:bg-white/20 transition">&#9654;</span>
                            {{ __('site.watch') }}
                        </button>
                        @if ($videoId)
                            <div x-show="videoOpen" x-cloak x-transition.opacity @keydown.escape.window="videoOpen = false"
                                class="fixed inset-0 z-[70] bg-black/85 flex items-center justify-center p-4" @click.self="videoOpen = false">
                                <div class="relative w-full max-w-4xl aspect-video">
                                    <button type="button" @click="videoOpen = false" class="absolute -top-10 right-0 text-white text-3xl leading-none" aria-label="Close video">&times;</button>
                                    <template x-if="videoOpen">
                                        <iframe class="w-full h-full rounded-lg shadow-2xl"
                                            src="https://www.youtube-nocookie.com/embed/{{ $videoId }}?autoplay=1&rel=0"
                                            title="Wild Mongolia" allow="autoplay; encrypted-media; fullscreen" allowfullscreen></iframe>
                                    </template>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden lg:block absolute right-8 top-[40%] -rotate-[8deg] text-center text-white pointer-events-none w-[300px] h-[210px]">
            <svg class="absolute inset-0 w-full h-full" viewBox="0 0 300 210" fill="none" aria-hidden="true">
                <path d="M30 60 C40 30 90 20 120 32 C150 14 200 12 235 30 C270 40 292 70 280 100 C292 130 270 165 240 170 C215 190 170 196 140 182 C110 196 60 190 40 160 C15 140 20 95 30 60Z"
                    stroke="white" stroke-opacity=".85" stroke-width="1.5" />
            </svg>
            <div class="relative pt-11">
                <p class="text-4xl leading-[1.1]" style="font-family: 'Dancing Script', cursive;">Feel<br>Real Mongolia</p>
                <p class="mt-2 text-[10px] tracking-[0.25em]">NATURE · PEOPLE · CULTURE</p>
                <p class="mt-2 text-xl" style="filter: grayscale(1) brightness(10)">🐎🐎 ⛺</p>
            </div>
        </div>

        <div class="container max-w-none relative mx-auto px-4 lg:px-6 mt-6">
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 lg:gap-4">
                <a href="{{ route('search.tours') }}"
                    class="group relative h-44 lg:h-52 rounded-lg overflow-hidden shadow-2xl">
                    <img src="{{ asset('images/card-1.png') }}" alt="{{ __('site.c1t') }}"
                        class="absolute inset-0 w-full h-full object-cover transition duration-500 group-hover:scale-105">
                    <span class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent"></span>
                    <span class="absolute bottom-3 left-4 right-14 text-white">
                        <span class="block font-serif text-sm lg:text-base leading-tight">{{ __('site.c1t') }}</span>
                        <span class="block font-serif text-[10px] lg:text-xs tracking-wider text-white/85 mt-1">{{ __('site.c1l') }}</span>
                    </span>
                    <span class="absolute bottom-3 right-3 h-9 w-9 rounded-full border border-white/80 text-white flex items-center justify-center group-hover:bg-white group-hover:text-gray-900 transition">&rarr;</span>
                </a>
                <a href="{{ route('search.tours') }}"
                    class="group relative h-44 lg:h-52 rounded-lg overflow-hidden shadow-2xl">
                    <img src="{{ asset('images/card-2.png') }}" alt="{{ __('site.c2t') }}"
                        class="absolute inset-0 w-full h-full object-cover transition duration-500 group-hover:scale-105">
                    <span class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent"></span>
                    <span class="absolute bottom-3 left-4 right-14 text-white">
                        <span class="block font-serif text-sm lg:text-base leading-tight">{{ __('site.c2t') }}</span>
                        <span class="block font-serif text-[10px] lg:text-xs tracking-wider text-white/85 mt-1">{{ __('site.c2l') }}</span>
                    </span>
                    <span class="absolute bottom-3 right-3 h-9 w-9 rounded-full border border-white/80 text-white flex items-center justify-center group-hover:bg-white group-hover:text-gray-900 transition">&rarr;</span>
                </a>
                <a href="{{ route('search.tours') }}"
                    class="group relative h-44 lg:h-52 rounded-lg overflow-hidden shadow-2xl">
                    <img src="{{ asset('images/card-3.png') }}" alt="{{ __('site.c3t') }}"
                        class="absolute inset-0 w-full h-full object-cover transition duration-500 group-hover:scale-105">
                    <span class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent"></span>
                    <span class="absolute bottom-3 left-4 right-14 text-white">
                        <span class="block font-serif text-sm lg:text-base leading-tight">{{ __('site.c3t') }}</span>
                        <span class="block font-serif text-[10px] lg:text-xs tracking-wider text-white/85 mt-1">{{ __('site.c3l') }}</span>
                    </span>
                    <span class="absolute bottom-3 right-3 h-9 w-9 rounded-full border border-white/80 text-white flex items-center justify-center group-hover:bg-white group-hover:text-gray-900 transition">&rarr;</span>
                </a>
                <a href="{{ route('search.tours') }}"
                    class="group relative h-44 lg:h-52 rounded-lg overflow-hidden shadow-2xl">
                    <img src="{{ asset('images/card-4.png') }}" alt="{{ __('site.c4t') }}"
                        class="absolute inset-0 w-full h-full object-cover transition duration-500 group-hover:scale-105">
                    <span class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent"></span>
                    <span class="absolute bottom-3 left-4 right-14 text-white">
                        <span class="block font-serif text-sm lg:text-base leading-tight">{{ __('site.c4t') }}</span>
                        <span class="block font-serif text-[10px] lg:text-xs tracking-wider text-white/85 mt-1">{{ __('site.c4l') }}</span>
                    </span>
                    <span class="absolute bottom-3 right-3 h-9 w-9 rounded-full border border-white/80 text-white flex items-center justify-center group-hover:bg-white group-hover:text-gray-900 transition">&rarr;</span>
                </a>
                <a href="{{ route('search.tours') }}"
                    class="group relative h-44 lg:h-52 rounded-lg overflow-hidden shadow-2xl">
                    <img src="{{ asset('images/card-5.png') }}" alt="{{ __('site.c5t') }}"
                        class="absolute inset-0 w-full h-full object-cover transition duration-500 group-hover:scale-105">
                    <span class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent"></span>
                    <span class="absolute bottom-3 left-4 right-14 text-white">
                        <span class="block font-serif text-sm lg:text-base leading-tight">{{ __('site.c5t') }}</span>
                        <span class="block font-serif text-[10px] lg:text-xs tracking-wider text-white/85 mt-1">{{ __('site.c5l') }}</span>
                    </span>
                    <span class="absolute bottom-3 right-3 h-9 w-9 rounded-full border border-white/80 text-white flex items-center justify-center group-hover:bg-white group-hover:text-gray-900 transition">&rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <section id="book" class="relative -mt-0 py-12 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden max-w-4xl mx-auto border border-gray-100 dark:border-gray-700"
                x-data="{ activeTab: 'cars' }">
                <div class="flex border-b border-gray-200 dark:border-gray-700">
                    <button @click="activeTab = 'cars'"
                        :class="activeTab === 'cars' ? 'border-primary text-primary dark:text-teal-400 border-b-2' :
                            'text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                        class="w-1/2 py-4 text-center font-semibold transition bg-gray-50 dark:bg-gray-900 hover:bg-white dark:hover:bg-gray-800">
                        {{ __('site.tab_car') }}
                    </button>
                    <button @click="activeTab = 'tours'"
                        :class="activeTab === 'tours' ? 'border-primary text-primary dark:text-teal-400 border-b-2' :
                            'text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                        class="w-1/2 py-4 text-center font-semibold transition bg-gray-50 dark:bg-gray-900 hover:bg-white dark:hover:bg-gray-800">
                        {{ __('site.tab_tours') }}
                    </button>
                </div>

                <div x-show="activeTab === 'cars'" class="p-6 md:p-8"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100">
                    <form method="GET" action="{{ route('search.cars') }}"
                        class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('site.service_type') }}</label>
                            <select name="service_type"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                                <option value="airport_transfer">{{ __('site.airport_transfer') }}</option>
                                <option value="city_rental">{{ __('site.city_rental') }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('site.pickup') }}</label>
                            <input type="text" name="pickup_location" placeholder="{{ __('site.pickup_ph') }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('site.date') }}</label>
                            <input type="date" name="service_date" min="{{ date('Y-m-d') }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                        </div>
                        <div class="md:col-span-1 flex items-end">
                            <button type="submit"
                                class="w-full bg-primary hover:bg-teal-700 text-white font-bold py-2.5 rounded-lg shadow-lg transition transform hover:-translate-y-0.5">
                                {{ __('site.search_cars') }}
                            </button>
                        </div>
                    </form>
                </div>

                <div x-show="activeTab === 'tours'" x-cloak class="p-6 md:p-8"
                    x-transition:enter="transition ease-out duration-300">
                    <form method="GET" action="{{ route('search.tours') }}"
                        class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-1">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('site.destination') }}</label>
                            <select name="destination"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                                <option value="ulaanbaatar">Ulaanbaatar</option>
                                <option value="gobi">Gobi Desert</option>
                                <option value="terelj">Terelj National Park</option>
                                <option value="kharkhorin">Kharkhorin</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('site.experience') }}</label>
                            <input type="text" name="experience_type"
                                placeholder="{{ __('site.experience_ph') }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                        </div>
                        <div class="md:col-span-1 flex items-end">
                            <button type="submit"
                                class="w-full bg-secondary hover:bg-yellow-600 text-white font-bold py-2.5 rounded-lg shadow-lg transition transform hover:-translate-y-0.5">
                                {{ __('site.find_tours') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section id="charm" class="py-20 bg-white dark:bg-gray-900 scroll-mt-24">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="p-6 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <div
                        class="w-16 h-16 bg-teal-100 dark:bg-teal-900 rounded-full flex items-center justify-center mx-auto mb-4 text-primary dark:text-teal-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">{{ __('site.f1t') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('site.f1d') }}</p>
                </div>
                <div class="p-6 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <div
                        class="w-16 h-16 bg-teal-100 dark:bg-teal-900 rounded-full flex items-center justify-center mx-auto mb-4 text-primary dark:text-teal-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">{{ __('site.f2t') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('site.f2d') }}</p>
                </div>
                <div class="p-6 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <div
                        class="w-16 h-16 bg-teal-100 dark:bg-teal-900 rounded-full flex items-center justify-center mx-auto mb-4 text-primary dark:text-teal-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">{{ __('site.f3t') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('site.f3d') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('site.tr_title') }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('site.tr_sub') }}</p>
                </div>
                <a href="{{ route('search.tours') }}" class="text-primary font-semibold hover:underline">{{ __('site.view_all') }} &rarr;</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg overflow-hidden group">
                    <div class="relative h-48 overflow-hidden">
                        <img src="{{ asset('images/gobi.jpg') }}"
                            class="w-full h-full object-cover transition transform group-hover:scale-110"
                            alt="Gobi Desert dunes">
                        <div
                            class="absolute top-2 right-2 bg-white/90 px-2 py-1 rounded text-xs font-bold text-gray-800">
                            Day Tour</div>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center space-x-1 text-yellow-400 text-sm mb-2">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span> <span
                                class="text-gray-400 dark:text-gray-500">(120)</span>
                        </div>
                        <h3 class="text-lg font-bold mb-2 line-clamp-2 hover:text-primary cursor-pointer">Gobi Desert Dunes
                            &
                            Camel Trek</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: 2 Days • English Guide</p>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm line-through">MNT 150,000</span>
                            <span class="text-xl font-bold text-primary">MNT 120,000</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg overflow-hidden group">
                    <div class="relative h-48 overflow-hidden">
                        <img src="{{ asset('images/eagle.jpg') }}"
                            class="w-full h-full object-cover transition transform group-hover:scale-110"
                            alt="Eagle hunter">
                        <div
                            class="absolute top-2 right-2 bg-white/90 px-2 py-1 rounded text-xs font-bold text-gray-800">
                            Nature</div>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center space-x-1 text-yellow-400 text-sm mb-2">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span
                                class="text-gray-300">★</span>
                            <span class="text-gray-400 dark:text-gray-500">(85)</span>
                        </div>
                        <h3 class="text-lg font-bold mb-2 line-clamp-2 hover:text-primary cursor-pointer">Eagle Hunter
                            Family
                            Homestay</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: 1 Day • Lunch Included</p>
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-primary">MNT 250,000</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gradient-to-r from-teal-600 to-teal-800 text-white">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center justify-between">
            <div class="md:w-1/2 mb-8 md:mb-0">
                <h2 class="text-3xl font-bold mb-4">{{ __('site.club_title') }}</h2>
                <p class="text-teal-100 text-lg mb-6">{!! __('site.club_text', ['points' => '<span class="font-bold text-yellow-400">' . e(__('site.club_pts')) . '</span>']) !!}</p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center"><svg class="w-5 h-5 mr-2 text-yellow-400" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                        </svg> {{ __('site.club_b1') }}</li>
                    <li class="flex items-center"><svg class="w-5 h-5 mr-2 text-yellow-400" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                        </svg> {{ __('site.club_b2') }}</li>
                </ul>
                <a href="/register"
                    class="inline-block bg-white text-teal-800 font-bold px-8 py-3 rounded-lg hover:bg-gray-100 transition shadow-lg">{{ __('site.signup') }}</a>
            </div>
            <div class="md:w-1/3">
                <div class="bg-white/10 backdrop-blur-sm p-6 rounded-2xl border border-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm uppercase tracking-wider text-teal-200">Membership Card</span>
                        <span class="font-mono text-yellow-400">GOLD TIER</span>
                    </div>
                    <div class="text-2xl font-bold mb-8">John Doe</div>
                    <div class="flex justify-between items-end">
                        <div>
                            <div class="text-xs text-teal-200">Points Balance</div>
                            <div class="text-xl font-bold">1,250 PTS</div>
                        </div>
                        <div class="h-8 w-8 bg-yellow-400 rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @auth
        <div class="fixed bottom-6 right-6 z-50" x-data="floatingSupportChat()">
            <button @click="toggle"
                class="h-14 w-14 rounded-full bg-primary text-white shadow-xl flex items-center justify-center hover:bg-teal-700 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h8m-8 4h5m-6 6l-4-4H4a2 2 0 01-2-2V6a2 2 0 012-2h16a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                    </path>
                </svg>
            </button>

            <div x-cloak x-show="open" x-transition
                class="mt-4 w-[360px] max-w-[90vw] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Support Chat</h3>
                        <p class="text-xs text-gray-500">Online now</p>
                    </div>
                    <button @click="toggle" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-4">
                    <div class="h-64 overflow-y-auto space-y-3 pr-1" x-ref="messages">
                        <template x-for="message in messages" :key="message.id">
                            <div class="flex"
                                :class="message.user.id === currentUserId ? 'justify-end' : 'justify-start'">
                                <div class="max-w-[80%] rounded-2xl px-3 py-2"
                                    :class="message.user.id === currentUserId ? 'bg-primary text-white' :
                                        'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100'">
                                    <div class="text-[10px] opacity-80 mb-1" x-text="message.user.name"></div>
                                    <div class="text-sm whitespace-pre-line" x-text="message.body"></div>
                                </div>
                            </div>
                        </template>

                        <template x-if="loading">
                            <div class="text-xs text-gray-500">Loading...</div>
                        </template>
                    </div>

                    <form class="mt-4" @submit.prevent="send">
                        <div class="flex items-center gap-2">
                            <textarea x-model="newMessage" rows="2" placeholder="Type your message..."
                                class="flex-1 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:ring-primary focus:border-primary"></textarea>
                            <button type="submit"
                                class="bg-primary hover:bg-teal-700 text-white font-semibold px-4 py-2 rounded-xl shadow"
                                :disabled="sending || newMessage.trim().length === 0">
                                <span x-show="!sending">Send</span>
                                <span x-show="sending">...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="fixed bottom-6 right-6 z-50">
            <a href="{{ route('login') }}"
                class="h-14 w-14 rounded-full bg-primary text-white shadow-xl flex items-center justify-center hover:bg-teal-700 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h8m-8 4h5m-6 6l-4-4H4a2 2 0 01-2-2V6a2 2 0 012-2h16a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                    </path>
                </svg>
            </a>
        </div>
    @endauth

    @auth
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('floatingSupportChat', () => ({
                    open: false,
                    loading: false,
                    sending: false,
                    conversationId: null,
                    currentUserId: {{ auth()->id() }},
                    messages: [],
                    newMessage: '',
                    toggle() {
                        this.open = !this.open;
                        if (this.open && !this.conversationId) {
                            this.load();
                        }
                        this.$nextTick(() => this.scrollToBottom());
                    },
                    async load() {
                        this.loading = true;

                        try {
                            const response = await fetch('{{ route('support.chat') }}', {
                                headers: {
                                    'Accept': 'application/json',
                                }
                            });

                            if (response.ok) {
                                const data = await response.json();
                                this.conversationId = data?.conversation?.id ?? null;
                                this.messages = data?.messages ?? [];

                                if (this.conversationId && window.Echo) {
                                    window.Echo.private(`support.chat.${this.conversationId}`)
                                        .listen('.support.message', (payload) => {
                                            if (payload?.message) {
                                                this.messages.push(payload.message);
                                                this.$nextTick(() => this.scrollToBottom());
                                            }
                                        });
                                }
                            }
                        } finally {
                            this.loading = false;
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    },
                    async send() {
                        const body = this.newMessage.trim();
                        if (!body || this.sending) return;

                        this.sending = true;

                        try {
                            const response = await fetch('{{ route('support.chat.message') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    body
                                }),
                            });

                            if (response.ok) {
                                const data = await response.json();
                                if (data?.message) {
                                    this.messages.push(data.message);
                                    this.newMessage = '';
                                    this.$nextTick(() => this.scrollToBottom());
                                }
                            }
                        } finally {
                            this.sending = false;
                        }
                    },
                    scrollToBottom() {
                        if (this.$refs.messages) {
                            this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight;
                        }
                    }
                }));
            });
        </script>
    @endauth
</x-layouts.app>
