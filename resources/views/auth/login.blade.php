<x-guest-layout>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-pitch-black">Masuk ke akun</h1>
        <p class="text-on-surface-variant text-sm mt-1.5">Selamat datang kembali. Masukkan kredensial Anda.</p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-on-surface-variant mb-1.5">
                Alamat Email
            </label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="email@contoh.com"
                   class="w-full px-4 py-3 text-sm bg-white border-2 border-stadium-gray rounded-lg outline-none transition focus:ring-0 placeholder:text-on-surface-variant text-pitch-black
                          {{ $errors->get('email') ? 'border-red-400/60 bg-red-500/10 focus:ring-red-400/40 focus:border-red-400' : 'border-stadium-gray focus:border-electric-blue focus:ring-0' }}">
            @error('email')
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px] shrink-0">cancel</span>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-on-surface-variant">Password</label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-xs text-electric-blue hover:text-electric-blue font-medium transition">
                        Lupa password?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password"
                   placeholder="••••••••"
                   class="w-full px-4 py-3 text-sm bg-white border-2 border-stadium-gray rounded-lg outline-none transition focus:ring-0 placeholder:text-on-surface-variant text-pitch-black
                          {{ $errors->get('password') ? 'border-red-400/60 bg-red-500/10 focus:ring-red-400/40 focus:border-red-400' : 'border-stadium-gray focus:border-electric-blue focus:ring-0' }}">
            @error('password')
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px] shrink-0">cancel</span>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Remember me --}}
        <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" name="remember"
                   class="w-4 h-4 rounded bg-white border-stadium-gray text-electric-blue focus:ring-0">
            <span class="text-sm text-on-surface-variant">Ingat saya di perangkat ini</span>
        </label>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-3 bg-electric-blue hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-blue-500/20 mt-1">
            Masuk
        </button>
    </form>

    {{-- Divider --}}
    <div class="flex items-center gap-3 my-6">
        <div class="flex-1 h-px bg-stadium-gray"></div>
        <span class="text-xs text-on-surface-variant">atau</span>
        <div class="flex-1 h-px bg-stadium-gray"></div>
    </div>

    {{-- Register link --}}
    <p class="text-center text-sm text-on-surface-variant">
        Belum punya akun?
        <a href="{{ route('register') }}"
           class="font-semibold text-electric-blue hover:text-electric-blue transition">
            Daftar sekarang
        </a>
    </p>

</x-guest-layout>