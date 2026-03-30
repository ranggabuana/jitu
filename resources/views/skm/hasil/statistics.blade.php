<x-layout>
    <x-slot:title>Statistik - Laporan SKM</x-slot:title>
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Statistik Survey Kepuasan Masyarakat</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Analisis lengkap hasil survey</p>
            </div>
            <a href="{{ route('skm.hasil.index') }}"
                class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <i class="mdi mdi-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Overall Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100">Total Responden</p>
                    <p class="text-4xl font-bold mt-2">{{ $totalAllResponses }}</p>
                </div>
                <i class="mdi mdi-account-multiple text-6xl text-blue-300"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100">Nilai Rata-rata</p>
                    <p class="text-4xl font-bold mt-2">{{ number_format($overallAverage, 2) }}</p>
                </div>
                <i class="mdi mdi-star text-6xl text-green-300"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100">Indeks Kepuasan</p>
                    <p class="text-4xl font-bold mt-2">{{ number_format($overallSatisfaction, 1) }}%</p>
                </div>
                <i class="mdi mdi-emoticon-happy text-6xl text-purple-300"></i>
            </div>
        </div>
    </div>

    <!-- Satisfaction Category -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Kategori Kepuasan</h3>
        <div class="flex items-center justify-center">
            @php
                $category = '';
                $categoryColor = '';
                $categoryIcon = '';
                if ($overallSatisfaction >= 90) {
                    $category = 'Sangat Baik';
                    $categoryColor = 'text-green-600';
                    $categoryIcon = 'mdi-emoticon-happy-outline';
                } elseif ($overallSatisfaction >= 70) {
                    $category = 'Baik';
                    $categoryColor = 'text-blue-600';
                    $categoryIcon = 'mdi-emoticon-happy';
                } elseif ($overallSatisfaction >= 50) {
                    $category = 'Cukup Baik';
                    $categoryColor = 'text-yellow-600';
                    $categoryIcon = 'mdi-emoticon-neutral';
                } else {
                    $category = 'Kurang Baik';
                    $categoryColor = 'text-red-600';
                    $categoryIcon = 'mdi-emoticon-sad';
                }
            @endphp
            <div class="text-center">
                <i class="mdi {{ $categoryIcon }} text-8xl {{ $categoryColor }} mb-4"></i>
                <p class="text-3xl font-bold {{ $categoryColor }}">{{ $category }}</p>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Berdasarkan {{ number_format($overallSatisfaction, 1) }}% kepuasan</p>
            </div>
        </div>
    </div>

    <!-- Per Question Statistics -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Statistik Per Pertanyaan</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pertanyaan</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total Jawaban</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nilai Rata-rata</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Kepuasan</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($overallStats as $index => $stat)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-200">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-200">
                                {{ Str::limit($stat['question']->pertanyaan, 60) }}
                            </td>
                            <td class="px-4 py-4 text-sm text-center text-gray-900 dark:text-gray-200">
                                {{ $stat['total_responses'] }}
                            </td>
                            <td class="px-4 py-4 text-sm text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $stat['average_score'] >= 3 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($stat['average_score'] >= 2 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                    {{ $stat['average_score'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stat['satisfaction_percentage'] }}%"></div>
                                    </div>
                                    <span class="text-gray-800 dark:text-white font-medium">{{ $stat['satisfaction_percentage'] }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-center">
                                <a href="{{ route('skm.data.show', $stat['question']->id) }}"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    <i class="mdi mdi-chart-bar"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Monthly Trend -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Tren Bulanan (6 Bulan Terakhir)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Bulan</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total Responden</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nilai Rata-rata</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($monthlyTrend as $month)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-200">
                                @php
                                    $monthName = \Carbon\Carbon::parse($month->month . '-01')->format('F Y');
                                @endphp
                                {{ $monthName }}
                            </td>
                            <td class="px-4 py-4 text-sm text-center text-gray-900 dark:text-gray-200">
                                {{ $month->total }}
                            </td>
                            <td class="px-4 py-4 text-sm text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $month->avg_score >= 3 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($month->avg_score >= 2 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                    {{ number_format($month->avg_score, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-center">
                                @php
                                    $monthSatisfaction = ($month->avg_score / 4) * 100;
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $monthSatisfaction >= 70 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($monthSatisfaction >= 50 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                    {{ number_format($monthSatisfaction, 1) }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="mdi mdi-chart-line text-4xl mb-2"></i>
                                <p>Belum ada data trend</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
