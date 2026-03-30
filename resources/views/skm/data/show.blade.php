<x-layout>
    <x-slot:title>Statistik Pertanyaan SKM</x-slot:title>
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Statistik Pertanyaan SKM</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Analisis jawaban untuk pertanyaan ini</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('skm.data.edit', $dataSkm->id) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                    <i class="mdi mdi-pencil"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('skm.data.index') }}"
                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                    <i class="mdi mdi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    @php
        $labels = \App\Models\DataSkm::getSkalaLabels();
        $colors = ['bg-red-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total Responses -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Jawaban</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalResponses }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <i class="mdi mdi-message-reply text-blue-600 dark:text-blue-400 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Average Score -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Nilai Rata-rata</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($averageScore, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <i class="mdi mdi-star text-green-600 dark:text-green-400 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Satisfaction Percentage -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Kepuasan</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($averageScore ? ($averageScore / 4) * 100 : 0, 1) }}%</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <i class="mdi mdi-emoticon-happy text-purple-600 dark:text-purple-400 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status Pertanyaan</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dataSkm->status === 'aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                            {{ $dataSkm->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                    <i class="mdi mdi-toggle-switch text-orange-600 dark:text-orange-400 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Question Detail -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Detail Pertanyaan</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pertanyaan</p>
                    <p class="text-gray-800 dark:text-white">{{ $dataSkm->pertanyaan }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                    <p class="text-gray-800 dark:text-white">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dataSkm->status === 'aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                            {{ $dataSkm->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Urutan</p>
                    <p class="text-gray-800 dark:text-white">{{ $dataSkm->urutan }}</p>
                </div>
            </div>
        </div>

        <!-- Score Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Distribusi Jawaban</h3>
            <div class="space-y-3">
                @foreach($scoreDistribution as $score => $count)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">{{ $score }} - {{ $labels[$score] }}</span>
                            <span class="text-gray-800 dark:text-white font-medium">{{ $count }} ({{ $totalResponses > 0 ? round(($count / $totalResponses) * 100, 1) : 0 }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="{{ $colors[$score - 1] }} h-2.5 rounded-full" style="width: {{ $totalResponses > 0 ? ($count / $totalResponses) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Responses -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Jawaban Terbaru</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Responden</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Jawaban</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Saran</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($dataSkm->hasilSkm as $response)
                        <tr>
                            <td class="px-4 py-3 text-sm">
                                <p class="text-gray-800 dark:text-white">{{ $response->responden_nama ?? 'Anonim' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $response->responden_email ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $response->jawaban == '4' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($response->jawaban == '3' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : ($response->jawaban == '2' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200')) }}">
                                    {{ $response->jawaban }} - {{ $response->jawaban_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ Str::limit($response->saran, 50) ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $response->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="mdi mdi-inbox-outline text-4xl mb-2"></i>
                                <p>Belum ada jawaban</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
