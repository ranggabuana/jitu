<x-front-layout>

    <!-- Hero Header -->
    <section class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900 text-white py-20 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div
                class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2">
            </div>
            <div
                class="absolute bottom-0 left-0 w-72 h-72 bg-blue-300 rounded-full blur-3xl transform -translate-x-1/2 translate-y-1/2">
            </div>
        </div>

        <!-- Floating Icons -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <i class="fas fa-file-signature absolute top-10 right-20 text-8xl text-white/5"></i>
            <i class="fas fa-stamp absolute bottom-10 left-20 text-6xl text-white/5"></i>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-8">
                <!-- Content Left -->
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-6 flex-wrap">
                        <span
                            class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-semibold border border-white/20">
                            <i class="fas fa-concierge-bell text-yellow-400"></i>
                            Layanan Perijinan
                        </span>
                        <span
                            class="inline-flex items-center gap-2 bg-green-500/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-semibold border border-green-400/30">
                            <i class="fas fa-check-circle text-green-400"></i>
                            Tersedia Online
                        </span>
                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        {{ $layanan->nama_perijinan }}
                    </h1>

                    <p class="text-xl text-blue-100 max-w-2xl mb-8 leading-relaxed">
                        Proses perijinan Anda menjadi lebih mudah, cepat, dan transparan dengan sistem online terpadu
                    </p>

                    <!-- Quick Stats -->
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm px-5 py-3 rounded-xl">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-layer-group text-blue-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-blue-200">Tahap Validasi</p>
                                <p class="font-bold">{{ $layanan->activeValidationFlows->count() }} Tahap</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm px-5 py-3 rounded-xl">
                            <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-alt text-purple-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-blue-200">Isian Formulir</p>
                                <p class="font-bold">
                                    {{ $layanan->activeFormFields->where('is_required', true)->count() }} Item
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Right -->
                <div class="lg:flex-shrink-0">
                    <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 lg:w-80">
                        <div class="text-center mb-4">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                                <i class="fas fa-rocket text-white text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-bold">Siap Mengajukan?</h3>
                            <p class="text-sm text-blue-100 mt-1">Mulai proses perijinan sekarang</p>
                        </div>
                        <button onclick="openInfoModal()"
                            class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-blue-900 font-bold py-4 px-6 rounded-xl transition-all shadow-lg hover:shadow-xl hover:scale-105">
                            <i class="fas fa-edit"></i>
                            <span>Ajukan Perijinan</span>
                        </button>
                        <p class="text-xs text-blue-200 text-center mt-3">
                            <i class="fas fa-check-circle mr-1"></i> Proses cepat & mudah
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content with Tabs -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Tab Navigation -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="flex overflow-x-auto" id="tabs-nav">
                    <button onclick="switchTab('dasar-hukum')" data-tab="dasar-hukum"
                        class="tab-btn flex items-center gap-2 px-6 py-4 font-semibold text-sm whitespace-nowrap transition-all border-b-2 border-blue-600 text-blue-600 bg-blue-50"
                        role="tab" aria-selected="true">
                        <i class="fas fa-balance-scale"></i>
                        <span>Dasar Hukum</span>
                    </button>
                    <button onclick="switchTab('persyaratan')" data-tab="persyaratan"
                        class="tab-btn flex items-center gap-2 px-6 py-4 font-semibold text-sm whitespace-nowrap transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                        role="tab" aria-selected="false">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Persyaratan</span>
                    </button>
                    <button onclick="switchTab('prosedur')" data-tab="prosedur"
                        class="tab-btn flex items-center gap-2 px-6 py-4 font-semibold text-sm whitespace-nowrap transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                        role="tab" aria-selected="false">
                        <i class="fas fa-list-ol"></i>
                        <span>Prosedur</span>
                    </button>
                    <button onclick="switchTab('informasi-biaya')" data-tab="informasi-biaya"
                        class="tab-btn flex items-center gap-2 px-6 py-4 font-semibold text-sm whitespace-nowrap transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                        role="tab" aria-selected="false">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Informasi Biaya</span>
                    </button>
                    <button onclick="switchTab('gambar-alur')" data-tab="gambar-alur"
                        class="tab-btn flex items-center gap-2 px-6 py-4 font-semibold text-sm whitespace-nowrap transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                        role="tab" aria-selected="false">
                        <i class="fas fa-sitemap"></i>
                        <span>Gambar Alur</span>
                    </button>
                    <button onclick="switchTab('formulir')" data-tab="formulir"
                        class="tab-btn flex items-center gap-2 px-6 py-4 font-semibold text-sm whitespace-nowrap transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                        role="tab" aria-selected="false">
                        <i class="fas fa-file-contract"></i>
                        <span>Formulir</span>
                    </button>
                    <button onclick="switchTab('alur-validasi')" data-tab="alur-validasi"
                        class="tab-btn flex items-center gap-2 px-6 py-4 font-semibold text-sm whitespace-nowrap transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                        role="tab" aria-selected="false">
                        <i class="fas fa-project-diagram"></i>
                        <span>Alur Validasi</span>
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <!-- Dasar Hukum Tab -->
                <div id="dasar-hukum" class="tab-content p-6 block" role="tabpanel">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-balance-scale text-white text-lg"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Dasar Hukum</h2>
                    </div>
                    <div class="text-gray-700 leading-relaxed prose prose-sm max-w-none">
                        @if ($layanan->dasar_hukum)
                            {!! $layanan->dasar_hukum !!}
                        @else
                            <div class="text-center py-8">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-400 italic">Dasar hukum belum tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Persyaratan Tab -->
                <div id="persyaratan" class="tab-content p-6 hidden" role="tabpanel">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-clipboard-check text-white text-lg"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Persyaratan</h2>
                    </div>
                    <div class="text-gray-700 leading-relaxed prose prose-sm max-w-none">
                        @if ($layanan->persyaratan)
                            {!! $layanan->persyaratan !!}
                        @else
                            <div class="text-center py-8">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-400 italic">Persyaratan belum tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Prosedur Tab -->
                <div id="prosedur" class="tab-content p-6 hidden" role="tabpanel">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-list-ol text-white text-lg"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Prosedur</h2>
                    </div>
                    <div class="text-gray-700 leading-relaxed prose prose-sm max-w-none">
                        @if ($layanan->prosedur)
                            {!! $layanan->prosedur !!}
                        @else
                            <div class="text-center py-8">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-tasks text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-400 italic">Prosedur belum tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informasi Biaya Tab -->
                <div id="informasi-biaya" class="tab-content p-6 hidden" role="tabpanel">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-money-bill-wave text-white text-lg"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Informasi Biaya</h2>
                    </div>
                    @if ($layanan->informasi_biaya)
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-tag text-green-600 text-2xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Biaya Perijinan</h3>
                                    <p class="text-gray-700 leading-relaxed">{{ $layanan->informasi_biaya }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-money-bill-wave text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-400 italic">Informasi biaya belum tersedia</p>
                        </div>
                    @endif
                </div>

                <!-- Gambar Alur Tab -->
                <div id="gambar-alur" class="tab-content p-6 hidden" role="tabpanel">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-sitemap text-white text-lg"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Gambar Alur</h2>
                    </div>
                    @if ($layanan->gambar_alur && file_exists(public_path($layanan->gambar_alur)))
                        <div class="flex justify-center">
                            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm max-w-4xl">
                                <img src="{{ asset($layanan->gambar_alur) }}" alt="Gambar Alur" class="w-full h-auto rounded-lg">
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-400 italic">Gambar alur belum tersedia</p>
                        </div>
                    @endif
                </div>

                <!-- Formulir Tab -->
                <div id="formulir" class="tab-content p-6 hidden" role="tabpanel">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-700 rounded-xl flex items-center justify-center shadow-md">
                                <i class="fas fa-file-contract text-white text-lg"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Formulir</h2>
                        </div>
                        <span class="bg-orange-100 text-orange-600 text-sm font-bold px-3 py-1 rounded-full">
                            {{ $layanan->activeFormFields->count() }} Field
                        </span>
                    </div>
                    @if ($layanan->activeFormFields->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($layanan->activeFormFields as $field)
                                <div
                                    class="flex items-start gap-3 p-4 rounded-xl border border-gray-100 hover:border-orange-300 hover:bg-orange-50/50 transition-all">
                                    <div
                                        class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        @if ($field->type === 'file')
                                            <i class="fas fa-file-upload text-orange-600"></i>
                                        @elseif($field->type === 'textarea')
                                            <i class="fas fa-paragraph text-orange-600"></i>
                                        @elseif($field->type === 'text')
                                            <i class="fas fa-font text-orange-600"></i>
                                        @elseif($field->type === 'email')
                                            <i class="fas fa-envelope text-orange-600"></i>
                                        @elseif($field->type === 'number')
                                            <i class="fas fa-hashtag text-orange-600"></i>
                                        @elseif($field->type === 'date')
                                            <i class="fas fa-calendar text-orange-600"></i>
                                        @elseif($field->type === 'select')
                                            <i class="fas fa-chevron-down text-orange-600"></i>
                                        @else
                                            <i class="fas fa-book text-orange-600"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-semibold text-gray-800 text-sm truncate">
                                                {{ $field->label }}</h4>
                                            @if ($field->is_required)
                                                <span
                                                    class="bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">Wajib</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 capitalize">
                                            {{ str_replace('_', ' ', $field->type) }}</p>
                                        @if ($field->help_text)
                                            <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $field->help_text }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-400 italic">Formulir belum tersedia</p>
                        </div>
                    @endif
                </div>

                <!-- Alur Validasi Tab -->
                <div id="alur-validasi" class="tab-content p-6 hidden" role="tabpanel">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl flex items-center justify-center shadow-md">
                                <i class="fas fa-project-diagram text-white text-lg"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Alur Validasi</h2>
                        </div>
                        <span class="bg-indigo-100 text-indigo-600 text-sm font-bold px-3 py-1 rounded-full">
                            {{ $layanan->activeValidationFlows->count() }} Tahap
                        </span>
                    </div>
                    @if ($layanan->activeValidationFlows->count() > 0)
                        <div class="relative">
                            <!-- Timeline Line -->
                            <div
                                class="absolute left-6 top-4 bottom-4 w-0.5 bg-gradient-to-b from-indigo-400 via-purple-400 to-pink-400 rounded-full">
                            </div>

                            <div class="space-y-4">
                                @foreach ($layanan->activeValidationFlows as $index => $flow)
                                    <div class="relative flex items-start gap-4 pl-2">
                                        <!-- Timeline Dot -->
                                        <div
                                            class="relative z-10 w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg flex-shrink-0 border-2 border-white">
                                            <span class="text-white font-bold text-sm">{{ $index + 1 }}</span>
                                        </div>

                                        <!-- Content -->
                                        <div
                                            class="flex-1 bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 border border-gray-100 hover:border-indigo-200 hover:shadow-sm transition-all">
                                            <div class="flex items-center justify-between mb-2">
                                                <h3 class="font-bold text-gray-800">{{ $flow->role_label }}</h3>
                                                @if ($flow->sla_hours)
                                                    <span
                                                        class="bg-indigo-100 text-indigo-600 text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap">
                                                        <i class="fas fa-clock mr-1"></i>{{ $flow->sla_hours }}j
                                                    </span>
                                                @endif
                                            </div>
                                            @if ($flow->description)
                                                <p class="text-gray-600 text-sm mb-2">{{ $flow->description }}</p>
                                            @endif
                                            @if ($flow->assignedUser)
                                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                                    <i class="fas fa-user-circle text-indigo-500"></i>
                                                    <span>{{ $flow->assignedUser->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-sitemap text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-400 italic">Alur validasi belum tersedia</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>

    <script>
        function switchTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('block');
            });

            // Remove active state from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-blue-600', 'text-blue-600', 'bg-blue-50');
                btn.classList.add('border-transparent', 'text-gray-500');
                btn.setAttribute('aria-selected', 'false');
            });

            // Show selected tab content
            const selectedContent = document.getElementById(tabId);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
                selectedContent.classList.add('block');
            }

            // Set active state on clicked button
            const selectedBtn = document.querySelector(`[data-tab="${tabId}"]`);
            if (selectedBtn) {
                selectedBtn.classList.remove('border-transparent', 'text-gray-500');
                selectedBtn.classList.add('border-blue-600', 'text-blue-600', 'bg-blue-50');
                selectedBtn.setAttribute('aria-selected', 'true');
            }
        }
    </script>

    <!-- Info Modal -->
    <div id="infoModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4"
        onclick="closeInfoModal(event)">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all"
            onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div
                class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between sticky top-0">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-info-circle text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold">Informasi Pengajuan Perijinan</h3>
                </div>
                <button onclick="closeInfoModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-6">
                <!-- Step 1: Akun -->
                <div class="flex items-start gap-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                        <span class="text-white font-bold text-lg">1</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <i class="fas fa-user-circle text-blue-600"></i>
                            Memiliki Akun Pemohon
                        </h4>
                        <p class="text-gray-600 text-sm leading-relaxed mb-3">
                            Anda harus memiliki akun pemohon untuk mengajukan perijinan. Jika belum memiliki akun,
                            silakan mendaftar terlebih dahulu dengan memilih jenis akun <strong>Perorangan</strong>
                            atau <strong>Badan Usaha</strong>.
                        </p>

                        <!-- Lupa Akun Info -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-start gap-3">
                            <i class="fab fa-whatsapp text-green-600 text-xl mt-0.5"></i>
                            <div class="text-sm text-green-800">
                                <p class="font-semibold mb-1">Sudah Punya Akun tapi Lupa Password?</p>
                                <p class="text-green-700">
                                    Jika Anda sudah pernah mendaftar namun lupa akun atau password,
                                    silakan hubungi admin melalui WhatsApp di
                                    <a href="https://wa.me/{{ formatWhatsAppNumber(setting('whatsapp', '081234567890')) }}"
                                        target="_blank"
                                        class="font-semibold text-green-700 hover:text-green-900 underline">
                                        <i class="fab fa-whatsapp"></i>
                                        {{ setting('whatsapp', '0812-3456-7890') }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Login -->
                <div class="flex items-start gap-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                        <span class="text-white font-bold text-lg">2</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <i class="fas fa-door-open text-green-600"></i>
                            Login ke Dashboard
                        </h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Setelah memiliki akun, login ke dashboard pengguna untuk mengakses fitur pengajuan
                            perijinan.
                            Di dashboard, Anda dapat mengajukan perijinan baru dan mengelola semua pengajuan Anda.
                        </p>
                    </div>
                </div>

                <!-- Step 3: Dashboard Features -->
                <div class="flex items-start gap-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                        <span class="text-white font-bold text-lg">3</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <i class="fas fa-th-large text-purple-600"></i>
                            Dashboard Pengguna
                        </h4>
                        <p class="text-gray-600 text-sm leading-relaxed mb-3">
                            Di dashboard, Anda dapat:
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <span>Mengajukan perijinan baru secara online</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <span>Melihat daftar semua perijinan yang pernah diajukan</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <span>Memantau status dan progres validasi perijinan</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <span>Mendapatkan notifikasi update status melalui email</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <span>Mengunduh berkas perijinan yang sudah ditandatangani</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                    <i class="fas fa-lightbulb text-yellow-500 text-xl mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Tahukah Anda?</p>
                        <p class="text-blue-700">
                            Semua komunikasi dan update terkait perijinan akan dikirimkan ke email yang terdaftar.
                            Pastikan email Anda aktif dan dapat diakses.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div
                class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl flex justify-end gap-3 sticky bottom-0">
                <button onclick="closeInfoModal()"
                    class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                    Tutup
                </button>
                <a href="{{ route('login') }}" target="_blank"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-sign-in-alt"></i>
                    Login Sekarang
                </a>
            </div>
        </div>
    </div>

    <script>
        function openInfoModal() {
            const modal = document.getElementById('infoModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeInfoModal(event) {
            if (event && event.target !== event.currentTarget) return;
            const modal = document.getElementById('infoModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeInfoModal();
            }
        });
    </script>

    <!-- Simple CTA Section -->
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-gray-600 mb-4">
                    Butuh bantuan atau informasi lebih lanjut?
                </p>
                <a href="{{ route('layanan') }}"
                    class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-semibold transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Daftar Layanan</span>
                </a>
            </div>
        </div>
    </section>
</x-front-layout>
