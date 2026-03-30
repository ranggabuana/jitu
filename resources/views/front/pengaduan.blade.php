<x-front-layout>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-red-700 via-red-600 to-orange-600 text-white py-16 overflow-hidden">
        <div class="absolute top-0 right-0 opacity-5 transform translate-x-16 -translate-y-8">
            <i class="fas fa-bullhorn text-9xl"></i>
        </div>
        <div class="absolute bottom-0 left-0 opacity-5 transform -translate-x-8 translate-y-8">
            <i class="fas fa-exclamation-circle text-8xl"></i>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full border border-white/20 mb-4">
                <i class="fas fa-bullhorn text-yellow-400"></i>
                <span class="text-sm font-medium">Pengaduan Masyarakat</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Sampaikan <span class="text-yellow-400">Pengaduan</span> Anda
            </h1>
            <p class="text-xl text-red-100 max-w-3xl mx-auto">
                Laporkan masalah atau berikan masukan untuk peningkatan kualitas pelayanan publik.
                Kami akan menindaklanjuti setiap pengaduan yang masuk.
            </p>
        </div>

        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-white/30 to-transparent">
        </div>
    </section>

    <!-- Info Cards -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 text-2xl mb-4">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Identitas Aman</h3>
                    <p class="text-gray-600 text-sm">Data pribadi Anda dilindungi dan hanya digunakan untuk validasi pengaduan.</p>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 text-2xl mb-4">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Tindak Lanjut Cepat</h3>
                    <p class="text-gray-600 text-sm">Setiap pengaduan akan diproses dan ditindaklanjuti oleh tim terkait.</p>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center text-green-600 text-2xl mb-4">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Nomor Pengaduan</h3>
                    <p class="text-gray-600 text-sm">Anda akan mendapatkan nomor pengaduan untuk tracking status.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Form Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('error'))
            <div class="mb-8 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-xl"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @if ($errors->any())
            <div class="mb-8 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-xl mt-0.5"></i>
                    <div>
                        <p class="font-semibold mb-2">Mohon periksa kembali form:</p>
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl shadow-xl overflow-hidden" id="pengaduanForm">
                @csrf

                <!-- Form Header -->
                <div class="bg-gradient-to-r from-red-600 to-orange-600 text-white p-8">
                    <h2 class="text-2xl font-bold mb-2">
                        <i class="fas fa-clipboard-list mr-3"></i>Formulir Pengaduan
                    </h2>
                    <p class="text-red-100">Lengkapi data di bawah ini untuk mengirim pengaduan</p>
                </div>

                <!-- Form Content -->
                <div class="p-8 space-y-8">
                    <!-- Data Pelapor -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-user-circle text-red-600"></i>
                            Data Pelapor
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user text-gray-400 mr-2"></i>Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all"
                                    placeholder="Sesuai KTP">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all"
                                    placeholder="contoh@email.com">
                            </div>

                            <div class="md:col-span-2">
                                <label for="no_hp" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-phone text-gray-400 mr-2"></i>Nomor HP/WA <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all"
                                    placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                    </div>

                    <!-- Detail Pengaduan -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                            Detail Pengaduan
                        </h3>
                        <div class="space-y-6">
                            <div>
                                <label for="kategori" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-folder text-gray-400 mr-2"></i>Kategori Pengaduan <span class="text-red-500">*</span>
                                </label>
                                <select name="kategori" id="kategori" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all bg-white">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $value => $label)
                                        <option value="{{ $value }}" {{ old('kategori') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="isi_pengaduan" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-edit text-gray-400 mr-2"></i>Isi Pengaduan <span class="text-red-500">*</span>
                                </label>
                                <textarea name="isi_pengaduan" id="isi_pengaduan" rows="6" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all resize-none"
                                    placeholder="Jelaskan detail pengaduan Anda (kronologi, lokasi, waktu kejadian, dll)">{{ old('isi_pengaduan') }}</textarea>
                                <p class="text-xs text-gray-500 mt-2">Minimal 10 karakter, maksimal 2000 karakter</p>
                            </div>

                            <div>
                                <label for="lampiran" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-paperclip text-gray-400 mr-2"></i>Lampiran (Opsional)
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-red-400 transition-colors">
                                    <input type="file" name="lampiran" id="lampiran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.rar"
                                        class="hidden" onchange="previewFile()">
                                    <label for="lampiran" class="cursor-pointer">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                        <p class="text-gray-600 font-medium">Klik untuk upload atau drag & drop</p>
                                        <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX, JPG, PNG, ZIP, RAR (Max 5MB)</p>
                                    </label>
                                    <div id="file-preview" class="mt-4 hidden">
                                        <div class="inline-flex items-center gap-2 bg-green-50 text-green-700 px-4 py-2 rounded-lg">
                                            <i class="fas fa-file"></i>
                                            <span id="file-name" class="text-sm"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-yellow-600 text-xl mt-0.5"></i>
                            <div class="text-sm text-yellow-800">
                                <p class="font-semibold mb-1">Keterangan:</p>
                                <ul class="list-disc list-inside space-y-1 text-yellow-700">
                                    <li>Pastikan data yang diisi sudah benar</li>
                                    <li>Pengaduan akan diproses dalam waktu maksimal 3x24 jam</li>
                                    <li>Nomor pengaduan akan dikirim ke email Anda</li>
                                    <li>Gunakan nomor pengaduan untuk tracking status</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CAPTCHA -->
                <div class="p-8 border-b border-gray-200 bg-gradient-to-r from-red-50 to-orange-50">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-robot text-red-600"></i>
                        Verifikasi Keamanan
                    </h3>
                    <div class="max-w-2xl">
                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <span id="captcha-question" class="block text-xl font-bold text-red-600 mb-2">
                                    {{ session('pengaduan_num1') }} + {{ session('pengaduan_num2') }} = ?
                                </span>
                                <input type="number"
                                       name="captcha"
                                       id="captcha"
                                       required
                                       placeholder="Hasil penjumlahan"
                                       class="w-full px-4 py-3 border-2 border-red-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all text-lg font-semibold @if(session('captcha_error')) border-red-500 @endif">
                                @if(session('captcha_error'))
                                    <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ session('captcha_error') }}
                                    </p>
                                @endif
                                @error('captcha')
                                    <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <button type="button"
                                    id="refresh-captcha"
                                    class="shrink-0 bg-red-600 hover:bg-red-700 text-white w-14 h-14 rounded-xl font-semibold transition-all flex items-center justify-center text-xl shadow-lg hover:shadow-xl"
                                    title="Refresh CAPTCHA">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-3">
                            <i class="fas fa-shield-alt mr-1 text-green-600"></i>
                            Masukkan hasil penjumlahan untuk memverifikasi bahwa Anda bukan robot.
                        </p>
                    </div>
                </div>

                <!-- Form Footer -->
                <div class="bg-gray-50 px-8 py-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <a href="{{ route('landing') }}" class="text-gray-600 hover:text-gray-800 font-medium transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <button type="submit"
                        class="bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white px-8 py-4 rounded-xl font-bold shadow-lg transition-all flex items-center gap-2 transform hover:scale-105">
                        <i class="fas fa-paper-plane"></i>
                        Kirim Pengaduan
                    </button>
                </div>
            </form>
        </div>
    </section>

    <script>
        function previewFile() {
            const fileInput = document.getElementById('lampiran');
            const filePreview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');
            
            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                fileName.textContent = file.name;
                filePreview.classList.remove('hidden');
            }
        }

        // Character counter for isi_pengaduan
        document.getElementById('isi_pengaduan').addEventListener('input', function() {
            const length = this.value.length;
            if (length > 2000) {
                this.value = this.value.substring(0, 2000);
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

            fetch('{{ route("pengaduan.refresh-captcha") }}')
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

        // Form validation
        document.getElementById('pengaduanForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
            submitBtn.disabled = true;
        });
    </script>
</x-front-layout>
