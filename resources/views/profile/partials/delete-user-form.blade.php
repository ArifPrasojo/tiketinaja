<div x-data="{ showModal: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }">

    <p class="text-sm text-red-600 mb-4 leading-relaxed">
        Setelah akun dihapus, semua data profil dan riwayat booking Anda akan dihapus secara permanen.
        Pastikan Anda sudah menyelesaikan semua sesi aktif sebelum melanjutkan.
    </p>

    <button type="button" @click="showModal = true"
            class="px-5 py-2.5 text-xs font-bold text-red-600 bg-white border border-red-200 hover:bg-red-50 rounded-xl transition shadow-sm">
        Hapus Akun Saya
    </button>

    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">

        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showModal = false"></div>

        <div class="relative w-full max-w-md bg-white border border-stadium-gray rounded-3xl shadow-2xl p-6 z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center gap-3.5 mb-4">
                <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center shrink-0 border border-red-200">
                    <span class="material-symbols-outlined text-[24px] text-red-600">warning</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-pitch-black">Konfirmasi Hapus Akun</h3>
                    <p class="text-xs text-on-surface-variant">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>

            <p class="text-xs text-on-surface-variant mb-5 leading-relaxed">
                Silakan masukkan password akun Anda untuk mengonfirmasi penghapusan secara permanen.
            </p>

            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('delete')

                <div>
                    <label for="del_password" class="block text-xs font-bold text-pitch-black mb-1.5">
                        Password Anda
                    </label>
                    <input id="del_password" name="password" type="password"
                           placeholder="Masukkan password Anda"
                           class="w-full px-3.5 py-2.5 text-sm bg-surface-light border border-stadium-gray text-pitch-black rounded-xl outline-none transition
                                  focus:border-red-500 focus:bg-white placeholder:text-on-surface-variant
                                  @error('password', 'userDeletion') border-red-500 @enderror">
                    @error('password', 'userDeletion')
                        <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-2.5 pt-2">
                    <button type="button" @click="showModal = false"
                            class="flex-1 py-2.5 text-xs font-bold bg-slate-100 text-on-surface-variant hover:bg-slate-200 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition shadow-sm">
                        Ya, Hapus Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
