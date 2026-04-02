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
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    <i class="mdi mdi-clock"></i> {{ $application->created_at->format('d M Y') }}
                </span>
            </div>
        </div>
    </div>

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
                                                @if ($isImage)
                                                    @php
                                                        // Extract the path after 'uploads/perijinan/' for the route
                                                        $routePath = str_replace('uploads/perijinan/', '', $file);
                                                    @endphp
                                                    <button
                                                        onclick="previewImage('{{ route('data-perijinan.download-file', rawurlencode($routePath)) }}', '{{ basename($file) }}')"
                                                        class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                                                        title="Preview">
                                                        <i class="mdi mdi-eye"></i>
                                                    </button>
                                                @endif
                                                @php
                                                    // Extract the path after 'uploads/perijinan/' for the route
                                                    $routePath = str_replace('uploads/perijinan/', '', $file);
                                                @endphp
                                                <a href="{{ route('data-perijinan.download-file', rawurlencode($routePath)) }}"
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

            <!-- Form Data -->
            @if ($application->perijinan->activeFormFields->count() > 0 && count($application->form_data) > 0)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-form-textbox text-blue-600"></i>
                            Data Pengajuan
                        </h2>
                    </div>
                    <div class="p-5 space-y-3">
                        @foreach ($application->perijinan->activeFormFields as $field)
                            @if (isset($application->form_data[$field->id]) &&
                                    !empty($application->form_data[$field->id]) &&
                                    $field->type !== 'file')
                                <div
                                    class="flex items-start gap-3 pb-3 border-b border-gray-100 dark:border-gray-700 last:border-0 last:pb-0">
                                    <div class="w-32 flex-shrink-0">
                                        <label
                                            class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ $field->label }}</label>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-gray-800 dark:text-white">
                                            @if (is_array($application->form_data[$field->id]))
                                                {{ implode(', ', $application->form_data[$field->id]) }}
                                            @else
                                                {{ $application->form_data[$field->id] }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Pemohon Info -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="mdi mdi-account text-blue-600"></i>
                        Informasi Pemohon
                    </h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Nama</label>
                            <p class="font-medium text-gray-800 dark:text-white">{{ $application->user->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Email</label>
                            <p class="font-medium text-gray-800 dark:text-white">{{ $application->user->email }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">No. Telepon</label>
                            <p class="font-medium text-gray-800 dark:text-white">{{ $application->user->no_hp ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Status</label>
                            <p class="font-medium text-gray-800 dark:text-white">
                                @if ($application->user->status_pemohon === 'badan_usaha')
                                    <i class="mdi mdi-building text-purple-600"></i> Badan Usaha
                                @else
                                    <i class="mdi mdi-account text-blue-600"></i> Perorangan
                                @endif
                            </p>
                        </div>
                        @if ($application->user->status_pemohon === 'badan_usaha')
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Perusahaan</label>
                                <p class="font-medium text-gray-800 dark:text-white">
                                    {{ $application->user->nama_perusahaan ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">NPWP</label>
                                <p class="font-medium text-gray-800 dark:text-white">
                                    {{ $application->user->npwp ?? '-' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Status & Timeline -->
        <div class="space-y-4">
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
                            @if ($application->catatan_perbaikan)
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-orange-200 dark:border-orange-700">
                                    <p class="text-xs text-orange-800 dark:text-orange-300 font-semibold mb-1">Catatan
                                        Perbaikan:</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $application->catatan_perbaikan }}</p>
                                </div>
                            @endif
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
                        $rolesWithoutAssignment = ['fo', 'bo', 'verifikator', 'kadin'];

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
                            // Role yang memerlukan assigned_user_id (Operator OPD, Kepala OPD)
                            $canValidate =
                                $currentValidasi->user_id === Auth::id() &&
                                $currentValidasi->status === 'pending' &&
                                !in_array($application->status, ['rejected', 'approved']);

                            // Cek apakah sudah validasi (untuk assigned user)
                            if ($currentValidasi->status !== 'pending' && $currentValidasi->user_id === Auth::id()) {
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
                                        
                                        // For assigned roles (operator_opd, kepala_opd), use assigned_user from validation_flow
                                        if (in_array($validatorRole, ['operator_opd', 'kepala_opd'])) {
                                            $validatorUser = $validasi->validationFlow->assignedUser;
                                        } 
                                        // For collective roles, use validator from data_perijinan_validasi
                                        elseif ($validasi->validator) {
                                            $validatorUser = $validasi->validator;
                                        }
                                    @endphp
                                    @if ($validatorUser)
                                        <div class="mt-2 flex items-center gap-2 {{ in_array($validatorRole, ['operator_opd', 'kepala_opd']) ? 'bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border-amber-200 dark:border-amber-800' : 'bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-blue-200 dark:border-blue-800' }} rounded-lg p-2 border">
                                            <div class="w-6 h-6 {{ in_array($validatorRole, ['operator_opd', 'kepala_opd']) ? 'bg-gradient-to-br from-amber-400 to-orange-500' : 'bg-gradient-to-br from-blue-400 to-indigo-500' }} rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="mdi mdi-{{ in_array($validatorRole, ['operator_opd', 'kepala_opd']) ? 'account-check' : 'account-group' }} text-white text-xs"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                                                    <i class="mdi mdi-{{ in_array($validatorRole, ['operator_opd', 'kepala_opd']) ? 'account-tie' : 'account-check' }} mr-1"></i>
                                                    {{ in_array($validatorRole, ['operator_opd', 'kepala_opd']) ? $validatorUser->name : 'Divalidasi oleh ' . ($validatorUser->role_label ?? 'Validator') }}
                                                </p>
                                                @if (in_array($validatorRole, ['operator_opd', 'kepala_opd']))
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
