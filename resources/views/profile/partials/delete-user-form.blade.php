<div x-data="{ showModal: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }">

    <p class="text-sm text-red-400 mb-4">
        Setelah akun dihapus, semua data Anda akan dihapus secara permanen.
        Pastikan Anda sudah menyimpan semua data penting sebelum melanjutkan.
    </p>

    <button type="button" @click="showModal = true"
            class="px-5 py-2.5 text-sm font-medium text-red-400 bg-red-500/10 border border-red-500/20 hover:bg-red-500/10 rounded-xl transition">
        Hapus Akun Saya
    </button>

    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">

        <div class="absolute inset-0 bg-gray-950/60 backdrop-blur-sm" @click="showModal = false"></div>

        <div class="relative w-full max-w-md bg-white border border-stadium-gray rounded-xl shadow-2xl p-6 z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-red-500/15 rounded-xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px] text-red-400">warning</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-pitch-black">Hapus akun?</h3>
                    <p class="text-xs text-on-surface-variant">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>

            <p class="text-sm text-on-surface-variant mb-5">
                Masukkan password Anda untuk mengkonfirmasi penghapusan akun secara permanen.
            </p>

            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('delete')

                <div>
                    <label for="del_password" class="block text-sm font-medium text-on-surface-variant mb-1.5">
                        Password
                    </label>
                    <input id="del_password" name="password" type="password"
                           placeholder="Masukkan password Anda"
                           class="w-full px-3 py-2.5 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg outline-none transition
                                  focus:border-red-400 focus:ring-0 focus:ring-red-500/30 placeholder:text-on-surface-variant
                                  @error('password', 'userDeletion') border-red-400 @enderror">
                    @error('password', 'userDeletion')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="showModal = false"
                            class="flex-1 py-2.5 text-sm font-medium bg-slate-100 text-on-surface-variant hover:bg-slate-200 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 text-sm font-medium text-pitch-black bg-red-600 hover:bg-red-500 rounded-xl transition">
                        Ya, Hapus Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
