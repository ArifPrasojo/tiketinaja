@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-electric-blue text-sm font-medium leading-5 text-electric-blue focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-on-surface-variant hover:text-electric-blue hover:border-stadium-gray focus:outline-none focus:text-electric-blue focus:border-stadium-gray transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
