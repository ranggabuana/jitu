<x-pemohon.layout>
    <x-slot:title>Perbaiki Pengajuan - JITU Banjarnegara</x-slot:title>

    <!-- Navbar -->
    <x-pemohon.navbar></x-pemohon.navbar>

    <!-- Main Content -->
    <main class="flex-1 max-w-5xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-br from-orange-600 via-orange-700 to-orange-800 rounded-3xl shadow-xl p-6 text-white">
            <div class="flex items-center gap-4">
                <a href="{{ route('pemohon.tracking.detail', $data->id) }}" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold mb-1">Perbaiki Pengajuan</h1>
                    <p class="text-orange-100 text-sm">{{ $data->perijinan->nama_perijinan }}</p>
                    <p class="text-orange-100 text-xs mt-1">No. Registrasi: {{ $data->no_registrasi }}</p>
                </div>
            </div>
        </div>

        <!-- Catatan Perbaikan Alert -->
        @if($data->catatan_perbaikan)
        <div class="bg-orange-50 border border-orange-200 rounded-2xl p-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-orange-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-orange-800 mb-2">Catatan Perbaikan dari Validator</h3>
                    <p class="text-orange-700">{{ $data->catatan_perbaikan }}</p>
                    <p class="text-xs text-orange-600 mt-2">
                        <i class="fas fa-info-circle"></i> Perbaiki pengajuan sesuai catatan di atas, lalu kirimkan kembali untuk validasi.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Error Alert -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-red-800">Mohon Perbaiki Error Berikut</h3>
                        <ul class="mt-2 text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('pemohon.pengajuan.update', $data->id) }}" method="POST" enctype="multipart/form-data"
            id="pengajuanForm" class="space-y-6">
            @csrf

            <!-- Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-orange-200 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-edit text-orange-600"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-800">Formulir Perbaikan</h2>
                        <p class="text-sm text-gray-500">Perbaiki data yang perlu diperbaiki dan lengkapi berkas yang diperlukan</p>
                    </div>
                </div>

                <!-- Form Fields -->
                @foreach($data->perijinan->activeFormFields as $field)
                    @php
                        $fieldValue = $data->form_data[$field->id] ?? '';
                        $fieldFiles = $data->form_files[$field->id] ?? [];
                    @endphp

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            {{ $field->label }}
                            @if($field->is_required)
                                <span class="text-red-500">*</span>
                            @endif
                        </label>

                        @if($field->type === 'textarea')
                            <textarea name="form_fields[{{ $field->id }}]" rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 @error('form_fields.'.$field->id) 'border-red-500' @enderror"
                                placeholder="Masukkan {{ strtolower($field->label) }}">{{ old('form_fields.'.$field->id, $fieldValue) }}</textarea>

                        @elseif($field->type === 'file')
                            <div class="space-y-2">
                                <input type="file" name="form_fields[{{ $field->id }}][]" multiple
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 @error('form_fields.'.$field->id) 'border-red-500' @enderror"
                                    accept="{{ $field->accepted_formats ?? '*' }}">
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle"></i> 
                                    File baru akan <strong>menambah</strong> file yang sudah ada. Hapus file lama jika perlu.
                                </p>
                                
                                @if(count($fieldFiles) > 0)
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-500 mb-2 font-semibold">File yang sudah diupload:</p>
                                        <div class="space-y-1" id="file-list-{{ $field->id }}">
                                            @foreach($fieldFiles as $index => $file)
                                                <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded-lg file-item"
                                                     data-file="{{ $file }}"
                                                     data-field-id="{{ $field->id }}">
                                                    <i class="fas fa-file text-orange-500"></i>
                                                    <span class="flex-1 truncate">{{ basename($file) }}</span>
                                                    <div class="flex items-center gap-1">
                                                        <a href="{{ asset($file) }}" target="_blank" 
                                                           class="text-blue-600 hover:text-blue-700 p-1" 
                                                           title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <button type="button" 
                                                                onclick="removeFile(this, '{{ $field->id }}', '{{ $file }}')"
                                                                class="text-red-600 hover:text-red-700 p-1" 
                                                                title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <!-- Hidden input untuk track file yang dihapus -->
                                        <input type="hidden" 
                                               name="deleted_files[{{ $field->id }}]" 
                                               id="deleted-files-{{ $field->id }}" 
                                               value="">
                                    </div>
                                @endif
                            </div>

                        @elseif($field->type === 'select')
                            <select name="form_fields[{{ $field->id }}]"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 @error('form_fields.'.$field->id) 'border-red-500' @enderror">
                                <option value="">Pilih {{ strtolower($field->label) }}</option>
                                @foreach(explode(',', $field->options ?? '') as $option)
                                    <option value="{{ trim($option) }}" 
                                        {{ old('form_fields.'.$field->id, $fieldValue) == trim($option) ? 'selected' : '' }}>
                                        {{ trim($option) }}
                                    </option>
                                @endforeach
                            </select>

                        @elseif($field->type === 'radio')
                            <div class="space-y-2">
                                @foreach(explode(',', $field->options ?? '') as $option)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="form_fields[{{ $field->id }}]" value="{{ trim($option) }}"
                                            {{ old('form_fields.'.$field->id, $fieldValue) == trim($option) ? 'checked' : '' }}
                                            class="text-orange-600 focus:ring-orange-500">
                                        <span class="text-gray-700">{{ trim($option) }}</span>
                                    </label>
                                @endforeach
                            </div>

                        @elseif($field->type === 'checkbox')
                            @php
                                $checkedValues = old('form_fields.'.$field->id, $fieldValue) ? explode(',', $fieldValue) : [];
                            @endphp
                            <div class="space-y-2">
                                @foreach(explode(',', $field->options ?? '') as $option)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="form_fields[{{ $field->id }}][]" value="{{ trim($option) }}"
                                            {{ in_array(trim($option), $checkedValues) ? 'checked' : '' }}
                                            class="text-orange-600 focus:ring-orange-500 rounded">
                                        <span class="text-gray-700">{{ trim($option) }}</span>
                                    </label>
                                @endforeach
                            </div>

                        @else
                            <input type="{{ $field->type }}" name="form_fields[{{ $field->id }}]"
                                value="{{ old('form_fields.'.$field->id, $fieldValue) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 @error('form_fields.'.$field->id) 'border-red-500' @enderror"
                                placeholder="Masukkan {{ strtolower($field->label) }}">
                        @endif

                        @error('form_fields.'.$field->id)
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4 pt-4">
                <a href="{{ route('pemohon.tracking.detail', $data->id) }}"
                    class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-semibold transition-colors shadow-lg">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Perbaikan
                </button>
            </div>
        </form>
    </main>

    <!-- Footer -->
    <x-pemohon.footer></x-pemohon.footer>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Remove file function
        function removeFile(button, fieldId, filePath) {
            Swal.fire({
                title: 'Hapus File?',
                text: 'File akan dihapus dari pengajuan. Anda yakin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Remove from DOM
                    const fileItem = button.closest('.file-item');
                    fileItem.style.opacity = '0';
                    fileItem.style.transform = 'translateX(-20px)';
                    fileItem.style.transition = 'all 0.3s ease';
                    
                    setTimeout(() => {
                        fileItem.remove();
                        
                        // Add to deleted files hidden input
                        const deletedInput = document.getElementById('deleted-files-' + fieldId);
                        const currentDeleted = deletedInput.value ? deletedInput.value.split(',') : [];
                        currentDeleted.push(filePath);
                        deletedInput.value = currentDeleted.join(',');
                        
                        // Show file list empty message if no files left
                        const fileList = document.getElementById('file-list-' + fieldId);
                        if (fileList.children.length === 0) {
                            fileList.innerHTML = '<p class="text-xs text-gray-400 italic text-center py-2">Semua file telah dihapus</p>';
                        }
                    }, 300);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'File berhasil dihapus',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }

        // Confirm before submit
        document.getElementById('pengajuanForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Check if Swal is available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Kirim Perbaikan?',
                    text: 'Pastikan semua data sudah diperbaiki dengan benar. Pengajuan akan dikirim kembali untuk validasi dari awal.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ea580c',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Kirim',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            } else {
                // Fallback if Swal is not available
                if (confirm('Kirim Perbaikan?\n\nPastikan semua data sudah diperbaiki dengan benar. Pengajuan akan dikirim kembali untuk validasi dari awal.')) {
                    this.submit();
                }
            }
        });
    </script>
</x-pemohon.layout>
