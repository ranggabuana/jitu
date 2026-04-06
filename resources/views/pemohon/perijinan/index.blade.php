<x-pemohon.layout>
    <x-slot:title>Daftar Perizinan - JITU Banjarnegara</x-slot:title>

    <!-- Navbar -->
    <x-pemohon.navbar></x-pemohon.navbar>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Header Section -->
        <div class="bg-gradient-to-br from-amber-600 via-amber-700 to-amber-800 rounded-3xl shadow-xl p-8 text-white">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('pemohon.dashboard') }}" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold mb-2">Daftar Layanan Perizinan</h1>
                    <p class="text-amber-100">Pilih jenis perizinan yang ingin Anda ajukan</p>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <form action="{{ route('pemohon.perijinan') }}" method="GET" class="max-w-2xl mx-auto">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari layanan perizinan..."
                    class="w-full px-6 py-4 pl-14 rounded-2xl text-gray-800 shadow-md focus:ring-4 focus:ring-amber-400 focus:outline-none text-lg border border-gray-200">
                <i class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl"></i>
                @if (request('search'))
                    <a href="{{ route('pemohon.perijinan') }}"
                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-lg"></i>
                    </a>
                @else
                    <button type="submit"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-amber-600 hover:bg-amber-700 text-white px-6 py-2 rounded-xl font-semibold transition-colors">
                        Cari
                    </button>
                @endif
            </div>
        </form>
        @if (request('search'))
            <div class="text-center mt-4">
                <p class="text-gray-600">
                    Menampilkan hasil pencarian untuk: <strong class="text-amber-600">"{{ request('search') }}"</strong>
                    <span class="text-gray-400">({{ $perijinans->total() }} hasil)</span>
                </p>
            </div>
        @endif

        <!-- Perijinan Grid -->
        @if ($perijinans->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($perijinans as $item)
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-amber-100 group cursor-pointer"
                        onclick="openDetailModal({{ $item->id }})">
                        <div class="p-6">
                            <!-- Icon Header -->
                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-amber-500 to-amber-700 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <i class="fas fa-file-signature text-white text-2xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3
                                        class="text-lg font-bold text-gray-800 group-hover:text-amber-600 transition-colors line-clamp-2">
                                        {{ $item->nama_perijinan }}
                                    </h3>
                                </div>
                            </div>

                            <!-- Description -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ Str::limit(strip_tags($item->dasar_hukum ?? 'Tidak ada deskripsi'), 120) }}
                            </p>

                            <!-- Footer -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-clock mr-1"></i> Proses Online
                                </span>
                                <button onclick="openDetailModal({{ $item->id }})"
                                    class="inline-flex items-center gap-2 text-amber-600 font-semibold text-sm hover:gap-3 transition-all">
                                    Detail <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                {{ $perijinans->links('pagination::tailwind') }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-amber-200">
                @if (request('search'))
                    <div class="w-24 h-24 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search text-amber-400 text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Layanan Tidak Ditemukan</h3>
                    <p class="text-gray-500 mb-6">
                        Tidak ada layanan yang cocok dengan pencarian <strong>"{{ request('search') }}"</strong>
                    </p>
                    <a href="{{ route('pemohon.perijinan') }}"
                        class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-arrow-left"></i> Lihat Semua Layanan
                    </a>
                @else
                    <div class="w-24 h-24 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-folder-open text-amber-400 text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Layanan</h3>
                    <p class="text-gray-500">Layanan perizinan akan segera ditambahkan</p>
                @endif
            </div>
        @endif
    </main>

    <!-- Detail Modal -->
    <div id="detailModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="bg-gradient-to-br from-amber-600 via-amber-700 to-amber-800 text-white p-6 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-file-signature text-2xl"></i>
                        </div>
                        <div>
                            <h2 id="modalTitle" class="text-2xl font-bold">Detail Perizinan</h2>
                            <p id="modalSubtitle" class="text-amber-100 text-sm">Informasi lengkap perizinan</p>
                        </div>
                    </div>
                    <button onclick="closeDetailModal()"
                        class="text-white/80 hover:text-white transition-colors bg-white/10 hover:bg-white/20 rounded-full p-2">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div id="modalContent" class="flex-1 overflow-y-auto p-0">
                <!-- Loading State -->
                <div class="flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-amber-600"></div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 border-t border-gray-200 bg-gray-50 flex-shrink-0">
                <div class="flex justify-end gap-3">
                    <button onclick="closeDetailModal()"
                        class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition-colors">
                        Tutup
                    </button>
                    <button onclick="submitPengajuan()"
                        class="px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-semibold transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        Ajukan Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <x-pemohon.footer></x-pemohon.footer>

    <!-- Scripts -->
    <script>
        let currentPerijinan = null;

        function openDetailModal(id) {
            const modal = document.getElementById('detailModal');
            const modalContent = document.getElementById('modalContent');
            const modalTitle = document.getElementById('modalTitle');

            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Fetch detail data
            fetch(`{{ route('pemohon.perijinan.detail', '__ID__') }}`.replace('__ID__', id))
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (!data.success) {
                        throw new Error(data.message || 'Gagal memuat detail perizinan');
                    }

                    currentPerijinan = data.data;

                    // Update modal title
                    modalTitle.textContent = currentPerijinan.nama_perijinan;
                    document.getElementById('modalSubtitle').textContent = 'Informasi lengkap perizinan';

                    // Build modal content with tabs
                    buildModalContent(currentPerijinan);
                })
                .catch(error => {
                    console.error('Error:', error);
                    let errorMessage = error.message || 'Terjadi kesalahan tidak diketahui';
                    modalContent.innerHTML = `
                        <div class="text-center py-12 px-6">
                            <i class="fas fa-exclamation-triangle text-amber-500 text-5xl mb-4"></i>
                            <p class="text-gray-600 font-semibold">Terjadi kesalahan saat memuat detail</p>
                            <p class="text-gray-400 text-sm mt-2">${errorMessage}</p>
                            <p class="text-red-400 text-xs mt-4">Periksa console browser (F12) untuk detail error</p>
                        </div>
                    `;
                });
        }

        function buildModalContent(perijinan) {
            const modalContent = document.getElementById('modalContent');

            const activeFormFieldsCount = perijinan.active_form_fields?.length || 0;
            const activeValidationFlowsCount = perijinan.active_validation_flows?.length || 0;

            modalContent.innerHTML = `
                <div class="max-w-4xl mx-auto">
                    <!-- Quick Stats -->

                    <!-- Tab Navigation -->
                    <div class="bg-white border-b border-gray-200 overflow-x-auto px-6 mt-4">
                        <div class="flex" id="tabs-nav">
                            <button onclick="switchModalTab('dasar-hukum')" data-tab="dasar-hukum"
                                class="tab-btn flex items-center gap-2 px-6 py-4 font-semibold text-sm whitespace-nowrap transition-all border-b-2 border-amber-600 text-amber-600 bg-amber-50"
                                role="tab" aria-selected="true">
                                <i class="fas fa-balance-scale"></i>
                                <span>Dasar Hukum</span>
                            </button>
                            <button onclick="switchModalTab('persyaratan')" data-tab="persyaratan"
                                class="tab-btn flex items-center gap-2 px-6 py-4 font-semibold text-sm whitespace-nowrap transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                                role="tab" aria-selected="false">
                                <i class="fas fa-clipboard-check"></i>
                                <span>Persyaratan</span>
                            </button>
                            <button onclick="switchModalTab('prosedur')" data-tab="prosedur"
                                class="tab-btn flex items-center gap-2 px-6 py-4 font-semibold text-sm whitespace-nowrap transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                                role="tab" aria-selected="false">
                                <i class="fas fa-list-ol"></i>
                                <span>Prosedur</span>
                            </button>
                            <button onclick="switchModalTab('informasi-biaya')" data-tab="informasi-biaya"
                                class="tab-btn flex items-center gap-2 px-6 py-4 font-semibold text-sm whitespace-nowrap transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                                role="tab" aria-selected="false">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Informasi Biaya</span>
                            </button>
                            <button onclick="switchModalTab('gambar-alur')" data-tab="gambar-alur"
                                class="tab-btn flex items-center gap-2 px-6 py-4 font-semibold text-sm whitespace-nowrap transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                                role="tab" aria-selected="false">
                                <i class="fas fa-sitemap"></i>
                                <span>Gambar Alur</span>
                            </button>
                            <button onclick="switchModalTab('formulir')" data-tab="formulir"
                                class="tab-btn flex items-center gap-2 px-6 py-4 font-semibold text-sm whitespace-nowrap transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                                role="tab" aria-selected="false">
                                <i class="fas fa-file-contract"></i>
                                <span>Formulir</span>
                            </button>
                            <button onclick="switchModalTab('alur-validasi')" data-tab="alur-validasi"
                                class="tab-btn flex items-center gap-2 px-6 py-4 font-semibold text-sm whitespace-nowrap transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                                role="tab" aria-selected="false">
                                <i class="fas fa-project-diagram"></i>
                                <span>Alur Validasi</span>
                            </button>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6">
                        <!-- Dasar Hukum Tab -->
                        <div id="dasar-hukum" class="tab-content block" role="tabpanel">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-balance-scale text-white text-lg"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">Dasar Hukum</h2>
                            </div>
                            <div class="text-gray-700 leading-relaxed prose prose-sm max-w-none">
                                ${perijinan.dasar_hukum ? perijinan.dasar_hukum : `
                                                    <div class="text-center py-8">
                                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                            <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                                                        </div>
                                                        <p class="text-gray-400 italic">Dasar hukum belum tersedia</p>
                                                    </div>
                                                `}
                            </div>
                        </div>

                        <!-- Persyaratan Tab -->
                        <div id="persyaratan" class="tab-content hidden" role="tabpanel">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-clipboard-check text-white text-lg"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">Persyaratan</h2>
                            </div>
                            <div class="text-gray-700 leading-relaxed prose prose-sm max-w-none">
                                ${perijinan.persyaratan ? perijinan.persyaratan : `
                                                    <div class="text-center py-8">
                                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                            <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                                                        </div>
                                                        <p class="text-gray-400 italic">Persyaratan belum tersedia</p>
                                                    </div>
                                                `}
                            </div>
                        </div>

                        <!-- Prosedur Tab -->
                        <div id="prosedur" class="tab-content hidden" role="tabpanel">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-list-ol text-white text-lg"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">Prosedur</h2>
                            </div>
                            <div class="text-gray-700 leading-relaxed prose prose-sm max-w-none">
                                ${perijinan.prosedur ? perijinan.prosedur : `
                                                    <div class="text-center py-8">
                                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                            <i class="fas fa-tasks text-gray-400 text-2xl"></i>
                                                        </div>
                                                        <p class="text-gray-400 italic">Prosedur belum tersedia</p>
                                                    </div>
                                                `}
                            </div>
                        </div>

                        <!-- Informasi Biaya Tab -->
                        <div id="informasi-biaya" class="tab-content hidden" role="tabpanel">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-money-bill-wave text-white text-lg"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">Informasi Biaya</h2>
                            </div>
                            ${perijinan.informasi_biaya ? `
                                                <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6">
                                                    <div class="flex items-start gap-4">
                                                        <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                                            <i class="fas fa-tag text-green-600 text-2xl"></i>
                                                        </div>
                                                        <div class="flex-1">
                                                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Biaya Perizinan</h3>
                                                            <p class="text-gray-700 leading-relaxed">${perijinan.informasi_biaya}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            ` : `
                                                <div class="text-center py-8">
                                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                        <i class="fas fa-money-bill-wave text-gray-400 text-2xl"></i>
                                                    </div>
                                                    <p class="text-gray-400 italic">Informasi biaya belum tersedia</p>
                                                </div>
                                            `}
                        </div>

                        <!-- Gambar Alur Tab -->
                        <div id="gambar-alur" class="tab-content hidden" role="tabpanel">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-sitemap text-white text-lg"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">Gambar Alur</h2>
                            </div>
                            ${perijinan.gambar_alur ? `
                                                <div class="flex justify-center">
                                                    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm max-w-4xl">
                                                        <img src="{{ url('') }}/${perijinan.gambar_alur}" alt="Gambar Alur" class="w-full h-auto rounded-lg">
                                                    </div>
                                                </div>
                                            ` : `
                                                <div class="text-center py-8">
                                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                                                    </div>
                                                    <p class="text-gray-400 italic">Gambar alur belum tersedia</p>
                                                </div>
                                            `}
                        </div>

                        <!-- Formulir Tab -->
                        <div id="formulir" class="tab-content hidden" role="tabpanel">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-700 rounded-xl flex items-center justify-center shadow-md">
                                        <i class="fas fa-file-contract text-white text-lg"></i>
                                    </div>
                                    <h2 class="text-xl font-bold text-gray-800">Formulir</h2>
                                </div>
                                <span class="bg-orange-100 text-orange-600 text-sm font-bold px-3 py-1 rounded-full">
                                    ${activeFormFieldsCount} Field
                                </span>
                            </div>
                            ${activeFormFieldsCount > 0 ? `
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    ${perijinan.active_form_fields.map(field => `
                                        <div class="flex items-start gap-3 p-4 rounded-xl border border-gray-100 hover:border-orange-300 hover:bg-orange-50/50 transition-all">
                                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                ${getFieldIcon(field.type)}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="font-semibold text-gray-800 text-sm truncate">${field.label}</h4>
                                                    ${field.is_required ? `<span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">Wajib</span>` : ''}
                                                </div>
                                                <p class="text-xs text-gray-500 capitalize">${field.type.replace('_', ' ')}</p>
                                                ${field.help_text ? `<p class="text-xs text-gray-600 mt-1">${field.help_text}</p>` : ''}
                                            </div>
                                        </div>
                                    `).join('')}
                                                </div>
                                            ` : `
                                                <div class="text-center py-8">
                                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                        <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                                                    </div>
                                                    <p class="text-gray-400 italic">Formulir belum tersedia</p>
                                                </div>
                                            `}
                        </div>

                        <!-- Alur Validasi Tab -->
                        <div id="alur-validasi" class="tab-content hidden" role="tabpanel">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl flex items-center justify-center shadow-md">
                                        <i class="fas fa-project-diagram text-white text-lg"></i>
                                    </div>
                                    <h2 class="text-xl font-bold text-gray-800">Alur Validasi</h2>
                                </div>
                                <span class="bg-indigo-100 text-indigo-600 text-sm font-bold px-3 py-1 rounded-full">
                                    ${activeValidationFlowsCount} Tahap
                                </span>
                            </div>
                            ${activeValidationFlowsCount > 0 ? `
                                                <div class="relative">
                                                    <!-- Timeline Line -->
                                                    <div class="absolute left-6 top-4 bottom-4 w-0.5 bg-gradient-to-b from-indigo-400 via-purple-400 to-pink-400 rounded-full"></div>

                                                    <div class="space-y-4">
                                                        ${perijinan.active_validation_flows.map((flow, index) => `
                                            <div class="relative flex items-start gap-4 pl-2">
                                                <!-- Timeline Dot -->
                                                <div class="relative z-10 w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg flex-shrink-0 border-2 border-white">
                                                    <span class="text-white font-bold text-sm">${index + 1}</span>
                                                </div>

                                                <!-- Content -->
                                                <div class="flex-1 bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 border border-gray-100 hover:border-indigo-200 hover:shadow-sm transition-all">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <h3 class="font-bold text-gray-800">${flow.role_label || flow.role || 'Validator'}</h3>
                                                        ${flow.sla_hours ? `
                                                                            <span class="bg-indigo-100 text-indigo-600 text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap">
                                                                                <i class="fas fa-clock mr-1"></i>${flow.sla_hours}j
                                                                            </span>
                                                                        ` : ''}
                                                    </div>
                                                    ${flow.description ? `<p class="text-gray-600 text-sm mb-2">${flow.description}</p>` : ''}
                                                    ${flow.assigned_user_id && flow.assigned_user ? `
                                                            <div class="mt-2 flex items-center gap-2 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-2 border border-indigo-200">
                                                                <div class="w-6 h-6 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                                                                    <i class="fas fa-user-check text-white text-xs"></i>
                                                                </div>
                                                                <div class="flex-1">
                                                                    <p class="text-xs font-semibold text-gray-800">
                                                                        <i class="fas fa-user-tie mr-1"></i>
                                                                        ${flow.assigned_user.name}
                                                                    </p>
                                                                    <p class="text-xs text-gray-500">${flow.assigned_user.role_label || 'Validator'}</p>
                                                                </div>
                                                            </div>
                                                        ` : ''}
                                                    ${!flow.assigned_user_id && ['fo', 'bo', 'verifikator', 'kadin'].includes(flow.role) ? `
                                                            <div class="mt-2 flex items-center gap-2 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-2 border border-blue-200">
                                                                <div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center flex-shrink-0">
                                                                    <i class="fas fa-users text-white text-xs"></i>
                                                                </div>
                                                                <p class="text-xs font-semibold text-gray-800">
                                                                    <i class="fas fa-user-check mr-1"></i>
                                                                    Divalidasi oleh ${flow.role_label || 'Validator'}
                                                                </p>
                                                            </div>
                                                        ` : ''}
                                                </div>
                                            </div>
                                        `).join('')}
                                                    </div>
                                                </div>
                                            ` : `
                                                <div class="text-center py-8">
                                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                        <i class="fas fa-sitemap text-gray-400 text-2xl"></i>
                                                    </div>
                                                    <p class="text-gray-400 italic">Alur validasi belum tersedia</p>
                                                </div>
                                            `}
                        </div>
                    </div>
                </div>
            `;
        }

        function getFieldIcon(type) {
            const icons = {
                'file': '<i class="fas fa-file-upload text-orange-600"></i>',
                'textarea': '<i class="fas fa-paragraph text-orange-600"></i>',
                'text': '<i class="fas fa-font text-orange-600"></i>',
                'email': '<i class="fas fa-envelope text-orange-600"></i>',
                'number': '<i class="fas fa-hashtag text-orange-600"></i>',
                'date': '<i class="fas fa-calendar text-orange-600"></i>',
                'select': '<i class="fas fa-chevron-down text-orange-600"></i>',
                'radio': '<i class="fas fa-dot-circle text-orange-600"></i>',
                'checkbox': '<i class="fas fa-check-square text-orange-600"></i>',
            };
            return icons[type] || '<i class="fas fa-book text-orange-600"></i>';
        }

        function switchModalTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('#modalContent .tab-content').forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('block');
            });

            // Remove active state from all buttons
            document.querySelectorAll('#tabs-nav .tab-btn').forEach(btn => {
                btn.classList.remove('border-amber-600', 'text-amber-600', 'bg-amber-50');
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
            const selectedBtn = document.querySelector(`#tabs-nav [data-tab="${tabId}"]`);
            if (selectedBtn) {
                selectedBtn.classList.remove('border-transparent', 'text-gray-500');
                selectedBtn.classList.add('border-amber-600', 'text-amber-600', 'bg-amber-50');
                selectedBtn.setAttribute('aria-selected', 'true');
            }
        }

        function submitPengajuan() {
            if (currentPerijinan && currentPerijinan.id) {
                // Redirect to pengajuan form page
                window.location.href = `{{ route('pemohon.pengajuan.create', '__ID__') }}`.replace('__ID__',
                    currentPerijinan.id);
            } else {
                alert('Silakan pilih perizinan yang ingin diajukan');
            }
        }

        function closeDetailModal() {
            const modal = document.getElementById('detailModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal on outside click
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDetailModal();
            }
        });
    </script>
</x-pemohon.layout>
