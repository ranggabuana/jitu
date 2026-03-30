<x-front-layout>
    <!-- Success Section -->
    <section class="min-h-screen bg-gradient-to-br from-red-600 via-red-700 to-orange-600 flex items-center justify-center py-20">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-orange-300 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-red-400 rounded-full blur-3xl"></div>
        </div>

        <!-- Floating Icons -->
        <div class="absolute inset-0 overflow-hidden">
            <i class="fas fa-check-circle absolute top-20 left-1/4 text-6xl text-white/5"></i>
            <i class="fas fa-bullhorn absolute top-40 right-1/3 text-5xl text-white/5"></i>
            <i class="fas fa-exclamation-circle absolute bottom-32 left-1/3 text-4xl text-white/5"></i>
            <i class="fas fa-hand-paper absolute bottom-20 right-1/4 text-7xl text-white/5"></i>
        </div>

        <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Success Icon -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-full shadow-2xl mb-6 animate-bounce">
                    <i class="fas fa-check text-5xl text-green-500"></i>
                </div>
            </div>

            <!-- Content -->
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-6">
                Pengaduan Terkirim!
            </h1>
            <p class="text-xl text-red-100 mb-8 leading-relaxed">
                Terima kasih telah menyampaikan pengaduan. Kami akan menindaklanjuti
                pengaduan Anda secepatnya.
            </p>

            <!-- Pengaduan Info Card -->
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20 mb-10">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <i class="fas fa-receipt text-3xl text-yellow-400"></i>
                    <h2 class="text-2xl font-bold text-white">Nomor Pengaduan Anda</h2>
                </div>
                @if($noPengaduan ?? null)
                    <div class="bg-white rounded-xl px-6 py-4 mb-4">
                        <p class="text-3xl font-bold text-red-600 tracking-wider">
                            {{ $noPengaduan }}
                        </p>
                    </div>
                    <p class="text-red-100 text-sm">
                        <i class="fas fa-info-circle mr-2"></i>
                        Simpan nomor ini untuk tracking status pengaduan Anda. Nomor pengaduan juga telah dikirim ke email Anda.
                    </p>
                @else
                    <div class="bg-white rounded-xl px-6 py-4 mb-4">
                        <p class="text-lg text-gray-600">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            Nomor pengaduan telah dikirim ke email Anda.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <div class="text-2xl font-bold text-yellow-400 mb-2">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="text-white font-semibold">Proses 3x24 Jam</div>
                    <div class="text-red-100 text-sm mt-1">Tim kami akan segera merespon</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <div class="text-2xl font-bold text-blue-400 mb-2">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="text-white font-semibold">Notifikasi Email</div>
                    <div class="text-red-100 text-sm mt-1">Update status via email</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <div class="text-2xl font-bold text-green-400 mb-2">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="text-white font-semibold">Konfirmasi WA</div>
                    <div class="text-red-100 text-sm mt-1">Jika diperlukan</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('landing') }}"
                   class="inline-flex items-center justify-center gap-2 bg-white text-red-700 hover:bg-red-50 px-8 py-4 rounded-xl font-bold shadow-xl transition-all transform hover:scale-105">
                    <i class="fas fa-home"></i>
                    Kembali ke Beranda
                </a>
                <a href="{{ route('pengaduan.create') }}"
                   class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 text-white border border-white/30 px-8 py-4 rounded-xl font-bold backdrop-blur-sm transition-all">
                    <i class="fas fa-plus"></i>
                    Buat Pengaduan Lain
                </a>
            </div>

            <!-- Additional Info -->
            <div class="mt-12 p-6 bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20">
                <p class="text-red-100 text-sm">
                    <i class="fas fa-shield-alt mr-2 text-yellow-400"></i>
                    Identitas Anda aman dan dijaga kerahasiaannya. Pengaduan akan diproses sesuai
                    dengan prosedur yang berlaku.
                </p>
            </div>
        </div>
    </section>
</x-front-layout>
