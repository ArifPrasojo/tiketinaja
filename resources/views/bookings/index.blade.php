<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-pitch-black">Booking Saya</h1>
                <p class="text-sm text-on-surface-variant mt-0.5">Pantau status reservasi, tiket QR check-in, dan riwayat pertandingan Anda</p>
            </div>
            <a href="{{ route('venues.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-electric-blue text-white text-sm font-bold rounded-xl hover:bg-blue-700 active:scale-95 transition shadow-sm self-start sm:self-auto">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Booking Lapangan Baru
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Filter Tabs Bar --}}
            <div class="flex flex-wrap gap-2">
                @php
                    $tabs = [
                        ''          => ['label' => 'Semua Reservasi', 'count' => $stats['total'],     'cls' => 'bg-slate-100 text-slate-700',   'activeBg' => 'bg-electric-blue text-white'],
                        'pending'   => ['label' => 'Menunggu Bayar',  'count' => $stats['pending'],   'cls' => 'bg-amber-50 text-amber-700',    'activeBg' => 'bg-amber-500 text-white'],
                        'confirmed' => ['label' => 'Terkonfirmasi',   'count' => $stats['confirmed'], 'cls' => 'bg-emerald-50 text-emerald-700','activeBg' => 'bg-field-green text-white'],
                        'completed' => ['label' => 'Selesai Main',    'count' => $stats['completed'], 'cls' => 'bg-blue-50 text-blue-700',      'activeBg' => 'bg-blue-600 text-white'],
                    ];
                @endphp
                @foreach($tabs as $val => $tab)
                    @php $active = (string) request('status') === $val; @endphp
                    <a href="{{ $val === '' ? route('bookings.index', request()->except(['status','page'])) : route('bookings.index', array_merge(request()->except(['status','page']), ['status' => $val])) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold border transition-all duration-200
                              {{ $active
                                  ? $tab['activeBg'] . ' border-transparent shadow-md'
                                  : 'bg-white text-on-surface-variant border-stadium-gray hover:border-electric-blue hover:text-electric-blue' }}">
                        {{ $tab['label'] }}
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-xs font-bold
                                     {{ $active ? 'bg-white/25 text-white' : $tab['cls'] }}">
                            {{ number_format($tab['count']) }}
                        </span>
                    </a>
                @endforeach
            </div>

            @if($bookings->isEmpty())
                <div class="bg-white border border-stadium-gray rounded-2xl sporty-shadow p-16 text-center">
                    <div class="w-16 h-16 bg-blue-50 text-electric-blue rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-[32px]">event_busy</span>
                    </div>
                    <h3 class="text-lg font-bold text-pitch-black mb-1">Tidak ada reservasi ditemukan</h3>
                    <p class="text-on-surface-variant text-sm mb-6 max-w-sm mx-auto">
                        @if(request('status'))
                            Tidak ada booking dengan status yang dipilih. Coba pilih tab status lainnya.
                        @else
                            Anda belum memiliki reservasi lapangan. Yuk cari arena favoritmu dan mulai bertanding!
                        @endif
                    </p>
                    <a href="{{ route('venues.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-electric-blue text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">search</span>
                        Jelajahi Lapangan Tersedia
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($bookings as $booking)
                        @php
                            $sv        = $booking->status instanceof \App\Enums\BookingStatus ? $booking->status->value : (string) $booking->status;
                            $firstItem = $booking->items->first();
                            $lastItem  = $booking->items->last();
                            $now       = now();

                            $isOngoing = $sv === 'confirmed'
                                && $booking->checked_in
                                && $firstItem && $lastItem
                                && $now->gte(\Carbon\Carbon::parse($firstItem->booking_date)->setTimeFromTimeString($firstItem->start_time))
                                && $now->lt(\Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time));
                        @endphp

                        <div class="bg-white border rounded-2xl p-5 sm:p-6 sporty-shadow hover:shadow-md transition-all
                                    {{ $isOngoing ? 'border-field-green ring-1 ring-field-green/20' : 'border-stadium-gray hover:border-electric-blue' }}">
                            
                            {{-- Card Top: Code, Status, and Total --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-stadium-gray/70">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <span class="font-mono text-sm font-bold text-electric-blue bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200">
                                        {{ $booking->booking_code }}
                                    </span>
                                    <x-booking-status-badge :booking="$booking"/>
                                    @if($isOngoing)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-green-50 text-field-green border border-green-200">
                                            <span class="w-2 h-2 rounded-full bg-field-green animate-ping"></span>
                                            Sedang Bermain
                                        </span>
                                    @elseif($booking->checked_in)
                                        <span class="inline-flex items-center gap-1 text-xs text-field-green font-bold">
                                            <span class="material-symbols-outlined text-[15px]">check_circle</span> Check-in {{ $booking->checked_in_at?->format('H:i') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-[11px] text-on-surface-variant font-medium">Total Pembayaran</p>
                                    <p class="text-base font-bold text-pitch-black">
                                        Rp {{ number_format($booking->total_amount - $booking->discount_amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Card Middle: Venue & Schedule Details --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 py-4 text-xs">
                                <div>
                                    <p class="text-on-surface-variant font-medium">Lokasi Venue</p>
                                    <h4 class="font-bold text-sm text-pitch-black mt-0.5 truncate">{{ $booking->venue?->name }}</h4>
                                    <p class="text-on-surface-variant mt-0.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px] text-electric-blue">location_on</span>
                                        {{ $booking->venue?->city }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-on-surface-variant font-medium">Arena & Lapangan</p>
                                    <p class="font-bold text-sm text-pitch-black mt-0.5 truncate">{{ $firstItem?->court?->name ?? 'Lapangan' }}</p>
                                    <p class="text-on-surface-variant mt-0.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px] text-electric-blue">sports_soccer</span>
                                        {{ $booking->items->count() }} Sesi Jam
                                    </p>
                                </div>

                                <div>
                                    <p class="text-on-surface-variant font-medium">Jadwal Pertandingan</p>
                                    <p class="font-bold text-sm text-pitch-black mt-0.5">
                                        {{ $firstItem ? \Carbon\Carbon::parse($firstItem->booking_date)->format('d M Y') : '-' }}
                                    </p>
                                    <p class="text-electric-blue font-bold mt-0.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">schedule</span>
                                        {{ $firstItem?->start_time }} – {{ $lastItem?->end_time }}
                                    </p>
                                </div>
                            </div>

                            {{-- Card Bottom: Action Buttons --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-stadium-gray/70">
                                <div>
                                    @if($sv === 'pending' && $booking->expires_at?->isFuture())
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                                            <span class="material-symbols-outlined text-[14px]">schedule</span>
                                            Bayar sebelum {{ $booking->expires_at->format('H:i') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-on-surface-variant">Dipesan pada {{ $booking->created_at->format('d M Y H:i') }}</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if($sv === 'pending')
                                        <a href="{{ route('bookings.show', $booking) }}"
                                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-electric-blue text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition shadow-sm">
                                            <span class="material-symbols-outlined text-[16px]">credit_card</span>
                                            Bayar Sekarang
                                        </a>
                                    @elseif($sv === 'confirmed')
                                        <a href="{{ route('checkin.show', $booking) }}"
                                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-50 text-electric-blue text-xs font-bold rounded-xl hover:bg-blue-100 transition border border-blue-200">
                                            <span class="material-symbols-outlined text-[16px]">qr_code_scanner</span>
                                            Lihat QR Tiket
                                        </a>
                                    @endif

                                    <a href="{{ route('bookings.show', $booking) }}"
                                       class="inline-flex items-center gap-1 px-4 py-2 bg-surface-light border border-stadium-gray text-pitch-black text-xs font-bold rounded-xl hover:bg-slate-100 transition">
                                        Detail Reservasi
                                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                @if($bookings->hasPages())
                    <div class="mt-8 flex justify-center">
                        {{ $bookings->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
