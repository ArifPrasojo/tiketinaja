<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-pitch-black">Manajemen Promo</h1>
                <p class="text-sm text-on-surface-variant mt-0.5">Kelola kode diskon, voucher potongan harga, dan kuota promosi</p>
            </div>
            <a href="{{ route('manage.promos.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-electric-blue text-white text-sm font-bold rounded-xl hover:bg-blue-700 active:scale-95 transition shadow-sm self-start sm:self-auto">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Promo Baru
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Metric Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Total Promo</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($stats['total']) }}</p>
                        <p class="text-xs text-on-surface-variant mt-1">Kode voucher terdaftar</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-electric-blue flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">local_activity</span>
                    </div>
                </div>

                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Promo Aktif</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($stats['active']) }}</p>
                        <span class="inline-block mt-1 text-[11px] font-bold text-field-green">Siap digunakan customer</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-field-green flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">check_circle</span>
                    </div>
                </div>

                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Total Penggunaan</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($stats['total_used']) }}x</p>
                        <span class="inline-block mt-1 text-[11px] font-bold text-athletic-orange">Klaim transaksi berhasil</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-orange-50 text-athletic-orange flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">redeem</span>
                    </div>
                </div>
            </div>

            {{-- Table / Cards Container --}}
            <div class="bg-white border border-stadium-gray rounded-2xl sporty-shadow overflow-hidden">
                @if($promos->isEmpty())
                    <div class="py-16 text-center text-on-surface-variant">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <span class="material-symbols-outlined text-[32px] text-slate-400">local_activity</span>
                        </div>
                        <p class="text-base font-bold text-pitch-black">Belum ada promo</p>
                        <p class="text-xs text-on-surface-variant mt-1 mb-5">Tambahkan kode promo pertama Anda untuk menarik lebih banyak customer.</p>
                        <a href="{{ route('manage.promos.create') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-electric-blue text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition">
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            Tambah Promo Pertama
                        </a>
                    </div>
                @else
                    {{-- Mobile View --}}
                    <div class="md:hidden divide-y divide-stadium-gray/60">
                        @foreach($promos as $promo)
                            @php $valid = $promo->isValid(); @endphp
                            <div class="p-4 space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-base font-bold text-electric-blue bg-blue-50 px-2 py-0.5 rounded-lg border border-blue-200">
                                                {{ $promo->code }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-md
                                                {{ $valid ? 'bg-green-50 text-field-green border border-green-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                                {{ $valid ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-on-surface-variant mt-1.5">{{ $promo->description }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-pitch-black">
                                            @if($promo->type === 'percentage')
                                                {{ $promo->value }}%
                                            @else
                                                Rp {{ number_format($promo->value, 0, ',', '.') }}
                                            @endif
                                        </p>
                                        @if($promo->max_discount)
                                            <p class="text-[10px] text-on-surface-variant">Maks Rp {{ number_format($promo->max_discount, 0, ',', '.') }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-xs bg-surface-light p-3 rounded-xl border border-stadium-gray/60">
                                    <div>
                                        <p class="text-on-surface-variant font-medium">Cakupan Venue</p>
                                        <p class="font-semibold text-pitch-black mt-0.5 truncate">{{ $promo->venue?->name ?? 'Semua Venue' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-on-surface-variant font-medium">Penggunaan</p>
                                        <p class="font-semibold text-pitch-black mt-0.5">{{ $promo->used_count }} / {{ $promo->max_uses ?? '∞' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-on-surface-variant font-medium">Mulai</p>
                                        <p class="font-semibold text-pitch-black mt-0.5">{{ $promo->starts_at ? $promo->starts_at->format('d/m/Y') : 'Sekarang' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-on-surface-variant font-medium">Berakhir</p>
                                        <p class="font-semibold text-pitch-black mt-0.5">{{ $promo->expires_at ? $promo->expires_at->format('d/m/Y') : 'Selamanya' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 pt-1">
                                    <a href="{{ route('manage.promos.edit', $promo) }}"
                                       class="flex-1 text-center py-2 text-xs font-bold text-electric-blue bg-blue-50 rounded-xl hover:bg-blue-100 transition">
                                        Edit Promo
                                    </a>
                                    <form method="POST" action="{{ route('manage.promos.destroy', $promo) }}"
                                          onsubmit="return confirm('Hapus promo {{ $promo->code }}?')" class="shrink-0">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-2 text-xs font-bold text-red-500 bg-red-50 rounded-xl hover:bg-red-100 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop Table View --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full text-left">
                            <thead>
                                <tr class="bg-surface-light border-b border-stadium-gray">
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Kode & Deskripsi</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Nilai Potongan</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Berlaku Untuk</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-center">Penggunaan</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Periode Aktif</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-center">Status</th>
                                    <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stadium-gray/60">
                                @foreach($promos as $promo)
                                    @php $valid = $promo->isValid(); @endphp
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        {{-- Code & Description --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-9 h-9 rounded-xl bg-blue-50 text-electric-blue flex items-center justify-center shrink-0">
                                                    <span class="material-symbols-outlined text-[20px]">local_activity</span>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-mono text-sm font-bold text-electric-blue tracking-wider">{{ $promo->code }}</p>
                                                    <p class="text-xs text-on-surface-variant mt-0.5 truncate max-w-[200px]">{{ $promo->description }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Discount Value --}}
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-bold text-pitch-black">
                                                @if($promo->type === 'percentage')
                                                    {{ $promo->value }}%
                                                @else
                                                    Rp {{ number_format($promo->value, 0, ',', '.') }}
                                                @endif
                                            </p>
                                            @if($promo->max_discount)
                                                <p class="text-[11px] text-on-surface-variant mt-0.5">
                                                    Maks: Rp {{ number_format($promo->max_discount, 0, ',', '.') }}
                                                </p>
                                            @endif
                                            @if($promo->min_booking_amount > 0)
                                                <p class="text-[11px] text-on-surface-variant">
                                                    Min: Rp {{ number_format($promo->min_booking_amount, 0, ',', '.') }}
                                                </p>
                                            @endif
                                        </td>

                                        {{-- Venue Scope --}}
                                        <td class="px-6 py-4 text-sm">
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-pitch-black">
                                                <span class="material-symbols-outlined text-[14px] text-on-surface-variant">stadium</span>
                                                {{ $promo->venue?->name ?? 'Semua Venue' }}
                                            </span>
                                        </td>

                                        {{-- Usage --}}
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-bold bg-slate-100 text-pitch-black">
                                                {{ $promo->used_count }} / {{ $promo->max_uses ?? '∞' }}
                                            </span>
                                        </td>

                                        {{-- Period --}}
                                        <td class="px-6 py-4 text-xs text-on-surface-variant">
                                            <p class="text-pitch-black font-medium">{{ $promo->starts_at ? $promo->starts_at->format('d M Y') : 'Sekarang' }}</p>
                                            <p class="text-[11px] mt-0.5">s/d {{ $promo->expires_at ? $promo->expires_at->format('d M Y') : 'Selamanya' }}</p>
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-lg border
                                                {{ $valid ? 'bg-green-50 text-field-green border-green-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $valid ? 'bg-field-green' : 'bg-slate-400' }}"></span>
                                                {{ $valid ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-6 py-4 text-right">
                                            <div class="inline-flex items-center gap-1">
                                                <a href="{{ route('manage.promos.edit', $promo) }}"
                                                   title="Edit Promo"
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-electric-blue hover:bg-blue-50 transition-colors">
                                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                                </a>
                                                <form method="POST" action="{{ route('manage.promos.destroy', $promo) }}"
                                                      onsubmit="return confirm('Hapus promo {{ $promo->code }}?')"
                                                      class="inline-block">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            title="Hapus Promo"
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-red-500 hover:text-white hover:bg-red-500 transition-colors">
                                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($promos->hasPages())
                        <div class="px-6 py-4 border-t border-stadium-gray bg-surface-light">
                            {{ $promos->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
