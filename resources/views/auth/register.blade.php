<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - JITU Banjarnegara</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
    <style>
        /* Select2 Custom Styles */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 50px;
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 32px;
            color: #374151;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            height: 48px;
        }
        .select2-container--bootstrap-5 .select2-dropdown {
            border-radius: 0.75rem;
            border: 1px solid #d1d5db;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: #3b82f6 !important;
        }
        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: #dbeafe !important;
        }
        .select2-container--focus .select2-selection {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
        .select2-container--bootstrap-5 .select2-search__field {
            border-radius: 0.5rem;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e5e7eb;
            z-index: 0;
        }
        .step {
            position: relative;
            z-index: 1;
            text-align: center;
            flex: 1;
        }
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #6b7280;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        .step.active .step-circle {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .step.completed .step-circle {
            background: #10b981;
            color: white;
        }
        .step-label {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
        }
        .step.active .step-label {
            color: #3b82f6;
            font-weight: 600;
        }
        .form-step {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .form-step.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .file-upload-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        .file-upload-wrapper input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            border: 2px dashed #d1d5db;
            border-radius: 0.75rem;
            background: #f9fafb;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .file-upload-label:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .file-upload-label i {
            font-size: 2.5rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        .file-upload-label.has-file {
            border-color: #10b981;
            background: #ecfdf5;
        }
        .file-upload-label.has-file i {
            color: #10b981;
        }
        .preview-image {
            max-width: 200px;
            max-height: 150px;
            border-radius: 0.5rem;
            margin-top: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e5e7eb;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen py-12 px-4">
    <div class="max-w-2xl w-full mx-auto">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-3 mb-4">
                <img src="{{ asset('assets/images/logo-banjarnegara.png') }}" alt="Logo Banjarnegara" class="w-16 h-16 object-contain drop-shadow-md">
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Buat Akun Baru</h1>
            <p class="text-gray-600">Daftar untuk mengajukan perizinan online</p>
        </div>

        <!-- Registration Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <!-- Info Banner -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi Penting:</p>
                        <ul class="list-disc list-inside space-y-1 text-blue-700">
                            <li>Akun akan menunggu aktivasi dari admin</li>
                            <li>Anda akan diberitahu melalui email setelah aktivasi</li>
                            <li>Pastikan data yang diisi sudah benar</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Data Pribadi</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Alamat & KTP</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Keamanan</div>
                </div>
            </div>

            <form method="POST" action="{{ route('front.register') }}" enctype="multipart/form-data" id="registerForm">
                @csrf

                <!-- Step 1: Data Pribadi -->
                <div class="form-step active" data-step="1">
                    <div class="space-y-5">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user text-blue-600 mr-2"></i>Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('name') border-red-500 @enderror"
                                placeholder="Masukkan nama lengkap">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-at text-blue-600 mr-2"></i>Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('username') border-red-500 @enderror"
                                placeholder="Pilih username unik">
                            @error('username')
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- NIK -->
                        <div>
                            <label for="nik" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-id-card text-blue-600 mr-2"></i>NIK (Nomor Induk Kependudukan) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required minlength="16" maxlength="16" pattern="[0-9]{16}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('nik') border-red-500 @enderror"
                                placeholder="16 digit NIK">
                            <span id="nik-error" class="text-red-600 text-xs mt-1 hidden"></span>
                            @error('nik')
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i>Masukkan 16 digit angka NIK</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope text-blue-600 mr-2"></i>Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('email') border-red-500 @enderror"
                                placeholder="contoh@email.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Pemohon -->
                        <div>
                            <label for="status_pemohon" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user-tag text-blue-600 mr-2"></i>Status Pemohon <span class="text-red-500">*</span>
                            </label>
                            <select id="status_pemohon" name="status_pemohon" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('status_pemohon') border-red-500 @enderror"
                                onchange="toggleCompanyFields()">
                                <option value="">-- Pilih Status Pemohon --</option>
                                <option value="perorangan" {{ old('status_pemohon') === 'perorangan' ? 'selected' : '' }}>Perorangan</option>
                                <option value="badan_usaha" {{ old('status_pemohon') === 'badan_usaha' ? 'selected' : '' }}>Badan Usaha</option>
                            </select>
                            @error('status_pemohon')
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Company Fields (shown for badan_usaha) -->
                        <div id="companyFields" class="hidden space-y-5">
                            <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-building text-purple-600 mt-0.5"></i>
                                    <div class="text-sm text-purple-800">
                                        <p class="font-semibold mb-1">Data Badan Usaha</p>
                                        <p class="text-purple-700">Lengkapi informasi perusahaan Anda</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Nama Perusahaan -->
                            <div>
                                <label for="nama_perusahaan" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-building text-blue-600 mr-2"></i>Nama Perusahaan
                                </label>
                                <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="{{ old('nama_perusahaan') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition-all @error('nama_perusahaan') border-red-500 @enderror"
                                    placeholder="PT/CV/UD ...">
                                @error('nama_perusahaan')
                                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- NPWP -->
                            <div>
                                <label for="npwp" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-file-invoice text-blue-600 mr-2"></i>NPWP (Opsional)
                                </label>
                                <input type="text" id="npwp" name="npwp" value="{{ old('npwp') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition-all @error('npwp') border-red-500 @enderror"
                                    placeholder="00.000.000.0-000.000">
                                @error('npwp')
                                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="no_hp" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-phone text-blue-600 mr-2"></i>Nomor WhatsApp (Opsional)
                            </label>
                            <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('no_hp') border-red-500 @enderror"
                                placeholder="08xxxxxxxxxx">
                            @error('no_hp')
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Next Button -->
                    <button type="button" onclick="nextStep(2)"
                        class="w-full mt-6 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-xl">
                        <i class="fas fa-arrow-right mr-2"></i>Lanjut ke Langkah Berikutnya
                    </button>
                </div>

                <!-- Step 2: Alamat & KTP -->
                <div class="form-step" data-step="2">
                    <div class="space-y-5">
                        <!-- Wilayah Dropdowns -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-map-marker-alt text-green-600 mt-0.5"></i>
                                <div class="text-sm text-green-800">
                                    <p class="font-semibold mb-1">Data Wilayah</p>
                                    <p class="text-green-700">Pilih lokasi tempat tinggal Anda</p>
                                </div>
                            </div>
                        </div>

                        <!-- Provinsi -->
                        <div>
                            <label for="provinsi" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map text-green-600 mr-2"></i>Provinsi
                            </label>
                            <select id="provinsi" name="provinsi_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all select2-wilayah">
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                        </div>

                        <!-- Kabupaten -->
                        <div>
                            <label for="kabupaten" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map text-green-600 mr-2"></i>Kabupaten/Kota
                            </label>
                            <select id="kabupaten" name="kabupaten_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all select2-wilayah" disabled>
                                <option value="">-- Pilih Kabupaten/Kota --</option>
                            </select>
                        </div>

                        <!-- Kecamatan -->
                        <div>
                            <label for="kecamatan" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map text-green-600 mr-2"></i>Kecamatan
                            </label>
                            <select id="kecamatan" name="kecamatan_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all select2-wilayah" disabled>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>

                        <!-- Kelurahan -->
                        <div>
                            <label for="kelurahan" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map text-green-600 mr-2"></i>Kelurahan/Desa
                            </label>
                            <select id="kelurahan" name="kelurahan_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all select2-wilayah" disabled>
                                <option value="">-- Pilih Kelurahan/Desa --</option>
                            </select>
                        </div>

                        <!-- Alamat Lengkap -->
                        <div>
                            <label for="alamat_lengkap" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-home text-green-600 mr-2"></i>Alamat Lengkap
                            </label>
                            <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all resize-none"
                                placeholder="Jalan, nomor rumah, RT/RW, dan keterangan lainnya">{{ old('alamat_lengkap') }}</textarea>
                            @error('alamat_lengkap')
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Foto KTP Upload -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-id-card text-green-600 mr-2"></i>Foto KTP <span class="text-red-500">*</span>
                            </label>
                            <div class="file-upload-wrapper">
                                <input type="file" id="foto_ktp" name="foto_ktp" accept="image/jpeg,image/png,image/jpg,application/pdf" onchange="previewKTP(this)" required>
                                <label for="foto_ktp" class="file-upload-label" id="ktpUploadLabel">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span class="text-sm text-gray-600">Klik atau drag & drop untuk mengunggah KTP</span>
                                    <span class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF (Maks. 2MB)</span>
                                    <img id="ktpPreview" class="preview-image hidden" alt="Preview KTP">
                                </label>
                            </div>
                            @error('foto_ktp')
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="prevStep(1)"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-4 rounded-xl transition-all duration-300">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </button>
                        <button type="button" onclick="nextStep(3)"
                            class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-xl">
                            Lanjut<i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Keamanan -->
                <div class="form-step" data-step="3">
                    <div class="space-y-5">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock text-blue-600 mr-2"></i>Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('password') border-red-500 @enderror"
                                    placeholder="Minimal 8 karakter">
                                <button type="button" onclick="togglePassword('password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="password-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock text-blue-600 mr-2"></i>Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                                    placeholder="Ulangi password">
                                <button type="button" onclick="togglePassword('password_confirmation')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="password-confirmation-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- CAPTCHA -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-shield-alt text-blue-600 mr-2"></i>Verifikasi Keamanan
                            </label>
                            <div class="flex items-end gap-3">
                                <div class="flex-1">
                                    <span id="captcha-question" class="block text-xl font-bold text-blue-600 mb-2">
                                        {{ session('register_num1') }} + {{ session('register_num2') }} = ?
                                    </span>
                                    <input type="number"
                                        id="captcha"
                                        name="captcha"
                                        required
                                        placeholder="Hasil penjumlahan"
                                        class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-lg font-semibold @if(session('captcha_error')) border-red-500 @endif">
                                    @if(session('captcha_error'))
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ session('captcha_error') }}
                                        </p>
                                    @endif
                                    @error('captcha')
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <button type="button"
                                    id="refresh-captcha"
                                    class="shrink-0 bg-blue-600 hover:bg-blue-700 text-white w-14 h-14 rounded-xl font-semibold transition-all flex items-center justify-center text-xl shadow-lg hover:shadow-xl">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="prevStep(2)"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-4 rounded-xl transition-all duration-300">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </button>
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-xl">
                            <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                        </button>
                    </div>
                </div>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold hover:underline">
                        Login disini
                    </a>
                </p>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="mt-6 text-center">
            <a href="{{ route('landing') }}" class="text-gray-600 hover:text-gray-800 text-sm flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

    <script>
        let currentStep = 1;

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.form-step').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.step').forEach(el => {
                el.classList.remove('active');
                if (parseInt(el.dataset.step) < step) {
                    el.classList.add('completed');
                } else {
                    el.classList.remove('completed');
                }
                if (parseInt(el.dataset.step) === step) {
                    el.classList.add('active');
                }
            });

            // Show current step
            document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
            currentStep = step;
        }

        function nextStep(step) {
            // Validate current step before proceeding
            const currentStepEl = document.querySelector(`.form-step[data-step="${currentStep}"]`);
            const requiredFields = currentStepEl.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            let firstInvalidField = null;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            if (isValid) {
                showStep(step);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Mohon lengkapi semua field yang wajib diisi.',
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'OK'
                });
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
            }
        }

        function prevStep(step) {
            showStep(step);
        }

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const eyeIcon = document.getElementById(inputId + '-eye');

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Real-time NIK validation
        const nikInput = document.getElementById('nik');
        const nikError = document.getElementById('nik-error');
        let nikTimeout;

        nikInput.addEventListener('input', function() {
            clearTimeout(nikTimeout);
            const nik = this.value;
            const statusPemohon = document.getElementById('status_pemohon').value;

            // Only validate if NIK is exactly 16 digits
            if (nik.length === 16 && /^[0-9]{16}$/.test(nik)) {
                nikTimeout = setTimeout(() => {
                    fetch('{{ route("api.nik.check") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ nik: nik, status_pemohon: statusPemohon || 'perorangan' })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            nikError.textContent = 'NIK ini sudah terdaftar untuk status ' + (statusPemohon === 'badan_usaha' ? 'Badan Usaha' : 'Perorangan') + '.';
                            nikError.classList.remove('hidden');
                            nikInput.classList.add('border-red-500');
                            nikInput.classList.remove('border-green-500');
                        } else {
                            nikError.classList.add('hidden');
                            nikInput.classList.remove('border-red-500');
                            nikInput.classList.add('border-green-500');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking NIK:', error);
                    });
                }, 500);
            } else {
                nikError.classList.add('hidden');
                nikInput.classList.remove('border-red-500', 'border-green-500');
            }
        });

        // Prevent form submission if NIK is already registered
        document.querySelector('form').addEventListener('submit', function(e) {
            const nik = document.getElementById('nik').value;
            if (nik.length === 16 && /^[0-9]{16}$/.test(nik)) {
                // Check if error is showing
                if (!nikError.classList.contains('hidden')) {
                    e.preventDefault();
                }
            }
        });

        // Toggle company fields based on status_pemohon
        function toggleCompanyFields() {
            const statusPemohon = document.getElementById('status_pemohon').value;
            const companyFields = document.getElementById('companyFields');
            const namaPerusahaanInput = document.getElementById('nama_perusahaan');
            const npwpInput = document.getElementById('npwp');

            if (statusPemohon === 'badan_usaha') {
                companyFields.classList.remove('hidden');
            } else {
                companyFields.classList.add('hidden');
                namaPerusahaanInput.value = '';
                npwpInput.value = '';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const statusPemohon = document.getElementById('status_pemohon').value;
            if (statusPemohon === 'badan_usaha') {
                toggleCompanyFields();
            }

            // Initialize Select2 on wilayah dropdowns
            $('.select2-wilayah').select2({
                theme: 'bootstrap-5',
                placeholder: function() {
                    return $(this).data('placeholder') || $(this).find('option:first').text();
                },
                allowClear: true,
                width: '100%',
                language: {
                    search: function() {
                        return 'Cari...';
                    },
                    noResults: function() {
                        return 'Data tidak ditemukan';
                    }
                }
            });

            // Add change event listeners for cascading
            $('#provinsi').on('change', function() {
                loadKabupaten();
            });

            $('#kabupaten').on('change', function() {
                loadKecamatan();
            });

            $('#kecamatan').on('change', function() {
                loadKelurahan();
            });

            // Load provinsi
            loadProvinsi();
        });

        // Wilayah Select2 functions
        function loadProvinsi() {
            fetch('{{ route("api.wilayah.provinsi") }}')
                .then(response => response.json())
                .then(data => {
                    const select = $('#provinsi');
                    select.empty().append('<option value="">-- Pilih Provinsi --</option>');
                    
                    data.data.forEach(item => {
                        const option = new Option(item.name, item.id, false, false);
                        select.append(option);
                    });
                    
                    // Restore old value if exists
                    @if(old('provinsi_id'))
                        select.val('{{ old('provinsi_id') }}').trigger('change');
                        loadKabupaten();
                    @endif
                })
                .catch(error => console.error('Error loading provinsi:', error));
        }

        function loadKabupaten() {
            const provinsiId = $('#provinsi').val();
            const kabupatenSelect = $('#kabupaten');
            const kecamatanSelect = $('#kecamatan');
            const kelurahanSelect = $('#kelurahan');

            // Reset dependent dropdowns
            kabupatenSelect.empty().append('<option value="">-- Pilih Kabupaten/Kota --</option>');
            kecamatanSelect.empty().append('<option value="">-- Pilih Kecamatan --</option>');
            kelurahanSelect.empty().append('<option value="">-- Pilih Kelurahan/Desa --</option>');
            
            kabupatenSelect.prop('disabled', true).trigger('change');
            kecamatanSelect.prop('disabled', true).trigger('change');
            kelurahanSelect.prop('disabled', true).trigger('change');

            if (!provinsiId) {
                return;
            }

            kabupatenSelect.prop('disabled', false).trigger('change');

            fetch(`/api/wilayah/provinsi/${provinsiId}/kabupaten`)
                .then(response => response.json())
                .then(data => {
                    kabupatenSelect.empty().append('<option value="">-- Pilih Kabupaten/Kota --</option>');
                    
                    data.data.forEach(item => {
                        const option = new Option(item.name, item.id, false, false);
                        kabupatenSelect.append(option);
                    });
                    
                    // Restore old value if exists
                    @if(old('kabupaten_id'))
                        kabupatenSelect.val('{{ old('kabupaten_id') }}').trigger('change');
                        loadKecamatan();
                    @endif
                })
                .catch(error => console.error('Error loading kabupaten:', error));
        }

        function loadKecamatan() {
            const kabupatenId = $('#kabupaten').val();
            const kecamatanSelect = $('#kecamatan');
            const kelurahanSelect = $('#kelurahan');

            // Reset dependent dropdowns
            kecamatanSelect.empty().append('<option value="">-- Pilih Kecamatan --</option>');
            kelurahanSelect.empty().append('<option value="">-- Pilih Kelurahan/Desa --</option>');
            kelurahanSelect.prop('disabled', true).trigger('change');

            if (!kabupatenId) {
                kecamatanSelect.prop('disabled', true).trigger('change');
                return;
            }

            kecamatanSelect.prop('disabled', false).trigger('change');

            fetch(`/api/wilayah/kabupaten/${kabupatenId}/kecamatan`)
                .then(response => response.json())
                .then(data => {
                    kecamatanSelect.empty().append('<option value="">-- Pilih Kecamatan --</option>');
                    
                    data.data.forEach(item => {
                        const option = new Option(item.name, item.id, false, false);
                        kecamatanSelect.append(option);
                    });
                    
                    // Restore old value if exists
                    @if(old('kecamatan_id'))
                        kecamatanSelect.val('{{ old('kecamatan_id') }}').trigger('change');
                        loadKelurahan();
                    @endif
                })
                .catch(error => console.error('Error loading kecamatan:', error));
        }

        function loadKelurahan() {
            const kecamatanId = $('#kecamatan').val();
            const kelurahanSelect = $('#kelurahan');

            // Reset
            kelurahanSelect.empty().append('<option value="">-- Pilih Kelurahan/Desa --</option>');

            if (!kecamatanId) {
                kelurahanSelect.prop('disabled', true).trigger('change');
                return;
            }

            kelurahanSelect.prop('disabled', false).trigger('change');

            fetch(`/api/wilayah/kecamatan/${kecamatanId}/kelurahan`)
                .then(response => response.json())
                .then(data => {
                    kelurahanSelect.empty().append('<option value="">-- Pilih Kelurahan/Desa --</option>');
                    
                    data.data.forEach(item => {
                        const option = new Option(item.name, item.id, false, false);
                        kelurahanSelect.append(option);
                    });
                    
                    // Restore old value if exists
                    @if(old('kelurahan_id'))
                        kelurahanSelect.val('{{ old('kelurahan_id') }}').trigger('change');
                    @endif
                })
                .catch(error => console.error('Error loading kelurahan:', error));
        }

        // Preview KTP upload
        function previewKTP(input) {
            const label = document.getElementById('ktpUploadLabel');
            const preview = document.getElementById('ktpPreview');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileType = file.type;
                const fileSize = file.size;

                // Validate file size (2MB max)
                if (fileSize > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file terlalu besar. Maksimal 2MB.',
                        confirmButtonColor: '#3b82f6',
                        confirmButtonText: 'OK'
                    });
                    input.value = '';
                    return;
                }

                label.classList.add('has-file');

                // Show preview for images
                if (fileType.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.classList.add('hidden');
                }

                // Update label text
                label.querySelector('span.text-sm').textContent = file.name;
            } else {
                label.classList.remove('has-file');
                preview.classList.add('hidden');
                label.querySelector('span.text-sm').textContent = 'Klik atau drag & drop untuk mengunggah KTP';
            }
        }

        // Refresh CAPTCHA
        const refreshBtn = document.getElementById('refresh-captcha');
        const captchaQuestion = document.getElementById('captcha-question');
        const captchaInput = document.getElementById('captcha');

        refreshBtn.addEventListener('click', function() {
            // Show loading state
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;

            fetch('{{ route("api.refresh-captcha") }}')
                .then(response => response.json())
                .then(data => {
                    captchaQuestion.textContent = `${data.num1} + ${data.num2} = ?`;
                    captchaInput.value = '';
                    captchaInput.focus();

                    // Reset button
                    refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
                    refreshBtn.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
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

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
</body>

</html>
