<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola Venue</h1>
                <p class="text-sm text-gray-500 mt-0.5">Daftar venue yang Anda kelola</p>
            </div>
            <a href="{{ route('manage.venues.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Venue
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($venues->isEmpty())
                <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-16 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum ada venue</h3>
                    <p class="text-gray-500 text-sm mb-5">Tambahkan venue pertama Anda untuk mulai menerima booking.</p>
                    <a href="{{ route('manage.venues.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
                        Tambah Venue
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($venues as $venue)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            {{-- Image --}}
                            <div class="h-36 bg-gradient-to-br from-indigo-100 to-violet-100 relative overflow-hidden">
                                @if($venue->image)
                                    <img src="{{ asset('storage/' . $venue->image) }}" class="w-full h-full object-cover" alt="">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-4xl font-bold text-indigo-200">{{ substr($venue->name, 0, 2) }}</span>
                                    </div>
                                @endif
                                <div class="absolute top-2 right-2">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $venue->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $venue->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900">{{ $venue->name }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    {{ $venue->city }}
                                </p>
                                <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                        {{ $venue->courts_count ?? 0 }} lapangan
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-50">
                                    <a href="{{ route('manage.venues.edit', $venue) }}"
                                       class="flex-1 text-center py-1.5 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                                        Edit
                                    </a>
                                    <a href="{{ route('manage.courts.create', $venue) }}"
                                       class="flex-1 text-center py-1.5 text-sm font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        + Lapangan
                                    </a>
                                    <form action="{{ route('manage.venues.destroy', $venue) }}" method="POST"
                                          onsubmit="return confirm('Hapus venue {{ $venue->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-red-400 bg-red-50 rounded-lg hover:bg-red-100 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
