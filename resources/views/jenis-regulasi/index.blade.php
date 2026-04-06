<x-layout>
    <x-slot:title>Jenis Regulasi</x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Jenis Regulasi</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola jenis-jenis regulasi</p>
        </div>
        <a href="{{ route('jenis-regulasi.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <i class="mdi mdi-plus text-lg"></i>
            <span>Tambah Jenis</span>
        </a>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    @if (session('error'))
        <meta name="error-message" content="{{ session('error') }}">
    @endif

    <style>
        /* No sortable styles needed */
    </style>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <!-- Toolbar -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <label for="status_filter" class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Status:</label>
                <select id="status_filter" name="status_filter"
                    class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onchange="updateStatusFilter(this.value)">
                    <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="aktif" {{ $statusFilter == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ $statusFilter == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>

            <form action="{{ route('jenis-regulasi.index') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari jenis regulasi..."
                    class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-md text-sm transition-colors">
                    <i class="mdi mdi-magnify"></i>
                </button>
                @if($search)
                    <a href="{{ route('jenis-regulasi.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-1.5 rounded-md text-sm transition-colors">
                        <i class="mdi mdi-close"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Nama Jenis
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Deskripsi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Jumlah Regulasi
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($jenisRegulasi as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->nama_jenis }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->slug }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate" title="{{ $item->deskripsi }}">
                                    {{ $item->deskripsi ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->status === 'aktif')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                                {{ $item->regulasi->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2 flex-wrap">
                                    <a href="{{ route('jenis-regulasi.edit', $item->id) }}"
                                        class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
                                        <i class="mdi mdi-pencil"></i>
                                        <span>Edit</span>
                                    </a>
                                    <form action="{{ route('jenis-regulasi.destroy', $item->id) }}" method="POST" class="delete-form inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors btn-delete"
                                            data-action="{{ route('jenis-regulasi.destroy', $item->id) }}">
                                            <i class="mdi mdi-delete"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="mdi mdi-folder-open text-4xl mb-2"></i>
                                <p>Belum ada data jenis regulasi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($jenisRegulasi->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $jenisRegulasi->links() }}
            </div>
        @endif
    </div>

    <script>
        function updateStatusFilter(status) {
            const url = new URL(window.location.href);
            url.searchParams.set('status_filter', status);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }
    </script>
</x-layout>
