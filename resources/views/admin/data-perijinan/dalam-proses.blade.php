<x-layout>
    <x-slot:title>Data Perijinan Dalam Proses</x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Data Perijinan Dalam Proses</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola pengajuan perizinan yang sedang diproses</p>
        </div>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    @if (session('error'))
        <meta name="error-message" content="{{ session('error') }}">
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total Dalam Proses -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transform transition-all hover:scale-105 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Dalam Proses</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($totalDalamProses) }}</h3>
                    <p class="text-xs text-blue-500 mt-2">
                        <i class="mdi mdi-progress-clock"></i> Sedang diproses
                    </p>
                </div>
                <div class="p-4 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <i class="mdi mdi-timer-outline text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Submitted -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transform transition-all hover:scale-105 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Diajukan</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($totalSubmitted) }}</h3>
                    <p class="text-xs text-green-500 mt-2">
                        <i class="mdi mdi-check-all"></i> Baru diajukan
                    </p>
                </div>
                <div class="p-4 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                    <i class="mdi mdi-file-upload text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transform transition-all hover:scale-105 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Dalam Validasi</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($totalInProgress) }}</h3>
                    <p class="text-xs text-yellow-500 mt-2">
                        <i class="mdi mdi-progress-download"></i> Sedang divalidasi
                    </p>
                </div>
                <div class="p-4 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                    <i class="mdi mdi-file-document-edit text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Perlu Perbaikan -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transform transition-all hover:scale-105 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Perlu Perbaikan</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($totalPerbaikan) }}</h3>
                    <p class="text-xs text-orange-500 mt-2">
                        <i class="mdi mdi-alert-circle"></i> Menunggu revisi
                    </p>
                </div>
                <div class="p-4 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400">
                    <i class="mdi mdi-file-alert text-2xl"></i>
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
                <form method="GET" action="{{ route('data-perijinan.dalam-proses') }}" class="flex items-center gap-2">
                    <select name="status" onchange="this.form.submit()"
                        class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Diajukan</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Dalam Validasi</option>
                        <option value="perbaikan" {{ request('status') == 'perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                    </select>
                </form>
            </div>

            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('data-perijinan.dalam-proses') }}" class="flex items-center gap-2">
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
                <form method="GET" action="{{ route('data-perijinan.dalam-proses') }}" class="relative">
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
                            Tanggal Pengajuan
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
                                {{ $app->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($app->status === 'submitted')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <i class="mdi mdi-file-upload"></i> Diajukan
                                    </span>
                                @elseif ($app->status === 'in_progress')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        <i class="mdi mdi-progress-download"></i> Dalam Validasi
                                    </span>
                                @elseif ($app->status === 'perbaikan')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                        <i class="mdi mdi-alert-circle"></i> Perlu Perbaikan
                                    </span>
                                @endif
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
                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="mdi mdi-file-document-off text-gray-400 dark:text-gray-500 text-3xl"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">Belum ada pengajuan dalam proses</p>
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
</x-layout>
