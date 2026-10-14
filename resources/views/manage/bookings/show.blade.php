<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('manage.bookings.index') }}"
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
                $sv = $booking->status instanceof \App\Enums\BookingStatus ? $booking->status->value : $booking->status;
                $statusCfg = [
                    'pending'   => ['bg-amber-50 text-amber-700 border-amber-200', 'Menunggu Pembayaran'],
                    'confirmed' => ['bg-blue-50 text-electric-blue border-blue-200',      'Terkonfirmasi'],
                    'completed' => ['bg-green-50 text-field-green border-green-200',   'Selesai'],
                    'cancelled' => ['bg-red-50 text-red-700 border-red-200',         'Dibatalkan'],
                    'expired'   => ['bg-slate-100 text-slate-600 border-slate-200',      'Kedaluwarsa'],
                ];
                $pv   = $booking->payment?->status instanceof \App\Enums\PaymentStatus
                    ? $booking->payment->status->value : ($booking->payment?->status ?? '');
                $isPaid = $pv === 'paid';
                $scls = ($sv === 'expired' && $isPaid)
                    ? 'bg-red-50 text-red-700 border-red-200'
                    : ($statusCfg[$sv][0] ?? 'bg-slate-100 text-slate-600 border-slate-200');
                $slbl = ($sv === 'expired' && $isPaid)
                    ? 'Tiket Hangus (Tidak Hadir)'
                    : ($statusCfg[$sv][1] ?? ucfirst($sv));
                $payLabel = ['pending'=>'Belum Dibayar','paid'=>'Lunas','failed'=>'Gagal','expired'=>'Kedaluwarsa','cancelled'=>'Dibatalkan'][$pv] ?? $pv;
            @endphp
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white border border-stadium-gray rounded-xl sporty-shadow px-5 py-4">
                <div class="flex items-center gap-3 flex-wrap">
                    @php
                        $lastItem  = $booking->items->last();
                        $isOngoing = $sv === 'confirmed'
                            && $booking->checked_in
                            && $lastItem
                            && now()->lt(\Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time));
                        $endDt = $lastItem
                            ? \Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time)
                            : null;
                    @endphp

                    @if($isOngoing)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-sm font-semibold rounded-full bg-blue-50 text-electric-blue border border-electric-blue/20">
                            <span class="w-2 h-2 rounded-full bg-electric-blue animate-pulse"></span>
                            Sedang Bermain
                        </span>
                        @if($endDt)
                            <span class="text-sm font-mono font-bold text-electric-blue"
                                  x-data="{
                                      end: new Date('{{ $endDt->toISOString() }}'),
                                      t: '--:--',
                                      tick() {
                                          const d = Math.max(0, this.end - new Date());
                                          const m = Math.floor(d/60000);
                                          const s = Math.floor((d%60000)/1000);
                                          this.t = m+':'+String(s).padStart(2,'0');
                                          if (d === 0) setTimeout(() => location.reload(), 2000);
                                      }
                                  }"
                                  x-init="tick(); setInterval(() => tick(), 1000)"
                                  x-text="'Sisa ' + t">
                            </span>
                        @endif
                    @else
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full border {{ $scls }}">{{ $slbl }}</span>
                    @endif

                    @if($booking->checked_in)
                        <span class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium rounded-full bg-blue-50 text-electric-blue border border-electric-blue/20">
                            <span class="material-symbols-outlined text-[16px]">check_circle</span> Check-in {{ $booking->checked_in_at?->format('d/m/Y H:i') }}
                        </span>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($sv === 'pending')
                        <form method="POST" action="{{ route('manage.bookings.confirm', $booking) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-electric-blue text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                                <span class="material-symbols-outlined text-[16px]">check</span>
                                Konfirmasi Booking
                            </button>
                        </form>
                    @endif
                    @if(in_array($sv, ['pending','confirmed']))
                        <form method="POST" action="{{ route('manage.bookings.cancel', $booking) }}"
                              onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-500/10 text-red-400 border border-red-500/20 text-sm font-medium rounded-xl hover:bg-red-500/10 transition">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                                Batalkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <div class="lg:col-span-2 space-y-5 order-2 lg:order-1">
                    <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5">
                        <h2 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide mb-4">Pemesan</h2>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                                <span class="text-electric-blue font-bold text-sm">{{ strtoupper(substr($booking->user?->name ?? 'U', 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-pitch-black">{{ $booking->user?->name }}</p>
                                <p class="text-sm text-on-surface-variant">{{ $booking->user?->email }}</p>
                                @if($booking->user?->phone)
                                    <p class="text-sm text-on-surface-variant">{{ $booking->user?->phone }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5">
                        <h2 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide mb-4">Detail Lapangan & Waktu</h2>
                        <div class="space-y-3">
                            @foreach($booking->items as $item)
                                <div class="flex items-center justify-between py-3 border-b border-stadium-gray last:border-0">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-[16px] text-electric-blue">stadium</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-pitch-black">{{ $item->court?->name }}</p>
                                            <p class="text-xs text-on-surface-variant">{{ $item->court?->sport?->name }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-pitch-black">{{ \Carbon\Carbon::parse($item->booking_date)->format('d M Y') }}</p>
                                        <p class="text-xs text-on-surface-variant">{{ $item->start_time }} – {{ $item->end_time }}</p>
                                        <p class="text-xs font-semibold text-electric-blue">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 pt-4 border-t border-stadium-gray space-y-1.5">
                            <div class="flex justify-between text-sm text-on-surface-variant">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                            </div>
                            @if($booking->discount_amount > 0)
                                <div class="flex justify-between text-sm text-electric-blue">
                                    <span>Diskon ({{ $booking->promo?->code }})</span>
                                    <span>- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-base font-bold text-pitch-black pt-1 border-t border-stadium-gray">
                                <span>Total Bayar</span>
                                <span>Rp {{ number_format($booking->total_amount - $booking->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    @if($booking->review)
                        <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5">
                            <h2 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide mb-3">Ulasan Customer</h2>
                            <div class="flex items-center gap-1 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-lg {{ $i <= $booking->review->rating ? 'text-amber-400' : 'text-on-surface-variant' }}">★</span>
                                @endfor
                                <span class="text-sm font-semibold text-on-surface-variant ml-1">{{ $booking->review->rating }}/5</span>
                            </div>
                            @if($booking->review->comment)
                                <p class="text-sm text-on-surface-variant italic">"{{ $booking->review->comment }}"</p>
                            @endif
                            <p class="text-xs text-on-surface-variant mt-2">{{ $booking->review->created_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                </div>

                <div class="space-y-5 order-1 lg:order-2">
                    <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5">
                        <h2 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide mb-4">Pembayaran</h2>
                        @if($booking->payment)
                            @php
                                $payBadge = ['paid'=>'bg-blue-50 text-electric-blue','pending'=>'bg-amber-500/15 text-amber-400','failed'=>'bg-red-500/15 text-red-400','expired'=>'bg-slate-500/15 text-on-surface-variant','cancelled'=>'bg-slate-500/15 text-on-surface-variant'][$pv] ?? 'bg-slate-500/15 text-on-surface-variant';
                            @endphp
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-on-surface-variant">Status</span>
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $payBadge }}">{{ $payLabel }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-on-surface-variant">Kode</span>
                                    <span class="font-mono text-xs text-electric-blue">{{ $booking->payment->payment_code }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-on-surface-variant">Jumlah</span>
                                    <span class="font-semibold text-pitch-black">Rp {{ number_format($booking->payment->amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-on-surface-variant">Metode</span>
                                    <span class="font-semibold text-xs text-pitch-black uppercase">{{ $booking->payment->payment_method ?: 'Midtrans / Online' }}</span>
                                </div>
                                @if($booking->payment->paid_at)
                                    <div class="flex justify-between">
                                        <span class="text-on-surface-variant">Dibayar</span>
                                        <span class="text-on-surface-variant">{{ $booking->payment->paid_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-sm text-on-surface-variant">Belum ada data pembayaran.</p>
                        @endif
                    </div>

                    <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5">
                        <h2 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide mb-3">Venue</h2>
                        <p class="font-semibold text-pitch-black text-sm">{{ $booking->venue?->name }}</p>
                        <p class="text-xs text-on-surface-variant mt-1">{{ $booking->venue?->address }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $booking->venue?->city }}</p>
                        @if($booking->venue?->phone)
                            <p class="text-xs text-on-surface-variant mt-1">📞 {{ $booking->venue->phone }}</p>
                        @endif
                        <div class="mt-3 pt-3 border-t border-stadium-gray">
                            <p class="text-xs text-on-surface-variant">Dibuat {{ $booking->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    @if($sv === 'confirmed')
                        <div class="bg-sky-500/10 border border-sky-500/20 rounded-2xl p-5 text-center">
                            <p class="text-sm font-semibold text-sky-400 mb-1">
                                {!! $booking->checked_in ? '<span class="material-symbols-outlined text-[16px] align-text-bottom">check_circle</span> Sudah Check-in' : 'Belum Check-in' !!}
                            </p>
                            <p class="text-xs text-sky-400/80">
                                {{ $booking->checked_in
                                    ? 'Check-in pada ' . $booking->checked_in_at?->format('d/m H:i')
                                    : 'Scan QR code customer saat kedatangan.' }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
