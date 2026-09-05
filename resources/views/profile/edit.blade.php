<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-pitch-black">Profil Saya</h1>
        <p class="text-sm text-on-surface-variant mt-0.5">Kelola informasi akun dan keamanan Anda</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
            <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-6">
                <div class="flex items-center gap-4 mb-6 pb-5 border-b border-stadium-gray">
                    <div class="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                        <span class="text-xl font-bold text-electric-blue">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-bold text-pitch-black">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-on-surface-variant">{{ auth()->user()->email }}</p>
                        <span class="inline-block mt-1 px-2.5 py-0.5 text-xs font-semibold rounded-full
                            {{ auth()->user()->isAdmin() ? 'bg-purple-500/20 text-purple-400' : (auth()->user()->isManager() ? 'bg-sky-500/20 text-sky-400' : 'bg-blue-50 text-electric-blue') }}">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </div>
                </div>

                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-6">
                <h2 class="text-base font-semibold text-pitch-black mb-5">Ubah Password</h2>
                @include('profile.partials.update-password-form')
            </div>

            <div class="bg-red-500/10 border border-red-500/20 rounded-2xl p-6">
                <h2 class="text-base font-semibold text-red-400 mb-1">Zona Berbahaya</h2>
                <p class="text-sm text-red-400/80 mb-4">Aksi berikut bersifat permanen dan tidak dapat dibatalkan.</p>
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
