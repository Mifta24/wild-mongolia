@props(['light' => false])
<img src="{{ asset('images/logo-wild.png') }}" alt="Wild Mongolia - Mongolia Travel"
    {{ $attributes->class([$light ? 'h-20 xl:h-28 w-auto' : 'h-14 w-auto', 'brightness-0 dark:brightness-100' => !$light]) }}>
