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
    @php
        $userRoleBadge = auth()->user()->isAdmin() ? 'Admin Central' : (auth()->user()->isManager() ? 'Venue Manager' : 'Member');
        $navActive = 'inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold bg-blue-50 text-electric-blue border border-blue-200/60 shadow-sm transition-all';
        $navInactive = 'inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-semibold text-on-surface-variant hover:text-pitch-black hover:bg-slate-50 transition-all';
    @endphp

    <header class="w-full top-0 sticky bg-white/95 backdrop-blur-md border-b border-stadium-gray/80 z-50 shadow-sm shadow-slate-100" x-data="{ mobileOpen: false }">
        <div class="flex justify-between items-center w-full px-3.5 sm:px-6 lg:px-8 max-w-7xl mx-auto h-16 sm:h-20">
            <div class="flex items-center gap-4 sm:gap-8 min-w-0">
                <x-logo-wordmark :href="route('dashboard')" :size="32" :badge="$userRoleBadge" class="transition-all duration-200 active:scale-95"/>
                
                <nav class="hidden md:flex items-center gap-1.5">
                    <a href="{{ route('dashboard') }}"
                       class="{{ request()->routeIs('dashboard') ? $navActive : $navInactive }}">
                        @if(request()->routeIs('dashboard'))
                            <span class="w-2 h-2 rounded-full bg-electric-blue"></span>
                        @endif
                        Dashboard
                    </a>
                    
                    @if(auth()->user()->isCustomer())
                        <a href="{{ route('venues.index') }}"
                           class="{{ request()->routeIs('venues.*','courts.*') ? $navActive : $navInactive }}">
                            @if(request()->routeIs('venues.*','courts.*'))
                                <span class="w-2 h-2 rounded-full bg-electric-blue"></span>
                            @endif
                            Venue
                        </a>
                        <a href="{{ route('bookings.index') }}"
                           class="{{ request()->routeIs('bookings.*') ? $navActive : $navInactive }}">
                            @if(request()->routeIs('bookings.*'))
                                <span class="w-2 h-2 rounded-full bg-electric-blue"></span>
                            @endif
                            Booking Saya
                        </a>
                    @endif

                    @if(auth()->user()->isManager() || auth()->user()->isAdmin())
                        <a href="{{ route('manage.venues.index') }}"
                           class="{{ request()->routeIs('manage.venues.*') ? $navActive : $navInactive }}">
                            @if(request()->routeIs('manage.venues.*'))
                                <span class="w-2 h-2 rounded-full bg-electric-blue"></span>
                            @endif
                            Venue
                        </a>
                        <a href="{{ route('manage.bookings.index') }}"
                           class="{{ request()->routeIs('manage.bookings.*') ? $navActive : $navInactive }}">
                            @if(request()->routeIs('manage.bookings.*'))
                                <span class="w-2 h-2 rounded-full bg-electric-blue"></span>
                            @endif
                            Transaksi
                        </a>
                        <a href="{{ route('manage.promos.index') }}"
                           class="{{ request()->routeIs('manage.promos.*') ? $navActive : $navInactive }}">
                            @if(request()->routeIs('manage.promos.*'))
                                <span class="w-2 h-2 rounded-full bg-electric-blue"></span>
                            @endif
                            Promo
                        </a>
                        <a href="{{ route('manage.checkin.index') }}"
                           class="{{ request()->routeIs('manage.checkin.*') ? $navActive : $navInactive }}">
                            @if(request()->routeIs('manage.checkin.*'))
                                <span class="w-2 h-2 rounded-full bg-electric-blue"></span>
                            @endif
                            Check-in
                        </a>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}"
                           class="{{ request()->routeIs('admin.users.*') ? $navActive : $navInactive }}">
                            @if(request()->routeIs('admin.users.*'))
                                <span class="w-2 h-2 rounded-full bg-electric-blue"></span>
                            @endif
                            Users
                        </a>
                    @endif
                </nav>
            </div>

            <div class="flex items-center gap-3">
                {{-- Notification bell --}}
                @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                <a href="{{ route('notifications.index') }}"
                   title="Notifikasi"
                   class="relative w-10 h-10 rounded-full flex items-center justify-center text-on-surface-variant hover:bg-slate-100 hover:text-electric-blue transition-colors">
                    <span class="material-symbols-outlined text-[22px]">notifications</span>
                    @if($unread > 0)
                        <span class="absolute top-1.5 right-1.5 min-w-[16px] h-4 px-1 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold leading-none ring-2 ring-white">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                    @endif
                </a>

                {{-- User Profile Pill Dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2.5 p-1 pl-1.5 pr-3 rounded-full hover:bg-slate-50 transition-all border border-stadium-gray bg-white shadow-sm hover:border-electric-blue">
                        <div class="w-8 h-8 rounded-full bg-electric-blue text-white flex items-center justify-center font-bold text-xs shadow-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-left leading-tight">
                            <p class="text-xs font-bold text-pitch-black truncate max-w-[120px]">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] font-semibold text-electric-blue truncate">
                                {{ auth()->user()->isAdmin() ? 'Super Admin' : (auth()->user()->isManager() ? 'Venue Manager' : 'Member') }}
                            </p>
                        </div>
                        <span class="material-symbols-outlined text-[18px] text-on-surface-variant transition-transform" :class="open ? 'rotate-180' : ''">keyboard_arrow_down</span>
                    </button>

                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-60 bg-white rounded-2xl shadow-xl border border-stadium-gray p-1.5 z-50">
                        <div class="px-3.5 py-3 border-b border-stadium-gray/70">
                            <p class="text-sm font-bold text-pitch-black truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-on-surface-variant truncate">{{ auth()->user()->email }}</p>
                            <div class="mt-2">
                                <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md
                                    {{ auth()->user()->isAdmin() ? 'bg-purple-100 text-purple-700' : (auth()->user()->isManager() ? 'bg-blue-100 text-electric-blue' : 'bg-green-100 text-field-green') }}">
                                    {{ auth()->user()->isAdmin() ? 'Super Admin' : (auth()->user()->isManager() ? 'Venue Manager' : 'Customer') }}
                                </span>
                            </div>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-pitch-black hover:bg-slate-50 rounded-xl transition">
                                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">person</span>
                                Profil Saya
                            </a>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-pitch-black hover:bg-slate-50 rounded-xl transition">
                                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">dashboard</span>
                                Dashboard
                            </a>
                        </div>
                        <div class="pt-1 border-t border-stadium-gray/70">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2.5 w-full px-3.5 py-2 text-xs font-bold text-red-600 hover:bg-red-50 rounded-xl transition">
                                    <span class="material-symbols-outlined text-[18px] text-red-500">logout</span>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Hamburger Menu for Mobile --}}
                <button @click="mobileOpen = !mobileOpen"
                        class="md:hidden w-10 h-10 rounded-full flex items-center justify-center text-on-surface-variant hover:bg-slate-100 hover:text-pitch-black transition">
                    <span class="material-symbols-outlined text-[24px]" x-show="!mobileOpen">menu</span>
                    <span class="material-symbols-outlined text-[24px]" x-show="mobileOpen" x-cloak>close</span>
                </button>
            </div>
        </div>

        {{-- Mobile Drawer Navigation --}}
        <div x-show="mobileOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden border-t border-stadium-gray bg-white px-4 py-4 space-y-2 shadow-xl max-h-[calc(100vh-4rem)] overflow-y-auto">
            
            {{-- User Bio Quick Card on Mobile --}}
            <div class="p-3 bg-surface-light border border-stadium-gray rounded-2xl flex items-center justify-between mb-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-electric-blue text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 leading-tight">
                        <p class="text-xs font-bold text-pitch-black truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-on-surface-variant truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <span class="inline-block px-2 py-0.5 text-[9px] font-bold uppercase rounded-md bg-blue-50 text-electric-blue shrink-0">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>

            <a href="{{ route('dashboard') }}"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-electric-blue' : 'text-on-surface-variant' }}">
                <span>Dashboard</span>
                @if(request()->routeIs('dashboard'))
                    <span class="w-2 h-2 rounded-full bg-electric-blue"></span>
                @endif
            </a>
            @if(auth()->user()->isCustomer())
                <a href="{{ route('venues.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('venues.*','courts.*') ? 'bg-blue-50 text-electric-blue' : 'text-on-surface-variant' }}">
                    <span>Cari Venue</span>
                </a>
                <a href="{{ route('bookings.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('bookings.*') ? 'bg-blue-50 text-electric-blue' : 'text-on-surface-variant' }}">
                    <span>Booking Saya</span>
                </a>
            @endif
            @if(auth()->user()->isManager() || auth()->user()->isAdmin())
                <a href="{{ route('manage.venues.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('manage.venues.*') ? 'bg-blue-50 text-electric-blue' : 'text-on-surface-variant' }}">
                    <span>Kelola Venue</span>
                </a>
                <a href="{{ route('manage.bookings.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('manage.bookings.*') ? 'bg-blue-50 text-electric-blue' : 'text-on-surface-variant' }}">
                    <span>Transaksi</span>
                </a>
                <a href="{{ route('manage.promos.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('manage.promos.*') ? 'bg-blue-50 text-electric-blue' : 'text-on-surface-variant' }}">
                    <span>Promo</span>
                </a>
                <a href="{{ route('manage.checkin.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('manage.checkin.*') ? 'bg-blue-50 text-electric-blue' : 'text-on-surface-variant' }}">
                    <span>Scanner Check-in</span>
                </a>
            @endif
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-electric-blue' : 'text-on-surface-variant' }}">
                    <span>Kelola Users</span>
                </a>
            @endif

            <div class="pt-3 border-t border-stadium-gray flex flex-col gap-1.5">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-pitch-black hover:bg-slate-50 transition">
                    <span class="material-symbols-outlined text-[18px] text-on-surface-variant">person</span>
                    Profil Akun Saya
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-2 w-full px-3.5 py-2 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50 transition">
                        <span class="material-symbols-outlined text-[18px] text-red-500">logout</span>
                        Keluar dari Akun
                    </button>
                </form>
            </div>
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
