<x-layout>
    <x-slot:title>Data Jenis Perijinan</x-slot:title>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Data Jenis Perijinan</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola data jenis perijinan</p>
        </div>
        <a href="{{ route('perijinan.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <i class="mdi mdi-plus text-lg"></i>
            <span>Tambah Perijinan</span>
        </a>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    @if (session('error'))
        <meta name="error-message" content="{{ session('error') }}">
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <!-- Toolbar: Show entries and Search -->
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
            <div class="w-full sm:w-auto sm:flex-1 sm:max-w-md">
                <form id="search_form" method="GET" action="{{ route('perijinan.index') }}" class="relative">
                    <input type="text" name="search" id="search" value="{{ $search }}"
                        placeholder="Search Perijinan..."
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
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            No
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                            onclick="updateSort('nama_perijinan')">
                            <div class="flex items-center gap-2">
                                <span>Nama Perijinan</span>
                                @if ($sortBy === 'nama_perijinan')
                                    <i class="mdi mdi-sort-{{ $sortOrder === 'asc' ? 'ascending' : 'descending' }}"></i>
                                @else
                                    <i class="mdi mdi-sort text-gray-300 dark:text-gray-500"></i>
                                @endif
                            </div>
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($perijinans as $index => $perijinan)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ $perijinans->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                                {{ $perijinan->nama_perijinan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2 flex-wrap">
                                    <a href="{{ route('perijinan.show', $perijinan->id) }}"
                                        class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
                                        <i class="mdi mdi-eye"></i>
                                        <span>Detail</span>
                                    </a>
                                    <a href="{{ route('perijinan.form-builder', $perijinan->id) }}"
                                        class="inline-flex items-center gap-1 bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
                                        <i class="mdi mdi-form-select"></i>
                                        <span>Kelola Formulir</span>
                                    </a>
                                    <a href="{{ route('perijinan.alur-validasi', $perijinan->id) }}"
                                        class="inline-flex items-center gap-1 bg-orange-600 hover:bg-orange-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
                                        <i class="mdi mdi-sitemap"></i>
                                        <span>Alur Validasi</span>
                                    </a>
                                    <a href="{{ route('perijinan.edit', $perijinan->id) }}"
                                        class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
                                        <i class="mdi mdi-pencil"></i>
                                        <span>Edit</span>
                                    </a>
                                    <form action="{{ route('perijinan.destroy', $perijinan->id) }}" method="POST"
                                        class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors btn-delete"
                                            data-action="{{ route('perijinan.destroy', $perijinan->id) }}">
                                            <i class="mdi mdi-delete"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="mdi mdi-folder-open-outline text-4xl mb-2"></i>
                                <p>Belum ada data Jenis Perijinan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($perijinans->hasPages())
            <div
                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ $perijinans->firstItem() }} to {{ $perijinans->lastItem() }} of
                    {{ $perijinans->total() }} entries
                </div>
                <div>
                    {{ $perijinans->links() }}
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

    function updateSort(sortBy) {
        const url = new URL(window.location.href);
        const currentSortBy = url.searchParams.get('sort_by') || 'id';
        const currentSortOrder = url.searchParams.get('sort_order') || 'desc';

        if (sortBy === currentSortBy) {
            url.searchParams.set('sort_order', currentSortOrder === 'asc' ? 'desc' : 'asc');
        } else {
            url.searchParams.set('sort_by', sortBy);
            url.searchParams.set('sort_order', 'asc');
        }
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }

    // Auto-submit search on input change with debounce
    let searchTimeout;
    document.getElementById('search')?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('search_form').submit();
        }, 500);
    });
</script>
