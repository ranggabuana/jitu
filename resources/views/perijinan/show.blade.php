<x-layout>
    <x-slot:title>Detail {{ $perijinan->nama_perijinan }}</x-slot:title>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('perijinan.index') }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <i class="mdi mdi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">{{ $perijinan->nama_perijinan }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Detail informasi perijinan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-6 flex gap-3">
        <a href="{{ route('perijinan.form-builder', $perijinan->id) }}"
            class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/30 transition-colors">
                    <i class="mdi mdi-form-select text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-200">Kelola Formulir</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $perijinan->activeFormFields->count() }} field aktif</p>
                </div>
            </div>
        </a>
        <a href="{{ route('perijinan.alur-validasi', $perijinan->id) }}"
            class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-orange-300 dark:hover:border-orange-600 transition-colors group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center group-hover:bg-orange-100 dark:group-hover:bg-orange-900/30 transition-colors">
                    <i class="mdi mdi-sitemap text-orange-600 dark:text-orange-400"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-200">Alur Validasi</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $perijinan->validationFlows->count() }} tahap validasi</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Left Column: Informasi & Formulir -->
        <div class="space-y-6">

            <!-- Informasi Umum -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                        <i class="mdi mdi-book-open-page-variant text-gray-500"></i>
                        Informasi Umum
                    </h2>
                </div>
                <div class="p-5 space-y-4">
                    <!-- Dasar Hukum -->
                    <div>
                        <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Dasar Hukum</h3>
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            {!! $perijinan->dasar_hukum ?? '<span class="text-gray-400 italic">Tidak ada informasi</span>' !!}
                        </div>
                    </div>

                    @if($perijinan->persyaratan)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Persyaratan</h3>
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                {!! $perijinan->persyaratan !!}
                            </div>
                        </div>
                    @endif

                    @if($perijinan->prosedur)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Prosedur</h3>
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                {!! $perijinan->prosedur !!}
                            </div>
                        </div>
                    @endif

                    @if($perijinan->informasi_biaya)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Informasi Biaya</h3>
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                {!! $perijinan->informasi_biaya !!}
                            </div>
                        </div>
                    @endif

                    @if($perijinan->lama_waktu_proses)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Lama Waktu Proses</h3>
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $perijinan->lama_waktu_proses }}
                            </div>
                        </div>
                    @endif

                    @if($perijinan->gambar_alur && file_exists(public_path($perijinan->gambar_alur)))
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Gambar Alur</h3>
                            <div class="cursor-pointer group" onclick="document.getElementById('gambarAlurModal').classList.remove('hidden')">
                                <div class="relative rounded-lg border-2 border-gray-200 dark:border-gray-700 overflow-hidden bg-gray-50 dark:bg-gray-900/50 group-hover:border-blue-400 dark:group-hover:border-blue-500 transition-colors">
                                    <img src="{{ asset($perijinan->gambar_alur) }}" alt="Gambar Alur {{ $perijinan->nama_perijinan }}"
                                        class="w-full h-32 object-cover">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                        <div class="opacity-0 group-hover:opacity-100 transition-opacity bg-white/90 dark:bg-gray-800/90 rounded-full p-2">
                                            <i class="mdi mdi-magnify-expand text-blue-600 dark:text-blue-400 text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">
                                    <i class="mdi mdi-arrow-expand-all mr-1"></i>Klik untuk memperbesar
                                </p>
                            </div>
                        </div>

                        <!-- Modal Preview -->
                        <div id="gambarAlurModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" onclick="this.classList.add('hidden')">
                            <div class="relative max-w-5xl max-h-full" onclick="event.stopPropagation()">
                                <!-- Close Button -->
                                <button onclick="document.getElementById('gambarAlurModal').classList.add('hidden')"
                                    class="absolute -top-10 right-0 text-white hover:text-gray-300 transition-colors">
                                    <i class="mdi mdi-close text-3xl"></i>
                                </button>
                                <!-- Image -->
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-2">
                                    <img src="{{ asset($perijinan->gambar_alur) }}" alt="Gambar Alur {{ $perijinan->nama_perijinan }}"
                                        class="max-w-full max-h-[85vh] object-contain rounded">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Formulir -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                        <i class="mdi mdi-file-document text-gray-500"></i>
                        Formulir Permohonan
                    </h2>
                    <span class="text-xs bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 px-2 py-0.5 rounded-full font-medium">
                        {{ $perijinan->activeFormFields->count() }} field
                    </span>
                </div>

                @if($perijinan->activeFormFields->count() > 0)
                    <div class="p-5 space-y-3">
                        @foreach($perijinan->activeFormFields as $field)
                            <div class="flex items-start justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $field->label }}</span>
                                        @if($field->is_required)
                                            <span class="text-red-500" title="Wajib diisi">*</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <code class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">{{ $field->name }}</code>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">•</span>
                                        <span class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">{{ $field->type }}</span>
                                    </div>
                                </div>
                                @if($field->type === 'file' && $field->file_types)
                                    <span class="text-xs text-gray-500 dark:text-gray-400 text-right">
                                        {{ Str::limit($field->file_types, 15) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center">
                        <i class="mdi mdi-file-document-outline text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada formulir</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Alur Validasi -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                    <i class="mdi mdi-account-group text-gray-500"></i>
                    Alur Validasi
                </h2>
                <span class="text-xs bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 px-2 py-0.5 rounded-full font-medium">
                    {{ $perijinan->validationFlows->count() }} tahap
                </span>
            </div>

            @if($perijinan->validationFlows->count() > 0)
                <div class="p-5">
                    <div class="space-y-4">
                        @foreach($perijinan->validationFlows as $flow)
                            <div class="flex gap-3">
                                <!-- Order Number -->
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center shadow-md">
                                        <span class="text-white font-bold text-sm">{{ $flow->order }}</span>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-800 dark:text-white">{{ $flow->role_label }}</h4>
                                            @if($flow->assignedUser)
                                                <div class="flex items-center gap-1.5 mt-1">
                                                    <div class="w-5 h-5 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                                        <span class="text-orange-600 dark:text-orange-400 text-xs font-bold">
                                                            {{ substr($flow->assignedUser->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $flow->assignedUser->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        @if($flow->is_active)
                                            <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-2 py-0.5 rounded font-medium">Aktif</span>
                                        @else
                                            <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded font-medium">Nonaktif</span>
                                        @endif
                                    </div>

                                    @if($flow->sla_hours || $flow->description)
                                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            @if($flow->sla_hours)
                                                <span class="flex items-center gap-1">
                                                    <i class="mdi mdi-clock-outline"></i>
                                                    {{ $flow->sla_hours }} jam
                                                </span>
                                            @endif
                                            @if($flow->description)
                                                <span class="flex items-center gap-1">
                                                    <i class="mdi mdi-information-outline"></i>
                                                    {{ Str::limit($flow->description, 40) }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-8 text-center">
                    <i class="mdi mdi-account-group text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada alur validasi</p>
                </div>
            @endif
        </div>
    </div>
</x-layout>
