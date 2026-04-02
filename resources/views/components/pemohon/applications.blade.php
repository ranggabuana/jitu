<section class="bg-white rounded-2xl border border-amber-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-amber-100 flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Daftar Pengajuan</h2>
            <p class="text-sm text-gray-500">Riwayat pengajuan perizinan Anda</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Status Filter -->
            <select id="statusFilter" onchange="filterApplications()"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                <option value="">Semua Status</option>
                <option value="submitted">Dalam Proses</option>
                <option value="in_progress">Dalam Validasi</option>
                <option value="perbaikan">Perlu Perbaikan</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
            </select>
            <a href="{{ route('pemohon.tracking') }}"
                class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-semibold text-sm">
                Lihat Semua <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-amber-50 text-gray-600 text-xs uppercase">
                <tr>
                    <th class="p-4 border-b">No. Registrasi</th>
                    <th class="p-4 border-b">Layanan</th>
                    <th class="p-4 border-b">Tgl Pengajuan</th>
                    <th class="p-4 border-b">Status</th>
                    <th class="p-4 border-b text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-amber-100" id="tableBody">
                @forelse($applications as $app)
                    <tr class="hover:bg-amber-50 transition-colors application-row" data-status="{{ $app->status }}">
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
                        <td colspan="5" class="p-8 text-center">
                            <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-folder-open text-amber-400 text-3xl"></i>
                            </div>
                            <h3 class="font-bold text-gray-700 mb-2">Belum Ada Pengajuan</h3>
                            <p class="text-gray-500 text-sm mb-4 max-w-md mx-auto">
                                Anda belum memiliki pengajuan perizinan. Mulai ajukan perizinan Anda sekarang untuk mengakses layanan perizinan terpadu.
                            </p>
                            <a href="{{ route('pemohon.perijinan') }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-all shadow-md">
                                <i class="fas fa-plus"></i> Ajukan Izin Baru
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- No Results Message -->
    <div id="noResults" class="hidden p-8 text-center">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-search text-gray-400 text-3xl"></i>
        </div>
        <h3 class="font-bold text-gray-700 mb-2">Tidak Ada Pengajuan</h3>
        <p class="text-gray-500 text-sm">Tidak ada pengajuan dengan status yang dipilih.</p>
    </div>
</section>

<script>
function filterApplications() {
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.application-row');
    const noResults = document.getElementById('noResults');
    const tableBody = document.getElementById('tableBody');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        
        if (!statusFilter || rowStatus === statusFilter) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    if (visibleCount === 0) {
        tableBody.style.display = 'none';
        noResults.classList.remove('hidden');
    } else {
        tableBody.style.display = '';
        noResults.classList.add('hidden');
    }
}
</script>
