<x-front-layout>
    <!-- Hero Section -->
    <section
        class="relative bg-gradient-to-br from-blue-800 via-blue-700 to-indigo-900 text-white py-16 overflow-hidden">
        <div class="absolute top-0 right-0 opacity-5 transform translate-x-16 -translate-y-8">
            <i class="fas fa-book-open text-9xl"></i>
        </div>
        <div class="absolute bottom-0 left-0 opacity-5 transform -translate-x-8 translate-y-8">
            <i class="fas fa-info-circle text-8xl"></i>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div
                class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full border border-white/20 mb-4">
                <i class="fas fa-question-circle text-yellow-400"></i>
                <span class="text-sm font-medium">Pusat Bantuan & Panduan</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Panduan <span class="text-yellow-400">Perizinan</span>
            </h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                Temukan langkah-langkah, petunjuk teknis, dan informasi bantuan untuk memudahkan proses pengajuan perizinan Anda.
            </p>
        </div>

        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-white/30 to-transparent">
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Search -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-12 border border-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 text-xl">
                            <i class="fas fa-search"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Cari Panduan</h3>
                            <p class="text-xs text-gray-500">Temukan panduan yang Anda butuhkan</p>
                        </div>
                    </div>

                    <form action="{{ route('panduan.public') }}" method="GET" class="w-full md:w-auto flex gap-3">
                        <div class="relative flex-1 md:w-80">
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Ketik kata kunci panduan..."
                                class="w-full pl-4 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                        </div>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-md">
                            Cari
                        </button>
                    </form>
                </div>
            </div>

            <!-- Panduan Cards -->
            @if ($panduan->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($panduan as $item)
                        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col group">
                            <div class="p-6 flex-1">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-3 py-1 rounded-full uppercase tracking-wider">
                                        PDF Dokumen
                                    </span>
                                </div>

                                <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-blue-600 transition-colors line-clamp-2">
                                    {{ $item->nama_panduan }}
                                </h3>

                                <p class="text-gray-600 text-sm leading-relaxed mb-6 line-clamp-3">
                                    {{ $item->deskripsi ?? 'Petunjuk lengkap mengenai proses perizinan dan penggunaan fitur aplikasi JITU.' }}
                                </p>
                            </div>

                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-xs text-gray-500 italic">
                                    <i class="far fa-clock mr-1"></i> {{ $item->created_at->isoFormat('D MMM Y') }}
                                </span>
                                <a href="{{ route('panduan.public.preview', $item->slug) }}" target="_blank"
                                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition-all shadow-sm">
                                    <i class="fas fa-eye"></i> Baca Panduan
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($panduan->hasPages())
                    <div class="mt-12">
                        {{ $panduan->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-2xl shadow-lg p-16 text-center border border-gray-100">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-full mb-6">
                        <i class="fas fa-book text-5xl text-gray-300"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Tidak Menemukan Panduan</h3>
                    <p class="text-gray-500 max-w-md mx-auto">
                        Maaf, panduan yang Anda cari tidak ditemukan atau belum tersedia saat ini.
                    </p>
                    <div class="mt-8">
                        <a href="{{ route('panduan.public') }}" class="text-blue-600 font-bold hover:underline">
                            <i class="fas fa-sync-alt mr-2"></i>Tampilkan Semua Panduan
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Helpdesk Banner -->
    <section class="py-12 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl p-8 md:p-12 shadow-2xl relative overflow-hidden text-white">
                <div class="absolute top-0 right-0 opacity-10 transform translate-x-12 -translate-y-12">
                    <i class="fas fa-headset text-[200px]"></i>
                </div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left">
                    <div>
                        <h2 class="text-3xl font-bold mb-3 text-white">Masih Bingung?</h2>
                        <p class="text-blue-100 text-lg">Jangan ragu untuk menghubungi tim teknis kami jika Anda membutuhkan bantuan lebih lanjut.</p>
                    </div>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="https://wa.me/{{ formatWhatsAppNumber(setting('whatsapp', '081234567890')) }}" target="_blank"
                            class="bg-white text-blue-700 hover:bg-blue-50 px-8 py-4 rounded-2xl font-bold transition-all shadow-lg flex items-center gap-3">
                            <i class="fab fa-whatsapp text-2xl"></i> Hubungi WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-front-layout>
