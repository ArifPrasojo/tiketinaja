<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('bookings.show', $booking) }}"
               class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">QR Check-in</h1>
                <p class="text-sm text-gray-500 font-mono">{{ $booking->booking_code }}</p>
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
                <div class="mb-4 flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-2xl text-sm">
                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl text-sm">
                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($sv === 'confirmed')
                @if($booking->checked_in)
                    {{-- ── Already checked in ── --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 mb-1">Sudah Check-in</h2>
                        <p class="text-sm text-gray-500">
                            Tercatat pada<br>
                            <span class="font-semibold text-gray-700">{{ $booking->checked_in_at?->format('d M Y, H:i') }} WIB</span>
                        </p>
                        <div class="mt-6 pt-5 border-t border-gray-100 space-y-2 text-sm text-left">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Venue</span>
                                <span class="font-medium text-gray-900">{{ $booking->venue?->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Lapangan</span>
                                <span class="font-medium text-gray-900">{{ $firstItem?->court?->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Waktu Main</span>
                                <span class="font-medium text-gray-900">
                                    {{ $firstItem?->start_time }} – {{ $lastItem?->end_time }}
                                </span>
                            </div>
                        </div>
                    </div>

                @else
                    {{-- ── QR + check-in actions ── --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

                        {{-- Header stripe --}}
                        <div class="bg-indigo-600 px-5 py-4 text-center">
                            <p class="text-indigo-200 text-xs font-medium uppercase tracking-wider mb-0.5">Tiket Check-in</p>
                            <p class="text-white font-bold text-lg">{{ $booking->venue?->name }}</p>
                        </div>

                        {{-- QR Code --}}
                        <div class="flex flex-col items-center px-6 py-5">
                            <div class="p-3 bg-white border-2 border-gray-100 rounded-2xl shadow-inner mb-3">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(196)
                                    ->margin(1)
                                    ->generate(route('manage.checkin.index', ['token' => $booking->qr_code])) !!}
                            </div>
                            <p class="text-xs text-gray-400 text-center">Tunjukkan kepada staff venue</p>
                        </div>

                        {{-- Dashed separator --}}
                        <div class="relative mx-6">
                            <div class="border-t border-dashed border-gray-200"></div>
                            <div class="absolute -top-3 -left-9 w-5 h-5 rounded-full bg-gray-50 border border-gray-100"></div>
                            <div class="absolute -top-3 -right-9 w-5 h-5 rounded-full bg-gray-50 border border-gray-100"></div>
                        </div>

                        {{-- Booking details --}}
                        <div class="px-6 py-5 space-y-2.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Kode</span>
                                <span class="font-mono font-bold text-indigo-600 text-xs">{{ $booking->booking_code }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Lapangan</span>
                                <span class="font-semibold text-gray-900">{{ $firstItem?->court?->name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tanggal</span>
                                <span class="font-semibold text-gray-900">
                                    {{ $firstItem ? \Carbon\Carbon::parse($firstItem->booking_date)->format('d M Y') : '-' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Jam Main</span>
                                <span class="font-semibold text-gray-900">
                                    {{ $firstItem?->start_time ?? '-' }} – {{ $lastItem?->end_time ?? '-' }}
                                </span>
                            </div>
                        </div>

                        {{-- Check-in window status + self check-in button --}}
                        <div class="px-5 pb-5">
                            @if($checkInInfo)
                                @if($checkInInfo['is_open'])
                                    {{-- Check-in window OPEN --}}
                                    <div class="mb-3 px-4 py-3 bg-green-50 border border-green-200 rounded-xl">
                                        <div class="flex items-center gap-2 text-sm text-green-700">
                                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse shrink-0"></span>
                                            @if($checkInInfo['is_play_time'])
                                                <span class="font-semibold">Waktunya main! Segera masuki venue.</span>
                                            @else
                                                <span>Check-in sudah dibuka · Jam main pukul <strong>{{ $checkInInfo['play_time']->format('H:i') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Self check-in button --}}
                                    <form method="POST" action="{{ route('checkin.store', $booking) }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                                            @if($checkInInfo['is_play_time'])
                                                🏃 Check-in & Masuk Sekarang
                                            @else
                                                ✓ Check-in Sekarang
                                            @endif
                                        </button>
                                    </form>

                                @else
                                    {{-- Check-in belum dibuka - tampilkan countdown --}}
                                    <div class="px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl mb-3 text-sm text-amber-800"
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
                                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>
                                                Check-in dibuka pukul <strong>{{ $checkInInfo['opens_at']->format('H:i') }}</strong>
                                            </span>
                                        </div>
                                        <div x-show="display" class="mt-1.5 ml-6 text-xs text-amber-600">
                                            Tersisa: <span x-text="display" class="font-mono font-bold"></span>
                                        </div>
                                    </div>

                                    <button disabled
                                            class="w-full py-3 bg-gray-100 text-gray-400 text-sm font-semibold rounded-xl cursor-not-allowed">
                                        Check-in belum dibuka
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <button onclick="window.print()"
                                class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Cetak tiket
                        </button>
                    </div>
                @endif

            @else
                {{-- Not confirmed --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                    <div class="w-14 h-14 bg-yellow-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900 mb-1">QR Belum Tersedia</h2>
                    <p class="text-sm text-gray-500">
                        QR check-in hanya tersedia setelah booking dikonfirmasi dan pembayaran selesai.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('bookings.show', $booking) }}"
                           class="inline-flex px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition">
                            Lihat status booking
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
