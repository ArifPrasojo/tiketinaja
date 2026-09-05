<x-guest-public-layout>
    <x-slot name="title">Cari Lapangan Olahraga</x-slot>

    {{-- Hero --}}
    <section class="relative bg-surface-light py-20 overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-10 left-10 w-64 h-64 rounded-full bg-blue-50 blur-3xl"></div>
            <div class="absolute bottom-0 right-10 w-80 h-80 rounded-full bg-teal-500/15 blur-3xl"></div>
            <div class="absolute inset-0" style="background: radial-gradient(ellipse at top, rgba(16,185,129,0.08) 0%, transparent 55%)"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl font-bold text-pitch-black mb-4 leading-tight">
                Booking Lapangan Olahraga<br>
                <span class="text-electric-blue">Mudah & Cepat</span>
            </h1>
            <p class="text-on-surface-variant text-lg mb-10 max-w-xl mx-auto">
                Temukan lapangan futsal, badminton, basket, dan olahraga lainnya di kotamu.
            </p>

            {{-- Search bar --}}
            <form method="GET" action="{{ route('venues.index') }}"
                  class="max-w-3xl mx-auto bg-white border border-stadium-gray rounded-xl p-4 shadow-2xl shadow-black/40">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] text-on-surface-variant">search</span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama venue..."
                               class="w-full pl-9 pr-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                    </div>
                    <select name="city"
                            class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none">
                        <option value="">Semua Kota</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                    <div class="flex gap-2">
                        <select name="sport"
                                class="flex-1 px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none">
                            <option value="">Semua Olahraga</option>
                            @foreach($sports as $sport)
                                <option value="{{ $sport->id }}" {{ request('sport') == $sport->id ? 'selected' : '' }}>{{ $sport->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="px-5 py-2.5 bg-electric-blue text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition whitespace-nowrap shadow-lg shadow-blue-500/20">
                            Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- Active filter chips --}}
    @if(request('search') || request('city') || request('sport'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5">
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <span class="text-on-surface-variant">Filter aktif:</span>
                @if(request('search'))
                    <span class="inline-flex items-center gap-1 bg-blue-50 text-electric-blue border border-electric-blue/20 px-3 py-1 rounded-full font-medium">
                        "{{ request('search') }}"
                        <a href="{{ request()->fullUrlWithoutQuery(['search']) }}" class="ml-1 text-electric-blue hover:text-electric-blue">×</a>
                    </span>
                @endif
                @if(request('city'))
                    <span class="inline-flex items-center gap-1 bg-blue-50 text-electric-blue border border-electric-blue/20 px-3 py-1 rounded-full font-medium">
                        {{ request('city') }}
                        <a href="{{ request()->fullUrlWithoutQuery(['city']) }}" class="ml-1 text-electric-blue hover:text-electric-blue">×</a>
                    </span>
                @endif
                @if(request('sport'))
                    @php $activeSport = $sports->firstWhere('id', request('sport')); @endphp
                    <span class="inline-flex items-center gap-1 bg-blue-50 text-electric-blue border border-electric-blue/20 px-3 py-1 rounded-full font-medium">
                        {{ $activeSport?->name }}
                        <a href="{{ request()->fullUrlWithoutQuery(['sport']) }}" class="ml-1 text-electric-blue hover:text-electric-blue">×</a>
                    </span>
                @endif
                <a href="{{ route('venues.index') }}" class="text-on-surface-variant hover:text-on-surface-variant underline">Hapus semua</a>
            </div>
        </div>
    @endif

    {{-- Results --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($venues->isEmpty())
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[40px] text-on-surface-variant">search</span>
                </div>
                <h3 class="text-lg font-semibold text-pitch-black mb-1">Venue tidak ditemukan</h3>
                <p class="text-on-surface-variant mb-4">Coba ubah filter pencarian Anda.</p>
                <a href="{{ route('venues.index') }}" class="text-electric-blue hover:text-electric-blue font-medium">Lihat semua venue</a>
            </div>
        @else
            <div class="flex items-center justify-between mb-5">
                <p class="text-sm text-on-surface-variant">
                    Menampilkan <span class="font-semibold text-pitch-black">{{ $venues->total() }}</span> venue
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($venues as $venue)
                    <a href="{{ route('venues.show', $venue) }}"
                       class="group bg-white rounded-xl overflow-hidden sporty-shadow border border-stadium-gray hover:shadow-md hover:border-electric-blue/30 transition-all duration-200">
                        {{-- Image --}}
                        <div class="relative h-48 bg-gradient-to-br from-slate-100 to-slate-200 overflow-hidden">
                            @if($venue->image)
                                <img src="{{ asset('storage/' . $venue->image) }}"
                                     alt="{{ $venue->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="text-5xl font-bold text-on-surface-variant">{{ substr($venue->name, 0, 2) }}</span>
                                </div>
                            @endif
                            {{-- Rating badge --}}
                            @if($venue->reviews_avg_rating)
                                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm rounded-lg px-2 py-1 flex items-center gap-1 shadow-sm border border-stadium-gray">
                                    <span class="text-yellow-400 text-sm">★</span>
                                    <span class="text-xs font-bold text-pitch-black">{{ number_format($venue->reviews_avg_rating, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="p-4">
                            <h3 class="font-semibold text-pitch-black text-base group-hover:text-electric-blue transition-colors">{{ $venue->name }}</h3>
                            <div class="flex items-center gap-1 mt-1 text-on-surface-variant">
                                <span class="material-symbols-outlined text-[14px]">location_on</span>
                                <span class="text-xs">{{ $venue->city }}</span>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-1.5 h-1.5 rounded-full bg-electric-blue"></div>
                                    <span class="text-xs text-on-surface-variant">{{ $venue->courts_count }} lapangan tersedia</span>
                                </div>
                                <span class="text-xs font-medium text-electric-blue group-hover:text-electric-blue">Lihat →</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($venues->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $venues->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </section>

    {{-- Sport categories --}}
    @if($sports->count())
        <section class="bg-white border-t border-stadium-gray py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-xl font-bold text-pitch-black mb-6 text-center">Jelajah Berdasarkan Olahraga</h2>
                <div class="flex flex-wrap justify-center gap-3">
                    @foreach($sports as $sport)
                        <a href="{{ route('venues.index', ['sport' => $sport->id]) }}"
                           class="flex items-center gap-2 px-5 py-2.5 border border-stadium-gray rounded-full text-sm font-medium text-on-surface-variant hover:border-electric-blue/50 hover:text-electric-blue hover:bg-blue-700/10 transition {{ request('sport') == $sport->id ? 'border-electric-blue bg-blue-50 text-electric-blue' : '' }}">
                            @if($sport->icon)<span>{{ $sport->icon }}</span>@endif
                            {{ $sport->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</x-guest-public-layout>