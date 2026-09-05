<x-guest-layout>

    <div class="mb-8">
        <div class="w-12 h-12 bg-blue-50 border border-electric-blue/25 rounded-2xl flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[24px] text-electric-blue">lock</span>
        </div>
        <h1 class="text-2xl font-bold text-pitch-black">Konfirmasi password</h1>
        <p class="text-on-surface-variant text-sm mt-1.5 leading-relaxed">
            Area ini dilindungi. Masukkan password Anda untuk melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <label for="password" class="block text-sm font-medium text-on-surface-variant mb-1.5">
                Password
            </label>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password" placeholder="••••••••"
                   class="w-full px-4 py-3 text-sm bg-white border-2 border-stadium-gray rounded-lg outline-none transition focus:ring-0 placeholder:text-on-surface-variant text-pitch-black
                          {{ $errors->get('password') ? 'border-red-400/60 bg-red-500/10 focus:ring-red-400/40 focus:border-red-400' : 'border-stadium-gray focus:border-electric-blue focus:ring-0' }}">
            @error('password')
                <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full py-3 bg-electric-blue hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-blue-500/20">
            Konfirmasi & Lanjutkan
        </button>
    </form>

</x-guest-layout>