<x-front-layout>
    <!-- Hero Slider -->
    <header class="relative overflow-hidden h-[600px]">
        <!-- Slider Container -->
        <div id="heroSlider" class="relative w-full h-full">
            <!-- Slide 1: Default Hero -->
            <div class="slide active absolute inset-0 transition-opacity duration-1000">
                <!-- Background Image with Overlay -->
                <div class="absolute inset-0 z-0">
                    <img src="{{ asset('assets/images/slider1.jpg') }}" alt="Layanan Perizinan" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-blue-900/70"></div>
                </div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 h-full flex items-center">
                    <div class="w-full md:w-1/2 space-y-6 animate-fadeInUp">
                        <div class="inline-block bg-blue-500/30 backdrop-blur-sm px-4 py-1.5 rounded-full border border-blue-400/30 text-blue-100 text-sm font-semibold mb-2">
                            <i class="fas fa-star text-yellow-400 mr-2"></i> Layanan Perizinan Terpadu Satu Pintu
                        </div>
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight text-white">
                            Urus Izin Kini <br>
                            <span class="text-blue-300">Lebih Mudah & Cepat</span>
                        </h1>
                        <p class="text-lg text-blue-100 max-w-lg leading-relaxed">
                            Sistem pelayanan perizinan terintegrasi Kabupaten Banjarnegara.
                            Ajukan permohonan, pantau status, dan cetak izin dari rumah.
                        </p>
                        <div class="flex flex-wrap gap-4 pt-4">
                            <a href="{{ route('front.register') }}"
                                class="bg-white text-blue-700 hover:bg-blue-50 px-8 py-4 rounded-xl font-bold shadow-xl transition-all flex items-center gap-3 transform hover:scale-105">
                                <i class="fas fa-user-plus"></i> Daftar Sekarang
                            </a>
                            <a href="#track"
                                class="bg-blue-700/50 hover:bg-blue-700/70 text-white border border-blue-500/50 px-8 py-4 rounded-xl font-bold backdrop-blur-md transition-all flex items-center gap-3">
                                <i class="fas fa-search"></i> Cek Status
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2+: Berita Articles -->
            @foreach($beritaSlider as $berita)
                <div class="slide absolute inset-0 transition-opacity duration-1000 opacity-0">
                    <!-- Background Image with Overlay -->
                    <div class="absolute inset-0 z-0">
                        @if($berita->gambar)
                            <img src="{{ asset($berita->gambar) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-800 via-blue-700 to-indigo-900"></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
                    </div>

                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 h-full flex items-center">
                        <div class="w-full md:w-2/3 space-y-6 animate-fadeInUp">
                            <!-- Category Badge -->
                            <div class="inline-block bg-red-500/90 backdrop-blur-sm px-4 py-1.5 rounded-full border border-red-400/30 text-white text-sm font-semibold">
                                <i class="fas fa-newspaper mr-2"></i> Berita Terbaru
                            </div>
                            
                            <!-- Title with Overlay Effect -->
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight text-white drop-shadow-lg">
                                {{ $berita->judul }}
                            </h2>
                            
                            <!-- Excerpt -->
                            <p class="text-lg text-gray-200 max-w-2xl leading-relaxed">
                                {{ Str::limit(strip_tags($berita->konten), 200) }}
                            </p>
                            
                            <!-- Meta Info -->
                            <div class="flex items-center gap-6 text-gray-300">
                                <span class="flex items-center gap-2">
                                    <i class="far fa-calendar text-yellow-400"></i>
                                    {{ $berita->created_at->isoFormat('D MMMM Y') }}
                                </span>
                                @if($berita->user)
                                    <span class="flex items-center gap-2">
                                        <i class="far fa-user text-yellow-400"></i>
                                        {{ $berita->user->name }}
                                    </span>
                                @endif
                                <span class="flex items-center gap-2">
                                    <i class="far fa-eye text-yellow-400"></i>
                                    {{ number_format($berita->views) }} views
                                </span>
                            </div>
                            
                            <!-- Read More Button -->
                            <div class="pt-4 flex flex-wrap gap-4">
                                <a href="{{ route('berita.show', $berita->id) }}"
                                    class="bg-white text-blue-700 hover:bg-blue-50 px-8 py-4 rounded-xl font-bold shadow-xl transition-all flex items-center gap-3 transform hover:scale-105">
                                    <i class="fas fa-arrow-right"></i>
                                    <span>Baca Selengkapnya</span>
                                </a>
                                <a href="{{ route('berita.show', $berita->id) }}"
                                    class="bg-blue-700/50 hover:bg-blue-700/70 text-white border border-blue-500/50 px-8 py-4 rounded-xl font-bold backdrop-blur-md transition-all flex items-center gap-3">
                                    <i class="fas fa-newspaper"></i>
                                    <span>Lihat Berita</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Slider Navigation Dots -->
        <div class="absolute bottom-12 left-1/2 transform -translate-x-1/2 z-30 flex items-center gap-3">
            @php $totalSlides = 1 + $beritaSlider->count(); @endphp
            @for($i = 0; $i < $totalSlides; $i++)
                <button onclick="goToSlide({{ $i }})" 
                    class="slider-dot w-3 h-3 rounded-full border-2 border-white transition-all duration-300 {{ $i === 0 ? 'bg-white' : 'bg-white/30' }}"
                    aria-label="Slide {{ $i + 1 }}">
                </button>
            @endfor
        </div>

        <!-- Slider Arrows -->
        <button onclick="prevSlide()" class="absolute left-4 top-1/2 transform -translate-y-1/2 z-20 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white p-4 rounded-full transition-all">
            <i class="fas fa-chevron-left text-2xl"></i>
        </button>
        <button onclick="nextSlide()" class="absolute right-4 top-1/2 transform -translate-y-1/2 z-20 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white p-4 rounded-full transition-all">
            <i class="fas fa-chevron-right text-2xl"></i>
        </button>
    </header>

    <!-- Slider JavaScript -->
    <script>
        let currentSlide = 0;
        let slideInterval;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dot');
        const autoSlideDelay = 5000; // 5 seconds

        function showSlide(index) {
            // Wrap around
            if (index >= slides.length) currentSlide = 0;
            else if (index < 0) currentSlide = slides.length - 1;
            else currentSlide = index;

            // Update slides
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                slide.classList.remove('opacity-100');
                slide.classList.add('opacity-0');
                
                if (i === currentSlide) {
                    slide.classList.add('active');
                    slide.classList.remove('opacity-0');
                    slide.classList.add('opacity-100');
                }
            });

            // Update dots
            dots.forEach((dot, i) => {
                if (i === currentSlide) {
                    dot.classList.remove('bg-white/30');
                    dot.classList.add('bg-white');
                } else {
                    dot.classList.remove('bg-white');
                    dot.classList.add('bg-white/30');
                }
            });
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
            resetInterval();
        }

        function prevSlide() {
            showSlide(currentSlide - 1);
            resetInterval();
        }

        function goToSlide(index) {
            showSlide(index);
            resetInterval();
        }

        function startInterval() {
            slideInterval = setInterval(() => {
                showSlide(currentSlide + 1);
            }, autoSlideDelay);
        }

        function resetInterval() {
            clearInterval(slideInterval);
            startInterval();
        }

        // Initialize slider
        document.addEventListener('DOMContentLoaded', function() {
            showSlide(0);
            startInterval();
        });

        // Pause on hover
        const slider = document.getElementById('heroSlider');
        slider.addEventListener('mouseenter', () => clearInterval(slideInterval));
        slider.addEventListener('mouseleave', startInterval);
    </script>

    <!-- Tracking Section -->
    <section id="track" class="relative -mt-10 z-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                <h2 class="text-center text-xl font-bold text-gray-800 mb-6 flex items-center justify-center gap-2">
                    <i class="fas fa-search-location text-blue-600"></i> 
                    Lacak Status Permohonan
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Track Perizinan -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white text-xl">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg">Lacak Perizinan</h3>
                                <p class="text-xs text-gray-500">Cek status permohonan perizinan Anda</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="relative flex-1">
                                <i class="fas fa-qrcode absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" 
                                    placeholder="Masukkan Nomor Registrasi Izin"
                                    class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            </div>
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all flex items-center gap-2">
                                <i class="fas fa-search"></i>
                                <span class="hidden lg:inline">Lacak</span>
                            </button>
                        </div>
                    </div>

                    <!-- Track Pengaduan -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 border border-orange-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-orange-600 rounded-xl flex items-center justify-center text-white text-xl">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg">Lacak Pengaduan</h3>
                                <p class="text-xs text-gray-500">Cek tindak lanjut pengaduan Anda</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="relative flex-1">
                                <i class="fas fa-ticket-alt absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" 
                                    id="trackPengaduanInput"
                                    placeholder="Masukkan Nomor Pengaduan"
                                    class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all"
                                    onkeypress="if(event.key === 'Enter') trackPengaduan()">
                            </div>
                            <button onclick="trackPengaduan()" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all flex items-center gap-2">
                                <i class="fas fa-search"></i>
                                <span class="hidden lg:inline">Lacak</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Track Pengaduan Modal -->
    <div id="trackModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeTrackModal()"></div>
        
        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full relative z-10 overflow-hidden animate-modalSlideUp">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-orange-600 to-red-600 text-white p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <i class="fas fa-bullhorn text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold">Detail Pengaduan</h3>
                                <p class="text-sm text-orange-100">Status tindak lanjut pengaduan Anda</p>
                            </div>
                        </div>
                        <button onclick="closeTrackModal()" class="w-10 h-10 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center transition-all">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div id="trackModalBody" class="p-6">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Loading -->
    <div id="trackLoading" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="bg-black/60 backdrop-blur-sm absolute inset-0" onclick="closeTrackModal()"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-8 relative z-10 text-center">
            <div class="w-16 h-16 border-4 border-orange-200 border-t-orange-600 rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-600 font-medium">Memuat data pengaduan...</p>
        </div>
    </div>

    <!-- Modal Scripts -->
    <script>
        function trackPengaduan() {
            const noPengaduan = document.getElementById('trackPengaduanInput').value.trim();

            if (!noPengaduan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan masukkan nomor pengaduan!',
                    confirmButtonColor: '#ea580c',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Show loading
            document.getElementById('trackLoading').classList.remove('hidden');
            document.getElementById('trackLoading').classList.add('flex');

            // Fetch data
            fetch('{{ route("pengaduan.track") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ no_pengaduan: noPengaduan })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('trackLoading').classList.add('hidden');
                document.getElementById('trackLoading').classList.remove('flex');

                if (data.success) {
                    showTrackModal(data.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Ditemukan',
                        text: data.message || 'Nomor pengaduan tidak ditemukan!',
                        confirmButtonColor: '#ea580c',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                document.getElementById('trackLoading').classList.add('hidden');
                document.getElementById('trackLoading').classList.remove('flex');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan. Silakan coba lagi.',
                    confirmButtonColor: '#ea580c',
                    confirmButtonText: 'OK'
                });
                console.error('Error:', error);
            });
        }

        function showTrackModal(data) {
            const statusColors = {
                yellow: 'bg-yellow-100 text-yellow-800 border-yellow-300',
                blue: 'bg-blue-100 text-blue-800 border-blue-300',
                green: 'bg-green-100 text-green-800 border-green-300',
                red: 'bg-red-100 text-red-800 border-red-300'
            };

            const statusIcons = {
                pending: 'fa-clock',
                proses: 'fa-spinner fa-spin',
                selesai: 'fa-check-circle',
                ditolak: 'fa-times-circle'
            };

            const html = `
                <!-- Pengaduan Info -->
                <div class="space-y-4">
                    <!-- Nomor & Status -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Nomor Pengaduan</p>
                            <p class="text-lg font-bold text-gray-800">${data.no_pengaduan}</p>
                        </div>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold border-2 ${statusColors[data.status_color]}">
                            <i class="fas ${statusIcons[data.status]}"></i>
                            ${data.status_label}
                        </span>
                    </div>

                    <!-- Pelapor Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-blue-50 rounded-xl">
                            <p class="text-xs text-blue-600 font-semibold mb-1"><i class="fas fa-user mr-1"></i> Nama Pelapor</p>
                            <p class="text-gray-800 font-medium">${data.nama}</p>
                        </div>
                        <div class="p-4 bg-green-50 rounded-xl">
                            <p class="text-xs text-green-600 font-semibold mb-1"><i class="fas fa-envelope mr-1"></i> Email</p>
                            <p class="text-gray-800 font-medium">${data.email}</p>
                        </div>
                    </div>

                    <!-- Kategori & Tanggal -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-purple-50 rounded-xl">
                            <p class="text-xs text-purple-600 font-semibold mb-1"><i class="fas fa-folder mr-1"></i> Kategori</p>
                            <p class="text-gray-800 font-medium">${data.kategori}</p>
                        </div>
                        <div class="p-4 bg-orange-50 rounded-xl">
                            <p class="text-xs text-orange-600 font-semibold mb-1"><i class="fas fa-calendar mr-1"></i> Tanggal Pengaduan</p>
                            <p class="text-gray-800 font-medium">${data.tanggal_pengaduan}</p>
                        </div>
                    </div>

                    <!-- Isi Pengaduan -->
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-500 font-semibold mb-2"><i class="fas fa-comment-alt mr-1"></i> Isi Pengaduan</p>
                        <p class="text-gray-700 leading-relaxed">${data.isi_pengaduan}</p>
                    </div>

                    <!-- Respon -->
                    ${data.respon ? `
                        <div class="p-4 bg-green-50 border-2 border-green-200 rounded-xl">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-reply text-green-600"></i>
                                <p class="text-sm text-green-600 font-semibold">Respon / Tindak Lanjut</p>
                            </div>
                            <p class="text-gray-700 leading-relaxed mb-2">${data.respon}</p>
                            ${data.tanggal_respon ? `
                                <p class="text-xs text-green-600 mt-2"><i class="fas fa-clock mr-1"></i> Ditanggapi pada: ${data.tanggal_respon}</p>
                            ` : ''}
                        </div>
                    ` : `
                        <div class="p-4 bg-yellow-50 border-2 border-yellow-200 rounded-xl">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-info-circle text-yellow-600"></i>
                                <p class="text-sm text-yellow-600 font-semibold">Pengaduan belum ditanggapi</p>
                            </div>
                            <p class="text-xs text-yellow-600 mt-2">Tim kami akan segera menindaklanjuti pengaduan Anda.</p>
                        </div>
                    `}
                </div>

                <!-- Modal Footer -->
                <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end gap-3">
                    <button onclick="closeTrackModal()" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition-all">
                        <i class="fas fa-times mr-2"></i>Tutup
                    </button>
                    <a href="{{ route('pengaduan.create') }}" class="px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-xl font-semibold transition-all shadow-lg">
                        <i class="fas fa-plus mr-2"></i>Buat Pengaduan Baru
                    </a>
                </div>
            `;

            document.getElementById('trackModalBody').innerHTML = html;
            document.getElementById('trackModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeTrackModal() {
            document.getElementById('trackModal').classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('trackPengaduanInput').value = '';
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTrackModal();
            }
        });
    </script>

    <style>
        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-modalSlideUp {
            animation: modalSlideUp 0.3s ease-out forwards;
        }
    </style>

    <!-- Services Grid -->
    <section id="layanan" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="text-blue-600 font-bold tracking-wider uppercase text-sm bg-blue-100 px-3 py-1 rounded-full">Layanan
                    Kami</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-4">Katalog Layanan Perizinan</h2>
                <p class="text-gray-500 mt-4 max-w-2xl mx-auto">Pilih dari berbagai jenis layanan perizinan dan
                    non-perizinan
                    yang tersedia secara online.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($layanan as $item)
                <a href="{{ route('layanan.show', $item->id) }}" class="group">
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 h-full group-hover:-translate-y-1">
                        <div class="p-6">
                            <!-- Icon -->
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fas fa-file-signature text-white text-2xl"></i>
                            </div>
                            
                            <!-- Title -->
                            <h3 class="text-lg font-bold text-gray-800 mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                {{ $item->nama_perijinan }}
                            </h3>
                            
                            <!-- Description -->
                            <p class="text-gray-500 text-sm mb-4 line-clamp-3">
                                {{ strip_tags($item->dasar_hukum) ?: 'Tidak ada deskripsi tersedia' }}
                            </p>
                            
                            <!-- Footer -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <span class="text-xs text-gray-400">
                                    <i class="fas fa-clock mr-1"></i> Online
                                </span>
                                <span class="inline-flex items-center gap-1 text-blue-600 font-semibold text-sm group-hover:gap-2 transition-all">
                                    Detail <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                <!-- Placeholder jika tidak ada data -->
                <div class="col-span-full text-center py-12">
                    <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-folder-open text-gray-400 text-3xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada layanan tersedia</p>
                </div>
                @endforelse
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('layanan') }}"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-4 rounded-xl transition-all shadow-lg hover:shadow-xl">
                    Lihat Semua Layanan <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="fitur" class="py-20 bg-white relative overflow-hidden">
        <div
            class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5">
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="w-full lg:w-1/2">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/online-registration-4489363-3723270.png"
                        alt="Features Illustration" class="w-full max-w-md mx-auto drop-shadow-2xl">
                </div>
                <div class="w-full lg:w-1/2 space-y-8">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Mengapa Menggunakan JITU?</h2>
                        <p class="text-gray-500 leading-relaxed">Aplikasi JITU (Sistem Informasi Perijinan Terpadu)
                            dirancang untuk mempermudah masyarakat dalam mengurus perizinan dengan transparan dan
                            akuntabel.</p>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 text-xl shrink-0">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Full Online System</h3>
                                <p class="text-gray-500 text-sm">Pengajuan hingga penerbitan izin dilakukan secara
                                    digital tanpa perlu datang ke kantor.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600 text-xl shrink-0">
                                <i class="fas fa-file-signature"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Tanda Tangan Elektronik (TTE)</h3>
                                <p class="text-gray-500 text-sm">Dokumen sah dengan QR Code dan Tanda Tangan
                                    Elektronik
                                    yang tervalidasi BSrE.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 text-xl shrink-0">
                                <i class="fas fa-stopwatch"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Monitoring Real-time</h3>
                                <p class="text-gray-500 text-sm">Pantau posisi berkas permohonan Anda secara
                                    real-time
                                    melalui fitur tracking.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SKM Section -->
    <section id="skm" class="py-20 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span
                    class="text-blue-200 font-bold tracking-wider uppercase text-sm bg-blue-700/50 px-3 py-1 rounded-full">Survei
                    Kepuasan Masyarakat</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-4">Bantu Kami Meningkatkan Pelayanan</h2>
                <p class="text-blue-100 mt-4 max-w-2xl mx-auto">Berikan penilaian Anda terhadap layanan kami.
                    Masukan
                    Anda sangat berharga untuk peningkatan kualitas pelayanan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <div class="bg-white/10 backdrop-blur-md border border-white/20 p-8 rounded-2xl text-center">
                    <div
                        class="w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center text-white text-3xl mx-auto mb-4">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Beri Penilaian</h3>
                    <p class="text-blue-100 text-sm">Nilai kualitas pelayanan kami dengan jujur dan objektif</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/20 p-8 rounded-2xl text-center">
                    <div
                        class="w-16 h-16 bg-green-400 rounded-full flex items-center justify-center text-white text-3xl mx-auto mb-4">
                        <i class="fas fa-comment-alt"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Berikan Masukan</h3>
                    <p class="text-blue-100 text-sm">Saran dan kritik membangun untuk perbaikan layanan</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/20 p-8 rounded-2xl text-center">
                    <div
                        class="w-16 h-16 bg-purple-400 rounded-full flex items-center justify-center text-white text-3xl mx-auto mb-4">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Peningkatan Layanan</h3>
                    <p class="text-blue-100 text-sm">Hasil survei digunakan untuk meningkatkan kualitas pelayanan
                    </p>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="/skm"
                    class="inline-flex items-center gap-3 bg-white text-blue-700 hover:bg-blue-50 px-8 py-4 rounded-xl font-bold shadow-xl transition-all">
                    <i class="fas fa-poll"></i> Isi Survei SKM
                </a>
            </div>
        </div>
    </section>

    <!-- Helpdesk / CTA -->
    <section class="py-16 bg-gradient-to-br from-secondary to-gray-900 text-white">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-6">Butuh Bantuan?</h2>
            <p class="text-gray-300 mb-8 max-w-2xl mx-auto">Tim Helpdesk kami siap membantu Anda pada jam kerja
                (Senin
                - Jumat, 08:00 - 16:00 WIB).</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="https://wa.me/{{ formatWhatsAppNumber(setting('whatsapp', '081234567890')) }}"
                    target="_blank"
                    class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg transition-all flex items-center justify-center gap-2">
                    <i class="fab fa-whatsapp text-xl"></i> Chat WhatsApp
                </a>
                <a href="mailto:{{ setting('email', 'info@example.com') }}"
                    class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-8 py-3 rounded-xl font-bold backdrop-blur-md transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-envelope"></i> Kirim Email
                </a>
            </div>
        </div>
    </section>
</x-front-layout>
