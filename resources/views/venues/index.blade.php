<x-guest-public-layout>
    <x-slot name="title">Cari Lapangan Olahraga</x-slot>

    {{-- Hero --}}
    <section class="relative bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-600 py-20 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-64 h-64 rounded-full bg-white blur-3xl"></div>
            <div class="absolute bottom-0 right-10 w-80 h-80 rounded-full bg-violet-300 blur-3xl"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4 leading-tight">
                Booking Lapangan Olahraga<br>
                <span class="text-indigo-200">Mudah & Cepat</span>
            </h1>
            <p class="text-indigo-100 text-lg mb-10 max-w-xl mx-auto">
                Temukan lapangan futsal, badminton, basket, dan olahraga lainnya di kotamu.
            </p>

            {{-- Search bar --}}
            <form method="GET" action="{{ route('venues.index') }}"
                  class="max-w-3xl mx-auto bg-white rounded-2xl p-4 shadow-2xl">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama venue..."
                               class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                    </div>
                    <select name="city"
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none text-gray-700">
                        <option value="">Semua Kota</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                    <div class="flex gap-2">
                        <select name="sport"
                                class="flex-1 px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none text-gray-700">
                            <option value="">Semua Olahraga</option>
                            @foreach($sports as $sport)
                                <option value="{{ $sport->id }}" {{ request('sport') == $sport->id ? 'selected' : '' }}>{{ $sport->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition whitespace-nowrap">
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
                <span class="text-gray-500">Filter aktif:</span>
                @if(request('search'))
                    <span class="inline-flex items-center gap-1 bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-medium">
                        "{{ request('search') }}"
                        <a href="{{ request()->fullUrlWithoutQuery(['search']) }}" class="ml-1 text-indigo-400 hover:text-indigo-700">×</a>
                    </span>
                @endif
                @if(request('city'))
                    <span class="inline-flex items-center gap-1 bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-medium">
                        {{ request('city') }}
                        <a href="{{ request()->fullUrlWithoutQuery(['city']) }}" class="ml-1 text-indigo-400 hover:text-indigo-700">×</a>
                    </span>
                @endif
                @if(request('sport'))
                    @php $activeSport = $sports->firstWhere('id', request('sport')); @endphp
                    <span class="inline-flex items-center gap-1 bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-medium">
                        {{ $activeSport?->name }}
                        <a href="{{ request()->fullUrlWithoutQuery(['sport']) }}" class="ml-1 text-indigo-400 hover:text-indigo-700">×</a>
                    </span>
                @endif
                <a href="{{ route('venues.index') }}" class="text-gray-400 hover:text-gray-600 underline">Hapus semua</a>
            </div>
        </div>
    @endif

    {{-- Results --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($venues->isEmpty())
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Venue tidak ditemukan</h3>
                <p class="text-gray-500 mb-4">Coba ubah filter pencarian Anda.</p>
                <a href="{{ route('venues.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Lihat semua venue</a>
            </div>
        @else
            <div class="flex items-center justify-between mb-5">
                <p class="text-sm text-gray-600">
                    Menampilkan <span class="font-semibold text-gray-900">{{ $venues->total() }}</span> venue
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($venues as $venue)
                    <a href="{{ route('venues.show', $venue) }}"
                       class="group bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-md hover:border-indigo-100 transition-all duration-200">
                        {{-- Image --}}
                        <div class="relative h-48 bg-gradient-to-br from-indigo-100 to-violet-100 overflow-hidden">
                            @if($venue->image)
                                <img src="{{ asset('storage/' . $venue->image) }}"
                                     alt="{{ $venue->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="text-5xl font-bold text-indigo-200">{{ substr($venue->name, 0, 2) }}</span>
                                </div>
                            @endif
                            {{-- Rating badge --}}
                            @if($venue->reviews_avg_rating)
                                <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm rounded-lg px-2 py-1 flex items-center gap-1 shadow-sm">
                                    <span class="text-yellow-400 text-sm">★</span>
                                    <span class="text-xs font-bold text-gray-800">{{ number_format($venue->reviews_avg_rating, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 text-base group-hover:text-indigo-600 transition-colors">{{ $venue->name }}</h3>
                            <div class="flex items-center gap-1 mt-1 text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span class="text-xs">{{ $venue->city }}</span>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                    <span class="text-xs text-gray-600">{{ $venue->courts_count }} lapangan tersedia</span>
                                </div>
                                <span class="text-xs font-medium text-indigo-600 group-hover:text-indigo-800">Lihat →</span>
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
        <section class="bg-white border-t border-gray-100 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6 text-center">Jelajah Berdasarkan Olahraga</h2>
                <div class="flex flex-wrap justify-center gap-3">
                    @foreach($sports as $sport)
                        <a href="{{ route('venues.index', ['sport' => $sport->id]) }}"
                           class="flex items-center gap-2 px-5 py-2.5 border border-gray-200 rounded-full text-sm font-medium text-gray-700 hover:border-indigo-400 hover:text-indigo-700 hover:bg-indigo-50 transition {{ request('sport') == $sport->id ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : '' }}">
                            @if($sport->icon)<span>{{ $sport->icon }}</span>@endif
                            {{ $sport->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</x-guest-public-layout>
