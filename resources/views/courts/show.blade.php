<x-guest-public-layout>
    <x-slot name="title">{{ $court->name }} — {{ $court->venue->name }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('venues.index') }}" class="hover:text-indigo-600">Venue</a>
            <span>/</span>
            <a href="{{ route('venues.show', $court->venue) }}" class="hover:text-indigo-600">{{ $court->venue->name }}</a>
            <span>/</span>
            <span class="text-gray-900 font-medium">{{ $court->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6"
             x-data="{
                date: '',
                slots: [],
                selected: [],
                loading: false,
                promoCode: '',
                promoResult: null,
                promoChecking: false,
                async fetchSlots() {
                    if (!this.date) return;
                    this.loading = true; this.selected = [];
                    const res = await fetch(`/courts/{{ $court->id }}/availability?date=${this.date}`);
                    this.slots = await res.json();
                    this.loading = false;
                },
                toggle(slot) {
                    if (!slot.available) return;
                    const i = this.selected.indexOf(slot.start_time);
                    if (i > -1) this.selected.splice(i,1); else this.selected.push(slot.start_time);
                },
                isSelected(s) { return this.selected.includes(s.start_time); },
                get total() { return this.selected.length * {{ $court->price_per_hour }}; },
                get discount() { return this.promoResult?.valid ? (this.promoResult.discount_amount ?? 0) : 0; },
                get finalTotal() { return this.total - this.discount; },
                fmt(n) { return 'Rp ' + n.toLocaleString('id-ID'); },
                async checkPromo() {
                    if (!this.promoCode.trim() || !this.total) return;
                    this.promoChecking = true; this.promoResult = null;
                    const res = await fetch('{{ route('promos.validate') }}', {
                        method: 'POST',
                        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
                        body: JSON.stringify({code:this.promoCode,amount:this.total,venue_id:{{ $court->venue_id }}}),
                    });
                    this.promoResult = await res.json();
                    this.promoChecking = false;
                }
             }">

            {{-- Left: Court info + slot picker --}}
            <div class="lg:col-span-2 space-y-5">
                {{-- Court card --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    @if($court->image)
                        <img src="{{ asset('storage/' . $court->image) }}" class="w-full h-52 object-cover" alt="">
                    @endif
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">{{ $court->name }}</h1>
                                <p class="text-sm text-gray-500 mt-0.5">
                                    <a href="{{ route('venues.show', $court->venue) }}" class="hover:text-indigo-600">{{ $court->venue->name }}</a>
                                    · {{ $court->venue->city }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-lg font-bold text-indigo-600">{{ 'Rp ' . number_format($court->price_per_hour, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-400">per jam</p>
                            </div>
                        </div>

                        @if($court->description)
                            <p class="text-sm text-gray-600 mt-3">{{ $court->description }}</p>
                        @endif

                        @if($court->facilities)
                            <div class="flex flex-wrap gap-1.5 mt-4">
                                @foreach($court->facilities as $f)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-600 text-xs rounded-lg">
                                        ✓ {{ $f }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if($court->sport)
                            <div class="mt-3">
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-medium rounded-full">
                                    🏃 {{ $court->sport->name }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Slot picker --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Pilih Tanggal & Waktu</h2>

                    <div class="mb-5">
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Booking</label>
                        <input type="date" id="date" x-model="date" @change="fetchSlots()"
                               min="{{ date('Y-m-d') }}"
                               class="px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none w-full sm:w-auto">
                    </div>

                    <div x-show="loading" class="text-center py-10">
                        <div class="inline-flex items-center gap-2 text-gray-500 text-sm">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Memuat jadwal...
                        </div>
                    </div>

                    <div x-show="!loading && date && slots.length === 0" class="text-center py-8">
                        <p class="text-gray-500 text-sm">Venue tutup atau tidak ada slot tersedia untuk tanggal ini.</p>
                    </div>

                    <div x-show="!loading && slots.length > 0">
                        <p class="text-xs text-gray-500 mb-3">Klik slot untuk memilih:</p>
                        <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-2">
                            <template x-for="slot in slots" :key="slot.start_time">
                                <button type="button" @click="toggle(slot)"
                                    :disabled="!slot.available"
                                    :class="{
                                        'bg-white border-gray-200 text-gray-400 cursor-not-allowed opacity-60': !slot.available,
                                        'bg-emerald-50 border-emerald-300 text-emerald-700 hover:bg-emerald-100': slot.available && !isSelected(slot),
                                        'bg-indigo-600 border-indigo-600 text-white shadow-md': isSelected(slot),
                                    }"
                                    class="border rounded-xl py-2.5 text-xs font-semibold text-center transition">
                                    <span x-text="slot.start_time"></span>
                                </button>
                            </template>
                        </div>
                        <div class="flex flex-wrap gap-3 mt-3 text-xs text-gray-500">
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-50 border border-emerald-300 inline-block"></span> Tersedia</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-gray-100 border border-gray-200 inline-block"></span> Terisi</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-indigo-600 inline-block"></span> Dipilih</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-20">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Ringkasan Booking</h2>

                    <div x-show="selected.length === 0" class="text-center py-6">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm text-gray-400">Pilih tanggal dan jam terlebih dahulu.</p>
                    </div>

                    <div x-show="selected.length > 0" class="space-y-3">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Lapangan</span>
                                <span class="font-medium text-gray-900 text-right">{{ $court->name }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Tanggal</span>
                                <span class="font-medium text-gray-900" x-text="date"></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Jam dipilih</span>
                                <span class="font-medium text-gray-900" x-text="selected.length + ' jam'"></span>
                            </div>
                        </div>

                        {{-- Selected slots --}}
                        <div class="flex flex-wrap gap-1">
                            <template x-for="s in [...selected].sort()" :key="s">
                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-medium rounded-lg" x-text="s"></span>
                            </template>
                        </div>

                        {{-- Promo code --}}
                        <div class="pt-3 border-t border-gray-100">
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Kode Promo</label>
                            <div class="flex gap-1.5">
                                <input type="text" x-model="promoCode" @keydown.enter.prevent="checkPromo()"
                                       placeholder="KODE PROMO"
                                       class="flex-1 px-2.5 py-2 text-xs uppercase border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                                <button type="button" @click="checkPromo()" :disabled="promoChecking"
                                        class="px-3 py-2 text-xs font-medium bg-gray-700 text-white rounded-lg hover:bg-gray-800 disabled:opacity-50 transition">
                                    Pakai
                                </button>
                            </div>
                            <template x-if="promoResult">
                                <div :class="promoResult.valid ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-600 border-red-200'"
                                     class="mt-1.5 px-2.5 py-2 rounded-lg text-xs border">
                                    <span x-show="promoResult.valid">
                                        ✓ <span x-text="promoResult.description"></span>
                                        · Hemat <span x-text="fmt(promoResult.discount_amount)"></span>
                                    </span>
                                    <span x-show="!promoResult.valid" x-text="promoResult.message"></span>
                                </div>
                            </template>
                        </div>

                        {{-- Total --}}
                        <div class="pt-3 border-t border-gray-100 space-y-1.5">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span x-text="fmt(total)"></span>
                            </div>
                            <template x-if="discount > 0">
                                <div class="flex justify-between text-sm text-green-600">
                                    <span>Diskon</span>
                                    <span x-text="'- ' + fmt(discount)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between text-base font-bold text-gray-900 pt-1 border-t border-gray-100">
                                <span>Total</span>
                                <span x-text="fmt(finalTotal)"></span>
                            </div>
                        </div>

                        @auth
                            <form method="POST" action="{{ route('bookings.store') }}" class="pt-1">
                                @csrf
                                <input type="hidden" name="court_id" value="{{ $court->id }}">
                                <input type="hidden" name="date" :value="date">
                                <template x-for="s in selected" :key="s">
                                    <input type="hidden" name="time_slots[]" :value="s">
                                </template>
                                <input type="hidden" name="promo_code" :value="promoResult?.valid ? promoCode : ''">
                                <button type="submit"
                                        class="w-full py-3 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm">
                                    Booking Sekarang
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}"
                               class="block w-full py-3 text-center bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition mt-1">
                                Login untuk Booking
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-public-layout>
