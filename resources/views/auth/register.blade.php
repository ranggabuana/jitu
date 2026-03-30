<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - JITU Banjarnegara</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
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

            <form method="POST" action="{{ route('front.register') }}" class="space-y-5">
                @csrf
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-blue-600 mr-2"></i>Nama Lengkap
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('name') border-red-500 @enderror"
                        placeholder="Masukkan nama lengkap">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-at text-blue-600 mr-2"></i>Username
                    </label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('username') border-red-500 @enderror"
                        placeholder="Pilih username unik">
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-id-card text-blue-600 mr-2"></i>NIK (Nomor Induk Kependudukan)
                    </label>
                    <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required minlength="16" maxlength="16" pattern="[0-9]{16}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('nik') border-red-500 @enderror"
                        placeholder="16 digit NIK">
                    <span id="nik-error" class="text-red-600 text-xs mt-1 hidden"></span>
                    @error('nik')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i>Masukkan 16 digit angka NIK</p>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope text-blue-600 mr-2"></i>Email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('email') border-red-500 @enderror"
                        placeholder="contoh@email.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Pemohon -->
                <div>
                    <label for="status_pemohon" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-tag text-blue-600 mr-2"></i>Status Pemohon
                    </label>
                    <select id="status_pemohon" name="status_pemohon" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('status_pemohon') border-red-500 @enderror"
                        onchange="toggleCompanyFields()">
                        <option value="">-- Pilih Status Pemohon --</option>
                        <option value="perorangan" {{ old('status_pemohon') === 'perorangan' ? 'selected' : '' }}>Perorangan</option>
                        <option value="badan_usaha" {{ old('status_pemohon') === 'badan_usaha' ? 'selected' : '' }}>Badan Usaha</option>
                    </select>
                    @error('status_pemohon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i>Pilih status sesuai jenis pengajuan</p>
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
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i>Format: 00.000.000.0-000.000 (Opsional)</p>
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
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-blue-600 mr-2"></i>Password
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
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-blue-600 mr-2"></i>Konfirmasi Password
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
                    <p class="text-xs text-gray-500 mt-3">
                        <i class="fas fa-info-circle mr-1 text-green-600"></i>
                        Masukkan hasil penjumlahan untuk memverifikasi bahwa Anda bukan robot.
                    </p>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-xl">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                </button>
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
                // Fields are optional, just show them
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
        });

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
                    alert('Gagal refresh CAPTCHA. Silakan coba lagi.');
                });
        });

        @if(session('success'))
            alert('{{ session('success') }}');
        @endif
    </script>
</body>

</html>
