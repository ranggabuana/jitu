<x-pemohon.layout>
    <x-slot:title>Ajukan {{ $perijinan->nama_perijinan }} - JITU Banjarnegara</x-slot:title>

    <!-- Navbar -->
    <x-pemohon.navbar></x-pemohon.navbar>

    <!-- Main Content -->
    <main class="flex-1 max-w-[95%] mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-br from-amber-600 via-amber-700 to-amber-800 rounded-3xl shadow-xl p-6 text-white">
            <div class="flex items-center gap-4">
                <a href="{{ route('pemohon.perijinan') }}" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold mb-1">Formulir Pengajuan</h1>
                    <p class="text-amber-100 text-sm">{{ $perijinan->nama_perijinan }}</p>
                </div>
            </div>
        </div>

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

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-red-800">Error</h3>
                        <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @php
            $sourceApp = $pembetulanFromApp ?? $renewFromApp ?? null;
        @endphp
        <!-- Form -->
        <form action="{{ route('pemohon.pengajuan.store') }}" method="POST" enctype="multipart/form-data"
            id="pengajuanForm" class="space-y-6">
            @csrf
            <input type="hidden" name="perijinan_id" value="{{ $perijinan->id }}">
            @if(isset($renewFromApp))
                <input type="hidden" name="renew_from" value="{{ $renewFromApp->id }}">
            @endif
            @if(isset($pembetulanFromApp))
                <input type="hidden" name="pembetulan_from" value="{{ $pembetulanFromApp->id }}">
                
                <!-- Alasan Pembetulan Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-red-200 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-800">Alasan Pembetulan</h2>
                            <p class="text-sm text-gray-500">Berikan penjelasan detail alasan Anda mengajukan pembetulan izin ini</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Pembetulan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alasan_pembetulan" rows="4" required
                            class="w-full px-4 py-3 border @error('alasan_pembetulan') border-red-500 @else border-gray-300 @endif rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all outline-none"
                            placeholder="Tulis alasan pembetulan di sini (wajib diisi)...">{{ old('alasan_pembetulan', $pembetulanFromApp->alasan_pembetulan ?? '') }}</textarea>
                        @error('alasan_pembetulan')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif

            <!-- Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-info-circle text-amber-600"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-800">Informasi Pengajuan</h2>
                        <p class="text-sm text-gray-500">Pastikan semua data yang diisi benar dan lengkap</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-amber-50 rounded-xl p-4">
                    <div>
                        <p class="text-sm text-gray-600">Jenis Perizinan</p>
                        <p class="font-semibold text-gray-800">{{ $perijinan->nama_perijinan }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tahap Validasi</p>
                        <p class="font-semibold text-gray-800">{{ $perijinan->activeValidationFlows->count() }} Tahap
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Pemohon</p>
                        <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Dynamic Form Fields Section -->
            @if ($perijinan->activeFormFields->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-amber-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-700 to-amber-800 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                            <h2 class="font-bold text-white">Formulir Perizinan</h2>
                            <span class="bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full">
                                {{ $perijinan->activeFormFields->count() }} Field
                            </span>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        @if ($perijinan->activeFormFields->count() === 0)
                            <p class="text-gray-500 text-center py-4">Tidak ada formulir untuk perizinan ini.</p>
                        @endif
                        
                        @foreach ($perijinan->activeFormFields as $index => $field)
                            <div class="form-field-group" data-order="{{ $field->order }}">
                                @if ($field->type === 'text' || $field->type === 'email' || $field->type === 'number' || $field->type === 'date')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $field->label }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <input type="{{ $field->type }}" name="form_fields[{{ $field->id }}]"
                                            value="{{ old('form_fields.' . $field->id, isset($sourceApp) ? ($sourceApp->form_data[$field->id] ?? '') : '') }}"
                                            class="w-full px-4 py-3 border @error('form_fields.' . $field->id) border-red-500 @else border-gray-300 @endif rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                            placeholder="{{ $field->placeholder ?? 'Masukkan ' . strtolower($field->label) }}">
                                        @error('form_fields.' . $field->id)
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                        @if ($field->help_text && !$errors->has('form_fields.' . $field->id))
                                            <p class="mt-1 text-xs text-gray-500">{{ $field->help_text }}</p>
                                        @endif
                                    </div>
                                @elseif ($field->type === 'textarea')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $field->label }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <textarea name="form_fields[{{ $field->id }}]" rows="4"
                                            class="w-full px-4 py-3 border @error('form_fields.' . $field->id) border-red-500 @else border-gray-300 @endif rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                            placeholder="{{ $field->placeholder ?? 'Masukkan ' . strtolower($field->label) }}">{{ old('form_fields.' . $field->id, isset($sourceApp) ? ($sourceApp->form_data[$field->id] ?? '') : '') }}</textarea>
                                        @error('form_fields.' . $field->id)
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                        @if ($field->help_text && !$errors->has('form_fields.' . $field->id))
                                            <p class="mt-1 text-xs text-gray-500">{{ $field->help_text }}</p>
                                        @endif
                                    </div>
                                @elseif ($field->type === 'select')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $field->label }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <select name="form_fields[{{ $field->id }}]"
                                            class="w-full px-4 py-3 border @error('form_fields.' . $field->id) border-red-500 @else border-gray-300 @endif rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                            <option value="">Pilih {{ strtolower($field->label) }}</option>
                                            @if (is_array($field->options))
                                                @foreach ($field->options as $option)
                                                    <option value="{{ $option }}"
                                                        {{ old('form_fields.' . $field->id, isset($sourceApp) ? ($sourceApp->form_data[$field->id] ?? '') : '') == $option ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('form_fields.' . $field->id)
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                        @if ($field->help_text && !$errors->has('form_fields.' . $field->id))
                                            <p class="mt-1 text-xs text-gray-500">{{ $field->help_text }}</p>
                                        @endif
                                    </div>
                                @elseif ($field->type === 'radio')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $field->label }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <div class="space-y-2">
                                            @if (is_array($field->options))
                                                @foreach ($field->options as $option)
                                                    <label class="flex items-center gap-2 cursor-pointer">
                                                        <input type="radio" name="form_fields[{{ $field->id }}]"
                                                            value="{{ $option }}"
                                                            @if (old('form_fields.' . $field->id, isset($sourceApp) ? ($sourceApp->form_data[$field->id] ?? '') : '') == $option) checked @endif
                                                            class="w-4 h-4 text-amber-600 focus:ring-amber-500">
                                                        <span class="text-sm text-gray-700">{{ $option }}</span>
                                                    </label>
                                                @endforeach
                                            @endif
                                        </div>
                                        @error('form_fields.' . $field->id)
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                        @if ($field->help_text && !$errors->has('form_fields.' . $field->id))
                                            <p class="mt-1 text-xs text-gray-500">{{ $field->help_text }}</p>
                                        @endif
                                    </div>
                                @elseif ($field->type === 'checkbox')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $field->label }}
                                        </label>
                                        <div class="space-y-2">
                                            @if (is_array($field->options))
                                                @foreach ($field->options as $option)
                                                    <label class="flex items-center gap-2 cursor-pointer">
                                                        <input type="checkbox"
                                                            name="form_fields[{{ $field->id }}][]"
                                                            value="{{ $option }}"
                                                            @if (in_array($option, old('form_fields.' . $field->id, isset($sourceApp) ? ($sourceApp->form_data[$field->id] ?? []) : []))) checked @endif
                                                            class="w-4 h-4 text-amber-600 focus:ring-amber-500 rounded">
                                                        <span class="text-sm text-gray-700">{{ $option }}</span>
                                                    </label>
                                                @endforeach
                                            @endif
                                        </div>
                                        @error('form_fields.' . $field->id)
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                        @if ($field->help_text && !$errors->has('form_fields.' . $field->id))
                                            <p class="mt-1 text-xs text-gray-500">{{ $field->help_text }}</p>
                                        @endif
                                    </div>
                                @elseif ($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar')
                                    <div class="p-5 border-2 border-amber-100 rounded-2xl bg-amber-50/30">
                                        <label class="block text-sm font-bold text-gray-800 mb-3">
                                            {{ $field->label }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <!-- Option 1: Upload from Device -->
                                            <div id="file_container_{{ $field->id }}"
                                                class="relative border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-amber-500 hover:bg-white transition-all cursor-pointer group"
                                                onclick="document.getElementById('file_{{ $field->id }}').click()">
                                                <input type="file" name="form_files[{{ $field->id }}][]"
                                                    id="file_{{ $field->id }}"
                                                    accept="{{ $field->file_types ? implode(',', array_map(fn($t) => '.' . trim($t), explode(',', $field->file_types))) : (($field->type === 'pas_foto' || $field->type === 'gambar') ? '.jpg,.jpeg,.png' : '*') }}"
                                                    style="position: absolute; left: -9999px; opacity: 0;"
                                                    multiple
                                                    onchange="previewFiles(this, {{ $field->id }})">
                                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:bg-amber-100 group-hover:text-amber-600 transition-colors text-gray-400">
                                                    <i class="fas fa-cloud-upload-alt text-lg"></i>
                                                </div>
                                                <p class="text-xs font-bold text-gray-700">Upload dari Perangkat</p>
                                                <p class="text-[10px] text-gray-500 mt-1">Klik atau seret file ke sini</p>
                                            </div>

                                            <!-- Option 2: Select from My Documents -->
                                            <div onclick="openDokumenModal({{ $field->id }})"
                                                class="relative border-2 border-dashed border-purple-300 rounded-xl p-4 text-center hover:border-purple-500 hover:bg-white transition-all cursor-pointer group bg-purple-50/50">
                                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:bg-purple-200 text-purple-600 transition-colors">
                                                    <i class="fas fa-folder-open text-lg"></i>
                                                </div>
                                                <p class="text-xs font-bold text-purple-800">Gunakan Dokumen Saya</p>
                                                <p class="text-[10px] text-purple-600 mt-1">Ambil dari file yang sudah tersimpan</p>
                                                
                                                <!-- Badge for guidance -->
                                                <span class="absolute -top-2 -right-2 bg-purple-600 text-white text-[8px] font-bold px-2 py-0.5 rounded-full shadow-sm">CEPAT</span>
                                            </div>
                                        </div>

                                        <input type="hidden" name="existing_files[{{ $field->id }}]" id="existing_file_{{ $field->id }}">
                                        <div id="preview_{{ $field->id }}" class="mt-2 empty:hidden">
                                            @if(isset($sourceApp) && !empty($sourceApp->form_files[$field->id]))
                                                @php
                                                    $oldFileArray = (array) $sourceApp->form_files[$field->id];
                                                @endphp
                                                <div class="space-y-2" id="old_files_container_{{ $field->id }}">
                                                    @foreach($oldFileArray as $oldIdx => $oldFilePath)
                                                        @php
                                                            $oldFileName = basename($oldFilePath);
                                                        @endphp
                                                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 flex items-center gap-3 relative" id="old_file_item_{{ $field->id }}_{{ $oldIdx }}">
                                                            <input type="hidden" name="old_files[{{ $field->id }}][]" value="{{ $oldFilePath }}">
                                                            <i class="fas fa-file-alt text-amber-600 text-xl"></i>
                                                            <div class="flex-1 min-w-0 text-left">
                                                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $oldFileName }}</p>
                                                                <p class="text-[10px] text-gray-500">Berkas sebelumnya</p>
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <a href="{{ asset($oldFilePath) }}" target="_blank" class="text-amber-600 hover:text-amber-800 p-1" title="Lihat Berkas">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <button type="button" onclick="removeOldFile({{ $field->id }}, {{ $oldIdx }})" class="text-red-500 hover:text-red-700 p-1" title="Hapus Berkas">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        
                                        @error('form_files.' . $field->id)
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror

                                        @if (!$errors->has('form_files.' . $field->id))
                                            <div class="mt-2 space-y-1">
                                                @if ($field->help_text)
                                                    <p class="text-[10px] text-gray-500 italic">{{ $field->help_text }}</p>
                                                @endif
                                                <p class="text-[10px] text-gray-500 flex items-center gap-1">
                                                    <i class="fas fa-info-circle text-amber-500"></i>
                                                    Format: {{ $field->file_types ?? 'Semua format' }} (Maks. {{ $field->max_file_size ?? '2MB' }})
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @elseif ($field->type === 'table')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $field->label }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        @include('components.form-field.table-input', [
                                            'field' => $field,
                                            'val' => old('form_fields.' . $field->id, isset($sourceApp) ? ($sourceApp->form_data[$field->id] ?? null) : null),
                                            'ro' => '',
                                            'inputNamePrefix' => "form_fields[{$field->id}]"
                                        ])
                                        @error('form_fields.' . $field->id)
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                        @if ($field->help_text && !$errors->has('form_fields.' . $field->id))
                                            <p class="mt-1 text-xs text-gray-500">{{ $field->help_text }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- CAPTCHA (Inside Form) -->
                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-shield-alt text-amber-600 mr-2"></i>Verifikasi Keamanan
                            </label>
                            <div class="flex items-end gap-3 max-w-sm">
                                <div class="flex-1">
                                    <span id="captcha-question" class="block text-xl font-bold text-amber-600 mb-2">
                                        {{ session('pengajuan_num1') }} + {{ session('pengajuan_num2') }} = ?
                                    </span>
                                    <input type="number"
                                        id="captcha"
                                        name="captcha"
                                        required
                                        placeholder="Hasil Penjumlahan"
                                        class="w-full px-4 py-2 border-2 border-amber-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all text-lg font-semibold @if(session('captcha_error')) border-red-500 @endif">
                                    @if(session('captcha_error'))
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ session('captcha_error') }}
                                        </p>
                                    @endif
                                </div>
                                <button type="button"
                                    id="refresh-captcha"
                                    class="shrink-0 bg-amber-600 hover:bg-amber-700 text-white w-12 h-12 rounded-xl font-semibold transition-all flex items-center justify-center text-xl shadow-lg hover:shadow-xl">
                                    <i class="fas fa-sync-alt" id="refresh-icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- If no active fields, still show CAPTCHA -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-6">
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-shield-alt text-amber-600 mr-2"></i>Verifikasi Keamanan
                        </label>
                        <div class="flex items-end gap-3 max-w-sm">
                            <div class="flex-1">
                                <span id="captcha-question" class="block text-xl font-bold text-amber-600 mb-2">
                                    {{ session('pengajuan_num1') }} + {{ session('pengajuan_num2') }} = ?
                                </span>
                                <input type="number"
                                    id="captcha"
                                    name="captcha"
                                    required
                                    placeholder="Hasil Penjumlahan"
                                    class="w-full px-4 py-2 border-2 border-amber-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all text-lg font-semibold @if(session('captcha_error')) border-red-500 @endif">
                                @if(session('captcha_error'))
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ session('captcha_error') }}
                                    </p>
                                @endif
                            </div>
                            <button type="button"
                                id="refresh-captcha"
                                class="shrink-0 bg-amber-600 hover:bg-amber-700 text-white w-12 h-12 rounded-xl font-semibold transition-all flex items-center justify-center text-xl shadow-lg hover:shadow-xl">
                                <i class="fas fa-sync-alt" id="refresh-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Persyaratan Info -->
            @if ($perijinan->persyaratan)
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-blue-600"></i>
                        </div>
                        <h2 class="font-bold text-gray-800">Persyaratan</h2>
                    </div>
                    <div class="text-gray-700 text-sm">
                        {!! $perijinan->persyaratan !!}
                    </div>
                </div>
            @endif

            <!-- Pernyataan Tanggung Jawab -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                <label class="flex items-start gap-4 cursor-pointer group">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="pernyataan" id="check-pernyataan" value="1" required
                            class="w-5 h-5 text-amber-600 focus:ring-amber-500 rounded border-amber-300">
                    </div>
                    <div class="text-sm">
                        <span class="font-bold text-gray-800 block mb-1">Pernyataan Pertanggungjawaban</span>
                        <p class="text-gray-700 leading-relaxed">
                            Saya menyatakan bahwa data yang saya berikan dalam formulir pengajuan ini adalah benar dan valid. Saya bersedia bertanggung jawab penuh secara hukum apabila di kemudian hari ditemukan ketidaksesuaian atau pemalsuan data pada berkas yang saya lampirkan.
                        </p>
                    </div>
                </label>
                @error('pernyataan')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between gap-4 pt-4">
                <a href="{{ route('pemohon.perijinan') }}"
                    class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Batal
                </a>
                <button type="button" id="btn-submit-proxy" disabled
                    class="px-8 py-3 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl flex items-center gap-2 opacity-50 cursor-not-allowed">
                    <i class="fas fa-paper-plane"></i>
                    <span>Kirim Pengajuan</span>
                </button>
                <button type="submit" id="btn-submit" class="hidden"></button>
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
                @if($userDokumens->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($userDokumens as $doc)
                            <div class="border border-gray-200 rounded-xl p-4 hover:border-purple-500 hover:bg-purple-50 cursor-pointer transition-colors group" onclick="selectDokumen({{ $doc->id }}, '{{ addslashes($doc->masterDokumen->nama_dokumen) }}', '{{ asset($doc->file_path) }}', '{{ basename($doc->file_path) }}')">
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

    <!-- Scripts -->
    <script>
        // Modal logic
        const modalDokumenSaya = document.getElementById('modal-dokumen-saya');
        const modalDokumenSayaContent = document.getElementById('modal-dokumen-saya-content');
        
        // Responsibility statement checkbox logic
        document.addEventListener('DOMContentLoaded', function() {
            const checkPernyataan = document.getElementById('check-pernyataan');
            const btnSubmitProxy = document.getElementById('btn-submit-proxy');
            const btnSubmitReal = document.getElementById('btn-submit');

            if (checkPernyataan && btnSubmitProxy) {
                const toggleSubmit = () => {
                    if (checkPernyataan.checked) {
                        btnSubmitProxy.disabled = false;
                        btnSubmitProxy.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        btnSubmitProxy.disabled = true;
                        btnSubmitProxy.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                };

                checkPernyataan.addEventListener('change', toggleSubmit);
                
                // Initial check in case of validation errors and old input
                toggleSubmit();

                btnSubmitProxy.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Show loading state
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang memeriksa status pajak daerah (KSWP)',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Call KSWP Check API
                    fetch('{{ route('pemohon.kswp.check') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();

                        if (data.status === 'ERROR') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Sistem',
                                text: data.message
                            });
                            return;
                        }

                        const status = data.status;
                        let message = data.message;
                        const statusPemohon = '{{ $user->status_pemohon }}';
                        const labelPajak = statusPemohon === 'badan_usaha' ? 'NPWP' : 'NIK';
                        
                        // Clean up message from API if it mentions NIK for a badan_usaha user
                        if (statusPemohon === 'badan_usaha' && message) {
                            message = message.replace(/NIK/g, 'NPWP').replace(/nik/g, 'npwp');
                        }
                        
                        // Check for both NIK and NPWP prefixes in status
                        const isLunas = status === 'LUNAS';
                        const isTidakTerdaftar = status === 'NIK TIDAK TERDAFTAR' || status === 'NPWP TIDAK TERDAFTAR';
                        const isInvalid = status === 'NIK INVALID' || status === 'NPWP INVALID';
                        const isBelumLunas = status === 'BELUM LUNAS';

                        if (isLunas || isTidakTerdaftar) {
                            let icon = isLunas ? 'success' : 'info';
                            let title = isLunas ? 'Konfirmasi Status Pajak' : `${labelPajak} Tidak Terdaftar`;
                            
                            let displayMessage = message;
                            if (isTidakTerdaftar) {
                                displayMessage = `${labelPajak} Anda tidak ditemukan dalam <strong>Database Pajak Daerah (BPPKAD) Kabupaten Banjarnegara</strong>. Hal ini berarti Anda tidak memiliki riwayat objek pajak daerah yang terdaftar.`;
                            } else if (isLunas) {
                                displayMessage = `${message} pada <strong>Database Pajak Daerah Kabupaten Banjarnegara</strong>.`;
                            }

                            Swal.fire({
                                icon: icon,
                                title: title,
                                html: `
                                    <div class="text-left bg-gray-50 p-4 rounded-xl border border-gray-100 mt-2">
                                        <p class="text-sm text-gray-700 leading-relaxed">${displayMessage}</p>
                                        <p class="text-xs text-amber-600 mt-3 font-semibold italic">* Anda diperbolehkan untuk melanjutkan pengajuan.</p>
                                    </div>
                                `,
                                showCancelButton: true,
                                confirmButtonText: 'Lanjutkan Pengajuan',
                                cancelButtonText: 'Batal',
                                confirmButtonColor: '#d97706',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    btnSubmitReal.click();
                                }
                            });
                        } else if (isBelumLunas) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Status Pajak: BELUM LUNAS',
                                html: `
                                    <div class="text-left bg-red-50 p-4 rounded-xl border border-red-100 mt-2">
                                        <p class="text-sm text-red-700 leading-relaxed">${message} pada <strong>Database Pajak Daerah Kabupaten Banjarnegara</strong>. Mohon selesaikan kewajiban pajak daerah Anda terlebih dahulu untuk dapat melanjutkan pengajuan.</p>
                                    </div>
                                `,
                                confirmButtonText: 'Mengerti',
                                confirmButtonColor: '#d97706',
                            });
                        } else if (isInvalid) {
                            Swal.fire({
                                icon: 'error',
                                title: `${labelPajak} Tidak Valid`,
                                html: `
                                    <div class="text-left bg-red-50 p-4 rounded-xl border border-red-100 mt-2">
                                        <p class="text-sm text-red-700 leading-relaxed">${message}. Sistem memvalidasi ${labelPajak} Anda berdasarkan format standar yang berlaku di <strong>Database Pajak Daerah</strong>.</p>
                                    </div>
                                `,
                                confirmButtonText: 'Perbaiki Profil',
                                confirmButtonColor: '#d97706',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '{{ route('pemohon.profile.edit') }}';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Status Tidak Diketahui',
                                text: 'Respon dari server KSWP: ' + status
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Koneksi',
                            text: 'Gagal menghubungi server KSWP. Mohon coba beberapa saat lagi.'
                        });
                    });
                });
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
            
            // Set hidden input
            document.getElementById('existing_file_' + fieldId).value = docId; // We store the userDokumen ID
            
            // Clear actual file input if any
            const fileInput = document.getElementById('file_' + fieldId);
            fileInput.value = '';
            
            // Clear old files container if any
            const oldContainer = document.getElementById('old_files_container_' + fieldId);
            if (oldContainer) {
                oldContainer.remove();
            }
            
            // Update preview
            const preview = document.getElementById('preview_' + fieldId);
            preview.innerHTML = `
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 flex items-center gap-3 relative">
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
            
            closeDokumenModal();
        }

        function removeSelectedDokumen(fieldId) {
            document.getElementById('existing_file_' + fieldId).value = '';
            document.getElementById('preview_' + fieldId).innerHTML = '';
        }

        function removeOldFile(fieldId, idx) {
            const item = document.getElementById(`old_file_item_${fieldId}_${idx}`);
            if (item) {
                item.remove();
            }
            const preview = document.getElementById('preview_' + fieldId);
            const remainingItems = preview.querySelectorAll('[id^="old_file_item_"]');
            if (remainingItems.length === 0) {
                preview.innerHTML = '';
            }
        }

        function previewFiles(input, fieldId) {
            const preview = document.getElementById('preview_' + fieldId);
            // If they pick a new file, clear the existing document selection
            document.getElementById('existing_file_' + fieldId).value = '';
            
            // Clear old files container if any
            const oldContainer = document.getElementById('old_files_container_' + fieldId);
            if (oldContainer) {
                oldContainer.remove();
            }
            
            if (input.files && input.files.length > 0) {
                let html = '<div class="space-y-2">';
                
                for (let i = 0; i < input.files.length; i++) {
                    const file = input.files[i];
                    const fileName = file.name;
                    const fileSize = (file.size / 1024).toFixed(2); // KB
                    
                    html += `
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-center gap-3">
                            <i class="fas fa-file text-green-600 text-xl"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">${fileName}</p>
                                <p class="text-xs text-gray-500">${fileSize} KB</p>
                            </div>
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                    `;
                }
                
                html += '</div>';
                preview.innerHTML = html;
            }
        }

        // Scroll to error alert if there are validation errors
        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                const errorAlert = document.querySelector('.bg-red-50');
                if (errorAlert) {
                    errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }

                // Add click handler to focus on first error field
                const firstErrorField = document.querySelector('.border-red-500');
                if (firstErrorField) {
                    setTimeout(() => {
                        firstErrorField.focus();
                    }, 500);
                }
            });
        @endif

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
                            confirmButtonColor: '#3b82f6',
                            confirmButtonText: 'OK'
                        });
                    });
            });
        }
    </script>
</x-pemohon.layout>
