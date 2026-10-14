<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-pitch-black">Hello, {{ auth()->user()->name }}!</h2>
            <p class="text-sm text-on-surface-variant mt-0.5">Berikut ringkasan platform TiketinAja hari ini.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(auth()->user()->role === 'admin')

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
                @foreach([
                    ['label'=>'Total Pengguna',  'value'=>number_format($totalUsers),  'icon'=>'group',         'iconBg'=>'bg-blue-100',   'iconColor'=>'text-electric-blue'],
                    ['label'=>'Total Venue',     'value'=>number_format($totalVenues), 'icon'=>'stadium',       'iconBg'=>'bg-green-100',  'iconColor'=>'text-field-green'],
                    ['label'=>'Total Booking',   'value'=>number_format($totalBookings),'icon'=>'receipt_long', 'iconBg'=>'bg-orange-100', 'iconColor'=>'text-athletic-orange'],
                    ['label'=>'Total Pendapatan','value'=>'Rp '.number_format($totalRevenue,0,',','.'),'icon'=>'payments','iconBg'=>'bg-purple-100','iconColor'=>'text-purple-600'],
                ] as $stat)
                    <div class="bg-white border border-stadium-gray rounded-2xl p-4 sm:p-5 flex items-center gap-3.5 sm:gap-4 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-xl {{ $stat['iconBg'] }} flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[24px] {{ $stat['iconColor'] }}">{{ $stat['icon'] }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-on-surface-variant font-medium uppercase tracking-wide">{{ $stat['label'] }}</p>
                            <p class="text-lg sm:text-xl font-bold text-pitch-black truncate mt-0.5">{{ $stat['value'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(isset($bookingsByStatus))
            <div class="mb-8">
                <h3 class="text-sm font-bold text-pitch-black uppercase tracking-wide mb-3">
                    <span class="material-symbols-outlined text-[16px] align-text-bottom text-electric-blue mr-1">monitoring</span>
                    Status Booking
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    @php
                        $statusCfg = [
                            'pending'   => ['label'=>'Menunggu',       'icon'=>'hourglass_top',  'iconBg'=>'bg-amber-100',  'iconColor'=>'text-amber-600',  'badge'=>'bg-amber-50 text-amber-700 border border-amber-200'],
                            'confirmed' => ['label'=>'Terkonfirmasi', 'icon'=>'check_circle',   'iconBg'=>'bg-blue-100',   'iconColor'=>'text-electric-blue','badge'=>'bg-blue-50 text-electric-blue border border-blue-200'],
                            'completed' => ['label'=>'Selesai',       'icon'=>'emoji_events',   'iconBg'=>'bg-green-100',  'iconColor'=>'text-field-green', 'badge'=>'bg-green-50 text-field-green border border-green-200'],
                            'cancelled' => ['label'=>'Dibatalkan',    'icon'=>'cancel',         'iconBg'=>'bg-red-100',    'iconColor'=>'text-red-600',     'badge'=>'bg-red-50 text-red-700 border border-red-200'],
                            'expired'   => ['label'=>'Kedaluwarsa',   'icon'=>'alarm_off',      'iconBg'=>'bg-slate-100',  'iconColor'=>'text-slate-500',   'badge'=>'bg-slate-50 text-slate-600 border border-slate-200'],
                        ];
                    @endphp
                    @foreach($statusCfg as $key => $cfg)
                        <div class="bg-white border border-stadium-gray rounded-2xl p-4 text-center hover:shadow-md transition-shadow">
                            <div class="w-10 h-10 rounded-xl {{ $cfg['iconBg'] }} flex items-center justify-center mx-auto mb-2">
                                <span class="material-symbols-outlined text-[20px] {{ $cfg['iconColor'] }}">{{ $cfg['icon'] }}</span>
                            </div>
                            <p class="text-2xl font-bold text-pitch-black">{{ $bookingsByStatus[$key] ?? 0 }}</p>
                            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $cfg['badge'] }}">{{ $cfg['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                @if(isset($topVenues))
                <div class="bg-white border border-stadium-gray rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-stadium-gray flex items-center justify-between">
                        <h3 class="text-sm font-bold text-pitch-black flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-athletic-orange">leaderboard</span>
                            Top Venue by Revenue
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($topVenues as $i => $v)
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0
                                    {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-slate-200 text-slate-600' : 'bg-orange-100 text-orange-600') }}">
                                    {{ $i + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-pitch-black truncate">{{ $v->name }}</p>
                                </div>
                                <span class="text-sm font-bold text-athletic-orange shrink-0">Rp {{ number_format($v->revenue ?? 0, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <div class="bg-white border border-stadium-gray rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-stadium-gray flex items-center justify-between">
                        <h3 class="text-sm font-bold text-pitch-black flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-electric-blue">history</span>
                            Aktivitas Terbaru
                        </h3>
                    </div>
                    <div class="p-6">
                        @if(isset($recentActivities) && $recentActivities->count())
                            <div class="space-y-3 max-h-72 overflow-y-auto">
                                @foreach($recentActivities as $log)
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center shrink-0 mt-0.5">
                                            <span class="material-symbols-outlined text-[16px] text-on-surface-variant">event_note</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm text-pitch-black">
                                                <span class="font-semibold">{{ $log->user?->name ?? 'System' }}</span>
                                                <span class="text-on-surface-variant"> · {{ str_replace('_', ' ', $log->action) }}</span>
                                            </p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                @if(isset($log->properties['booking_code']))
                                                    <span class="font-mono text-xs text-electric-blue font-bold">{{ $log->properties['booking_code'] }}</span>
                                                @endif
                                                <span class="text-xs text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <span class="material-symbols-outlined text-[40px] text-slate-300">inbox</span>
                                <p class="text-sm text-slate-400 mt-2">Belum ada aktivitas.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold text-pitch-black uppercase tracking-wide mb-3">
                    <span class="material-symbols-outlined text-[16px] align-text-bottom text-electric-blue mr-1">bolt</span>
                    Aksi Cepat
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach([
                        ['label'=>'Kelola Users',   'desc'=>'Manajemen akun pengguna',  'href'=>route('admin.users.index'),     'icon'=>'group',          'iconBg'=>'bg-blue-100',   'iconColor'=>'text-electric-blue'],
                        ['label'=>'Kelola Venue',   'desc'=>'Atur venue & lapangan',    'href'=>route('manage.venues.index'),   'icon'=>'stadium',        'iconBg'=>'bg-green-100',  'iconColor'=>'text-field-green'],
                        ['label'=>'Semua Booking',  'desc'=>'Lihat semua transaksi',    'href'=>route('manage.bookings.index'), 'icon'=>'receipt_long',    'iconBg'=>'bg-orange-100', 'iconColor'=>'text-athletic-orange'],
                        ['label'=>'Promo',          'desc'=>'Kelola kode diskon',       'href'=>route('manage.promos.index'),   'icon'=>'local_activity',  'iconBg'=>'bg-purple-100', 'iconColor'=>'text-purple-600'],
                    ] as $link)
                        <a href="{{ $link['href'] }}" class="bg-white border border-stadium-gray rounded-2xl p-5 hover:border-electric-blue hover:shadow-md hover:-translate-y-0.5 transition-all group">
                            <div class="w-11 h-11 rounded-xl {{ $link['iconBg'] }} flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-[22px] {{ $link['iconColor'] }}">{{ $link['icon'] }}</span>
                            </div>
                            <p class="text-sm font-bold text-pitch-black">{{ $link['label'] }}</p>
                            <p class="text-xs text-on-surface-variant mt-0.5">{{ $link['desc'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>

        @elseif(auth()->user()->role === 'manager')

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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

            {{-- Customer Hero Banner --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-electric-blue to-blue-600 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-500/15 mb-8">
                <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="max-w-xl">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-bold uppercase tracking-wider mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-field-green"></span>
                            Portal Pemain
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Siap bertanding hari ini, {{ explode(' ', auth()->user()->name)[0] }}?</h2>
                        <p class="text-blue-100 text-sm mt-1.5 leading-relaxed">
                            Pesan lapangan futsal, badminton, basket, dan tenis favoritmu dengan slot real-time dan pembayaran instan.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('venues.index') }}"
                           class="inline-flex items-center gap-2 px-6 py-3.5 bg-white text-electric-blue font-bold text-sm rounded-2xl hover:bg-blue-50 active:scale-95 transition shadow-lg shadow-black/10">
                            <span class="material-symbols-outlined text-[18px]">search</span>
                            Cari Lapangan
                        </a>
                        <a href="{{ route('bookings.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-3.5 bg-white/15 hover:bg-white/25 text-white font-bold text-sm rounded-2xl backdrop-blur-md active:scale-95 transition">
                            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                            Tiket Saya
                        </a>
                    </div>
                </div>
            </div>

            {{-- Metric Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Jadwal Mendatang</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ $upcomingBookings->count() }}</p>
                        <span class="inline-block mt-1 text-[11px] font-bold text-electric-blue">Sesi menunggu</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-electric-blue flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">event_available</span>
                    </div>
                </div>

                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Total Booking</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($totalBookings) }}</p>
                        <span class="inline-block mt-1 text-[11px] font-bold text-field-green">Seluruh reservasi</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-field-green flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">sports_score</span>
                    </div>
                </div>

                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Total Pengeluaran</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">Rp {{ number_format($totalSpent, 0, ',', '.') }}</p>
                        <span class="inline-block mt-1 text-[11px] font-bold text-athletic-orange">Selesai terbayar</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-orange-50 text-athletic-orange flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">payments</span>
                    </div>
                </div>
            </div>

            {{-- Upcoming / Active Bookings --}}
            @if($upcomingBookings->count())
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-pitch-black flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px] text-electric-blue">schedule</span>
                            Jadwal Main Mendatang
                        </h3>
                        <span class="text-xs font-semibold text-on-surface-variant">{{ $upcomingBookings->count() }} Jadwal Aktif</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        <div class="bg-white border rounded-2xl p-5 sporty-shadow transition-all hover:shadow-md
                                    {{ $isOngoing ? 'border-field-green ring-1 ring-field-green/20' : 'border-stadium-gray hover:border-electric-blue' }}">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div>
                                    <span class="font-mono text-xs font-bold text-electric-blue bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200">
                                        {{ $booking->booking_code }}
                                    </span>
                                    <h4 class="font-bold text-base text-pitch-black mt-2">{{ $booking->venue?->name }}</h4>
                                    <p class="text-xs text-on-surface-variant flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[13px] text-electric-blue">sports_soccer</span>
                                        {{ $firstItem?->court?->name ?? 'Lapangan' }}
                                    </p>
                                </div>

                                <div>
                                    @if($isOngoing)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-xl bg-green-50 text-field-green border border-green-200">
                                            <span class="w-2 h-2 rounded-full bg-field-green animate-ping"></span>
                                            Sedang Bermain
                                        </span>
                                    @else
                                        <x-booking-status-badge :booking="$booking"/>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs bg-surface-light p-3 rounded-xl border border-stadium-gray/60 mb-4">
                                <div>
                                    <p class="text-on-surface-variant font-medium">Tanggal</p>
                                    <p class="font-bold text-pitch-black mt-0.5">
                                        {{ $firstItem ? \Carbon\Carbon::parse($firstItem->booking_date)->format('d M Y') : '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-on-surface-variant font-medium">Waktu Main</p>
                                    <p class="font-bold text-electric-blue mt-0.5">
                                        {{ $firstItem?->start_time }} – {{ $lastItem?->end_time }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <div>
                                    @if($isOngoing)
                                        @php
                                            $endDt = \Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time);
                                        @endphp
                                        <div class="flex items-center gap-1.5 text-xs text-field-green font-bold"
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
                                             x-init="tick(); setInterval(()=>tick(),1000)">
                                            <span class="material-symbols-outlined text-[15px]">timer</span>
                                            Sisa: <span x-text="t" class="font-mono"></span>
                                        </div>
                                    @elseif($booking->checked_in)
                                        <span class="inline-flex items-center gap-1 text-xs text-field-green font-bold">
                                            <span class="material-symbols-outlined text-[15px]">check_circle</span> Sudah Check-in
                                        </span>
                                    @else
                                        <span class="text-xs text-on-surface-variant">Tunjukkan QR di lokasi</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2">
                                    @if(!$booking->checked_in && $sv === 'confirmed')
                                        <a href="{{ route('checkin.show', $booking) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-electric-blue text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition shadow-sm">
                                            <span class="material-symbols-outlined text-[14px]">qr_code_scanner</span>
                                            QR Tiket
                                        </a>
                                    @endif
                                    <a href="{{ route('bookings.show', $booking) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 text-pitch-black text-xs font-bold rounded-xl hover:bg-slate-200 transition">
                                        Detail
                                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Recent Bookings Table / List --}}
            <div class="bg-white border border-stadium-gray rounded-2xl sporty-shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-stadium-gray flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-pitch-black flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-electric-blue">history</span>
                            Riwayat Booking Terbaru
                        </h3>
                    </div>
                    <a href="{{ route('bookings.index') }}" class="text-xs text-electric-blue font-bold hover:underline inline-flex items-center gap-0.5">
                        Lihat Semua Transaksi
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>

                @if($recentBookings->isEmpty())
                    <div class="py-16 text-center text-on-surface-variant">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <span class="material-symbols-outlined text-[32px] text-slate-400">sports_soccer</span>
                        </div>
                        <p class="text-base font-bold text-pitch-black">Belum ada riwayat booking</p>
                        <p class="text-xs text-on-surface-variant mt-1 mb-5">Mulai cari lapangan favoritmu dan jadwalkan sesi permainan pertamamu!</p>
                        <a href="{{ route('venues.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-electric-blue text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition">
                            <span class="material-symbols-outlined text-[16px]">search</span>
                            Jelajahi Lapangan
                        </a>
                    </div>
                @else
                    {{-- Mobile View --}}
                    <div class="sm:hidden divide-y divide-stadium-gray/60">
                        @foreach($recentBookings as $b)
                        @php $sv = $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : $b->status; @endphp
                        <a href="{{ route('bookings.show', $b) }}"
                           class="block p-4 hover:bg-slate-50 transition">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-mono text-xs font-bold text-electric-blue">{{ $b->booking_code }}</span>
                                <x-booking-status-badge :booking="$b"/>
                            </div>
                            <p class="text-sm font-bold text-pitch-black">{{ $b->venue?->name ?? '-' }}</p>
                            <div class="flex items-center justify-between text-xs text-on-surface-variant mt-2 pt-2 border-t border-stadium-gray/60">
                                <span>{{ $b->items->first() ? \Carbon\Carbon::parse($b->items->first()->booking_date)->format('d M Y') : '-' }}</span>
                                <span class="font-bold text-pitch-black">Rp {{ number_format($b->total_amount - $b->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>

                    {{-- Desktop View --}}
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full text-left">
                            <thead>
                                <tr class="bg-surface-light border-b border-stadium-gray">
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Kode Booking</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Venue</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Tanggal & Waktu</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-center">Status</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-right">Total</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stadium-gray/60">
                                @foreach($recentBookings as $b)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('bookings.show', $b) }}" class="font-mono text-xs font-bold text-electric-blue hover:underline">
                                            {{ $b->booking_code }}
                                        </a>
                                        <p class="text-[11px] text-on-surface-variant mt-0.5">{{ $b->created_at->format('d/m/Y H:i') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-pitch-black">{{ $b->venue?->name ?? '-' }}</p>
                                        <p class="text-xs text-on-surface-variant">{{ $b->items->first()?->court?->name }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <p class="font-semibold text-pitch-black">{{ $b->items->first() ? \Carbon\Carbon::parse($b->items->first()->booking_date)->format('d M Y') : '-' }}</p>
                                        <p class="text-on-surface-variant mt-0.5">{{ $b->items->first()?->start_time }} – {{ $b->items->last()?->end_time }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <x-booking-status-badge :booking="$b"/>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-sm font-bold text-pitch-black">
                                            Rp {{ number_format($b->total_amount - $b->discount_amount, 0, ',', '.') }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('bookings.show', $b) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-electric-blue bg-blue-50 rounded-xl hover:bg-blue-100 transition">
                                            Detail
                                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($recentBookings, 'links') && $recentBookings->hasPages())
                        <div class="px-6 py-4 border-t border-stadium-gray bg-surface-light">
                            {{ $recentBookings->links() }}
                        </div>
                    @endif
                @endif
            </div>
        @endif

        </div>
    </div>
</x-app-layout>
