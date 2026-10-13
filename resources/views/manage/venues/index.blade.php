<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-pitch-black">Kelola Venue</h1>
                <p class="text-sm text-on-surface-variant mt-0.5">Daftar lokasi gedung olahraga, lapangan, dan jam operasional</p>
            </div>
            <a href="{{ route('manage.venues.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-electric-blue text-white text-sm font-bold rounded-xl hover:bg-blue-700 active:scale-95 transition shadow-sm self-start sm:self-auto">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Venue Baru
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Metric Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Total Venue</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($stats['total']) }}</p>
                        <p class="text-xs text-on-surface-variant mt-1">Lokasi terdaftar</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-electric-blue flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">stadium</span>
                    </div>
                </div>

                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Status Aktif</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($stats['active']) }}</p>
                        <span class="inline-block mt-1 text-[11px] font-bold text-field-green">Dapat dipesan publik</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-field-green flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">check_circle</span>
                    </div>
                </div>

                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Total Lapangan</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($stats['total_courts']) }}</p>
                        <span class="inline-block mt-1 text-[11px] font-bold text-athletic-orange">Unit arena bermain</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-orange-50 text-athletic-orange flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">sports_soccer</span>
                    </div>
                </div>
            </div>

            {{-- Venues Grid --}}
            @if($venues->isEmpty())
                <div class="bg-white border border-stadium-gray rounded-2xl sporty-shadow p-16 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-[32px] text-on-surface-variant">stadium</span>
                    </div>
                    <h3 class="text-lg font-bold text-pitch-black mb-1">Belum ada venue terdaftar</h3>
                    <p class="text-on-surface-variant text-sm mb-6">Tambahkan venue pertama Anda untuk mulai mendaftarkan lapangan dan menerima reservasi.</p>
                    <a href="{{ route('manage.venues.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-electric-blue text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Tambah Venue Sekarang
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($venues as $venue)
                        <div class="bg-white border border-stadium-gray rounded-2xl sporty-shadow overflow-hidden flex flex-col hover:border-electric-blue hover:shadow-md transition-all">
                            {{-- Image Banner with Status Badge --}}
                            <div class="h-44 bg-surface-light relative overflow-hidden">
                                @if($venue->image)
                                    <img src="{{ asset('storage/' . $venue->image) }}" class="w-full h-full object-cover" alt="{{ $venue->name }}">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-blue-100 to-sky-50 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[54px] text-electric-blue/30">stadium</span>
                                    </div>
                                @endif
                                <div class="absolute top-3 right-3">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-lg shadow-sm
                                        {{ $venue->is_active ? 'bg-white text-field-green border border-green-200' : 'bg-white text-slate-500 border border-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $venue->is_active ? 'bg-field-green' : 'bg-slate-400' }}"></span>
                                        {{ $venue->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="font-bold text-base text-pitch-black line-clamp-1">{{ $venue->name }}</h3>
                                <p class="text-xs text-on-surface-variant mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px] text-electric-blue">location_on</span>
                                    {{ $venue->city }}
                                </p>

                                <div class="mt-4 pt-3 border-t border-stadium-gray/60 flex items-center justify-between text-xs">
                                    <span class="inline-flex items-center gap-1.5 font-bold text-pitch-black">
                                        <span class="material-symbols-outlined text-[16px] text-electric-blue">sports_soccer</span>
                                        {{ $venue->courts_count ?? 0 }} Lapangan
                                    </span>
                                    @if($venue->phone)
                                        <span class="text-on-surface-variant">{{ $venue->phone }}</span>
                                    @endif
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex items-center gap-2 mt-5 pt-3 border-t border-stadium-gray">
                                    <a href="{{ route('manage.venues.edit', $venue) }}"
                                       class="flex-1 text-center py-2 text-xs font-bold text-electric-blue bg-blue-50 rounded-xl hover:bg-blue-100 transition">
                                        Edit Detail
                                    </a>
                                    <a href="{{ route('manage.courts.create', $venue) }}"
                                       class="flex-1 text-center py-2 text-xs font-bold text-pitch-black bg-surface-light border border-stadium-gray rounded-xl hover:bg-slate-100 transition">
                                        + Lapangan
                                    </a>
                                    <form action="{{ route('manage.venues.destroy', $venue) }}" method="POST"
                                          onsubmit="return confirm('Hapus venue {{ addslashes($venue->name) }} beserta lapangannya?')"
                                          class="shrink-0">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                title="Hapus Venue"
                                                class="w-8 h-8 rounded-xl flex items-center justify-center text-red-500 hover:text-white hover:bg-red-500 transition">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($venues->hasPages())
                    <div class="mt-8 flex justify-center">
                        {{ $venues->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
