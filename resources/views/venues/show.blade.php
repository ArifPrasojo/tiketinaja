<x-guest-public-layout>
    <x-slot name="title">{{ $venue->name }} - TiketinAja</x-slot>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-4">
            <a href="{{ route('venues.index') }}" class="inline-flex items-center gap-1.5 text-sm text-electric-blue hover:text-electric-blue">
                <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                Kembali ke daftar venue
            </a>
        </div>

        {{-- Venue Header --}}
        <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow overflow-hidden mb-6">
            @if($venue->image)
                <img src="{{ asset('storage/' . $venue->image) }}" alt="{{ $venue->name }}" class="w-full h-64 object-cover">
            @else
                <div class="w-full h-48 bg-gradient-to-r from-blue-100 to-blue-200 flex items-center justify-center">
                    <span class="text-pitch-black text-4xl font-bold">{{ substr($venue->name, 0, 2) }}</span>
                </div>
            @endif
            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-pitch-black">{{ $venue->name }}</h1>
                        <p class="text-on-surface-variant mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">location_on</span>
                            {{ $venue->address }}, {{ $venue->city }}
                        </p>
                        @if($venue->phone)
                            <p class="text-sm text-on-surface-variant mt-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">call</span>
                                {{ $venue->phone }}
                            </p>
                        @endif
                    </div>
                    {{-- Rating badge --}}
                    @php $avgRating = $venue->reviews()->avg('rating'); $reviewCount = $venue->reviews()->count(); @endphp
                    @if($reviewCount > 0)
                        <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-xl px-4 py-2 self-start shadow-sm">
                            <span class="text-amber-500 text-xl font-bold">★</span>
                            <div>
                                <p class="text-base font-bold text-pitch-black">{{ number_format($avgRating, 1) }}</p>
                                <p class="text-[11px] text-on-surface-variant font-medium">{{ $reviewCount }} ulasan</p>
                            </div>
                        </div>
                    @endif
                </div>
                @if($venue->description)
                    <p class="text-on-surface-variant mt-4 text-sm leading-relaxed">{{ $venue->description }}</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-2 space-y-5 order-2 lg:order-1">
                {{-- Courts --}}
                <div>
                    <h2 class="text-lg font-semibold text-pitch-black mb-4">Lapangan Tersedia ({{ $venue->courts->count() }})</h2>
                    @if($venue->courts->isEmpty())
                        <div class="bg-white border border-stadium-gray rounded-xl p-8 text-center text-on-surface-variant">Belum ada lapangan tersedia.</div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($venue->courts as $court)
                                <div class="bg-white border border-stadium-gray rounded-xl overflow-hidden">
                                    @if($court->image)
                                        <img src="{{ asset('storage/' . $court->image) }}" class="w-full h-32 object-cover" alt="">
                                    @endif
                                    <div class="p-4">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h3 class="font-semibold text-pitch-black">{{ $court->name }}</h3>
                                                <p class="text-xs text-on-surface-variant mt-0.5">{{ $court->sport?->name }}</p>
                                            </div>
                                            <span class="text-electric-blue font-bold text-sm whitespace-nowrap">Rp {{ number_format($court->price_per_hour, 0, ',', '.') }}/jam</span>
                                        </div>
                                        @if($court->facilities)
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                @foreach($court->facilities as $f)
                                                    <span class="bg-slate-100 text-on-surface-variant text-xs px-2 py-0.5 rounded">{{ $f }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        <a href="{{ route('courts.show', $court) }}" class="mt-3 block text-center bg-electric-blue text-white text-sm py-2 rounded-xl hover:bg-blue-700 font-medium shadow-lg shadow-blue-500/20">
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
                    <div class="bg-white border border-stadium-gray rounded-xl p-6">
                        <h2 class="text-lg font-semibold text-pitch-black mb-4">Ulasan Pelanggan</h2>
                        <div class="space-y-4">
                            @foreach($reviews as $review)
                                <div class="border-b border-stadium-gray pb-4 last:border-0 last:pb-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="font-medium text-sm text-pitch-black">{{ $review->user?->name }}</p>
                                        <span class="text-xs text-on-surface-variant">{{ $review->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-0.5 mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="{{ $i <= $review->rating ? 'text-amber-400' : 'text-on-surface-variant' }} text-sm">★</span>
                                        @endfor
                                    </div>
                                    @if($review->comment)
                                        <p class="text-sm text-on-surface-variant">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar: Operating Hours + Stats — tampil di atas di mobile --}}
            <div class="space-y-4 order-1 lg:order-2">
                @if($venue->operatingHours && $venue->operatingHours->count())
                    @php
                        $dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                        $todayDow = now()->dayOfWeek;
                    @endphp
                    <div class="bg-white border border-stadium-gray rounded-xl p-5">
                        <h3 class="text-sm font-semibold text-pitch-black mb-3">Jam Operasional</h3>
                        <div class="space-y-1.5 text-sm">
                            @foreach($venue->operatingHours->sortBy('day_of_week') as $h)
                                <div class="flex justify-between {{ $h->day_of_week === $todayDow ? 'font-semibold text-electric-blue' : 'text-on-surface-variant' }}">
                                    <span>{{ $dayNames[$h->day_of_week] }}</span>
                                    <span>
                                        @if($h->is_closed)
                                            <span class="text-red-400">Tutup</span>
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
                <div class="bg-blue-50 border border-electric-blue/20 rounded-2xl p-5">
                    <h3 class="text-sm font-semibold text-pitch-black mb-3">Statistik</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-on-surface-variant">
                            <span>Jumlah Lapangan</span>
                            <span class="font-semibold text-pitch-black">{{ $venue->courts->count() }}</span>
                        </div>
                        <div class="flex justify-between text-on-surface-variant">
                            <span>Total Ulasan</span>
                            <span class="font-semibold text-pitch-black">{{ $reviewCount }}</span>
                        </div>
                        @if($reviewCount > 0)
                            <div class="flex justify-between text-on-surface-variant">
                                <span>Rating</span>
                                <span class="font-semibold text-amber-400">★ {{ number_format($avgRating, 1) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-public-layout>