<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('bookings.show', $booking) }}"
               class="p-2 rounded-xl text-on-surface-variant hover:bg-slate-100 hover:text-pitch-black transition">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <h1 class="text-xl font-bold text-pitch-black">Tiket Check-in</h1>
                <p class="text-xs text-on-surface-variant font-mono mt-0.5">{{ $booking->booking_code }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto px-4 sm:px-6 space-y-4">
            @php
                $sv        = $booking->status instanceof \App\Enums\BookingStatus
                    ? $booking->status->value : $booking->status;
                $firstItem = $booking->items->first();
                $lastItem  = $booking->items->last();
            @endphp

            @if(session('success'))
                <div class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3.5 rounded-2xl text-sm shadow-sm">
                    <span class="material-symbols-outlined text-[20px] text-field-green shrink-0 mt-0.5">check_circle</span>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3.5 rounded-2xl text-sm shadow-sm">
                    <span class="material-symbols-outlined text-[20px] text-red-500 shrink-0 mt-0.5">cancel</span>
                    <div>
                        @foreach($errors->all() as $error)
                            <p class="font-medium">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($sv === 'confirmed')
                @if($booking->checked_in)
                    {{-- State: Already Checked In --}}
                    <div class="bg-white border border-stadium-gray rounded-3xl sporty-shadow p-8 text-center">
                        <div class="w-16 h-16 bg-green-50 text-field-green rounded-2xl flex items-center justify-center mx-auto mb-4 border border-green-200">
                            <span class="material-symbols-outlined text-[36px]">verified</span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-field-green border border-green-200 mb-2">
                            <span class="w-2 h-2 rounded-full bg-field-green animate-pulse"></span>
                            Terverifikasi Masuk
                        </span>
                        <h2 class="text-xl font-extrabold text-pitch-black">Sudah Check-in</h2>
                        <p class="text-xs text-on-surface-variant mt-1">
                            Tercatat pada <strong class="text-pitch-black">{{ $booking->checked_in_at?->format('d M Y, H:i') }} WIB</strong>
                        </p>

                        <div class="mt-6 pt-5 border-t border-stadium-gray space-y-3 text-xs text-left bg-surface-light p-4 rounded-2xl">
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant font-medium">Venue</span>
                                <span class="font-bold text-pitch-black">{{ $booking->venue?->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant font-medium">Lapangan</span>
                                <span class="font-bold text-pitch-black">{{ $firstItem?->court?->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant font-medium">Waktu Main</span>
                                <span class="font-bold text-electric-blue">
                                    {{ $firstItem?->start_time }} – {{ $lastItem?->end_time }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('bookings.show', $booking) }}"
                               class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-pitch-black font-bold text-xs rounded-xl transition">
                                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                                Kembali ke Detail Reservasi
                            </a>
                        </div>
                    </div>

                @else
                    {{-- State: Ready for Check-in with QR --}}
                    <div class="bg-white border border-stadium-gray rounded-3xl sporty-shadow overflow-hidden shadow-lg">

                        {{-- Ticket Header --}}
                        <div class="bg-gradient-to-r from-electric-blue to-blue-600 px-6 py-5 text-center text-white">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-white/20 backdrop-blur-md text-[10px] font-extrabold uppercase tracking-widest text-white mb-1.5">
                                Tiket Masuk Pertandingan
                            </span>
                            <h3 class="text-white font-extrabold text-xl leading-tight">{{ $booking->venue?->name }}</h3>
                            <p class="text-blue-100 text-xs mt-0.5 flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">location_on</span>
                                {{ $booking->venue?->city }}
                            </p>
                        </div>

                        {{-- QR Code Display --}}
                        <div class="flex flex-col items-center px-6 py-6 bg-white">
                            <div class="p-4 bg-white border-2 border-stadium-gray rounded-2xl shadow-inner mb-3">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)
                                    ->margin(1)
                                    ->generate(route('manage.checkin.index', ['token' => $booking->qr_code])) !!}
                            </div>
                            <p class="text-xs font-semibold text-pitch-black flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px] text-electric-blue">qr_code_scanner</span>
                                Tunjukkan QR ini kepada petugas di lapangan
                            </p>
                        </div>

                        {{-- Perforated Ticket Notches --}}
                        <div class="relative mx-6">
                            <div class="border-t-2 border-dashed border-stadium-gray"></div>
                            <div class="absolute -top-3.5 -left-10 w-7 h-7 rounded-full bg-surface-light border border-stadium-gray"></div>
                            <div class="absolute -top-3.5 -right-10 w-7 h-7 rounded-full bg-surface-light border border-stadium-gray"></div>
                        </div>

                        {{-- Match Info Table --}}
                        <div class="px-6 py-5 space-y-3 text-xs bg-white">
                            <div class="flex justify-between items-center">
                                <span class="text-on-surface-variant font-medium">Kode Booking</span>
                                <span class="font-mono font-bold text-electric-blue text-sm">{{ $booking->booking_code }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-on-surface-variant font-medium">Lapangan</span>
                                <span class="font-bold text-pitch-black">{{ $firstItem?->court?->name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-on-surface-variant font-medium">Tanggal Main</span>
                                <span class="font-bold text-pitch-black">
                                    {{ $firstItem ? \Carbon\Carbon::parse($firstItem->booking_date)->format('d M Y') : '-' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-on-surface-variant font-medium">Jam Main</span>
                                <span class="font-bold text-electric-blue text-sm">
                                    {{ $firstItem?->start_time ?? '-' }} – {{ $lastItem?->end_time ?? '-' }}
                                </span>
                            </div>
                        </div>

                        {{-- Self Check-in Trigger / Countdown --}}
                        <div class="px-6 pb-6 pt-1">
                            @if($checkInInfo)
                                @if($checkInInfo['is_open'])
                                    <div class="mb-3 px-4 py-3 bg-blue-50 border border-blue-200 rounded-2xl">
                                        <div class="flex items-center gap-2 text-xs text-electric-blue font-bold">
                                            <span class="w-2 h-2 rounded-full bg-field-green animate-pulse shrink-0"></span>
                                            @if($checkInInfo['is_play_time'])
                                                <span>Waktunya main! Segera masuki arena lapangan.</span>
                                            @else
                                                <span>Check-in sudah dibuka! Jam main pukul {{ $checkInInfo['play_time']->format('H:i') }}.</span>
                                            @endif
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('checkin.store', $booking) }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full py-3.5 bg-electric-blue hover:bg-blue-700 active:scale-95 text-white text-sm font-bold rounded-2xl transition shadow-lg shadow-blue-500/20 flex items-center justify-center gap-2">
                                            @if($checkInInfo['is_play_time'])
                                                <span class="material-symbols-outlined text-[18px]">sprint</span> Check-in & Masuk Sekarang
                                            @else
                                                <span class="material-symbols-outlined text-[18px]">check_circle</span> Check-in Sekarang
                                            @endif
                                        </button>
                                    </form>

                                @else
                                    <div class="px-4 py-3.5 bg-amber-50 border border-amber-200 rounded-2xl mb-3 text-xs text-amber-800"
                                         x-data="{
                                            opensAt: new Date('{{ $checkInInfo['opens_at']->toISOString() }}'),
                                            display: '',
                                            tick() {
                                                const diff = Math.max(0, this.opensAt - new Date());
                                                if (diff === 0) {
                                                    this.display = '';
                                                    setTimeout(() => location.reload(), 500);
                                                    return;
                                                }
                                                const m = Math.floor(diff / 60000);
                                                const s = Math.floor((diff % 60000) / 1000);
                                                this.display = m + 'm ' + String(s).padStart(2,'0') + 'd';
                                            }
                                         }"
                                         x-init="tick(); setInterval(() => tick(), 1000)">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[18px] text-amber-600 shrink-0">schedule</span>
                                            <span>
                                                Check-in mandiri dibuka pukul <strong class="font-bold text-pitch-black">{{ $checkInInfo['opens_at']->format('H:i') }}</strong>
                                            </span>
                                        </div>
                                        <div x-show="display" class="mt-2 text-xs font-semibold text-amber-900 flex items-center gap-1.5">
                                            <span>Tersisa:</span>
                                            <span x-text="display" class="font-mono font-bold bg-amber-100 px-2 py-0.5 rounded-md"></span>
                                        </div>
                                    </div>

                                    <button disabled
                                            class="w-full py-3.5 bg-slate-100 text-on-surface-variant text-sm font-bold rounded-2xl cursor-not-allowed">
                                        Check-in Belum Dibuka
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif

            @elseif($sv === 'expired')
                @php
                    $isPaid = $booking->payment && (
                        $booking->payment->status === \App\Enums\PaymentStatus::Paid ||
                        (is_string($booking->payment->status) && $booking->payment->status === 'paid')
                    );
                @endphp
                <div class="bg-white border border-stadium-gray rounded-3xl sporty-shadow p-8 text-center">
                    @if($isPaid)
                        <div class="w-16 h-16 bg-red-50 text-red-600 border border-red-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-[32px]">event_busy</span>
                        </div>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200 mb-2">
                            Tiket Hangus
                        </span>
                        <h2 class="text-xl font-extrabold text-pitch-black">Masa Sewa Telah Berakhir</h2>
                        <p class="text-xs text-on-surface-variant leading-relaxed max-w-xs mx-auto mt-2">
                            Tiket ini telah dinyatakan hangus karena Anda tidak melakukan check-in selama masa aktif sewa lapangan.
                        </p>
                    @else
                        <div class="w-16 h-16 bg-amber-50 text-amber-600 border border-amber-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-[32px]">warning</span>
                        </div>
                        <h2 class="text-lg font-bold text-pitch-black mb-1">Booking Kedaluwarsa</h2>
                        <p class="text-xs text-on-surface-variant leading-relaxed max-w-xs mx-auto mt-1">
                            Booking ini telah kedaluwarsa karena pembayaran tidak diselesaikan dalam batas waktu yang ditentukan.
                        </p>
                    @endif

                    <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-2">
                        <a href="{{ route('bookings.show', $booking) }}"
                           class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-bold text-pitch-black bg-surface-light border border-stadium-gray rounded-xl hover:bg-slate-100 transition shadow-sm">
                            <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                            Detail Reservasi
                        </a>
                        <a href="{{ route('venues.index') }}"
                           class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-bold text-white bg-electric-blue rounded-xl hover:bg-blue-700 transition shadow-sm">
                            <span class="material-symbols-outlined text-[16px]">search</span>
                            Cari Lapangan Lain
                        </a>
                    </div>
                </div>

            @else
                {{-- State: Not Confirmed Yet (Pending / Cancelled) --}}
                <div class="bg-white border border-stadium-gray rounded-3xl sporty-shadow p-8 text-center">
                    <div class="w-16 h-16 bg-amber-50 text-amber-600 border border-amber-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-[32px]">warning</span>
                    </div>
                    <h2 class="text-lg font-bold text-pitch-black mb-1">Tiket QR Belum Tersedia</h2>
                    <p class="text-xs text-on-surface-variant leading-relaxed max-w-xs mx-auto">
                        Tiket QR check-in hanya akan diterbitkan setelah pembayaran Anda berhasil dikonfirmasi.
                    </p>
                    <div class="mt-5">
                        <a href="{{ route('bookings.show', $booking) }}"
                           class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-bold text-white bg-electric-blue rounded-xl hover:bg-blue-700 transition shadow-sm">
                            <span class="material-symbols-outlined text-[16px]">credit_card</span>
                            Selesaikan Pembayaran
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
