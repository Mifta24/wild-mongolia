<x-layouts.app>
    <section class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <p class="text-sm uppercase tracking-wide text-primary font-semibold mb-2">Premium Transfers</p>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">Car rental & private
                        driver in Mongolia</h1>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">Licensed fleet, experienced drivers, transparent
                        pricing. Ideal for airport transfers, city tours, or business trips.</p>
                    <div class="flex flex-wrap gap-3 mb-6">
                        <span class="px-3 py-2 bg-teal-50 text-primary rounded-full text-sm">Airport transfer</span>
                        <span class="px-3 py-2 bg-teal-50 text-primary rounded-full text-sm">City ride</span>
                        <span class="px-3 py-2 bg-teal-50 text-primary rounded-full text-sm">10-hour charter</span>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('search.cars') }}"
                            class="px-5 py-3 bg-primary text-white rounded-lg hover:bg-teal-700 transition">View
                            fleet</a>
                        <a href="/contact"
                            class="px-5 py-3 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 transition">Need
                            help?</a>
                    </div>
                </div>
                <div
                    class="bg-gray-50 dark:bg-gray-800/70 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Vehicle options</h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center font-semibold">
                                S</div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Sedan</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">2-3 passengers, ideal for city rides
                                    & airport transfers.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center font-semibold">
                                V</div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">MPV / Van</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">5-9 passengers, great for families
                                    or small groups.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center font-semibold">
                                L</div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Luxury</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Premium option for VIP guests and
                                    business travel.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Why book with Wild Mongolia?</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div
                    class="p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Licensed drivers</p>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">Experienced local drivers with 24/7 Indonesian
                        and English support.</p>
                </div>
                <div
                    class="p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Transparent pricing</p>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">All fees include tolls, standard parking, and
                        passenger insurance.</p>
                </div>
                <div
                    class="p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Free cancellation</p>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">Cancel free up to 24 hours before pickup.</p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
