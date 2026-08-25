<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Edit Venue: {{ $venue->name }}</h2></x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            {{-- Venue Info --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Informasi Venue</h3>
                <form action="{{ route('manage.venues.update', $venue) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Venue</label>
                        <input type="text" name="name" value="{{ old('name', $venue->name) }}" required
                            class="w-full mt-1 border-gray-300 rounded-md text-sm @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kota</label>
                            <input type="text" name="city" value="{{ old('city', $venue->city) }}" required
                                class="w-full mt-1 border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $venue->phone) }}" required
                                class="w-full mt-1 border-gray-300 rounded-md text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea name="address" required class="w-full mt-1 border-gray-300 rounded-md text-sm" rows="2">{{ old('address', $venue->address) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" class="w-full mt-1 border-gray-300 rounded-md text-sm" rows="3">{{ old('description', $venue->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Foto Venue</label>
                        @if($venue->image)
                            <img src="{{ asset('storage/' . $venue->image) }}" class="h-24 rounded mt-1 mb-2 object-cover" alt="">
                        @endif
                        <input type="file" name="image" accept="image/*" class="mt-1 text-sm text-gray-600">
                        <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah foto. Maks 2MB.</p>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('manage.venues.index') }}" class="px-4 py-2 border rounded-md text-sm text-gray-700">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Perbarui Venue</button>
                    </div>
                </form>
            </div>

            {{-- Operating Hours --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Jam Operasional</h3>
                @php
                    $dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                    $hoursMap = $venue->operatingHours->keyBy('day_of_week');
                @endphp
                <form action="{{ route('manage.venues.operating-hours.update', $venue) }}" method="POST" class="space-y-3">
                    @csrf @method('PUT')
                    @for($day = 0; $day <= 6; $day++)
                        @php $h = $hoursMap->get($day); @endphp
                        <div class="flex flex-wrap items-center gap-3 py-2 border-b last:border-0">
                            <input type="hidden" name="hours[{{ $day }}][day_of_week]" value="{{ $day }}">
                            <div class="w-20 text-sm font-medium text-gray-700">{{ $dayNames[$day] }}</div>
                            <label class="flex items-center gap-1 text-sm text-gray-600">
                                <input type="checkbox" name="hours[{{ $day }}][is_closed]" value="1"
                                    {{ $h?->is_closed ? 'checked' : '' }}
                                    x-data
                                    @change="$nextTick(() => { let row = $el.closest('[data-day]'); row.querySelectorAll('input[type=time]').forEach(i => i.disabled = $el.checked) })"
                                    class="rounded">
                                Tutup
                            </label>
                            <div class="flex items-center gap-2" data-day="{{ $day }}">
                                <input type="time" name="hours[{{ $day }}][open_time]"
                                    value="{{ $h?->open_time ? substr($h->open_time, 0, 5) : '07:00' }}"
                                    {{ $h?->is_closed ? 'disabled' : '' }}
                                    class="border-gray-300 rounded text-sm disabled:bg-gray-100">
                                <span class="text-gray-500 text-sm">–</span>
                                <input type="time" name="hours[{{ $day }}][close_time]"
                                    value="{{ $h?->close_time ? substr($h->close_time, 0, 5) : '22:00' }}"
                                    {{ $h?->is_closed ? 'disabled' : '' }}
                                    class="border-gray-300 rounded text-sm disabled:bg-gray-100">
                            </div>
                        </div>
                    @endfor
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Simpan Jam Operasional</button>
                    </div>
                </form>
            </div>

            {{-- Courts List --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Lapangan</h3>
                    <a href="{{ route('manage.courts.create', $venue) }}" class="bg-indigo-600 text-white text-sm px-3 py-1.5 rounded hover:bg-indigo-700">+ Tambah Lapangan</a>
                </div>
                @if($venue->courts->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada lapangan.</p>
                @else
                    <div class="space-y-2">
                        @foreach($venue->courts as $court)
                            <div class="flex items-center justify-between border rounded p-3">
                                <div>
                                    <p class="font-medium text-sm text-gray-900">{{ $court->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $court->sport?->name ?? '-' }} • Rp {{ number_format($court->price_per_hour, 0, ',', '.') }}/jam</p>
                                </div>
                                <div class="flex gap-2 text-sm">
                                    <a href="{{ route('manage.courts.edit', $court) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    <form method="POST" action="{{ route('manage.courts.destroy', $court) }}" onsubmit="return confirm('Hapus lapangan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:text-red-900">Hapus</button>
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
