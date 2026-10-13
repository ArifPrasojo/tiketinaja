<form method="post" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    @method('put')

    <div>
        <label for="update_password_current_password"
               class="block text-sm font-medium text-on-surface-variant mb-1.5">
            Password Saat Ini
        </label>
        <input id="update_password_current_password" name="current_password" type="password"
               autocomplete="current-password" placeholder="••••••••"
               class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg outline-none transition
                      focus:ring-0 focus:ring-0 focus:border-electric-blue placeholder:text-on-surface-variant
                      @error('current_password', 'updatePassword') border-red-400 @enderror">
        @error('current_password', 'updatePassword')
            <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="update_password_password"
               class="block text-xs font-bold text-pitch-black mb-1.5">
            Password Baru
        </label>
        <input id="update_password_password" name="password" type="password"
               autocomplete="new-password" placeholder="Minimal 8 karakter"
               class="w-full px-3.5 py-2.5 text-sm bg-surface-light border border-stadium-gray text-pitch-black rounded-xl outline-none transition
                      focus:border-electric-blue focus:bg-white placeholder:text-on-surface-variant
                      @error('password', 'updatePassword') border-red-500 @enderror">
        @error('password', 'updatePassword')
            <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="update_password_password_confirmation"
               class="block text-xs font-bold text-pitch-black mb-1.5">
            Konfirmasi Password Baru
        </label>
        <input id="update_password_password_confirmation" name="password_confirmation" type="password"
               autocomplete="new-password" placeholder="Ulangi password baru"
               class="w-full px-3.5 py-2.5 text-sm bg-surface-light border border-stadium-gray text-pitch-black rounded-xl outline-none transition
                      focus:border-electric-blue focus:bg-white placeholder:text-on-surface-variant
                      @error('password_confirmation', 'updatePassword') border-red-500 @enderror">
        @error('password_confirmation', 'updatePassword')
            <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-4 pt-2">
        <button type="submit"
                class="px-6 py-2.5 text-xs font-bold text-white bg-electric-blue rounded-xl hover:bg-blue-700 active:scale-95 transition shadow-sm">
            Perbarui Password
        </button>
        @if(session('status') === 'password-updated')
            <p x-data="{ show: true }" x-show="show" x-transition
               x-init="setTimeout(() => show = false, 2500)"
               class="text-xs text-field-green font-bold flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">check_circle</span> Password berhasil diubah!
            </p>
        @endif
    </div>
</form>
