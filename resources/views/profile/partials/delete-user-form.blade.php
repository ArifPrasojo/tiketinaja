<div x-data="{ showModal: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }">

    <p class="text-sm text-red-700 mb-4">
        Setelah akun dihapus, semua data Anda akan dihapus secara permanen.
        Pastikan Anda sudah menyimpan semua data penting sebelum melanjutkan.
    </p>

    <button type="button" @click="showModal = true"
            class="px-5 py-2.5 text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-xl transition">
        Hapus Akun Saya
    </button>

    {{-- Modal overlay --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-gray-950/60 backdrop-blur-sm" @click="showModal = false"></div>

        {{-- Dialog --}}
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Hapus akun?</h3>
                    <p class="text-xs text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>

            <p class="text-sm text-gray-600 mb-5">
                Masukkan password Anda untuk mengkonfirmasi penghapusan akun secara permanen.
            </p>

            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('delete')

                <div>
                    <label for="del_password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Password
                    </label>
                    <input id="del_password" name="password" type="password"
                           placeholder="Masukkan password Anda"
                           class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl outline-none transition
                                  focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @error('password', 'userDeletion') border-red-400 bg-red-50 @enderror">
                    @error('password', 'userDeletion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="showModal = false"
                            class="flex-1 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-xl transition">
                        Ya, Hapus Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
