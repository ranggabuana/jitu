<x-front-layout>
    <!-- Hero Section -->
    <section
        class="relative bg-gradient-to-br from-blue-800 via-blue-700 to-indigo-900 text-white py-16 overflow-hidden">
        <div class="absolute top-0 right-0 opacity-5 transform translate-x-16 -translate-y-8">
            <i class="fas fa-balance-scale text-9xl"></i>
        </div>
        <div class="absolute bottom-0 left-0 opacity-5 transform -translate-x-8 translate-y-8">
            <i class="fas fa-gavel text-8xl"></i>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div
                class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full border border-white/20 mb-4">
                <i class="fas fa-scale-balanced text-yellow-400"></i>
                <span class="text-sm font-medium">Database Peraturan & Regulasi</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Pusat <span class="text-yellow-400">Regulasi</span>
            </h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                Akses berbagai peraturan, keputusan, dan dokumen hukum terkait pelayanan perizinan
                di Kabupaten Banjarnegara
            </p>
        </div>

        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-white/30 to-transparent">
        </div>
    </section>

    <!-- Table Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Stats & Search -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
                <div class="flex flex-col lg:flex-row justify-between items-center gap-6">
                    <!-- Stats -->
                    <div class="flex flex-wrap justify-center lg:justify-start gap-6">
                        <div class="text-center">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-file-alt text-2xl text-blue-600"></i>
                                <div class="text-3xl font-bold text-gray-800">{{ $totalRegulasi }}</div>
                            </div>
                            <div class="text-gray-600 text-sm font-medium">Total Regulasi</div>
                        </div>
                        <div class="w-px h-12 bg-gray-200 hidden sm:block"></div>
                        <div class="text-center">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-calendar-check text-2xl text-green-600"></i>
                                <div class="text-3xl font-bold text-gray-800">{{ $recentCount }}</div>
                            </div>
                            <div class="text-gray-600 text-sm font-medium">Baru Bulan Ini</div>
                        </div>
                    </div>

                    <!-- Search & Filter -->
                    <form action="{{ route('regulasi.public') }}" method="GET" class="w-full lg:w-auto flex flex-col sm:flex-row gap-3">
                        <!-- Jenis Filter -->
                        <select name="jenis_filter" 
                            class="px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all bg-white text-gray-700">
                            <option value="all">Semua Jenis</option>
                            @foreach($jenisList as $jenis)
                                <option value="{{ $jenis->id }}" {{ $jenisFilter == $jenis->id ? 'selected' : '' }}>
                                    {{ $jenis->nama_jenis }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Search Input -->
                        <div class="relative flex-1 lg:w-80">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Cari regulasi..."
                                class="w-full pl-11 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-3">
                            <button type="submit"
                                class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-md hover:shadow-lg">
                                <i class="fas fa-search"></i>
                            </button>
                            @if ($search || $jenisFilter != 'all')
                                <a href="{{ route('regulasi.public') }}"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-bold transition-all flex items-center justify-center">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            @if ($regulasi->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">No.</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Jenis</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Nama Regulasi</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Deskripsi</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">File</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Tanggal</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($regulasi as $index => $item)
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $regulasi->firstItem() + $index }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-block bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-semibold">
                                                {{ $item->jenisRegulasi->nama_jenis ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                                                    <i
                                                        class="fas
                                                        @if (pathinfo($item->file_regulasi, PATHINFO_EXTENSION) === 'pdf') fa-file-pdf
                                                        @elseif(pathinfo($item->file_regulasi, PATHINFO_EXTENSION) === 'doc' ||
                                                                pathinfo($item->file_regulasi, PATHINFO_EXTENSION) === 'docx') fa-file-word
                                                        @elseif(pathinfo($item->file_regulasi, PATHINFO_EXTENSION) === 'xls' ||
                                                                pathinfo($item->file_regulasi, PATHINFO_EXTENSION) === 'xlsx') fa-file-excel
                                                        @else fa-file @endif"></i>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-800 mb-1">
                                                        {{ $item->nama_regulasi }}
                                                    </div>
                                                    <span
                                                        class="inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                                                        <i class="fas fa-check-circle mr-1"></i>Aktif
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 max-w-md">
                                            {{ Str::limit($item->deskripsi ?? 'Tidak ada deskripsi', 80) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded-lg text-xs font-semibold uppercase">
                                                    {{ strtoupper(pathinfo($item->file_regulasi, PATHINFO_EXTENSION)) }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    {{ $item->formatted_file_size }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <div class="flex items-center gap-2">
                                                <i class="far fa-calendar text-gray-400"></i>
                                                {{ $item->created_at->format('d M Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('regulasi.public.download', $item->id) }}"
                                                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                                                <i class="fas fa-download"></i>
                                                <span class="hidden sm:inline">Unduh</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($regulasi->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <div class="text-sm text-gray-600">
                                    Menampilkan <span
                                        class="font-semibold text-gray-800">{{ $regulasi->firstItem() }}</span>
                                    sampai <span class="font-semibold text-gray-800">{{ $regulasi->lastItem() }}</span>
                                    dari <span class="font-semibold text-gray-800">{{ $regulasi->total() }}</span>
                                    regulasi
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($regulasi->onFirstPage())
                                        <span
                                            class="px-4 py-2 rounded-lg border border-gray-200 text-gray-400 cursor-not-allowed bg-white">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    @else
                                        <a href="{{ $regulasi->previousPageUrl() }}"
                                            class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all bg-white text-gray-700">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    @endif

                                    @foreach ($regulasi->getUrlRange(1, min($regulasi->lastPage(), 5)) as $page => $url)
                                        @if ($page == $regulasi->currentPage())
                                            <span
                                                class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold shadow-md">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $url }}"
                                                class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all bg-white text-gray-700 font-medium">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach

                                    @if ($regulasi->hasMorePages())
                                        <a href="{{ $regulasi->nextPageUrl() }}"
                                            class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all bg-white text-gray-700">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    @else
                                        <span
                                            class="px-4 py-2 rounded-lg border border-gray-200 text-gray-400 cursor-not-allowed bg-white">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-100">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-full mb-6">
                        <i class="fas fa-folder-open text-5xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Belum Ada Regulasi</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">
                        Maaf, saat ini belum ada regulasi yang tersedia untuk umum.
                        Silakan kunjungi kembali nanti.
                    </p>
                    <a href="{{ route('landing') }}"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg transition-all transform hover:scale-105">
                        <i class="fas fa-home"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            @endif
        </div>
    </section>
</x-front-layout>
