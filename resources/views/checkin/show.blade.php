<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('bookings.show', $booking) }}"
               class="p-2 rounded-lg text-on-surface-variant hover:bg-slate-50 hover:text-pitch-black transition">
                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
            </a>
            <div>
                <h1 class="text-xl font-bold text-pitch-black">QR Check-in</h1>
                <p class="text-sm text-on-surface-variant font-mono">{{ $booking->booking_code }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-sm mx-auto px-4 sm:px-6">
            @php
                $sv        = $booking->status instanceof \App\Enums\BookingStatus
                    ? $booking->status->value : $booking->status;
                $firstItem = $booking->items->first();
                $lastItem  = $booking->items->last();
            @endphp

            @if(session('success'))
                <div class="mb-4 flex items-start gap-3 bg-blue-50 border border-electric-blue/20 text-electric-blue px-4 py-3 rounded-2xl text-sm">
                    <span class="material-symbols-outlined text-[20px] text-electric-blue shrink-0 mt-0.5">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 flex items-start gap-3 bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-2xl text-sm">
                    <span class="material-symbols-outlined text-[20px] text-red-400 shrink-0 mt-0.5">cancel</span>
                    <div>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($sv === 'confirmed')
                @if($booking->checked_in)
                    <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-8 text-center">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-[32px] text-electric-blue">check</span>
                        </div>
                        <h2 class="text-lg font-bold text-pitch-black mb-1">Sudah Check-in</h2>
                        <p class="text-sm text-on-surface-variant">
                            Tercatat pada<br>
                            <span class="font-semibold text-on-surface-variant">{{ $booking->checked_in_at?->format('d M Y, H:i') }} WIB</span>
                        </p>
                        <div class="mt-6 pt-5 border-t border-stadium-gray space-y-2 text-sm text-left">
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Venue</span>
                                <span class="font-medium text-pitch-black">{{ $booking->venue?->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Lapangan</span>
                                <span class="font-medium text-pitch-black">{{ $firstItem?->court?->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Waktu Main</span>
                                <span class="font-medium text-pitch-black">
                                    {{ $firstItem?->start_time }} – {{ $lastItem?->end_time }}
                                </span>
                            </div>
                        </div>
                    </div>

                @else
                    <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow overflow-hidden">

                        <div class="bg-electric-blue px-5 py-4 text-center">
                            <p class="text-on-surface-variant text-xs font-medium uppercase tracking-wider mb-0.5">Tiket Check-in</p>
                            <p class="text-pitch-black font-bold text-lg">{{ $booking->venue?->name }}</p>
                        </div>

                        <div class="flex flex-col items-center px-6 py-5">
                            <div class="p-3 bg-white border-2 border-stadium-gray rounded-2xl shadow-inner mb-3">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(196)
                                    ->margin(1)
                                    ->generate(route('manage.checkin.index', ['token' => $booking->qr_code])) !!}
                            </div>
                            <p class="text-xs text-on-surface-variant text-center">Tunjukkan kepada staff venue</p>
                        </div>

                        <div class="relative mx-6">
                            <div class="border-t border-dashed border-stadium-gray"></div>
                            <div class="absolute -top-3 -left-9 w-5 h-5 rounded-full bg-white border border-stadium-gray"></div>
                            <div class="absolute -top-3 -right-9 w-5 h-5 rounded-full bg-white border border-stadium-gray"></div>
                        </div>

                        <div class="px-6 py-5 space-y-2.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Kode</span>
                                <span class="font-mono font-bold text-electric-blue text-xs">{{ $booking->booking_code }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Lapangan</span>
                                <span class="font-semibold text-pitch-black">{{ $firstItem?->court?->name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Tanggal</span>
                                <span class="font-semibold text-pitch-black">
                                    {{ $firstItem ? \Carbon\Carbon::parse($firstItem->booking_date)->format('d M Y') : '-' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Jam Main</span>
                                <span class="font-semibold text-pitch-black">
                                    {{ $firstItem?->start_time ?? '-' }} – {{ $lastItem?->end_time ?? '-' }}
                                </span>
                            </div>
                        </div>

                        <div class="px-5 pb-5">
                            @if($checkInInfo)
                                @if($checkInInfo['is_open'])
                                    <div class="mb-3 px-4 py-3 bg-blue-50 border border-electric-blue/20 rounded-xl">
                                        <div class="flex items-center gap-2 text-sm text-electric-blue">
                                            <span class="w-2 h-2 rounded-full bg-electric-blue animate-pulse shrink-0"></span>
                                            @if($checkInInfo['is_play_time'])
                                                <span class="font-semibold">Waktunya main! Segera masuki venue.</span>
                                            @else
                                                <span>Check-in sudah dibuka · Jam main pukul <strong>{{ $checkInInfo['play_time']->format('H:i') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('checkin.store', $booking) }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full py-3 bg-electric-blue hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                                            @if($checkInInfo['is_play_time'])
                                                <span class="material-symbols-outlined text-[18px]">sprint</span> Check-in & Masuk Sekarang
                                            @else
                                                <span class="material-symbols-outlined text-[18px]">check_circle</span> Check-in Sekarang
                                            @endif
                                        </button>
                                    </form>

                                @else
                                    <div class="px-4 py-3 bg-amber-500/15 border border-amber-500/20 rounded-xl mb-3 text-sm text-amber-400"
                                         x-data="{
                                            opensAt: new Date('{{ $checkInInfo['opens_at']->toISOString() }}'),
                                            display: '',
                                            tick() {
                                                const diff = Math.max(0, this.opensAt - new Date());
                                                if (diff === 0) {
                                                    this.display = '';
                                                    // Reload page so button appears
                                                    setTimeout(() => location.reload(), 500);
                                                    return;
                                                }
                                                const m = Math.floor(diff / 60000);
                                                const s = Math.floor((diff % 60000) / 1000);
                                                this.display = m + ' menit ' + String(s).padStart(2,'0') + ' detik';
                                            }
                                         }"
                                         x-init="tick(); setInterval(() => tick(), 1000)">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[16px] text-amber-400 shrink-0">schedule</span>
                                            <span>
                                                Check-in dibuka pukul <strong>{{ $checkInInfo['opens_at']->format('H:i') }}</strong>
                                            </span>
                                        </div>
                                        <div x-show="display" class="mt-1.5 ml-6 text-xs text-amber-400/80">
                                            Tersisa: <span x-text="display" class="font-mono font-bold"></span>
                                        </div>
                                    </div>

                                    <button disabled
                                            class="w-full py-3 bg-slate-100 text-on-surface-variant text-sm font-semibold rounded-xl cursor-not-allowed">
                                        Check-in belum dibuka
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif

            @else
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-8 text-center">
                    <div class="w-14 h-14 bg-amber-500/15 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-[28px] text-amber-400">warning</span>
                    </div>
                    <h2 class="text-base font-semibold text-pitch-black mb-1">QR Belum Tersedia</h2>
                    <p class="text-sm text-on-surface-variant">
                        QR check-in hanya tersedia setelah booking dikonfirmasi dan pembayaran selesai.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('bookings.show', $booking) }}"
                           class="inline-flex px-4 py-2 text-sm font-medium text-electric-blue bg-blue-50 rounded-xl hover:bg-blue-700/20 transition">
                            Lihat status booking
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
