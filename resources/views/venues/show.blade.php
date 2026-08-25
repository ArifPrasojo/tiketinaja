<x-guest-public-layout>
    <x-slot name="title">{{ $venue->name }} - TiketinAja</x-slot>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-4">
            <a href="{{ route('venues.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">&larr; Kembali ke daftar venue</a>
        </div>

        {{-- Venue Header --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            @if($venue->image)
                <img src="{{ asset('storage/' . $venue->image) }}" alt="{{ $venue->name }}" class="w-full h-64 object-cover">
            @else
                <div class="w-full h-48 bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                    <span class="text-white text-4xl font-bold">{{ substr($venue->name, 0, 2) }}</span>
                </div>
            @endif
            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $venue->name }}</h1>
                        <p class="text-gray-500 mt-1 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $venue->address }}, {{ $venue->city }}
                        </p>
                        @if($venue->phone)
                            <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $venue->phone }}
                            </p>
                        @endif
                    </div>
                    {{-- Rating badge --}}
                    @php $avgRating = $venue->reviews()->avg('rating'); $reviewCount = $venue->reviews()->count(); @endphp
                    @if($reviewCount > 0)
                        <div class="flex items-center gap-2 bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2 self-start">
                            <span class="text-yellow-400 text-xl">★</span>
                            <div>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($avgRating, 1) }}</p>
                                <p class="text-xs text-gray-500">{{ $reviewCount }} ulasan</p>
                            </div>
                        </div>
                    @endif
                </div>
                @if($venue->description)
                    <p class="text-gray-700 mt-4 text-sm leading-relaxed">{{ $venue->description }}</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                {{-- Courts --}}
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Lapangan Tersedia ({{ $venue->courts->count() }})</h2>
                    @if($venue->courts->isEmpty())
                        <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500">Belum ada lapangan tersedia.</div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($venue->courts as $court)
                                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                    @if($court->image)
                                        <img src="{{ asset('storage/' . $court->image) }}" class="w-full h-32 object-cover" alt="">
                                    @endif
                                    <div class="p-4">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h3 class="font-semibold text-gray-900">{{ $court->name }}</h3>
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $court->sport?->name }}</p>
                                            </div>
                                            <span class="text-indigo-600 font-bold text-sm whitespace-nowrap">Rp {{ number_format($court->price_per_hour, 0, ',', '.') }}/jam</span>
                                        </div>
                                        @if($court->facilities)
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                @foreach($court->facilities as $f)
                                                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded">{{ $f }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        <a href="{{ route('courts.show', $court) }}" class="mt-3 block text-center bg-indigo-600 text-white text-sm py-2 rounded-md hover:bg-indigo-700 font-medium">
                                            Lihat & Booking
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Reviews --}}
                @php $reviews = $venue->reviews()->with('user:id,name')->latest()->take(5)->get(); @endphp
                @if($reviews->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Ulasan Pelanggan</h2>
                        <div class="space-y-4">
                            @foreach($reviews as $review)
                                <div class="border-b pb-4 last:border-0 last:pb-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="font-medium text-sm text-gray-900">{{ $review->user?->name }}</p>
                                        <span class="text-xs text-gray-400">{{ $review->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-0.5 mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }} text-sm">★</span>
                                        @endfor
                                    </div>
                                    @if($review->comment)
                                        <p class="text-sm text-gray-600">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar: Operating Hours --}}
            <div class="space-y-4">
                @if($venue->operatingHours && $venue->operatingHours->count())
                    @php
                        $dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                        $todayDow = now()->dayOfWeek;
                    @endphp
                    <div class="bg-white rounded-lg shadow-sm p-5">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Jam Operasional</h3>
                        <div class="space-y-1.5 text-sm">
                            @foreach($venue->operatingHours->sortBy('day_of_week') as $h)
                                <div class="flex justify-between {{ $h->day_of_week === $todayDow ? 'font-semibold text-indigo-700' : 'text-gray-600' }}">
                                    <span>{{ $dayNames[$h->day_of_week] }}</span>
                                    <span>
                                        @if($h->is_closed)
                                            <span class="text-red-500">Tutup</span>
                                        @else
                                            {{ substr($h->open_time, 0, 5) }} – {{ substr($h->close_time, 0, 5) }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Stats --}}
                <div class="bg-indigo-50 rounded-lg p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Statistik</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-700">
                            <span>Jumlah Lapangan</span>
                            <span class="font-semibold">{{ $venue->courts->count() }}</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Total Ulasan</span>
                            <span class="font-semibold">{{ $reviewCount }}</span>
                        </div>
                        @if($reviewCount > 0)
                            <div class="flex justify-between text-gray-700">
                                <span>Rating</span>
                                <span class="font-semibold text-yellow-600">★ {{ number_format($avgRating, 1) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-public-layout>
