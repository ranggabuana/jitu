<x-layout>
    <x-slot:title>Detail Perijinan - {{ $application->no_registrasi }}</x-slot:title>

    <div class="mb-6">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium">
            <i class="mdi mdi-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    <!-- Header Card -->
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 rounded-2xl shadow-xl p-6 text-white mb-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="font-mono text-lg">{{ $application->no_registrasi }}</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20">
                        {{ $application->status_label }}
                    </span>
                </div>
                <h1 class="text-2xl font-bold">{{ $application->perijinan->nama_perijinan }}</h1>
                <p class="text-blue-100 text-sm mt-1">Diajukan pada {{ $application->created_at->format('d M Y, H:i') }} WIB</p>
            </div>
            <div class="text-right">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="mdi mdi-file-document text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Application Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Pemohon Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <i class="mdi mdi-account text-blue-600"></i>
                    Informasi Pemohon
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Nama Lengkap</label>
                        <p class="font-semibold text-gray-800 dark:text-white">{{ $application->user->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Email</label>
                        <p class="font-semibold text-gray-800 dark:text-white">{{ $application->user->email }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">No. Telepon</label>
                        <p class="font-semibold text-gray-800 dark:text-white">{{ $application->user->no_hp ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Status Pemohon</label>
                        <p class="font-semibold text-gray-800 dark:text-white">
                            @if($application->user->status_pemohon === 'badan_usaha')
                                <i class="mdi mdi-building text-purple-600"></i> Badan Usaha
                            @else
                                <i class="mdi mdi-account text-blue-600"></i> Perorangan
                            @endif
                        </p>
                    </div>
                    @if($application->user->status_pemohon === 'badan_usaha')
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Nama Perusahaan</label>
                            <p class="font-semibold text-gray-800 dark:text-white">{{ $application->user->nama_perusahaan ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">NPWP</label>
                            <p class="font-semibold text-gray-800 dark:text-white">{{ $application->user->npwp ?? '-' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Data -->
            @if($application->perijinan->activeFormFields->count() > 0 && count($application->form_data) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="mdi mdi-form-textbox text-blue-600"></i>
                        Data Pengajuan
                    </h2>
                    <div class="space-y-4">
                        @foreach($application->perijinan->activeFormFields as $field)
                            @if(isset($application->form_data[$field->id]) && !empty($application->form_data[$field->id]))
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0">
                                    <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ $field->label }}</label>
                                    <p class="text-gray-800 dark:text-white mt-1">
                                        @if($field->type === 'file')
                                            @php
                                                $files = is_array($application->form_data[$field->id]) 
                                                    ? $application->form_data[$field->id] 
                                                    : [$application->form_data[$field->id]];
                                            @endphp
                                            <div class="space-y-2">
                                                @foreach($files as $file)
                                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" 
                                                       class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                                                        <i class="mdi mdi-file-download"></i>
                                                        {{ basename($file) }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @elseif(is_array($application->form_data[$field->id]))
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

            <!-- Validation Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <i class="mdi mdi-progress-download text-blue-600"></i>
                    Alur Validasi
                </h2>
                <div class="relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-8 top-4 bottom-4 w-0.5 bg-gradient-to-b from-blue-400 via-purple-400 to-pink-400 rounded-full"></div>

                    <div class="space-y-6">
                        @foreach ($application->validasiRecords as $index => $validasi)
                            @php
                                $isCompleted = $validasi->status === 'approved';
                                $isCurrent = $index + 1 == $application->current_step && !$isCompleted;
                                $isPending = $validasi->status === 'pending';
                                $isRejected = $validasi->status === 'rejected';
                                $isRevision = $validasi->status === 'revision';
                            @endphp

                            <div class="relative flex items-start gap-4 pl-4">
                                <!-- Timeline Dot -->
                                <div class="relative z-10 w-16 h-16 rounded-full flex items-center justify-center shadow-lg flex-shrink-0 border-4 border-white
                                    {{ $isCompleted ? 'bg-gradient-to-br from-green-500 to-green-600' : '' }}
                                    {{ $isCurrent ? 'bg-gradient-to-br from-blue-500 to-blue-600 animate-pulse' : '' }}
                                    {{ $isPending && !$isCurrent ? 'bg-gradient-to-br from-gray-300 to-gray-400' : '' }}
                                    {{ $isRejected ? 'bg-gradient-to-br from-red-500 to-red-600' : '' }}
                                    {{ $isRevision ? 'bg-gradient-to-br from-orange-500 to-orange-600' : '' }}
                                ">
                                    @if ($isCompleted)
                                        <i class="mdi mdi-check text-white text-2xl"></i>
                                    @elseif ($isRejected)
                                        <i class="mdi mdi-close text-white text-2xl"></i>
                                    @elseif ($isRevision)
                                        <i class="mdi mdi-alert text-white text-2xl"></i>
                                    @else
                                        <span class="text-white font-bold text-lg">{{ $index + 1 }}</span>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1 bg-gradient-to-br from-gray-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border-2
                                    {{ $isCompleted ? 'border-green-200 dark:border-green-800' : '' }}
                                    {{ $isCurrent ? 'border-blue-300 dark:border-blue-700 shadow-lg' : '' }}
                                    {{ $isPending && !$isCurrent ? 'border-gray-200 dark:border-gray-600' : '' }}
                                    {{ $isRejected ? 'border-red-200 dark:border-red-800' : '' }}
                                    {{ $isRevision ? 'border-orange-200 dark:border-orange-800' : '' }}
                                ">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <h3 class="font-bold text-gray-800 dark:text-white">
                                                {{ $validasi->validationFlow->role_label ?? 'Tahap ' . ($index + 1) }}
                                            </h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $validasi->validationFlow->description ?? 'Proses validasi' }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $validasi->status_color }}">
                                            {{ $validasi->status_label }}
                                        </span>
                                    </div>

                                    @if ($validasi->catatan)
                                        <div class="mt-3 bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                                <i class="mdi mdi-comment-text text-blue-500 mr-2"></i>
                                                <strong>Catatan:</strong> {{ $validasi->catatan }}
                                            </p>
                                        </div>
                                    @endif

                                    @if ($validasi->validated_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            <i class="mdi mdi-clock mr-1"></i>
                                            Divalidasi pada {{ $validasi->validated_at->format('d M Y, H:i') }} WIB
                                        </p>
                                    @endif

                                    @if ($validasi->validator)
                                        <div class="flex items-center gap-2 mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            <i class="mdi mdi-account-circle text-blue-500"></i>
                                            <span>{{ $validasi->validator->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Status & Actions -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Status Pengajuan</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Status Saat Ini</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $application->status_color }}">
                            {{ $application->status_label }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Tahap Validasi</span>
                        <span class="font-semibold text-gray-800 dark:text-white">{{ $application->current_step }} / {{ $application->perijinan->activeValidationFlows->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Progress</span>
                        <span class="font-semibold text-gray-800 dark:text-white">{{ $application->progress_percentage }}%</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Tanggal Pengajuan</span>
                        <span class="font-semibold text-gray-800 dark:text-white">{{ $application->created_at->format('d M Y') }}</span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500"
                            style="width: {{ $application->progress_percentage }}%"></div>
                    </div>
                </div>
            </div>

            @if($application->catatan_perbaikan)
                <!-- Catatan Perbaikan -->
                <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-400 p-4 rounded-r-xl">
                    <h3 class="font-bold text-orange-800 dark:text-orange-300 mb-2 flex items-center gap-2">
                        <i class="mdi mdi-alert-circle"></i>
                        Catatan Perbaikan
                    </h3>
                    <p class="text-orange-700 dark:text-orange-200 text-sm">{{ $application->catatan_perbaikan }}</p>
                </div>
            @endif

            @if($application->catatan_reject)
                <!-- Catatan Penolakan -->
                <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 p-4 rounded-r-xl">
                    <h3 class="font-bold text-red-800 dark:text-red-300 mb-2 flex items-center gap-2">
                        <i class="mdi mdi-close-circle"></i>
                        Penolakan
                    </h3>
                    <p class="text-red-700 dark:text-red-200 text-sm">{{ $application->catatan_reject }}</p>
                </div>
            @endif
        </div>
    </div>
</x-layout>
