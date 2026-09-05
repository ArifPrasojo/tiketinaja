<x-guest-layout>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-pitch-black">Buat password baru</h1>
        <p class="text-on-surface-variant text-sm mt-1.5">Pastikan password baru Anda cukup kuat.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-on-surface-variant mb-1.5">
                Alamat Email
            </label>
            <input id="email" type="email" name="email"
                   value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                   class="w-full px-4 py-3 text-sm bg-white border-2 border-stadium-gray rounded-lg outline-none transition focus:ring-0 placeholder:text-on-surface-variant text-pitch-black
                          {{ $errors->get('email') ? 'border-red-400/60 bg-red-500/10 focus:ring-red-400/40 focus:border-red-400' : 'border-stadium-gray focus:border-electric-blue focus:ring-0' }}">
            @error('email')
                <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-on-surface-variant mb-1.5">
                Password Baru
            </label>
            <input id="password" type="password" name="password"
                   required autocomplete="new-password" placeholder="Minimal 8 karakter"
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
                   required autocomplete="new-password" placeholder="Ulangi password baru"
                   class="w-full px-4 py-3 text-sm bg-white border-2 border-stadium-gray rounded-lg outline-none transition focus:ring-0 placeholder:text-on-surface-variant text-pitch-black
                          {{ $errors->get('password_confirmation') ? 'border-red-400/60 bg-red-500/10 focus:ring-red-400/40 focus:border-red-400' : 'border-stadium-gray focus:border-electric-blue focus:ring-0' }}">
            @error('password_confirmation')
                <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full py-3 bg-electric-blue hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-blue-500/20">
            Simpan Password Baru
        </button>
    </form>

</x-guest-layout>