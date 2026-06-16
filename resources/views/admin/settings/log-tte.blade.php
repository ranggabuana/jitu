<x-layout>
    <x-slot:title>Log Tanda Tangan Elektronik (TTE)</x-slot:title>

    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-800 dark:text-white flex items-center gap-3">
                <i class="mdi mdi-shield-key-outline text-green-500"></i>
                Log Tanda Tangan Elektronik
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Catatan aktivitas persetujuan dokumen melalui layanan BSrE E-Sign.
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
        <form action="{{ route('settings.log-tte') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label for="search" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Pencarian</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white transition-shadow" 
                           placeholder="Cari nama atau No Registrasi...">
                    <i class="mdi mdi-magnify absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                </div>
            </div>
            <div class="w-full sm:w-48">
                <label for="status" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Status</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white transition-shadow cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Berhasil</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2 shadow-sm shadow-blue-600/20">
                    <i class="mdi mdi-filter-variant"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('settings.log-tte') }}" class="w-full sm:w-auto px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-xl text-sm font-bold transition-all flex items-center justify-center shadow-sm">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-6 py-4 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Perijinan</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dokumen</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pesan/Error</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-800 dark:text-white">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-800 dark:text-white">{{ $log->user->name ?? '-' }}</div>
                                <div class="text-[10px] text-gray-500 uppercase tracking-wider">{{ $log->user->role ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->dataPerijinan)
                                    <div class="text-sm font-bold text-blue-600 dark:text-blue-400">
                                        <a href="{{ route('data-perijinan.show', $log->data_perijinan_id) }}" class="hover:underline">
                                            {{ $log->dataPerijinan->no_registrasi }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-gray-500 truncate max-w-[200px]" title="{{ $log->dataPerijinan->perijinan->nama_perijinan ?? '' }}">
                                        {{ $log->dataPerijinan->perijinan->nama_perijinan ?? '-' }}
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Data Dihapus</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider rounded-lg {{ $log->document_type == 'rekomendasi' ? 'bg-purple-100 text-purple-700' : 'bg-indigo-100 text-indigo-700' }}">
                                    {{ $log->document_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->status === 'success')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[10px] font-black uppercase tracking-wider">
                                        <i class="mdi mdi-check-circle text-sm leading-none"></i> Berhasil
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-[10px] font-black uppercase tracking-wider">
                                        <i class="mdi mdi-close-circle text-sm leading-none"></i> Gagal
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs {{ $log->status === 'success' ? 'text-green-600' : 'text-red-600 font-medium' }} line-clamp-2 max-w-xs" title="{{ $log->error_message }}">
                                    {{ $log->error_message ?? 'TTE berhasil dilakukan.' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="mdi mdi-text-box-search-outline text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-1">Tidak ada log</h3>
                                <p class="text-xs text-gray-500">Belum ada aktivitas TTE yang tercatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-layout>