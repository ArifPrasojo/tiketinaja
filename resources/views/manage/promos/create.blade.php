<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-pitch-black">Tambah Promo Baru</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-6">
                <form action="{{ route('manage.promos.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Kode Promo <span class="text-red-400">*</span></label>
                            <input type="text" name="code" value="{{ old('code') }}" required placeholder="FUTSAL20"
                                class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm uppercase focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant @error('code') border-red-400 @enderror">
                            @error('code') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Venue <span class="text-on-surface-variant text-xs">(kosongkan = berlaku semua)</span></label>
                            <select name="venue_id" class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none">
                                <option value="">Platform-wide</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant">Deskripsi <span class="text-red-400">*</span></label>
                        <input type="text" name="description" value="{{ old('description') }}" required
                            class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant @error('description') border-red-400 @enderror">
                        @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Tipe Diskon <span class="text-red-400">*</span></label>
                            <select name="type" required class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none" id="promo-type">
                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Nilai <span class="text-red-400">*</span></label>
                            <input type="number" name="value" value="{{ old('value') }}" required min="1"
                                class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant @error('value') border-red-400 @enderror">
                            @error('value') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Maks Diskon (Rp)</label>
                            <input type="number" name="max_discount" value="{{ old('max_discount') }}" min="0"
                                placeholder="Untuk tipe %"
                                class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Min. Booking (Rp)</label>
                            <input type="number" name="min_booking_amount" value="{{ old('min_booking_amount', 0) }}" min="0"
                                class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Maks Penggunaan</label>
                            <input type="number" name="max_uses" value="{{ old('max_uses') }}" min="1"
                                placeholder="Kosong = tidak terbatas"
                                class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Mulai Berlaku</label>
                            <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                                class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Berakhir</label>
                            <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                                class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} class="w-4 h-4 text-electric-blue border-stadium-gray bg-white rounded focus:ring-0">
                        <label for="is_active" class="text-sm font-medium text-on-surface-variant">Aktif</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-stadium-gray">
                        <a href="{{ route('manage.promos.index') }}" class="px-4 py-2 bg-slate-100 text-on-surface-variant rounded-xl text-sm hover:bg-slate-200 transition">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-electric-blue text-white rounded-xl text-sm hover:bg-blue-700 transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
