<x-pemohon.layout>
    <x-slot:title>Tracking Pengajuan - JITU Banjarnegara</x-slot:title>

    <!-- Navbar -->
    <x-pemohon.navbar></x-pemohon.navbar>

    @php
        if (!function_exists('getSortUrl')) {
            function getSortUrl($column, $currentSort, $currentOrder) {
                $order = ($currentSort === $column && $currentOrder === 'asc') ? 'desc' : 'asc';
                return request()->fullUrlWithQuery(['sort' => $column, 'order' => $order]);
            }
        }
        
        if (!function_exists('getSortIcon')) {
            function getSortIcon($column, $currentSort, $currentOrder) {
                if ($currentSort !== $column) {
                    return 'fa-sort text-gray-300';
                }
                return $currentOrder === 'asc' ? 'fa-sort-up text-amber-600' : 'fa-sort-down text-amber-600';
            }
        }
    @endphp

    <!-- Main Content -->
    <main class="flex-1 max-w-[95%] mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-br from-amber-600 via-amber-700 to-amber-800 rounded-3xl shadow-xl p-6 text-white">
            <div class="flex items-center gap-4">
                <a href="{{ route('pemohon.dashboard') }}" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold mb-1">Tracking Pengajuan</h1>
                    <p class="text-amber-100 text-sm">Pantau status perizinan Anda secara real-time</p>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white rounded-2xl shadow-sm border border-amber-100 p-5">
            <form method="GET" action="{{ route('pemohon.tracking') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="order" value="{{ $order }}">

                <!-- Search Input -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search text-sm"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari Nomor Registrasi atau Jenis Perizinan..." 
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none text-sm transition-all bg-gray-50/50">
                </div>

                <!-- Show per page & Buttons -->
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="flex items-center gap-2 text-xs font-semibold text-gray-600">
                        <label for="per_page" class="whitespace-nowrap">Tampilkan:</label>
                        <select name="per_page" id="per_page" onchange="this.form.submit()" 
                                class="px-3 py-2 rounded-xl border border-gray-200 focus:border-amber-500 outline-none bg-white transition-all text-xs font-bold cursor-pointer">
                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5 entri</option>
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 entri</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 entri</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 entri</option>
                        </select>
                    </div>

                    <button type="submit" class="flex-1 md:flex-initial px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl transition-all shadow-sm shadow-amber-200 text-xs flex items-center justify-center gap-2">
                        Filter
                    </button>
                    
                    @if($search)
                        <a href="{{ route('pemohon.tracking') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all text-xs flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table List -->
        @if ($data->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-amber-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-amber-50/50 border-b border-amber-100">
                                <th class="px-6 py-4 text-xs font-black text-amber-900 uppercase tracking-wider">
                                    <a href="{{ getSortUrl('no_registrasi', $sort, $order) }}" class="flex items-center gap-1.5 hover:text-amber-700 transition-colors">
                                        No. Registrasi <i class="fas {{ getSortIcon('no_registrasi', $sort, $order) }} text-[10px]"></i>
                                    </a>
                                </th>
                                <th class="px-6 py-4 text-xs font-black text-amber-900 uppercase tracking-wider">
                                    Jenis Perizinan
                                </th>
                                <th class="px-6 py-4 text-xs font-black text-amber-900 uppercase tracking-wider">
                                    <a href="{{ getSortUrl('created_at', $sort, $order) }}" class="flex items-center gap-1.5 hover:text-amber-700 transition-colors">
                                        Tgl Pengajuan <i class="fas {{ getSortIcon('created_at', $sort, $order) }} text-[10px]"></i>
                                    </a>
                                </th>
                                <th class="px-6 py-4 text-xs font-black text-amber-900 uppercase tracking-wider">
                                    <a href="{{ getSortUrl('status', $sort, $order) }}" class="flex items-center gap-1.5 hover:text-amber-700 transition-colors">
                                        Status <i class="fas {{ getSortIcon('status', $sort, $order) }} text-[10px]"></i>
                                    </a>
                                </th>
                                <th class="px-6 py-4 text-xs font-black text-amber-900 uppercase tracking-wider">
                                    Progress
                                </th>
                                <th class="px-6 py-4 text-xs font-black text-amber-900 uppercase tracking-wider text-center">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($data as $app)
                                <tr class="hover:bg-amber-50/10 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-mono text-xs text-amber-800 font-bold block">{{ $app->no_registrasi }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-800 line-clamp-2 max-w-sm">{{ $app->perijinan->nama_perijinan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-medium">
                                        {{ $app->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $app->status_color }}">
                                            {{ $app->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-gradient-to-r from-amber-500 to-amber-600 h-1.5 rounded-full"
                                                     style="width: {{ $app->progress_percentage }}%"></div>
                                            </div>
                                            <span class="text-[10px] font-bold text-gray-500">{{ $app->progress_percentage }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="{{ route('pemohon.tracking.detail', $app->id) }}"
                                           class="inline-flex items-center gap-1 bg-amber-100 hover:bg-amber-200 text-amber-800 font-bold py-1.5 px-3 rounded-lg text-[10px] transition-all shadow-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Footer & Pagination -->
                <div class="px-6 py-4 bg-amber-50/10 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-xs text-gray-500 font-semibold">
                        Menampilkan {{ $data->firstItem() ?? 0 }} sampai {{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} entri
                    </div>
                    @if ($data->hasPages())
                        <div class="flex justify-end">
                            {{ $data->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Empty State / No Results -->
            <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-12 text-center">
                <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6 text-amber-500">
                    <i class="fas fa-search-minus text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-700 mb-1">Tidak Ada Pengajuan Ditemukan</h3>
                <p class="text-gray-500 mb-6 max-w-sm mx-auto text-sm">
                    @if($search)
                        Tidak ada pengajuan yang cocok dengan kata kunci "{{ $search }}". Silakan reset pencarian Anda.
                    @else
                        Anda belum memiliki pengajuan perizinan aktif saat ini.
                    @endif
                </p>
                <div class="flex items-center justify-center gap-3">
                    @if($search)
                        <a href="{{ route('pemohon.tracking') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl text-xs transition-colors">
                            Kembali
                        </a>
                    @else
                        <a href="{{ route('pemohon.perijinan') }}" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs transition-colors">
                            Ajukan Perizinan
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <x-pemohon.footer></x-pemohon.footer>
</x-pemohon.layout>
