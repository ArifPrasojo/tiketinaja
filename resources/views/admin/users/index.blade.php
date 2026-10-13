<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-pitch-black">Kelola Pengguna</h1>
                <p class="text-sm text-on-surface-variant mt-0.5">Manajemen akun, hak akses, dan verifikasi pengguna platform</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-electric-blue border border-blue-200/60 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-electric-blue animate-pulse"></span>
                    {{ number_format($stats['total']) }} Total Terdaftar
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Metric Summary Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Total User</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($stats['total']) }}</p>
                        <p class="text-xs text-on-surface-variant mt-1">Semua tingkatan akun</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-electric-blue flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">groups</span>
                    </div>
                </div>

                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Customer</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($stats['customer']) }}</p>
                        <span class="inline-block mt-1 text-[11px] font-bold text-field-green">Pemain aktif</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-field-green flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">sports_soccer</span>
                    </div>
                </div>

                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Venue Manager</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($stats['manager']) }}</p>
                        <span class="inline-block mt-1 text-[11px] font-bold text-electric-blue">Mitra pengelola</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-electric-blue flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">storefront</span>
                    </div>
                </div>

                <div class="bg-white border border-stadium-gray rounded-2xl p-5 flex items-center justify-between sporty-shadow hover:border-electric-blue transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Administrator</p>
                        <p class="text-2xl font-bold text-pitch-black mt-1">{{ number_format($stats['admin']) }}</p>
                        <span class="inline-block mt-1 text-[11px] font-bold text-purple-600">Akses penuh</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">shield_person</span>
                    </div>
                </div>
            </div>

            {{-- Role Filter Tabs --}}
            <div class="flex flex-wrap gap-2">
                @php
                    $roleTabs = [
                        ''          => ['label' => 'Semua Pengguna', 'count' => $stats['total'],    'cls' => 'bg-slate-100 text-slate-700',       'activeBg' => 'bg-electric-blue text-white'],
                        'customer'  => ['label' => 'Customer',       'count' => $stats['customer'], 'cls' => 'bg-green-50 text-field-green',      'activeBg' => 'bg-field-green text-white'],
                        'manager'   => ['label' => 'Venue Manager',  'count' => $stats['manager'],  'cls' => 'bg-blue-50 text-electric-blue',     'activeBg' => 'bg-electric-blue text-white'],
                        'admin'     => ['label' => 'Administrator',  'count' => $stats['admin'],    'cls' => 'bg-purple-50 text-purple-700',      'activeBg' => 'bg-purple-600 text-white'],
                    ];
                @endphp
                @foreach($roleTabs as $rKey => $tab)
                    @php $isActive = (string) request('role') === $rKey; @endphp
                    <a href="{{ $rKey === '' ? route('admin.users.index', request()->except(['role', 'page'])) : route('admin.users.index', array_merge(request()->except(['role', 'page']), ['role' => $rKey])) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold border transition-all duration-200
                              {{ $isActive
                                  ? $tab['activeBg'] . ' border-transparent shadow-md'
                                  : 'bg-white text-on-surface-variant border-stadium-gray hover:border-electric-blue hover:text-electric-blue' }}">
                        {{ $tab['label'] }}
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-xs font-bold
                                     {{ $isActive ? 'bg-white/25 text-white' : $tab['cls'] }}">
                            {{ number_format($tab['count']) }}
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- Search & Action Bar --}}
            <form method="GET" class="bg-white border border-stadium-gray rounded-2xl sporty-shadow p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex-1 min-w-0 relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari berdasarkan nama, email, atau nomor telepon..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-surface-light border border-stadium-gray text-pitch-black rounded-xl focus:border-electric-blue focus:bg-white outline-none transition placeholder:text-on-surface-variant">
                </div>

                @if(request('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif

                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-electric-blue text-white text-sm font-bold rounded-xl hover:bg-blue-700 active:scale-95 transition shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">filter_list</span>
                        Cari
                    </button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 px-4 py-2.5 bg-slate-100 text-on-surface-variant text-sm font-medium rounded-xl hover:bg-slate-200 transition">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Users Table Card --}}
            <div class="bg-white border border-stadium-gray rounded-2xl sporty-shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left">
                        <thead>
                            <tr class="bg-surface-light border-b border-stadium-gray">
                                <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Profil Pengguna</th>
                                <th class="hidden md:table-cell px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Kontak</th>
                                <th class="hidden lg:table-cell px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-center">Aktivitas Booking</th>
                                <th class="hidden xl:table-cell px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Terdaftar</th>
                                <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Peran (Role)</th>
                                <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stadium-gray/60">
                            @forelse($users as $u)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    {{-- User Info --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center font-bold text-base shrink-0 shadow-sm
                                                {{ $u->isAdmin() ? 'bg-purple-100 text-purple-700' : ($u->isManager() ? 'bg-blue-100 text-electric-blue' : 'bg-green-100 text-field-green') }}">
                                                {{ strtoupper(substr($u->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="text-sm font-bold text-pitch-black truncate max-w-[180px] sm:max-w-xs">{{ $u->name }}</p>
                                                    @if($u->id === auth()->id())
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-electric-blue border border-blue-200">
                                                            Anda
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-on-surface-variant mt-0.5 truncate max-w-[200px]">{{ $u->email }}</p>
                                                
                                                {{-- Mobile badges --}}
                                                <div class="flex items-center gap-2 mt-1 sm:hidden">
                                                    @if($u->hasVerifiedEmail())
                                                        <span class="inline-flex items-center gap-0.5 text-[11px] text-field-green font-medium">
                                                            <span class="material-symbols-outlined text-[13px]">verified</span> Terverifikasi
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-0.5 text-[11px] text-amber-600 font-medium">
                                                            <span class="material-symbols-outlined text-[13px]">warning</span> Belum Verif
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Contact Info --}}
                                    <td class="hidden md:table-cell px-6 py-4 text-sm">
                                        <div class="space-y-0.5">
                                            <p class="text-pitch-black text-xs font-semibold">{{ $u->phone ?: '—' }}</p>
                                            @if($u->hasVerifiedEmail())
                                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-field-green">
                                                    <span class="material-symbols-outlined text-[13px]">verified</span> Email Terverifikasi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-600">
                                                    <span class="material-symbols-outlined text-[13px]">pending</span> Belum Terverifikasi
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Booking Count --}}
                                    <td class="hidden lg:table-cell px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold
                                            {{ $u->bookings_count > 0 ? 'bg-blue-50 text-electric-blue' : 'bg-slate-100 text-on-surface-variant' }}">
                                            <span class="material-symbols-outlined text-[14px]">receipt_long</span>
                                            {{ $u->bookings_count }} Transaksi
                                        </span>
                                    </td>

                                    {{-- Registration Date --}}
                                    <td class="hidden xl:table-cell px-6 py-4 text-sm text-on-surface-variant">
                                        <p class="text-xs text-pitch-black font-medium">{{ $u->created_at->format('d M Y') }}</p>
                                        <p class="text-[11px] text-on-surface-variant">{{ $u->created_at->diffForHumans() }}</p>
                                    </td>

                                    {{-- Role Management --}}
                                    <td class="px-6 py-4">
                                        @if($u->id === auth()->id())
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                <span class="material-symbols-outlined text-[14px]">shield_person</span>
                                                Administrator (Anda)
                                            </span>
                                        @else
                                            <form action="{{ route('admin.users.update', $u) }}" method="POST" class="inline-flex items-center gap-2">
                                                @csrf
                                                @method('PUT')
                                                <select name="role" onchange="this.form.submit()"
                                                        class="text-xs font-bold rounded-xl px-3 py-1.5 border-2 border-stadium-gray focus:border-electric-blue focus:ring-0 outline-none transition cursor-pointer
                                                        {{ $u->isAdmin() ? 'bg-purple-50 text-purple-700 border-purple-200' : ($u->isManager() ? 'bg-blue-50 text-electric-blue border-blue-200' : 'bg-green-50 text-field-green border-green-200') }}">
                                                    <option value="customer" {{ $u->role === 'customer' ? 'selected' : '' }}>Customer</option>
                                                    <option value="manager"  {{ $u->role === 'manager'  ? 'selected' : '' }}>Manager</option>
                                                    <option value="admin"    {{ $u->role === 'admin'    ? 'selected' : '' }}>Admin</option>
                                                </select>
                                            </form>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 text-right">
                                        @if($u->id !== auth()->id() && !$u->isAdmin())
                                            <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline-block"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ addslashes($u->name) }}? Semua relasi booking akan terpengaruh.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        title="Hapus Pengguna"
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-red-500 hover:text-white hover:bg-red-500 transition-colors">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-on-surface-variant/40">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center text-on-surface-variant">
                                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                            <span class="material-symbols-outlined text-[32px] text-slate-400">person_off</span>
                                        </div>
                                        <p class="text-base font-bold text-pitch-black">Pengguna tidak ditemukan</p>
                                        <p class="text-xs text-on-surface-variant mt-1">Coba sesuaikan kata kunci pencarian atau ubah filter role.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-stadium-gray bg-surface-light">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
