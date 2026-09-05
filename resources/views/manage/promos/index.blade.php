<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-pitch-black">Manajemen Promo</h1>
                <p class="text-sm text-on-surface-variant mt-0.5">Kelola kode diskon untuk venue Anda</p>
            </div>
            <a href="{{ route('manage.promos.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-electric-blue text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                <span class="material-symbols-outlined text-[16px]">add</span>
                Tambah Promo
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($promos->isEmpty())
                <div class="bg-white rounded-xl border border-dashed border-stadium-gray p-12 text-center">
                    <p class="mb-2 text-stadium-gray"><span class="material-symbols-outlined text-[48px]">local_activity</span></p>
                    <h3 class="text-base font-semibold text-pitch-black mb-1">Belum ada promo</h3>
                    <p class="text-sm text-on-surface-variant mb-4">Tambahkan kode promo untuk menarik lebih banyak customer.</p>
                    <a href="{{ route('manage.promos.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-electric-blue text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                        Tambah Promo Pertama
                    </a>
                </div>
            @else
                <div class="md:hidden space-y-3">
                    @foreach($promos as $promo)
                        <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-4">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div>
                                    <span class="font-mono text-base font-bold text-electric-blue">{{ $promo->code }}</span>
                                    <p class="text-xs text-on-surface-variant mt-0.5">{{ $promo->description }}</p>
                                </div>
                                @if($promo->isValid())
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-electric-blue shrink-0">Aktif</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-500/15 text-on-surface-variant shrink-0">Nonaktif</span>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                                <div>
                                    <p class="text-on-surface-variant">Diskon</p>
                                    <p class="font-semibold text-electric-blue">
                                        @if($promo->type === 'percentage')
                                            {{ $promo->value }}%
                                            @if($promo->max_discount)(maks Rp {{ number_format($promo->max_discount, 0, ',', '.') }})@endif
                                        @else
                                            Rp {{ number_format($promo->value, 0, ',', '.') }}
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-on-surface-variant">Venue</p>
                                    <p class="font-medium text-on-surface-variant truncate">{{ $promo->venue?->name ?? 'Semua Venue' }}</p>
                                </div>
                                <div>
                                    <p class="text-on-surface-variant">Penggunaan</p>
                                    <p class="font-medium text-on-surface-variant">{{ $promo->used_count }} / {{ $promo->max_uses ?? '∞' }}</p>
                                </div>
                                <div>
                                    <p class="text-on-surface-variant">Berakhir</p>
                                    <p class="font-medium text-on-surface-variant">{{ $promo->expires_at ? $promo->expires_at->format('d/m/Y') : '-' }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2 pt-3 border-t border-stadium-gray">
                                <a href="{{ route('manage.promos.edit', $promo) }}"
                                   class="flex-1 text-center py-2 text-sm font-medium text-electric-blue bg-blue-50 rounded-xl hover:bg-blue-700/20 transition">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('manage.promos.destroy', $promo) }}"
                                      onsubmit="return confirm('Hapus promo {{ $promo->code }}?')" class="flex-1">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-full py-2 text-sm font-medium text-red-400 bg-red-500/10 border border-red-500/20 rounded-xl hover:bg-red-500/10 transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="hidden md:block bg-white border border-stadium-gray rounded-xl sporty-shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#1e2638]">
                            <thead class="bg-white border-b border-stadium-gray">
                                <tr>
                                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Kode</th>
                                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Deskripsi</th>
                                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Diskon</th>
                                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Venue</th>
                                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Penggunaan</th>
                                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Berakhir</th>
                                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Status</th>
                                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#1e2638]">
                                @foreach($promos as $promo)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="px-5 py-4 font-mono text-sm font-bold text-electric-blue whitespace-nowrap">{{ $promo->code }}</td>
                                        <td class="px-5 py-4 text-sm text-on-surface-variant max-w-[200px] truncate">{{ $promo->description }}</td>
                                        <td class="px-5 py-4 text-sm text-on-surface-variant whitespace-nowrap">
                                            @if($promo->type === 'percentage')
                                                <span class="font-semibold text-electric-blue">{{ $promo->value }}%</span>
                                                @if($promo->max_discount)
                                                    <span class="text-xs text-on-surface-variant block">maks Rp {{ number_format($promo->max_discount, 0, ',', '.') }}</span>
                                                @endif
                                            @else
                                                <span class="font-semibold text-electric-blue">Rp {{ number_format($promo->value, 0, ',', '.') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-sm text-on-surface-variant max-w-[140px] truncate">{{ $promo->venue?->name ?? 'Semua Venue' }}</td>
                                        <td class="px-5 py-4 text-sm text-on-surface-variant whitespace-nowrap">{{ $promo->used_count }} / {{ $promo->max_uses ?? '∞' }}</td>
                                        <td class="px-5 py-4 text-sm text-on-surface-variant whitespace-nowrap">{{ $promo->expires_at ? $promo->expires_at->format('d/m/Y') : '-' }}</td>
                                        <td class="px-5 py-4">
                                            @if($promo->isValid())
                                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-electric-blue">Aktif</span>
                                            @else
                                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-500/15 text-on-surface-variant">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-sm whitespace-nowrap space-x-3">
                                            <a href="{{ route('manage.promos.edit', $promo) }}" class="text-electric-blue hover:text-electric-blue font-medium">Edit</a>
                                            <form method="POST" action="{{ route('manage.promos.destroy', $promo) }}"
                                                  class="inline" onsubmit="return confirm('Hapus promo {{ $promo->code }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-300 font-medium">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($promos->hasPages())
                        <div class="px-5 py-4 border-t border-stadium-gray">{{ $promos->links() }}</div>
                    @endif
                </div>

                @if($promos->hasPages())
                    <div class="md:hidden mt-4">{{ $promos->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
