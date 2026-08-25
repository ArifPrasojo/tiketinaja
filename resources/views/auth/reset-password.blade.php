<x-guest-layout>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Buat password baru</h1>
        <p class="text-gray-500 text-sm mt-1.5">Pastikan password baru Anda cukup kuat.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                Alamat Email
            </label>
            <input id="email" type="email" name="email"
                   value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                   class="w-full px-4 py-3 text-sm border rounded-xl outline-none transition
                          {{ $errors->get('email') ? 'border-red-400 bg-red-50 focus:ring-2 focus:ring-red-300' : 'border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100' }}">
            @error('email')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                Password Baru
            </label>
            <input id="password" type="password" name="password"
                   required autocomplete="new-password" placeholder="Minimal 8 karakter"
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
                   required autocomplete="new-password" placeholder="Ulangi password baru"
                   class="w-full px-4 py-3 text-sm border rounded-xl outline-none transition
                          {{ $errors->get('password_confirmation') ? 'border-red-400 bg-red-50 focus:ring-2 focus:ring-red-300' : 'border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100' }}">
            @error('password_confirmation')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
            Simpan Password Baru
        </button>
    </form>

</x-guest-layout>
