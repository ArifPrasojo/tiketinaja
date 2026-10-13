<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-pitch-black">Notifikasi</h1>
                <p class="text-sm text-on-surface-variant mt-0.5">Informasi status reservasi, konfirmasi pembayaran, dan jadwal bermain</p>
            </div>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-electric-blue bg-blue-50 border border-blue-200/60 rounded-xl hover:bg-blue-100 transition shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">done_all</span>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-2xl text-sm shadow-sm">
                    <span class="material-symbols-outlined text-[20px] text-field-green shrink-0">check_circle</span>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white border border-stadium-gray rounded-2xl sporty-shadow divide-y divide-stadium-gray/60 overflow-hidden">
                @forelse($notifications as $notification)
                    @php $data = $notification->data; @endphp
                    <div class="flex items-start gap-4 p-5 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50/50' }} hover:bg-slate-50 transition-colors">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5 shadow-sm
                            {{ match($data['type'] ?? '') {
                                'booking_confirmed' => 'bg-green-50 text-field-green border border-green-200',
                                'booking_cancelled' => 'bg-red-50 text-red-500 border border-red-200',
                                'booking_expired'   => 'bg-amber-50 text-amber-600 border border-amber-200',
                                'checkin_success', 'checkin_playtime' => 'bg-blue-50 text-electric-blue border border-blue-200',
                                'session_completed' => 'bg-orange-50 text-athletic-orange border border-orange-200',
                                default             => 'bg-slate-100 text-on-surface-variant border border-stadium-gray',
                            } }}">
                            @php
                                $icon = match($data['type'] ?? '') {
                                    'booking_confirmed'  => 'check_circle',
                                    'booking_cancelled'  => 'cancel',
                                    'booking_expired'    => 'alarm',
                                    'checkin_success'    => 'verified',
                                    'checkin_playtime'   => 'sprint',
                                    'session_completed'  => 'emoji_events',
                                    default              => 'notifications',
                                };
                            @endphp
                            <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-pitch-black leading-snug {{ $notification->read_at ? 'font-medium' : 'font-bold' }}">
                                {{ $data['message'] ?? '-' }}
                            </p>
                            <p class="text-[11px] text-on-surface-variant mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">schedule</span>
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if(isset($data['url']))
                                <a href="{{ $data['url'] }}"
                                   class="inline-flex items-center gap-0.5 px-3 py-1.5 text-xs font-bold text-electric-blue bg-blue-50 rounded-xl hover:bg-blue-100 transition">
                                    Buka
                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </a>
                            @endif
                            @if(!$notification->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit"
                                            title="Tandai dibaca"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-on-surface-variant hover:text-pitch-black hover:bg-slate-100 transition">
                                        <span class="material-symbols-outlined text-[16px]">check</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center text-on-surface-variant">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <span class="material-symbols-outlined text-[32px] text-slate-400">notifications_off</span>
                        </div>
                        <p class="text-base font-bold text-pitch-black">Tidak ada notifikasi</p>
                        <p class="text-xs text-on-surface-variant mt-1">Semua pemberitahuan dan status booking terbaru akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="mt-4">{{ $notifications->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
