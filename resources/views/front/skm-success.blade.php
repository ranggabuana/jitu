<x-front-layout>
    <!-- Success Section -->
    <section class="min-h-screen bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 flex items-center justify-center py-20">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-blue-300 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-blue-400 rounded-full blur-3xl"></div>
        </div>

        <!-- Floating Icons -->
        <div class="absolute inset-0 overflow-hidden">
            <i class="fas fa-check-circle absolute top-20 left-1/4 text-6xl text-white/5"></i>
            <i class="fas fa-star absolute top-40 right-1/3 text-5xl text-white/5"></i>
            <i class="fas fa-heart absolute bottom-32 left-1/3 text-4xl text-white/5"></i>
            <i class="fas fa-smile absolute bottom-20 right-1/4 text-7xl text-white/5"></i>
        </div>

        <div class="relative z-10 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Success Icon -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-full shadow-2xl mb-6 animate-bounce">
                    <i class="fas fa-check text-5xl text-green-500"></i>
                </div>
            </div>

            <!-- Content -->
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-6">
                Terima Kasih!
            </h1>
            <p class="text-xl text-blue-100 mb-8 leading-relaxed">
                Penilaian Anda telah berhasil dikirim. Masukan Anda sangat berharga bagi kami 
                untuk meningkatkan kualitas pelayanan.
            </p>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 gap-4 mb-10">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-3xl font-bold text-yellow-400 mb-2">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="text-white font-semibold">Penilaian Tersimpan</div>
                    <div class="text-blue-200 text-sm mt-1">Data aman terenkripsi</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-3xl font-bold text-green-400 mb-2">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="text-white font-semibold">Dampak Nyata</div>
                    <div class="text-blue-200 text-sm mt-1">Untuk layanan lebih baik</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('landing') }}" 
                   class="inline-flex items-center justify-center gap-2 bg-white text-blue-700 hover:bg-blue-50 px-8 py-4 rounded-xl font-bold shadow-xl transition-all transform hover:scale-105">
                    <i class="fas fa-home"></i>
                    Kembali ke Beranda
                </a>
                <a href="{{ route('informasi') }}" 
                   class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 text-white border border-white/30 px-8 py-4 rounded-xl font-bold backdrop-blur-sm transition-all">
                    <i class="fas fa-newspaper"></i>
                    Lihat Berita Lainnya
                </a>
            </div>

            <!-- Additional Info -->
            <div class="mt-12 p-6 bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20">
                <p class="text-blue-100 text-sm">
                    <i class="fas fa-info-circle mr-2 text-yellow-400"></i>
                    Setiap penilaian akan ditinjau oleh tim kami dan digunakan sebagai bahan evaluasi 
                    untuk peningkatan kualitas pelayanan publik di Kabupaten Banjarnegara.
                </p>
            </div>
        </div>
    </section>
</x-front-layout>
