<x-guest-layout>

    <div class="mb-7">
        <h1 class="text-2xl font-bold text-pitch-black">Buat akun baru</h1>
        <p class="text-on-surface-variant text-sm mt-1.5">Gratis. Booking lapangan dalam 2 menit.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Name --}}
        <div>
            <label for="name" class="block text-sm font-medium text-on-surface-variant mb-1.5">
                Nama Lengkap
            </label>
            <input id="name" type="text" name="name"
                   value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Nama lengkap Anda"
                   class="w-full px-4 py-3 text-sm bg-white border-2 border-stadium-gray rounded-lg outline-none transition focus:ring-0 placeholder:text-on-surface-variant text-pitch-black
                          {{ $errors->get('name') ? 'border-red-400/60 bg-red-500/10 focus:ring-red-400/40 focus:border-red-400' : 'border-stadium-gray focus:border-electric-blue focus:ring-0' }}">
            @error('name')
                <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-on-surface-variant mb-1.5">
                Alamat Email
            </label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}" required autocomplete="username"
                   placeholder="email@contoh.com"
                   class="w-full px-4 py-3 text-sm bg-white border-2 border-stadium-gray rounded-lg outline-none transition focus:ring-0 placeholder:text-on-surface-variant text-pitch-black
                          {{ $errors->get('email') ? 'border-red-400/60 bg-red-500/10 focus:ring-red-400/40 focus:border-red-400' : 'border-stadium-gray focus:border-electric-blue focus:ring-0' }}">
            @error('email')
                <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Phone --}}
        <div>
            <label for="phone" class="block text-sm font-medium text-on-surface-variant mb-1.5">
                No. Telepon
                <span class="text-on-surface-variant font-normal">(opsional)</span>
            </label>
            <input id="phone" type="tel" name="phone"
                   value="{{ old('phone') }}" autocomplete="tel"
                   placeholder="08xx-xxxx-xxxx"
                   class="w-full px-4 py-3 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg outline-none transition focus:border-electric-blue focus:ring-0 focus:ring-0 placeholder:text-on-surface-variant">
            @error('phone')
                <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-on-surface-variant mb-1.5">
                Password
            </label>
            <input id="password" type="password" name="password"
                   required autocomplete="new-password"
                   placeholder="Minimal 8 karakter"
                   class="w-full px-4 py-3 text-sm bg-white border-2 border-stadium-gray rounded-lg outline-none transition focus:ring-0 placeholder:text-on-surface-variant text-pitch-black
                          {{ $errors->get('password') ? 'border-red-400/60 bg-red-500/10 focus:ring-red-400/40 focus:border-red-400' : 'border-stadium-gray focus:border-electric-blue focus:ring-0' }}">
            @error('password')
                <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-on-surface-variant mb-1.5">
                Konfirmasi Password
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   required autocomplete="new-password"
                   placeholder="Ulangi password"
                   class="w-full px-4 py-3 text-sm bg-white border-2 border-stadium-gray rounded-lg outline-none transition focus:ring-0 placeholder:text-on-surface-variant text-pitch-black
                          {{ $errors->get('password_confirmation') ? 'border-red-400/60 bg-red-500/10 focus:ring-red-400/40 focus:border-red-400' : 'border-stadium-gray focus:border-electric-blue focus:ring-0' }}">
            @error('password_confirmation')
                <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <p class="text-xs text-on-surface-variant leading-relaxed">
            Dengan mendaftar, Anda menyetujui syarat & ketentuan penggunaan platform.
        </p>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-3 bg-electric-blue hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-blue-500/20">
            Buat Akun
        </button>
    </form>

    {{-- Login link --}}
    <div class="flex items-center gap-3 my-5">
        <div class="flex-1 h-px bg-stadium-gray"></div>
        <span class="text-xs text-on-surface-variant">sudah punya akun?</span>
        <div class="flex-1 h-px bg-stadium-gray"></div>
    </div>

    <a href="{{ route('login') }}"
       class="block w-full py-3 text-center text-sm font-semibold text-pitch-black bg-slate-100 hover:bg-slate-200 rounded-xl transition">
        Masuk sekarang
    </a>

</x-guest-layout>