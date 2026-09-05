<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($pageTitle) ? $pageTitle . ' — ' : '' }}{{ config('app.name', 'TiketinAja') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Serif:ital,wght@0,400;0,700;1,700;1,800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-surface-light text-pitch-black antialiased">

<div class="min-h-screen flex flex-col">
    <header class="w-full top-0 sticky bg-white shadow-sm shadow-electric-blue/10 z-50" x-data="{ mobileOpen: false }">
        <div class="flex justify-between items-center w-full px-6 max-w-7xl mx-auto h-20">
            <div class="flex items-center gap-8">
                <x-logo-wordmark :href="route('dashboard')" :size="36" class="transition-all duration-200 active:scale-95"/>
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('dashboard') }}"
                       class="font-semibold text-sm px-3 py-1.5 rounded-lg transition
                              {{ request()->routeIs('dashboard') ? 'text-electric-blue border-b-2 border-electric-blue' : 'text-on-surface-variant hover:text-electric-blue hover:bg-slate-50' }}">
                        Dashboard
                    </a>
                    @if(auth()->user()->isCustomer())
                        <a href="{{ route('venues.index') }}"
                           class="font-semibold text-sm px-3 py-1.5 rounded-lg transition
                                  {{ request()->routeIs('venues.*','courts.*') ? 'text-electric-blue border-b-2 border-electric-blue' : 'text-on-surface-variant hover:text-electric-blue hover:bg-slate-50' }}">
                            Venue
                        </a>
                        <a href="{{ route('bookings.index') }}"
                           class="font-semibold text-sm px-3 py-1.5 rounded-lg transition
                                  {{ request()->routeIs('bookings.*') ? 'text-electric-blue border-b-2 border-electric-blue' : 'text-on-surface-variant hover:text-electric-blue hover:bg-slate-50' }}">
                            Booking Saya
                        </a>
                    @endif
                    @if(auth()->user()->isManager() || auth()->user()->isAdmin())
                        <a href="{{ route('manage.venues.index') }}"
                           class="font-semibold text-sm px-3 py-1.5 rounded-lg transition
                                  {{ request()->routeIs('manage.venues.*') ? 'text-electric-blue border-b-2 border-electric-blue' : 'text-on-surface-variant hover:text-electric-blue hover:bg-slate-50' }}">
                            Venue
                        </a>
                        <a href="{{ route('manage.bookings.index') }}"
                           class="font-semibold text-sm px-3 py-1.5 rounded-lg transition
                                  {{ request()->routeIs('manage.bookings.*') ? 'text-electric-blue border-b-2 border-electric-blue' : 'text-on-surface-variant hover:text-electric-blue hover:bg-slate-50' }}">
                            Transaksi
                        </a>
                        <a href="{{ route('manage.promos.index') }}"
                           class="font-semibold text-sm px-3 py-1.5 rounded-lg transition
                                  {{ request()->routeIs('manage.promos.*') ? 'text-electric-blue border-b-2 border-electric-blue' : 'text-on-surface-variant hover:text-electric-blue hover:bg-slate-50' }}">
                            Promo
                        </a>
                        <a href="{{ route('manage.checkin.index') }}"
                           class="font-semibold text-sm px-3 py-1.5 rounded-lg transition
                                  {{ request()->routeIs('manage.checkin.*') ? 'text-electric-blue border-b-2 border-electric-blue' : 'text-on-surface-variant hover:text-electric-blue hover:bg-slate-50' }}">
                            Check-in
                        </a>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}"
                           class="font-semibold text-sm px-3 py-1.5 rounded-lg transition
                                  {{ request()->routeIs('admin.users.*') ? 'text-electric-blue border-b-2 border-electric-blue' : 'text-on-surface-variant hover:text-electric-blue hover:bg-slate-50' }}">
                            Users
                        </a>
                    @endif
                </nav>
            </div>

            <div class="flex items-center gap-3">
                @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                <a href="{{ route('notifications.index') }}"
                   class="relative p-2 rounded-lg text-on-surface-variant hover:bg-slate-50 hover:text-electric-blue transition">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                    @if($unread > 0)
                        <span class="absolute top-1 right-1 w-4 h-4 bg-athletic-orange text-white text-xs rounded-full flex items-center justify-center font-bold leading-none">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                    @endif
                </a>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2.5 pl-2 pr-3 py-1.5 rounded-lg border-2 border-stadium-gray bg-white hover:border-electric-blue transition">
                        <div class="w-7 h-7 rounded-lg bg-electric-blue text-white flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-medium text-pitch-black max-w-[120px] truncate">
                            {{ auth()->user()->name }}
                        </span>
                        <span class="material-symbols-outlined text-[16px] text-on-surface-variant">expand_more</span>
                    </button>

                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-stadium-gray py-1.5 z-50">
                        <div class="px-4 py-2.5 border-b border-stadium-gray">
                            <p class="text-sm font-bold text-pitch-black truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-on-surface-variant truncate">{{ auth()->user()->email }}</p>
                            <span class="inline-block mt-1.5 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md
                                {{ auth()->user()->isAdmin() ? 'bg-purple-100 text-purple-700' : (auth()->user()->isManager() ? 'bg-blue-100 text-electric-blue' : 'bg-green-100 text-field-green') }}">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-pitch-black hover:bg-slate-50 transition">
                            Profil Saya
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>

                <button @click="mobileOpen = !mobileOpen"
                        class="md:hidden p-2 rounded-lg text-on-surface-variant hover:bg-slate-50">
                    <span class="material-symbols-outlined text-[24px]" x-show="!mobileOpen">menu</span>
                    <span class="material-symbols-outlined text-[24px]" x-show="mobileOpen" x-cloak>close</span>
                </button>
            </div>
        </div>

        <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-stadium-gray bg-white px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('dashboard') ? 'text-electric-blue font-bold' : 'text-on-surface-variant' }}">Dashboard</a>
            @if(auth()->user()->isCustomer())
                <a href="{{ route('venues.index') }}" class="block px-3 py-2 rounded-lg text-sm text-on-surface-variant">Venue</a>
                <a href="{{ route('bookings.index') }}" class="block px-3 py-2 rounded-lg text-sm text-on-surface-variant">Booking Saya</a>
            @endif
            @if(!auth()->user()->isCustomer())
                <a href="{{ route('manage.venues.index') }}" class="block px-3 py-2 rounded-lg text-sm text-on-surface-variant">Venue</a>
                <a href="{{ route('manage.bookings.index') }}" class="block px-3 py-2 rounded-lg text-sm text-on-surface-variant">Transaksi</a>
                <a href="{{ route('manage.promos.index') }}" class="block px-3 py-2 rounded-lg text-sm text-on-surface-variant">Promo</a>
                <a href="{{ route('manage.checkin.index') }}" class="block px-3 py-2 rounded-lg text-sm text-on-surface-variant">Check-in</a>
            @endif
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-lg text-sm text-on-surface-variant">Users</a>
            @endif
        </div>
    </header>

    @if(session('success') || session('error') || $errors->any())
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4">
            @if(session('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
                    <span class="material-symbols-outlined text-[20px] text-field-green shrink-0">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                    <span class="material-symbols-outlined text-[20px] text-red-500 shrink-0">cancel</span>
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    @isset($header)
        <div class="bg-white border-b border-stadium-gray">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                {{ $header }}
            </div>
        </div>
    @endisset

    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="bg-pitch-black mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <x-logo-wordmark :size="28" :dark="true"/>
            <p class="text-xs text-slate-400">&copy; {{ date('Y') }} TiketinAja — Elevate Your Game</p>
        </div>
    </footer>
</div>

</body>
</html>
