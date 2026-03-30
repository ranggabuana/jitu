<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Selamat Datang, {{ Auth::user()->name ?? 'Admin' }}!</h1>
                <p class="mt-2 opacity-90">Dashboard Sistem Perizinan - Pantau semua aktivitas perizinan Anda di sini.
                </p>
            </div>
            <div class="hidden lg:block">
                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                    <p class="text-sm opacity-80">Tanggal Hari Ini</p>
                    <p class="text-xl font-semibold">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Perijinan -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transform transition-all hover:scale-105 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Perizinan</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ number_format($totalPerijinan) }}</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Jenis perizinan terdaftar</p>
                </div>
                <div class="p-4 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <i class="mdi mdi-file-document-multiple text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Pemohon -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transform transition-all hover:scale-105 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Pemohon</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($totalPemohon) }}
                    </h3>
                    <p class="text-xs text-green-500 mt-2">
                        <i class="mdi mdi-check-circle"></i> {{ number_format($totalPemohonAktif) }} aktif
                    </p>
                </div>
                <div class="p-4 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                    <i class="mdi mdi-account-group text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total OPD -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transform transition-all hover:scale-105 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total OPD</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($totalOpd) }}
                    </h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Organisasi perangkat daerah</p>
                </div>
                <div class="p-4 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                    <i class="mdi mdi-office-building text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- SKM Satisfaction -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transform transition-all hover:scale-105 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Kepuasan Layanan</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $satisfactionPercentage }}%
                    </h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ number_format($totalResponsSkm) }}
                        respons SKM</p>
                </div>
                <div class="p-4 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                    <i class="mdi mdi-emoticon-happy text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Statistics Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <!-- Total Berita -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                    <i class="mdi mdi-newspaper"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Berita</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $totalBerita }}</p>
                    <p class="text-xs text-green-500">{{ $totalBeritaAktif }} aktif</p>
                </div>
            </div>
        </div>

        <!-- Total Pengguna -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                    <i class="mdi mdi-account-multiple"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Pengguna</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $totalPengguna }}</p>
                    <p class="text-xs text-gray-400">Selain pemohon</p>
                </div>
            </div>
        </div>

        <!-- Pertanyaan SKM -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-lg bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400">
                    <i class="mdi mdi-clipboard-text"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pertanyaan SKM</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $totalPertanyaanSkm }}</p>
                    <p class="text-xs text-green-500">{{ $totalPertanyaanAktif }} aktif</p>
                </div>
            </div>
        </div>

        <!-- Activity Logs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400">
                    <i class="mdi mdi-history"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Aktivitas Hari Ini</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        {{ $recentActivities->where('created_at', '>=', now()->startOfDay())->count() }}</p>
                    <p class="text-xs text-gray-400">Log aktivitas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Perijinan Trend -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    <i class="mdi mdi-chart-line text-blue-500 mr-2"></i>
                    Tren Perizinan (6 Bulan Terakhir)
                </h3>
            </div>
            <div class="h-64">
                <canvas id="perijinanChart"></canvas>
            </div>
        </div>

        <!-- SKM Monthly Trend -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    <i class="mdi mdi-chart-areaspline text-green-500 mr-2"></i>
                    Tren SKM & Kepuasan (6 Bulan Terakhir)
                </h3>
            </div>
            <div class="h-64">
                <canvas id="skmChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Users by Role & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Users by Role -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                <i class="mdi mdi-account-circle text-purple-500 mr-2"></i>
                Pengguna Berdasarkan Role
            </h3>
            <div class="space-y-3">
                @php
                    $roleColors = [
                        'admin' => 'bg-red-500',
                        'fo' => 'bg-blue-500',
                        'bo' => 'bg-green-500',
                        'operator_opd' => 'bg-yellow-500',
                        'kepala_opd' => 'bg-purple-500',
                        'verifikator' => 'bg-indigo-500',
                        'kadin' => 'bg-pink-500',
                    ];
                    $roleLabels = [
                        'admin' => 'Admin',
                        'fo' => 'Front Office',
                        'bo' => 'Back Office',
                        'operator_opd' => 'Operator OPD',
                        'kepala_opd' => 'Kepala OPD',
                        'verifikator' => 'Verifikator',
                        'kadin' => 'Kadin',
                    ];
                @endphp
                @foreach ($usersByRole as $role => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 rounded-full {{ $roleColors[$role] ?? 'bg-gray-500' }}"></div>
                            <span
                                class="text-sm text-gray-600 dark:text-gray-300">{{ $roleLabels[$role] ?? ucfirst($role) }}</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Total</span>
                    <span class="text-lg font-bold text-gray-800 dark:text-white">{{ array_sum($usersByRole) }}</span>
                </div>
            </div>
        </div>

        <!-- Recent Activity Logs -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    <i class="mdi mdi-history text-orange-500 mr-2"></i>
                    Aktivitas Terbaru
                </h3>
                <a href="#" class="text-sm text-blue-500 hover:text-blue-600">Lihat Semua</a>
            </div>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($recentActivities as $activity)
                    <div
                        class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex-shrink-0">
                            @php
                                $activityIcons = [
                                    'created' => 'mdi-plus-circle text-green-500',
                                    'updated' => 'mdi-pencil text-blue-500',
                                    'deleted' => 'mdi-delete text-red-500',
                                    'viewed' => 'mdi-eye text-purple-500',
                                    'login' => 'mdi-login text-indigo-500',
                                    'logout' => 'mdi-logout text-gray-500',
                                ];
                            @endphp
                            <div
                                class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <i
                                    class="mdi {{ $activityIcons[$activity->event] ?? 'mdi-information' }} text-lg"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800 dark:text-gray-200 truncate">{{ $activity->description }}
                            </p>
                            <div class="flex items-center space-x-2 mt-1">
                                <span
                                    class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->user?->name ?? 'System' }}</span>
                                <span class="text-xs text-gray-400">•</span>
                                <span
                                    class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">Belum ada aktivitas tercatat</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Data Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Perijinan -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    <i class="mdi mdi-file-document text-blue-500 mr-2"></i>
                    Perizinan Terbaru
                </h3>
                <a href="{{ route('perijinan.index') }}" class="text-sm text-blue-500 hover:text-blue-600">Lihat
                    Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Nama Perizinan</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Dasar Hukum</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentPerijinan as $perijinan)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ Str::limit($perijinan->nama_perijinan, 40) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ Str::limit($perijinan->dasar_hukum, 30) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Belum ada data perizinan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Pemohon -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    <i class="mdi mdi-account-plus text-green-500 mr-2"></i>
                    Pemohon Terbaru
                </h3>
                <a href="{{ route('pemohon.index') }}" class="text-sm text-blue-500 hover:text-blue-600">Lihat
                    Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Nama</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Email</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentPemohon as $pemohon)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $pemohon->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $pemohon->email }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($pemohon->status === 'aktif')
                                        <span
                                            class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Belum ada pemohon terdaftar
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Common chart options
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                    }
                },
                x: {
                    grid: {
                        color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                    }
                }
            }
        };

        // Perijinan Chart
        const perijinanCtx = document.getElementById('perijinanChart').getContext('2d');
        const perijinanData = @json($monthlyPerijinan);

        new Chart(perijinanCtx, {
            type: 'bar',
            data: {
                labels: perijinanData.map(item => item.month),
                datasets: [{
                    label: 'Jumlah Perizinan',
                    data: perijinanData.map(item => item.total),
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: commonOptions
        });

        // SKM Chart
        const skmCtx = document.getElementById('skmChart').getContext('2d');
        const skmData = @json($monthlySkm);

        new Chart(skmCtx, {
            type: 'line',
            data: {
                labels: skmData.map(item => item.month),
                datasets: [{
                        label: 'Rata-rata Skor SKM',
                        data: skmData.map(item => item.avg_score),
                        borderColor: 'rgba(34, 197, 94, 1)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Total Respons',
                        data: skmData.map(item => item.total),
                        borderColor: 'rgba(251, 191, 36, 1)',
                        backgroundColor: 'rgba(251, 191, 36, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        min: 0,
                        max: 4,
                        title: {
                            display: true,
                            text: 'Skor (1-4)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        min: 0,
                        grid: {
                            drawOnChartArea: false,
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Respons'
                        }
                    }
                }
            }
        });
    </script>
</x-layout>
