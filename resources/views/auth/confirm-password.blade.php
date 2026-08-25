<x-guest-layout>

    <div class="mb-8">
        <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Konfirmasi password</h1>
        <p class="text-gray-500 text-sm mt-1.5 leading-relaxed">
            Area ini dilindungi. Masukkan password Anda untuk melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                Password
            </label>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password" placeholder="••••••••"
                   class="w-full px-4 py-3 text-sm border rounded-xl outline-none transition
                          {{ $errors->get('password') ? 'border-red-400 bg-red-50 focus:ring-2 focus:ring-red-300' : 'border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100' }}">
            @error('password')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
            Konfirmasi & Lanjutkan
        </button>
    </form>

</x-guest-layout>
