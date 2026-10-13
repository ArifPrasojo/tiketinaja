<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-pitch-black">Scanner QR Check-in</h1>
                <p class="text-sm text-on-surface-variant mt-0.5">Scan tiket QR customer untuk verifikasi kehadiran dan akses lapangan</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-electric-blue border border-blue-200/60 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-field-green animate-pulse"></span>
                    Scanner Siap
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
            {{-- Main Scanner Card --}}
            <div class="bg-white border border-stadium-gray rounded-2xl sporty-shadow overflow-hidden"
                 x-data="qrScanner()" x-init="init()" x-effect="scanLoop()">

                {{-- Mode Switcher Tabs --}}
                <div class="flex border-b border-stadium-gray bg-surface-light p-1.5 gap-1.5">
                    <button @click="mode = 'camera'; startCamera()"
                            :class="mode === 'camera' ? 'bg-white text-electric-blue shadow-sm font-bold border-stadium-gray' : 'text-on-surface-variant hover:text-pitch-black font-semibold'"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-sm rounded-xl border border-transparent transition-all">
                        <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                        Kamera Scanner
                    </button>
                    <button @click="mode = 'manual'; stopCamera()"
                            :class="mode === 'manual' ? 'bg-white text-electric-blue shadow-sm font-bold border-stadium-gray' : 'text-on-surface-variant hover:text-pitch-black font-semibold'"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-sm rounded-xl border border-transparent transition-all">
                        <span class="material-symbols-outlined text-[18px]">keyboard</span>
                        Input Kode Manual
                    </button>
                </div>

                <div class="p-6">

                    {{-- Camera Mode --}}
                    <div x-show="mode === 'camera'" x-cloak>

                        {{-- Camera Error State --}}
                        <div x-show="cameraError" class="text-center py-10">
                            <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <span class="material-symbols-outlined text-[28px] text-red-500">no_photography</span>
                            </div>
                            <p class="text-sm font-bold text-pitch-black mb-1">Akses kamera tidak diizinkan</p>
                            <p class="text-xs text-on-surface-variant mb-4" x-text="cameraError"></p>
                            <button @click="startCamera()"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold bg-electric-blue text-white rounded-xl hover:bg-blue-700 transition">
                                <span class="material-symbols-outlined text-[16px]">refresh</span>
                                Coba Hubungkan Ulang
                            </button>
                        </div>

                        {{-- Camera Loading State --}}
                        <div x-show="!cameraReady && !cameraError" class="text-center py-12">
                            <div class="inline-flex items-center gap-2.5 text-on-surface-variant text-sm font-semibold">
                                <span class="material-symbols-outlined animate-spin text-[22px] text-electric-blue">progress_activity</span>
                                Menghubungkan kamera perangkat...
                            </div>
                        </div>

                        {{-- Camera Video Viewport --}}
                        <div x-show="cameraReady && !cameraError" class="relative flex flex-col items-center">
                            <div class="relative rounded-2xl overflow-hidden bg-pitch-black w-full max-w-[360px] shadow-lg border-2 border-stadium-gray" style="aspect-ratio: 1/1;">
                                <video x-ref="video" autoplay playsinline muted
                                       class="w-full h-full object-cover"
                                       @play="cameraReady = true"></video>

                                {{-- Laser/Grid Overlay --}}
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="absolute inset-0 bg-pitch-black/30"></div>

                                    <div class="relative w-52 h-52">
                                        <div class="absolute inset-0 bg-transparent"
                                             style="box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.45)"></div>

                                        {{-- Corner Markers --}}
                                        <div class="absolute top-0 left-0 w-8 h-8 rounded-tl-lg"
                                             style="border-width: 4px 0 0 4px; border-color: #0052FF"></div>
                                        <div class="absolute top-0 right-0 w-8 h-8 rounded-tr-lg"
                                             style="border-width: 4px 4px 0 0; border-color: #0052FF"></div>
                                        <div class="absolute bottom-0 left-0 w-8 h-8 rounded-bl-lg"
                                             style="border-width: 0 0 4px 4px; border-color: #0052FF"></div>
                                        <div class="absolute bottom-0 right-0 w-8 h-8 rounded-br-lg"
                                             style="border-width: 0 4px 4px 0; border-color: #0052FF"></div>

                                        {{-- Scanning Laser Line Animation --}}
                                        <div x-show="scanning" class="absolute left-1 right-1 h-0.5 bg-electric-blue shadow-[0_0_8px_#0052FF] rounded-full"
                                             style="animation: scanLine 2s ease-in-out infinite; top: 50%"></div>
                                    </div>
                                </div>

                                {{-- Live Status Indicator --}}
                                <div class="absolute top-3 left-1/2 -translate-x-1/2">
                                    <span x-show="scanning"
                                          class="inline-flex items-center gap-1.5 px-3 py-1 bg-electric-blue text-white text-xs font-bold rounded-full shadow-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        Mencari QR Code...
                                    </span>
                                    <span x-show="verifying"
                                          class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500 text-white text-xs font-bold rounded-full shadow-md">
                                        <span class="material-symbols-outlined animate-spin text-[14px]">progress_activity</span>
                                        Memverifikasi...
                                    </span>
                                </div>
                            </div>

                            <canvas x-ref="canvas" class="hidden"></canvas>

                            <p class="text-xs font-medium text-center text-on-surface-variant mt-3 flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-[15px] text-electric-blue">center_focus_strong</span>
                                Posisikan QR Code tiket di dalam bingkai kotak
                            </p>
                        </div>
                    </div>

                    {{-- Manual Input Mode --}}
                    <div x-show="mode === 'manual'" x-cloak class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1.5">
                                Kode Token QR Tiket
                            </label>
                            <div class="flex gap-2">
                                <input type="text" x-model="token" @keydown.enter="verify()"
                                       placeholder="Paste kode token QR di sini..."
                                       class="flex-1 px-4 py-3 text-sm bg-surface-light border border-stadium-gray text-pitch-black rounded-xl outline-none focus:border-electric-blue focus:bg-white font-mono placeholder:text-on-surface-variant transition">
                                <button @click="verify()" :disabled="verifying || !token.trim()"
                                        class="px-6 py-3 bg-electric-blue text-white text-sm font-bold rounded-xl hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed active:scale-95 transition shadow-sm shrink-0">
                                    <span x-show="!verifying" class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[18px]">verified</span>
                                        Verifikasi
                                    </span>
                                    <span x-show="verifying" class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span>
                                        Proses...
                                    </span>
                                </button>
                            </div>
                            <p class="text-xs text-on-surface-variant mt-2">
                                Token dapat disalin langsung dari halaman tiket check-in milik customer.
                            </p>
                        </div>
                    </div>

                    {{-- Result Panel --}}
                    <template x-if="result">
                        <div class="mt-6">
                            {{-- Success Result --}}
                            <template x-if="result.success">
                                <div class="bg-green-50 border border-green-200 rounded-2xl p-5 space-y-4 shadow-sm">
                                    <template x-if="result.is_play_time">
                                        <div class="flex items-center gap-2 bg-field-green text-white px-4 py-2.5 rounded-xl text-sm font-bold">
                                            <span class="material-symbols-outlined text-[20px]">sprint</span>
                                            Sesi bermain sedang berlangsung! Lapangan sudah siap.
                                        </div>
                                    </template>
                                    <template x-if="!result.is_play_time">
                                        <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-2.5 rounded-xl text-sm font-semibold">
                                            <span class="material-symbols-outlined text-[18px] text-amber-600 shrink-0">schedule</span>
                                            Lapangan siap pukul <strong class="mx-1" x-text="result.play_time"></strong>
                                            (<span x-text="result.minutes_to_play"></span> menit lagi)
                                        </div>
                                    </template>

                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-field-green text-white flex items-center justify-center shrink-0 shadow-sm">
                                            <span class="material-symbols-outlined text-[26px]">check_circle</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-pitch-black text-base">Check-in Berhasil Terverifikasi</p>
                                            <p class="text-xs font-mono font-bold text-electric-blue mt-0.5" x-text="result.booking_code"></p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 text-xs bg-white p-3.5 rounded-xl border border-green-200">
                                        <div>
                                            <p class="text-on-surface-variant font-medium">Pelanggan</p>
                                            <p class="font-bold text-pitch-black text-sm mt-0.5" x-text="result.customer"></p>
                                        </div>
                                        <div>
                                            <p class="text-on-surface-variant font-medium">Lapangan</p>
                                            <p class="font-bold text-pitch-black text-sm mt-0.5" x-text="result.court"></p>
                                        </div>
                                        <div>
                                            <p class="text-on-surface-variant font-medium">Tanggal</p>
                                            <p class="font-bold text-pitch-black text-sm mt-0.5" x-text="result.date"></p>
                                        </div>
                                        <div>
                                            <p class="text-on-surface-variant font-medium">Jam Main</p>
                                            <p class="font-bold text-electric-blue text-sm mt-0.5" x-text="result.time"></p>
                                        </div>
                                    </div>

                                    <button @click="resetResult()"
                                            class="w-full py-2.5 text-sm font-bold text-field-green bg-white border border-green-200 hover:bg-green-100 rounded-xl transition shadow-sm">
                                        Scan Tiket Berikutnya
                                    </button>
                                </div>
                            </template>

                            {{-- Error Result --}}
                            <template x-if="!result.success">
                                <div class="bg-red-50 border border-red-200 rounded-2xl p-5 flex items-start gap-3 shadow-sm">
                                    <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[22px]">cancel</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold text-red-700 text-sm">Verifikasi Gagal</p>
                                        <p class="text-xs text-red-600 mt-1" x-text="result.message"></p>
                                        <button @click="resetResult()"
                                                class="mt-3 px-4 py-1.5 text-xs font-bold text-red-700 bg-white border border-red-200 hover:bg-red-100 rounded-lg transition shadow-sm">
                                            Coba Lagi
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Guide Card --}}
            <div class="bg-blue-50 border border-blue-200/70 rounded-2xl p-5 sporty-shadow">
                <div class="flex items-start gap-3.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-electric-blue flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]">info</span>
                    </div>
                    <div class="text-xs text-pitch-black leading-relaxed">
                        <p class="font-bold text-sm mb-1 text-electric-blue">Prosedur Verifikasi Check-in</p>
                        <ol class="space-y-1 text-on-surface-variant list-decimal list-inside">
                            <li>Customer menunjukkan QR code tiket dari menu <strong class="text-pitch-black">Booking Saya → Lihat QR Check-in</strong>.</li>
                            <li>Arahkan kamera scanner ke layar handphone customer hingga kotak hijau mengunci QR.</li>
                            <li>Sistem otomatis menandai status customer sebagai <strong class="text-field-green">Sudah Check-in / Sedang Bermain</strong>.</li>
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

            this.stopCamera();

            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: 'environment' },
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
                    this.cameraError = 'Izin kamera ditolak. Aktifkan izin kamera pada browser Anda.';
                } else if (err.name === 'NotFoundError') {
                    this.cameraError = 'Kamera tidak ditemukan pada perangkat ini.';
                } else {
                    this.cameraError = 'Gagal mengakses kamera: ' + err.message;
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

        extractToken(raw) {
            try {
                const url = new URL(raw);
                return url.searchParams.get('token') || raw;
            } catch (_) {
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
                this.result = { success: false, message: 'Terjadi gangguan koneksi. Silakan coba lagi.' };
            } finally {
                this.verifying = false;
                this.token     = '';

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
