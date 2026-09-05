<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-pitch-black">Booking Saya</h1>
                <p class="text-sm text-on-surface-variant mt-0.5">Riwayat dan status pemesanan lapangan Anda</p>
            </div>
            <a href="{{ route('venues.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-electric-blue text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                <span class="material-symbols-outlined text-[16px]">add</span>
                Booking Baru
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($bookings->isEmpty())
                <div class="bg-white border border-dashed border-stadium-gray rounded-2xl shadow-sm p-16 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-[32px] text-on-surface-variant">calendar_month</span>
                    </div>
                    <h3 class="text-lg font-semibold text-pitch-black mb-1">Belum ada booking</h3>
                    <p class="text-on-surface-variant text-sm mb-5">Mulai cari lapangan favoritmu sekarang.</p>
                    <a href="{{ route('venues.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-electric-blue text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                        Cari Venue
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($bookings as $booking)
                        @php
                            $status      = $booking->status instanceof \App\Enums\BookingStatus
                                ? $booking->status : \App\Enums\BookingStatus::tryFrom((string) $booking->status);
                            $sv          = $status?->value ?? '';
                            $firstItem   = $booking->items->first();
                            $lastItem    = $booking->items->last();
                            $now         = now();

                            $isOngoing = $sv === 'confirmed'
                                && $booking->checked_in
                                && $lastItem
                                && $now->lt(\Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time));

                            $barColor = match(true) {
                                $isOngoing      => 'bg-electric-blue',
                                $sv === 'confirmed' => 'bg-sky-400',
                                $sv === 'pending'   => 'bg-amber-400',
                                $sv === 'completed' => 'bg-electric-blue',
                                default             => 'bg-slate-500',
                            };

                            $payStatus = $booking->payment?->status instanceof \App\Enums\PaymentStatus
                                ? $booking->payment->status->value
                                : ($booking->payment?->status ?? '');
                        @endphp

                        <a href="{{ route('bookings.show', $booking) }}"
                           class="group flex flex-col sm:flex-row sm:items-center gap-4 bg-white rounded-2xl border
                                  {{ $isOngoing ? 'border-electric-blue/30' : 'border-stadium-gray' }}
                                  shadow-sm hover:border-electric-blue/40 px-5 py-4 transition-all">

                            <div class="w-1 self-stretch rounded-full shrink-0 {{ $barColor }}
                                       {{ $isOngoing ? 'animate-pulse' : '' }}">
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="font-mono text-sm font-bold text-pitch-black">
                                        {{ $booking->booking_code }}
                                    </span>

                                    <x-booking-status-badge :booking="$booking"/>

                                    @if($isOngoing)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-xs font-medium rounded-full bg-blue-50 text-electric-blue">
                                            <span class="w-1.5 h-1.5 rounded-full bg-electric-blue animate-ping"></span>
                                            Sedang Bermain
                                        </span>
                                    @elseif($booking->checked_in)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-blue-50 text-electric-blue">
                                            <span class="material-symbols-outlined text-[14px]">check_circle</span> Check-in
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm text-on-surface-variant font-medium truncate">
                                    {{ $booking->venue?->name }}
                                </p>

                                <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-on-surface-variant">
                                    @if($firstItem)
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">calendar_month</span>
                                            {{ \Carbon\Carbon::parse($firstItem->booking_date)->format('d M Y') }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">schedule</span>
                                            {{ $firstItem->start_time }} – {{ $lastItem?->end_time }}
                                        </span>
                                        <span>{{ $firstItem->court?->name }}</span>

                                        @if($isOngoing)
                                            @php
                                                $endDt = \Carbon\Carbon::parse($lastItem->booking_date)
                                                    ->setTimeFromTimeString($lastItem->end_time);
                                            @endphp
                                            <span class="flex items-center gap-1 text-electric-blue font-medium"
                                                  x-data="{
                                                      end: new Date('{{ $endDt->toISOString() }}'),
                                                      t: '',
                                                      tick() {
                                                          const d = Math.max(0, this.end - new Date());
                                                          const m = Math.floor(d / 60000);
                                                          const s = Math.floor((d % 60000) / 1000);
                                                          this.t = m + ':' + String(s).padStart(2, '0');
                                                      }
                                                  }"
                                                  x-init="tick(); setInterval(() => tick(), 1000)">
                                                <span class="material-symbols-outlined text-[14px]">schedule</span>
                                                Sisa <span x-text="t" class="font-mono"></span>
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-4 shrink-0">
                                <div class="text-right">
                                    <p class="text-sm font-bold text-pitch-black">
                                        Rp {{ number_format($booking->total_amount - $booking->discount_amount, 0, ',', '.') }}
                                    </p>
                                    @if($booking->discount_amount > 0)
                                        <p class="text-xs text-electric-blue">
                                            Hemat Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-on-surface-variant mt-0.5">
                                        @if($payStatus === 'paid') Lunas
                                        @elseif($payStatus === 'pending') Belum dibayar
                                        @else {{ ucfirst($payStatus ?: '-') }}
                                        @endif
                                    </p>
                                </div>
                                <span class="material-symbols-outlined text-[20px] text-on-surface-variant group-hover:text-electric-blue transition">chevron_right</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($bookings->hasPages())
                    <div class="mt-6 flex justify-center">{{ $bookings->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
