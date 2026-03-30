<section class="bg-white rounded-2xl border border-amber-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-amber-100 flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Daftar Pengajuan</h2>
            <p class="text-sm text-gray-500">Riwayat pengajuan perizinan Anda akan muncul di sini.</p>
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
                    <tr class="hover:bg-amber-50 transition-colors">
                        <td class="p-4 border-b">
                            <span class="font-mono font-semibold text-amber-700">{{ $app->no_registrasi ?? 'N/A' }}</span>
                        </td>
                        <td class="p-4 border-b">
                            <span class="font-medium text-gray-800">{{ $app->perijinan->nama_perijinan ?? 'N/A' }}</span>
                        </td>
                        <td class="p-4 border-b">
                            <span class="text-sm text-gray-500">{{ $app->created_at->format('d M Y') }}</span>
                        </td>
                        <td class="p-4 border-b">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'proses' => 'bg-amber-100 text-amber-800',
                                    'validasi' => 'bg-purple-100 text-purple-800',
                                    'perbaikan' => 'bg-red-100 text-red-800',
                                    'selesai' => 'bg-green-100 text-green-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                ];
                                $statusLabels = [
                                    'pending' => 'Menunggu',
                                    'proses' => 'Diproses',
                                    'validasi' => 'Validasi',
                                    'perbaikan' => 'Perbaikan',
                                    'selesai' => 'Selesai',
                                    'approved' => 'Disetujui',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$app->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$app->status] ?? $app->status }}
                            </span>
                        </td>
                        <td class="p-4 border-b text-right">
                            <button onclick="openDetail('{{ $app->id }}')" 
                                class="text-amber-700 hover:text-amber-900 font-medium text-sm">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </button>
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
                            <a href="#" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-all shadow-md">
                                <i class="fas fa-plus"></i> Ajukan Izin Baru
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
