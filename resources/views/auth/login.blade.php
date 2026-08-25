<x-guest-layout>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Masuk ke akun</h1>
        <p class="text-gray-500 text-sm mt-1.5">Selamat datang kembali. Masukkan kredensial Anda.</p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                Alamat Email
            </label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="email@contoh.com"
                   class="w-full px-4 py-3 text-sm border rounded-xl outline-none transition
                          {{ $errors->get('email') ? 'border-red-400 bg-red-50 focus:ring-2 focus:ring-red-300' : 'border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100' }}">
            @error('email')
                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">
                        Lupa password?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password"
                   placeholder="••••••••"
                   class="w-full px-4 py-3 text-sm border rounded-xl outline-none transition
                          {{ $errors->get('password') ? 'border-red-400 bg-red-50 focus:ring-2 focus:ring-red-300' : 'border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100' }}">
            @error('password')
                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Remember me --}}
        <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" name="remember"
                   class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-600">Ingat saya di perangkat ini</span>
        </label>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-sm mt-1">
            Masuk
        </button>
    </form>

    {{-- Divider --}}
    <div class="flex items-center gap-3 my-6">
        <div class="flex-1 h-px bg-gray-200"></div>
        <span class="text-xs text-gray-400">atau</span>
        <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    {{-- Register link --}}
    <p class="text-center text-sm text-gray-600">
        Belum punya akun?
        <a href="{{ route('register') }}"
           class="font-semibold text-indigo-600 hover:text-indigo-800 transition">
            Daftar sekarang
        </a>
    </p>

</x-guest-layout>
