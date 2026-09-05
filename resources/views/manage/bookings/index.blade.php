<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-pitch-black">Manajemen Booking</h1>
        <p class="text-sm text-on-surface-variant mt-0.5">Kelola semua transaksi booking di venue Anda</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach([
                    ['label'=>'Total','value'=>$stats['total'],'bar'=>'bg-slate-400'],
                    ['label'=>'Pending','value'=>$stats['pending'],'bar'=>'bg-amber-500'],
                    ['label'=>'Konfirmasi','value'=>$stats['confirmed'],'bar'=>'bg-sky-500'],
                    ['label'=>'Hari Ini','value'=>$stats['today'],'bar'=>'bg-electric-blue'],
                ] as $stat)
                    <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-3 sm:p-4 flex items-center gap-3">
                        <div class="w-1.5 h-10 rounded-full {{ $stat['bar'] }}"></div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-0.5">{{ $stat['label'] }}</p>
                            <p class="text-xl sm:text-2xl font-bold text-pitch-black">{{ number_format($stat['value']) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Status tab filter --}}
            <div class="flex flex-wrap gap-2">
                @php
                    $tabs = [
                        ''          => ['label' => 'Semua',           'count' => $stats['total'],     'cls' => 'text-slate-600 bg-slate-100'],
                        'pending'   => ['label' => 'Menunggu Bayar',  'count' => $stats['pending'],   'cls' => 'text-amber-700 bg-amber-50'],
                        'confirmed' => ['label' => 'Sudah Lunas',     'count' => $stats['confirmed'], 'cls' => 'text-emerald-700 bg-emerald-50'],
                        'playing'   => ['label' => 'Sedang Bermain',  'count' => $stats['playing'],   'cls' => 'text-teal-700 bg-teal-50'],
                        'completed' => ['label' => 'Selesai Bermain', 'count' => $stats['completed'], 'cls' => 'text-blue-700 bg-blue-50'],
                        'cancelled' => ['label' => 'Dibatalkan',      'count' => $stats['cancelled'], 'cls' => 'text-red-700 bg-red-50'],
                        'expired'   => ['label' => 'Kedaluwarsa',     'count' => $stats['expired'],   'cls' => 'text-slate-600 bg-slate-100'],
                    ];
                @endphp
                @foreach($tabs as $val => $tab)
                    @php $active = (string) request('status') === $val; @endphp
                    <a href="{{ $val === '' ? route('manage.bookings.index', request()->except(['status','page'])) : route('manage.bookings.index', array_merge(request()->except(['status','page']), ['status' => $val])) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold border transition
                              {{ $active
                                  ? 'bg-electric-blue text-white border-electric-blue shadow-lg shadow-blue-500/20'
                                  : 'bg-white text-on-surface-variant border-stadium-gray hover:border-electric-blue hover:text-electric-blue' }}">
                        {{ $tab['label'] }}
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1 rounded-full text-xs
                                     {{ $active ? 'bg-white/20 text-white' : $tab['cls'] }}">{{ number_format($tab['count']) }}</span>
                    </a>
                @endforeach
            </div>

            <form method="GET" class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-4 space-y-3 sm:space-y-0 sm:flex sm:flex-wrap sm:items-end sm:gap-3">
                <div class="flex-1 min-w-0">
                    <label class="block text-xs font-medium text-on-surface-variant mb-1">Cari</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] text-on-surface-variant">search</span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Kode / nama customer..."
                               class="w-full pl-9 pr-3 py-2 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-electric-blue text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">Filter</button>
                    @if(request('status') || request('search'))
                        <a href="{{ route('manage.bookings.index') }}" class="px-4 py-2 bg-slate-100 text-on-surface-variant text-sm font-medium rounded-xl hover:bg-slate-200 transition">Reset</a>
                    @endif
                </div>
            </form>

            @if($bookings->isEmpty())
                <div class="bg-white border border-dashed border-stadium-gray rounded-2xl shadow-sm py-16 text-center">
                    <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="material-symbols-outlined text-[28px] text-on-surface-variant">calendar_month</span>
                    </div>
                    <p class="text-on-surface-variant text-sm">Tidak ada booking ditemukan.</p>
                </div>
            @else
                <div class="md:hidden space-y-3">
                    @foreach($bookings as $b)
                        @php
                            $sv = $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : $b->status;
                            $now = now(); $lastItem = $b->items->last(); $firstItem = $b->items->first();
                            $isOngoing = $sv === 'confirmed' && $b->checked_in && $firstItem && $lastItem
                                && $now->gte(\Carbon\Carbon::parse($firstItem->booking_date)->setTimeFromTimeString($firstItem->start_time))
                                && $now->lt(\Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time));
                            $badgeCls = match(true) { $isOngoing=>'bg-teal-50 text-teal-700', $sv==='pending'=>'bg-amber-50 text-amber-700', $sv==='confirmed'=>'bg-blue-50 text-electric-blue', $sv==='completed'=>'bg-blue-50 text-electric-blue', $sv==='cancelled'=>'bg-red-50 text-red-700', default=>'bg-slate-100 text-slate-600' };
                            $badgeLbl = match(true) { $isOngoing=>'Sedang Bermain', $sv==='pending'=>'Pending', $sv==='confirmed'=>'Konfirmasi', $sv==='completed'=>'Selesai Bermain', $sv==='cancelled'=>'Batal', default=>ucfirst($sv) };
                        @endphp
                        <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-4">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div>
                                    <p class="font-mono text-xs font-bold text-electric-blue">{{ $b->booking_code }}</p>
                                    <p class="text-sm font-semibold text-pitch-black mt-0.5">{{ $b->user?->name }}</p>
                                    <p class="text-xs text-on-surface-variant truncate max-w-[200px]">{{ $b->user?->email }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full {{ $badgeCls }} shrink-0">
                                    @if($isOngoing)<span class="w-1.5 h-1.5 rounded-full bg-electric-blue animate-pulse"></span>@endif
                                    {{ $badgeLbl }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                                <div>
                                    <p class="text-on-surface-variant">Lapangan</p>
                                    <p class="font-medium text-on-surface-variant truncate">{{ $b->items->first()?->court?->name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-on-surface-variant">Tanggal</p>
                                    <p class="font-medium text-on-surface-variant">{{ $b->items->first() ? \Carbon\Carbon::parse($b->items->first()->booking_date)->format('d M Y') : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-on-surface-variant">Waktu</p>
                                    <p class="font-medium text-on-surface-variant">{{ $b->items->first()?->start_time }} – {{ $b->items->last()?->end_time }}</p>
                                </div>
                                <div>
                                    <p class="text-on-surface-variant">Total</p>
                                    <p class="font-semibold text-pitch-black">Rp {{ number_format($b->total_amount - $b->discount_amount, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <a href="{{ route('manage.bookings.show', $b) }}"
                               class="block w-full text-center py-2 text-sm font-medium text-electric-blue bg-blue-50 rounded-xl hover:bg-blue-700/20 transition">
                                Lihat Detail →
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="hidden md:block bg-white border border-stadium-gray rounded-xl sporty-shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-white border-b border-stadium-gray">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Booking</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Customer</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Lapangan</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Tanggal</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Total</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#1e2638]">
                                @foreach($bookings as $b)
                                    @php
                                        $sv = $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : $b->status;
                                        $now = now(); $lastItem = $b->items->last(); $firstItem = $b->items->first();
                                        $isOngoing = $sv === 'confirmed' && $b->checked_in && $firstItem && $lastItem
                                            && $now->gte(\Carbon\Carbon::parse($firstItem->booking_date)->setTimeFromTimeString($firstItem->start_time))
                                            && $now->lt(\Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time));
                                        $badgeCls = match(true) { $isOngoing=>'bg-teal-50 text-teal-700', $sv==='pending'=>'bg-amber-50 text-amber-700', $sv==='confirmed'=>'bg-blue-50 text-electric-blue', $sv==='completed'=>'bg-blue-50 text-electric-blue', $sv==='cancelled'=>'bg-red-50 text-red-700', default=>'bg-slate-100 text-slate-600' };
                                        $badgeLbl = match(true) { $isOngoing=>'Sedang Bermain', $sv==='pending'=>'Pending', $sv==='confirmed'=>'Konfirmasi', $sv==='completed'=>'Selesai Bermain', $sv==='cancelled'=>'Batal', default=>ucfirst($sv) };
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="px-5 py-4">
                                            <p class="font-mono text-xs font-bold text-electric-blue">{{ $b->booking_code }}</p>
                                            <p class="text-xs text-on-surface-variant mt-0.5">{{ $b->created_at->format('d/m/Y') }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm font-medium text-pitch-black">{{ $b->user?->name }}</p>
                                            <p class="text-xs text-on-surface-variant truncate max-w-[150px]">{{ $b->user?->email }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm text-on-surface-variant">{{ $b->items->first()?->court?->name ?? '-' }}</p>
                                            <p class="text-xs text-on-surface-variant">{{ $b->venue?->name }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm text-on-surface-variant">{{ $b->items->first() ? \Carbon\Carbon::parse($b->items->first()->booking_date)->format('d M Y') : '-' }}</p>
                                            <p class="text-xs text-on-surface-variant">{{ $b->items->first()?->start_time }} – {{ $b->items->last()?->end_time }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm font-semibold text-pitch-black">Rp {{ number_format($b->total_amount - $b->discount_amount, 0, ',', '.') }}</p>
                                            @if($b->discount_amount > 0)<p class="text-xs text-electric-blue">- Rp {{ number_format($b->discount_amount, 0, ',', '.') }}</p>@endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $badgeCls }}">
                                                @if($isOngoing)<span class="w-1.5 h-1.5 rounded-full bg-electric-blue animate-pulse"></span>@endif
                                                {{ $badgeLbl }}
                                            </span>
                                            @if($b->checked_in && !$isOngoing)<p class="text-xs text-electric-blue mt-1 flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">check_circle</span> {{ $b->checked_in_at?->format('H:i') }}</p>@endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <a href="{{ route('manage.bookings.show', $b) }}"
                                               class="inline-flex items-center gap-1 text-sm text-electric-blue hover:text-electric-blue font-medium">
                                                Detail <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($bookings->hasPages())
                        <div class="px-5 py-4 border-t border-stadium-gray">{{ $bookings->links() }}</div>
                    @endif
                </div>
                @if($bookings->hasPages())
                    <div class="md:hidden">{{ $bookings->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
