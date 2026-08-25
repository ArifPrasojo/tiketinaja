<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Tambah Promo Baru</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('manage.promos.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kode Promo <span class="text-red-500">*</span></label>
                            <input type="text" name="code" value="{{ old('code') }}" required placeholder="FUTSAL20"
                                class="mt-1 w-full border-gray-300 rounded-md text-sm uppercase @error('code') border-red-500 @enderror">
                            @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Venue <span class="text-gray-400 text-xs">(kosongkan = berlaku semua)</span></label>
                            <select name="venue_id" class="mt-1 w-full border-gray-300 rounded-md text-sm">
                                <option value="">Platform-wide</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi <span class="text-red-500">*</span></label>
                        <input type="text" name="description" value="{{ old('description') }}" required
                            class="mt-1 w-full border-gray-300 rounded-md text-sm @error('description') border-red-500 @enderror">
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipe Diskon <span class="text-red-500">*</span></label>
                            <select name="type" required class="mt-1 w-full border-gray-300 rounded-md text-sm" id="promo-type">
                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nilai <span class="text-red-500">*</span></label>
                            <input type="number" name="value" value="{{ old('value') }}" required min="1"
                                class="mt-1 w-full border-gray-300 rounded-md text-sm @error('value') border-red-500 @enderror">
                            @error('value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Maks Diskon (Rp)</label>
                            <input type="number" name="max_discount" value="{{ old('max_discount') }}" min="0"
                                placeholder="Untuk tipe %"
                                class="mt-1 w-full border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Min. Booking (Rp)</label>
                            <input type="number" name="min_booking_amount" value="{{ old('min_booking_amount', 0) }}" min="0"
                                class="mt-1 w-full border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Maks Penggunaan</label>
                            <input type="number" name="max_uses" value="{{ old('max_uses') }}" min="1"
                                placeholder="Kosong = tidak terbatas"
                                class="mt-1 w-full border-gray-300 rounded-md text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mulai Berlaku</label>
                            <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                                class="mt-1 w-full border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Berakhir</label>
                            <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                                class="mt-1 w-full border-gray-300 rounded-md text-sm">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} class="rounded">
                        <label for="is_active" class="text-sm font-medium text-gray-700">Aktif</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('manage.promos.index') }}" class="px-4 py-2 border rounded-md text-sm text-gray-700">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
