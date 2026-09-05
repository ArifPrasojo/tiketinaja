<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('manage.venues.edit', $venue) }}"
               class="p-2 rounded-lg text-on-surface-variant hover:bg-slate-50 hover:text-pitch-black transition">
                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
            </a>
            <div>
                <h1 class="text-xl font-bold text-pitch-black">Tambah Lapangan</h1>
                <p class="text-sm text-on-surface-variant">{{ $venue->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5 sm:p-6">
                <form action="{{ route('manage.courts.store', $venue) }}" method="POST"
                      enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">
                            Nama Lapangan <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="Contoh: Lapangan A (Vinyl)"
                               class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                        @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant mb-1.5">
                                Jenis Olahraga <span class="text-red-400">*</span>
                            </label>
                            <select name="sport_id" required
                                    class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none">
                                <option value="">Pilih olahraga...</option>
                                @foreach($sports as $sport)
                                    <option value="{{ $sport->id }}" {{ old('sport_id') == $sport->id ? 'selected' : '' }}>
                                        {{ $sport->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant mb-1.5">
                                Harga per Jam (Rp) <span class="text-red-400">*</span>
                            </label>
                            <input type="number" name="price_per_hour" value="{{ old('price_per_hour') }}"
                                   required min="10000" step="5000" placeholder="100000"
                                   class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Deskripsi</label>
                        <textarea name="description" rows="2" placeholder="Deskripsi singkat lapangan..."
                                  class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant resize-none">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">
                            Fasilitas
                            <span class="text-xs text-on-surface-variant font-normal">(pisahkan dengan koma)</span>
                        </label>
                        <input type="text" name="facilities" value="{{ old('facilities') }}"
                               placeholder="Bola, Rompi, AC, Papan Skor"
                               class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Foto Lapangan</label>
                        <div class="border-2 border-dashed border-stadium-gray rounded-xl p-5 text-center hover:border-electric-blue/50 transition">
                            <label class="cursor-pointer">
                                <span class="material-symbols-outlined text-[28px] text-on-surface-variant mx-auto mb-1.5">image</span>
                                <span class="text-sm text-electric-blue font-medium">Pilih foto</span>
                                <input type="file" name="image" accept="image/*" class="sr-only">
                            </label>
                            <p class="text-xs text-on-surface-variant mt-0.5">Maks 2MB</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="court_active" value="1"
                               {{ old('is_active', 1) ? 'checked' : '' }}
                               class="w-4 h-4 text-electric-blue border-stadium-gray bg-white rounded focus:ring-0">
                        <label for="court_active" class="text-sm font-medium text-on-surface-variant">Lapangan aktif (dapat dipesan)</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-stadium-gray">
                        <a href="{{ route('manage.venues.edit', $venue) }}"
                           class="px-5 py-2.5 text-sm font-medium bg-slate-100 text-on-surface-variant rounded-xl hover:bg-slate-200 transition">Batal</a>
                        <button type="submit"
                                class="px-5 py-2.5 text-sm font-medium text-white bg-electric-blue rounded-xl hover:bg-blue-700 transition">
                            Simpan Lapangan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
