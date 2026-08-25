<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
        <p class="text-sm text-gray-500 mt-0.5">Kelola informasi akun dan keamanan Anda</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Profile card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-4 mb-6 pb-5 border-b border-gray-100">
                    <div class="w-14 h-14 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                        <span class="text-xl font-bold text-indigo-700">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                        <span class="inline-block mt-1 px-2.5 py-0.5 text-xs font-semibold rounded-full
                            {{ auth()->user()->isAdmin() ? 'bg-purple-100 text-purple-700' : (auth()->user()->isManager() ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </div>
                </div>

                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- Change password --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-5">Ubah Password</h2>
                @include('profile.partials.update-password-form')
            </div>

            {{-- Danger zone --}}
            <div class="bg-red-50 border border-red-100 rounded-2xl p-6">
                <h2 class="text-base font-semibold text-red-800 mb-1">Zona Berbahaya</h2>
                <p class="text-sm text-red-600 mb-4">Aksi berikut bersifat permanen dan tidak dapat dibatalkan.</p>
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
