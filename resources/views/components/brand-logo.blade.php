@props(['light' => false])
<img src="{{ asset('images/logo-wild.png') }}" alt="Wild Mongolia - Mongolia Travel"
    {{ $attributes->class(['h-20 w-auto', 'brightness-0 dark:brightness-100' => !$light]) }}>
