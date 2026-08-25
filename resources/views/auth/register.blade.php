<x-guest-layout>

    <div class="mb-7">
        <h1 class="text-2xl font-bold text-gray-900">Buat akun baru</h1>
        <p class="text-gray-500 text-sm mt-1.5">Gratis. Booking lapangan dalam 2 menit.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Name --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                Nama Lengkap
            </label>
            <input id="name" type="text" name="name"
                   value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Nama lengkap Anda"
                   class="w-full px-4 py-3 text-sm border rounded-xl outline-none transition
                          {{ $errors->get('name') ? 'border-red-400 bg-red-50 focus:ring-2 focus:ring-red-300' : 'border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100' }}">
            @error('name')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                Alamat Email
            </label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}" required autocomplete="username"
                   placeholder="email@contoh.com"
                   class="w-full px-4 py-3 text-sm border rounded-xl outline-none transition
                          {{ $errors->get('email') ? 'border-red-400 bg-red-50 focus:ring-2 focus:ring-red-300' : 'border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100' }}">
            @error('email')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Phone --}}
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">
                No. Telepon
                <span class="text-gray-400 font-normal">(opsional)</span>
            </label>
            <input id="phone" type="tel" name="phone"
                   value="{{ old('phone') }}" autocomplete="tel"
                   placeholder="08xx-xxxx-xxxx"
                   class="w-full px-4 py-3 text-sm border border-gray-300 rounded-xl outline-none transition
                          focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
            @error('phone')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                Password
            </label>
            <input id="password" type="password" name="password"
                   required autocomplete="new-password"
                   placeholder="Minimal 8 karakter"
                   class="w-full px-4 py-3 text-sm border rounded-xl outline-none transition
                          {{ $errors->get('password') ? 'border-red-400 bg-red-50 focus:ring-2 focus:ring-red-300' : 'border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100' }}">
            @error('password')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                Konfirmasi Password
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   required autocomplete="new-password"
                   placeholder="Ulangi password"
                   class="w-full px-4 py-3 text-sm border rounded-xl outline-none transition
                          {{ $errors->get('password_confirmation') ? 'border-red-400 bg-red-50 focus:ring-2 focus:ring-red-300' : 'border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100' }}">
            @error('password_confirmation')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <p class="text-xs text-gray-400 leading-relaxed">
            Dengan mendaftar, Anda menyetujui syarat & ketentuan penggunaan platform.
        </p>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
            Buat Akun
        </button>
    </form>

    {{-- Login link --}}
    <div class="flex items-center gap-3 my-5">
        <div class="flex-1 h-px bg-gray-200"></div>
        <span class="text-xs text-gray-400">sudah punya akun?</span>
        <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    <a href="{{ route('login') }}"
       class="block w-full py-3 text-center text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
        Masuk sekarang
    </a>

</x-guest-layout>
