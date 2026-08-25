<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Manajemen Promo</h2>
            <a href="{{ route('manage.promos.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                + Tambah Promo
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe / Nilai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Venue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penggunaan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Berakhir</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($promos as $promo)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-sm font-bold text-indigo-700">{{ $promo->code }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $promo->description }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        @if($promo->type === 'percentage')
                                            <span class="text-green-700 font-semibold">{{ $promo->value }}%</span>
                                            @if($promo->max_discount)
                                                <span class="text-xs text-gray-500">(maks Rp {{ number_format($promo->max_discount, 0, ',', '.') }})</span>
                                            @endif
                                        @else
                                            <span class="text-green-700 font-semibold">Rp {{ number_format($promo->value, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $promo->venue?->name ?? 'Platform-wide' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $promo->used_count }} / {{ $promo->max_uses ?? '∞' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $promo->expires_at ? $promo->expires_at->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($promo->isValid())
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                        <a href="{{ route('manage.promos.edit', $promo) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form method="POST" action="{{ route('manage.promos.destroy', $promo) }}" class="inline" onsubmit="return confirm('Hapus promo ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">Belum ada promo.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($promos->hasPages())
                    <div class="px-6 py-4 border-t">{{ $promos->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
