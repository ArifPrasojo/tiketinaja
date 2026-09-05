@props(['size' => 36])

@php $gid = 'taGrad-'.uniqid(); @endphp

<svg viewBox="0 0 64 64" width="{{ $size }}" height="{{ $size }}" xmlns="http://www.w3.org/2000/svg" {{ $attributes }} aria-hidden="true">
    <defs>
        <linearGradient id="{{ $gid }}" x1="8" y1="6" x2="56" y2="58" gradientUnits="userSpaceOnUse">
            <stop stop-color="#0052FF"/>
            <stop offset="1" stop-color="#3D7BFF"/>
        </linearGradient>
    </defs>
    <rect x="2" y="2" width="60" height="60" rx="16" fill="url(#{{ $gid }})"/>
    <path d="M18 20h28v7.2H36.6V44h-9.2V27.2H18V20Z" fill="#fff"/>
    <circle cx="46.5" cy="46.5" r="7.5" fill="#FF5C00"/>
    <circle cx="46.5" cy="46.5" r="3.2" fill="none" stroke="#fff" stroke-width="1.6"/>
</svg>
