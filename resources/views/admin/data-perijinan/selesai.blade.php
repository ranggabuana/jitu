<x-layout>
    <x-slot:title>Data Perijinan Selesai</x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Data Perijinan Selesai</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Riwayat pengajuan perizinan yang telah disetujui</p>
        </div>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    @if (session('error'))
        <meta name="error-message" content="{{ session('error') }}">
    @endif

    <!-- Header Stats -->
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 w-1/3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="mdi mdi-folder-check text-white text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">Total Pengajuan</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pengajuan selesai</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-bold text-green-600 dark:text-green-400">{{ number_format($totalSelesai) }}</div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">pengajuan</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <!-- Toolbar -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Total: <strong class="text-gray-800 dark:text-white">{{ $applications->total() }} pengajuan</strong>
                </span>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="openExportModal()"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors text-sm font-medium">
                    <i class="mdi mdi-file-excel"></i>
                    <span>Export Excel</span>
                </button>
                <form method="GET" action="{{ route('data-perijinan.selesai') }}" class="flex items-center gap-2">
                    <select name="perijinan_id" onchange="this.form.submit()"
                        class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Jenis</option>
                        @foreach($perijinanTypes as $type)
                            <option value="{{ $type->id }}" {{ request('perijinan_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->nama_perijinan }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="w-full sm:w-auto sm:flex-1 sm:max-w-md">
                <form method="GET" action="{{ route('data-perijinan.selesai') }}" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pengajuan..."
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 pl-10 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="mdi mdi-magnify absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            No. Registrasi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Pemohon
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Jenis Perijinan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Tanggal Disetujui
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($applications as $app)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono font-semibold text-blue-600 dark:text-blue-400">{{ $app->no_registrasi }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="mdi mdi-account text-blue-600 dark:text-blue-300"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $app->user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $app->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900 dark:text-white">{{ $app->perijinan->nama_perijinan }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $app->approved_at ? $app->approved_at->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <i class="mdi mdi-check-circle"></i> Disetujui
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('data-perijinan.show', $app->id) }}"
                                    class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
                                    <i class="mdi mdi-eye"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="mdi mdi-inbox-check text-green-400 dark:text-green-500 text-3xl"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada pengajuan selesai</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">Pengajuan yang telah disetujui akan muncul disini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $applications->links() }}
        </div>
    </div>

    <!-- Export Modal -->
    <div id="exportModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="mdi mdi-file-excel text-2xl"></i>
                    <h3 class="text-lg font-bold">Export Data Selesai</h3>
                </div>
                <button onclick="closeExportModal()" class="text-white/80 hover:text-white">
                    <i class="mdi mdi-close text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tanggal Mulai <span class="text-gray-400 text-xs">(opsional)</span>
                    </label>
                    <input type="date" id="date_from" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tanggal Sampai <span class="text-gray-400 text-xs">(opsional)</span>
                    </label>
                    <input type="date" id="date_to" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-green-500">
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                    <p class="text-sm text-blue-700 dark:text-blue-400">
                        <i class="mdi mdi-information mr-1"></i>
                        Kosongkan kedua tanggal untuk export semua data
                    </p>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                <button onclick="closeExportModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button onclick="exportData()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors inline-flex items-center gap-2">
                    <i class="mdi mdi-download"></i>
                    Export
                </button>
            </div>
        </div>
    </div>
</x-layout>

<script>
    function openExportModal() {
        document.getElementById('exportModal').classList.remove('hidden');
        document.getElementById('exportModal').classList.add('flex');
        document.getElementById('date_from').value = '';
        document.getElementById('date_to').value = '';
    }

    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
        document.getElementById('exportModal').classList.remove('flex');
    }

    function exportData() {
        const dateFrom = document.getElementById('date_from').value;
        const dateTo = document.getElementById('date_to').value;
        const baseUrl = '{{ route("data-perijinan.selesai.export") }}';
        const url = new URL(baseUrl);
        const currentParams = new URLSearchParams(window.location.search);
        currentParams.forEach((value, key) => {
            if (key !== 'page') url.searchParams.set(key, value);
        });
        if (dateFrom) url.searchParams.set('date_from', dateFrom);
        if (dateTo) url.searchParams.set('date_to', dateTo);
        window.location.href = url.toString();
        closeExportModal();
    }

    document.getElementById('exportModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeExportModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeExportModal();
    });
</script>
