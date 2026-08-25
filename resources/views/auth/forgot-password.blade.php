<x-guest-layout>

    <div class="mb-8">
        <a href="{{ route('login') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 mb-6 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke halaman masuk
        </a>

        <h1 class="text-2xl font-bold text-gray-900">Lupa password?</h1>
        <p class="text-gray-500 text-sm mt-1.5 leading-relaxed">
            Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang password.
        </p>
    </div>

    <x-auth-session-status :status="session('status')" />

    @if(session('status'))
        <div class="mb-5 flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3.5 rounded-xl text-sm">
            <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                Alamat Email
            </label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}" required autofocus
                   placeholder="email@contoh.com"
                   class="w-full px-4 py-3 text-sm border rounded-xl outline-none transition
                          {{ $errors->get('email') ? 'border-red-400 bg-red-50 focus:ring-2 focus:ring-red-300' : 'border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100' }}">
            @error('email')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
            Kirim Tautan Reset
        </button>
    </form>

</x-guest-layout>
