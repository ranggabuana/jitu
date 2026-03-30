<x-layout>
    <x-slot:title>Data Regulasi</x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Data Regulasi</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola regulasi dan peraturan</p>
        </div>
        <a href="{{ route('regulasi.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <i class="mdi mdi-plus text-lg"></i>
            <span>Tambah Regulasi</span>
        </a>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    @if (session('error'))
        <meta name="error-message" content="{{ session('error') }}">
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <!-- Toolbar: Show entries, Status Filter, and Search -->
        <div
            class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <label for="per_page" class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Show</label>
                <select id="per_page" name="per_page"
                    class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onchange="updatePerPage(this.value)">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
            </div>

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

            <form action="{{ route('regulasi.index') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari regulasi..."
                    class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-md text-sm transition-colors">
                    <i class="mdi mdi-magnify"></i>
                </button>
                @if($search)
                    <a href="{{ route('regulasi.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-1.5 rounded-md text-sm transition-colors">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:hover:text-gray-200"
                            onclick="sortBy('id')">
                            ID
                            @if($sortBy === 'id')
                                @if($sortOrder === 'asc') <i class="mdi mdi-arrow-up"></i>
                                @else <i class="mdi mdi-arrow-down"></i>
                                @endif
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:hover:text-gray-200"
                            onclick="sortBy('nama_regulasi')">
                            Nama Regulasi
                            @if($sortBy === 'nama_regulasi')
                                @if($sortOrder === 'asc') <i class="mdi mdi-arrow-up"></i>
                                @else <i class="mdi mdi-arrow-down"></i>
                                @endif
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            File
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Deskripsi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:hover:text-gray-200"
                            onclick="sortBy('status')">
                            Status
                            @if($sortBy === 'status')
                                @if($sortOrder === 'asc') <i class="mdi mdi-arrow-up"></i>
                                @else <i class="mdi mdi-arrow-down"></i>
                                @endif
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:hover:text-gray-200"
                            onclick="sortBy('created_at')">
                            Dibuat
                            @if($sortBy === 'created_at')
                                @if($sortOrder === 'asc') <i class="mdi mdi-arrow-up"></i>
                                @else <i class="mdi mdi-arrow-down"></i>
                                @endif
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($regulasi as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->nama_regulasi }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('regulasi.download', $item->id) }}"
                                    class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    <i class="mdi mdi-file-download mr-1"></i>
                                    <span class="text-sm">{{ pathinfo($item->file_regulasi, PATHINFO_EXTENSION) }}</span>
                                </a>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->formatted_file_size }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate" title="{{ $item->deskripsi }}">
                                    {{ $item->deskripsi ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->status === 'aktif')
                                    <span
                                        class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('regulasi.show', $item->id) }}"
                                        class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300"
                                        title="Detail">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('regulasi.edit', $item->id) }}"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                        title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('regulasi.destroy', $item->id) }}" method="POST"
                                        class="delete-form inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                            title="Hapus">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="mdi mdi-folder-open text-4xl mb-2"></i>
                                <p>Belum ada data regulasi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($regulasi->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing {{ $regulasi->firstItem() ?? 0 }} to {{ $regulasi->lastItem() ?? 0 }} of {{ $regulasi->total() }} entries
                    </div>
                    <div class="flex gap-2">
                        {{-- Previous Button --}}
                        @if($regulasi->onFirstPage())
                            <button disabled
                                class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500 text-sm cursor-not-allowed">
                                Previous
                            </button>
                        @else
                            <a href="{{ $regulasi->previousPageUrl() }}"
                                class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">
                                Previous
                            </a>
                        @endif

                        {{-- Pagination Numbers --}}
                        @foreach($regulasi->getUrlRange(1, $regulasi->lastPage()) as $page => $url)
                            @if($page == $regulasi->currentPage())
                                <span class="px-3 py-1 rounded bg-blue-500 text-white text-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Next Button --}}
                        @if($regulasi->hasMorePages())
                            <a href="{{ $regulasi->nextPageUrl() }}"
                                class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">
                                Next
                            </a>
                        @else
                            <button disabled
                                class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500 text-sm cursor-not-allowed">
                                Next
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function updatePerPage(perPage) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', perPage);
            window.location.href = url.toString();
        }

        function updateStatusFilter(status) {
            const url = new URL(window.location.href);
            url.searchParams.set('status_filter', status);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }

        function sortBy(field) {
            const url = new URL(window.location.href);
            const currentSort = url.searchParams.get('sort_by') || 'id';
            const currentOrder = url.searchParams.get('sort_order') || 'desc';
            
            if (currentSort === field) {
                url.searchParams.set('sort_order', currentOrder === 'asc' ? 'desc' : 'asc');
            } else {
                url.searchParams.set('sort_by', field);
                url.searchParams.set('sort_order', 'desc');
            }
            window.location.href = url.toString();
        }
    </script>
</x-layout>
