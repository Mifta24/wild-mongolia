<x-layouts.app>
    <section class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <p class="text-sm uppercase tracking-wide text-primary font-semibold mb-2">Curated Experiences</p>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">Curated tour packages
                        in
                        Mongolia</h1>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">From Ulaanbaatar city highlights, Gobi desert camel treks,
                        to nomad stays in Terelj. All include transport and licensed guides.</p>
                    <div class="flex flex-wrap gap-3 mb-6">
                        <span class="px-3 py-2 bg-teal-50 text-primary rounded-full text-sm">Family friendly</span>
                        <span class="px-3 py-2 bg-teal-50 text-primary rounded-full text-sm">Private & small group</span>
                        <span class="px-3 py-2 bg-teal-50 text-primary rounded-full text-sm">Flexible itinerary</span>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('search.tours') }}"
                            class="px-5 py-3 bg-primary text-white rounded-lg hover:bg-teal-700 transition">Explore
                            tours</a>
                        <a href="/contact"
                            class="px-5 py-3 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 transition">Free
                            consultation</a>
                    </div>
                </div>
                <div
                    class="bg-gray-50 dark:bg-gray-800/70 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Popular destinations</h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center font-semibold">
                                UB</div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Ulaanbaatar</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Gandan Monastery, Sukhbaatar Square,
                                    Chinggis Khaan Museum.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center font-semibold">
                                GB</div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Gobi Desert</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Khongoryn Els dunes, camel trek,
                                    Flaming Cliffs.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center font-semibold">
                                TR</div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Terelj National Park</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Ger camp stays, horse riding,
                                    Turtle Rock hiking.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Why tour with Wild Mongolia?</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div
                    class="p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Licensed guides</p>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">Certified local guides, fluent in English and
                        Indonesian (on request).</p>
                </div>
                <div
                    class="p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Flexible itinerary</p>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">Swap attractions, adjust durations, or add
                        activities with ease.</p>
                </div>
                <div
                    class="p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Everything organized</p>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">Transport, tickets, and meals (per package) are
                        handled; you just enjoy the trip.</p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
