@props(['href' => null, 'size' => 36, 'dark' => false])

@php
    $link = $href ?? route('home');
@endphp

<a href="{{ $link }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 group shrink-0']) }}>
    <x-logo :size="$size" class="shrink-0"/>
    <span class="leading-none">
        <span class="block font-display italic font-extrabold tracking-tighter text-[1.15em] {{ $dark ? 'text-white' : 'text-pitch-black' }}">
            Tiketin<span class="text-electric-blue">Aja</span>
        </span>
    </span>
</a>
