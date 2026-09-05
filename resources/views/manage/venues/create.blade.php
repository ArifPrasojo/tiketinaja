<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('manage.venues.index') }}"
               class="p-2 rounded-lg text-on-surface-variant hover:bg-slate-50 hover:text-pitch-black transition">
                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
            </a>
            <div>
                <h1 class="text-xl font-bold text-pitch-black">Tambah Venue Baru</h1>
                <p class="text-sm text-on-surface-variant">Isi informasi venue yang akan ditambahkan</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-6">
                <form action="{{ route('manage.venues.store') }}" method="POST"
                      enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">
                            Nama Venue <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="Contoh: GOR Bintaro Jaya"
                               class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant @error('name') border-red-400 @enderror">
                        @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant mb-1.5">
                                Kota <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="city" value="{{ old('city') }}" required
                                   placeholder="Jakarta Selatan"
                                   class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant mb-1.5">
                                No. Telepon <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required
                                   placeholder="0812-XXXX-XXXX"
                                   class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">
                            Alamat Lengkap <span class="text-red-400">*</span>
                        </label>
                        <textarea name="address" required rows="2"
                                  placeholder="Jl. Contoh No. 123, Kelurahan, Kecamatan"
                                  class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant resize-none">{{ old('address') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Deskripsi</label>
                        <textarea name="description" rows="3"
                                  placeholder="Ceritakan fasilitas unggulan venue Anda..."
                                  class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant resize-none">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Foto Venue</label>
                        <div class="border-2 border-dashed border-stadium-gray rounded-xl p-6 text-center hover:border-electric-blue/50 transition">
                            <span class="material-symbols-outlined text-[32px] text-on-surface-variant mx-auto mb-2">image</span>
                            <label class="cursor-pointer">
                                <span class="text-sm text-electric-blue font-medium hover:text-electric-blue">Pilih foto</span>
                                <span class="text-sm text-on-surface-variant"> atau drag & drop</span>
                                <input type="file" name="image" accept="image/*" class="sr-only">
                            </label>
                            <p class="text-xs text-on-surface-variant mt-1">PNG, JPG, WEBP hingga 3MB</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', 1) ? 'checked' : '' }}
                               class="w-4 h-4 text-electric-blue border-stadium-gray bg-white rounded focus:ring-0">
                        <label for="is_active" class="text-sm font-medium text-on-surface-variant">
                            Venue langsung aktif (dapat ditemukan customer)
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-stadium-gray">
                        <a href="{{ route('manage.venues.index') }}"
                           class="px-5 py-2.5 text-sm font-medium bg-slate-100 text-on-surface-variant rounded-xl hover:bg-slate-200 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-5 py-2.5 text-sm font-medium text-white bg-electric-blue rounded-xl hover:bg-blue-700 transition">
                            Simpan Venue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
