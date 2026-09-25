<x-layouts.app>
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">Frequently Asked Questions</h1>
                <p class="mt-4 text-gray-600 dark:text-gray-400">Have questions? We're here to help.</p>
            </div>

            <div class="space-y-4" x-data="{ selected: null }">

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button @click="selected !== 1 ? selected = 1 : selected = null"
                        class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none">
                        <span class="font-semibold text-gray-900 dark:text-gray-100">How do I meet my driver at the
                            airport?</span>
                        <svg :class="{ 'rotate-180': selected === 1 }"
                            class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="selected === 1" x-collapse class="px-6 pb-4">
                        <p class="text-gray-600 dark:text-gray-400">Our driver will be waiting at the arrival gate
                            holding a sign with your name. For Chinggis Khaan International Airport (UBN), the meeting point is usually
                            in the arrivals hall. Detailed instructions will be sent to your email after booking.</p>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button @click="selected !== 2 ? selected = 2 : selected = null"
                        class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none">
                        <span class="font-semibold text-gray-900 dark:text-gray-100">What if my flight is
                            delayed?</span>
                        <svg :class="{ 'rotate-180': selected === 2 }"
                            class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="selected === 2" x-collapse class="px-6 pb-4">
                        <p class="text-gray-600 dark:text-gray-400">We monitor flight schedules in real-time. We offer
                            free waiting time up to 90 minutes after your actual landing time. If the delay is
                            significant, please contact our support via WhatsApp immediately.</p>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button @click="selected !== 3 ? selected = 3 : selected = null"
                        class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none">
                        <span class="font-semibold text-gray-900 dark:text-gray-100">Can I cancel or change my
                            booking?</span>
                        <svg :class="{ 'rotate-180': selected === 3 }"
                            class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="selected === 3" x-collapse class="px-6 pb-4">
                        <p class="text-gray-600 dark:text-gray-400">Yes. Cancellations made at least 24 hours before the
                            service time are eligible for a full refund. Cancellations within 24 hours may be subject to
                            a 50% fee.</p>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button @click="selected !== 4 ? selected = 4 : selected = null"
                        class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none">
                        <span class="font-semibold text-gray-900 dark:text-gray-100">Do you provide child seats?</span>
                        <svg :class="{ 'rotate-180': selected === 4 }"
                            class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="selected === 4" x-collapse class="px-6 pb-4">
                        <p class="text-gray-600 dark:text-gray-400">Yes, child seats are available as an add-on option
                            during the booking process for a small additional fee. Please select it when booking to
                            ensure availability.</p>
                    </div>
                </div>

            </div>

            <div class="mt-12 text-center">
                <p class="text-gray-600 dark:text-gray-400">Can't find what you are looking for?</p>
                <a href="{{ route('contact') }}"
                    class="mt-4 inline-block px-6 py-3 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 transition">Contact
                    Support</a>
            </div>

        </div>
    </div>
</x-layouts.app>
