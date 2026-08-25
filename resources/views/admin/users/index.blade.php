<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Pengguna</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manajemen akun dan role pengguna platform</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Filter bar --}}
            <form method="GET" class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4 flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cari nama / email</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari..."
                               class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                    </div>
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
                    <select name="role" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                        <option value="">Semua Role</option>
                        <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="manager"  {{ request('role') === 'manager'  ? 'selected' : '' }}>Manager</option>
                        <option value="admin"    {{ request('role') === 'admin'    ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">Filter</button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Reset</a>
                    @endif
                </div>
                <p class="ml-auto text-sm text-gray-500">{{ $users->total() }} pengguna</p>
            </form>

            {{-- Table --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Pengguna</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Telepon</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Booking</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Bergabung</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($users as $u)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                                {{ $u->isAdmin() ? 'bg-purple-100' : ($u->isManager() ? 'bg-blue-100' : 'bg-green-100') }}">
                                                <span class="text-sm font-bold {{ $u->isAdmin() ? 'text-purple-700' : ($u->isManager() ? 'text-blue-700' : 'text-green-700') }}">
                                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $u->name }}</p>
                                                @if($u->id === auth()->id())
                                                    <p class="text-xs text-indigo-500 font-medium">Akun Anda</p>
                                                @endif
                                                @if(!$u->hasVerifiedEmail())
                                                    <p class="text-xs text-amber-500">Email belum diverifikasi</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600">{{ $u->email }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-600">{{ $u->phone ?? '-' }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-700 font-medium">{{ $u->bookings_count }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-400">{{ $u->created_at->format('d M Y') }}</td>
                                    <td class="px-5 py-4">
                                        @if($u->id === auth()->id())
                                            {{-- Guard: tidak bisa ubah role diri sendiri --}}
                                            <span class="inline-flex px-2.5 py-1.5 text-xs font-semibold rounded-lg
                                                {{ $u->isAdmin() ? 'bg-purple-100 text-purple-700' : ($u->isManager() ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                                {{ ucfirst($u->role) }}
                                            </span>
                                        @else
                                            <form action="{{ route('admin.users.update', $u) }}" method="POST"
                                                  class="flex items-center gap-2">
                                                @csrf @method('PUT')
                                                <select name="role"
                                                        class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none
                                                               {{ $u->isAdmin() ? 'bg-purple-50 text-purple-700' : ($u->isManager() ? 'bg-blue-50 text-blue-700' : 'bg-green-50 text-green-700') }}">
                                                    <option value="customer" {{ $u->role === 'customer' ? 'selected' : '' }}>Customer</option>
                                                    <option value="manager"  {{ $u->role === 'manager'  ? 'selected' : '' }}>Manager</option>
                                                    <option value="admin"    {{ $u->role === 'admin'    ? 'selected' : '' }}>Admin</option>
                                                </select>
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 text-xs font-medium bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                                                    Ubah
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($u->id !== auth()->id() && !$u->isAdmin())
                                            <form action="{{ route('admin.users.destroy', $u) }}" method="POST"
                                                  onsubmit="return confirm('Hapus pengguna {{ addslashes($u->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">
                                        Tidak ada pengguna ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
