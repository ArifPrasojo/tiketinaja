<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Booking Saya</h1>
                <p class="text-sm text-gray-500 mt-0.5">Riwayat dan status pemesanan lapangan Anda</p>
            </div>
            <a href="{{ route('venues.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Booking Baru
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($bookings->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
                    <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum ada booking</h3>
                    <p class="text-gray-500 text-sm mb-5">Mulai cari lapangan favoritmu sekarang.</p>
                    <a href="{{ route('venues.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
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

                            // Sedang bermain: confirmed + checked_in + belum end_time
                            $isOngoing = $sv === 'confirmed'
                                && $booking->checked_in
                                && $lastItem
                                && $now->lt(\Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time));

                            $barColor = match(true) {
                                $isOngoing      => 'bg-indigo-500',
                                $sv === 'confirmed' => 'bg-blue-400',
                                $sv === 'pending'   => 'bg-yellow-400',
                                $sv === 'completed' => 'bg-green-400',
                                default             => 'bg-gray-300',
                            };

                            $payStatus = $booking->payment?->status instanceof \App\Enums\PaymentStatus
                                ? $booking->payment->status->value
                                : ($booking->payment?->status ?? '');
                        @endphp

                        <a href="{{ route('bookings.show', $booking) }}"
                           class="group flex flex-col sm:flex-row sm:items-center gap-4 bg-white rounded-2xl border
                                  {{ $isOngoing ? 'border-indigo-200 shadow-indigo-50' : 'border-gray-100' }}
                                  shadow-sm hover:shadow-md hover:border-indigo-100 px-5 py-4 transition-all">

                            {{-- Status bar --}}
                            <div class="w-1 self-stretch rounded-full shrink-0 {{ $barColor }}
                                       {{ $isOngoing ? 'animate-pulse' : '' }}">
                            </div>

                            {{-- Main info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="font-mono text-sm font-bold text-gray-900">
                                        {{ $booking->booking_code }}
                                    </span>

                                    {{-- Status badge via component --}}
                                    <x-booking-status-badge :booking="$booking"/>

                                    {{-- Sedang bermain pulse indicator --}}
                                    @if($isOngoing)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-ping"></span>
                                            Sedang Bermain
                                        </span>
                                    @elseif($booking->checked_in)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">
                                            ✓ Check-in
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm text-gray-700 font-medium truncate">
                                    {{ $booking->venue?->name }}
                                </p>

                                <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                                    @if($firstItem)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($firstItem->booking_date)->format('d M Y') }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $firstItem->start_time }} – {{ $lastItem?->end_time }}
                                        </span>
                                        <span>{{ $firstItem->court?->name }}</span>

                                        {{-- Timer sisa untuk yang sedang bermain --}}
                                        @if($isOngoing)
                                            @php
                                                $endDt = \Carbon\Carbon::parse($lastItem->booking_date)
                                                    ->setTimeFromTimeString($lastItem->end_time);
                                            @endphp
                                            <span class="flex items-center gap-1 text-indigo-600 font-medium"
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
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Sisa <span x-text="t" class="font-mono"></span>
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- Right: amount + arrow --}}
                            <div class="flex items-center gap-4 shrink-0">
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-900">
                                        Rp {{ number_format($booking->total_amount - $booking->discount_amount, 0, ',', '.') }}
                                    </p>
                                    @if($booking->discount_amount > 0)
                                        <p class="text-xs text-green-600">
                                            Hemat Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        @if($payStatus === 'paid') Lunas
                                        @elseif($payStatus === 'pending') Belum dibayar
                                        @else {{ ucfirst($payStatus ?: '-') }}
                                        @endif
                                    </p>
                                </div>
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-indigo-400 transition"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
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
