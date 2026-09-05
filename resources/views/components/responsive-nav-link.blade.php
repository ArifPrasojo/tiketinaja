@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-electric-blue text-start text-base font-medium text-electric-blue bg-blue-50 focus:outline-none transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-on-surface-variant hover:text-electric-blue hover:bg-slate-50 hover:border-stadium-gray focus:outline-none focus:text-electric-blue focus:bg-slate-50 focus:border-stadium-gray transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
