<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Notifikasi</h2>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf
                    <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800">Tandai semua sudah dibaca</button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg divide-y divide-gray-100">
                @forelse($notifications as $notification)
                    @php $data = $notification->data; @endphp
                    <div class="flex items-start gap-4 p-4 {{ $notification->read_at ? '' : 'bg-indigo-50' }} hover:bg-gray-50 transition">
                        <div class="mt-0.5">
                            @php
                                $icon = match($data['type'] ?? '') {
                                    'booking_confirmed'  => '✅',
                                    'booking_cancelled'  => '❌',
                                    'booking_expired'    => '⏰',
                                    'checkin_success'    => '✓',
                                    'checkin_playtime'   => '🏃',
                                    'session_completed'  => '🏆',
                                    default              => '🔔',
                                };
                            @endphp
                            <span class="text-xl">{{ $icon }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 {{ $notification->read_at ? '' : 'font-semibold' }}">
                                {{ $data['message'] ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            @if(isset($data['url']))
                                <a href="{{ $data['url'] }}" class="text-xs text-indigo-600 hover:text-indigo-800">Lihat</a>
                            @endif
                            @if(!$notification->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-gray-400 hover:text-gray-600">Tandai dibaca</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <p class="text-2xl mb-2">🔔</p>
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
