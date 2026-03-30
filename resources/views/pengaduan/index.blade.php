<x-layout>
    <x-slot:title>Data Pengaduan</x-slot:title>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Data Pengaduan</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola dan tindak lanjuti pengaduan masyarakat</p>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    @if (session('error'))
        <meta name="error-message" content="{{ session('error') }}">
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Pengaduan</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalPengaduan }}</p>
                </div>
                <div class="p-3 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                    <i class="mdi mdi-clipboard-text text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</p>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                    <i class="mdi mdi-clock-outline text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Dalam Proses</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $prosesCount }}</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <i class="mdi mdi-progress-clock text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Selesai</p>
                    <p class="text-2xl font-bold text-green-600">{{ $selesaiCount }}</p>
                </div>
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                    <i class="mdi mdi-check-circle text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Ditolak</p>
                    <p class="text-2xl font-bold text-red-600">{{ $ditolakCount }}</p>
                </div>
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                    <i class="mdi mdi-close-circle text-xl"></i>
                </div>
            </div>
        </div>
    </div>

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
                    <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="proses" {{ $statusFilter == 'proses' ? 'selected' : '' }}>Dalam Proses</option>
                    <option value="selesai" {{ $statusFilter == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ $statusFilter == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <form action="{{ route('admin.pengaduan.index') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari pengaduan..."
                    class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-md text-sm transition-colors">
                    <i class="mdi mdi-magnify"></i>
                </button>
                @if($search)
                    <a href="{{ route('admin.pengaduan.index') }}"
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            No. Pengaduan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:hover:text-gray-200"
                            onclick="sortBy('nama')">
                            Nama
                            @if($sortBy === 'nama')
                                @if($sortOrder === 'asc') <i class="mdi mdi-arrow-up"></i>
                                @else <i class="mdi mdi-arrow-down"></i>
                                @endif
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Kontak
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:hover:text-gray-200"
                            onclick="sortBy('kategori')">
                            Kategori
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
                            onclick="sortBy('tanggal_pengaduan')">
                            Tanggal
                            @if($sortBy === 'tanggal_pengaduan')
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
                    @forelse($pengaduan as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->no_pengaduan }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->nama }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($item->email, 20) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $item->no_hp }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                                    {{ ucfirst($item->kategori) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                        'proses' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
                                        'selesai' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                        'ditolak' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$item->status] }}">
                                    {{ $item->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->tanggal_pengaduan->format('d M Y') }}
                                <div class="text-xs">{{ $item->tanggal_pengaduan->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.pengaduan.show', $item->id) }}"
                                        class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300"
                                        title="Detail">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    @if($item->lampiran)
                                        <a href="{{ route('admin.pengaduan.download', $item->id) }}"
                                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                            title="Download Lampiran">
                                            <i class="mdi mdi-download"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.pengaduan.destroy', $item->id) }}" method="POST"
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
                                <p>Belum ada data pengaduan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pengaduan->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing {{ $pengaduan->firstItem() ?? 0 }} to {{ $pengaduan->lastItem() ?? 0 }} of {{ $pengaduan->total() }} entries
                    </div>
                    <div class="flex gap-2">
                        @if($pengaduan->onFirstPage())
                            <button disabled
                                class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500 text-sm cursor-not-allowed">
                                Previous
                            </button>
                        @else
                            <a href="{{ $pengaduan->previousPageUrl() }}"
                                class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">
                                Previous
                            </a>
                        @endif

                        @foreach($pengaduan->getUrlRange(1, $pengaduan->lastPage()) as $page => $url)
                            @if($page == $pengaduan->currentPage())
                                <span class="px-3 py-1 rounded bg-blue-500 text-white text-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if($pengaduan->hasMorePages())
                            <a href="{{ $pengaduan->nextPageUrl() }}"
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
