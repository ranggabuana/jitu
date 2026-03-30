<x-front-layout>

    <!-- Header Section -->
    <section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-blue-800 text-white py-16 overflow-hidden">
        <div class="absolute top-0 right-0 opacity-5 transform translate-x-16 -translate-y-8">
            <i class="fas fa-file-signature text-9xl"></i>
        </div>
        <div class="absolute bottom-0 left-0 opacity-5 transform -translate-x-8 translate-y-8">
            <i class="fas fa-stamp text-8xl"></i>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div
                class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full border border-white/20 mb-4">
                <i class="fas fa-concierge-bell text-yellow-400"></i>
                <span class="text-sm font-medium">Layanan Perijinan Terpadu</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Daftar <span class="text-yellow-400">Layanan</span> Perijinan
            </h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                Pilih jenis perijinan yang Anda butuhkan untuk memulai proses pengajuan secara online
            </p>
        </div>

        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-white/30 to-transparent">
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Search Bar -->
            <div class="mb-8">
                <form action="{{ route('layanan') }}" method="GET" class="max-w-2xl mx-auto">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari layanan perijinan..."
                            class="w-full px-6 py-4 pl-14 rounded-2xl text-gray-800 shadow-lg focus:ring-4 focus:ring-blue-400 focus:outline-none text-lg border border-gray-200">
                        <i
                            class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl"></i>
                        @if (request('search'))
                            <a href="{{ route('layanan') }}"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-lg"></i>
                            </a>
                        @else
                            <button type="submit"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl font-semibold transition-colors">
                                Cari
                            </button>
                        @endif
                    </div>
                </form>
                @if (request('search'))
                    <div class="text-center mt-4">
                        <p class="text-gray-600">
                            Menampilkan hasil pencarian untuk: <strong
                                class="text-blue-600">"{{ request('search') }}"</strong>
                            <span class="text-gray-400">({{ $layanan->total() }} hasil)</span>
                        </p>
                    </div>
                @endif
            </div>

            @if ($layanan->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($layanan as $item)
                        <a href="{{ route('layanan.show', $item->id) }}" class="group">
                            <div
                                class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 h-full">
                                <div class="p-6">
                                    <!-- Icon Header -->
                                    <div class="flex items-center gap-4 mb-4">
                                        <div
                                            class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                            <i class="fas fa-file-signature text-white text-2xl"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3
                                                class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition-colors line-clamp-2">
                                                {{ $item->nama_perijinan }}
                                            </h3>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                        {{ Str::limit(strip_tags($item->dasar_hukum ?? 'Tidak ada deskripsi'), 120) }}
                                    </p>

                                    <!-- Footer -->
                                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-clock mr-1"></i> Proses Online
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-2 text-blue-600 font-semibold text-sm group-hover:gap-3 transition-all">
                                            Detail <i class="fas fa-arrow-right"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    @if (request('search'))
                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-search text-gray-400 text-5xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Layanan Tidak Ditemukan</h3>
                        <p class="text-gray-500 mb-6">
                            Tidak ada layanan yang cocok dengan pencarian <strong>"{{ request('search') }}"</strong>
                        </p>
                        <a href="{{ route('layanan') }}"
                            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                            <i class="fas fa-arrow-left"></i> Lihat Semua Layanan
                        </a>
                    @else
                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-folder-open text-gray-400 text-5xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Layanan</h3>
                        <p class="text-gray-500">Layanan perijinan akan segera ditambahkan</p>
                    @endif
                </div>
            @endif
        </div>
    </section>

</x-front-layout>
