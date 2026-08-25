<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Booking</h1>
        <p class="text-sm text-gray-500 mt-0.5">Kelola semua transaksi booking di venue Anda</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Stats row --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach([
                    ['label'=>'Total Booking','value'=>$stats['total'],'color'=>'text-gray-900','bg'=>'bg-gray-50 border-gray-200'],
                    ['label'=>'Pending','value'=>$stats['pending'],'color'=>'text-yellow-700','bg'=>'bg-yellow-50 border-yellow-200'],
                    ['label'=>'Terkonfirmasi','value'=>$stats['confirmed'],'color'=>'text-blue-700','bg'=>'bg-blue-50 border-blue-200'],
                    ['label'=>'Booking Hari Ini','value'=>$stats['today'],'color'=>'text-indigo-700','bg'=>'bg-indigo-50 border-indigo-200'],
                ] as $stat)
                    <div class="{{ $stat['bg'] }} border rounded-2xl p-4">
                        <p class="text-xs text-gray-500 mb-1">{{ $stat['label'] }}</p>
                        <p class="text-2xl font-bold {{ $stat['color'] }}">{{ number_format($stat['value']) }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Filter bar --}}
            <form method="GET"
                  class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4 flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Kode booking / nama customer..."
                               class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                    </div>
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                        <option value="">Semua Status</option>
                        @foreach(['pending'=>'Pending','confirmed'=>'Terkonfirmasi','completed'=>'Selesai','cancelled'=>'Dibatalkan','expired'=>'Kedaluwarsa'] as $val => $lbl)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
                        Filter
                    </button>
                    @if(request('status') || request('search'))
                        <a href="{{ route('manage.bookings.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Table --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                @if($bookings->isEmpty())
                    <div class="py-16 text-center">
                        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <p class="text-gray-500 text-sm">Tidak ada booking ditemukan.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Booking</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Lapangan</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($bookings as $b)
                                    @php
                                        $sv = $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : $b->status;
                                        $now = now();
                                        $lastItem = $b->items->last();
                                        $isOngoing = $sv === 'confirmed'
                                            && $b->checked_in
                                            && $lastItem
                                            && $now->lt(\Carbon\Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time));
                                        $badgeCls = match(true) {
                                            $isOngoing          => 'bg-indigo-100 text-indigo-700',
                                            $sv === 'pending'   => 'bg-yellow-100 text-yellow-700',
                                            $sv === 'confirmed' => 'bg-blue-100 text-blue-700',
                                            $sv === 'completed' => 'bg-green-100 text-green-700',
                                            $sv === 'cancelled' => 'bg-red-100 text-red-700',
                                            default             => 'bg-gray-100 text-gray-500',
                                        };
                                        $badgeLbl = match(true) {
                                            $isOngoing          => 'Sedang Bermain',
                                            $sv === 'pending'   => 'Pending',
                                            $sv === 'confirmed' => 'Konfirmasi',
                                            $sv === 'completed' => 'Selesai',
                                            $sv === 'cancelled' => 'Batal',
                                            default             => ucfirst($sv),
                                        };
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-5 py-4">
                                            <p class="font-mono text-xs font-bold text-indigo-600">{{ $b->booking_code }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $b->created_at->format('d/m/Y') }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm font-medium text-gray-900">{{ $b->user?->name }}</p>
                                            <p class="text-xs text-gray-400 truncate max-w-[150px]">{{ $b->user?->email }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm text-gray-700">{{ $b->items->first()?->court?->name ?? '-' }}</p>
                                            <p class="text-xs text-gray-400">{{ $b->venue?->name }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm text-gray-700">
                                                {{ $b->items->first() ? \Carbon\Carbon::parse($b->items->first()->booking_date)->format('d M Y') : '-' }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ $b->items->first()?->start_time }} – {{ $b->items->last()?->end_time }}
                                            </p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($b->total_amount - $b->discount_amount, 0, ',', '.') }}</p>
                                            @if($b->discount_amount > 0)
                                                <p class="text-xs text-green-600">- Rp {{ number_format($b->discount_amount, 0, ',', '.') }}</p>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $badgeCls }}">
                                                @if($isOngoing)
                                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                                @endif
                                                {{ $badgeLbl }}
                                            </span>
                                            @if($b->checked_in && !$isOngoing)
                                                <p class="text-xs text-emerald-600 mt-1">✓ Check-in {{ $b->checked_in_at?->format('H:i') }}</p>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <a href="{{ route('manage.bookings.show', $b) }}"
                                               class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                                Detail
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($bookings->hasPages())
                        <div class="px-5 py-4 border-t border-gray-100">
                            {{ $bookings->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
