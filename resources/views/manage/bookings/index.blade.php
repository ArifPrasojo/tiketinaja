<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-pitch-black">Manajemen Transaksi</h1>
                <p class="text-sm text-on-surface-variant mt-0.5">Pantau seluruh riwayat pemesanan, pembayaran, dan jadwal bermain</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-electric-blue border border-blue-200/60 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-electric-blue animate-pulse"></span>
                    {{ number_format($stats['total']) }} Total Transaksi
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Metric Summary Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Hari Ini</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($stats['today']) }}</p>
                        <span class="inline-block mt-1 text-[11px] font-bold text-electric-blue">Jadwal main hari ini</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-electric-blue flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">today</span>
                    </div>
                </div>

                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Menunggu Bayar</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($stats['pending']) }}</p>
                        <span class="inline-block mt-1 text-[11px] font-bold text-amber-600">Perlu diselesaikan</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">hourglass_top</span>
                    </div>
                </div>

                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Sedang Bermain</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($stats['playing']) }}</p>
                        <span class="inline-block mt-1 text-[11px] font-bold text-teal-600">Sesi berlangsung</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">sprint</span>
                    </div>
                </div>

                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Sudah Lunas</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($stats['confirmed']) }}</p>
                        <span class="inline-block mt-1 text-[11px] font-bold text-field-green">Pembayaran valid</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-field-green flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">verified</span>
                    </div>
                </div>
            </div>

            {{-- Status Tabs Bar --}}
            <div class="flex flex-wrap gap-2">
                @php
                    $tabs = [
                        ''          => ['label' => 'Semua',           'count' => $stats['total'],     'cls' => 'bg-slate-100 text-slate-700',   'activeBg' => 'bg-electric-blue text-white'],
                        'pending'   => ['label' => 'Menunggu Bayar',  'count' => $stats['pending'],   'cls' => 'bg-amber-50 text-amber-700',    'activeBg' => 'bg-amber-500 text-white'],
                        'confirmed' => ['label' => 'Sudah Lunas',     'count' => $stats['confirmed'], 'cls' => 'bg-emerald-50 text-emerald-700','activeBg' => 'bg-field-green text-white'],
                        'playing'   => ['label' => 'Sedang Bermain',  'count' => $stats['playing'],   'cls' => 'bg-teal-50 text-teal-700',      'activeBg' => 'bg-teal-600 text-white'],
                        'completed' => ['label' => 'Selesai Bermain', 'count' => $stats['completed'], 'cls' => 'bg-blue-50 text-blue-700',      'activeBg' => 'bg-blue-600 text-white'],
                        'cancelled' => ['label' => 'Dibatalkan',      'count' => $stats['cancelled'], 'cls' => 'bg-red-50 text-red-700',        'activeBg' => 'bg-red-500 text-white'],
                        'expired'   => ['label' => 'Kedaluwarsa',     'count' => $stats['expired'],   'cls' => 'bg-slate-100 text-slate-600',   'activeBg' => 'bg-slate-600 text-white'],
                    ];
                @endphp
                @foreach($tabs as $val => $tab)
                    @php $active = (string) request('status') === $val; @endphp
                    <a href="{{ $val === '' ? route('manage.bookings.index', request()->except(['status','page'])) : route('manage.bookings.index', array_merge(request()->except(['status','page']), ['status' => $val])) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold border transition-all duration-200
                              {{ $active
                                  ? $tab['activeBg'] . ' border-transparent shadow-md'
                                  : 'bg-white text-on-surface-variant border-stadium-gray hover:border-electric-blue hover:text-electric-blue' }}">
                        {{ $tab['label'] }}
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-xs font-bold
                                     {{ $active ? 'bg-white/25 text-white' : $tab['cls'] }}">{{ number_format($tab['count']) }}</span>
                    </a>
                @endforeach
            </div>

            {{-- Filter Bar (Search + Date Picker) --}}
            <form method="GET" class="bg-white border border-stadium-gray rounded-2xl sporty-shadow p-4">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Cari Transaksi</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant">search</span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Kode booking atau nama customer..."
                                   class="w-full pl-10 pr-4 py-2.5 text-sm bg-surface-light border border-stadium-gray text-pitch-black rounded-xl focus:border-electric-blue focus:bg-white outline-none transition placeholder:text-on-surface-variant">
                        </div>
                    </div>
                    <div class="min-w-[150px]">
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">
                            <span class="material-symbols-outlined text-[14px] align-text-bottom">calendar_today</span>
                            Dari Tanggal
                        </label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full px-3 py-2 text-sm bg-surface-light border border-stadium-gray text-pitch-black rounded-xl focus:border-electric-blue focus:bg-white outline-none transition">
                    </div>
                    <div class="min-w-[150px]">
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">
                            <span class="material-symbols-outlined text-[14px] align-text-bottom">calendar_month</span>
                            Sampai Tanggal
                        </label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full px-3 py-2 text-sm bg-surface-light border border-stadium-gray text-pitch-black rounded-xl focus:border-electric-blue focus:bg-white outline-none transition">
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-electric-blue text-white text-sm font-bold rounded-xl hover:bg-blue-700 active:scale-95 transition shadow-sm">
                            <span class="material-symbols-outlined text-[16px]">filter_list</span>
                            Filter
                        </button>
                        @if(request('status') || request('search') || request('date_from') || request('date_to'))
                            <a href="{{ route('manage.bookings.index') }}"
                               class="inline-flex items-center gap-1 px-4 py-2.5 bg-slate-100 text-on-surface-variant text-sm font-medium rounded-xl hover:bg-slate-200 transition">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                                Reset
                            </a>
                        @endif
                    </div>
                </div>

                @if(request('date_from') || request('date_to'))
                    <div class="mt-3 pt-3 border-t border-stadium-gray flex items-center gap-2 text-xs text-on-surface-variant">
                        <span class="material-symbols-outlined text-[14px] text-electric-blue">info</span>
                        Menampilkan booking pada periode:
                        <span class="font-semibold text-pitch-black">
                            {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('d M Y') : '—' }}
                        </span>
                        sampai
                        <span class="font-semibold text-pitch-black">
                            {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('d M Y') : '—' }}
                        </span>
                    </div>
                @endif

                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
            </form>

            {{-- Table Card --}}
            <div class="bg-white border border-stadium-gray rounded-2xl sporty-shadow overflow-hidden">
                @if($bookings->isEmpty())
                    <div class="py-16 text-center text-on-surface-variant">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <span class="material-symbols-outlined text-[32px] text-slate-400">receipt_long</span>
                        </div>
                        <p class="text-base font-bold text-pitch-black">Tidak ada transaksi ditemukan</p>
                        <p class="text-xs text-on-surface-variant mt-1">Coba sesuaikan kata kunci pencarian, filter status, atau periode tanggal.</p>
                    </div>
                @else
                    {{-- Mobile Card View --}}
                    <div class="md:hidden divide-y divide-stadium-gray/60">
                        @foreach($bookings as $b)
                            @php
                                $sv = $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : $b->status;
                                $now = now(); $lastItem = $b->items->last(); $firstItem = $b->items->first();
                                $isOngoing = $sv === 'confirmed' && $b->checked_in && $firstItem && $lastItem
                                    && $now->gte(\Carbon\Carbon::parse($firstItem->booking_date)->setTimeFromTimeString($firstItem->start_time))
                                    && $now->lt(\Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time));
                                $badgeCls = match(true) { $isOngoing=>'bg-teal-50 text-teal-700 border-teal-200', $sv==='pending'=>'bg-amber-50 text-amber-700 border-amber-200', $sv==='confirmed'=>'bg-blue-50 text-electric-blue border-blue-200', $sv==='completed'=>'bg-blue-50 text-electric-blue border-blue-200', $sv==='cancelled'=>'bg-red-50 text-red-700 border-red-200', default=>'bg-slate-100 text-slate-600 border-slate-200' };
                                $badgeLbl = match(true) { $isOngoing=>'Sedang Bermain', $sv==='pending'=>'Pending', $sv==='confirmed'=>'Konfirmasi', $sv==='completed'=>'Selesai Bermain', $sv==='cancelled'=>'Batal', default=>ucfirst($sv) };
                            @endphp
                            <div class="p-4 space-y-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="font-mono text-xs font-bold text-electric-blue">{{ $b->booking_code }}</p>
                                        <p class="text-sm font-bold text-pitch-black mt-0.5">{{ $b->user?->name }}</p>
                                        <p class="text-xs text-on-surface-variant truncate max-w-[200px]">{{ $b->user?->email }}</p>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-lg border {{ $badgeCls }} shrink-0">
                                        @if($isOngoing)<span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>@endif
                                        {{ $badgeLbl }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs bg-surface-light p-3 rounded-xl border border-stadium-gray/60">
                                    <div>
                                        <p class="text-on-surface-variant font-medium">Lapangan</p>
                                        <p class="font-bold text-pitch-black truncate mt-0.5">{{ $b->items->first()?->court?->name ?? '-' }}</p>
                                        <p class="text-[11px] text-on-surface-variant">{{ $b->venue?->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-on-surface-variant font-medium">Waktu Main</p>
                                        <p class="font-bold text-pitch-black mt-0.5">{{ $b->items->first() ? \Carbon\Carbon::parse($b->items->first()->booking_date)->format('d M Y') : '-' }}</p>
                                        <p class="text-[11px] text-electric-blue font-semibold">{{ $b->items->first()?->start_time }} – {{ $b->items->last()?->end_time }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-1">
                                    <div>
                                        <p class="text-[11px] text-on-surface-variant">Total Bayar</p>
                                        <p class="text-sm font-bold text-pitch-black">Rp {{ number_format($b->total_amount - $b->discount_amount, 0, ',', '.') }}</p>
                                    </div>
                                    <a href="{{ route('manage.bookings.show', $b) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-electric-blue bg-blue-50 rounded-xl hover:bg-blue-100 transition">
                                        Detail <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop Table View --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full text-left">
                            <thead>
                                <tr class="bg-surface-light border-b border-stadium-gray">
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Kode Booking</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Venue & Lapangan</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Jadwal Sesi</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-center">Status</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stadium-gray/60">
                                @foreach($bookings as $b)
                                    @php
                                        $sv = $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : $b->status;
                                        $now = now(); $lastItem = $b->items->last(); $firstItem = $b->items->first();
                                        $isOngoing = $sv === 'confirmed' && $b->checked_in && $firstItem && $lastItem
                                            && $now->gte(\Carbon\Carbon::parse($firstItem->booking_date)->setTimeFromTimeString($firstItem->start_time))
                                            && $now->lt(\Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time));
                                        $badgeCls = match(true) { $isOngoing=>'bg-teal-50 text-teal-700 border-teal-200', $sv==='pending'=>'bg-amber-50 text-amber-700 border-amber-200', $sv==='confirmed'=>'bg-blue-50 text-electric-blue border-blue-200', $sv==='completed'=>'bg-blue-50 text-electric-blue border-blue-200', $sv==='cancelled'=>'bg-red-50 text-red-700 border-red-200', default=>'bg-slate-100 text-slate-600 border-slate-200' };
                                        $badgeLbl = match(true) { $isOngoing=>'Sedang Bermain', $sv==='pending'=>'Pending', $sv==='confirmed'=>'Konfirmasi', $sv==='completed'=>'Selesai Bermain', $sv==='cancelled'=>'Batal', default=>ucfirst($sv) };
                                    @endphp
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="font-mono text-xs font-bold text-electric-blue">{{ $b->booking_code }}</p>
                                            <p class="text-[11px] text-on-surface-variant mt-0.5">{{ $b->created_at->format('d/m/Y H:i') }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-bold text-pitch-black">{{ $b->user?->name }}</p>
                                            <p class="text-xs text-on-surface-variant truncate max-w-[160px]">{{ $b->user?->email }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-semibold text-pitch-black">{{ $b->items->first()?->court?->name ?? '-' }}</p>
                                            <p class="text-xs text-on-surface-variant flex items-center gap-1 mt-0.5">
                                                <span class="material-symbols-outlined text-[13px]">stadium</span>
                                                {{ $b->venue?->name }}
                                            </p>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <p class="font-semibold text-pitch-black text-xs">{{ $b->items->first() ? \Carbon\Carbon::parse($b->items->first()->booking_date)->format('d M Y') : '-' }}</p>
                                            <p class="text-xs text-electric-blue font-bold mt-0.5">{{ $b->items->first()?->start_time }} – {{ $b->items->last()?->end_time }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-bold text-pitch-black">Rp {{ number_format($b->total_amount - $b->discount_amount, 0, ',', '.') }}</p>
                                            @if($b->discount_amount > 0)
                                                <span class="inline-block text-[11px] font-semibold text-field-green mt-0.5">
                                                    - Rp {{ number_format($b->discount_amount, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-lg border {{ $badgeCls }}">
                                                @if($isOngoing)<span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>@endif
                                                {{ $badgeLbl }}
                                            </span>
                                            @if($b->checked_in && !$isOngoing)
                                                <p class="text-[11px] text-field-green font-semibold mt-1 flex items-center justify-center gap-0.5">
                                                    <span class="material-symbols-outlined text-[13px]">check_circle</span>
                                                    {{ $b->checked_in_at?->format('H:i') }}
                                                </p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('manage.bookings.show', $b) }}"
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

                    @if($bookings->hasPages())
                        <div class="px-6 py-4 border-t border-stadium-gray bg-surface-light">
                            {{ $bookings->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
