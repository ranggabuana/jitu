<x-layout>
    <x-slot:title>Detail {{ $application->no_registrasi }}</x-slot:title>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    <i class="mdi mdi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-bold text-lg text-gray-800 dark:text-white">{{ $application->no_registrasi }}</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $application->status_color }}">
                            {{ $application->status_label }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $application->perijinan->nama_perijinan }}</p>
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
            @if($application->form_files && count($application->form_files) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-white dark:from-purple-900/20 dark:to-gray-800">
                        <div class="flex items-center justify-between">
                            <h2 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <i class="mdi mdi-paperclip text-purple-600"></i>
                                Lampiran File ({{ collect($application->form_files)->flatten()->count() }})
                            </h2>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($application->form_files as $fieldId => $files)
                                @php
                                    $field = $application->perijinan->activeFormFields->firstWhere('id', $fieldId);
                                    $fieldName = $field ? $field->label : 'Field #' . $fieldId;
                                    $filesArray = is_array($files) ? $files : [$files];
                                @endphp
                                @foreach($filesArray as $file)
                                    @if($file && file_exists(public_path($file)))
                                        @php
                                            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            $isPdf = $extension === 'pdf';
                                            $isExcel = in_array($extension, ['xls', 'xlsx', 'csv']);
                                        @endphp
                                        <div class="group flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-purple-400 dark:hover:border-purple-600 hover:shadow-md transition-all bg-gray-50 dark:bg-gray-900/50">
                                            <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0
                                                {{ $isImage ? 'bg-green-100 dark:bg-green-900/30' : '' }}
                                                {{ $isPdf ? 'bg-red-100 dark:bg-red-900/30' : '' }}
                                                {{ $isExcel ? 'bg-yellow-100 dark:bg-yellow-900/30' : '' }}
                                                {{ !$isImage && !$isPdf && !$isExcel ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}
                                            ">
                                                @if($isImage)
                                                    <i class="mdi mdi-image text-green-600 dark:text-green-400"></i>
                                                @elseif($isPdf)
                                                    <i class="mdi mdi-file-pdf-box text-red-600 dark:text-red-400"></i>
                                                @elseif($isExcel)
                                                    <i class="mdi mdi-file-excel text-yellow-600 dark:text-yellow-400"></i>
                                                @else
                                                    <i class="mdi mdi-file text-blue-600 dark:text-blue-400"></i>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ basename($file) }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $fieldName }}</p>
                                            </div>
                                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                @if($isImage)
                                                    <button onclick="previewImage('{{ asset($file) }}', '{{ basename($file) }}')" 
                                                        class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors" title="Preview">
                                                        <i class="mdi mdi-eye"></i>
                                                    </button>
                                                @endif
                                                <a href="{{ asset($file) }}" target="_blank" download
                                                    class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg transition-colors" title="Unduh">
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
            @if($application->perijinan->activeFormFields->count() > 0 && count($application->form_data) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-form-textbox text-blue-600"></i>
                            Data Pengajuan
                        </h2>
                    </div>
                    <div class="p-5 space-y-3">
                        @foreach($application->perijinan->activeFormFields as $field)
                            @if(isset($application->form_data[$field->id]) && !empty($application->form_data[$field->id]) && $field->type !== 'file')
                                <div class="flex items-start gap-3 pb-3 border-b border-gray-100 dark:border-gray-700 last:border-0 last:pb-0">
                                    <div class="w-32 flex-shrink-0">
                                        <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ $field->label }}</label>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-gray-800 dark:text-white">
                                            @if(is_array($application->form_data[$field->id]))
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
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
                            <p class="font-medium text-gray-800 dark:text-white">{{ $application->user->no_hp ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Status</label>
                            <p class="font-medium text-gray-800 dark:text-white">
                                @if($application->user->status_pemohon === 'badan_usaha')
                                    <i class="mdi mdi-building text-purple-600"></i> Badan Usaha
                                @else
                                    <i class="mdi mdi-account text-blue-600"></i> Perorangan
                                @endif
                            </p>
                        </div>
                        @if($application->user->status_pemohon === 'badan_usaha')
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Perusahaan</label>
                                <p class="font-medium text-gray-800 dark:text-white">{{ $application->user->nama_perusahaan ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">NPWP</label>
                                <p class="font-medium text-gray-800 dark:text-white">{{ $application->user->npwp ?? '-' }}</p>
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
                    <div class="bg-white rounded-full h-2 transition-all duration-500" style="width: {{ $application->progress_percentage }}%"></div>
                </div>
                <div class="flex items-center justify-between text-sm text-blue-100">
                    <span>Tahap {{ $application->current_step }}</span>
                    <span>dari {{ $application->perijinan->activeValidationFlows->count() }}</span>
                </div>
            </div>

            @if($application->catatan_perbaikan)
                <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-400 p-4 rounded-r-xl">
                    <h4 class="font-bold text-orange-800 dark:text-orange-300 mb-2 flex items-center gap-2">
                        <i class="mdi mdi-alert-circle"></i>
                        Catatan Perbaikan
                    </h4>
                    <p class="text-orange-700 dark:text-orange-200 text-sm">{{ $application->catatan_perbaikan }}</p>
                </div>
            @endif

            @if($application->catatan_reject)
                <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 p-4 rounded-r-xl">
                    <h4 class="font-bold text-red-800 dark:text-red-300 mb-2 flex items-center gap-2">
                        <i class="mdi mdi-close-circle"></i>
                        Penolakan
                    </h4>
                    <p class="text-red-700 dark:text-red-200 text-sm">{{ $application->catatan_reject }}</p>
                </div>
            @endif

            <!-- Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="mdi mdi-timeline-text text-blue-600"></i>
                        Alur Validasi
                    </h2>
                </div>
                <div class="p-5">
                    <div class="space-y-0">
                        @foreach($application->validasiRecords as $index => $validasi)
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
                                @if(!$loop->last)
                                    <div class="absolute left-2.5 top-6 bottom-0 w-0.5 {{ $isCompleted ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                                @endif
                                <div class="w-5 h-5 rounded-full {{ $statusColors[$validasi->status] ?? 'bg-gray-300' }} flex items-center justify-center flex-shrink-0 z-10">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <h4 class="font-semibold text-gray-800 dark:text-white text-sm">
                                            {{ $validasi->validationFlow->role_label ?? 'Tahap ' . ($index + 1) }}
                                        </h4>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $validasi->status_color }}">
                                            {{ $validasi->status_label }}
                                        </span>
                                    </div>
                                    @if($validasi->catatan)
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">
                                            <i class="mdi mdi-comment-text"></i> {{ $validasi->catatan }}
                                        </p>
                                    @endif
                                    @if($validasi->validated_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-500">
                                            <i class="mdi mdi-clock"></i> {{ $validasi->validated_at->format('d M Y, H:i') }}
                                        </p>
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
    <div id="imagePreviewModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4" onclick="closeImagePreview()">
        <div class="relative max-w-4xl max-h-full">
            <button onclick="closeImagePreview()" class="absolute -top-8 right-0 text-white hover:text-gray-300 transition-colors">
                <i class="mdi mdi-close text-3xl"></i>
            </button>
            <img id="previewImageElement" src="" alt="Preview" class="max-w-full max-h-[85vh] rounded-lg shadow-2xl">
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
    </script>
</x-layout>
