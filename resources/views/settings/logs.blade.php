<x-layout>
    <x-slot:title>Log Aplikasi</x-slot:title>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <i class="mdi mdi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Log Aplikasi</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Catatan aktivitas dan perubahan data</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-6">
            <form action="{{ route('settings.logs') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        <i class="mdi mdi-magnify mr-1"></i> Cari
                    </label>
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Cari deskripsi atau user..."
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Filter by Log Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        <i class="mdi mdi-tag mr-1"></i> Kategori
                    </label>
                    <select name="log_name"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Kategori</option>
                        @foreach($logNames as $name)
                            <option value="{{ $name }}" {{ $logName == $name ? 'selected' : '' }}>
                                @php
                                    $labels = [
                                        'default' => 'Aktivitas Umum',
                                        'authentication' => 'Autentikasi',
                                        'perijinan' => 'Perizinan',
                                        'berita' => 'Berita',
                                        'regulasi' => 'Regulasi',
                                        'pengaduan' => 'Pengaduan',
                                        'user' => 'Pengguna',
                                        'settings' => 'Pengaturan',
                                    ];
                                @endphp
                                {{ $labels[$name] ?? ucfirst($name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter by Event -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        <i class="mdi mdi-lightning mr-1"></i> Event
                    </label>
                    <select name="event"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Event</option>
                        @foreach($events as $evt)
                            @php
                                $labels = [
                                    'created' => 'Data Dibuat',
                                    'updated' => 'Data Diubah',
                                    'deleted' => 'Data Dihapus',
                                    'login' => 'Masuk Sistem',
                                    'logout' => 'Keluar Sistem',
                                    'viewed' => 'Data Dilihat',
                                    'exported' => 'Data Diekspor',
                                    'imported' => 'Data Diimpor',
                                    'restored' => 'Data Dipulihkan',
                                ];
                            @endphp
                            <option value="{{ $evt }}" {{ $event == $evt ? 'selected' : '' }}>
                                {{ $labels[$evt] ?? ucfirst($evt) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg transition-colors font-medium flex items-center justify-center gap-2">
                        <i class="mdi mdi-filter"></i>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('settings.logs') }}"
                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2.5 rounded-lg transition-colors font-medium"
                        title="Reset Filter">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="mdi mdi-clipboard-text-clock text-gray-500"></i>
                Riwayat Aktivitas
            </h2>
            <div class="flex items-center gap-3">
                <button onclick="openExportModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center gap-2">
                    <i class="mdi mdi-file-excel"></i>
                    <span>Export Excel</span>
                </button>
                <span class="text-xs bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 px-3 py-1 rounded-full font-medium">
                    {{ $logs->total() }} logs
                </span>
            </div>
        </div>

        @if($logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Event
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Deskripsi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Subject
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                IP Address
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Waktu
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $color = $log->event_color;
                                        $icons = [
                                            'created' => 'mdi-plus-circle',
                                            'updated' => 'mdi-pencil',
                                            'deleted' => 'mdi-delete',
                                            'login' => 'mdi-login',
                                            'logout' => 'mdi-logout',
                                            'viewed' => 'mdi-eye',
                                            'exported' => 'mdi-download',
                                            'imported' => 'mdi-upload',
                                            'restored' => 'mdi-restore',
                                        ];
                                        $icon = $icons[$log->event] ?? 'mdi-information';
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-medium bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 text-{{ $color }}-700 dark:text-{{ $color }}-400">
                                        <i class="mdi {{ $icon }}"></i>
                                        {{ $log->event_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $log->description }}</p>
                                    @if($log->formatted_changes && count($log->formatted_changes) > 0)
                                        <div class="mt-2">
                                            <details class="text-xs">
                                                <summary class="cursor-pointer text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 font-medium">
                                                    <i class="mdi mdi-history"></i> {{ count($log->formatted_changes) }} field berubah
                                                </summary>
                                                <div class="mt-2 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                                    <table class="w-full">
                                                        <thead class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                                            <tr>
                                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Field</th>
                                                                @if($log->event === 'created')
                                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Nilai</th>
                                                                @else
                                                                    <th class="px-3 py-2 text-left text-xs font-medium text-red-600 dark:text-red-400">Sebelum</th>
                                                                    <th class="px-3 py-2 text-left text-xs font-medium text-green-600 dark:text-green-400">Sesudah</th>
                                                                @endif
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                            @foreach($log->formatted_changes as $change)
                                                                <tr>
                                                                    <td class="px-3 py-2 text-gray-700 dark:text-gray-300 font-medium">{{ $change['label'] }}</td>
                                                                    @if($log->event === 'created')
                                                                        <td class="px-3 py-2 text-gray-900 dark:text-gray-100 break-all">{{ $change['new'] ?: '-' }}</td>
                                                                    @else
                                                                        <td class="px-3 py-2 text-red-700 dark:text-red-300 break-all">{{ $change['old'] ?: '-' }}</td>
                                                                        <td class="px-3 py-2 text-green-700 dark:text-green-300 break-all">{{ $change['new'] ?: '-' }}</td>
                                                                    @endif
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </details>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->user)
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-{{ $log->user->role === 'admin' ? 'purple' : 'blue' }}-100 dark:bg-{{ $log->user->role === 'admin' ? 'purple' : 'blue' }}-900/30 flex items-center justify-center">
                                                <span class="text-{{ $log->user->role === 'admin' ? 'purple' : 'blue' }}-600 dark:text-{{ $log->user->role === 'admin' ? 'purple' : 'blue' }}-400 text-xs font-bold">
                                                    {{ substr($log->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $log->user->name }}</p>
                                                @php
                                                    $roleLabels = [
                                                        'admin' => 'Administrator',
                                                        'operator_opd' => 'Operator OPD',
                                                        'kepala_opd' => 'Kepala OPD',
                                                        'pemohon' => 'Pemohon',
                                                        'admin_publik' => 'Admin Publik',
                                                    ];
                                                    $roleLabel = $roleLabels[$log->user->role] ?? ucfirst(str_replace('_', ' ', $log->user->role));
                                                @endphp
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $roleLabel }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Sistem</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->subject_type && $log->subject_id)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                            <i class="mdi mdi-shape"></i>
                                            {{ $log->subject_label }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-mono">{{ $log->ip_address ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $log->created_at->format('d M Y, H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $log->created_at->diffForHumans() }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $logs->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                    <i class="mdi mdi-clipboard-text-off text-4xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <p class="text-gray-600 dark:text-gray-400 font-medium">Belum ada log aktivitas</p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Aktivitas akan tercatat otomatis saat ada perubahan data</p>
            </div>
        @endif
    </div>

    <!-- Export Modal -->
    <div id="exportModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="mdi mdi-file-excel text-2xl"></i>
                    <h3 class="text-lg font-bold">Export Log Aktivitas</h3>
                </div>
                <button onclick="closeExportModal()" class="text-white/80 hover:text-white">
                    <i class="mdi mdi-close text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tanggal Mulai <span class="text-gray-400 text-xs">(opsional)</span>
                    </label>
                    <input type="date" id="date_from" 
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tanggal Sampai <span class="text-gray-400 text-xs">(opsional)</span>
                    </label>
                    <input type="date" id="date_to" 
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                    <p class="text-sm text-blue-700 dark:text-blue-400">
                        <i class="mdi mdi-information mr-1"></i>
                        Kosongkan kedua tanggal untuk export semua data
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                <button onclick="closeExportModal()" 
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button onclick="exportLogs()" 
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors inline-flex items-center gap-2">
                    <i class="mdi mdi-download"></i>
                    Export
                </button>
            </div>
        </div>
    </div>
</x-layout>

<script>
    // Export Modal Functions
    function openExportModal() {
        const modal = document.getElementById('exportModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Reset date inputs
        document.getElementById('date_from').value = '';
        document.getElementById('date_to').value = '';
    }

    function closeExportModal() {
        const modal = document.getElementById('exportModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function exportLogs() {
        const dateFrom = document.getElementById('date_from').value;
        const dateTo = document.getElementById('date_to').value;
        
        // Build export URL with current filters + date range
        const baseUrl = '{{ route("settings.logs.export") }}';
        const url = new URL(baseUrl);
        
        // Add current filters
        const currentParams = new URLSearchParams(window.location.search);
        currentParams.forEach((value, key) => {
            url.searchParams.set(key, value);
        });
        
        // Add date range if provided
        if (dateFrom) {
            url.searchParams.set('date_from', dateFrom);
        }
        if (dateTo) {
            url.searchParams.set('date_to', dateTo);
        }
        
        // Redirect to export
        window.location.href = url.toString();
        
        // Close modal
        closeExportModal();
    }

    // Close modal on outside click
    document.getElementById('exportModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeExportModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeExportModal();
        }
    });
</script>
