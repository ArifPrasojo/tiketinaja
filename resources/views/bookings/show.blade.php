<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('bookings.index') }}"
               class="p-2 rounded-lg text-on-surface-variant hover:bg-slate-50 hover:text-pitch-black transition">
                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
            </a>
            <div>
                <h1 class="text-xl font-bold text-pitch-black">Detail Booking</h1>
                <p class="text-sm text-on-surface-variant font-mono">{{ $booking->booking_code }}</p>
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

                $isOngoing = $svValue === 'confirmed'
                    && $booking->checked_in
                    && $lastItem
                    && $now->lt(\Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time));

                $endDateTime = $lastItem
                    ? \Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time)
                    : null;
            @endphp

            @if(session('success'))
                <div class="flex items-center gap-3 bg-blue-50 border border-electric-blue/20 text-electric-blue px-4 py-3 rounded-2xl text-sm">
                    <span class="material-symbols-outlined text-[20px] text-electric-blue shrink-0">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-2xl text-sm">
                    @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                </div>
            @endif

            @if($isOngoing)
                <div class="relative overflow-hidden bg-gradient-to-r from-teal-600 to-emerald-600 rounded-3xl p-6 text-white shadow-xl shadow-teal-500/15"
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
                    <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-white/10 pointer-events-none"></div>
                    <div class="absolute -right-2 bottom-2 w-16 h-16 rounded-full bg-white/5 pointer-events-none"></div>

                    <div class="relative z-10 flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                                <span class="material-symbols-outlined text-[28px] text-white">sprint</span>
                            </div>
                            <div>
                                <span class="inline-flex items-center gap-1 text-[11px] font-black uppercase tracking-wider text-teal-200">
                                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span> Sesi Berlangsung
                                </span>
                                <h3 class="font-extrabold text-lg text-white">Sedang Bermain</h3>
                                <p class="text-teal-100 text-xs mt-0.5">{{ $firstItem?->court?->name }} · {{ $booking->venue?->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[11px] font-semibold text-teal-200 mb-0.5 uppercase tracking-wider">Sisa Waktu</p>
                            <p class="font-mono font-black text-3xl text-white tracking-tight" x-text="display">--:--</p>
                        </div>
                    </div>

                    <div class="relative z-10 mt-4 h-2 bg-white/20 rounded-full overflow-hidden">
                        <div class="absolute inset-y-0 left-0 bg-white rounded-full transition-all"
                             :style="`width: ${pct}%`"></div>
                    </div>
                    <div class="relative z-10 flex justify-between mt-1.5 text-xs text-teal-100 font-semibold">
                        <span>Mulai: {{ $firstItem?->start_time }}</span>
                        <span>Selesai: {{ $lastItem?->end_time }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <x-booking-status-badge :booking="$booking"/>
                            @if($booking->checked_in)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-blue-50 text-electric-blue">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span> Check-in {{ $booking->checked_in_at?->format('H:i') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-on-surface-variant mt-1.5">Dibuat {{ $booking->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    <div class="flex flex-col sm:flex-row flex-wrap gap-2 w-full sm:w-auto shrink-0">
                        @if($svValue === 'pending')
                            @if($booking->expires_at?->isFuture())
                                <div class="flex items-center gap-1.5 text-xs text-amber-700 bg-amber-50 border border-amber-200 px-3 py-2 rounded-xl"
                                     x-data="{ expires: new Date('{{ $booking->expires_at->toISOString() }}'), d: '', expired: false }"
                                     x-init="
                                         let t = () => {
                                             let s = Math.max(0, Math.floor((expires - new Date()) / 1000));
                                             if (s <= 0 && !expired) {
                                                 expired = true;
                                                 setTimeout(() => location.reload(), 1000);
                                             }
                                             let m = Math.floor(s/60), ss = s%60;
                                             d = m+':'+String(ss).padStart(2,'0');
                                         };
                                         t(); setInterval(t, 1000);
                                     ">
                                    <span class="material-symbols-outlined text-[14px]">schedule</span>
                                    Bayar dalam <span x-text="d" class="font-mono font-bold ml-0.5"></span>
                                </div>
                            @else
                                <div class="flex items-center gap-1.5 text-xs text-red-700 bg-red-50 border border-red-200 px-3 py-2 rounded-xl">
                                    <span class="material-symbols-outlined text-[14px]">timer_off</span>
                                    Waktu pembayaran telah habis
                                </div>
                            @endif

                            @if($booking->expires_at?->isFuture())
                                @if(!empty($snapToken))
                                    <button type="button"
                                            id="pay-button"
                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-electric-blue text-white text-sm font-bold rounded-xl hover:bg-blue-700 active:scale-95 transition shadow-lg shadow-blue-500/20">
                                        <span class="material-symbols-outlined text-[18px]">payments</span>
                                        Bayar Sekarang (Midtrans)
                                    </button>
                                @else
                                    <form method="POST" action="{{ route('bookings.pay', $booking) }}" class="w-full sm:w-auto">
                                        @csrf
                                        <button type="submit"
                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-electric-blue text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                                            <span class="material-symbols-outlined text-[16px]">credit_card</span>
                                            Bayar Sekarang (Simulasi)
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @endif

                        @if($svValue === 'confirmed' && !$booking->checked_in)
                            <a href="{{ route('checkin.show', $booking) }}"
                               class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-electric-blue text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                                <span class="material-symbols-outlined text-[16px]">qr_code_scanner</span>
                                Lihat QR Check-in
                            </a>
                        @endif

                        @if(in_array($svValue, ['pending','confirmed']))
                            <form method="POST" action="{{ route('bookings.cancel', $booking) }}"
                                  class="w-full sm:w-auto"
                                  onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                                @csrf
                                <button type="submit"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                    Batalkan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="flex items-start gap-2 mb-5 pb-5 border-b border-stadium-gray">
                    <span class="material-symbols-outlined text-[16px] text-on-surface-variant shrink-0 mt-0.5">location_on</span>
                    <div>
                        <p class="text-sm font-semibold text-pitch-black">{{ $booking->venue?->name }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $booking->venue?->address }}, {{ $booking->venue?->city }}</p>
                    </div>
                </div>

                <h2 class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide mb-3">Detail Sesi</h2>
                <div class="space-y-2 mb-5">
                    @foreach($booking->items as $item)
                        <div class="flex items-center justify-between py-2.5 px-3 bg-white border border-stadium-gray rounded-xl text-sm">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[14px] text-electric-blue">stadium</span>
                                </div>
                                <div>
                                    <p class="font-medium text-pitch-black">{{ $item->court?->name }}</p>
                                    <p class="text-xs text-on-surface-variant">
                                        {{ \Carbon\Carbon::parse($item->booking_date)->format('d M Y') }}
                                        · {{ $item->start_time }} – {{ $item->end_time }}
                                    </p>
                                </div>
                            </div>
                            <p class="font-semibold text-pitch-black shrink-0">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-stadium-gray pt-4 space-y-1.5">
                    <div class="flex justify-between text-sm text-on-surface-variant">
                        <span>Subtotal ({{ $booking->items->count() }} jam)</span>
                        <span>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($booking->discount_amount > 0)
                        <div class="flex justify-between text-sm text-electric-blue">
                            <span>Diskon ({{ $booking->promo?->code }})</span>
                            <span>− Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-base font-bold text-pitch-black pt-2 border-t border-stadium-gray">
                        <span>Total Dibayar</span>
                        <span>Rp {{ number_format($booking->total_amount - $booking->discount_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @if($booking->payment)
                @php
                    $pv = $booking->payment->status instanceof \App\Enums\PaymentStatus
                        ? $booking->payment->status->value : (string)$booking->payment->status;
                    $payBadge = [
                        'paid'      => 'bg-blue-50 text-electric-blue',
                        'pending'   => 'bg-amber-500/15 text-amber-400',
                        'failed'    => 'bg-red-500/15 text-red-400',
                        'expired'   => 'bg-slate-500/15 text-on-surface-variant',
                        'cancelled' => 'bg-slate-500/15 text-on-surface-variant',
                    ][$pv] ?? 'bg-slate-500/15 text-on-surface-variant';
                    $payLabel = [
                        'paid'      => 'Lunas',
                        'pending'   => 'Belum Dibayar',
                        'failed'    => 'Gagal',
                        'expired'   => 'Kedaluwarsa',
                        'cancelled' => 'Dibatalkan',
                    ][$pv] ?? ucfirst($pv);
                @endphp
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5">
                    <h2 class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide mb-4">Pembayaran</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-on-surface-variant mb-0.5">Status</p>
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ $payBadge }}">{{ $payLabel }}</span>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-0.5">Kode Transaksi</p>
                            <p class="font-mono text-xs font-semibold text-electric-blue">{{ $booking->payment->payment_code }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-0.5">Metode</p>
                            <p class="font-semibold text-xs text-pitch-black uppercase">{{ $booking->payment->payment_method ?: 'Midtrans / Online' }}</p>
                        </div>
                        @if($booking->payment->paid_at)
                            <div>
                                <p class="text-xs text-on-surface-variant mb-0.5">Waktu Lunas</p>
                                <p class="text-on-surface-variant text-xs">{{ $booking->payment->paid_at->format('d M Y, H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($svValue === 'expired' && $booking->payment && ($booking->payment->status === \App\Enums\PaymentStatus::Paid || (is_string($booking->payment->status) && $booking->payment->status === 'paid')))
                <div class="bg-red-50 border border-red-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[24px]">event_busy</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-700">
                                    Tiket Hangus
                                </span>
                            </div>
                            <h3 class="font-bold text-pitch-black text-base mt-1">Masa Sewa Lapangan Telah Berakhir</h3>
                            <p class="text-xs text-on-surface-variant leading-relaxed mt-1">
                                Tiket reservasi ini dinyatakan hangus karena Anda tidak melakukan proses check-in selama masa aktif sewa lapangan di <strong>{{ $booking->venue?->name }}</strong>. Sesuai ketentuan, tiket yang hangus tidak dapat digunakan kembali atau dijadwalkan ulang.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if($svValue === 'completed')
                <div class="bg-blue-50 border border-electric-blue/20 rounded-2xl p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="material-symbols-outlined text-[32px] text-electric-blue">emoji_events</span>
                        <div>
                            <p class="font-bold text-electric-blue">Sesi Bermain Selesai</p>
                            <p class="text-xs text-electric-blue/80">
                                {{ $booking->items->count() }} jam bermain di {{ $booking->venue?->name }}
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-white rounded-xl p-3 border border-stadium-gray">
                            <p class="text-xs text-on-surface-variant mb-0.5">Check-in</p>
                            <p class="font-semibold text-pitch-black">{{ $booking->checked_in_at?->format('H:i') ?? '-' }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-3 border border-stadium-gray">
                            <p class="text-xs text-on-surface-variant mb-0.5">Selesai</p>
                            <p class="font-semibold text-pitch-black">{{ $lastItem?->end_time ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(in_array($svValue, ['confirmed', 'completed']))
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5">
                    <h2 class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide mb-4">Ulasan</h2>

                    @if($booking->review)
                        <div class="flex items-start gap-3">
                            <div class="flex items-center gap-0.5 shrink-0">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-lg {{ $i <= $booking->review->rating ? 'text-amber-400' : 'text-on-surface-variant' }}">★</span>
                                @endfor
                            </div>
                            <div class="flex-1">
                                @if($booking->review->comment)
                                    <p class="text-sm text-on-surface-variant">"{{ $booking->review->comment }}"</p>
                                @endif
                                <p class="text-xs text-on-surface-variant mt-1">{{ $booking->review->created_at->format('d M Y') }}</p>
                            </div>
                            <form method="POST" action="{{ route('reviews.destroy', $booking->review) }}"
                                  onsubmit="return confirm('Hapus ulasan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:bg-red-500/10">Hapus</button>
                            </form>
                        </div>
                    @else
                        <form method="POST" action="{{ route('reviews.store', $booking) }}" class="space-y-4">
                            @csrf
                            <div x-data="{ rating: {{ old('rating', 0) }} }">
                                <label class="block text-sm font-medium text-on-surface-variant mb-2">Rating</label>
                                <div class="flex gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" @click="rating = {{ $i }}"
                                                :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-on-surface-variant hover:text-amber-300'"
                                                class="text-3xl transition focus:outline-none">★</button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" :value="rating">
                                @error('rating')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Komentar (opsional)</label>
                                <textarea name="comment" rows="3"
                                          placeholder="Bagikan pengalaman bermain Anda..."
                                          class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg outline-none focus:ring-0 focus:ring-0 focus:border-electric-blue placeholder:text-on-surface-variant resize-none">{{ old('comment') }}</textarea>
                            </div>
                            <button type="submit"
                                    class="px-5 py-2.5 text-sm font-medium text-white bg-electric-blue rounded-xl hover:bg-blue-700 transition">
                                Kirim Ulasan
                            </button>
                        </form>
                    @endif
                </div>
            @endif

        </div>
    </div>

    @if(!empty($snapToken))
        <script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const payButton = document.getElementById('pay-button');
                if (payButton) {
                    payButton.addEventListener('click', function () {
                        payButton.disabled = true;
                        payButton.classList.add('opacity-75');

                        window.snap.pay('{{ $snapToken }}', {
                            onSuccess: function (result) {
                                fetch('{{ route('bookings.midtrans.finish', $booking) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify(result)
                                })
                                .then(response => response.json())
                                .then(data => {
                                    window.location.reload();
                                })
                                .catch(() => {
                                    window.location.reload();
                                });
                            },
                            onPending: function (result) {
                                window.location.reload();
                            },
                            onError: function (result) {
                                alert('Pembayaran gagal atau terjadi kesalahan. Silakan coba lagi.');
                                payButton.disabled = false;
                                payButton.classList.remove('opacity-75');
                                window.location.reload();
                            },
                            onClose: function () {
                                payButton.disabled = false;
                                payButton.classList.remove('opacity-75');
                            }
                        });
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
