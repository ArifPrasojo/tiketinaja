<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('manage.venues.edit', $venue) }}"
               class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Tambah Lapangan</h1>
                <p class="text-sm text-gray-500">{{ $venue->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <form action="{{ route('manage.courts.store', $venue) }}" method="POST"
                      enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Lapangan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="Contoh: Lapangan A (Vinyl)"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Jenis Olahraga <span class="text-red-500">*</span>
                            </label>
                            <select name="sport_id" required
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                                <option value="">Pilih olahraga...</option>
                                @foreach($sports as $sport)
                                    <option value="{{ $sport->id }}" {{ old('sport_id') == $sport->id ? 'selected' : '' }}>
                                        {{ $sport->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Harga per Jam (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="price_per_hour" value="{{ old('price_per_hour') }}"
                                   required min="10000" step="5000" placeholder="100000"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                        <textarea name="description" rows="2" placeholder="Deskripsi singkat lapangan..."
                                  class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none resize-none">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Fasilitas
                            <span class="text-xs text-gray-400 font-normal">(pisahkan dengan koma)</span>
                        </label>
                        <input type="text" name="facilities" value="{{ old('facilities') }}"
                               placeholder="Bola, Rompi, AC, Papan Skor"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto Lapangan</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-indigo-400 transition">
                            <label class="cursor-pointer">
                                <svg class="w-7 h-7 text-gray-400 mx-auto mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-sm text-indigo-600 font-medium">Pilih foto</span>
                                <input type="file" name="image" accept="image/*" class="sr-only">
                            </label>
                            <p class="text-xs text-gray-400 mt-0.5">Maks 2MB</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="court_active" value="1"
                               {{ old('is_active', 1) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="court_active" class="text-sm font-medium text-gray-700">Lapangan aktif (dapat dipesan)</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('manage.venues.edit', $venue) }}"
                           class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">Batal</a>
                        <button type="submit"
                                class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">
                            Simpan Lapangan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
