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

    <!-- Tabs Navigation -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button onclick="switchTab('tab-umum')" id="btn-tab-umum" class="tab-btn active-tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-all">
                <i class="mdi mdi-information-outline text-lg"></i>
                Umum
            </button>
            <button onclick="switchTab('tab-formulir')" id="btn-tab-formulir" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-all">
                <i class="mdi mdi-file-document-edit-outline text-lg"></i>
                Isian Formulir
            </button>
            <button onclick="switchTab('tab-alur')" id="btn-tab-alur" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-all">
                <i class="mdi mdi-sitemap text-lg"></i>
                Alur Validasi
            </button>

        </nav>
    </div>

    <!-- Tab Contents -->
    <div class="max-w-5xl">
        <!-- Tab: Umum -->
        <div id="tab-umum" class="tab-content space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30">
                    <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="mdi mdi-book-open-page-variant text-blue-500"></i>
                        Informasi Perizinan
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Kode Perizinan</h3>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700/50 px-3 py-2 rounded-lg border border-gray-100 dark:border-gray-600 inline-block min-w-[120px]">
                                    {{ $perijinan->kode_perijinan ?: '-' }}
                                </p>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Nama Perizinan</h3>
                                <p class="text-base font-bold text-gray-900 dark:text-white leading-relaxed">
                                    {{ $perijinan->nama_perijinan }}
                                </p>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Opsi Perpanjangan</h3>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    @if($perijinan->opsi_perpanjangan == 'setelah_habis')
                                        Setelah masa berlaku habis
                                    @elseif($perijinan->opsi_perpanjangan == 'sebelum_habis')
                                        Sebelum masa berlaku habis
                                    @elseif($perijinan->opsi_perpanjangan == 'keduanya')
                                        Keduanya (Sebelum & Setelah habis)
                                    @else
                                        <span class="text-gray-400 italic">Tidak ada perpanjangan</span>
                                    @endif
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Lama Waktu Proses</h3>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                        <i class="mdi mdi-clock-outline text-orange-500"></i>
                                        {{ $perijinan->lama_waktu_proses ?: '-' }}
                                    </p>
                                </div>
                                <div>
                                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Informasi Biaya</h3>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                        <i class="mdi mdi-cash text-green-500"></i>
                                        {{ $perijinan->informasi_biaya ?: '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            @if($perijinan->gambar_alur && file_exists(public_path($perijinan->gambar_alur)))
                                <div>
                                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Gambar Alur</h3>
                                    <div class="relative group cursor-pointer" onclick="document.getElementById('gambarAlurModal').classList.remove('hidden')">
                                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-gray-50 dark:bg-gray-900/50 aspect-video flex items-center justify-center">
                                            <img src="{{ asset($perijinan->gambar_alur) }}" class="max-w-full max-h-full object-contain">
                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all flex items-center justify-center">
                                                <div class="opacity-0 group-hover:opacity-100 bg-white/90 dark:bg-gray-800/90 rounded-full p-2 shadow-lg scale-90 group-hover:scale-100 transition-all">
                                                    <i class="mdi mdi-magnify-plus text-blue-600 dark:text-blue-400 text-xl"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Preview -->
                                <div id="gambarAlurModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" onclick="this.classList.add('hidden')">
                                    <div class="relative max-w-5xl w-full" onclick="event.stopPropagation()">
                                        <button onclick="document.getElementById('gambarAlurModal').classList.add('hidden')" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
                                            <i class="mdi mdi-close text-4xl"></i>
                                        </button>
                                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-2 shadow-2xl">
                                            <img src="{{ asset($perijinan->gambar_alur) }}" class="w-full h-auto max-h-[85vh] object-contain rounded-xl">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700 space-y-8">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <i class="mdi mdi-gavel text-blue-500"></i> Dasar Hukum
                            </h3>
                            <div class="prose dark:prose-invert max-w-none text-sm text-gray-700 dark:text-gray-300">
                                {!! $perijinan->dasar_hukum !!}
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <i class="mdi mdi-clipboard-list-outline text-orange-500"></i> Persyaratan
                            </h3>
                            <div class="prose dark:prose-invert max-w-none text-sm text-gray-700 dark:text-gray-300 bg-orange-50/30 dark:bg-orange-900/10 p-4 rounded-xl border border-orange-100/50 dark:border-orange-800/30">
                                {!! $perijinan->persyaratan !!}
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <i class="mdi mdi-ray-start-arrow text-green-500"></i> Prosedur
                            </h3>
                            <div class="prose dark:prose-invert max-w-none text-sm text-gray-700 dark:text-gray-300">
                                {!! $perijinan->prosedur !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Isian Formulir -->
        <div id="tab-formulir" class="tab-content hidden space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="mdi mdi-file-document-edit text-indigo-500"></i>
                        Field Formulir Permohonan
                    </h2>
                    <a href="{{ route('perijinan.form-builder', $perijinan->id) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1">
                        <i class="mdi mdi-cog"></i> Kelola Form
                    </a>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Field</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-center">Wajib</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Konfigurasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($perijinan->activeFormFields as $field)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $field->label }}</div>
                                            <div class="text-[10px] font-mono text-gray-400 mt-0.5">name: {{ $field->name }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400">
                                                {{ $field->type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($field->is_required)
                                                <i class="mdi mdi-check-circle text-green-500 text-lg"></i>
                                            @else
                                                <i class="mdi mdi-minus text-gray-300"></i>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                                            @if($field->type == 'file')
                                                <div class="flex flex-col gap-1">
                                                    <span>Format: {{ $field->file_types ?: '*' }}</span>
                                                    <span>Max: {{ $field->max_file_size ? ($field->max_file_size/1024).'MB' : 'Default' }}</span>
                                                </div>
                                            @elseif(in_array($field->type, ['select', 'radio', 'checkbox']) && $field->options)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($field->options as $option)
                                                        <span class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">{{ $option }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <i class="mdi mdi-file-document-outline text-4xl text-gray-300 mb-2"></i>
                                                <p class="text-sm text-gray-500 italic">Belum ada field formulir yang dikonfigurasi.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Alur Validasi -->
        <div id="tab-alur" class="tab-content hidden space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="mdi mdi-sitemap text-orange-500"></i>
                        Tahapan Alur Validasi
                    </h2>
                    <a href="{{ route('perijinan.alur-validasi', $perijinan->id) }}" class="text-xs font-bold text-orange-600 hover:text-orange-700 dark:text-orange-400 flex items-center gap-1">
                        <i class="mdi mdi-cog"></i> Kelola Alur
                    </a>
                </div>
                <div class="p-6">
                    <div class="relative">
                        <!-- Vertical Line -->
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-100 dark:bg-gray-700"></div>

                        <div class="space-y-8 relative">
                            @forelse($perijinan->validationFlows as $flow)
                                <div class="flex items-start gap-6 group">
                                    <div class="relative z-10">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                            <span class="text-white font-bold text-sm">{{ $flow->order }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 group-hover:border-orange-200 dark:group-hover:border-orange-800 transition-colors">
                                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                                    {{ $flow->role_label }}
                                                    @if(!$flow->is_active)
                                                        <span class="text-[10px] bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-1.5 py-0.5 rounded font-normal uppercase">Nonaktif</span>
                                                    @endif
                                                </h4>
                                                @if($flow->assignedUser)
                                                    <div class="mt-1 flex items-center gap-2">
                                                        <div class="w-5 h-5 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                                            <i class="mdi mdi-account text-[10px] text-orange-600 dark:text-orange-400"></i>
                                                        </div>
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $flow->assignedUser->name }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-4 text-xs">
                                                @if($flow->sla_hours)
                                                    <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 px-2 py-1 rounded-lg border border-gray-100 dark:border-gray-700">
                                                        <i class="mdi mdi-clock-outline text-orange-500"></i>
                                                        SLA: {{ $flow->sla_hours }} jam
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if($flow->description)
                                            <div class="mt-3 text-xs text-gray-500 dark:text-gray-400 italic leading-relaxed">
                                                "{{ $flow->description }}"
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="flex flex-col items-center py-12">
                                    <div class="w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center mb-4">
                                        <i class="mdi mdi-sitemap text-3xl text-gray-300 dark:text-gray-600"></i>
                                    </div>
                                    <p class="text-sm text-gray-500 italic">Belum ada alur validasi yang dikonfigurasi.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <script>
        function switchTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active-tab-btn');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected content
            document.getElementById(tabId).classList.remove('hidden');

            // Set active button
            const activeBtn = document.getElementById('btn-' + tabId);
            activeBtn.classList.add('active-tab-btn');
            activeBtn.classList.remove('border-transparent', 'text-gray-500');

            // Save to localStorage
            localStorage.setItem('perijinan_detail_tab', tabId);
        }

        // Handle initial tab
        document.addEventListener('DOMContentLoaded', () => {
            let initialTab = '{{ session("active_tab") ? "tab-" . session("active_tab") : "" }}';
            if (!initialTab) {
                initialTab = localStorage.getItem('perijinan_detail_tab') || 'tab-umum';
            }
            if (initialTab && document.getElementById(initialTab)) {
                switchTab(initialTab);
            }
        });
    </script>
</x-layout>
