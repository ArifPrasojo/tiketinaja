<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Scanner QR Check-in</h1>
            <p class="text-sm text-gray-500 mt-0.5">Scan QR code customer untuk verifikasi kedatangan</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Scanner card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"
                 x-data="qrScanner()" x-init="init()" x-effect="scanLoop()">

                {{-- Tabs --}}
                <div class="flex border-b border-gray-100">
                    <button @click="mode = 'camera'; startCamera()"
                            :class="mode === 'camera' ? 'border-b-2 border-indigo-600 text-indigo-700 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3.5 text-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0"/>
                        </svg>
                        Scan Kamera
                    </button>
                    <button @click="mode = 'manual'; stopCamera()"
                            :class="mode === 'manual' ? 'border-b-2 border-indigo-600 text-indigo-700 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3.5 text-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5a.5.5 0 110-1 .5.5 0 010 1zm.5 3.5a.5.5 0 11-1 0 .5.5 0 011 0z"/>
                        </svg>
                        Input Manual
                    </button>
                </div>

                <div class="p-6">

                    {{-- Camera mode --}}
                    <div x-show="mode === 'camera'" x-cloak>

                        {{-- Camera permission denied --}}
                        <div x-show="cameraError" class="text-center py-10">
                            <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-800 mb-1">Akses kamera ditolak</p>
                            <p class="text-xs text-gray-500 mb-4" x-text="cameraError"></p>
                            <button @click="startCamera()"
                                    class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                                Coba Lagi
                            </button>
                        </div>

                        {{-- Camera loading --}}
                        <div x-show="!cameraReady && !cameraError" class="text-center py-10">
                            <div class="inline-flex items-center gap-2 text-gray-500 text-sm">
                                <svg class="animate-spin w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Memuat kamera...
                            </div>
                        </div>

                        {{-- Camera viewport --}}
                        <div x-show="cameraReady && !cameraError" class="relative">
                            <div class="relative rounded-xl overflow-hidden bg-gray-900" style="aspect-ratio: 1/1; max-height: 360px;">
                                <video x-ref="video" autoplay playsinline muted
                                       class="w-full h-full object-cover"
                                       @play="cameraReady = true"></video>

                                {{-- Scanning overlay --}}
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    {{-- Dimmed corners --}}
                                    <div class="absolute inset-0 bg-gray-950/40"></div>

                                    {{-- Scan box --}}
                                    <div class="relative w-52 h-52">
                                        {{-- Clear center --}}
                                        <div class="absolute inset-0 bg-transparent"
                                             style="box-shadow: 0 0 0 9999px rgba(10,10,15,0.4)"></div>

                                        {{-- Corner brackets --}}
                                        <div class="absolute top-0 left-0 w-8 h-8 border-t-3 border-l-3 border-indigo-400 rounded-tl-lg"
                                             style="border-width: 3px 0 0 3px"></div>
                                        <div class="absolute top-0 right-0 w-8 h-8 border-t-3 border-r-3 border-indigo-400 rounded-tr-lg"
                                             style="border-width: 3px 3px 0 0"></div>
                                        <div class="absolute bottom-0 left-0 w-8 h-8 border-b-3 border-l-3 border-indigo-400 rounded-bl-lg"
                                             style="border-width: 0 0 3px 3px"></div>
                                        <div class="absolute bottom-0 right-0 w-8 h-8 border-b-3 border-r-3 border-indigo-400 rounded-br-lg"
                                             style="border-width: 0 3px 3px 0"></div>

                                        {{-- Scanning line animation --}}
                                        <div x-show="scanning" class="absolute left-1 right-1 h-0.5 bg-indigo-400/80 rounded-full"
                                             style="animation: scanLine 2s ease-in-out infinite; top: 50%"></div>
                                    </div>
                                </div>

                                {{-- Status badge --}}
                                <div class="absolute top-3 left-1/2 -translate-x-1/2">
                                    <span x-show="scanning"
                                          class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-600/90 text-white text-xs font-medium rounded-full backdrop-blur-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        Scanning...
                                    </span>
                                    <span x-show="verifying"
                                          class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/90 text-white text-xs font-medium rounded-full backdrop-blur-sm">
                                        <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        Memverifikasi...
                                    </span>
                                </div>
                            </div>

                            {{-- Hidden canvas for frame capture --}}
                            <canvas x-ref="canvas" class="hidden"></canvas>

                            <p class="text-xs text-center text-gray-500 mt-3">
                                Arahkan kamera ke QR code customer
                            </p>
                        </div>
                    </div>

                    {{-- Manual mode --}}
                    <div x-show="mode === 'manual'" x-cloak>
                        <p class="text-sm text-gray-600 mb-4">
                            Masukkan token QR secara manual. Token dapat disalin dari halaman QR Check-in milik customer.
                        </p>
                        <div class="flex gap-2">
                            <input type="text" x-model="token" @keydown.enter="verify()"
                                   placeholder="Paste token QR di sini..."
                                   class="flex-1 px-4 py-3 text-sm border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-mono">
                            <button @click="verify()" :disabled="verifying || !token.trim()"
                                    class="px-5 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                <span x-show="!verifying">Verifikasi</span>
                                <span x-show="verifying" class="flex items-center gap-1.5">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Proses...
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- Result panel --}}
                    <template x-if="result">
                        <div class="mt-5">
                            <div :class="result.success
                                    ? 'bg-green-50 border-green-200'
                                    : 'bg-red-50 border-red-200'"
                                 class="border rounded-2xl p-5">

                                {{-- Success --}}
                                <template x-if="result.success">
                                    <div>
                                        {{-- Play time banner --}}
                                        <template x-if="result.is_play_time">
                                            <div class="mb-4 flex items-center gap-2 bg-indigo-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold">
                                                <span class="text-lg">🏃</span>
                                                Waktunya main! Lapangan sudah siap.
                                            </div>
                                        </template>
                                        <template x-if="!result.is_play_time">
                                            <div class="mb-4 flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-2.5 rounded-xl text-sm">
                                                <svg class="w-4 h-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Lapangan siap pukul <strong class="mx-1" x-text="result.play_time"></strong>
                                                (<span x-text="result.minutes_to_play"></span> menit lagi)
                                            </div>
                                        </template>

                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-bold text-green-800">Check-in Berhasil</p>
                                                <p class="text-xs text-green-600 font-mono" x-text="result.booking_code"></p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3 text-sm">
                                            <div class="bg-white rounded-xl p-3 border border-green-100">
                                                <p class="text-xs text-gray-500 mb-0.5">Pelanggan</p>
                                                <p class="font-semibold text-gray-900" x-text="result.customer"></p>
                                            </div>
                                            <div class="bg-white rounded-xl p-3 border border-green-100">
                                                <p class="text-xs text-gray-500 mb-0.5">Lapangan</p>
                                                <p class="font-semibold text-gray-900" x-text="result.court"></p>
                                            </div>
                                            <div class="bg-white rounded-xl p-3 border border-green-100">
                                                <p class="text-xs text-gray-500 mb-0.5">Tanggal</p>
                                                <p class="font-semibold text-gray-900" x-text="result.date"></p>
                                            </div>
                                            <div class="bg-white rounded-xl p-3 border border-green-100">
                                                <p class="text-xs text-gray-500 mb-0.5">Jam Main</p>
                                                <p class="font-semibold text-gray-900" x-text="result.time"></p>
                                            </div>
                                        </div>
                                        <button @click="resetResult()"
                                                class="mt-4 w-full py-2.5 text-sm font-medium text-green-700 bg-green-100 hover:bg-green-200 rounded-xl transition">
                                            Scan Berikutnya
                                        </button>
                                    </div>
                                </template>

                                {{-- Error --}}
                                <template x-if="!result.success">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-red-800 text-sm" x-text="result.message"></p>
                                            <button @click="resetResult()"
                                                    class="mt-3 px-4 py-2 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition">
                                                Coba Lagi
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Info card --}}
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Cara kerja check-in</p>
                        <ol class="space-y-1 text-blue-700 text-xs list-decimal list-inside">
                            <li>Customer membuka halaman QR di aplikasi (menu Booking → Lihat QR Check-in)</li>
                            <li>Arahkan kamera scanner ke QR code yang ditampilkan customer</li>
                            <li>Sistem otomatis memverifikasi dan menandai booking sebagai check-in</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- jsQR library (loaded from CDN) --}}
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<style>
@keyframes scanLine {
    0%   { top: 8%; }
    50%  { top: 88%; }
    100% { top: 8%; }
}
</style>

<script>
function qrScanner() {
    return {
        mode: 'camera',
        token: '',
        result: null,
        verifying: false,
        scanning: false,
        cameraReady: false,
        cameraError: null,
        stream: null,
        animFrame: null,
        lastScanned: null,
        cooldown: false,

        init() {
            this.startCamera();
        },

        async startCamera() {
            this.cameraError = null;
            this.cameraReady = false;
            this.scanning = false;

            // Stop existing stream first
            this.stopCamera();

            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: 'environment' }, // rear camera on mobile
                        width:  { ideal: 1280 },
                        height: { ideal: 720 },
                    }
                });

                const video = this.$refs.video;
                video.srcObject = this.stream;

                video.addEventListener('loadedmetadata', () => {
                    video.play().then(() => {
                        this.cameraReady = true;
                        this.scanning = true;
                    });
                }, { once: true });

            } catch (err) {
                console.error('Camera error:', err);
                if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                    this.cameraError = 'Izin kamera ditolak. Aktifkan akses kamera di pengaturan browser.';
                } else if (err.name === 'NotFoundError') {
                    this.cameraError = 'Kamera tidak ditemukan di perangkat ini.';
                } else {
                    this.cameraError = 'Tidak dapat mengakses kamera: ' + err.message;
                }
            }
        },

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
            if (this.animFrame) {
                cancelAnimationFrame(this.animFrame);
                this.animFrame = null;
            }
            this.scanning = false;
            this.cameraReady = false;
        },

        // x-effect watches scanning + cameraReady reactively
        scanLoop() {
            if (!this.scanning || !this.cameraReady) return;
            this.tick();
        },

        tick() {
            if (!this.scanning || !this.cameraReady) return;

            const video  = this.$refs.video;
            const canvas = this.$refs.canvas;

            if (!video || video.readyState < 2) {
                this.animFrame = requestAnimationFrame(() => this.tick());
                return;
            }

            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;

            const ctx = canvas.getContext('2d', { willReadFrequently: true });
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: 'dontInvert',
            });

            if (code && code.data && !this.cooldown && !this.verifying) {
                const token = this.extractToken(code.data);
                if (token) {
                    this.scanning  = false;
                    this.token     = token;
                    this.verify();
                }
            }

            this.animFrame = requestAnimationFrame(() => this.tick());
        },

        /**
         * QR contains a full URL like:
         * http://localhost/manage/checkin?token=UUID
         * OR just a raw UUID.
         * Extract the token in both cases.
         */
        extractToken(raw) {
            try {
                const url = new URL(raw);
                return url.searchParams.get('token') || raw;
            } catch (_) {
                // Not a URL — return as-is (raw UUID)
                return raw.trim();
            }
        },

        async verify() {
            if (!this.token.trim()) return;

            this.verifying = true;
            this.result    = null;
            this.cooldown  = true;

            try {
                const res = await fetch('{{ route('manage.checkin.verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type':  'application/json',
                        'X-CSRF-TOKEN':  document.querySelector('meta[name=csrf-token]').content,
                        'Accept':        'application/json',
                    },
                    body: JSON.stringify({ qr_token: this.token }),
                });

                this.result = await res.json();

            } catch (e) {
                this.result = { success: false, message: 'Terjadi kesalahan koneksi. Coba lagi.' };
            } finally {
                this.verifying = false;
                this.token     = '';

                // 3-second cooldown before next scan attempt
                setTimeout(() => { this.cooldown = false; }, 3000);
            }
        },

        resetResult() {
            this.result    = null;
            this.cooldown  = false;
            this.token     = '';

            if (this.mode === 'camera') {
                this.scanning = true;
            }
        },
    };
}
</script>
