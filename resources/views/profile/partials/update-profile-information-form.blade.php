<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="space-y-4">
    @csrf
    @method('patch')

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="name" class="block text-sm font-medium text-on-surface-variant mb-1.5">Nama Lengkap</label>
            <input id="name" name="name" type="text"
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                   class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant @error('name') border-red-400 @enderror">
            @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-on-surface-variant mb-1.5">No. Telepon</label>
            <input id="phone" name="phone" type="tel"
                   value="{{ old('phone', $user->phone) }}" autocomplete="tel"
                   placeholder="08xx-xxxx-xxxx"
                   class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant @error('phone') border-red-400 @enderror">
            @error('phone')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-on-surface-variant mb-1.5">Alamat Email</label>
        <input id="email" name="email" type="email"
               value="{{ old('email', $user->email) }}" required autocomplete="username"
               class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant @error('email') border-red-400 @enderror">
        @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2 p-3 bg-amber-500/15 border border-amber-500/20 rounded-lg">
                <p class="text-sm text-amber-400">
                    Email Anda belum diverifikasi.
                    <button form="send-verification"
                            class="underline font-medium hover:text-amber-300">
                        Kirim ulang email verifikasi.
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 text-sm text-electric-blue font-medium">Link verifikasi sudah dikirim!</p>
                @endif
            </div>
        @endif
    </div>

    <div class="flex items-center gap-4 pt-2">
        <button type="submit"
                class="px-5 py-2.5 text-sm font-medium text-white bg-electric-blue rounded-xl hover:bg-blue-700 transition">
            Simpan Perubahan
        </button>
        @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition
               x-init="setTimeout(() => show = false, 2500)"
               class="text-sm text-electric-blue font-medium">
                <span class="material-symbols-outlined text-[16px] inline-block align-text-bottom">check_circle</span> Tersimpan!
            </p>
        @endif
    </div>
</form>
