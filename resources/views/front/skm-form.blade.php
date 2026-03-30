<x-front-layout>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-blue-800 text-white py-16 overflow-hidden">
        <div class="absolute top-0 right-0 opacity-5 transform translate-x-16 -translate-y-8">
            <i class="fas fa-star text-9xl"></i>
        </div>
        <div class="absolute bottom-0 left-0 opacity-5 transform -translate-x-8 translate-y-8">
            <i class="fas fa-smile text-8xl"></i>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full border border-white/20 mb-4">
                <i class="fas fa-poll text-yellow-400"></i>
                <span class="text-sm font-medium">Survei Kepuasan Masyarakat</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Bantu Kami <span class="text-yellow-400">Meningkat</span>
            </h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                Berikan penilaian Anda terhadap layanan kami. Masukan Anda sangat berharga
                untuk peningkatan kualitas pelayanan publik di Kabupaten Banjarnegara.
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
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 text-2xl mb-4">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Anonimitas Terjaga</h3>
                    <p class="text-gray-600 text-sm">Identitas Anda aman dan hanya digunakan untuk keperluan validasi.</p>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center text-green-600 text-2xl mb-4">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Cepat & Mudah</h3>
                    <p class="text-gray-600 text-sm">Hanya butuh beberapa menit untuk mengisi survei ini.</p>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 text-2xl mb-4">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Dampak Nyata</h3>
                    <p class="text-gray-600 text-sm">Penilaian Anda akan digunakan untuk meningkatkan layanan.</p>
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

            <form action="{{ route('skm.store') }}" method="POST" class="bg-white rounded-3xl shadow-xl overflow-hidden" id="skmForm">
                @csrf
                
                <!-- Form Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-8">
                    <h2 class="text-2xl font-bold mb-2">
                        <i class="fas fa-clipboard-list mr-3"></i>Kuesioner Survei Kepuasan Masyarakat
                    </h2>
                    <p class="text-blue-100">
                        Silakan berikan penilaian Anda dengan jujur dan objektif.
                    </p>
                </div>

                <!-- Respondent Info -->
                <div class="p-8 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-user-circle text-blue-600"></i>
                        Informasi Responden
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="responden_nama" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="responden_nama"
                                   id="responden_nama"
                                   value="{{ old('responden_nama') }}"
                                   required
                                   placeholder="Masukkan nama lengkap Anda"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('responden_nama') border-red-500 @enderror">
                            @error('responden_nama')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="responden_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                   name="responden_email"
                                   id="responden_email"
                                   value="{{ old('responden_email') }}"
                                   required
                                   placeholder="contoh@email.com"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('responden_email') border-red-500 @enderror">
                            @error('responden_email')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="nip" class="block text-sm font-semibold text-gray-700 mb-2">
                                NIP / NIK (Opsional)
                            </label>
                            <input type="text"
                                   name="nip"
                                   id="nip"
                                   value="{{ old('nip') }}"
                                   placeholder="Masukkan NIP/NIK jika ada"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all @error('nip') border-red-500 @enderror">
                            @error('nip')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Questions -->
                <div class="p-8 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                        <i class="fas fa-list-check text-blue-600"></i>
                        Penilaian Layanan
                    </h3>
                    <p class="text-gray-500 text-sm mb-6">
                        Berikan penilaian Anda dengan memilih salah satu opsi pada setiap pertanyaan.
                    </p>

                    @if($questions->count() > 0)
                    <div class="space-y-6">
                        @foreach($questions as $index => $question)
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ $index + 1 }}
                                </div>
                                <p class="text-gray-800 font-semibold text-lg leading-relaxed">
                                    {{ $question->pertanyaan }}
                                </p>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 ml-12">
                                @foreach(['1' => 'Kurang Baik', '2' => 'Cukup Baik', '3' => 'Baik', '4' => 'Sangat Baik'] as $value => $label)
                                <label class="relative cursor-pointer group">
                                    <input type="radio"
                                           name="jawaban[{{ $question->id }}]"
                                           value="{{ $value }}"
                                           required
                                           class="peer sr-only"
                                           @if(old('jawaban.' . $question->id) == $value) checked @endif>
                                    <div class="px-4 py-3 rounded-xl border-2 border-gray-200 text-center transition-all group-hover:border-blue-300 peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white">
                                        <div class="font-bold text-sm">{{ $label }}</div>
                                        <div class="text-xs opacity-70 mt-1">
                                            @if($value == '1')
                                                <i class="fas fa-star text-yellow-400"></i>
                                            @elseif($value == '2')
                                                <i class="fas fa-star text-yellow-400"></i>
                                                <i class="fas fa-star text-yellow-400"></i>
                                            @elseif($value == '3')
                                                <i class="fas fa-star text-yellow-400"></i>
                                                <i class="fas fa-star text-yellow-400"></i>
                                                <i class="fas fa-star text-yellow-400"></i>
                                            @else
                                                <i class="fas fa-star text-yellow-400"></i>
                                                <i class="fas fa-star text-yellow-400"></i>
                                                <i class="fas fa-star text-yellow-400"></i>
                                                <i class="fas fa-star text-yellow-400"></i>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('jawaban.' . $question->id)
                                <p class="mt-2 ml-12 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                            <i class="fas fa-clipboard-list text-4xl text-gray-400"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Pertanyaan</h4>
                        <p class="text-gray-500">Survei sedang dalam tahap penyusunan. Silakan kembali lagi nanti.</p>
                    </div>
                    @endif
                </div>

                <!-- Saran -->
                <div class="p-8 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-comment-dots text-blue-600"></i>
                        Saran & Masukan (Opsional)
                    </h3>
                    <textarea name="saran"
                              id="saran"
                              rows="4"
                              placeholder="Berikan saran atau masukan Anda untuk peningkatan layanan kami..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none @error('saran') border-red-500 @enderror">{{ old('saran') }}</textarea>
                    @error('saran')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Saran Anda akan membantu kami memberikan layanan yang lebih baik.
                        </p>
                        <span id="charCount" class="text-xs text-gray-400">0/1000</span>
                    </div>
                </div>

                <!-- CAPTCHA -->
                <div class="p-8 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-robot text-blue-600"></i>
                        Verifikasi Keamanan
                    </h3>
                    <div class="max-w-2xl">
                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <span id="captcha-question" class="block text-xl font-bold text-blue-600 mb-2">
                                    {{ session('captcha_num1') }} + {{ session('captcha_num2') }} = ?
                                </span>
                                <input type="number"
                                       name="captcha"
                                       id="captcha"
                                       required
                                       placeholder="Hasil penjumlahan"
                                       class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-lg font-semibold @if(session('captcha_error')) border-red-500 @endif">
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
                                    class="shrink-0 bg-blue-600 hover:bg-blue-700 text-white w-14 h-14 rounded-xl font-semibold transition-all flex items-center justify-center text-xl shadow-lg hover:shadow-xl"
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

                <!-- Submit Button -->
                <div class="p-8 bg-gray-50">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <i class="fas fa-lock text-green-600"></i>
                            <span>Data Anda aman dan terenkripsi</span>
                        </div>
                        <button type="submit"
                                class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-10 py-4 rounded-xl font-bold shadow-xl transition-all transform hover:scale-105 flex items-center justify-center gap-3">
                            <i class="fas fa-paper-plane"></i>
                            Kirim Penilaian
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script>
        // Character counter for saran textarea
        const saranTextarea = document.getElementById('saran');
        const charCount = document.getElementById('charCount');

        saranTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length + '/1000';

            if (length > 1000) {
                charCount.classList.add('text-red-500');
                charCount.classList.remove('text-gray-400');
            } else {
                charCount.classList.add('text-gray-400');
                charCount.classList.remove('text-red-500');
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

            fetch('{{ route("skm.refresh-captcha") }}')
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
        document.getElementById('skmForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
            submitBtn.disabled = true;
        });
    </script>
</x-front-layout>
