<x-front-layout>

    <!-- Article Header -->
    <section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-blue-800 text-white py-16 overflow-hidden">
        <!-- News Pattern Decoration -->
        <div class="absolute top-0 right-0 opacity-5 transform translate-x-16 -translate-y-8">
            <i class="fas fa-newspaper text-9xl"></i>
        </div>
        <div class="absolute bottom-0 left-0 opacity-5 transform -translate-x-8 translate-y-8">
            <i class="fas fa-pen-fancy text-8xl"></i>
        </div>
        
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex items-center gap-2 mb-4">
                <span class="bg-white/20 backdrop-blur-sm text-white px-3 py-1 rounded text-xs font-semibold uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-newspaper text-xs"></i>
                    Berita
                </span>
                <span class="text-blue-200 text-sm">•</span>
                <span class="text-blue-200 text-sm">{{ $berita->created_at->format('d M Y') }}</span>
                <span class="text-blue-200 text-sm">•</span>
                <span class="text-blue-200 text-sm">{{ number_format($berita->views) }} kali dibaca</span>
            </div>

            <h1 class="text-4xl md:text-5xl font-bold mb-8 leading-tight">
                {{ $berita->judul }}
            </h1>

            <div class="flex items-center gap-3 text-blue-200 text-sm">
                <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white text-sm"></i>
                </div>
                <div>
                    <span class="text-white font-medium">{{ $berita->user?->name ?? 'Admin' }}</span>
                    <span class="text-blue-300 mx-2">•</span>
                    <span>{{ $berita->created_at->format('d M Y, H:i') }} WIB</span>
                </div>
            </div>
        </div>
        
        <!-- Bottom Border Accent -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
    </section>

    <!-- Article Content -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                <!-- Featured Image -->
                @if ($berita->gambar)
                    <div class="relative">
                        <img src="{{ asset($berita->gambar) }}" alt="{{ $berita->judul }}"
                            class="w-full h-96 object-cover">
                        <div
                            class="absolute bottom-4 right-4 bg-black/50 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm">
                            <i class="fas fa-image mr-2"></i>Dokumentasi
                        </div>
                    </div>
                @else
                    <div
                        class="w-full h-96 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                        <div class="text-center p-8">
                            <i class="fas fa-image text-7xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500 font-medium text-lg">Tidak ada gambar</p>
                            <p class="text-gray-400 text-sm mt-2">Dokumentasi visual tidak tersedia untuk berita ini</p>
                        </div>
                    </div>
                @endif

                <!-- Content -->
                <div class="p-8 md:p-12">

                    <!-- Article Body -->
                    <div class="prose prose-lg max-w-none">
                        <div class="text-gray-700 leading-relaxed space-y-6">
                            {!! $berita->konten !!}
                        </div>
                    </div>

                </div>
            </div>

            <!-- Related Articles -->
            @if ($relatedBerita->count() > 0)
                <div class="mt-16">
                    <h2 class="text-3xl font-bold text-gray-800 mb-8 flex items-center gap-3">
                        <i class="fas fa-newspaper text-blue-600"></i>
                        Berita Terkait
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($relatedBerita as $related)
                            <article
                                class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all group">
                                <div class="relative overflow-hidden">
                                    @if ($related->gambar)
                                        <img src="{{ asset($related->gambar) }}" alt="{{ $related->judul }}"
                                            class="w-full h-40 object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <div
                                            class="w-full h-40 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                            <i class="fas fa-image text-3xl text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3
                                        class="text-lg font-bold text-gray-800 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                        {{ $related->judul }}
                                    </h3>
                                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                                        <i class="far fa-calendar mr-1"></i>{{ $related->created_at->format('d M Y') }}
                                    </div>
                                    <a href="{{ route('informasi.show', $related->slug) }}"
                                        class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:text-blue-800 transition-colors text-sm">
                                        Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Back Button -->
            <div class="mt-12">
                <a href="{{ route('informasi') }}"
                    class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-full font-semibold transition-colors">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Berita
                </a>
            </div>
        </div>
    </section>

    <script>
        function copyLink() {
            navigator.clipboard.writeText(window.location.href);
            alert('Link berhasil disalin!');
        }
    </script>
</x-front-layout>
