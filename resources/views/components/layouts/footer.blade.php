<footer class="bg-gray-900 text-gray-300 py-12 border-t border-gray-800">
    <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
            <a href="{{ route('home') }}" class="text-2xl font-bold text-white mb-4 inline-block">Wild<span class="text-yellow-500">Mongolia</span></a>
            <p class="text-sm text-gray-400">{{ __('site.ft_tag') }}</p>
        </div>
        <div>
            <h4 class="text-white font-bold mb-4">{{ __('site.ft_services') }}</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('cars') }}" class="hover:text-white transition">{{ __('site.ft_airport') }}</a></li>
                <li><a href="{{ route('cars') }}" class="hover:text-white transition">{{ __('site.ft_city') }}</a></li>
                <li><a href="{{ route('tours') }}" class="hover:text-white transition">{{ __('site.ft_day') }}</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-bold mb-4">{{ __('site.ft_support') }}</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('faq') }}" class="hover:text-white transition">{{ __('site.ft_help') }}</a></li>
                <li><a href="{{ route('terms') }}" class="hover:text-white transition">{{ __('site.ft_terms') }}</a></li>
                <li><a href="{{ route('privacy') }}" class="hover:text-white transition">{{ __('site.ft_privacy') }}</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-bold mb-4">{{ __('site.ft_contact') }}</h4>
            <ul class="space-y-2 text-sm">
                <li>{{ __('site.ft_whatsapp') }}: <a href="https://wa.me/97699112345" class="hover:text-white transition" target="_blank" rel="noopener noreferrer">+976 9911 2345</a></li>
                <li>{{ __('site.ft_email') }}: <a href="mailto:support@wildmongolia.com" class="hover:text-white transition">support@wildmongolia.com</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-gray-800 mt-12 pt-8 text-center text-sm text-gray-500">
        &copy; 2026 Wild Mongolia Co., Ltd. {{ __('site.ft_rights') }}
    </div>
</footer>
