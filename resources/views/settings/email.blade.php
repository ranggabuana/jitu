<x-layout>
    <x-slot:title>Pengaturan Email (SMTP)</x-slot:title>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <i class="mdi mdi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Pengaturan Email (SMTP)</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Konfigurasi pengiriman email notifikasi dan reset password</p>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center gap-2 text-green-700 dark:text-green-400">
                <i class="mdi mdi-check-circle"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center gap-2 text-red-700 dark:text-red-400">
                <i class="mdi mdi-alert-circle"></i>
                <span class="font-medium">Terjadi kesalahan validasi:</span>
            </div>
            <ul class="mt-2 ml-6 list-disc text-sm text-red-600 dark:text-red-400">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Configuration Form -->
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('settings.email.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-server-network text-gray-500"></i>
                            Konfigurasi Server SMTP
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Host -->
                            <div>
                                <label for="mail_host" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    SMTP Host <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="mail_host" id="mail_host"
                                    value="{{ old('mail_host', $settings['mail_host'] ?? config('mail.mailers.smtp.host')) }}"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    placeholder="e.g., smtp.gmail.com">
                            </div>

                            <!-- Port -->
                            <div>
                                <label for="mail_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    SMTP Port <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="mail_port" id="mail_port"
                                    value="{{ old('mail_port', $settings['mail_port'] ?? config('mail.mailers.smtp.port')) }}"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    placeholder="e.g., 587">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Username -->
                            <div>
                                <label for="mail_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    SMTP Username
                                </label>
                                <input type="text" name="mail_username" id="mail_username"
                                    value="{{ old('mail_username', $settings['mail_username'] ?? config('mail.mailers.smtp.username')) }}"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    placeholder="Email atau username akun">
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="mail_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    SMTP Password
                                </label>
                                <div class="relative">
                                    <input type="password" name="mail_password" id="mail_password"
                                        value="{{ old('mail_password', $settings['mail_password'] ?? config('mail.mailers.smtp.password')) }}"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        placeholder="Sandi Aplikasi atau password">
                                    <button type="button" onclick="togglePasswordVisibility('mail_password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                                        <i class="mdi mdi-eye" id="mail_password_icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Encryption -->
                        <div>
                            <label for="mail_encryption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Enkripsi
                            </label>
                            <select name="mail_encryption" id="mail_encryption"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="tls" {{ (old('mail_encryption', $settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption')) == 'tls') ? 'selected' : '' }}>TLS (Direkomendasikan)</option>
                                <option value="ssl" {{ (old('mail_encryption', $settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption')) == 'ssl') ? 'selected' : '' }}>SSL</option>
                                <option value="null" {{ (old('mail_encryption', $settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption')) == null) ? 'selected' : '' }}>Tanpa Enkripsi</option>
                            </select>
                        </div>

                        <hr class="my-4 border-gray-200 dark:border-gray-700">

                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-4">Informasi Pengirim</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- From Address -->
                            <div>
                                <label for="mail_from_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email Pengirim (From) <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="mail_from_address" id="mail_from_address"
                                    value="{{ old('mail_from_address', $settings['mail_from_address'] ?? config('mail.from.address')) }}"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    placeholder="e.g., no-reply@jitu.id">
                            </div>

                            <!-- From Name -->
                            <div>
                                <label for="mail_from_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nama Pengirim <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="mail_from_name" id="mail_from_name"
                                    value="{{ old('mail_from_name', $settings['mail_from_name'] ?? config('mail.from.name')) }}"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    placeholder="e.g., JITU Banjarnegara">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 rounded-b-lg">
                        <button type="submit"
                            class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                            <i class="mdi mdi-content-save"></i>
                            Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar / Test Connection -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Test Connection Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="mdi mdi-email-check text-gray-500"></i>
                        Test Koneksi
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Gunakan fitur ini untuk memastikan konfigurasi SMTP Anda sudah benar dan bisa mengirim email.
                    </p>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="test_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email Penerima Tes
                            </label>
                            <input type="email" id="test_email" 
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                placeholder="Masukkan email Anda">
                        </div>
                        
                        <button type="button" id="btn-test-connection"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                            <i class="mdi mdi-send"></i>
                            Kirim Email Percobaan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Helpful Tips Card -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border border-blue-100 dark:border-blue-800">
                <h3 class="text-sm font-bold text-blue-800 dark:text-blue-400 flex items-center gap-2 mb-3">
                    <i class="mdi mdi-information-outline"></i> Tips Konfigurasi
                </h3>
                <ul class="text-xs text-blue-700 dark:text-blue-300 space-y-2 list-disc ml-4">
                    <li>Untuk <strong>Gmail</strong>, gunakan host <code>smtp.gmail.com</code> port <code>587</code> (TLS).</li>
                    <li>Pastikan sudah mengaktifkan <strong>2-Step Verification</strong> dan menggunakan <strong>App Password</strong> dari akun Google Anda.</li>
                    <li>Jika email gagal terkirim di hosting, coba ganti port ke <code>465</code> dengan enkripsi <code>SSL</code>.</li>
                    <li>Setelah menyimpan perubahan, disarankan untuk melakukan <strong>Test Koneksi</strong> sebelum digunakan oleh user.</li>
                </ul>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '_icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('mdi-eye');
                icon.classList.add('mdi-eye-off');
            } else {
                input.type = 'password';
                icon.classList.remove('mdi-eye-off');
                icon.classList.add('mdi-eye');
            }
        }

        document.getElementById('btn-test-connection').addEventListener('click', function() {
            const email = document.getElementById('test_email').value;
            if (!email) {
                Swal.fire('Error', 'Silakan masukkan email penerima tes terlebih dahulu.', 'error');
                return;
            }

            const btn = this;
            const originalContent = btn.innerHTML;
            
            // Disable button and show loading
            btn.disabled = true;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Menghubungkan...';

            fetch('{{ route('settings.email.test') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ test_email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonColor: '#059669'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Koneksi',
                        html: `<div class="text-left text-sm mt-2 p-2 bg-gray-100 rounded border border-gray-200">${data.message}</div>`,
                        confirmButtonColor: '#dc2626'
                    });
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Terjadi kesalahan sistem saat mencoba koneksi.', 'error');
                console.error(error);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalContent;
            });
        });
    </script>
    @endpush
</x-layout>
