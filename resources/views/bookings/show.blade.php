<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('bookings.index') }}"
               class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Detail Booking</h1>
                <p class="text-sm text-gray-500 font-mono">{{ $booking->booking_code }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @php
                $booking->load(['items.court.sport', 'venue', 'payment', 'review', 'promo']);
                $sv        = $booking->status instanceof \App\Enums\BookingStatus ? $booking->status : \App\Enums\BookingStatus::tryFrom((string)$booking->status);
                $svValue   = $sv?->value ?? '';
                $firstItem = $booking->items->first();
                $lastItem  = $booking->items->last();
                $now       = now();

                // Apakah sedang bermain sekarang
                $isOngoing = $svValue === 'confirmed'
                    && $booking->checked_in
                    && $lastItem
                    && $now->lt(\Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time));

                // Hitung sisa waktu jika sedang bermain
                $endDateTime = $lastItem
                    ? \Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time)
                    : null;
            @endphp

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-2xl text-sm">
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl text-sm">
                    @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                </div>
            @endif

            {{-- ── SEDANG BERMAIN BANNER ── --}}
            @if($isOngoing)
                <div class="relative overflow-hidden bg-indigo-600 rounded-2xl px-5 py-4 text-white shadow-lg"
                     x-data="{
                        endTime: new Date('{{ $endDateTime->toISOString() }}'),
                        display: '',
                        pct: 100,
                        tick() {
                            const total = {{ $booking->items->count() * 3600 }};
                            const diff  = Math.max(0, (this.endTime - new Date()) / 1000);
                            const m     = Math.floor(diff / 60);
                            const s     = Math.floor(diff % 60);
                            this.display = m + ':' + String(s).padStart(2,'0');
                            this.pct     = Math.round((diff / total) * 100);
                            if (diff === 0) { setTimeout(() => location.reload(), 2000); }
                        }
                     }"
                     x-init="tick(); setInterval(() => tick(), 1000)">
                    {{-- Decorative circle --}}
                    <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-white/10"></div>
                    <div class="absolute -right-2 bottom-2 w-16 h-16 rounded-full bg-white/5"></div>

                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <span class="text-xl">🏃</span>
                            </div>
                            <div>
                                <p class="font-bold text-sm">Sedang Bermain</p>
                                <p class="text-indigo-200 text-xs">{{ $firstItem?->court?->name }} · {{ $booking->venue?->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-indigo-200 mb-0.5">Sisa waktu</p>
                            <p class="font-mono font-bold text-2xl" x-text="display">--:--</p>
                        </div>
                    </div>

                    {{-- Progress bar --}}
                    <div class="relative mt-3 h-1.5 bg-white/20 rounded-full overflow-hidden">
                        <div class="absolute inset-y-0 left-0 bg-white/70 rounded-full transition-all"
                             :style="`width: ${pct}%`"></div>
                    </div>
                    <div class="flex justify-between mt-1 text-xs text-indigo-200">
                        <span>{{ $firstItem?->start_time }}</span>
                        <span>{{ $lastItem?->end_time }}</span>
                    </div>
                </div>
            @endif

            {{-- ── STATUS CARD ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <x-booking-status-badge :booking="$booking"/>
                            @if($booking->checked_in)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">
                                    ✓ Check-in {{ $booking->checked_in_at?->format('H:i') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">Dibuat {{ $booking->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex flex-wrap gap-2 shrink-0">
                        @if($svValue === 'pending')
                            {{-- Expiry countdown --}}
                            @if($booking->expires_at?->isFuture())
                                <div class="flex items-center gap-1.5 text-xs text-amber-700 bg-amber-50 border border-amber-200 px-3 py-2 rounded-xl"
                                     x-data="{ expires: new Date('{{ $booking->expires_at->toISOString() }}'), d: '' }"
                                     x-init="
                                         let t = () => {
                                             let s = Math.max(0, Math.floor((expires - new Date()) / 1000));
                                             let m = Math.floor(s/60), ss = s%60;
                                             d = m+':'+String(ss).padStart(2,'0');
                                         };
                                         t(); setInterval(t, 1000);
                                     ">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Bayar dalam <span x-text="d" class="font-mono font-bold ml-0.5"></span>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('bookings.pay', $booking) }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    Bayar Sekarang
                                </button>
                            </form>
                        @endif

                        @if($svValue === 'confirmed' && !$booking->checked_in)
                            <a href="{{ route('checkin.show', $booking) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5a.5.5 0 110-1 .5.5 0 010 1zm.5 3.5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
                                Lihat QR Check-in
                            </a>
                        @endif

                        @if(in_array($svValue, ['pending','confirmed']))
                            <form method="POST" action="{{ route('bookings.cancel', $booking) }}"
                                  onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Batalkan
                                </button>
                            </form>
                        @endif

                        <button onclick="window.print()"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Cetak
                        </button>
                    </div>
                </div>

                {{-- Venue --}}
                <div class="flex items-start gap-2 mb-5 pb-5 border-b border-gray-100">
                    <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $booking->venue?->name }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->venue?->address }}, {{ $booking->venue?->city }}</p>
                    </div>
                </div>

                {{-- Items table --}}
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Detail Sesi</h2>
                <div class="space-y-2 mb-5">
                    @foreach($booking->items as $item)
                        <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 rounded-xl text-sm">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->court?->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($item->booking_date)->format('d M Y') }}
                                        · {{ $item->start_time }} – {{ $item->end_time }}
                                    </p>
                                </div>
                            </div>
                            <p class="font-semibold text-gray-900 shrink-0">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Totals --}}
                <div class="border-t border-gray-100 pt-4 space-y-1.5">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal ({{ $booking->items->count() }} jam)</span>
                        <span>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($booking->discount_amount > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Diskon ({{ $booking->promo?->code }})</span>
                            <span>− Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-gray-100">
                        <span>Total Dibayar</span>
                        <span>Rp {{ number_format($booking->total_amount - $booking->discount_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- ── PAYMENT INFO ── --}}
            @if($booking->payment)
                @php
                    $pv = $booking->payment->status instanceof \App\Enums\PaymentStatus
                        ? $booking->payment->status->value : (string)$booking->payment->status;
                    $payBadge = [
                        'paid'      => 'bg-green-100 text-green-700',
                        'pending'   => 'bg-yellow-100 text-yellow-700',
                        'failed'    => 'bg-red-100 text-red-700',
                        'expired'   => 'bg-gray-100 text-gray-500',
                        'cancelled' => 'bg-gray-100 text-gray-500',
                    ][$pv] ?? 'bg-gray-100 text-gray-600';
                    $payLabel = [
                        'paid'      => 'Lunas',
                        'pending'   => 'Belum Dibayar',
                        'failed'    => 'Gagal',
                        'expired'   => 'Kedaluwarsa',
                        'cancelled' => 'Dibatalkan',
                    ][$pv] ?? ucfirst($pv);
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Pembayaran</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Status</p>
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ $payBadge }}">{{ $payLabel }}</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Kode</p>
                            <p class="font-mono text-xs font-semibold text-gray-700">{{ $booking->payment->payment_code }}</p>
                        </div>
                        @if($booking->payment->paid_at)
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Dibayar</p>
                                <p class="text-gray-700 text-xs">{{ $booking->payment->paid_at->format('d M Y, H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ── COMPLETED SUMMARY ── --}}
            @if($svValue === 'completed')
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-2xl">🏆</span>
                        <div>
                            <p class="font-bold text-green-800">Sesi Bermain Selesai</p>
                            <p class="text-xs text-green-600">
                                {{ $booking->items->count() }} jam bermain di {{ $booking->venue?->name }}
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-white/70 rounded-xl p-3">
                            <p class="text-xs text-gray-500 mb-0.5">Check-in</p>
                            <p class="font-semibold text-gray-900">{{ $booking->checked_in_at?->format('H:i') ?? '-' }}</p>
                        </div>
                        <div class="bg-white/70 rounded-xl p-3">
                            <p class="text-xs text-gray-500 mb-0.5">Selesai</p>
                            <p class="font-semibold text-gray-900">{{ $lastItem?->end_time ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── REVIEW ── --}}
            @if(in_array($svValue, ['confirmed', 'completed']))
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Ulasan</h2>

                    @if($booking->review)
                        <div class="flex items-start gap-3">
                            <div class="flex items-center gap-0.5 shrink-0">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-lg {{ $i <= $booking->review->rating ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                                @endfor
                            </div>
                            <div class="flex-1">
                                @if($booking->review->comment)
                                    <p class="text-sm text-gray-700">"{{ $booking->review->comment }}"</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-1">{{ $booking->review->created_at->format('d M Y') }}</p>
                            </div>
                            <form method="POST" action="{{ route('reviews.destroy', $booking->review) }}"
                                  onsubmit="return confirm('Hapus ulasan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-600">Hapus</button>
                            </form>
                        </div>
                    @else
                        <form method="POST" action="{{ route('reviews.store', $booking) }}" class="space-y-4">
                            @csrf
                            <div x-data="{ rating: {{ old('rating', 0) }} }">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                <div class="flex gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" @click="rating = {{ $i }}"
                                                :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-200 hover:text-yellow-300'"
                                                class="text-3xl transition focus:outline-none">★</button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" :value="rating">
                                @error('rating')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Komentar (opsional)</label>
                                <textarea name="comment" rows="3"
                                          placeholder="Bagikan pengalaman bermain Anda..."
                                          class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none">{{ old('comment') }}</textarea>
                            </div>
                            <button type="submit"
                                    class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">
                                Kirim Ulasan
                            </button>
                        </form>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
