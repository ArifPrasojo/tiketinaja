<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('manage.venues.edit', $court->venue) }}"
               class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Edit Lapangan</h1>
                <p class="text-sm text-gray-500">{{ $court->venue->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <form action="{{ route('manage.courts.update', $court) }}" method="POST"
                      enctype="multipart/form-data" class="space-y-5">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lapangan</label>
                        <input type="text" name="name" value="{{ old('name', $court->name) }}" required
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Olahraga</label>
                            <select name="sport_id" required
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                                @foreach($sports as $sport)
                                    <option value="{{ $sport->id }}" {{ old('sport_id', $court->sport_id) == $sport->id ? 'selected' : '' }}>
                                        {{ $sport->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga per Jam (Rp)</label>
                            <input type="number" name="price_per_hour" value="{{ old('price_per_hour', $court->price_per_hour) }}"
                                   required min="10000" step="5000"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                        <textarea name="description" rows="2"
                                  class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none resize-none">{{ old('description', $court->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Fasilitas <span class="text-xs text-gray-400 font-normal">(pisahkan koma)</span>
                        </label>
                        <input type="text" name="facilities"
                               value="{{ old('facilities', is_array($court->facilities) ? implode(', ', $court->facilities) : '') }}"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Ganti Foto</label>
                        @if($court->image)
                            <img src="{{ asset('storage/' . $court->image) }}" class="h-24 rounded-xl mb-2 object-cover" alt="">
                        @endif
                        <input type="file" name="image" accept="image/*"
                               class="text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-400 mt-0.5">Kosongkan jika tidak ingin mengubah foto.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="court_active" value="1"
                               {{ old('is_active', $court->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="court_active" class="text-sm font-medium text-gray-700">Lapangan aktif (dapat dipesan)</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('manage.venues.edit', $court->venue) }}"
                           class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">Batal</a>
                        <button type="submit"
                                class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">
                            Perbarui Lapangan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
