<x-layout>
    <x-slot:title>Data Perijinan Perlu Perbaikan</x-slot:title>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    @if (session('error'))
        <meta name="error-message" content="{{ session('error') }}">
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Perlu Perbaikan</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm">Pengajuan yang dikembalikan untuk diperbaiki pemohon</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('data-perijinan.dalam-proses') }}" 
                class="inline-flex items-center gap-1 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="mdi mdi-timer-outline"></i>
                <span>Dalam Proses</span>
            </a>
        </div>
    </div>

    <!-- Stats Card -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg">
            <div class="text-3xl font-bold">{{ $totalPerluPerbaikan }}</div>
            <div class="text-orange-100 text-xs mt-1">Perlu Perbaikan</div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('data-perijinan.perlu-perbaikan') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="mdi mdi-magnify absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari no. registrasi, pemohon, atau perijinan..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <select name="perijinan_id" onchange="this.form.submit()"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                <option value="">Semua Jenis</option>
                @foreach($perijinanTypes as $type)
                    <option value="{{ $type->id }}" {{ request('perijinan_id') == $type->id ? 'selected' : '' }}>
                        {{ Str::limit($type->nama_perijinan, 25) }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Applications Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($applications as $app)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-orange-200 dark:border-orange-800 p-5 hover:shadow-lg transition-all cursor-pointer group"
                onclick="window.location.href='{{ route('data-perijinan.show', $app->id) }}'">
                
                <!-- Header -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-2 py-0.5 rounded">
                                {{ $app->no_registrasi }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded text-xs">
                                <i class="mdi mdi-arrow-return"></i>
                                Perbaikan
                            </span>
                        </div>
                        <h3 class="font-semibold text-gray-800 dark:text-white text-sm line-clamp-2 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                            {{ $app->perijinan->nama_perijinan }}
                        </h3>
                    </div>
                </div>

                <!-- Pemohon -->
                <div class="flex items-center gap-2 mb-3 text-sm text-gray-600 dark:text-gray-400">
                    <i class="mdi mdi-account text-gray-400"></i>
                    <span class="truncate">{{ $app->user->name }}</span>
                </div>

                <!-- Catatan Perbaikan -->
                @if($app->catatan_perbaikan)
                    <div class="mb-3 p-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
                        <div class="flex items-start gap-2">
                            <i class="mdi mdi-alert-circle text-orange-500 mt-0.5 flex-shrink-0"></i>
                            <p class="text-xs text-orange-700 dark:text-orange-300 line-clamp-2">{{ $app->catatan_perbaikan }}</p>
                        </div>
                    </div>
                @endif

                <!-- Footer -->
                <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            <i class="mdi mdi-clock"></i> {{ $app->updated_at->diffForHumans() }}
                        </span>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                        <i class="mdi mdi-arrow-return"></i> Menunggu Perbaikan
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="mdi mdi-file-document-off text-gray-400 dark:text-gray-500 text-4xl"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400">Belum ada pengajuan perlu perbaikan</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($applications->hasPages())
        <div class="mt-6">
            {{ $applications->links() }}
        </div>
    @endif
</x-layout>
