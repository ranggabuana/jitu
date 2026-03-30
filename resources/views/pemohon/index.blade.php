<x-layout>
    <x-slot:title>Data Pemohon</x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Data Pemohon</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola pemohon perizinan</p>
        </div>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    @if (session('error'))
        <meta name="error-message" content="{{ session('error') }}">
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Total Pemohon -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transform transition-all hover:scale-105 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Pemohon</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($totalPemohon) }}</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Semua pemohon terdaftar</p>
                </div>
                <div class="p-4 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <i class="mdi mdi-account-group text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Perorangan -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transform transition-all hover:scale-105 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Perorangan</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($peroranganCount) }}</h3>
                    <p class="text-xs text-green-500 mt-2">
                        <i class="mdi mdi-account"></i> Pemohon pribadi
                    </p>
                </div>
                <div class="p-4 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                    <i class="mdi mdi-account-outline text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Badan Usaha -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transform transition-all hover:scale-105 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Badan Usaha</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($badanUsahaCount) }}</h3>
                    <p class="text-xs text-purple-500 mt-2">
                        <i class="mdi mdi-office-building"></i> Perusahaan/organisasi
                    </p>
                </div>
                <div class="p-4 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                    <i class="mdi mdi-office-building text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <!-- Toolbar -->
        <div
            class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Total: <strong class="text-gray-800 dark:text-white">{{ $pemohon->total() }} pemohon</strong>
                </span>
            </div>

            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('pemohon.index') }}" class="flex items-center gap-2">
                    <select name="status" onchange="this.form.submit()"
                        class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak
                            Aktif</option>
                    </select>
                </form>
            </div>

            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('pemohon.index') }}" class="flex items-center gap-2">
                    <select name="status_pemohon" onchange="this.form.submit()"
                        class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Jenis</option>
                        <option value="perorangan" {{ request('status_pemohon') == 'perorangan' ? 'selected' : '' }}>Perorangan</option>
                        <option value="badan_usaha" {{ request('status_pemohon') == 'badan_usaha' ? 'selected' : '' }}>Badan Usaha</option>
                    </select>
                </form>
            </div>

            <div class="w-full sm:w-auto sm:flex-1 sm:max-w-md">
                <form method="GET" action="{{ route('pemohon.index') }}" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pemohon..."
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 pl-10 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="mdi mdi-magnify absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Pemohon
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status Pemohon
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Kontak
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Terdaftar
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pemohon as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="mdi mdi-account text-blue-600 dark:text-blue-300"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $item->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ '@' }}{{ $item->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->status_pemohon === 'badan_usaha')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 border border-purple-200 dark:border-purple-700">
                                        <i class="fas fa-building"></i>
                                        Badan Usaha
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 border border-blue-200 dark:border-blue-700">
                                        <i class="fas fa-user"></i>
                                        Perorangan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $item->email }}</div>
                                @if ($item->no_hp)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->no_hp }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->status === 'aktif')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <i class="mdi mdi-check-circle"></i> Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        <i class="mdi mdi-clock-outline"></i> Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2 flex-wrap">
                                    <a href="{{ route('pemohon.show', $item->id) }}"
                                        class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
                                        <i class="mdi mdi-eye"></i>
                                        <span>Detail</span>
                                    </a>
                                    <a href="{{ route('pemohon.edit', $item->id) }}"
                                        class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
                                        <i class="mdi mdi-pencil"></i>
                                        <span>Edit</span>
                                    </a>

                                    <!-- Toggle Status Form -->
                                    <form method="POST" action="{{ route('pemohon.update-status', $item->id) }}"
                                        class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" name="status"
                                            value="{{ $item->status === 'aktif' ? 'tidak_aktif' : 'aktif' }}"
                                            class="inline-flex items-center gap-1 {{ $item->status === 'aktif' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
                                            <i
                                                class="mdi mdi-{{ $item->status === 'aktif' ? 'clock-outline' : 'check-circle' }}"></i>
                                            <span>{{ $item->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                                        </button>
                                    </form>

                                    <!-- Delete Form -->
                                    <form action="{{ route('pemohon.destroy', $item->id) }}" method="POST"
                                        class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors btn-delete"
                                            data-action="{{ route('pemohon.destroy', $item->id) }}">
                                            <i class="mdi mdi-delete"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div
                                    class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="mdi mdi-account-off text-gray-400 dark:text-gray-500 text-3xl"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">Belum ada data pemohon</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $pemohon->links() }}
        </div>
    </div>
</x-layout>
