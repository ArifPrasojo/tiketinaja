<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-pitch-black">Kelola Pengguna</h1>
            <p class="text-sm text-on-surface-variant mt-0.5">Manajemen akun dan role pengguna platform</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <form method="GET" class="bg-white border border-stadium-gray rounded-xl sporty-shadow px-5 py-4 flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-on-surface-variant mb-1">Cari nama / email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] text-on-surface-variant">search</span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari..."
                               class="w-full pl-9 pr-3 py-2 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none placeholder:text-on-surface-variant">
                    </div>
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-medium text-on-surface-variant mb-1">Role</label>
                    <select name="role" class="w-full px-3 py-2 text-sm bg-white border-2 border-stadium-gray text-pitch-black rounded-lg focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none">
                        <option value="">Semua Role</option>
                        <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="manager"  {{ request('role') === 'manager'  ? 'selected' : '' }}>Manager</option>
                        <option value="admin"    {{ request('role') === 'admin'    ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-electric-blue text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">Filter</button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-slate-100 text-on-surface-variant text-sm font-medium rounded-xl hover:bg-slate-200 transition">Reset</a>
                    @endif
                </div>
                <p class="ml-auto text-sm text-on-surface-variant">{{ $users->total() }} pengguna</p>
            </form>

            <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-white border-b border-stadium-gray">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Pengguna</th>
                                <th class="hidden sm:table-cell px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Email</th>
                                <th class="hidden md:table-cell px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Telepon</th>
                                <th class="hidden lg:table-cell px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Booking</th>
                                <th class="hidden lg:table-cell px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Bergabung</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Role</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#1e2638]">
                            @forelse($users as $u)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                                {{ $u->isAdmin() ? 'bg-purple-500/20' : ($u->isManager() ? 'bg-sky-500/20' : 'bg-blue-50') }}">
                                                <span class="text-sm font-bold {{ $u->isAdmin() ? 'text-purple-400' : ($u->isManager() ? 'text-sky-400' : 'text-electric-blue') }}">
                                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-pitch-black truncate">{{ $u->name }}</p>
                                                <p class="text-xs text-on-surface-variant sm:hidden truncate">{{ $u->email }}</p>
                                                @if($u->id === auth()->id())
                                                    <p class="text-xs text-electric-blue font-medium">Akun Anda</p>
                                                @endif
                                                @if(!$u->hasVerifiedEmail())
                                                    <p class="text-xs text-amber-400">Belum verifikasi</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell px-5 py-4 text-sm text-on-surface-variant max-w-[180px] truncate">{{ $u->email }}</td>
                                    <td class="hidden md:table-cell px-5 py-4 text-sm text-on-surface-variant">{{ $u->phone ?? '-' }}</td>
                                    <td class="hidden lg:table-cell px-5 py-4 text-sm text-on-surface-variant font-medium">{{ $u->bookings_count }}</td>
                                    <td class="hidden lg:table-cell px-5 py-4 text-sm text-on-surface-variant">{{ $u->created_at->format('d M Y') }}</td>
                                    <td class="px-5 py-4">
                                        @if($u->id === auth()->id())
                                            <span class="inline-flex px-2.5 py-1.5 text-xs font-semibold rounded-lg
                                                {{ $u->isAdmin() ? 'bg-purple-500/20 text-purple-400' : ($u->isManager() ? 'bg-sky-500/20 text-sky-400' : 'bg-blue-50 text-electric-blue') }}">
                                                {{ ucfirst($u->role) }}
                                            </span>
                                        @else
                                            <form action="{{ route('admin.users.update', $u) }}" method="POST"
                                                  class="flex items-center gap-2">
                                                @csrf @method('PUT')
                                                <select name="role"
                                                        class="text-xs bg-white border border-stadium-gray rounded-lg px-2.5 py-1.5 focus:ring-0 focus:ring-0 focus:border-electric-blue outline-none
                                                               {{ $u->isAdmin() ? 'text-purple-400' : ($u->isManager() ? 'text-sky-400' : 'text-electric-blue') }}">
                                                    <option value="customer" {{ $u->role === 'customer' ? 'selected' : '' }}>Customer</option>
                                                    <option value="manager"  {{ $u->role === 'manager'  ? 'selected' : '' }}>Manager</option>
                                                    <option value="admin"    {{ $u->role === 'admin'    ? 'selected' : '' }}>Admin</option>
                                                </select>
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 text-xs font-medium bg-electric-blue text-white rounded-lg hover:bg-blue-700 transition">
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
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg hover:bg-red-500/10 transition">
                                                    <span class="material-symbols-outlined text-[14px]">delete</span>
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-on-surface-variant">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center text-on-surface-variant text-sm">
                                        Tidak ada pengguna ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-5 py-4 border-t border-stadium-gray">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
