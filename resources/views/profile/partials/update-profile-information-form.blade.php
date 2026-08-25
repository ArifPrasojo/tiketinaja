<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="space-y-4">
    @csrf
    @method('patch')

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
            <input id="name" name="name" type="text"
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none @error('name') border-red-400 @enderror">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon</label>
            <input id="phone" name="phone" type="tel"
                   value="{{ old('phone', $user->phone) }}" autocomplete="tel"
                   placeholder="08xx-xxxx-xxxx"
                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none @error('phone') border-red-400 @enderror">
            @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email</label>
        <input id="email" name="email" type="email"
               value="{{ old('email', $user->email) }}" required autocomplete="username"
               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none @error('email') border-red-400 @enderror">
        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-700">
                    Email Anda belum diverifikasi.
                    <button form="send-verification"
                            class="underline font-medium hover:text-yellow-900">
                        Kirim ulang email verifikasi.
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 text-sm text-green-600 font-medium">Link verifikasi sudah dikirim!</p>
                @endif
            </div>
        @endif
    </div>

    <div class="flex items-center gap-4 pt-2">
        <button type="submit"
                class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">
            Simpan Perubahan
        </button>
        @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition
               x-init="setTimeout(() => show = false, 2500)"
               class="text-sm text-green-600 font-medium">
                ✓ Tersimpan!
            </p>
        @endif
    </div>
</form>
