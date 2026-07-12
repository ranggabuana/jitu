<x-pemohon.layout>
    <x-slot:title>Perbaiki Pengajuan - JITU Banjarnegara</x-slot:title>

    <!-- Navbar -->
    <x-pemohon.navbar></x-pemohon.navbar>

    <!-- Main Content -->
    <main class="flex-1 max-w-[95%] mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
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
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex justify-between items-center">
                            <span>
                                {{ $field->label }}
                                @if($field->is_required)
                                    <span class="text-red-500">*</span>
                                @endif
                            </span>
                            @if($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar')
                                <button type="button" onclick="openDokumenModal({{ $field->id }})" class="text-xs bg-purple-100 text-purple-700 hover:bg-purple-200 px-3 py-1 rounded-full transition-colors flex items-center gap-1 font-semibold">
                                    <i class="mdi mdi-folder-account"></i> Dokumen Saya
                                </button>
                            @endif
                        </label>

                        @if($field->type === 'textarea')
                            <textarea name="form_fields[{{ $field->id }}]" rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 @error('form_fields.'.$field->id) 'border-red-500' @enderror"
                                placeholder="Masukkan {{ strtolower($field->label) }}">{{ old('form_fields.'.$field->id, $fieldValue) }}</textarea>

                        @elseif($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar')
                            <div class="p-5 border-2 border-orange-100 rounded-2xl bg-orange-50/30 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Option 1: Upload from Device -->
                                    <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-orange-500 hover:bg-white transition-all cursor-pointer group"
                                        onclick="document.getElementById('file_{{ $field->id }}').click()">
                                        <input type="file" name="form_fields[{{ $field->id }}][]" id="file_{{ $field->id }}" multiple
                                            style="position: absolute; left: -9999px; opacity: 0;"
                                            accept="{{ $field->file_types ? implode(',', array_map(fn($t) => '.' . trim($t), explode(',', $field->file_types))) : (($field->type === 'pas_foto' || $field->type === 'gambar') ? '.jpg,.jpeg,.png' : ($field->accepted_formats ?? '*')) }}">
                                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:bg-orange-100 group-hover:text-orange-600 transition-colors text-gray-400">
                                            <i class="fas fa-cloud-upload-alt text-lg"></i>
                                        </div>
                                        <p class="text-xs font-bold text-gray-700">Tambah File Baru</p>
                                        <p class="text-[10px] text-gray-500 mt-1">Klik untuk upload file tambahan</p>
                                    </div>

                                    <!-- Option 2: Select from My Documents -->
                                    <div onclick="openDokumenModal({{ $field->id }})"
                                        class="relative border-2 border-dashed border-purple-300 rounded-xl p-4 text-center hover:border-purple-500 hover:bg-white transition-all cursor-pointer group bg-purple-50/50">
                                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:bg-purple-200 text-purple-600 transition-colors">
                                            <i class="fas fa-folder-open text-lg"></i>
                                        </div>
                                        <p class="text-xs font-bold text-purple-800">Ambil dari Dokumen Saya</p>
                                        <p class="text-[10px] text-purple-600 mt-1">Gunakan file yang sudah tersimpan</p>
                                        <span class="absolute -top-2 -right-2 bg-purple-600 text-white text-[8px] font-bold px-2 py-0.5 rounded-full shadow-sm">CEPAT</span>
                                    </div>
                                </div>

                                <input type="hidden" name="existing_files[{{ $field->id }}]" id="existing_file_{{ $field->id }}">
                                <div id="preview_{{ $field->id }}" class="mt-2 empty:hidden"></div>
                                
                                <div class="mt-2 space-y-1">
                                    @if ($field->help_text)
                                        <p class="text-[10px] text-gray-500 italic">{{ $field->help_text }}</p>
                                    @endif
                                    <p class="text-[10px] text-gray-500 flex items-center gap-1">
                                        <i class="fas fa-info-circle text-orange-500"></i>
                                        Format: {{ $field->file_types ?? 'Semua format' }} (Maks. {{ $field->max_file_size ?? '2MB' }})
                                    </p>
                                    <p class="text-[10px] text-gray-400 italic">
                                        * File baru akan <strong>menambah</strong> daftar file di bawah. Hapus file lama jika ingin menggantinya.
                                    </p>
                                </div>
                                
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

                        @elseif($field->type === 'table')
                            @include('components.form-field.table-input', [
                                'field' => $field,
                                'val' => old('form_fields.' . $field->id, $fieldValue),
                                'ro' => '',
                                'inputNamePrefix' => "form_fields[{$field->id}]"
                            ])
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

                <hr class="my-6 border-gray-200">

                <div class="mb-6">
                    <label for="catatan_pemohon" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-comment-dots text-orange-500 mr-1"></i> Catatan Perbaikan dari Anda (Opsional)
                    </label>
                    <p class="text-xs text-gray-500 mb-2">Tinggalkan pesan untuk validator terkait perbaikan yang telah Anda lakukan.</p>
                    <textarea name="catatan_pemohon" id="catatan_pemohon" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Contoh: Berkas KTP sudah saya ganti dengan pindaian yang lebih jelas..."></textarea>
                </div>
            </div>

            <!-- CAPTCHA (Inside Form) -->
            <div class="mt-8 pt-8 border-t border-gray-100">
                <div class="bg-orange-50 border border-orange-200 rounded-2xl p-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-shield-alt text-orange-600 mr-2"></i>Verifikasi Keamanan
                    </label>
                    <div class="flex items-end gap-3 max-w-sm">
                        <div class="flex-1">
                            <span id="captcha-question" class="block text-xl font-bold text-orange-600 mb-2">
                                {{ session('pengajuan_num1') }} + {{ session('pengajuan_num2') }} = ?
                            </span>
                            <input type="number"
                                id="captcha"
                                name="captcha"
                                required
                                placeholder="Hasil Penjumlahan"
                                class="w-full px-4 py-2 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all text-lg font-semibold @if(session('captcha_error')) border-red-500 @endif">
                            @if(session('captcha_error'))
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ session('captcha_error') }}
                                </p>
                            @endif
                        </div>
                        <button type="button"
                            id="refresh-captcha"
                            class="shrink-0 bg-orange-600 hover:bg-orange-700 text-white w-12 h-12 rounded-xl font-semibold transition-all flex items-center justify-center text-xl shadow-lg hover:shadow-xl">
                            <i class="fas fa-sync-alt" id="refresh-icon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pernyataan Tanggung Jawab -->
            <div class="bg-orange-50 border border-orange-200 rounded-2xl p-6">
                <label class="flex items-start gap-4 cursor-pointer group">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="pernyataan" id="check-pernyataan" value="1" required
                            class="w-5 h-5 text-orange-600 focus:ring-orange-500 rounded border-orange-300">
                    </div>
                    <div class="text-sm">
                        <span class="font-bold text-gray-800 block mb-1">Pernyataan Pertanggungjawaban</span>
                        <p class="text-gray-700 leading-relaxed">
                            Saya menyatakan bahwa data yang saya berikan dalam formulir perbaikan ini adalah benar and valid. Saya bersedia bertanggung jawab penuh secara hukum apabila di kemudian hari ditemukan ketidaksesuaian atau pemalsuan data pada berkas yang saya lampirkan.
                        </p>
                    </div>
                </label>
                @error('pernyataan')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4 pt-4">
                <a href="{{ route('pemohon.tracking.detail', $data->id) }}"
                    class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition-colors">
                    Batal
                </a>
                <button type="submit" id="btn-submit" disabled
                    class="px-8 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-semibold transition-colors shadow-lg opacity-50 cursor-not-allowed">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Perbaikan
                </button>
            </div>
        </form>
    </main>

    <!-- Footer -->
    <x-pemohon.footer></x-pemohon.footer>

    <!-- Modal Dokumen Saya -->
    <div id="modal-dokumen-saya" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden transform scale-95 transition-transform" id="modal-dokumen-saya-content">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="mdi mdi-folder-account text-purple-600"></i> Pilih dari Dokumen Saya
                </h3>
                <button type="button" onclick="closeDokumenModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 max-h-[60vh] overflow-y-auto">
                <input type="hidden" id="current_field_id_for_modal">
                @if(isset($userDokumens) && $userDokumens->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($userDokumens as $doc)
                            <div class="border border-gray-200 rounded-xl p-4 hover:border-purple-500 hover:bg-purple-50 cursor-pointer transition-colors group" onclick="selectDokumen({{ $doc->id }}, '{{ addslashes($doc->masterDokumen->nama_dokumen) }}', '{{ route('secure-file', ['filepath' => $doc->file_path]) }}', '{{ basename($doc->file_path) }}')">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0 group-hover:bg-purple-200">
                                        <i class="mdi mdi-file-document text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-sm">{{ $doc->masterDokumen->nama_dokumen }}</h4>
                                        <p class="text-xs text-gray-500 mt-1 truncate max-w-[150px]">{{ basename($doc->file_path) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="mdi mdi-folder-open text-2xl text-gray-400"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">Belum Ada Dokumen</h4>
                        <p class="text-sm text-gray-500 mt-1">Anda belum mengunggah dokumen apapun ke repositori "Dokumen Saya".</p>
                        <a href="{{ route('pemohon.dokumen.index') }}" target="_blank" class="inline-block mt-4 text-purple-600 hover:underline text-sm font-medium">Unggah Dokumen Sekarang</a>
                    </div>
                @endif
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end">
                <button type="button" onclick="closeDokumenModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Tutup</button>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Modal Dokumen Saya Logic
        const modalDokumenSaya = document.getElementById('modal-dokumen-saya');
        const modalDokumenSayaContent = document.getElementById('modal-dokumen-saya-content');
        
        // Responsibility statement checkbox logic
        document.addEventListener('DOMContentLoaded', function() {
            const checkPernyataan = document.getElementById('check-pernyataan');
            const btnSubmit = document.getElementById('btn-submit');

            if (checkPernyataan && btnSubmit) {
                const toggleSubmit = () => {
                    if (checkPernyataan.checked) {
                        btnSubmit.disabled = false;
                        btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        btnSubmit.disabled = true;
                        btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                };

                checkPernyataan.addEventListener('change', toggleSubmit);
                
                // Initial check in case of validation errors and old input
                toggleSubmit();
            }
        });

        function openDokumenModal(fieldId) {
            document.getElementById('current_field_id_for_modal').value = fieldId;
            modalDokumenSaya.classList.remove('hidden');
            modalDokumenSaya.classList.add('flex');
            setTimeout(() => {
                modalDokumenSayaContent.classList.remove('scale-95');
                modalDokumenSayaContent.classList.add('scale-100');
            }, 10);
        }

        function closeDokumenModal() {
            modalDokumenSayaContent.classList.remove('scale-100');
            modalDokumenSayaContent.classList.add('scale-95');
            setTimeout(() => {
                modalDokumenSaya.classList.remove('flex');
                modalDokumenSaya.classList.add('hidden');
            }, 200);
        }

        function selectDokumen(docId, docName, fileUrl, fileName) {
            const fieldId = document.getElementById('current_field_id_for_modal').value;
            
            document.getElementById('existing_file_' + fieldId).value = docId;
            
            const fileInput = document.getElementById('file_' + fieldId);
            if(fileInput) fileInput.value = '';
            
            const preview = document.getElementById('preview_' + fieldId);
            if(preview) {
                preview.innerHTML = `
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 flex items-center gap-3 relative mb-2">
                        <i class="mdi mdi-folder-account text-purple-600 text-xl"></i>
                        <div class="flex-1 min-w-0 text-left">
                            <p class="text-sm font-bold text-purple-800 truncate">${docName}</p>
                            <p class="text-xs text-purple-600 truncate">${fileName}</p>
                        </div>
                        <button type="button" onclick="removeSelectedDokumen(${fieldId})" class="text-red-500 hover:text-red-700 p-1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            }
            
            closeDokumenModal();
        }

        function removeSelectedDokumen(fieldId) {
            document.getElementById('existing_file_' + fieldId).value = '';
            const preview = document.getElementById('preview_' + fieldId);
            if(preview) preview.innerHTML = '';
        }

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

        // Captcha Refresh
        const refreshBtn = document.getElementById('refresh-captcha');
        const refreshIcon = document.getElementById('refresh-icon');
        const captchaQuestion = document.getElementById('captcha-question');
        const captchaInput = document.getElementById('captcha');

        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                // Show loading state
                refreshIcon.classList.add('fa-spin');
                refreshBtn.disabled = true;

                fetch('{{ route("pemohon.api.refresh-pengajuan-captcha") }}')
                    .then(response => response.json())
                    .then(data => {
                        captchaQuestion.textContent = `${data.num1} + ${data.num2} = ?`;
                        captchaInput.value = '';
                        captchaInput.focus();

                        // Reset button
                        refreshIcon.classList.remove('fa-spin');
                        refreshBtn.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        refreshIcon.classList.remove('fa-spin');
                        refreshBtn.disabled = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal refresh CAPTCHA. Silakan coba lagi.',
                            confirmButtonColor: '#ea580c',
                            confirmButtonText: 'OK'
                        });
                    });
            });
        }
    </script>
</x-pemohon.layout>
