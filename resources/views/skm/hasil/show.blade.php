<x-layout>
    <x-slot:title>Detail Jawaban - Laporan SKM</x-slot:title>
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Detail Jawaban</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Informasi lengkap jawaban responden</p>
            </div>
            <a href="{{ route('skm.hasil.index') }}"
                class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <i class="mdi mdi-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Question Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Pertanyaan</h3>
                <p class="text-lg text-gray-800 dark:text-white">{{ $hasilSkm->dataSkm->pertanyaan }}</p>
            </div>

            <!-- Answer Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Jawaban</h3>
                @php
                    $jawabanColors = [
                        '1' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                        '2' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                        '3' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        '4' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                    ];
                    $jawabanLabels = [
                        '1' => 'Kurang Baik',
                        '2' => 'Cukup Baik',
                        '3' => 'Baik',
                        '4' => 'Sangat Baik',
                    ];
                @endphp
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center justify-center w-16 h-16 rounded-full text-2xl font-bold {{ $jawabanColors[$hasilSkm->jawaban] }}">
                        {{ $hasilSkm->jawaban }}
                    </span>
                    <div>
                        <p class="text-xl font-semibold text-gray-800 dark:text-white">{{ $jawabanLabels[$hasilSkm->jawaban] ?? $hasilSkm->jawaban_label }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nilai: {{ $hasilSkm->score }} dari 4</p>
                    </div>
                </div>
            </div>

            <!-- Saran Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Saran / Komentar</h3>
                @if($hasilSkm->saran)
                    <p class="text-gray-800 dark:text-white">{{ $hasilSkm->saran }}</p>
                @else
                    <p class="text-gray-500 dark:text-gray-400 italic">Tidak ada saran</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Respondent Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Informasi Responden</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nama</p>
                        <p class="text-gray-800 dark:text-white">{{ $hasilSkm->responden_nama ?? 'Anonim' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-gray-800 dark:text-white">{{ $hasilSkm->responden_email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">NIP</p>
                        <p class="text-gray-800 dark:text-white">{{ $hasilSkm->nip ?? '-' }}</p>
                    </div>
                    @if($hasilSkm->user)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">User</p>
                            <p class="text-gray-800 dark:text-white">{{ $hasilSkm->user->name }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Metadata -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Metadata</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">IP Address</p>
                        <p class="text-gray-800 dark:text-white font-mono text-sm">{{ $hasilSkm->ip_address ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Submit</p>
                        <p class="text-gray-800 dark:text-white">{{ $hasilSkm->created_at->format('d/m/Y H:i') }} WIB</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Terakhir Update</p>
                        <p class="text-gray-800 dark:text-white">{{ $hasilSkm->updated_at->format('d/m/Y H:i') }} WIB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
