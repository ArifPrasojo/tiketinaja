<x-guest-layout>

    <div class="mb-8">
        <a href="{{ route('login') }}"
           class="inline-flex items-center gap-1.5 text-sm text-on-surface-variant hover:text-pitch-black mb-6 transition">
            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
            Kembali ke halaman masuk
        </a>

        <h1 class="text-2xl font-bold text-pitch-black">Lupa password?</h1>
        <p class="text-on-surface-variant text-sm mt-1.5 leading-relaxed">
            Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang password.
        </p>
    </div>

    <x-auth-session-status :status="session('status')" />

    @if(session('status'))
        <div class="mb-5 flex items-start gap-3 bg-blue-50 border border-electric-blue/30 text-electric-blue px-4 py-3.5 rounded-xl text-sm">
            <span class="material-symbols-outlined text-[20px] text-electric-blue shrink-0 mt-0.5">check_circle</span>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-on-surface-variant mb-1.5">
                Alamat Email
            </label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}" required autofocus
                   placeholder="email@contoh.com"
                   class="w-full px-4 py-3 text-sm bg-white border-2 border-stadium-gray rounded-lg outline-none transition focus:ring-0 placeholder:text-on-surface-variant text-pitch-black
                          {{ $errors->get('email') ? 'border-red-400/60 bg-red-500/10 focus:ring-red-400/40 focus:border-red-400' : 'border-stadium-gray focus:border-electric-blue focus:ring-0' }}">
            @error('email')
                <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full py-3 bg-electric-blue hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-blue-500/20">
            Kirim Tautan Reset
        </button>
    </form>

</x-guest-layout>