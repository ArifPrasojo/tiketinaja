<x-guest-public-layout>
    <x-slot name="title">{{ $court->name }} — {{ $court->venue->name }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1.5 text-sm text-on-surface-variant mb-5 flex-wrap">
            <a href="{{ route('venues.index') }}" class="hover:text-electric-blue">Venue</a>
            <span class="text-on-surface-variant">/</span>
            <a href="{{ route('venues.show', $court->venue) }}" class="hover:text-electric-blue truncate max-w-[120px] sm:max-w-none">{{ $court->venue->name }}</a>
            <span class="text-on-surface-variant">/</span>
            <span class="text-pitch-black font-medium truncate max-w-[120px] sm:max-w-none">{{ $court->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6"             x-data="{
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
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow overflow-hidden">
                    @if($court->image)
                        <img src="{{ asset('storage/' . $court->image) }}" class="w-full h-52 object-cover" alt="">
                    @endif
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h1 class="text-xl font-bold text-pitch-black">{{ $court->name }}</h1>
                                <p class="text-sm text-on-surface-variant mt-0.5">
                                    <a href="{{ route('venues.show', $court->venue) }}" class="hover:text-electric-blue">{{ $court->venue->name }}</a>
                                    · {{ $court->venue->city }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-lg font-bold text-electric-blue">{{ 'Rp ' . number_format($court->price_per_hour, 0, ',', '.') }}</p>
                                <p class="text-xs text-on-surface-variant">per jam</p>
                            </div>
                        </div>

                        @if($court->description)
                            <p class="text-sm text-on-surface-variant mt-3">{{ $court->description }}</p>
                        @endif

                        @if($court->facilities)
                            <div class="flex flex-wrap gap-1.5 mt-4">
                                @foreach($court->facilities as $f)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-on-surface-variant text-xs rounded-lg">
                                        <span class="material-symbols-outlined text-[14px]">check</span> {{ $f }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if($court->sport)
                            <div class="mt-3">
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-50 text-electric-blue border border-electric-blue/20 text-xs font-medium rounded-full">
                                    <span class="material-symbols-outlined text-[16px]">sprint</span> {{ $court->sport->name }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Slot picker --}}
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5">
                    <h2 class="text-base font-semibold text-pitch-black mb-4">Pilih Tanggal & Waktu</h2>

                    <div class="mb-5">
                        <label for="date" class="block text-sm font-medium text-on-surface-variant mb-1.5">Tanggal Booking</label>
                        <input type="date" id="date" x-model="date" @change="fetchSlots()"
                               min="{{ date('Y-m-d') }}"
                               class="px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none w-full sm:w-auto">
                    </div>

                    <div x-show="loading" class="text-center py-10">
                        <div class="inline-flex items-center gap-2 text-on-surface-variant text-sm">
                            <span class="material-symbols-outlined animate-spin text-[16px] text-electric-blue">progress_activity</span>
                            Memuat jadwal...
                        </div>
                    </div>

                    <div x-show="!loading && date && slots.length === 0" class="text-center py-8">
                        <p class="text-on-surface-variant text-sm">Venue tutup atau tidak ada slot tersedia untuk tanggal ini.</p>
                    </div>

                    <div x-show="!loading && slots.length > 0">
                        <p class="text-xs font-semibold text-on-surface-variant mb-3">Pilih slot jam yang tersedia:</p>
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                            <template x-for="slot in slots" :key="slot.start_time">
                                <button type="button" @click="toggle(slot)"
                                    :disabled="!slot.available"
                                    :class="{
                                        'bg-slate-100 border-stadium-gray text-on-surface-variant/50 cursor-not-allowed opacity-60': !slot.available,
                                        'bg-blue-50/70 border-electric-blue/30 text-electric-blue hover:bg-blue-100 active:scale-95': slot.available && !isSelected(slot),
                                        'bg-electric-blue border-electric-blue text-white shadow-md shadow-blue-500/30 font-bold': isSelected(slot),
                                    }"
                                    class="border rounded-xl py-2.5 sm:py-3 text-xs font-semibold text-center transition">
                                    <span x-text="slot.start_time"></span>
                                </button>
                            </template>
                        </div>
                        <div class="flex flex-wrap gap-3 mt-3 text-xs text-on-surface-variant">
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-50 border border-electric-blue/40 inline-block"></span> Tersedia</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-white border border-stadium-gray inline-block"></span> Terisi</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-electric-blue inline-block"></span> Dipilih</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-5 sticky top-20">
                    <h2 class="text-base font-semibold text-pitch-black mb-4">Ringkasan Booking</h2>

                    <div x-show="selected.length === 0" class="text-center py-6">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="material-symbols-outlined text-[24px] text-on-surface-variant">schedule</span>
                        </div>
                        <p class="text-sm text-on-surface-variant">Pilih tanggal dan jam terlebih dahulu.</p>
                    </div>

                    <div x-show="selected.length > 0" class="space-y-3">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-on-surface-variant">
                                <span>Lapangan</span>
                                <span class="font-medium text-pitch-black text-right">{{ $court->name }}</span>
                            </div>
                            <div class="flex justify-between text-on-surface-variant">
                                <span>Tanggal</span>
                                <span class="font-medium text-pitch-black" x-text="date"></span>
                            </div>
                            <div class="flex justify-between text-on-surface-variant">
                                <span>Jam dipilih</span>
                                <span class="font-medium text-pitch-black" x-text="selected.length + ' jam'"></span>
                            </div>
                        </div>

                        {{-- Selected slots --}}
                        <div class="flex flex-wrap gap-1">
                            <template x-for="s in [...selected].sort()" :key="s">
                                <span class="px-2 py-0.5 bg-blue-50 text-electric-blue text-xs font-medium rounded-lg" x-text="s"></span>
                            </template>
                        </div>

                        {{-- Promo code --}}
                        <div class="pt-3 border-t border-stadium-gray">
                            <label class="block text-xs font-medium text-on-surface-variant mb-1.5">Kode Promo</label>
                            <div class="flex gap-1.5">
                                <input type="text" x-model="promoCode" @keydown.enter.prevent="checkPromo()"
                                       placeholder="KODE PROMO"
                                       class="flex-1 px-2.5 py-2 text-xs uppercase bg-white border border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                                <button type="button" @click="checkPromo()" :disabled="promoChecking"
                                        class="px-3 py-2 text-xs font-medium bg-electric-blue text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition">
                                    Pakai
                                </button>
                            </div>
                            <template x-if="promoResult">
                                <div :class="promoResult.valid ? 'bg-blue-50 text-electric-blue border-electric-blue/30' : 'bg-red-500/10 text-red-400 border-red-500/30'"
                                     class="mt-1.5 px-2.5 py-2 rounded-lg text-xs border">
                                    <span x-show="promoResult.valid">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span> <span x-text="promoResult.description"></span>
                                        · Hemat <span x-text="fmt(promoResult.discount_amount)"></span>
                                    </span>
                                    <span x-show="!promoResult.valid" x-text="promoResult.message"></span>
                                </div>
                            </template>
                        </div>

                        {{-- Total --}}
                        <div class="pt-3 border-t border-stadium-gray space-y-1.5">
                            <div class="flex justify-between text-sm text-on-surface-variant">
                                <span>Subtotal</span>
                                <span x-text="fmt(total)"></span>
                            </div>
                            <template x-if="discount > 0">
                                <div class="flex justify-between text-sm text-electric-blue">
                                    <span>Diskon</span>
                                    <span x-text="'- ' + fmt(discount)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between text-base font-bold text-pitch-black pt-1 border-t border-stadium-gray">
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
                                        class="w-full py-3 bg-electric-blue text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-500/20">
                                    Booking Sekarang
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}"
                               class="block w-full py-3 text-center bg-slate-100 text-pitch-black text-sm font-semibold rounded-xl hover:bg-slate-200 transition mt-1">
                                Login untuk Booking
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-public-layout>