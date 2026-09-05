<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('manage.venues.index') }}"
               class="p-2 rounded-lg text-on-surface-variant hover:bg-slate-50 hover:text-pitch-black transition">
                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
            </a>
            <div>
                <h1 class="text-xl font-bold text-pitch-black truncate">Edit Venue</h1>
                <p class="text-sm text-on-surface-variant truncate">{{ $venue->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5 sm:p-6">
                <h2 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide mb-4">Informasi Venue</h2>
                <form action="{{ route('manage.venues.update', $venue) }}" method="POST"
                      enctype="multipart/form-data" class="space-y-4">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Nama Venue</label>
                        <input type="text" name="name" value="{{ old('name', $venue->name) }}" required
                               class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant @error('name') border-red-400 @enderror">
                        @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Kota</label>
                            <input type="text" name="city" value="{{ old('city', $venue->city) }}" required
                                   class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant mb-1.5">No. Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $venue->phone) }}" required
                                   class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Alamat</label>
                        <textarea name="address" required rows="2"
                                  class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant resize-none">{{ old('address', $venue->address) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Deskripsi</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant resize-none">{{ old('description', $venue->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Foto Venue</label>
                        @if($venue->image)
                            <img src="{{ asset('storage/' . $venue->image) }}"
                                 class="h-24 w-full sm:w-auto rounded-xl mb-2 object-cover" alt="">
                        @endif
                        <input type="file" name="image" accept="image/*"
                               class="w-full text-sm text-on-surface-variant file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-electric-blue hover:file:bg-electric-blue/25">
                        <p class="text-xs text-on-surface-variant mt-1">Kosongkan jika tidak ingin mengubah. Maks 3MB.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="venue_is_active" value="1"
                               {{ old('is_active', $venue->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-electric-blue border-stadium-gray bg-white rounded focus:ring-0">
                        <label for="venue_is_active" class="text-sm font-medium text-on-surface-variant">Venue aktif (tampil di pencarian)</label>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">
                        <a href="{{ route('manage.venues.index') }}"
                           class="px-5 py-2.5 text-center text-sm font-medium bg-slate-100 text-on-surface-variant rounded-xl hover:bg-slate-200 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-5 py-2.5 text-sm font-medium text-white bg-electric-blue rounded-xl hover:bg-blue-700 transition">
                            Perbarui Venue
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5 sm:p-6">
                <h2 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide mb-4">Jam Operasional</h2>
                @php
                    $dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                    $hoursMap = $venue->operatingHours->keyBy('day_of_week');
                @endphp
                <form action="{{ route('manage.venues.operating-hours.update', $venue) }}" method="POST" class="space-y-2">
                    @csrf @method('PUT')
                    @for($day = 0; $day <= 6; $day++)
                        @php $h = $hoursMap->get($day); @endphp
                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-x-3 gap-y-2 py-2.5 border-b border-stadium-gray last:border-0">
                            <input type="hidden" name="hours[{{ $day }}][day_of_week]" value="{{ $day }}">

                            <span class="w-16 text-sm font-medium text-on-surface-variant shrink-0">{{ $dayNames[$day] }}</span>

                            <label class="flex items-center gap-1.5 text-sm text-on-surface-variant shrink-0 cursor-pointer"
                                   x-data="{ closed: {{ $h?->is_closed ? 'true' : 'false' }} }">
                                <input type="checkbox" name="hours[{{ $day }}][is_closed]" value="1"
                                       x-model="closed"
                                       class="w-4 h-4 rounded border-stadium-gray bg-white text-electric-blue focus:ring-0">
                                <span>Tutup</span>

                                <template x-if="!closed">
                                    <span class="flex items-center gap-2 ml-2">
                                        <input type="time" name="hours[{{ $day }}][open_time]"
                                               value="{{ $h?->open_time ? substr($h->open_time, 0, 5) : '07:00' }}"
                                               class="bg-white border border-stadium-gray text-pitch-black rounded-lg text-sm px-2 py-1.5 focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none w-[110px]">
                                        <span class="text-on-surface-variant text-xs">–</span>
                                        <input type="time" name="hours[{{ $day }}][close_time]"
                                               value="{{ $h?->close_time ? substr($h->close_time, 0, 5) : '22:00' }}"
                                               class="bg-white border border-stadium-gray text-pitch-black rounded-lg text-sm px-2 py-1.5 focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none w-[110px]">
                                    </span>
                                </template>
                                <template x-if="closed">
                                    <span>
                                        <input type="hidden" name="hours[{{ $day }}][open_time]" value="{{ $h?->open_time ? substr($h->open_time, 0, 5) : '07:00' }}">
                                        <input type="hidden" name="hours[{{ $day }}][close_time]" value="{{ $h?->close_time ? substr($h->close_time, 0, 5) : '22:00' }}">
                                    </span>
                                </template>
                            </label>
                        </div>
                    @endfor
                    <div class="flex justify-end pt-3">
                        <button type="submit"
                                class="px-5 py-2.5 text-sm font-medium text-white bg-electric-blue rounded-xl hover:bg-blue-700 transition">
                            Simpan Jam Operasional
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide">Lapangan</h2>
                    <a href="{{ route('manage.courts.create', $venue) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-white bg-electric-blue rounded-xl hover:bg-blue-700 transition">
                        <span class="material-symbols-outlined text-[14px]">add</span>
                        Tambah
                    </a>
                </div>
                @if($venue->courts->isEmpty())
                    <p class="text-sm text-on-surface-variant text-center py-4">Belum ada lapangan. Tambahkan lapangan pertama.</p>
                @else
                    <div class="space-y-2">
                        @foreach($venue->courts as $court)
                            <div class="flex items-center justify-between border border-stadium-gray rounded-xl p-3">
                                <div class="min-w-0 flex-1 mr-3">
                                    <p class="font-semibold text-sm text-pitch-black truncate">{{ $court->name }}</p>
                                    <p class="text-xs text-on-surface-variant">
                                        {{ $court->sport?->name ?? '-' }} ·
                                        Rp {{ number_format($court->price_per_hour, 0, ',', '.') }}/jam ·
                                        <span class="{{ $court->is_active ? 'text-electric-blue' : 'text-red-400' }}">
                                            {{ $court->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('manage.courts.edit', $court) }}"
                                       class="px-3 py-1.5 text-xs font-medium text-electric-blue bg-blue-50 rounded-lg hover:bg-blue-700/20 transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('manage.courts.destroy', $court) }}"
                                          onsubmit="return confirm('Hapus lapangan {{ addslashes($court->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1.5 text-xs font-medium text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg hover:bg-red-500/10 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
