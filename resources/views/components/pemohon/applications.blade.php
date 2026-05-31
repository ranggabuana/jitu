<section class="bg-white rounded-2xl border border-amber-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-amber-100 flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Daftar Pengajuan</h2>
            <p class="text-sm text-gray-500">Riwayat pengajuan perizinan Anda</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pemohon.perijinan') }}" 
               class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2 px-4 rounded-xl text-sm transition-all shadow-sm">
                <i class="fas fa-plus"></i> Ajukan Baru
            </a>
        </div>
    </div>

    <!-- Table Filters -->
    <div class="p-6 bg-gray-50/50 border-b border-amber-100 flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
        <form action="{{ route('pemohon.dashboard') }}" method="GET" class="flex flex-col md:flex-row gap-4 w-full" id="filterForm">
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            @if(request('direction'))
                <input type="hidden" name="direction" value="{{ request('direction') }}">
            @endif
            
            <div class="flex-1 relative">
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                       placeholder="Cari No. Registrasi atau Layanan..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <select name="per_page" id="perPageSelect"
                        class="border border-gray-300 rounded-xl px-3 py-2 text-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5 per hal</option>
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 per hal</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per hal</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per hal</option>
                </select>

                @if(request()->anyFilled(['search', 'per_page']))
                    <a href="{{ route('pemohon.dashboard') }}" class="bg-gray-100 text-gray-600 hover:bg-gray-200 px-4 py-2 rounded-xl text-sm font-semibold transition-colors flex items-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-amber-50 text-gray-600 text-xs uppercase">
                <tr>
                    <th class="p-4 border-b">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'no_registrasi', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-amber-700">
                            No. Registrasi
                            <i class="fas {{ request('sort') === 'no_registrasi' ? (request('direction') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} opacity-50"></i>
                        </a>
                    </th>
                    <th class="p-4 border-b">Layanan</th>
                    <th class="p-4 border-b">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-amber-700">
                            Tgl Pengajuan
                            <i class="fas {{ request('sort') === 'created_at' || !request('sort') ? (request('direction') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} opacity-50"></i>
                        </a>
                    </th>
                    <th class="p-4 border-b">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-amber-700">
                            Status
                            <i class="fas {{ request('sort') === 'status' ? (request('direction') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} opacity-50"></i>
                        </a>
                    </th>
                    <th class="p-4 border-b text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-amber-100">
                @forelse($applications as $app)
                    <tr class="hover:bg-amber-50 transition-colors">
                        <td class="p-4 border-b">
                            <span class="font-mono font-semibold text-amber-700">{{ $app->no_registrasi }}</span>
                        </td>
                        <td class="p-4 border-b">
                            <span class="font-medium text-gray-800">{{ $app->perijinan->nama_perijinan }}</span>
                        </td>
                        <td class="p-4 border-b">
                            <span class="text-sm text-gray-500">{{ $app->created_at->format('d M Y') }}</span>
                        </td>
                        <td class="p-4 border-b">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $app->status_color }}">
                                {{ $app->status_label }}
                            </span>
                        </td>
                        <td class="p-4 border-b text-right">
                            <a href="{{ route('pemohon.tracking.detail', $app->id) }}"
                                class="text-amber-700 hover:text-amber-900 font-medium text-sm">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">
                            <i class="fas fa-folder-open text-4xl mb-3 opacity-20"></i>
                            <p>Tidak ada data pengajuan yang ditemukan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 bg-amber-50/30 border-t border-amber-100">
        {{ $applications->links() }}
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const perPageSelect = document.getElementById('perPageSelect');
        const filterForm = document.getElementById('filterForm');
        let timeout = null;

        // Real-time search with debounce
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    filterForm.submit();
                }, 500); // Wait 500ms after user stops typing
            });

            // Set focus to end of input text
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
            if (val) searchInput.focus();
        }

        // Auto-submit on per_page change
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                filterForm.submit();
            });
        }
    });
</script>