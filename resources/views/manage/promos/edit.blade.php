<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-pitch-black">Edit Promo: {{ $promo->code }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-6">
                <form action="{{ route('manage.promos.update', $promo) }}" method="POST" class="space-y-5">
                    @csrf @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Kode Promo</label>
                            <input type="text" name="code" value="{{ old('code', $promo->code) }}" required
                                class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm uppercase focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                            @error('code') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Venue</label>
                            <select name="venue_id" class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none">
                                <option value="">Platform-wide</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" {{ old('venue_id', $promo->venue_id) == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant">Deskripsi</label>
                        <input type="text" name="description" value="{{ old('description', $promo->description) }}" required
                            class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Tipe Diskon</label>
                            <select name="type" required class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none">
                                <option value="fixed" {{ old('type', $promo->type) == 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                                <option value="percentage" {{ old('type', $promo->type) == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Nilai</label>
                            <input type="number" name="value" value="{{ old('value', $promo->value) }}" required min="1"
                                class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Maks Diskon (Rp)</label>
                            <input type="number" name="max_discount" value="{{ old('max_discount', $promo->max_discount) }}" min="0"
                                class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Min. Booking (Rp)</label>
                            <input type="number" name="min_booking_amount" value="{{ old('min_booking_amount', $promo->min_booking_amount) }}" min="0"
                                class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Maks Penggunaan</label>
                            <input type="number" name="max_uses" value="{{ old('max_uses', $promo->max_uses) }}" min="1"
                                placeholder="Tidak terbatas"
                                class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Mulai Berlaku</label>
                            <input type="datetime-local" name="starts_at"
                                value="{{ old('starts_at', $promo->starts_at?->format('Y-m-d\TH:i')) }}"
                                class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant">Berakhir</label>
                            <input type="datetime-local" name="expires_at"
                                value="{{ old('expires_at', $promo->expires_at?->format('Y-m-d\TH:i')) }}"
                                class="mt-1 w-full px-3 py-2.5 bg-white border-2 border-stadium-gray text-pitch-black rounded-lg text-sm focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $promo->is_active) ? 'checked' : '' }} class="w-4 h-4 text-electric-blue border-stadium-gray bg-white rounded focus:ring-0">
                        <label for="is_active" class="text-sm font-medium text-on-surface-variant">Aktif</label>
                    </div>

                    <div class="bg-white border border-stadium-gray rounded-xl p-3 text-sm text-on-surface-variant">
                        <p>Penggunaan saat ini: <span class="font-semibold text-pitch-black">{{ $promo->used_count }}</span> dari {{ $promo->max_uses ?? 'tidak terbatas' }}</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-stadium-gray">
                        <a href="{{ route('manage.promos.index') }}" class="px-4 py-2 bg-slate-100 text-on-surface-variant rounded-xl text-sm hover:bg-slate-200 transition">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-electric-blue text-white rounded-xl text-sm hover:bg-blue-700 transition">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
