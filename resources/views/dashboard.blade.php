<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-bold text-pitch-black italic">Dashboard</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(auth()->user()->role === 'admin')

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @foreach([
                    ['label'=>'Total Pengguna',  'value'=>number_format($totalUsers),  'color'=>'bg-electric-blue'],
                    ['label'=>'Total Venue',     'value'=>number_format($totalVenues), 'color'=>'bg-field-green'],
                    ['label'=>'Total Booking',   'value'=>number_format($totalBookings),'color'=>'bg-athletic-orange'],
                    ['label'=>'Total Pendapatan','value'=>'Rp '.number_format($totalRevenue,0,',','.'),'color'=>'bg-purple-500'],
                ] as $stat)
                    <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5 flex items-center gap-4">
                        <div class="w-2 h-10 rounded-full {{ $stat['color'] }}"></div>
                        <div>
                            <p class="text-xs text-on-surface-variant font-medium">{{ $stat['label'] }}</p>
                            <p class="text-xl font-bold text-pitch-black">{{ $stat['value'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(isset($bookingsByStatus))
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
                @php
                    $statusCfg = [
                        'pending'   => ['label'=>'Menunggu', 'cls'=>'bg-amber-50 text-amber-700'],
                        'confirmed' => ['label'=>'Terkonfirmasi','cls'=>'bg-blue-50 text-electric-blue'],
                        'completed' => ['label'=>'Selesai',  'cls'=>'bg-green-50 text-field-green'],
                        'cancelled' => ['label'=>'Dibatalkan','cls'=>'bg-red-50 text-red-700'],
                        'expired'   => ['label'=>'Kedaluwarsa','cls'=>'bg-slate-100 text-slate-600'],
                    ];
                @endphp
                @foreach($statusCfg as $key => $cfg)
                    <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold {{ $cfg['cls'] }}">{{ $cfg['label'] }}</span>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ $bookingsByStatus[$key] ?? 0 }}</p>
                    </div>
                @endforeach
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                @if(isset($topVenues))
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-6">
                    <h3 class="text-sm font-bold text-pitch-black mb-4">Top Venue by Revenue</h3>
                    <div class="space-y-3">
                        @foreach($topVenues as $v)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-pitch-black font-medium">{{ $v->name }}</span>
                            <span class="text-athletic-orange font-bold">Rp {{ number_format($v->revenue ?? 0, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-6">
                    <h3 class="text-sm font-bold text-pitch-black mb-4">Aktivitas Terbaru</h3>
                    @if(isset($recentActivities) && $recentActivities->count())
                        <div class="space-y-2 text-sm max-h-64 overflow-y-auto">
                            @foreach($recentActivities as $log)
                                <div class="flex items-start gap-2 text-on-surface-variant">
                                    <span class="text-slate-400 text-xs whitespace-nowrap mt-0.5">{{ $log->created_at->format('d/m H:i') }}</span>
                                    <span>
                                        <span class="font-medium text-pitch-black">{{ $log->user?->name ?? 'System' }}</span>
                                        · {{ str_replace('_', ' ', $log->action) }}
                                        @if(isset($log->properties['booking_code']))
                                            <span class="font-mono text-xs text-electric-blue">{{ $log->properties['booking_code'] }}</span>
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-slate-400 text-sm">Belum ada aktivitas.</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach([
                    ['label'=>'Kelola Users','href'=>route('admin.users.index'),'icon'=>'group'],
                    ['label'=>'Kelola Venue','href'=>route('manage.venues.index'),'icon'=>'stadium'],
                    ['label'=>'Semua Booking','href'=>route('manage.bookings.index'),'icon'=>'receipt_long'],
                    ['label'=>'Promo','href'=>route('manage.promos.index'),'icon'=>'local_activity'],
                ] as $link)
                    <a href="{{ $link['href'] }}" class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-4 text-center hover:border-electric-blue hover:-translate-y-0.5 transition text-sm font-bold text-pitch-black group">
                        <div class="text-3xl mb-1 text-electric-blue group-hover:scale-110 transition-transform"><span class="material-symbols-outlined text-[32px]">{{ $link['icon'] }}</span></div>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

        @elseif(auth()->user()->role === 'manager')

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5 flex items-center gap-4">
                    <div class="w-2 h-10 rounded-full bg-field-green"></div>
                    <div>
                        <p class="text-xs text-on-surface-variant font-medium">Booking Hari Ini</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($todayBookings) }}</p>
                    </div>
                </div>
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5 flex items-center gap-4">
                    <div class="w-2 h-10 rounded-full bg-electric-blue"></div>
                    <div>
                        <p class="text-xs text-on-surface-variant font-medium">Total Pendapatan</p>
                        <p class="text-xl font-bold text-pitch-black mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5 flex items-center gap-4">
                    <div class="w-2 h-10 rounded-full bg-athletic-orange"></div>
                    <div>
                        <p class="text-xs text-on-surface-variant font-medium">Total Booking</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($bookingCount) }}</p>
                    </div>
                </div>
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5 flex items-center gap-4">
                    <div class="w-2 h-10 rounded-full bg-purple-500"></div>
                    <div class="min-w-0">
                        <p class="text-xs text-on-surface-variant font-medium">Lapangan Terpopuler</p>
                        <p class="text-base font-bold text-pitch-black mt-1 truncate">{{ $popularCourt?->name ?? '-' }}</p>
                        @if($popularCourt)
                            <p class="text-xs text-slate-400">{{ $popularCourt->total_bookings ?? 0 }}x booking</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="lg:col-span-2 bg-white border border-stadium-gray rounded-xl sporty-shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-pitch-black">Booking Hari Ini</h3>
                        <a href="{{ route('manage.bookings.index') }}" class="text-xs text-electric-blue font-bold hover:underline">Lihat semua →</a>
                    </div>
                    @if(isset($todayBookingList) && $todayBookingList->count())
                        <div class="sm:hidden space-y-2">
                            @foreach($todayBookingList as $b)
                            @php $sv = $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : $b->status; @endphp
                            <a href="{{ route('manage.bookings.show', $b) }}"
                               class="block border border-stadium-gray rounded-xl p-3 hover:border-electric-blue transition">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-mono text-xs font-bold text-electric-blue">{{ $b->booking_code }}</span>
                                    @php $cls=['pending'=>'bg-amber-50 text-amber-700','confirmed'=>'bg-blue-50 text-electric-blue','cancelled'=>'bg-red-50 text-red-700']; @endphp
                                    <span class="inline-flex px-2 py-0.5 text-xs font-bold rounded-full {{ $cls[$sv] ?? 'bg-slate-100 text-slate-600' }}">{{ $b->status instanceof \App\Enums\BookingStatus ? $b->status->label() : ucfirst($sv) }}</span>
                                </div>
                                <p class="text-sm font-medium text-pitch-black">{{ $b->user?->name }}</p>
                                <p class="text-xs text-on-surface-variant">{{ $b->items->first()?->court?->name ?? '-' }} · {{ $b->items->first()?->start_time }} – {{ $b->items->last()?->end_time }}</p>
                            </a>
                            @endforeach
                        </div>
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="min-w-full text-sm divide-y divide-stadium-gray">
                                <thead class="bg-slate-50">
                                    <tr class="text-xs text-on-surface-variant uppercase">
                                        <th class="pb-2 pt-2 px-2 text-left">Kode</th>
                                        <th class="pb-2 pt-2 px-2 text-left">Pelanggan</th>
                                        <th class="pb-2 pt-2 px-2 text-left">Lapangan</th>
                                        <th class="pb-2 pt-2 px-2 text-left">Jam</th>
                                        <th class="pb-2 pt-2 px-2 text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stadium-gray">
                                    @foreach($todayBookingList as $b)
                                    @php $sv = $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : $b->status; @endphp
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-2 px-2 font-mono text-xs text-electric-blue font-bold">{{ $b->booking_code }}</td>
                                        <td class="py-2 px-2 text-pitch-black">{{ $b->user?->name }}</td>
                                        <td class="py-2 px-2 text-pitch-black">{{ $b->items->first()?->court?->name ?? '-' }}</td>
                                        <td class="py-2 px-2 text-on-surface-variant text-xs">{{ $b->items->first()?->start_time }} – {{ $b->items->last()?->end_time }}</td>
                                        <td class="py-2 px-2">
                                            @php $cls=['pending'=>'bg-amber-50 text-amber-700','confirmed'=>'bg-blue-50 text-electric-blue','cancelled'=>'bg-red-50 text-red-700','expired'=>'bg-slate-100 text-slate-600']; @endphp
                                            <span class="inline-flex px-2 py-0.5 text-xs font-bold rounded-full {{ $cls[$sv] ?? 'bg-slate-100 text-slate-600' }}">{{ $b->status instanceof \App\Enums\BookingStatus ? $b->status->label() : ucfirst($sv) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-slate-400 text-sm">Tidak ada booking hari ini.</p>
                    @endif
                </div>

                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-6">
                    <h3 class="text-sm font-bold text-pitch-black mb-4">Peak Hours</h3>
                    @if(isset($peakHours) && $peakHours->count())
                        <div class="space-y-2">
                            @foreach($peakHours as $ph)
                                @php $pct = $peakHours->max('count') > 0 ? ($ph->count / $peakHours->max('count') * 100) : 0; @endphp
                                <div>
                                    <div class="flex justify-between text-xs text-on-surface-variant mb-1">
                                        <span>{{ substr($ph->start_time, 0, 5) }}</span>
                                        <span>{{ $ph->count }}x</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                                        <div class="bg-electric-blue h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-slate-400 text-sm">Belum ada data.</p>
                    @endif

                    <div class="mt-4 pt-4 border-t border-stadium-gray">
                        <h3 class="text-sm font-bold text-pitch-black mb-2">Aksi Cepat</h3>
                        <div class="space-y-2 text-sm">
                            <a href="{{ route('manage.venues.index') }}" class="block text-electric-blue font-semibold hover:underline">→ Kelola Venue</a>
                            <a href="{{ route('manage.promos.index') }}" class="block text-electric-blue font-semibold hover:underline">→ Kelola Promo</a>
                            <a href="{{ route('manage.checkin.index') }}" class="block text-electric-blue font-semibold hover:underline">→ Scanner Check-in</a>
                        </div>
                    </div>
                </div>
            </div>

        @else

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5 flex items-center gap-4">
                    <div class="w-2 h-10 rounded-full bg-field-green"></div>
                    <div>
                        <p class="text-xs text-on-surface-variant font-medium">Booking Mendatang</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ $upcomingBookings->count() }}</p>
                    </div>
                </div>
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5 flex items-center gap-4">
                    <div class="w-2 h-10 rounded-full bg-electric-blue"></div>
                    <div>
                        <p class="text-xs text-on-surface-variant font-medium">Total Booking</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($totalBookings) }}</p>
                    </div>
                </div>
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5 flex items-center gap-4 col-span-2 sm:col-span-1">
                    <div class="w-2 h-10 rounded-full bg-athletic-orange"></div>
                    <div>
                        <p class="text-xs text-on-surface-variant font-medium">Total Pengeluaran</p>
                        <p class="text-xl font-bold text-pitch-black mt-1">Rp {{ number_format($totalSpent, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            @if($upcomingBookings->count())
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-6 mb-6">
                    <h3 class="text-sm font-bold text-pitch-black mb-4">Booking Mendatang</h3>
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
                           class="flex items-center justify-between border rounded-lg p-4 hover:bg-slate-50 transition
                                  {{ $isOngoing ? 'border-field-green bg-green-50' : 'border-stadium-gray' }}">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-mono text-sm font-semibold text-pitch-black">{{ $booking->booking_code }}</p>
                                    @if($isOngoing)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-xs font-bold rounded-full bg-green-50 text-field-green">
                                            <span class="w-1.5 h-1.5 rounded-full bg-field-green animate-ping"></span>
                                            Sedang Bermain
                                        </span>
                                    @else
                                        <x-booking-status-badge :booking="$booking"/>
                                    @endif
                                </div>
                                <p class="text-sm text-on-surface-variant mt-0.5">
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
                                    <span class="text-xs font-mono font-bold text-field-green"
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
                                    <span class="text-xs text-field-green font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">check_circle</span> Check-in</span>
                                @else
                                    <a href="{{ route('checkin.show', $booking) }}"
                                       class="text-xs text-electric-blue font-bold hover:underline"
                                       onclick="event.stopPropagation()">QR</a>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-pitch-black">Riwayat Booking</h3>
                    <a href="{{ route('bookings.index') }}" class="text-xs text-electric-blue font-bold hover:underline">Lihat semua →</a>
                </div>
                @if($recentBookings->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-slate-400 mb-3">Belum ada booking.</p>
                        <a href="{{ route('venues.index') }}" class="bg-athletic-orange text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-orange-600">Cari Venue</a>
                    </div>
                @else
                    <div class="sm:hidden space-y-2">
                        @foreach($recentBookings as $b)
                        @php $sv = $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : $b->status; @endphp
                        <a href="{{ route('bookings.show', $b) }}"
                           class="flex items-center justify-between border border-stadium-gray rounded-xl p-3 hover:border-electric-blue transition">
                            <div class="min-w-0 flex-1">
                                <p class="font-mono text-xs font-semibold text-electric-blue">{{ $b->booking_code }}</p>
                                <p class="text-sm text-pitch-black truncate">{{ $b->venue?->name ?? '-' }}</p>
                                <p class="text-xs text-on-surface-variant">{{ $b->items->first() ? \Carbon\Carbon::parse($b->items->first()->booking_date)->format('d/m/Y') : '-' }}</p>
                            </div>
                            <div class="text-right ml-3 shrink-0">
                                @php $cls=['pending'=>'bg-amber-50 text-amber-700','confirmed'=>'bg-blue-50 text-electric-blue','cancelled'=>'bg-red-50 text-red-700','expired'=>'bg-slate-100 text-slate-600','completed'=>'bg-green-50 text-field-green']; @endphp
                                <span class="inline-flex px-2 py-0.5 text-xs font-bold rounded-full {{ $cls[$sv] ?? 'bg-slate-100 text-slate-600' }}">{{ $b->status instanceof \App\Enums\BookingStatus ? $b->status->label() : ucfirst($sv) }}</span>
                                <p class="text-xs font-semibold text-pitch-black mt-1">Rp {{ number_format($b->total_amount - $b->discount_amount, 0, ',', '.') }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-stadium-gray">
                            <thead class="bg-slate-50">
                                <tr class="text-xs text-on-surface-variant uppercase">
                                    <th class="pb-2 pt-2 px-2 text-left">Kode</th>
                                    <th class="pb-2 pt-2 px-2 text-left">Venue</th>
                                    <th class="pb-2 pt-2 px-2 text-left">Tanggal</th>
                                    <th class="pb-2 pt-2 px-2 text-left">Status</th>
                                    <th class="pb-2 pt-2 px-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stadium-gray">
                                @foreach($recentBookings as $b)
                                @php $sv = $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : $b->status; @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="py-2 px-2">
                                        <a href="{{ route('bookings.show', $b) }}" class="font-mono text-xs text-electric-blue font-bold hover:underline">{{ $b->booking_code }}</a>
                                    </td>
                                    <td class="py-2 px-2 text-pitch-black">{{ $b->venue?->name ?? '-' }}</td>
                                    <td class="py-2 px-2 text-on-surface-variant text-xs">{{ $b->items->first() ? \Carbon\Carbon::parse($b->items->first()->booking_date)->format('d/m/Y') : '-' }}</td>
                                    <td class="py-2 px-2">
                                        @php $cls=['pending'=>'bg-amber-50 text-amber-700','confirmed'=>'bg-blue-50 text-electric-blue','cancelled'=>'bg-red-50 text-red-700','expired'=>'bg-slate-100 text-slate-600','completed'=>'bg-green-50 text-field-green']; @endphp
                                        <span class="inline-flex px-2 py-0.5 text-xs font-bold rounded-full {{ $cls[$sv] ?? 'bg-slate-100 text-slate-600' }}">{{ $b->status instanceof \App\Enums\BookingStatus ? $b->status->label() : ucfirst($sv) }}</span>
                                    </td>
                                    <td class="py-2 px-2 text-right text-pitch-black font-medium">Rp {{ number_format($b->total_amount - $b->discount_amount, 0, ',', '.') }}</td>
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
