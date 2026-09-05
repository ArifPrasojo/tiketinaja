<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-pitch-black">Scanner QR Check-in</h1>
            <p class="text-sm text-on-surface-variant mt-0.5">Scan QR code customer untuk verifikasi kedatangan</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
            <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow overflow-hidden"
                 x-data="qrScanner()" x-init="init()" x-effect="scanLoop()">

                <div class="flex border-b border-stadium-gray">
                    <button @click="mode = 'camera'; startCamera()"
                            :class="mode === 'camera' ? 'border-b-2 border-electric-blue text-electric-blue font-semibold' : 'text-on-surface-variant hover:text-pitch-black'"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3.5 text-sm transition">
                        <span class="material-symbols-outlined text-[16px]">camera_alt</span>
                        Scan Kamera
                    </button>
                    <button @click="mode = 'manual'; stopCamera()"
                            :class="mode === 'manual' ? 'border-b-2 border-electric-blue text-electric-blue font-semibold' : 'text-on-surface-variant hover:text-pitch-black'"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3.5 text-sm transition">
                        <span class="material-symbols-outlined text-[16px]">qr_code_scanner</span>
                        Input Manual
                    </button>
                </div>

                <div class="p-6">

                    <div x-show="mode === 'camera'" x-cloak>

                        <div x-show="cameraError" class="text-center py-10">
                            <div class="w-14 h-14 bg-red-500/10 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <span class="material-symbols-outlined text-[28px] text-red-400">no_photography</span>
                            </div>
                            <p class="text-sm font-semibold text-pitch-black mb-1">Akses kamera ditolak</p>
                            <p class="text-xs text-on-surface-variant mb-4" x-text="cameraError"></p>
                            <button @click="startCamera()"
                                    class="px-4 py-2 text-sm font-medium bg-electric-blue text-white rounded-xl hover:bg-blue-700 transition">
                                Coba Lagi
                            </button>
                        </div>

                        <div x-show="!cameraReady && !cameraError" class="text-center py-10">
                            <div class="inline-flex items-center gap-2 text-on-surface-variant text-sm">
                                <span class="material-symbols-outlined animate-spin text-[20px] text-electric-blue">progress_activity</span>
                                Memuat kamera...
                            </div>
                        </div>

                        <div x-show="cameraReady && !cameraError" class="relative flex justify-center">
                            <div class="relative rounded-xl overflow-hidden bg-pitch-black w-full max-w-[360px]" style="aspect-ratio: 1/1;">
                                <video x-ref="video" autoplay playsinline muted
                                       class="w-full h-full object-cover"
                                       @play="cameraReady = true"></video>

                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="absolute inset-0 bg-pitch-black/40"></div>

                                    <div class="relative w-52 h-52">
                                        <div class="absolute inset-0 bg-transparent"
                                             style="box-shadow: 0 0 0 9999px rgba(10,10,15,0.4)"></div>

                                        <div class="absolute top-0 left-0 w-8 h-8 border-t-3 border-l-3 border-emerald-400 rounded-tl-lg"
                                             style="border-width: 3px 0 0 3px"></div>
                                        <div class="absolute top-0 right-0 w-8 h-8 border-t-3 border-r-3 border-emerald-400 rounded-tr-lg"
                                             style="border-width: 3px 3px 0 0"></div>
                                        <div class="absolute bottom-0 left-0 w-8 h-8 border-b-3 border-l-3 border-emerald-400 rounded-bl-lg"
                                             style="border-width: 0 0 3px 3px"></div>
                                        <div class="absolute bottom-0 right-0 w-8 h-8 border-b-3 border-r-3 border-emerald-400 rounded-br-lg"
                                             style="border-width: 0 3px 3px 0"></div>

                                        <div x-show="scanning" class="absolute left-1 right-1 h-0.5 bg-electric-blue/80 rounded-full"
                                             style="animation: scanLine 2s ease-in-out infinite; top: 50%"></div>
                                    </div>
                                </div>

                                <div class="absolute top-3 left-1/2 -translate-x-1/2">
                                    <span x-show="scanning"
                                          class="inline-flex items-center gap-1.5 px-3 py-1 bg-electric-blue/90 text-white text-xs font-medium rounded-full backdrop-blur-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        Scanning...
                                    </span>
                                    <span x-show="verifying"
                                          class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/90 text-pitch-black text-xs font-medium rounded-full backdrop-blur-sm">
                                        <span class="material-symbols-outlined animate-spin text-[12px]">progress_activity</span>
                                        Memverifikasi...
                                    </span>
                                </div>
                            </div>

                            <canvas x-ref="canvas" class="hidden"></canvas>

                            <p class="text-xs text-center text-on-surface-variant mt-3">
                                Arahkan kamera ke QR code customer
                            </p>
                        </div>
                    </div>

                    <div x-show="mode === 'manual'" x-cloak>
                        <p class="text-sm text-on-surface-variant mb-4">
                            Masukkan token QR secara manual. Token dapat disalin dari halaman QR Check-in milik customer.
                        </p>
                        <div class="flex gap-2">
                            <input type="text" x-model="token" @keydown.enter="verify()"
                                   placeholder="Paste token QR di sini..."
                                   class="flex-1 px-4 py-3 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg outline-none focus:ring-0 focus:ring-0 focus:border-electric-blue transition font-mono placeholder:text-on-surface-variant">
                            <button @click="verify()" :disabled="verifying || !token.trim()"
                                    class="px-5 py-3 bg-electric-blue text-white text-sm font-semibold rounded-xl hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                <span x-show="!verifying">Verifikasi</span>
                                <span x-show="verifying" class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span>
                                    Proses...
                                </span>
                            </button>
                        </div>
                    </div>

                    <template x-if="result">
                        <div class="mt-5">
                            <div :class="result.success
                                    ? 'bg-blue-50 border-electric-blue/20'
                                    : 'bg-red-500/10 border-red-500/20'"
                                 class="border rounded-2xl p-5">

                                <template x-if="result.success">
                                    <div>
                                        <template x-if="result.is_play_time">
                                            <div class="mb-4 flex items-center gap-2 bg-electric-blue text-white px-4 py-2.5 rounded-xl text-sm font-semibold">
                                                <span class="material-symbols-outlined text-[20px]">sprint</span>
                                                Waktunya main! Lapangan sudah siap.
                                            </div>
                                        </template>
                                        <template x-if="!result.is_play_time">
                                            <div class="mb-4 flex items-center gap-2 bg-amber-500/15 border border-amber-500/20 text-amber-400 px-4 py-2.5 rounded-xl text-sm">
                                                <span class="material-symbols-outlined text-[16px] shrink-0 text-amber-400">schedule</span>
                                                Lapangan siap pukul <strong class="mx-1" x-text="result.play_time"></strong>
                                                (<span x-text="result.minutes_to_play"></span> menit lagi)
                                            </div>
                                        </template>

                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-[20px] text-electric-blue">check</span>
                                            </div>
                                            <div>
                                                <p class="font-bold text-electric-blue">Check-in Berhasil</p>
                                                <p class="text-xs text-electric-blue/80 font-mono" x-text="result.booking_code"></p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3 text-sm">
                                            <div class="bg-white rounded-xl p-3 border border-electric-blue/20">
                                                <p class="text-xs text-on-surface-variant mb-0.5">Pelanggan</p>
                                                <p class="font-semibold text-pitch-black" x-text="result.customer"></p>
                                            </div>
                                            <div class="bg-white rounded-xl p-3 border border-electric-blue/20">
                                                <p class="text-xs text-on-surface-variant mb-0.5">Lapangan</p>
                                                <p class="font-semibold text-pitch-black" x-text="result.court"></p>
                                            </div>
                                            <div class="bg-white rounded-xl p-3 border border-electric-blue/20">
                                                <p class="text-xs text-on-surface-variant mb-0.5">Tanggal</p>
                                                <p class="font-semibold text-pitch-black" x-text="result.date"></p>
                                            </div>
                                            <div class="bg-white rounded-xl p-3 border border-electric-blue/20">
                                                <p class="text-xs text-on-surface-variant mb-0.5">Jam Main</p>
                                                <p class="font-semibold text-pitch-black" x-text="result.time"></p>
                                            </div>
                                        </div>
                                        <button @click="resetResult()"
                                                class="mt-4 w-full py-2.5 text-sm font-medium text-electric-blue bg-blue-50 hover:bg-blue-700/25 rounded-xl transition">
                                            Scan Berikutnya
                                        </button>
                                    </div>
                                </template>

                                <template x-if="!result.success">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-full bg-red-500/20 flex items-center justify-center shrink-0 mt-0.5">
                                            <span class="material-symbols-outlined text-[16px] text-red-400">close</span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-red-400 text-sm" x-text="result.message"></p>
                                            <button @click="resetResult()"
                                                    class="mt-3 px-4 py-2 text-xs font-medium text-red-400 bg-red-500/10 hover:bg-red-500/20 rounded-lg transition">
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

            <div class="bg-sky-500/10 border border-sky-500/20 rounded-2xl p-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[20px] text-sky-400 shrink-0 mt-0.5">info</span>
                    <div class="text-sm text-sky-300">
                        <p class="font-semibold mb-1">Cara kerja check-in</p>
                        <ol class="space-y-1 text-sky-400/80 text-xs list-decimal list-inside">
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
