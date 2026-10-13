<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-electric-blue">
                        <x-logo :size="36" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>

                    @if(auth()->user()->role === 'customer')
                        <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.*')">
                            Booking Saya
                        </x-nav-link>
                    @endif

                    @if(in_array(auth()->user()->role, ['manager', 'admin']))
                        <x-nav-link :href="route('manage.venues.index')" :active="request()->routeIs('manage.venues.*')">
                            Kelola Venue
                        </x-nav-link>
                        <x-nav-link :href="route('manage.bookings.index')" :active="request()->routeIs('manage.bookings.*')">
                            Kelola Booking
                        </x-nav-link>
                        <x-nav-link :href="route('manage.promos.index')" :active="request()->routeIs('manage.promos.*')">
                            Promo
                        </x-nav-link>
                        <x-nav-link :href="route('manage.checkin.index')" :active="request()->routeIs('manage.checkin.*')">
                            Check-in
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->role === 'admin')
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            Users
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                {{-- Notification Bell --}}
                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                <a href="{{ route('notifications.index') }}" class="relative mr-4 text-on-surface-variant hover:text-on-surface-variant">
                    <span class="material-symbols-outlined text-[24px]">notifications</span>
                    @if($unreadCount > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-pitch-black text-xs rounded-full h-4 w-4 flex items-center justify-center font-bold">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-on-surface-variant bg-white hover:text-on-surface-variant focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <span class="material-symbols-outlined text-[16px]">expand_more</span>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-on-surface-variant hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-on-surface-variant transition duration-150 ease-in-out">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>

            @if(auth()->user()->role === 'customer')
                <x-responsive-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.*')">
                    Booking Saya
                </x-responsive-nav-link>
            @endif

            @if(in_array(auth()->user()->role, ['manager', 'admin']))
                <x-responsive-nav-link :href="route('manage.venues.index')" :active="request()->routeIs('manage.venues.*')">
                    Kelola Venue
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('manage.bookings.index')" :active="request()->routeIs('manage.bookings.*')">
                    Kelola Booking
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    Users
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-pitch-black">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-on-surface-variant">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
