<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-pitch-black">Kelola Venue</h1>
                <p class="text-sm text-on-surface-variant mt-0.5">Daftar venue yang Anda kelola</p>
            </div>
            <a href="{{ route('manage.venues.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-electric-blue text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                <span class="material-symbols-outlined text-[16px]">add</span>
                Tambah Venue
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($venues->isEmpty())
                <div class="bg-white rounded-xl border border-dashed border-stadium-gray p-16 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-[32px] text-on-surface-variant">stadium</span>
                    </div>
                    <h3 class="text-lg font-semibold text-pitch-black mb-1">Belum ada venue</h3>
                    <p class="text-on-surface-variant text-sm mb-5">Tambahkan venue pertama Anda untuk mulai menerima booking.</p>
                    <a href="{{ route('manage.venues.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-electric-blue text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                        Tambah Venue
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($venues as $venue)
                        <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow overflow-hidden hover:border-electric-blue transition-shadow">
                            <div class="h-36 bg-gradient-to-br from-blue-500/20 to-sky-500/10 relative overflow-hidden">
                                @if($venue->image)
                                    <img src="{{ asset('storage/' . $venue->image) }}" class="w-full h-full object-cover" alt="">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-4xl font-bold text-electric-blue/30">{{ substr($venue->name, 0, 2) }}</span>
                                    </div>
                                @endif
                                <div class="absolute top-2 right-2">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $venue->is_active ? 'bg-blue-50 text-electric-blue' : 'bg-slate-500/15 text-on-surface-variant' }}">
                                        {{ $venue->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-4">
                                <h3 class="font-semibold text-pitch-black">{{ $venue->name }}</h3>
                                <p class="text-xs text-on-surface-variant mt-0.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">location_on</span>
                                    {{ $venue->city }}
                                </p>
                                <div class="flex items-center gap-3 mt-2 text-xs text-on-surface-variant">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px] text-electric-blue">stadium</span>
                                        {{ $venue->courts_count ?? 0 }} lapangan
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mt-4 pt-3 border-t border-stadium-gray">
                                    <a href="{{ route('manage.venues.edit', $venue) }}"
                                       class="flex-1 text-center py-1.5 text-sm font-medium text-electric-blue bg-blue-50 rounded-lg hover:bg-blue-700/20 transition">
                                        Edit
                                    </a>
                                    <a href="{{ route('manage.courts.create', $venue) }}"
                                       class="flex-1 text-center py-1.5 text-sm font-medium text-on-surface-variant bg-slate-100 rounded-lg hover:bg-slate-200 transition">
                                        + Lapangan
                                    </a>
                                    <form action="{{ route('manage.venues.destroy', $venue) }}" method="POST"
                                          onsubmit="return confirm('Hapus venue {{ $venue->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg hover:bg-red-500/10 transition">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($venues->hasPages())
                    <div class="mt-6 flex justify-center">{{ $venues->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
