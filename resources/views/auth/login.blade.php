<x-guest-layout>

    <div x-data="loginPage()" x-init="init()" class="relative">

        {{-- Header Section --}}
        <div class="mb-7">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-electric-blue border border-blue-200/60 text-xs font-bold uppercase tracking-wider mb-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-field-green"></span>
                Autentikasi Akun
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-pitch-black tracking-tight">Masuk ke Akun</h1>
            <p class="text-on-surface-variant text-sm mt-1.5">Selamat datang kembali! Masukkan kredensial akun Anda untuk melanjutkan.</p>
        </div>

        {{-- Interactive Login Form --}}
        <form method="POST" action="{{ route('login') }}" @submit.prevent="handleSubmit" class="space-y-4" novalidate>
            @csrf

            {{-- Email Input Field --}}
            <div>
                <label for="email" class="block text-xs font-bold text-pitch-black uppercase tracking-wider mb-1.5">
                    Alamat Email <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] transition-colors"
                          :class="errors.email || {{ $errors->has('email') ? 'true' : 'false' }} ? 'text-red-500' : 'text-on-surface-variant'">
                        mail
                    </span>
                    <input id="email" type="email" name="email"
                           x-ref="emailInput"
                           x-model="email"
                           @input="clearError('email')"
                           required autofocus autocomplete="username"
                           placeholder="nama@email.com"
                           class="w-full pl-10 pr-4 py-3 text-sm bg-surface-light border rounded-xl outline-none transition placeholder:text-on-surface-variant text-pitch-black font-medium"
                           :class="errors.email || {{ $errors->has('email') ? 'true' : 'false' }}
                                ? 'border-red-500 bg-red-50/20 focus:border-red-500 focus:ring-2 focus:ring-red-100'
                                : 'border-stadium-gray focus:border-electric-blue focus:bg-white focus:ring-2 focus:ring-blue-50'">
                </div>
                <template x-if="errors.email">
                    <p class="text-red-600 text-xs mt-1.5 flex items-center gap-1 font-semibold" x-text="errors.email"></p>
                </template>
                @if ($errors->has('email'))
                    <p class="text-red-600 text-xs mt-1.5 flex items-center gap-1 font-semibold" x-show="!errors.email">
                        <span class="material-symbols-outlined text-[15px] shrink-0">cancel</span>
                        {{ $errors->first('email') }}
                    </p>
                @endif
            </div>

            {{-- Password Input Field with Toggle Visibility --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold text-pitch-black uppercase tracking-wider">
                        Password <span class="text-red-500">*</span>
                    </label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           tabindex="-1"
                           class="text-xs text-electric-blue hover:text-blue-700 font-bold transition">
                            Lupa password?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] transition-colors"
                          :class="errors.password || {{ $errors->has('password') ? 'true' : 'false' }} ? 'text-red-500' : 'text-on-surface-variant'">
                        lock
                    </span>
                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password"
                           x-ref="passwordInput"
                           x-model="password"
                           @input="clearError('password')"
                           required autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full pl-10 pr-11 py-3 text-sm bg-surface-light border rounded-xl outline-none transition placeholder:text-on-surface-variant text-pitch-black font-medium"
                           :class="errors.password || {{ $errors->has('password') ? 'true' : 'false' }}
                                ? 'border-red-500 bg-red-50/20 focus:border-red-500 focus:ring-2 focus:ring-red-100'
                                : 'border-stadium-gray focus:border-electric-blue focus:bg-white focus:ring-2 focus:ring-blue-50'">
                    
                    {{-- Visibility Toggle Button --}}
                    <button type="button" @click="showPassword = !showPassword"
                            tabindex="-1"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-pitch-black transition p-0.5 rounded focus:outline-none">
                        <span class="material-symbols-outlined text-[19px]" x-text="showPassword ? 'visibility_off' : 'visibility'"></span>
                    </button>
                </div>
                <template x-if="errors.password">
                    <p class="text-red-600 text-xs mt-1.5 flex items-center gap-1 font-semibold" x-text="errors.password"></p>
                </template>
                @if ($errors->has('password'))
                    <p class="text-red-600 text-xs mt-1.5 flex items-center gap-1 font-semibold" x-show="!errors.password">
                        <span class="material-symbols-outlined text-[15px] shrink-0">cancel</span>
                        {{ $errors->first('password') }}
                    </p>
                @endif
            </div>

            {{-- Remember Me & Role Badge Indicator --}}
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" name="remember"
                           class="w-4 h-4 rounded border-stadium-gray text-electric-blue focus:ring-0 focus:ring-offset-0 cursor-pointer">
                    <span class="text-xs font-semibold text-on-surface-variant">Ingat saya di perangkat ini</span>
                </label>
            </div>

            {{-- Submit Button --}}
            <div class="pt-2">
                <button type="submit"
                        :disabled="isSubmitting"
                        class="w-full py-3.5 px-4 bg-electric-blue hover:bg-blue-700 active:scale-95 text-white text-sm font-bold rounded-2xl transition shadow-lg shadow-blue-500/20 flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span x-show="!isSubmitting" class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">login</span>
                        <span>Masuk Sekarang</span>
                    </span>
                    <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                        <span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                        <span>Memverifikasi akun...</span>
                    </span>
                </button>
            </div>
        </form>

        {{-- Fast Demo Account Picker (Collapsible for easy testing of all roles) --}}
        <div class="mt-6 pt-5 border-t border-stadium-gray/80" x-data="{ openDemo: false }">
            <button type="button" @click="openDemo = !openDemo"
                    class="w-full flex items-center justify-between text-xs font-bold text-on-surface-variant hover:text-electric-blue transition py-1">
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">badge</span>
                    Kredensial Demo Akun (Testing)
                </span>
                <span class="material-symbols-outlined text-[18px] transition-transform" :class="openDemo ? 'rotate-180' : ''">expand_more</span>
            </button>
            <div x-show="openDemo" x-cloak x-transition class="mt-3 grid grid-cols-3 gap-2">
                <button type="button" @click="fillCreds('admin@tiketinaja.test', 'password')"
                        class="p-2 text-left bg-surface-light hover:bg-purple-50 border border-stadium-gray hover:border-purple-300 rounded-xl transition">
                    <span class="block text-[10px] font-bold uppercase text-purple-700">Admin</span>
                    <span class="block text-[11px] font-mono text-pitch-black truncate">admin@...</span>
                </button>
                <button type="button" @click="fillCreds('manager@tiketinaja.test', 'password')"
                        class="p-2 text-left bg-surface-light hover:bg-blue-50 border border-stadium-gray hover:border-blue-300 rounded-xl transition">
                    <span class="block text-[10px] font-bold uppercase text-electric-blue">Manager</span>
                    <span class="block text-[11px] font-mono text-pitch-black truncate">manager@...</span>
                </button>
                <button type="button" @click="fillCreds('customer@tiketinaja.test', 'password')"
                        class="p-2 text-left bg-surface-light hover:bg-green-50 border border-stadium-gray hover:border-green-300 rounded-xl transition">
                    <span class="block text-[10px] font-bold uppercase text-field-green">Customer</span>
                    <span class="block text-[11px] font-mono text-pitch-black truncate">customer@...</span>
                </button>
            </div>
        </div>

        {{-- Divider & Register Link --}}
        <div class="mt-6 pt-4 text-center border-t border-stadium-gray">
            <p class="text-sm text-on-surface-variant">
                Belum memiliki akun?
                <a href="{{ route('register') }}"
                   class="font-bold text-electric-blue hover:underline transition ml-1">
                    Daftar akun gratis
                </a>
            </p>
        </div>

        {{-- ── GLOBAL POPUP ALERT MODAL (Alpine.js) ─────────────────── --}}
        <div x-show="popup.show" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                 @click="closePopup()"></div>

            {{-- Modal Card --}}
            <div class="relative w-full max-w-sm bg-white border border-stadium-gray rounded-3xl shadow-2xl p-6 z-10 text-center"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 @keydown.escape.window="closePopup()">

                {{-- Status Icon Badge --}}
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 border shadow-sm"
                     :class="{
                        'bg-red-50 text-red-600 border-red-200': popup.type === 'error',
                        'bg-amber-50 text-amber-600 border-amber-200': popup.type === 'warning',
                        'bg-blue-50 text-electric-blue border-blue-200': popup.type === 'info',
                        'bg-green-50 text-field-green border-green-200': popup.type === 'success'
                     }">
                    <span class="material-symbols-outlined text-[34px]"
                          x-text="popup.type === 'error' ? 'cancel' : (popup.type === 'warning' ? 'warning' : (popup.type === 'success' ? 'check_circle' : 'info'))"></span>
                </div>

                {{-- Popup Title --}}
                <h3 class="text-lg font-extrabold text-pitch-black leading-tight" x-text="popup.title"></h3>

                {{-- Popup Message --}}
                <p class="text-sm text-on-surface-variant mt-2 leading-relaxed" x-text="popup.message"></p>

                {{-- Action Button --}}
                <div class="mt-6">
                    <button type="button" @click="closePopup()"
                            :class="{
                                'bg-red-600 hover:bg-red-700': popup.type === 'error',
                                'bg-amber-600 hover:bg-amber-700': popup.type === 'warning',
                                'bg-electric-blue hover:bg-blue-700': popup.type === 'info' || popup.type === 'success'
                            }"
                            class="w-full py-3 text-xs font-bold text-white rounded-xl active:scale-95 transition shadow-sm">
                        <span x-text="popup.type === 'error' ? 'Coba Lagi' : 'Mengerti'"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Alpine.js Page Controller Script --}}
    <script>
        function loginPage() {
            return {
                email: '{{ old('email') }}',
                password: '',
                showPassword: false,
                isSubmitting: false,
                errors: {
                    email: null,
                    password: null
                },
                popup: {
                    show: false,
                    type: 'error',
                    title: '',
                    message: ''
                },

                init() {
                    // Check if server returned validation errors
                    @if ($errors->any())
                        this.triggerPopup(
                            'error',
                            'Gagal Masuk Akun',
                            '{{ addslashes($errors->first()) }}'
                        );
                    @elseif (session('status'))
                        this.triggerPopup(
                            'success',
                            'Informasi Akun',
                            '{{ addslashes(session('status')) }}'
                        );
                    @elseif (session('error'))
                        this.triggerPopup(
                            'error',
                            'Peringatan Sistem',
                            '{{ addslashes(session('error')) }}'
                        );
                    @endif
                },

                clearError(field) {
                    this.errors[field] = null;
                },

                fillCreds(email, password) {
                    this.email = email;
                    this.password = password;
                    this.clearError('email');
                    this.clearError('password');
                },

                validateEmailFormat(email) {
                    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return re.test(String(email).toLowerCase());
                },

                triggerPopup(type, title, message) {
                    this.popup.type = type;
                    this.popup.title = title;
                    this.popup.message = message;
                    this.popup.show = true;
                },

                closePopup() {
                    this.popup.show = false;
                    this.isSubmitting = false;

                    // Focus back to appropriate input if there was an error
                    if (this.errors.email) {
                        this.$nextTick(() => this.$refs.emailInput?.focus());
                    } else if (this.errors.password) {
                        this.$nextTick(() => this.$refs.passwordInput?.focus());
                    }
                },

                handleSubmit(e) {
                    this.errors.email = null;
                    this.errors.password = null;

                    const trimmedEmail = (this.email || '').trim();
                    const trimmedPassword = (this.password || '').trim();

                    // 1. Validasi Email Kosong
                    if (!trimmedEmail) {
                        this.errors.email = 'Alamat email wajib diisi.';
                        this.triggerPopup(
                            'warning',
                            'Email Wajib Diisi',
                            'Silakan masukkan alamat email akun Anda untuk masuk.'
                        );
                        return;
                    }

                    // 2. Validasi Format Email
                    if (!this.validateEmailFormat(trimmedEmail)) {
                        this.errors.email = 'Format email tidak valid (contoh: user@gmail.com).';
                        this.triggerPopup(
                            'warning',
                            'Format Email Salah',
                            'Pastikan format email Anda sudah benar, contoh: nama@domain.com.'
                        );
                        return;
                    }

                    // 3. Validasi Password Kosong
                    if (!trimmedPassword) {
                        this.errors.password = 'Password wajib diisi.';
                        this.triggerPopup(
                            'warning',
                            'Password Wajib Diisi',
                            'Silakan masukkan password akun Anda untuk melanjutkan.'
                        );
                        return;
                    }

                    // 4. Validasi Panjang Password Minimal
                    if (trimmedPassword.length < 6) {
                        this.errors.password = 'Password minimal harus 6 karakter.';
                        this.triggerPopup(
                            'warning',
                            'Password Terlalu Pendek',
                            'Password harus memiliki minimal 6 karakter. Silakan periksa kembali.'
                        );
                        return;
                    }

                    // Validasi lolos, submit form ke server
                    this.isSubmitting = true;
                    e.target.submit();
                }
            };
        }
    </script>

</x-guest-layout>
