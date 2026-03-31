<x-pemohon.layout>
    <x-slot:title>Tracking Pengajuan - JITU Banjarnegara</x-slot:title>

    <!-- Navbar -->
    <x-pemohon.navbar></x-pemohon.navbar>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-br from-amber-600 via-amber-700 to-amber-800 rounded-3xl shadow-xl p-6 text-white">
            <div class="flex items-center gap-4">
                <a href="{{ route('pemohon.dashboard') }}" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold mb-1">Tracking Pengajuan</h1>
                    <p class="text-amber-100 text-sm">Pantau status perizinan Anda secara real-time</p>
                </div>
            </div>
        </div>

        <!-- Applications List -->
        @if ($data->count() > 0)
            <div class="space-y-4">
                @foreach ($data as $app)
                    <a href="{{ route('pemohon.tracking.detail', $app->id) }}"
                        class="block bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all border border-amber-200 overflow-hidden group">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="font-mono text-sm text-amber-600 font-semibold">
                                            {{ $app->no_registrasi }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $app->status_color }}">
                                            {{ $app->status_label }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-amber-600 transition-colors">
                                        {{ $app->perijinan->nama_perijinan }}
                                    </h3>
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $app->created_at->format('d M Y') }}
                                        </span>
                                        <span>
                                            <i class="fas fa-layer-group mr-1"></i>
                                            Tahap {{ $app->current_step }} dari {{ $app->perijinan->activeValidationFlows->count() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-amber-600 transition-colors"></i>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-4">
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                    <span>Progress</span>
                                    <span>{{ $app->progress_percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-amber-500 to-amber-600 h-2 rounded-full transition-all duration-500"
                                        style="width: {{ $app->progress_percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($data->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $data->links('pagination::tailwind') }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-12 text-center">
                <div class="w-24 h-24 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-amber-400 text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Pengajuan</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">
                    Anda belum memiliki pengajuan perizinan. Mulai ajukan perizinan Anda sekarang.
                </p>
                <a href="{{ route('pemohon.perijinan') }}"
                    class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-6 rounded-xl transition-colors">
                    <i class="fas fa-plus"></i>
                    Ajukan Perizinan
                </a>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <x-pemohon.footer></x-pemohon.footer>
</x-pemohon.layout>
