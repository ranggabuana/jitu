<x-layout>
    <x-slot:title>Laporan SKM</x-slot:title>
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Laporan Survey Kepuasan Masyarakat</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Lihat dan analisis hasil survey</p>
            </div>
            {{-- <a href="{{ route('skm.hasil.statistics') }}"
                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <i class="mdi mdi-chart-box"></i>
                <span>Statistik Lengkap</span>
            </a> --}}
        </div>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Responden</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalResponses }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <i class="mdi mdi-account-multiple text-blue-600 dark:text-blue-400 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Nilai Rata-rata</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ number_format($overallAverage ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <i class="mdi mdi-star text-green-600 dark:text-green-400 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Indeks Kepuasan</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ number_format($satisfactionPercentage ?? 0, 1) }}%</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <i class="mdi mdi-emoticon-happy text-purple-600 dark:text-purple-400 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <!-- Toolbar -->
        <div
            class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <label for="per_page" class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Show</label>
                <select id="per_page" name="per_page"
                    class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onchange="updatePerPage(this.value)">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
            </div>

            <div class="flex items-center gap-2">
                <label for="question_filter"
                    class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Pertanyaan:</label>
                <select id="question_filter" name="question_filter"
                    class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onchange="updateQuestionFilter(this.value)">
                    <option value="all" {{ $questionFilter == 'all' ? 'selected' : '' }}>Semua</option>
                    @foreach ($questions as $q)
                        <option value="{{ $q->id }}" {{ $questionFilter == $q->id ? 'selected' : '' }}>
                            {{ Str::limit($q->pertanyaan, 40) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-auto sm:flex-1 sm:max-w-md">
                <form id="search_form" method="GET" action="{{ route('skm.hasil.index') }}" class="relative">
                    <input type="text" name="search" id="search" value="{{ $search }}"
                        placeholder="Search Responden..."
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 pl-10 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="mdi mdi-magnify absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <button type="submit" class="hidden">Search</button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Responden
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Pertanyaan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Jawaban
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($hasilSkm as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ $item->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $item->responden_nama ?? 'Anonim' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $item->responden_email ?? ($item->nip ? 'NIP: ' . $item->nip : '-') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                                {{ Str::limit($item->dataSkm->pertanyaan, 50) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
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
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $jawabanColors[$item->jawaban] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $item->jawaban }} -
                                    {{ $jawabanLabels[$item->jawaban] ?? $item->jawaban_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2 flex-wrap">
                                    <a href="{{ route('skm.hasil.show', $item->id) }}"
                                        class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
                                        <i class="mdi mdi-eye"></i>
                                        <span>Detail</span>
                                    </a>
                                    <form action="{{ route('skm.hasil.destroy', $item->id) }}" method="POST"
                                        class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors btn-delete"
                                            data-action="{{ route('skm.hasil.destroy', $item->id) }}">
                                            <i class="mdi mdi-delete"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="mdi mdi-folder-open-outline text-4xl mb-2"></i>
                                <p>Belum ada data Hasil SKM</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($hasilSkm->hasPages())
            <div
                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ $hasilSkm->firstItem() }} to {{ $hasilSkm->lastItem() }} of
                    {{ $hasilSkm->total() }} entries
                </div>
                <div>
                    {{ $hasilSkm->links() }}
                </div>
            </div>
        @endif
    </div>
</x-layout>

<script>
    function updatePerPage(perPage) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }

    function updateQuestionFilter(questionId) {
        const url = new URL(window.location.href);
        url.searchParams.set('question_filter', questionId);
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }

    let searchTimeout;
    document.getElementById('search')?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('search_form').submit();
        }, 500);
    });
</script>
