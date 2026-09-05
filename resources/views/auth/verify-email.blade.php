<x-guest-layout>

    <div class="mb-8 text-center">
        <div class="w-14 h-14 bg-blue-50 border border-electric-blue/25 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-[28px] text-electric-blue">mail</span>
        </div>
        <h1 class="text-2xl font-bold text-pitch-black">Verifikasi email Anda</h1>
        <p class="text-on-surface-variant text-sm mt-2 leading-relaxed max-w-xs mx-auto">
            Kami sudah mengirim link verifikasi ke email Anda. Periksa kotak masuk atau folder spam.
        </p>
    </div>

    @if(session('status') == 'verification-link-sent')
        <div class="mb-5 flex items-start gap-3 bg-blue-50 border border-electric-blue/30 text-electric-blue px-4 py-3.5 rounded-xl text-sm">
            <span class="material-symbols-outlined text-[20px] text-electric-blue shrink-0 mt-0.5">check_circle</span>
            Link verifikasi baru sudah dikirim ke email Anda.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
        @csrf
        <button type="submit"
                class="w-full py-3 bg-electric-blue hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-blue-500/20">
            Kirim Ulang Email Verifikasi
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full py-3 text-sm font-medium text-pitch-black bg-slate-100 hover:bg-slate-200 rounded-xl transition">
            Keluar dari akun
        </button>
    </form>

</x-guest-layout>