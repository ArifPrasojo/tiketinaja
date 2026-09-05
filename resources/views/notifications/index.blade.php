<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-pitch-black">Notifikasi</h2>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf
                    <button type="submit" class="text-sm text-electric-blue hover:text-electric-blue">Tandai semua sudah dibaca</button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 flex items-center gap-3 bg-blue-50 border border-electric-blue/20 text-electric-blue px-4 py-3 rounded-2xl text-sm">
                    <span class="material-symbols-outlined text-[20px] text-electric-blue shrink-0">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow divide-y divide-[#1e2638]">
                @forelse($notifications as $notification)
                    @php $data = $notification->data; @endphp
                    <div class="flex items-start gap-4 p-4 {{ $notification->read_at ? '' : 'bg-blue-50' }} hover:bg-slate-50/50 transition">
                        <div class="mt-0.5">
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
                                $iconColor = match($data['type'] ?? '') {
                                    'booking_confirmed'  => 'text-field-green',
                                    'booking_cancelled'  => 'text-red-500',
                                    'booking_expired'    => 'text-amber-500',
                                    'checkin_success'    => 'text-electric-blue',
                                    'checkin_playtime'   => 'text-field-green',
                                    'session_completed'  => 'text-athletic-orange',
                                    default              => 'text-on-surface-variant',
                                };
                            @endphp
                            <span class="material-symbols-outlined text-[24px] {{ $iconColor }}">{{ $icon }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-pitch-black {{ $notification->read_at ? '' : 'font-semibold' }}">
                                {{ $data['message'] ?? '-' }}
                            </p>
                            <p class="text-xs text-on-surface-variant mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            @if(isset($data['url']))
                                <a href="{{ $data['url'] }}" class="text-xs text-electric-blue hover:text-electric-blue">Lihat</a>
                            @endif
                            @if(!$notification->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-on-surface-variant hover:text-on-surface-variant">Tandai dibaca</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-on-surface-variant">
                        <p class="mb-2"><span class="material-symbols-outlined text-[48px] text-slate-300">notifications</span></p>
                        <p>Tidak ada notifikasi.</p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="mt-4">{{ $notifications->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
