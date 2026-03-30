<x-front-layout>
    <!-- Hero Section dengan Dekorasi -->
    <section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-blue-800 text-white py-16 overflow-hidden">
        <div class="absolute top-0 right-0 opacity-5 transform translate-x-16 -translate-y-8">
            <i class="fas fa-newspaper text-9xl"></i>
        </div>
        <div class="absolute bottom-0 left-0 opacity-5 transform -translate-x-8 translate-y-8">
            <i class="fas fa-bullhorn text-8xl"></i>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div
                class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full border border-white/20 mb-4">
                <i class="fas fa-bell text-yellow-400"></i>
                <span class="text-sm font-medium">Tetap Terupdate dengan Informasi Terbaru</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Pusat <span class="text-yellow-400">Informasi</span> & Berita
            </h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                Dapatkan informasi terkini seputar perizinan, berita terbaru, dan update penting
                dari DPMPTSP Kabupaten Banjarnegara
            </p>
        </div>

        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-white/30 to-transparent">
        </div>
    </section>

    <!-- Artikel Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($featuredBerita || $secondaryBerita->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                    <!-- Featured Article (Large) -->
                    @if ($featuredBerita)
                        <article class="relative group overflow-hidden rounded-3xl shadow-xl">
                            @if ($featuredBerita->gambar)
                                <img src="{{ asset($featuredBerita->gambar) }}" alt="{{ $featuredBerita->judul }}"
                                    class="w-full h-96 object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div
                                    class="w-full h-96 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                    <div class="text-center p-8">
                                        <i class="fas fa-image text-6xl text-gray-400 mb-4"></i>
                                        <p class="text-gray-500 font-medium">Tidak ada gambar</p>
                                    </div>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent">
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-8">
                                <div class="flex items-center gap-3 mb-4">
                                    <span
                                        class="bg-blue-600 text-white px-4 py-1 rounded-full text-sm font-semibold">Featured</span>
                                    <span class="text-white/80 text-sm"><i
                                            class="far fa-calendar mr-2"></i>{{ $featuredBerita->created_at->format('d M Y') }}</span>
                                </div>
                                <h2 class="text-3xl font-bold text-white mb-3 line-clamp-2">
                                    {{ $featuredBerita->judul }}
                                </h2>
                                <p class="text-gray-200 mb-4 line-clamp-3">
                                    {{ Str::limit(strip_tags($featuredBerita->konten), 150) }}
                                </p>
                                <a href="{{ route('informasi.show', $featuredBerita->slug) }}"
                                    class="inline-flex items-center gap-2 text-yellow-400 font-semibold hover:text-yellow-300 transition-colors">
                                    Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    @endif

                    <!-- Secondary Articles (Stacked) -->
                    <div class="space-y-6">
                        @foreach ($secondaryBerita as $berita)
                            <article
                                class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all group flex">
                                <div class="w-2/5 relative overflow-hidden">
                                    @if ($berita->gambar)
                                        <img src="{{ asset($berita->gambar) }}" alt="{{ $berita->judul }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <div
                                            class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                            <i class="fas fa-image text-4xl text-gray-400"></i>
                                        </div>
                                    @endif
                                    @if ($loop->first)
                                        <div
                                            class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                            <i class="fas fa-fire mr-1"></i> Populer
                                        </div>
                                    @endif
                                </div>
                                <div class="w-3/5 p-5">
                                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                                        <span
                                            class="bg-blue-100 text-blue-600 px-2 py-1 rounded-md font-semibold">Berita</span>
                                        <span><i
                                                class="far fa-calendar mr-1"></i>{{ $berita->created_at->format('d M Y') }}</span>
                                    </div>
                                    <h3
                                        class="text-lg font-bold text-gray-800 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                        {{ $berita->judul }}
                                    </h3>
                                    <p class="text-gray-600 text-sm line-clamp-2">
                                        {{ Str::limit(strip_tags($berita->konten), 80) }}
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Regular Articles Grid -->
            @if ($beritaGrid->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($beritaGrid as $berita)
                        <article
                            class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all group">
                            <div class="relative overflow-hidden">
                                @if ($berita->gambar)
                                    <img src="{{ asset($berita->gambar) }}" alt="{{ $berita->judul }}"
                                        class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div
                                        class="w-full h-56 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                        <div class="text-center">
                                            <i class="fas fa-image text-5xl text-gray-400 mb-2"></i>
                                            <p class="text-gray-500 text-sm font-medium">Tidak ada gambar</p>
                                        </div>
                                    </div>
                                @endif
                                <div
                                    class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-gray-700">
                                    <i class="far fa-eye mr-1"></i> {{ number_format($berita->views) }}
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                                    <span
                                        class="bg-blue-100 text-blue-600 px-2 py-1 rounded-md font-semibold">Berita</span>
                                    <span><i
                                            class="far fa-calendar mr-1"></i>{{ $berita->created_at->format('d M Y') }}</span>
                                </div>
                                <h3
                                    class="text-xl font-bold text-gray-800 mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                    {{ $berita->judul }}
                                </h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                    {{ Str::limit(strip_tags($berita->konten), 100) }}
                                </p>
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('informasi.show', $berita->slug) }}"
                                        class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:text-blue-800 transition-colors">
                                        Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12 flex justify-center">
                    {{ $beritaGrid->links('pagination::tailwind') }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-full mb-6">
                        <i class="fas fa-newspaper text-5xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Belum Ada Berita</h3>
                    <p class="text-gray-500 max-w-md mx-auto">
                        Belum ada berita yang tersedia. Silakan kembali lagi nanti untuk update terbaru.
                    </p>
                </div>
            @endif
        </div>
    </section>

    <script>
        // Kategori filter (optional - untuk future implementation)
        document.querySelectorAll('.kategori-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.kategori-btn').forEach(b => {
                    b.classList.remove('bg-blue-600', 'text-white');
                    b.classList.add('bg-white', 'text-gray-700');
                });
                this.classList.remove('bg-white', 'text-gray-700');
                this.classList.add('bg-blue-600', 'text-white');
            });
        });
    </script>
</x-front-layout>
