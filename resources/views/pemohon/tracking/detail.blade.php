<x-pemohon.layout>
    <x-slot:title>Detail Tracking - {{ $data->no_registrasi }} - JITU Banjarnegara</x-slot:title>

    <!-- Navbar -->
    <x-pemohon.navbar></x-pemohon.navbar>

    <!-- Main Content -->
    <main class="flex-1 max-w-[95%] mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-br from-amber-600 via-amber-700 to-amber-800 rounded-3xl shadow-xl p-6 text-white">
            <div class="flex items-center gap-4">
                <a href="{{ route('pemohon.tracking') }}" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold mb-1">Tracking Pengajuan</h1>
                    <p class="text-amber-100 text-sm font-mono">{{ $data->no_registrasi }}</p>
                </div>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $data->status_color }}">
                    {{ $data->status_label }}
                </span>
            </div>
        </div>

        <!-- Application Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 flex-1">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jenis Perizinan</p>
                    <p class="font-bold text-gray-800">{{ $data->perijinan->nama_perijinan }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tanggal Pengajuan</p>
                    <p class="font-bold text-gray-800">{{ $data->created_at->format('d M Y, H:i') }} WIB</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pemohon</p>
                    <p class="font-bold text-gray-800">{{ $data->user->name }}</p>
                </div>
            </div>
            <div>
                <button type="button" onclick="openGlobalFormModal()" class="w-full md:w-auto px-5 py-2.5 bg-amber-100 hover:bg-amber-200 text-amber-800 rounded-xl font-bold transition-colors border border-amber-200 shadow-sm flex items-center justify-center gap-2 text-sm whitespace-nowrap">
                    <i class="fas fa-file-alt"></i> Lihat Isian Formulir
                </button>
            </div>
        </div>
        
        <!-- Dokumen Izin Download Section -->
        @if($data->status === 'approved' && ($data->file_izin_tte || $data->file_izin))
            @if($data->isSkmFilled())
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-2xl p-6 shadow-sm flex items-center justify-between flex-wrap gap-4 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center text-2xl shadow-inner">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-emerald-800">Dokumen Izin Telah Terbit!</h3>
                            <p class="text-sm text-emerald-600">Pengajuan Anda telah disetujui. Silakan unduh dokumen izin Anda di bawah ini.</p>
                        </div>
                    </div>
                    <a href="{{ asset($data->file_izin_tte ?: $data->file_izin) }}" target="_blank" download class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md transition-all transform hover:-translate-y-1">
                        <i class="fas fa-download"></i> Unduh Dokumen Izin
                    </a>
                </div>
            @else
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-2xl shadow-inner flex-shrink-0">
                            <i class="fas fa-poll-h"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-blue-800">Survei Kepuasan Masyarakat (SKM)</h3>
                            <p class="text-sm text-blue-600 leading-relaxed mb-4">
                                Pengajuan Anda telah <strong>Disetujui</strong> dan dokumen izin telah siap. Sesuai ketentuan, mohon kesediaan Anda untuk mengisi Survei Kepuasan Masyarakat (SKM) terlebih dahulu sebelum mengunduh dokumen izin resmi.
                            </p>
                            <a href="#" onclick="openSkmModal(); return false;" 
                               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-all transform hover:scale-105 active:scale-95">
                                <i class="fas fa-edit"></i>
                                Isi Survei Sekarang & Unduh Izin
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Progress Overview -->
        <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-line text-amber-600"></i>
                Progress Validasi
            </h2>
            
            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
                    <span>Tahap {{ $data->current_step }} dari {{ $data->perijinan->activeValidationFlows->count() }}</span>
                    <span>{{ $data->progress_percentage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-amber-500 to-amber-600 h-3 rounded-full transition-all duration-500"
                        style="width: {{ $data->progress_percentage }}%"></div>
                </div>
            </div>

            <!-- Validation Timeline -->
            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-8 top-4 bottom-4 w-0.5 bg-gradient-to-b from-amber-400 via-purple-400 to-pink-400 rounded-full"></div>

                <div class="space-y-6">
                    @foreach ($data->validasiRecords as $index => $validasi)
                        @php
                            $isCompleted = $validasi->status === 'approved';
                            $isCurrent = ($index + 1 == $data->current_step) && 
                                         !$isCompleted && 
                                         in_array($data->status, ['submitted', 'in_progress']);
                            $isPending = $validasi->status === 'pending';
                            $isRejected = $validasi->status === 'rejected';
                            $isRevision = $validasi->status === 'revision';
                        @endphp

                        <div class="relative flex items-start gap-4 pl-4">
                            <!-- Timeline Dot -->
                            <div class="relative z-10 w-16 h-16 rounded-full flex items-center justify-center shadow-lg flex-shrink-0 border-4 border-white
                                {{ $isCompleted ? 'bg-gradient-to-br from-green-500 to-green-600' : '' }}
                                {{ $isCurrent ? 'bg-gradient-to-br from-amber-500 to-amber-600 animate-pulse scale-110' : '' }}
                                {{ $isPending && !$isCurrent ? 'bg-gradient-to-br from-gray-300 to-gray-400' : '' }}
                                {{ $isRejected ? 'bg-gradient-to-br from-red-500 to-red-600' : '' }}
                                {{ $isRevision ? 'bg-gradient-to-br from-orange-500 to-orange-600' : '' }}
                            ">
                                @if($isCurrent)
                                    <div class="absolute inset-0 rounded-full bg-amber-400 animate-ping opacity-30"></div>
                                @endif
                                
                                @if ($isCompleted)
                                    <i class="fas fa-check text-white text-xl"></i>
                                @elseif ($isRejected)
                                    <i class="fas fa-times text-white text-xl"></i>
                                @elseif ($isRevision)
                                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                                @else
                                    <span class="text-white font-bold text-lg">{{ $index + 1 }}</span>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 bg-gradient-to-br from-gray-50 to-white rounded-xl p-5 border-2 
                                {{ $isCompleted ? 'border-green-200' : '' }}
                                {{ $isCurrent ? 'border-amber-300 shadow-lg shadow-amber-200' : '' }}
                                {{ $isPending && !$isCurrent ? 'border-gray-200' : '' }}
                                {{ $isRejected ? 'border-red-200' : '' }}
                                {{ $isRevision ? 'border-orange-200' : '' }}
                            ">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <h3 class="font-bold text-gray-800">
                                            {{ $validasi->validationFlow->role_label ?? 'Tahap ' . ($index + 1) }}
                                        </h3>
                                        <p class="text-xs text-gray-500">
                                            {{ $validasi->validationFlow->description ?? 'Proses validasi' }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $validasi->status_color }}">
                                        {{ $validasi->status_label }}
                                    </span>
                                </div>

                                @if ($validasi->catatan)
                                    <div class="mt-3 bg-white rounded-lg p-3 border border-gray-200">
                                        <p class="text-sm text-gray-700">
                                            <i class="fas fa-comment-alt text-amber-500 mr-2"></i>
                                            <strong>Catatan:</strong> {{ $validasi->catatan }}
                                        </p>
                                    </div>
                                @endif

                                @if ($validasi->validated_at)
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-clock mr-1"></i>
                                        Divalidasi pada {{ $validasi->validated_at->format('d M Y, H:i') }} WIB
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-hourglass-half mr-1"></i>
                                        Waktu Proses (SLA): <span class="font-semibold text-amber-700">{{ formatDuration($validasi->duration_seconds ?? 0) }}</span>
                                    </p>
                                @elseif ($validasi->duration_seconds > 0)
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-hourglass-half mr-1"></i>
                                        Waktu Proses Terakumulasi (SLA): <span class="font-semibold text-amber-700">{{ formatDuration($validasi->duration_seconds) }}</span>
                                    </p>
                                @endif

                                @php
                                    $validatorRole = $validasi->validationFlow->role ?? '';
                                    $validatorName = null;
                                    $validatorRoleLabel = null;

                                    // Untuk fo, bo, operator_opd, kepala_opd:
                                    // Prioritas 1 - assigned_user di validation_flow (petugas yang ditugaskan admin)
                                    if (in_array($validatorRole, ['fo', 'bo', 'operator_opd', 'kepala_opd'])) {
                                        if ($validasi->validationFlow->assignedUser) {
                                            $validatorName = $validasi->validationFlow->assignedUser->name;
                                            $validatorRoleLabel = $validasi->validationFlow->assignedUser->role_label
                                                ?? $validasi->validationFlow->role_label;
                                        }
                                        // Prioritas 2 - validator aktual (sudah memvalidasi)
                                        elseif ($validasi->validator) {
                                            $validatorName = $validasi->validator->name;
                                            $validatorRoleLabel = $validasi->validator->role_label ?? 'Validator';
                                        }
                                    }

                                    $showAssignedUser = !empty($validatorName);
                                @endphp

                                @if ($showAssignedUser)
                                    @php
                                        // Tentukan warna berdasarkan role
                                        $isOpdRole = in_array($validatorRole, ['operator_opd', 'kepala_opd']);
                                        $isFoBo = in_array($validatorRole, ['fo', 'bo']);
                                        $bgFrom = $isFoBo ? 'from-blue-50' : 'from-amber-50';
                                        $bgTo = $isFoBo ? 'to-indigo-50' : 'to-orange-50';
                                        $borderColor = $isFoBo ? 'border-blue-200' : 'border-amber-200';
                                        $avatarFrom = $isFoBo ? 'from-blue-400' : 'from-amber-400';
                                        $avatarTo = $isFoBo ? 'to-indigo-500' : 'to-orange-500';
                                        $textColor = $isFoBo ? 'text-blue-700' : 'text-amber-700';
                                        $labelKey = $isFoBo
                                            ? ($validatorRole === 'fo' ? 'Front Office' : 'Back Office')
                                            : ($validatorRole === 'operator_opd' ? 'Operator OPD' : 'Kepala OPD');
                                    @endphp
                                    <div class="mt-3 flex items-center gap-2 bg-gradient-to-r {{ $bgFrom }} {{ $bgTo }} rounded-lg p-3 border {{ $borderColor }}">
                                        <div class="w-8 h-8 bg-gradient-to-br {{ $avatarFrom }} {{ $avatarTo }} rounded-full flex items-center justify-center flex-shrink-0 shadow-md">
                                            <i class="fas fa-user-check text-white text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs {{ $textColor }} font-semibold mb-0.5">
                                                <i class="fas fa-user-tie mr-1"></i>
                                                Petugas yang Ditugaskan:
                                            </p>
                                            <p class="text-sm font-bold text-gray-800">
                                                {{ $validatorName }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <i class="fas fa-id-badge mr-1"></i>
                                                {{ $validatorRoleLabel ?? $labelKey }}
                                            </p>
                                        </div>
                                    </div>
                                @elseif ($validasi->validator && in_array($validatorRole, ['verifikator', 'kadin']))
                                    <!-- Untuk role kolektif yang belum di-assign, tampilkan role saja -->
                                    <div class="mt-3 flex items-center gap-2 bg-gradient-to-r from-purple-50 to-violet-50 rounded-lg p-3 border border-purple-200">
                                        <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-violet-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-md">
                                            <i class="fas fa-users text-white text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-800">
                                                <i class="fas fa-user-check mr-2"></i>
                                                Divalidasi oleh {{ $validasi->validationFlow->role_label ?? 'Validator' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $validasi->validated_at ? 'Telah menyelesaikan validasi' : 'Menunggu validasi' }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                @php
                                    $showTteDocument = false;
                                    $tteDocumentUrl = null;
                                    
                                    if ($validatorRole === 'kepala_opd' && $validasi->status === 'approved') {
                                        if ($data->perijinan->is_multi_opd) {
                                            $opdId = $validasi->validationFlow->assignedUser->opd_id ?? null;
                                            if ($opdId && !empty($data->file_rekom_multi_tte[$opdId])) {
                                                $showTteDocument = true;
                                                $tteDocumentUrl = asset($data->file_rekom_multi_tte[$opdId]);
                                            }
                                        } else if (!empty($data->file_rekom_tte)) {
                                            $showTteDocument = true;
                                            $tteDocumentUrl = asset($data->file_rekom_tte);
                                        }
                                    }
                                @endphp

                                @if ($showTteDocument)
                                    <div class="mt-4">
                                        <button onclick="openPdfModal('{{ $tteDocumentUrl }}')" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl text-xs font-bold transition-all w-full sm:w-auto justify-center">
                                            <i class="fas fa-file-pdf text-indigo-500"></i> Lihat Surat Rekomendasi
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if ($data->catatan_perbaikan)
            <!-- Catatan Perbaikan -->
            <div class="bg-orange-50 border-l-4 border-orange-400 p-6 rounded-r-2xl">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-orange-500 text-2xl mt-0.5"></i>
                    <div class="flex-1">
                        <h3 class="font-bold text-orange-800 mb-2">Catatan Perbaikan</h3>
                        <p class="text-orange-700 mb-3">{{ $data->catatan_perbaikan }}</p>
                        
                        @php
                            // Find the validator who gave the revision note
                            $revisionValidasi = $data->validasiRecords->firstWhere('status', 'revision');
                        @endphp
                        
                        @if($revisionValidasi)
                            <div class="flex items-center gap-2 text-sm text-orange-600 bg-orange-100 rounded-lg p-3">
                                <i class="fas fa-user-circle text-orange-500"></i>
                                <span><strong>Validator:</strong> {{ $revisionValidasi->validator->name ?? 'N/A' }}</span>
                                @if($revisionValidasi->validated_at)
                                    <span class="text-orange-500">•</span>
                                    <span><i class="fas fa-clock mr-1"></i>{{ $revisionValidasi->validated_at->format('d M Y, H:i') }} WIB</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($data->catatan_reject)
            <!-- Catatan Penolakan -->
            <div class="bg-red-50 border-l-4 border-red-400 p-6 rounded-r-2xl">
                <div class="flex items-start gap-3">
                    <i class="fas fa-times-circle text-red-500 text-2xl mt-0.5"></i>
                    <div class="flex-1">
                        <h3 class="font-bold text-red-800 mb-2">Pengajuan Ditolak</h3>
                        <p class="text-red-700">{{ $data->catatan_reject }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-between gap-4 pt-4">
            <a href="{{ route('pemohon.tracking') }}"
                class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition-colors text-center sm:text-left">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Tracking
            </a>
            
            @if ($data->status === 'perbaikan')
                <a href="{{ route('pemohon.pengajuan.edit', $data->id) }}"
                    class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-semibold transition-colors text-center sm:text-left">
                    <i class="fas fa-edit mr-2"></i> Perbaiki Pengajuan
                </a>
            @endif
        </div>
    </main>

    <!-- SKM Modal -->
    @if($data->status === 'approved' && !$data->isSkmFilled())
        <div id="skmModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col animate-in fade-in zoom-in duration-300">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-amber-600 to-amber-700 text-white p-6 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <i class="fas fa-poll-h text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Kuesioner SKM</h3>
                            <p class="text-sm text-amber-100">Survei Kepuasan Masyarakat</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeSkmModal()" class="w-10 h-10 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-8 overflow-y-auto custom-scrollbar flex-1 bg-gray-50 dark:bg-gray-800/50">
                    <form action="{{ route('skm.store') }}" method="POST" id="skmForm">
                        @csrf
                        <input type="hidden" name="data_perijinan_id" value="{{ $data->id }}">

                        <!-- Error Alert Area -->
                        <div id="skmError" class="hidden mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl flex items-start gap-3 animate-in fade-in slide-in-from-left-4 duration-300">
                            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                            <p class="text-sm text-red-700" id="skmErrorMessage"></p>
                        </div>

                        <div class="mb-8 p-5 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 rounded-r-xl">
                            <p class="text-sm text-amber-800 dark:text-amber-200">
                                Mohon kesediaan Anda mengisi survei singkat ini untuk membantu kami meningkatkan kualitas pelayanan. Data survei ini diperlukan sebagai syarat pengunduhan dokumen izin Anda.
                            </p>
                        </div>

                        <!-- Respondent Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="responden_nama" value="{{ auth()->user()->name }}" required class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl outline-none focus:ring-2 focus:ring-amber-500 transition-all text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="responden_email" value="{{ auth()->user()->email }}" required class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl outline-none focus:ring-2 focus:ring-amber-500 transition-all text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">NIP / NIK <span class="text-red-500">*</span></label>
                                <input type="text" name="nip" value="{{ auth()->user()->nip }}" required class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl outline-none focus:ring-2 focus:ring-amber-500 transition-all text-sm">
                            </div>
                        </div>

                        <!-- Questions -->
                        <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                            <i class="fas fa-list-check text-amber-600"></i>
                            Penilaian Layanan <span class="text-red-500 text-xs font-normal">(Semua Wajib Diisi)</span>
                        </h4>
                        
                        <div class="space-y-6 mb-10">
                            @foreach($skmQuestions as $index => $question)
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                                <p class="text-gray-800 dark:text-gray-200 font-bold leading-relaxed mb-6">
                                    {{ $index + 1 }}. {{ $question->pertanyaan }}
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                    @foreach(['1' => 'Kurang Baik', '2' => 'Cukup Baik', '3' => 'Baik', '4' => 'Sangat Baik'] as $value => $label)
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="jawaban[{{ $question->id }}]" value="{{ $value }}" required class="peer sr-only">
                                        <div class="px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-gray-700 text-center transition-all group-hover:border-amber-300 peer-checked:border-amber-600 peer-checked:bg-amber-600 peer-checked:text-white">
                                            <div class="font-bold text-xs">{{ $label }}</div>
                                            <div class="text-[10px] opacity-70 mt-1">
                                                @for($s=1; $s<=$value; $s++) <i class="fas fa-star text-yellow-400"></i> @endfor
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Saran -->
                        <div class="mb-10">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Saran & Masukan <span class="text-red-500">*</span></label>
                            <textarea name="saran" id="saranText" rows="4" required class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl outline-none focus:ring-2 focus:ring-amber-500 transition-all text-sm resize-none" placeholder="Berikan saran Anda untuk peningkatan layanan kami..."></textarea>
                            <p class="text-right text-[10px] text-gray-400 mt-1" id="charCount">0/1000</p>
                        </div>

                        <!-- Security -->
                        <div class="p-6 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-100 dark:border-amber-800">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Verifikasi Keamanan</label>
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <span id="captcha-question" class="block text-lg font-bold text-amber-600 mb-2">{{ session('captcha_num1') }} + {{ session('captcha_num2') }} = ?</span>
                                    <input type="number" name="captcha" id="captchaInput" required class="w-full px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-xl outline-none focus:ring-2 focus:ring-amber-500 transition-all font-bold">
                                </div>
                                <button type="button" onclick="refreshCaptcha()" class="mt-8 bg-amber-600 hover:bg-amber-700 text-white w-10 h-10 rounded-xl flex items-center justify-center transition-all shadow-md">
                                    <i class="fas fa-sync-alt" id="refreshIcon"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="p-6 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50 flex justify-end gap-3 flex-shrink-0">
                    <button type="button" onclick="closeSkmModal()" class="px-6 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-300 transition-all text-sm">Batal</button>
                    <button type="submit" form="skmForm" class="px-8 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl transition-all shadow-lg text-sm flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i> Kirim & Unduh
                    </button>
                </div>
            </div>
        </div>

        <script>
            function openSkmModal() {
                document.getElementById('skmModal').classList.remove('hidden');
                document.getElementById('skmModal').classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
            function closeSkmModal() {
                document.getElementById('skmModal').classList.add('hidden');
                document.getElementById('skmModal').classList.remove('flex');
                document.body.style.overflow = '';
                // Clear errors on close
                document.getElementById('skmError').classList.add('hidden');
            }

            const saranText = document.getElementById('saranText');
            if(saranText) {
                saranText.addEventListener('input', function() {
                    document.getElementById('charCount').textContent = this.value.length + '/1000';
                });
            }

            function refreshCaptcha() {
                const icon = document.getElementById('refreshIcon');
                if(!icon) return;
                icon.classList.add('fa-spin');
                fetch('{{ route("skm.refresh-captcha") }}')
                    .then(r => r.json())
                    .then(d => {
                        document.getElementById('captcha-question').textContent = `${d.num1} + ${d.num2} = ?`;
                        document.getElementById('captchaInput').value = '';
                        icon.classList.remove('fa-spin');
                    });
            }

            document.getElementById('skmForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const modal = document.getElementById('skmModal');
                const btn = modal.querySelector('button[type="submit"]');
                const errorArea = document.getElementById('skmError');
                const errorMessage = document.getElementById('skmErrorMessage');
                
                errorArea.classList.add('hidden');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                btn.disabled = true;

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    let data;
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                        data = await response.json();
                    } else {
                        const text = await response.text();
                        console.error('Non-JSON response:', text);
                        throw new Error('Server tidak memberikan respon valid (Bukan JSON). Silakan hubungi admin.');
                    }
                    
                    if (response.ok && data.success) {
                        // Success - reload page to show download button
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            alert(data.message);
                            location.reload();
                        }
                    } else {
                        // Handle validation errors or others
                        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim & Unduh';
                        btn.disabled = false;
                        
                        errorArea.classList.remove('hidden');
                        if (data.errors) {
                            errorMessage.innerHTML = Object.values(data.errors).flat().join('<br>');
                        } else {
                            errorMessage.textContent = data.message || 'Terjadi kesalahan. Silakan periksa kembali isian Anda.';
                        }
                        
                        // Scroll to top of modal
                        modal.querySelector('.overflow-y-auto').scrollTop = 0;
                        
                        // Refresh captcha automatically on error
                        refreshCaptcha();
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim & Unduh';
                    btn.disabled = false;
                    errorArea.classList.remove('hidden');
                    // Show actual error message if possible
                    const detailedError = error.message || 'Terjadi kesalahan sistem.';
                    errorMessage.textContent = detailedError + ' Silakan coba lagi nanti.';
                    modal.querySelector('.overflow-y-auto').scrollTop = 0;
                });
            });
        </script>
    @endif

    <!-- Global Form Modal -->
    <div id="globalFormModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col animate-in fade-in zoom-in duration-300">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-amber-600 to-amber-700 text-white p-6 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Data Formulir</h3>
                        <p class="text-sm text-amber-100">Informasi yang diisikan saat pengajuan</p>
                    </div>
                </div>
                <button type="button" onclick="closeGlobalFormModal()" class="w-10 h-10 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-8 overflow-y-auto custom-scrollbar flex-1 bg-gray-50 dark:bg-gray-800/50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @php
                        $globalFields = $data->perijinan->activeFormFields ? $data->perijinan->activeFormFields->where('form_type', 'global')->sortBy('order') : collect();
                    @endphp
                    
                    @forelse($globalFields as $field)
                        <div class="space-y-1">
                            <label class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">{{ $field->label }}</label>
                            <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                @if($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar')
                                    @php 
                                        $files = $data->form_files[$field->id] ?? [];
                                        $filesArray = is_array($files) ? $files : [$files];
                                        $filesArray = array_filter($filesArray);
                                    @endphp
                                    @if(count($filesArray) > 0)
                                        <div class="flex flex-col gap-2">
                                            @foreach($filesArray as $file)
                                                @if($field->type === 'pas_foto')
                                                    <div class="mb-2">
                                                        <img src="{{ asset($file) }}" style="width: 2.79cm; height: 3.81cm; object-fit: cover;" class="rounded border shadow-sm" alt="Pas Foto" />
                                                    </div>
                                                @elseif($field->type === 'gambar')
                                                    <div class="mb-2">
                                                        <img src="{{ asset($file) }}" style="max-width: 300px; max-height: 200px; object-fit: contain;" class="rounded border shadow-sm" alt="Gambar" />
                                                    </div>
                                                @endif
                                                <a href="{{ asset($file) }}" target="_blank" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-bold text-sm truncate">
                                                    <i class="fas fa-file-download"></i> Buka Berkas
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-400 italic">Tidak ada berkas diunggah</p>
                                    @endif
                                @elseif($field->type === 'table')
                                    @php
                                        $val = $data->form_data[$field->id] ?? null;
                                    @endphp
                                    @include('components.form-field.table-input', [
                                        'field' => $field,
                                        'val' => $val,
                                        'ro' => 'readonly disabled',
                                        'inputNamePrefix' => "form_fields[{$field->id}]"
                                    ])
                                @else
                                    <p class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                        @php 
                                            $val = $data->form_data[$field->id] ?? '-';
                                            if (is_array($val)) $val = implode(', ', $val);
                                        @endphp
                                        {{ $val ?: '-' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center">
                            <i class="fas fa-file-excel text-4xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500 italic">Tidak ada isian formulir tambahan pada pengajuan ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50 flex justify-end flex-shrink-0">
                <button type="button" onclick="closeGlobalFormModal()" class="px-6 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-300 transition-all text-sm shadow-sm">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function openGlobalFormModal() {
            document.getElementById('globalFormModal').classList.remove('hidden');
            document.getElementById('globalFormModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeGlobalFormModal() {
            document.getElementById('globalFormModal').classList.add('hidden');
            document.getElementById('globalFormModal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        function openPdfModal(url) {
            document.getElementById('pdfModal').classList.remove('hidden');
            document.getElementById('pdfModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
            // append #toolbar=0&navpanes=0 to hide download and print buttons in most standard pdf viewers
            document.getElementById('pdfViewer').src = url + '#toolbar=0&navpanes=0&scrollbar=0';
        }

        function closePdfModal() {
            document.getElementById('pdfModal').classList.add('hidden');
            document.getElementById('pdfModal').classList.remove('flex');
            document.body.style.overflow = '';
            document.getElementById('pdfViewer').src = '';
        }
    </script>

    <!-- PDF Modal (Read Only) -->
    <div id="pdfModal" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4 sm:p-6">
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-5xl h-[85vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-300">
            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-pdf text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-white">Pratinjau Dokumen Rekomendasi</h3>
                        <p class="text-xs text-gray-500">Mode Hanya Baca</p>
                    </div>
                </div>
                <button onclick="closePdfModal()" class="w-10 h-10 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 flex items-center justify-center text-gray-500 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="flex-1 bg-gray-100 dark:bg-gray-950 p-2 sm:p-4">
                <iframe id="pdfViewer" src="" class="w-full h-full rounded-xl border border-gray-200 dark:border-gray-800" title="PDF Preview"></iframe>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <x-pemohon.footer></x-pemohon.footer>
</x-pemohon.layout>
