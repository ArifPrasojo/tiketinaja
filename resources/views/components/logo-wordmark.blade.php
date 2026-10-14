@props(['href' => null, 'size' => 36, 'dark' => false, 'badge' => null])

@php
    $link = $href ?? route('home');
@endphp

<a href="{{ $link }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-3 group shrink-0']) }}>
    <x-logo :size="$size" class="shrink-0 text-electric-blue transition-transform group-hover:scale-105"/>
    <div class="leading-none">
        <span class="block font-sans font-black tracking-tight text-[1.15em] {{ $dark ? 'text-white' : 'text-pitch-black' }}">
            Tiketin<span class="text-electric-blue">Aja</span>
        </span>
        @if($badge)
            <span class="hidden sm:inline-flex items-center gap-1 text-[9px] font-black uppercase tracking-wider {{ $dark ? 'text-slate-400' : 'text-on-surface-variant' }} mt-1">
                <span class="w-1.5 h-1.5 rounded-full bg-field-green"></span>
                {{ $badge }}
            </span>
        @endif
    </div>
</a>
