<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('manage.bookings.index') }}"
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
                $sv = $booking->status instanceof \App\Enums\BookingStatus ? $booking->status->value : $booking->status;
                $statusCfg = [
                    'pending'   => ['bg-yellow-100 text-yellow-800 border-yellow-200', 'Menunggu Pembayaran'],
                    'confirmed' => ['bg-blue-100 text-blue-800 border-blue-200',      'Terkonfirmasi'],
                    'completed' => ['bg-green-100 text-green-800 border-green-200',   'Selesai'],
                    'cancelled' => ['bg-red-100 text-red-800 border-red-200',         'Dibatalkan'],
                    'expired'   => ['bg-gray-100 text-gray-600 border-gray-200',      'Kedaluwarsa'],
                ];
                $scls = $statusCfg[$sv][0] ?? 'bg-gray-100 text-gray-600 border-gray-200';
                $slbl = $statusCfg[$sv][1] ?? ucfirst($sv);
                $pv   = $booking->payment?->status instanceof \App\Enums\PaymentStatus
                    ? $booking->payment->status->value : ($booking->payment?->status ?? '');
                $payLabel = ['pending'=>'Belum Dibayar','paid'=>'Lunas','failed'=>'Gagal','expired'=>'Kedaluwarsa','cancelled'=>'Dibatalkan'][$pv] ?? $pv;
            @endphp

            {{-- Status banner --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
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
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800 border border-indigo-200">
                            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                            Sedang Bermain
                        </span>
                        @if($endDt)
                            <span class="text-sm font-mono font-bold text-indigo-600"
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
                        <span class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                            ✓ Check-in {{ $booking->checked_in_at?->format('d/m/Y H:i') }}
                        </span>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($sv === 'pending')
                        <form method="POST" action="{{ route('manage.bookings.confirm', $booking) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Konfirmasi Booking
                            </button>
                        </form>
                    @endif
                    @if(in_array($sv, ['pending','confirmed']))
                        <form method="POST" action="{{ route('manage.bookings.cancel', $booking) }}"
                              onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 text-red-700 border border-red-200 text-sm font-medium rounded-xl hover:bg-red-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Batalkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                {{-- Left: booking detail --}}
                <div class="lg:col-span-2 space-y-5">
                    {{-- Customer info --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Pemesan</h2>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                <span class="text-indigo-700 font-bold text-sm">{{ strtoupper(substr($booking->user?->name ?? 'U', 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $booking->user?->name }}</p>
                                <p class="text-sm text-gray-500">{{ $booking->user?->email }}</p>
                                @if($booking->user?->phone)
                                    <p class="text-sm text-gray-500">{{ $booking->user?->phone }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Items --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Detail Lapangan & Waktu</h2>
                        <div class="space-y-3">
                            @foreach($booking->items as $item)
                                <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $item->court?->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $item->court?->sport?->name }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($item->booking_date)->format('d M Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->start_time }} – {{ $item->end_time }}</p>
                                        <p class="text-xs font-semibold text-indigo-600">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Totals --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 space-y-1.5">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                            </div>
                            @if($booking->discount_amount > 0)
                                <div class="flex justify-between text-sm text-green-600">
                                    <span>Diskon ({{ $booking->promo?->code }})</span>
                                    <span>- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-base font-bold text-gray-900 pt-1 border-t border-gray-100">
                                <span>Total Bayar</span>
                                <span>Rp {{ number_format($booking->total_amount - $booking->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Review --}}
                    @if($booking->review)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Ulasan Customer</h2>
                            <div class="flex items-center gap-1 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-lg {{ $i <= $booking->review->rating ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                                @endfor
                                <span class="text-sm font-semibold text-gray-700 ml-1">{{ $booking->review->rating }}/5</span>
                            </div>
                            @if($booking->review->comment)
                                <p class="text-sm text-gray-600 italic">"{{ $booking->review->comment }}"</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-2">{{ $booking->review->created_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Right: payment + venue --}}
                <div class="space-y-5">
                    {{-- Payment --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Pembayaran</h2>
                        @if($booking->payment)
                            @php
                                $payBadge = ['paid'=>'bg-green-100 text-green-700','pending'=>'bg-yellow-100 text-yellow-700','failed'=>'bg-red-100 text-red-700','expired'=>'bg-gray-100 text-gray-600','cancelled'=>'bg-gray-100 text-gray-600'][$pv] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Status</span>
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $payBadge }}">{{ $payLabel }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Kode</span>
                                    <span class="font-mono text-xs text-gray-700">{{ $booking->payment->payment_code }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Jumlah</span>
                                    <span class="font-semibold text-gray-900">Rp {{ number_format($booking->payment->amount, 0, ',', '.') }}</span>
                                </div>
                                @if($booking->payment->paid_at)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Dibayar</span>
                                        <span class="text-gray-700">{{ $booking->payment->paid_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-sm text-gray-400">Belum ada data pembayaran.</p>
                        @endif
                    </div>

                    {{-- Venue --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Venue</h2>
                        <p class="font-semibold text-gray-900 text-sm">{{ $booking->venue?->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $booking->venue?->address }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->venue?->city }}</p>
                        @if($booking->venue?->phone)
                            <p class="text-xs text-gray-500 mt-1">📞 {{ $booking->venue->phone }}</p>
                        @endif
                        <div class="mt-3 pt-3 border-t border-gray-50">
                            <p class="text-xs text-gray-400">Dibuat {{ $booking->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    {{-- QR / Check-in info --}}
                    @if($sv === 'confirmed')
                        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 text-center">
                            <p class="text-sm font-semibold text-blue-800 mb-1">
                                {{ $booking->checked_in ? '✓ Sudah Check-in' : 'Belum Check-in' }}
                            </p>
                            <p class="text-xs text-blue-600">
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
