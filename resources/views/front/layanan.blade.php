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
                            <span class="text-gray-400">({{ $layanan->total() + ($includePencabutanMedis ? 1 : 0) }} hasil)</span>
                        </p>
                    </div>
                @endif
            </div>

            @if ($layanan->count() > 0 || $includePencabutanMedis)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @if($includePencabutanMedis && request('page', 1) == 1)
                        <!-- Card Representatif Pencabutan Medis -->
                        <div class="cursor-pointer group" onclick="openPencabutanModal()">
                            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-red-100 h-full flex flex-col justify-between">
                                <div class="p-6 flex-1">
                                    <!-- Icon Header -->
                                    <div class="flex items-center gap-4 mb-4">
                                        <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-700 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                            <i class="fas fa-file-medical-alt text-white text-2xl"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-lg font-bold text-gray-800 group-hover:text-red-600 transition-colors">
                                                PENCABUTAN SURAT IZIN PRAKTIK TENAGA MEDIS DAN TENAGA KESEHATAN
                                            </h3>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <p class="text-gray-600 text-sm mb-4">
                                        Layanan khusus untuk pengajuan pencabutan Surat Izin Praktik (SIP) bagi tenaga medis dan tenaga kesehatan di wilayah Kabupaten Banjarnegara.
                                    </p>
                                </div>
                                <div class="px-6 pb-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-list-ul mr-1"></i> {{ $pencabutanMedisItems->count() }} Layanan
                                    </span>
                                    <span class="inline-flex items-center gap-2 text-red-600 font-semibold text-sm group-hover:gap-3 transition-all">
                                        Lihat Daftar <i class="fas fa-arrow-right"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

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
                                                class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition-colors">
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

                <!-- Pagination -->
                @if ($layanan->hasPages())
                    <div class="mt-12 flex justify-center">
                        {{ $layanan->links() }}
                    </div>
                @endif
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

    <!-- Modal List Pencabutan Medis -->
    <div id="pencabutanModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[85vh] overflow-hidden flex flex-col transform transition-all duration-300 scale-95 opacity-0" id="pencabutanModalContent">
            <!-- Header -->
            <div class="bg-gradient-to-br from-red-600 via-red-700 to-red-800 text-white p-6 flex-shrink-0 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-medical-alt text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">PENCABUTAN SURAT IZIN PRAKTIK TENAGA MEDIS DAN TENAGA KESEHATAN</h2>
                        <p class="text-red-100 text-xs mt-0.5">Pilih jenis perijinan pencabutan medis yang Anda inginkan</p>
                    </div>
                </div>
                <button onclick="closePencabutanModal()" class="text-white/80 hover:text-white transition-colors bg-white/10 hover:bg-white/20 rounded-full p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <!-- Body -->
            <div class="flex-1 overflow-y-auto p-6 space-y-3 bg-gray-50">
                @foreach($pencabutanMedisItems as $pencabutanItem)
                    <a href="{{ route('layanan.show', $pencabutanItem->id) }}" class="block bg-white hover:bg-red-50/30 p-4 rounded-xl border border-gray-200 hover:border-red-300 transition-all shadow-sm hover:shadow-md group">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 pr-4">
                                <h4 class="font-bold text-gray-800 group-hover:text-red-600 transition-colors">
                                    {{ $pencabutanItem->nama_perijinan }}
                                </h4>
                                @if($pencabutanItem->dasar_hukum)
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">
                                        {{ strip_tags($pencabutanItem->dasar_hukum) }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-gray-400 group-hover:text-red-600 group-hover:translate-x-1 transition-all">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <!-- Footer -->
            <div class="p-4 border-t border-gray-200 bg-white flex justify-end flex-shrink-0">
                <button onclick="closePencabutanModal()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function openPencabutanModal() {
            const modal = document.getElementById('pencabutanModal');
            const modalContent = document.getElementById('pencabutanModalContent');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closePencabutanModal() {
            const modal = document.getElementById('pencabutanModal');
            const modalContent = document.getElementById('pencabutanModalContent');
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }
    </script>

</x-front-layout>
