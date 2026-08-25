<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if(auth()->user()->role === 'admin')
            {{-- ═══════════════════════════════════════════
                 ADMIN DASHBOARD
            ═══════════════════════════════════════════ --}}

            {{-- Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @foreach([
                    ['label'=>'Total Pengguna',  'value'=>number_format($totalUsers),  'color'=>'bg-blue-500'],
                    ['label'=>'Total Venue',     'value'=>number_format($totalVenues), 'color'=>'bg-green-500'],
                    ['label'=>'Total Booking',   'value'=>number_format($totalBookings),'color'=>'bg-yellow-500'],
                    ['label'=>'Total Pendapatan','value'=>'Rp '.number_format($totalRevenue,0,',','.'),'color'=>'bg-indigo-500'],
                ] as $stat)
                    <div class="bg-white shadow-sm rounded-lg p-5 flex items-center gap-4">
                        <div class="w-2 h-10 rounded-full {{ $stat['color'] }}"></div>
                        <div>
                            <p class="text-xs text-gray-500">{{ $stat['label'] }}</p>
                            <p class="text-xl font-bold text-gray-900">{{ $stat['value'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Booking status breakdown --}}
            @if(isset($bookingsByStatus))
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
                @php
                    $statusCfg = [
                        'pending'   => ['label'=>'Menunggu', 'cls'=>'bg-yellow-100 text-yellow-800'],
                        'confirmed' => ['label'=>'Terkonfirmasi','cls'=>'bg-blue-100 text-blue-800'],
                        'completed' => ['label'=>'Selesai',  'cls'=>'bg-green-100 text-green-800'],
                        'cancelled' => ['label'=>'Dibatalkan','cls'=>'bg-red-100 text-red-800'],
                        'expired'   => ['label'=>'Kedaluwarsa','cls'=>'bg-gray-100 text-gray-600'],
                    ];
                @endphp
                @foreach($statusCfg as $key => $cfg)
                    <div class="bg-white shadow-sm rounded-lg p-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $cfg['cls'] }}">{{ $cfg['label'] }}</span>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $bookingsByStatus[$key] ?? 0 }}</p>
                    </div>
                @endforeach
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Top venues --}}
                @if(isset($topVenues))
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Top Venue by Revenue</h3>
                    <div class="space-y-3">
                        @foreach($topVenues as $v)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-800 font-medium">{{ $v->name }}</span>
                            <span class="text-indigo-600 font-semibold">Rp {{ number_format($v->revenue ?? 0, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Activity log --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Aktivitas Terbaru</h3>
                    @if(isset($recentActivities) && $recentActivities->count())
                        <div class="space-y-2 text-sm max-h-64 overflow-y-auto">
                            @foreach($recentActivities as $log)
                                <div class="flex items-start gap-2 text-gray-600">
                                    <span class="text-gray-400 text-xs whitespace-nowrap mt-0.5">{{ $log->created_at->format('d/m H:i') }}</span>
                                    <span>
                                        <span class="font-medium text-gray-800">{{ $log->user?->name ?? 'System' }}</span>
                                        · {{ str_replace('_', ' ', $log->action) }}
                                        @if(isset($log->properties['booking_code']))
                                            <span class="font-mono text-xs text-indigo-600">{{ $log->properties['booking_code'] }}</span>
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 text-sm">Belum ada aktivitas.</p>
                    @endif
                </div>
            </div>

            {{-- Quick links --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach([
                    ['label'=>'Kelola Users','href'=>route('admin.users.index'),'icon'=>'👥'],
                    ['label'=>'Kelola Venue','href'=>route('manage.venues.index'),'icon'=>'🏟️'],
                    ['label'=>'Semua Booking','href'=>route('manage.bookings.index'),'icon'=>'📋'],
                    ['label'=>'Promo','href'=>route('manage.promos.index'),'icon'=>'🎟️'],
                ] as $link)
                    <a href="{{ $link['href'] }}" class="bg-white shadow-sm rounded-lg p-4 text-center hover:shadow-md transition text-sm font-medium text-gray-700">
                        <div class="text-2xl mb-1">{{ $link['icon'] }}</div>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

        @elseif(auth()->user()->role === 'manager')
            {{-- ═══════════════════════════════════════════
                 MANAGER DASHBOARD
            ═══════════════════════════════════════════ --}}

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-xs text-gray-500">Booking Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($todayBookings) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-xs text-gray-500">Total Pendapatan</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-xs text-gray-500">Total Booking</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($bookingCount) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-xs text-gray-500">Lapangan Terpopuler</p>
                    <p class="text-base font-bold text-gray-900 mt-1 truncate">{{ $popularCourt?->name ?? '-' }}</p>
                    @if($popularCourt)
                        <p class="text-xs text-gray-400">{{ $popularCourt->total_bookings ?? 0 }}x booking</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Today bookings --}}
                <div class="lg:col-span-2 bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-700">Booking Hari Ini</h3>
                        <a href="{{ route('manage.bookings.index') }}" class="text-xs text-indigo-600">Lihat semua →</a>
                    </div>
                    @if(isset($todayBookingList) && $todayBookingList->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm divide-y divide-gray-100">
                                <thead>
                                    <tr class="text-xs text-gray-500 uppercase">
                                        <th class="pb-2 text-left">Kode</th>
                                        <th class="pb-2 text-left">Pelanggan</th>
                                        <th class="pb-2 text-left">Lapangan</th>
                                        <th class="pb-2 text-left">Jam</th>
                                        <th class="pb-2 text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($todayBookingList as $b)
                                    @php $sv = $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : $b->status; @endphp
                                    <tr>
                                        <td class="py-2 font-mono text-xs text-indigo-600">{{ $b->booking_code }}</td>
                                        <td class="py-2">{{ $b->user?->name }}</td>
                                        <td class="py-2">{{ $b->items->first()?->court?->name ?? '-' }}</td>
                                        <td class="py-2 text-gray-500 text-xs">{{ $b->items->first()?->start_time }} – {{ $b->items->last()?->end_time }}</td>
                                        <td class="py-2">
                                            @php $cls=['pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-blue-100 text-blue-700','cancelled'=>'bg-red-100 text-red-700','expired'=>'bg-gray-100 text-gray-600']; @endphp
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ $cls[$sv] ?? 'bg-gray-100 text-gray-700' }}">{{ $b->status instanceof \App\Enums\BookingStatus ? $b->status->label() : ucfirst($sv) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm">Tidak ada booking hari ini.</p>
                    @endif
                </div>

                {{-- Peak hours --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Peak Hours</h3>
                    @if(isset($peakHours) && $peakHours->count())
                        <div class="space-y-2">
                            @foreach($peakHours as $ph)
                                @php $pct = $peakHours->max('count') > 0 ? ($ph->count / $peakHours->max('count') * 100) : 0; @endphp
                                <div>
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>{{ substr($ph->start_time, 0, 5) }}</span>
                                        <span>{{ $ph->count }}x</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 text-sm">Belum ada data.</p>
                    @endif

                    <div class="mt-4 pt-4 border-t">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Aksi Cepat</h3>
                        <div class="space-y-2 text-sm">
                            <a href="{{ route('manage.venues.index') }}" class="block text-indigo-600 hover:text-indigo-800">→ Kelola Venue</a>
                            <a href="{{ route('manage.promos.index') }}" class="block text-indigo-600 hover:text-indigo-800">→ Kelola Promo</a>
                            <a href="{{ route('manage.checkin.index') }}" class="block text-indigo-600 hover:text-indigo-800">→ Scanner Check-in</a>
                        </div>
                    </div>
                </div>
            </div>

        @else
            {{-- ═══════════════════════════════════════════
                 CUSTOMER DASHBOARD
            ═══════════════════════════════════════════ --}}

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-xs text-gray-500">Booking Mendatang</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $upcomingBookings->count() }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-xs text-gray-500">Total Booking</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalBookings) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5 col-span-2 sm:col-span-1">
                    <p class="text-xs text-gray-500">Total Pengeluaran</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">Rp {{ number_format($totalSpent, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Upcoming bookings --}}
            @if($upcomingBookings->count())
                <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Booking Mendatang</h3>
                    <div class="space-y-3">
                        @foreach($upcomingBookings as $booking)
                        @php
                            $sv = $booking->status instanceof \App\Enums\BookingStatus ? $booking->status->value : (string) $booking->status;
                            $now = now();
                            $lastItem = $booking->items->last();
                            $firstItem = $booking->items->first();
                            $isOngoing = $sv === 'confirmed'
                                && $booking->checked_in
                                && $lastItem
                                && $now->lt(\Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time));
                        @endphp
                        <a href="{{ route('bookings.show', $booking) }}"
                           class="flex items-center justify-between border rounded-lg p-4 hover:bg-gray-50 transition
                                  {{ $isOngoing ? 'border-indigo-300 bg-indigo-50/30' : '' }}">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-mono text-sm font-semibold text-gray-900">{{ $booking->booking_code }}</p>
                                    @if($isOngoing)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-ping"></span>
                                            Sedang Bermain
                                        </span>
                                    @else
                                        <x-booking-status-badge :booking="$booking"/>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 mt-0.5">
                                    {{ $booking->venue?->name }}
                                    @if($firstItem)
                                        · {{ \Carbon\Carbon::parse($firstItem->booking_date)->format('d M') }}
                                        · {{ $firstItem->start_time }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0 ml-3">
                                @if($isOngoing)
                                    @php
                                        $endDt = \Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time);
                                    @endphp
                                    <span class="text-xs font-mono font-bold text-indigo-600"
                                          x-data="{
                                              end: new Date('{{ $endDt->toISOString() }}'),
                                              t: '--:--',
                                              tick() {
                                                  const d = Math.max(0, this.end - new Date());
                                                  const m = Math.floor(d/60000);
                                                  const s = Math.floor((d%60000)/1000);
                                                  this.t = m+':'+String(s).padStart(2,'0');
                                              }
                                          }"
                                          x-init="tick(); setInterval(()=>tick(),1000)"
                                          x-text="t">
                                    </span>
                                @elseif($booking->checked_in)
                                    <span class="text-xs text-emerald-600 font-medium">✓ Check-in</span>
                                @else
                                    <a href="{{ route('checkin.show', $booking) }}"
                                       class="text-xs text-indigo-600 hover:text-indigo-800"
                                       onclick="event.stopPropagation()">QR</a>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Recent bookings --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700">Riwayat Booking</h3>
                    <a href="{{ route('bookings.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800">Lihat semua →</a>
                </div>
                @if($recentBookings->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-400 mb-3">Belum ada booking.</p>
                        <a href="{{ route('venues.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Cari Venue</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-100">
                            <thead>
                                <tr class="text-xs text-gray-500 uppercase">
                                    <th class="pb-2 text-left">Kode</th>
                                    <th class="pb-2 text-left">Venue</th>
                                    <th class="pb-2 text-left">Tanggal</th>
                                    <th class="pb-2 text-left">Status</th>
                                    <th class="pb-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($recentBookings as $b)
                                @php $sv = $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : $b->status; @endphp
                                <tr>
                                    <td class="py-2">
                                        <a href="{{ route('bookings.show', $b) }}" class="font-mono text-xs text-indigo-600 hover:text-indigo-800">{{ $b->booking_code }}</a>
                                    </td>
                                    <td class="py-2 text-gray-700">{{ $b->venue?->name ?? '-' }}</td>
                                    <td class="py-2 text-gray-500 text-xs">{{ $b->items->first() ? \Carbon\Carbon::parse($b->items->first()->booking_date)->format('d/m/Y') : '-' }}</td>
                                    <td class="py-2">
                                        @php $cls=['pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-blue-100 text-blue-700','cancelled'=>'bg-red-100 text-red-700','expired'=>'bg-gray-100 text-gray-600','completed'=>'bg-green-100 text-green-700']; @endphp
                                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ $cls[$sv] ?? 'bg-gray-100 text-gray-700' }}">{{ $b->status instanceof \App\Enums\BookingStatus ? $b->status->label() : ucfirst($sv) }}</span>
                                    </td>
                                    <td class="py-2 text-right text-gray-700">Rp {{ number_format($b->total_amount - $b->discount_amount, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(method_exists($recentBookings, 'links') && $recentBookings->hasPages())
                        <div class="mt-4">{{ $recentBookings->links() }}</div>
                    @endif
                @endif
            </div>
        @endif

        </div>
    </div>
</x-app-layout>
