<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-pitch-black">Profil Saya</h1>
            <p class="text-sm text-on-surface-variant mt-0.5">Kelola informasi data akun, kontak, dan keamanan kredensial Anda</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- User Bio Card --}}
            <div class="bg-white border border-stadium-gray rounded-2xl sporty-shadow p-6 sm:p-7">
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-stadium-gray/80">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 text-electric-blue flex items-center justify-center font-bold text-2xl shrink-0 shadow-sm border border-blue-200/50">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-pitch-black truncate">{{ auth()->user()->name }}</h2>
                        <p class="text-xs text-on-surface-variant truncate mt-0.5">{{ auth()->user()->email }}</p>
                        <div class="mt-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[11px] font-bold rounded-lg
                                {{ auth()->user()->isAdmin() ? 'bg-purple-50 text-purple-700 border border-purple-200' : (auth()->user()->isManager() ? 'bg-blue-50 text-electric-blue border border-blue-200' : 'bg-green-50 text-field-green border border-green-200') }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ auth()->user()->isAdmin() ? 'bg-purple-600' : (auth()->user()->isManager() ? 'bg-electric-blue' : 'bg-field-green') }}"></span>
                                {{ auth()->user()->isAdmin() ? 'Super Admin' : (auth()->user()->isManager() ? 'Venue Manager' : 'Customer') }}
                            </span>
                        </div>
                    </div>
                </div>

                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- Password Change Card --}}
            <div class="bg-white border border-stadium-gray rounded-2xl sporty-shadow p-6 sm:p-7">
                <div class="mb-5 pb-4 border-b border-stadium-gray/80">
                    <h2 class="text-base font-bold text-pitch-black flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-electric-blue">lock</span>
                        Keamanan & Password
                    </h2>
                    <p class="text-xs text-on-surface-variant mt-0.5">Pastikan password baru Anda menggunakan kombinasi karakter yang kuat</p>
                </div>
                @include('profile.partials.update-password-form')
            </div>

            {{-- Danger Zone Card --}}
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 sm:p-7">
                <div class="mb-4">
                    <h2 class="text-base font-bold text-red-700 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-red-600">warning</span>
                        Zona Berbahaya
                    </h2>
                    <p class="text-xs text-red-600/80 mt-0.5">Penghapusan akun bersifat permanen dan seluruh data riwayat akan dihapus.</p>
                </div>
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
