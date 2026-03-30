<x-layout>
    <x-slot:title>Data SKM - Pertanyaan</x-slot:title>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Data Pertanyaan SKM</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola pertanyaan Survey Kepuasan Masyarakat</p>
        </div>
        <a href="{{ route('skm.data.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <i class="mdi mdi-plus text-lg"></i>
            <span>Tambah Pertanyaan</span>
        </a>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <!-- Toolbar -->
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

            <div class="w-full sm:w-auto sm:flex-1 sm:max-w-md">
                <form id="search_form" method="GET" action="{{ route('skm.data.index') }}" class="relative">
                    <input type="text" name="search" id="search" value="{{ $search }}"
                        placeholder="Search Pertanyaan..."
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 pl-10 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="mdi mdi-magnify absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <button type="submit" class="hidden">Search</button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-16">
                            Urutan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider max-w-[400px]">
                            Pertanyaan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-28">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-64">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($dataSkm as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200 w-16">
                                {{ $item->urutan }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 max-w-[400px]">
                                <p class="truncate" title="{{ $item->pertanyaan }}">{{ $item->pertanyaan }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm w-28">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->status === 'aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $item->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium w-64">
                                <div class="flex justify-end gap-1 flex-nowrap">
                                    <a href="{{ route('skm.data.show', $item->id) }}"
                                        class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors"
                                        title="Statistik">
                                        <i class="mdi mdi-chart-bar"></i>
                                    </a>
                                    <a href="{{ route('skm.data.edit', $item->id) }}"
                                        class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors"
                                        title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('skm.data.destroy', $item->id) }}" method="POST"
                                        class="delete-form inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors btn-delete"
                                            data-action="{{ route('skm.data.destroy', $item->id) }}"
                                            title="Hapus">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="mdi mdi-folder-open-outline text-4xl mb-2"></i>
                                <p>Belum ada data Pertanyaan SKM</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($dataSkm->hasPages())
            <div
                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ $dataSkm->firstItem() }} to {{ $dataSkm->lastItem() }} of
                    {{ $dataSkm->total() }} entries
                </div>
                <div>
                    {{ $dataSkm->links() }}
                </div>
            </div>
        @endif
    </div>
</x-layout>

<script>
    function updatePerPage(perPage) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }

    function updateStatusFilter(status) {
        const url = new URL(window.location.href);
        url.searchParams.set('status_filter', status);
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }

    let searchTimeout;
    document.getElementById('search')?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('search_form').submit();
        }, 500);
    });
</script>
