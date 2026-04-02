<x-pemohon.layout>
    <x-slot:title>Detail Tracking - {{ $data->no_registrasi }} - JITU Banjarnegara</x-slot:title>

    <!-- Navbar -->
    <x-pemohon.navbar></x-pemohon.navbar>

    <!-- Main Content -->
    <main class="flex-1 max-w-5xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
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
        <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
        </div>

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
                            $isCurrent = $index + 1 == $data->current_step && !$isCompleted;
                            $isPending = $validasi->status === 'pending';
                            $isRejected = $validasi->status === 'rejected';
                            $isRevision = $validasi->status === 'revision';
                        @endphp

                        <div class="relative flex items-start gap-4 pl-4">
                            <!-- Timeline Dot -->
                            <div class="relative z-10 w-16 h-16 rounded-full flex items-center justify-center shadow-lg flex-shrink-0 border-4 border-white
                                {{ $isCompleted ? 'bg-gradient-to-br from-green-500 to-green-600' : '' }}
                                {{ $isCurrent ? 'bg-gradient-to-br from-amber-500 to-amber-600 animate-pulse' : '' }}
                                {{ $isPending && !$isCurrent ? 'bg-gradient-to-br from-gray-300 to-gray-400' : '' }}
                                {{ $isRejected ? 'bg-gradient-to-br from-red-500 to-red-600' : '' }}
                                {{ $isRevision ? 'bg-gradient-to-br from-orange-500 to-orange-600' : '' }}
                            ">
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
                                @endif

                                @if ($validasi->validator)
                                    <div class="flex items-center gap-2 mt-2 text-sm text-gray-600">
                                        <i class="fas fa-user-circle text-amber-500"></i>
                                        <span>{{ $validasi->validator->name }}</span>
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
        <div class="flex justify-between gap-4 pt-4">
            <a href="{{ route('pemohon.tracking') }}"
                class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            @if ($data->status === 'perbaikan')
                <a href="{{ route('pemohon.pengajuan.edit', $data->id) }}"
                    class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-edit mr-2"></i> Perbaiki Pengajuan
                </a>
            @elseif ($data->status === 'approved')
                <button onclick="alert('Fitur download sertifikat akan segera tersedia')"
                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-download mr-2"></i> Download Sertifikat
                </button>
            @endif
        </div>
    </main>

    <!-- Footer -->
    <x-pemohon.footer></x-pemohon.footer>
</x-pemohon.layout>
