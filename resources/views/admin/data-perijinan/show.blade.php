<x-layout>
    <x-slot:title>Detail {{ $application->no_registrasi }}</x-slot:title>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    <i class="mdi mdi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
                <div>
                    <div class="flex items-center gap-2">
                        <span
                            class="font-mono font-bold text-lg text-gray-800 dark:text-white">{{ $application->no_registrasi }}</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $application->status_color }}">
                            {{ $application->status_label }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $application->perijinan->nama_perijinan }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    <i class="mdi mdi-clock"></i> {{ $application->created_at->format('d M Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Identity Modal -->
    <div id="modal-identity" class="fixed inset-0 z-[400] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                        <i class="mdi mdi-account-details text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white">Identitas Lengkap Pemohon</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">Data Profil & Perusahaan</p>
                    </div>
                </div>
                <button type="button" onclick="closeIdentityModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <i class="mdi mdi-close text-2xl"></i>
                </button>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Info -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest border-b border-blue-100 dark:border-blue-900/30 pb-2">Informasi Pribadi</h4>
                        
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Nama Lengkap</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->name }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">NIK / NIP</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->nip ?? $application->user->nik ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Email</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->email }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">No. Telepon</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->no_hp ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Address & Business -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest border-b border-purple-100 dark:border-purple-900/30 pb-2">Domisili & Bisnis</h4>
                        
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Status Pemohon</label>
                            <div class="mt-1">
                                @if ($application->user->status_pemohon === 'badan_usaha')
                                    <span class="px-2 py-1 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-[10px] font-bold">
                                        <i class="mdi mdi-building mr-1"></i> BADAN USAHA
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-[10px] font-bold">
                                        <i class="mdi mdi-account mr-1"></i> PERORANGAN
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if ($application->user->status_pemohon === 'badan_usaha')
                            <div>
                                <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Nama Perusahaan</label>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->nama_perusahaan ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">NPWP</label>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->npwp ?? '-' }}</p>
                            </div>
                        @endif

                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Alamat Domisili</label>
                            <p class="text-xs font-medium text-gray-600 dark:text-gray-400 leading-relaxed">
                                {{ $application->user->alamat_domisili ?? $application->user->alamat_lengkap ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($application->form_data && count($application->form_data) > 0)
                    <div class="mt-8">
                        <h4 class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest border-b border-emerald-100 dark:border-emerald-900/30 pb-2 mb-4">Data Tambahan Formulir</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            @foreach ($application->perijinan->activeFormFields as $field)
                                @if (isset($application->form_data[$field->id]) && !empty($application->form_data[$field->id]) && $field->type !== 'file')
                                    <div>
                                        <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">{{ $field->label }}</label>
                                        <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                                            @if (is_array($application->form_data[$field->id]))
                                                {{ implode(', ', $application->form_data[$field->id]) }}
                                            @else
                                                {{ $application->form_data[$field->id] }}
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                <button type="button" onclick="closeIdentityModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-xl text-sm font-bold transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openIdentityModal() {
            const modal = document.getElementById('modal-identity');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeIdentityModal() {
            const modal = document.getElementById('modal-identity');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('modal-identity').addEventListener('click', function(e) {
            if (e.target === this) closeIdentityModal();
        });
    </script>
    @endpush

    <!-- Main Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Left: File & Data -->
        <div class="xl:col-span-2 space-y-4">

            <!-- File Uploads - Priority Display -->
            @if ($application->form_files && count($application->form_files) > 0)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-white dark:from-purple-900/20 dark:to-gray-800">
                        <div class="flex items-center justify-between">
                            <h2 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <i class="mdi mdi-paperclip text-purple-600"></i>
                                Lampiran File ({{ collect($application->form_files)->flatten()->count() }})
                            </h2>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ($application->form_files as $fieldId => $files)
                                @php
                                    $field = $application->perijinan->activeFormFields->firstWhere('id', $fieldId);
                                    $fieldName = $field ? $field->label : 'Field #' . $fieldId;
                                    $filesArray = is_array($files) ? $files : [$files];
                                @endphp
                                @foreach ($filesArray as $file)
                                    @if ($file && file_exists(public_path($file)))
                                        @php
                                            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            $isPdf = $extension === 'pdf';
                                            $isExcel = in_array($extension, ['xls', 'xlsx', 'csv']);
                                        @endphp
                                        <div
                                            class="group flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-purple-400 dark:hover:border-purple-600 hover:shadow-md transition-all bg-gray-50 dark:bg-gray-900/50">
                                            <div
                                                class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0
                                                {{ $isImage ? 'bg-green-100 dark:bg-green-900/30' : '' }}
                                                {{ $isPdf ? 'bg-red-100 dark:bg-red-900/30' : '' }}
                                                {{ $isExcel ? 'bg-yellow-100 dark:bg-yellow-900/30' : '' }}
                                                {{ !$isImage && !$isPdf && !$isExcel ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}
                                            ">
                                                @if ($isImage)
                                                    <i class="mdi mdi-image text-green-600 dark:text-green-400"></i>
                                                @elseif($isPdf)
                                                    <i class="mdi mdi-file-pdf-box text-red-600 dark:text-red-400"></i>
                                                @elseif($isExcel)
                                                    <i
                                                        class="mdi mdi-file-excel text-yellow-600 dark:text-yellow-400"></i>
                                                @else
                                                    <i class="mdi mdi-file text-blue-600 dark:text-blue-400"></i>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 dark:text-white truncate">
                                                    {{ basename($file) }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                    {{ $fieldName }}</p>
                                            </div>
                                            <div
                                                class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                @php
                                                    $routePath = str_replace('uploads/perijinan/', '', $file);
                                                    $previewUrl = route('data-perijinan.download-file', ['filepath' => rawurlencode($routePath), 'preview' => 1]);
                                                    $downloadUrl = route('data-perijinan.download-file', rawurlencode($routePath));
                                                @endphp

                                                @if ($isImage)
                                                    <button
                                                        onclick="previewImage('{{ $previewUrl }}', '{{ basename($file) }}')"
                                                        class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                                                        title="Pratinjau Gambar">
                                                        <i class="mdi mdi-eye"></i>
                                                    </button>
                                                @elseif($isPdf)
                                                    <button
                                                        onclick="openPdfPreview('{{ $previewUrl }}', '{{ basename($file) }}')"
                                                        class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                                                        title="Pratinjau PDF">
                                                        <i class="mdi mdi-eye"></i>
                                                    </button>
                                                @endif

                                                <a href="{{ $downloadUrl }}"
                                                    class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg transition-colors"
                                                    title="Unduh">
                                                    <i class="mdi mdi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Dokumen Surat Otomatis -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Section header -->
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-600 to-indigo-600 flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                            <i class="mdi mdi-file-document-multiple text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-white text-base">Dokumen Surat Pengajuan</h2>
                            <p class="text-blue-100 text-xs mt-0.5">Digenerate otomatis dari data pemohon · Format PDF</p>
                        </div>
                    </div>
                    <form action="{{ route('data-perijinan.regenerate-documents', $application->id) }}" method="POST"
                        onsubmit="return confirm('Generasi ulang akan menimpa dokumen lama. Lanjutkan?');">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white border border-white/30 rounded-lg text-xs font-semibold transition-all">
                            <i class="mdi mdi-sync text-sm"></i> Generasi Ulang
                        </button>
                    </form>
                </div>

                <div class="p-5">
                    @if($application->file_pernyataan || $application->file_permohonan || $application->file_keabsahan || $application->file_rekom || $application->file_izin)
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                            @php
                            $docItems = [
                                [
                                    'label'   => 'Surat Pernyataan',
                                    'desc'    => 'Pernyataan dari pemohon atas kebenaran data',
                                    'icon'    => 'mdi-file-certificate-outline',
                                    'color'   => 'amber',
                                    'file'    => $application->file_pernyataan,
                                    'id'      => 'pernyataan',
                                ],
                                [
                                    'label'   => 'Surat Permohonan',
                                    'desc'    => 'Permohonan resmi dari pemohon kepada instansi',
                                    'icon'    => 'mdi-file-send-outline',
                                    'color'   => 'blue',
                                    'file'    => $application->file_permohonan,
                                    'id'      => 'permohonan',
                                ],
                                [
                                    'label'   => 'Surat Keabsahan',
                                    'desc'    => 'Pernyataan keabsahan dokumen yang dilampirkan',
                                    'icon'    => 'mdi-file-check-outline',
                                    'color'   => 'emerald',
                                    'file'    => $application->file_keabsahan,
                                    'id'      => 'keabsahan',
                                ],
                                [
                                    'label'   => 'Surat Rekomendasi',
                                    'desc'    => 'Surat Rekomendasi dari instansi terkait',
                                    'icon'    => 'mdi-file-document-outline',
                                    'color'   => 'purple',
                                    'file'    => $application->file_rekom,
                                    'id'      => 'rekom',
                                ],
                                [
                                    'label'   => 'Surat Izin / Keputusan',
                                    'desc'    => 'Surat Keputusan Izin yang dikeluarkan',
                                    'icon'    => 'mdi-file-star-outline',
                                    'color'   => 'indigo',
                                    'file'    => $application->file_izin,
                                    'id'      => 'izin',
                                ],
                            ];
                            $colorMap = [
                                'amber'   => ['bg' => 'bg-amber-50 dark:bg-amber-900/10',   'border' => 'border-amber-200 dark:border-amber-800/40',   'icon_bg' => 'bg-amber-100 dark:bg-amber-900/30',   'icon_text' => 'text-amber-600 dark:text-amber-400',   'badge' => 'bg-amber-500', 'btn_preview' => 'bg-amber-500 hover:bg-amber-600'],
                                'blue'    => ['bg' => 'bg-blue-50 dark:bg-blue-900/10',     'border' => 'border-blue-200 dark:border-blue-800/40',     'icon_bg' => 'bg-blue-100 dark:bg-blue-900/30',     'icon_text' => 'text-blue-600 dark:text-blue-400',     'badge' => 'bg-blue-500',  'btn_preview' => 'bg-blue-600 hover:bg-blue-700'],
                                'emerald' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/10', 'border' => 'border-emerald-200 dark:border-emerald-800/40', 'icon_bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'icon_text' => 'text-emerald-600 dark:text-emerald-400', 'badge' => 'bg-emerald-500', 'btn_preview' => 'bg-emerald-600 hover:bg-emerald-700'],
                                'purple'  => ['bg' => 'bg-purple-50 dark:bg-purple-900/10', 'border' => 'border-purple-200 dark:border-purple-800/40', 'icon_bg' => 'bg-purple-100 dark:bg-purple-900/30', 'icon_text' => 'text-purple-600 dark:text-purple-400', 'badge' => 'bg-purple-500', 'btn_preview' => 'bg-purple-600 hover:bg-purple-700'],
                                'indigo'  => ['bg' => 'bg-indigo-50 dark:bg-indigo-900/10', 'border' => 'border-indigo-200 dark:border-indigo-800/40', 'icon_bg' => 'bg-indigo-100 dark:bg-indigo-900/30', 'icon_text' => 'text-indigo-600 dark:text-indigo-400', 'badge' => 'bg-indigo-500', 'btn_preview' => 'bg-indigo-600 hover:bg-indigo-700'],
                            ];
                            @endphp

                            @foreach($docItems as $doc)
                                @if($doc['file'])
                                    @php
                                        $c = $colorMap[$doc['color']];
                                        $routePath = str_replace('uploads/perijinan/', '', $doc['file']);
                                        $fileUrl = asset($doc['file']);
                                    @endphp
                                    <div class="rounded-2xl border {{ $c['border'] }} {{ $c['bg'] }} overflow-hidden flex flex-col transition-all hover:shadow-md">

                                        <!-- Card top -->
                                        <div class="p-5 flex-1">
                                            <div class="flex items-start gap-3 mb-3">
                                                <div class="w-12 h-12 rounded-xl {{ $c['icon_bg'] }} flex items-center justify-center flex-shrink-0">
                                                    <i class="mdi {{ $doc['icon'] }} text-2xl {{ $c['icon_text'] }}"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold text-white {{ $c['badge'] }} mb-1">
                                                        <i class="mdi mdi-file-pdf-box text-xs"></i> PDF
                                                    </span>
                                                    <h4 class="text-sm font-bold text-gray-800 dark:text-white leading-tight">{{ $doc['label'] }}</h4>
                                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 leading-snug">{{ $doc['desc'] }}</p>
                                                </div>
                                            </div>
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500 truncate font-mono bg-white/60 dark:bg-gray-900/40 rounded-lg px-2 py-1 border border-gray-100 dark:border-gray-700"
                                               title="{{ basename($doc['file']) }}">
                                                {{ basename($doc['file']) }}
                                            </p>
                                        </div>

                                        <!-- Card actions -->
                                        <div class="px-4 pb-4 flex items-center gap-2">
                                            <button type="button"
                                                onclick="openPdfPreview('{{ $fileUrl }}', '{{ $doc['label'] }}')"
                                                class="flex-1 flex items-center justify-center gap-1.5 py-2 {{ $c['btn_preview'] }} text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                                                <i class="mdi mdi-eye-outline text-sm"></i> Pratinjau
                                            </button>
                                            <a href="{{ route('data-perijinan.download-file', rawurlencode($routePath)) }}"
                                                class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold transition-all">
                                                <i class="mdi mdi-download text-sm"></i> Unduh
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    {{-- Placeholder for missing doc --}}
                                    @php $c = $colorMap[$doc['color']]; @endphp
                                    <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20 flex flex-col items-center justify-center p-6 min-h-[180px]">
                                        <i class="mdi {{ $doc['icon'] }} text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500">{{ $doc['label'] }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1">Belum digenerate</p>
                                    </div>
                                @endif
                            @endforeach

                        </div>
                    @else
                        <div class="flex flex-col items-center py-10">
                            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-900/50 flex items-center justify-center mb-4">
                                <i class="mdi mdi-file-document-remove-outline text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Dokumen surat belum digenerate</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 mb-4">Klik tombol di bawah untuk membuat dokumen dari data pengajuan ini</p>
                            <form action="{{ route('data-perijinan.regenerate-documents', $application->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold transition-all shadow-md flex items-center gap-2">
                                    <i class="mdi mdi-file-plus-outline"></i> Generate Dokumen Sekarang
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- PDF Preview Modal -->
            <div id="modal-pdf-preview" class="fixed inset-0 z-[300] hidden items-center justify-center bg-black/70 backdrop-blur-sm">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl flex flex-col overflow-hidden"
                     style="width: min(960px, 95vw); height: 90vh;">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex-shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                                <i class="mdi mdi-file-pdf-box text-red-600 dark:text-red-400 text-lg"></i>
                            </div>
                            <div>
                                <h3 id="pdf-modal-title" class="text-sm font-bold text-gray-800 dark:text-white">Pratinjau Dokumen</h3>
                                <p class="text-[10px] text-gray-500">Pratinjau langsung · Untuk mengunduh klik tombol Unduh</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a id="pdf-modal-download" href="#"
                                class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition-all">
                                <i class="mdi mdi-download text-sm"></i> Unduh
                            </a>
                            <a id="pdf-modal-open" href="#" target="_blank"
                                class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-xs font-semibold transition-all">
                                <i class="mdi mdi-open-in-new text-sm"></i> Tab Baru
                            </a>
                            <button type="button" onclick="closePdfPreview()"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                                <i class="mdi mdi-close text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- PDF iframe -->
                    <div class="flex-1 bg-gray-200 dark:bg-gray-950 overflow-hidden">
                        <iframe id="pdf-modal-iframe" src="" class="w-full h-full border-0"
                            title="Pratinjau PDF">
                        </iframe>
                    </div>
                </div>
            </div>

            @push('scripts')
            <script>
                function openPdfPreview(url, title) {
                    document.getElementById('pdf-modal-title').textContent = title;
                    document.getElementById('pdf-modal-iframe').src = url;
                    document.getElementById('pdf-modal-open').href = url;
                    document.getElementById('pdf-modal-download').href = url;

                    const modal = document.getElementById('modal-pdf-preview');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function closePdfPreview() {
                    const modal = document.getElementById('modal-pdf-preview');
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                    // Stop loading the pdf when closed
                    document.getElementById('pdf-modal-iframe').src = '';
                }

                document.getElementById('modal-pdf-preview').addEventListener('click', function(e) {
                    if (e.target === this) closePdfPreview();
                });
            </script>
            @endpush
        </div>

        <!-- Right: Status & Timeline -->
        <div class="space-y-4">
            <!-- Identity Detail Shortcut - Visible & Attractive -->
            <button type="button" onclick="openIdentityModal()"
                class="w-full flex items-center justify-center gap-3 p-4 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white rounded-2xl shadow-lg shadow-blue-500/20 transition-all hover:-translate-y-1 active:scale-[0.98] group">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center transition-transform group-hover:scale-110">
                    <i class="mdi mdi-account-box-multiple text-white text-xl"></i>
                </div>
                <div class="text-left flex-1">
                    <p class="text-[10px] text-blue-100 font-bold uppercase tracking-widest mb-0.5">Informasi Lengkap</p>
                    <p class="text-sm font-bold">Detail Identitas Pemohon</p>
                </div>
                <i class="mdi mdi-chevron-right text-xl text-white/50 group-hover:text-white transition-colors"></i>
            </button>

            <!-- Progress Card -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-5 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold">Progress Validasi</h3>
                    <span class="text-2xl font-bold">{{ $application->progress_percentage }}%</span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-2 mb-3">
                    <div class="bg-white rounded-full h-2 transition-all duration-500"
                        style="width: {{ $application->progress_percentage }}%"></div>
                </div>
                <div class="flex items-center justify-between text-sm text-blue-100">
                    <span>Tahap {{ $application->current_step }}</span>
                    <span>dari {{ $application->perijinan->activeValidationFlows->count() }}</span>
                </div>
            </div>

            @if ($application->status === 'perbaikan')
                <!-- Info Box - Status Perbaikan -->
                <div
                    class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-900/10 border-2 border-orange-300 dark:border-orange-700 rounded-xl p-5">
                    <div class="flex items-start gap-3">
                        <div
                            class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                            <i class="mdi mdi-file-document-edit text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-orange-800 dark:text-orange-300 mb-2">
                                <i class="mdi mdi-clock-outline"></i>
                                Menunggu Perbaikan Pemohon
                            </h3>
                            <p class="text-orange-700 dark:text-orange-400 text-sm mb-3">
                                Pengajuan ini telah dikembalikan untuk diperbaiki. Validator tidak dapat melakukan
                                validasi sampai pemohon submit ulang.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif ($application->status === 'rejected')
                <!-- Info Box - Status Ditolak -->
                <div
                    class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-900/10 border-2 border-red-300 dark:border-red-700 rounded-xl p-5">
                    <div class="flex items-start gap-3">
                        <div
                            class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                            <i class="mdi mdi-close-circle text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-red-800 dark:text-red-300 mb-2">
                                <i class="mdi mdi-alert-circle"></i>
                                Pengajuan Ditolak
                            </h3>
                            <p class="text-red-700 dark:text-red-400 text-sm mb-3">
                                Pengajuan ini telah ditolak. Proses validasi telah dihentikan secara permanen.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Validation Form - Only show if user can validate current step -->
            @php
                $canValidate = false;
                $currentValidasi = null;
                $isPerbaikan = $application->status === 'perbaikan';
                $hasValidated = false; // Track if user already validated

                // Admin hanya bisa memantau, tidak bisa validasi
                if (Auth::user()->isAdmin()) {
                    $canValidate = false; // Admin tidak bisa validasi
                } elseif ($isPerbaikan) {
                    $canValidate = false; // Status perbaikan - tunggu submit ulang
                } else {
                    // Cek apakah user ditugaskan di tahap validasi saat ini
                    $currentValidasi = $application
                        ->validasiRecords()
                        ->where('order', $application->current_step)
                        ->first();

                    if ($currentValidasi) {
                        $validationFlow = $currentValidasi->validationFlow;
                        $userRole = Auth::user()->role;

                        // Role yang tidak memerlukan assigned_user_id (semua user dengan role ini bisa validasi)
                        $rolesWithoutAssignment = ['verifikator', 'kadin'];

                        if (in_array($userRole, $rolesWithoutAssignment)) {
                            // Cek apakah role user match dengan role di validation flow
                            $canValidate =
                                $userRole === $validationFlow->role &&
                                $currentValidasi->status === 'pending' &&
                                !in_array($application->status, ['rejected', 'approved']);

                            // Cek apakah user sudah pernah validasi di tahap ini
                            $existingValidasi = $application
                                ->validasiRecords()
                                ->where('order', $application->current_step)
                                ->where('user_id', Auth::id())
                                ->where('status', '!=', 'pending')
                                ->first();

                            if ($existingValidasi) {
                                $hasValidated = true;
                                $canValidate = false; // User sudah validasi, tidak bisa validasi lagi
                            }
                        } else {
                            // Role yang memerlukan assigned_user_id (FO, BO, Operator OPD, Kepala OPD)
                            $assignedUserId = $currentValidasi->user_id ?? $validationFlow->assigned_user_id;

                            $canValidate =
                                $assignedUserId === Auth::id() &&
                                $currentValidasi->status === 'pending' &&
                                !in_array($application->status, ['rejected', 'approved']);

                            // Cek apakah sudah validasi (untuk assigned user)
                            if ($currentValidasi->status !== 'pending' && $assignedUserId === Auth::id()) {
                                $hasValidated = true;
                                $canValidate = false;
                            }
                        }
                    }
                }

                $isRejected = $application->status === 'rejected';
                $isApproved = $application->status === 'approved';
            @endphp

            @if ($hasValidated)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-green-200 dark:border-green-800 p-5">
                    <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="mdi mdi-check-circle text-green-600"></i>
                        Status Validasi Anda
                    </h3>

                    <div
                        class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <p class="text-sm text-green-800 dark:text-green-300">
                            <i class="mdi mdi-information"></i>
                            <strong>Anda telah memvalidasi pengajuan ini.</strong>
                        </p>
                        @if ($currentValidasi && $currentValidasi->status !== 'pending')
                            <p class="text-sm text-green-700 dark:text-green-400 mt-2">
                                <strong>Keputusan Anda:</strong>
                                @if ($currentValidasi->status === 'approved')
                                    <span class="text-green-600 dark:text-green-300 font-semibold">✅ Disetujui</span>
                                @elseif($currentValidasi->status === 'rejected')
                                    <span class="text-red-600 dark:text-red-300 font-semibold">❌ Ditolak</span>
                                @elseif($currentValidasi->status === 'revision')
                                    <span class="text-orange-600 dark:text-orange-300 font-semibold">🔄 Perlu
                                        Perbaikan</span>
                                @endif
                            </p>
                            @if ($currentValidasi->catatan)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                    <strong>Catatan:</strong> {{ $currentValidasi->catatan }}
                                </p>
                            @endif
                            @if ($currentValidasi->validated_at)
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">
                                    <i class="mdi mdi-clock"></i>
                                    {{ $currentValidasi->validated_at->format('d M Y, H:i') }}
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            @if ($canValidate && !$isRejected && !$isApproved)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-blue-200 dark:border-blue-800 p-5">
                    <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="mdi mdi-clipboard-check text-blue-600"></i>
                        Aksi Validasi
                    </h3>

                    <div
                        class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <p class="text-sm text-blue-800 dark:text-blue-300">
                            <i class="mdi mdi-information"></i>
                            <strong>Anda dapat melakukan validasi pada tahap ini.</strong>
                        </p>
                    </div>

                    <form id="validationForm" action="{{ route('data-perijinan.validate', $application->id) }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="action" id="validationAction" value="">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Catatan <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <textarea name="catatan" id="catatan" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Tambahkan catatan untuk pemohon..."></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="submitValidation('approved')"
                                class="flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-all">
                                <i class="mdi mdi-check-circle"></i>
                                <span>Setujui</span>
                            </button>
                            <button type="button" onclick="showRejectForm()"
                                class="flex items-center justify-center gap-2 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-all">
                                <i class="mdi mdi-close-circle"></i>
                                <span>Tolak</span>
                            </button>
                        </div>
                        <button type="button" onclick="showRevisionForm()"
                            class="w-full mt-2 flex items-center justify-center gap-2 px-4 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold transition-all">
                            <i class="mdi mdi-arrow-return"></i>
                            <span>Kembalikan untuk Perbaikan</span>
                        </button>
                    </form>
                </div>
            @endif

            @if ($application->catatan_perbaikan)
                <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-400 p-4 rounded-r-xl">
                    <h4 class="font-bold text-orange-800 dark:text-orange-300 mb-2 flex items-center gap-2">
                        <i class="mdi mdi-alert-circle"></i>
                        Catatan Perbaikan
                    </h4>
                    <p class="text-orange-700 dark:text-orange-200 text-sm">{{ $application->catatan_perbaikan }}</p>
                </div>
            @endif

            @if ($application->catatan_reject)
                <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 p-4 rounded-r-xl">
                    <h4 class="font-bold text-red-800 dark:text-red-300 mb-2 flex items-center gap-2">
                        <i class="mdi mdi-close-circle"></i>
                        Penolakan
                    </h4>
                    <p class="text-red-700 dark:text-red-200 text-sm">{{ $application->catatan_reject }}</p>
                </div>
            @endif

            @if ($application->catatan_pemohon)
                <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 rounded-r-xl mb-4">
                    <h4 class="font-bold text-blue-800 dark:text-blue-300 mb-2 flex items-center gap-2">
                        <i class="mdi mdi-message-text-outline"></i>
                        Catatan dari Pemohon
                    </h4>
                    <p class="text-blue-700 dark:text-blue-200 text-sm">{{ $application->catatan_pemohon }}</p>
                </div>
            @endif

            <!-- Timeline -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="mdi mdi-timeline-text text-blue-600"></i>
                        Alur Validasi
                    </h2>
                </div>
                <div class="p-5">
                    <div class="space-y-0">
                        @foreach ($application->validasiRecords as $index => $validasi)
                            @php
                                $isCompleted = $validasi->status === 'approved';
                                $isCurrent = $index + 1 == $application->current_step && !$isCompleted;
                                $isPending = $validasi->status === 'pending';
                                $isRejected = $validasi->status === 'rejected';
                                $isRevision = $validasi->status === 'revision';

                                $statusColors = [
                                    'approved' => 'bg-green-500',
                                    'pending' => 'bg-gray-300 dark:bg-gray-600',
                                    'rejected' => 'bg-red-500',
                                    'revision' => 'bg-orange-500',
                                ];
                                $statusIcons = [
                                    'approved' => 'mdi-check',
                                    'pending' => 'mdi-clock-outline',
                                    'rejected' => 'mdi-close',
                                    'revision' => 'mdi-alert',
                                ];
                            @endphp
                            <div class="relative flex gap-3 {{ !$loop->last ? 'pb-4' : '' }}">
                                @if (!$loop->last)
                                    <div
                                        class="absolute left-2.5 top-6 bottom-0 w-0.5 {{ $isCompleted ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700' }}">
                                    </div>
                                @endif
                                <div
                                    class="w-5 h-5 rounded-full {{ $statusColors[$validasi->status] ?? 'bg-gray-300' }} flex items-center justify-center flex-shrink-0 z-10">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <h4 class="font-semibold text-gray-800 dark:text-white text-sm">
                                            {{ $validasi->validationFlow->role_label ?? 'Tahap ' . ($index + 1) }}
                                        </h4>
                                        <span
                                            class="px-2 py-0.5 rounded-full text-xs font-medium {{ $validasi->status_color }}">
                                            {{ $validasi->status_label }}
                                        </span>
                                    </div>
                                    @if ($validasi->catatan)
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">
                                            <i class="mdi mdi-comment-text"></i> {{ $validasi->catatan }}
                                        </p>
                                    @endif
                                    @if ($validasi->validated_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-1">
                                            <i class="mdi mdi-clock"></i>
                                            {{ $validasi->validated_at->format('d M Y, H:i') }}
                                        </p>
                                    @endif
                                    @php
                                        // Determine validator based on role
                                        $validatorUser = null;
                                        $validatorRole = $validasi->validationFlow->role ?? '';
                                        
                                        // For assigned roles (fo, bo, operator_opd, kepala_opd), use assigned_user from validation_flow
                                        if (in_array($validatorRole, ['fo', 'bo', 'operator_opd', 'kepala_opd'])) {
                                            $validatorUser = $validasi->validationFlow->assignedUser;
                                            
                                            // Fallback if somehow missing
                                            if (!$validatorUser && $validasi->validator) {
                                                $validatorUser = $validasi->validator;
                                            }
                                        } 
                                        // For collective roles, use validator from data_perijinan_validasi
                                        elseif ($validasi->validator) {
                                            $validatorUser = $validasi->validator;
                                        }
                                    @endphp
                                    @if ($validatorUser)
                                        <div class="mt-2 flex items-center gap-2 {{ in_array($validatorRole, ['fo', 'bo', 'operator_opd', 'kepala_opd']) ? 'bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border-amber-200 dark:border-amber-800' : 'bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-blue-200 dark:border-blue-800' }} rounded-lg p-2 border">
                                            <div class="w-6 h-6 {{ in_array($validatorRole, ['fo', 'bo', 'operator_opd', 'kepala_opd']) ? 'bg-gradient-to-br from-amber-400 to-orange-500' : 'bg-gradient-to-br from-blue-400 to-indigo-500' }} rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="mdi mdi-{{ in_array($validatorRole, ['fo', 'bo', 'operator_opd', 'kepala_opd']) ? 'account-check' : 'account-group' }} text-white text-xs"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                                                    <i class="mdi mdi-{{ in_array($validatorRole, ['fo', 'bo', 'operator_opd', 'kepala_opd']) ? 'account-tie' : 'account-check' }} mr-1"></i>
                                                    {{ in_array($validatorRole, ['fo', 'bo', 'operator_opd', 'kepala_opd']) ? $validatorUser->name : 'Divalidasi oleh ' . ($validatorUser->role_label ?? 'Validator') }}
                                                </p>
                                                @if (in_array($validatorRole, ['fo', 'bo', 'operator_opd', 'kepala_opd']))
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        <i class="mdi mdi-badge-account-horizontal mr-1"></i>
                                                        {{ $validatorUser->role_label ?? 'Validator' }}
                                                    </p>
                                                @endif
                                                @if ($validatorUser->id === Auth::id())
                                                    <p class="text-xs text-green-600 dark:text-green-400 font-semibold mt-0.5">
                                                        <i class="mdi mdi-check-circle mr-1"></i>
                                                        (Anda)
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div id="imagePreviewModal"
        class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4"
        onclick="closeImagePreview()">
        <div class="relative max-w-4xl max-h-full">
            <button onclick="closeImagePreview()"
                class="absolute -top-8 right-0 text-white hover:text-gray-300 transition-colors">
                <i class="mdi mdi-close text-3xl"></i>
            </button>
            <img id="previewImageElement" src="" alt="Preview"
                class="max-w-full max-h-[85vh] rounded-lg shadow-2xl">
            <p id="previewImageName" class="text-white text-center mt-3 text-sm"></p>
        </div>
    </div>

    <script>
        function previewImage(imageUrl, imageName) {
            document.getElementById('previewImageElement').src = imageUrl;
            document.getElementById('previewImageName').textContent = imageName;
            document.getElementById('imagePreviewModal').classList.remove('hidden');
        }

        function closeImagePreview() {
            document.getElementById('imagePreviewModal').classList.add('hidden');
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeImagePreview();
        });

        // Validation Form Functions
        function submitValidation(action) {
            const catatan = document.getElementById('catatan').value;

            let confirmMessage = '';
            let confirmTitle = '';
            let confirmIcon = '';
            let confirmColor = '';

            if (action === 'approved') {
                confirmTitle = 'Setujui Pengajuan?';
                confirmMessage = 'Apakah Anda yakin ingin menyetujui pengajuan ini?';
                confirmIcon = 'check-circle';
                confirmColor = '#16a34a';
            } else if (action === 'rejected') {
                confirmTitle = 'Tolak Pengajuan?';
                confirmMessage =
                    'Apakah Anda yakin ingin menolak pengajuan ini? Pengajuan akan dihentikan dan tidak dapat dilanjutkan.';
                confirmIcon = 'close-circle';
                confirmColor = '#dc2626';
            } else if (action === 'revision') {
                confirmTitle = 'Kembalikan untuk Perbaikan?';
                confirmMessage = 'Pengajuan akan dikembalikan ke pemohon untuk diperbaiki.';
                confirmIcon = 'arrow-return';
                confirmColor = '#ea580c';
            }

            Swal.fire({
                title: confirmTitle,
                text: confirmMessage,
                icon: action === 'approved' ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('validationAction').value = action;
                    document.getElementById('validationForm').submit();
                }
            });
        }

        function showRejectForm() {
            Swal.fire({
                title: '<div class="flex items-center gap-2 mb-2"><i class="mdi mdi-close-circle text-red-500 text-2xl"></i><span class="text-gray-800 dark:text-gray-200">Tolak Pengajuan</span></div>',
                html: `
                    <div class="text-left">
                        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-r-lg p-4 mb-4">
                            <div class="flex items-start gap-3">
                                <i class="mdi mdi-alert text-red-500 text-xl mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-semibold text-red-800 dark:text-red-300 mb-1">
                                        Penolakan akan menghentikan proses validasi
                                    </p>
                                    <p class="text-xs text-red-600 dark:text-red-400">
                                        Pengajuan tidak dapat dilanjutkan lagi. Pastikan alasan penolakan sudah tepat.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="swal-catatan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <i class="mdi mdi-comment-text text-gray-500 mr-1"></i>
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="swal-catatan" 
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white text-sm transition-all resize-none"
                                rows="5" 
                                placeholder="Contoh:&#10;- Dokumen tidak sesuai dengan persyaratan yang ditetapkan&#10;- Data yang diajukan tidak valid berdasarkan verifikasi&#10;- Pemohon tidak memenuhi syarat untuk jenis perizinan ini"
                            ></textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center gap-1">
                                <i class="mdi mdi-keyboard"></i>
                                <span id="char-count-reject">0</span> karakter
                            </p>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="mdi mdi-check mr-2"></i>Tolak Pengajuan',
                cancelButtonText: '<i class="mdi mdi-close mr-2"></i>Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-2xl',
                    title: 'text-center border-b border-gray-200 dark:border-gray-700 pb-3 mb-3',
                    htmlContainer: 'px-2',
                    actions: 'border-t border-gray-200 dark:border-gray-700 pt-3 mt-3'
                },
                preConfirm: () => {
                    const catatan = document.getElementById('swal-catatan').value;
                    if (!catatan || catatan.trim() === '') {
                        Swal.showValidationMessage(
                            '<i class="mdi mdi-alert-circle mr-2"></i>Alasan penolakan wajib diisi');
                        return false;
                    }
                    if (catatan.length < 10) {
                        Swal.showValidationMessage(
                            '<i class="mdi mdi-alert-circle mr-2"></i>Alasan minimal 10 karakter');
                        return false;
                    }
                    return catatan;
                },
                didOpen: () => {
                    const textarea = document.getElementById('swal-catatan');
                    const charCount = document.getElementById('char-count-reject');

                    textarea.focus();

                    textarea.addEventListener('input', () => {
                        charCount.textContent = textarea.value.length;
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('catatan').value = result.value;
                    submitValidation('rejected');
                }
            });
        }

        function showRevisionForm() {
            Swal.fire({
                title: '<div class="flex items-center gap-2 mb-2"><i class="mdi mdi-arrow-return text-orange-500 text-2xl"></i><span class="text-gray-800 dark:text-gray-200">Kembalikan untuk Perbaikan</span></div>',
                html: `
                    <div class="text-left">
                        <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 rounded-r-lg p-4 mb-4">
                            <div class="flex items-start gap-3">
                                <i class="mdi mdi-information text-orange-500 text-xl mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-semibold text-orange-800 dark:text-orange-300 mb-1">
                                        Catatan ini akan dikirim ke pemohon
                                    </p>
                                    <p class="text-xs text-orange-600 dark:text-orange-400">
                                        Berikan instruksi yang jelas agar pemohon dapat memperbaiki pengajuan dengan tepat.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="swal-catatan-revision" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <i class="mdi mdi-comment-text text-gray-500 mr-1"></i>
                                Catatan Perbaikan <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="swal-catatan-revision" 
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white text-sm transition-all resize-none"
                                rows="5" 
                                placeholder="Contoh:&#10;- Dokumen KTP belum terbaca dengan jelas, mohon upload ulang dengan resolusi lebih tinggi&#10;- KK perlu diperbarui karena tanggal terbit sudah kadaluarsa&#10;- Lampiran NPWP belum dilampirkan"
                            ></textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center gap-1">
                                <i class="mdi mdi-keyboard"></i>
                                <span id="char-count">0</span> karakter
                            </p>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="mdi mdi-check mr-2"></i>Kembalikan untuk Perbaikan',
                cancelButtonText: '<i class="mdi mdi-close mr-2"></i>Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-2xl',
                    title: 'text-center border-b border-gray-200 dark:border-gray-700 pb-3 mb-3',
                    htmlContainer: 'px-2',
                    actions: 'border-t border-gray-200 dark:border-gray-700 pt-3 mt-3'
                },
                preConfirm: () => {
                    const catatan = document.getElementById('swal-catatan-revision').value;
                    if (!catatan || catatan.trim() === '') {
                        Swal.showValidationMessage(
                            '<i class="mdi mdi-alert-circle mr-2"></i>Catatan perbaikan wajib diisi');
                        return false;
                    }
                    if (catatan.length < 10) {
                        Swal.showValidationMessage(
                            '<i class="mdi mdi-alert-circle mr-2"></i>Catatan minimal 10 karakter');
                        return false;
                    }
                    return catatan;
                },
                didOpen: () => {
                    const textarea = document.getElementById('swal-catatan-revision');
                    const charCount = document.getElementById('char-count');

                    textarea.focus();

                    textarea.addEventListener('input', () => {
                        charCount.textContent = textarea.value.length;
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('catatan').value = result.value;
                    submitValidation('revision');
                }
            });
        }

        function previewImage(url, title) {
            // Fetch the image through the authenticated route
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to load image');
                    }
                    return response.blob();
                })
                .then(blob => {
                    const imageUrl = URL.createObjectURL(blob);
                    Swal.fire({
                        title: title,
                        imageUrl: imageUrl,
                        imageAlt: title,
                        imageClass: 'max-w-full',
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#2563eb'
                    });
                })
                .catch(error => {
                    console.error('Error loading image:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal memuat preview gambar',
                        confirmButtonColor: '#2563eb'
                    });
                });
        }
    </script>
</x-layout>
