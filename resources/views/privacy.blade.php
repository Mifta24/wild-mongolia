<x-layouts.app>
    <section class="bg-white dark:bg-gray-900 min-h-screen py-12 border-b border-gray-100 dark:border-gray-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Privacy Policy</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Last updated: {{ now(config('app.timezone'))->format('d M Y') }}</p>

            <div class="space-y-6 text-gray-700 dark:text-gray-300 leading-relaxed">
                <p>
                    ThaiTravel is committed to protecting your personal data. This page explains what information we collect,
                    how we use it, and how we protect it.
                </p>

                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Information We Collect</h2>
                    <p>We may collect your name, email address, phone number, booking details, and payment-related metadata required to process your bookings.</p>
                </div>

                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">How We Use Your Data</h2>
                    <p>Your data is used to provide services, process payments, confirm bookings, improve support quality, and fulfill legal obligations.</p>
                </div>

                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Data Security</h2>
                    <p>We apply appropriate technical and organizational safeguards to protect your data from unauthorized access, disclosure, or misuse.</p>
                </div>

                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Contact</h2>
                    <p>If you have any privacy questions, contact us via the <a href="{{ route('contact') }}" class="text-primary hover:underline">contact page</a>.</p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
