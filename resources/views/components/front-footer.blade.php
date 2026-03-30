<div>
    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('assets/images/logo-banjarnegara.png') }}" alt="Logo Banjarnegara"
                            class="w-8 h-8 object-contain">
                        <div>
                            <span class="font-bold text-xl text-blue-900 leading-tight">JITU</span>
                            <p class="text-[10px] text-gray-500 leading-tight">Sistem Informasi Perijinan Terpadu
                            </p>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                        {{ setting('app_description', 'Aplikasi JITU (Sistem Informasi Perijinan Terpadu) adalah inovasi layanan perizinan terpadu Kabupaten Banjarnegara.') }}
                    </p>
                    <div class="flex gap-4">
                        @if(setting('facebook'))
                            <a href="{{ setting('facebook') }}" target="_blank"
                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        @endif
                        @if(setting('instagram'))
                            <a href="{{ setting('instagram') }}" target="_blank"
                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        @if(setting('youtube'))
                            <a href="{{ setting('youtube') }}" target="_blank"
                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                <i class="fab fa-youtube"></i>
                            </a>
                        @endif
                        @if(setting('tiktok'))
                            <a href="{{ setting('tiktok') }}" target="_blank"
                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        @endif
                        @if(setting('twitter'))
                            <a href="{{ setting('twitter') }}" target="_blank"
                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                <i class="fab fa-twitter"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-gray-800 mb-6">Tautan Cepat</h4>
                    <ul class="space-y-3 text-sm text-gray-500">
                        <li><a href="{{ route('landing') }}" class="hover:text-blue-600 transition-colors">Beranda</a></li>
                        <li><a href="{{ route('layanan') }}" class="hover:text-blue-600 transition-colors">Layanan</a></li>
                        <li><a href="{{ route('informasi') }}" class="hover:text-blue-600 transition-colors">Informasi</a></li>
                        <li><a href="{{ route('pengaduan.create') }}" class="hover:text-blue-600 transition-colors">Pengaduan</a></li>
                        <li><a href="{{ route('regulasi.public') }}" class="hover:text-blue-600 transition-colors">Regulasi</a></li>
                        <li><a href="{{ route('skm') }}" class="hover:text-blue-600 transition-colors">SKM</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-gray-800 mb-6">Layanan Populer</h4>
                    <ul class="space-y-3 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Izin Usaha (NIB)</a>
                        </li>
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Persetujuan Bangunan
                                (PBG)</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Izin Praktik
                                Kesehatan</a>
                        </li>
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Izin Reklame</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-gray-800 mb-6">Kontak Kami</h4>
                    <ul class="space-y-4 text-sm text-gray-500">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-blue-600 mt-1"></i>
                            <span>{{ setting('address', 'Jl. Jendral Sudirman No. 45, Banjarnegara, Jawa Tengah') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-blue-600"></i>
                            <span>{{ setting('phone', '(0286) 123456') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-blue-600"></i>
                            <span>{{ setting('email', 'dptmsp@banjarnegarakab.go.id') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-400">
                <p>&copy; 2023 DPMPTSP Kabupaten Banjarnegara. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-gray-600">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-gray-600">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Data Layanan Perizinan (Same as in form-permohonan.html)
        const services = [{
                id: 1,
                name: "Surat Keterangan Penelitian Lingkup Daerah",
                icon: "fa-microscope",
                color: "text-blue-600",
                bg: "bg-blue-100"
            },
            {
                id: 2,
                name: "Surat Tanda Penyehat Tradisional",
                icon: "fa-spa",
                color: "text-green-600",
                bg: "bg-green-100"
            },
            {
                id: 3,
                name: "Pencabutan Izin Apotek",
                icon: "fa-prescription-bottle-alt",
                color: "text-red-600",
                bg: "bg-red-100"
            },
            {
                id: 4,
                name: "Izin Tenaga Pelaksana Perkawinan Ternak",
                icon: "fa-paw",
                color: "text-yellow-600",
                bg: "bg-yellow-100"
            },
            {
                id: 5,
                name: "Surat Izin Usaha Veteriner (SIVET)",
                icon: "fa-clinic-medical",
                color: "text-pink-600",
                bg: "bg-pink-100"
            },
            {
                id: 6,
                name: "Izin Praktek Dokter Hewan Umum/Spesialis",
                icon: "fa-user-md",
                color: "text-amber-600",
                bg: "bg-amber-100"
            },
            {
                id: 7,
                name: "Izin Praktek Paramedik & Sarjana Kedokteran Hewan",
                icon: "fa-user-nurse",
                color: "text-emerald-600",
                bg: "bg-emerald-100"
            },
            {
                id: 8,
                name: "Izin Trayek atau Operasi Angkutan Umum",
                icon: "fa-bus",
                color: "text-teal-600",
                bg: "bg-teal-100"
            },
            {
                id: 9,
                name: "Kartu Pengawasan Angkutan Umum",
                icon: "fa-id-card-alt",
                color: "text-indigo-600",
                bg: "bg-indigo-100"
            },
            {
                id: 10,
                name: "Izin Penggunaan Alun-Alun & Ring Road",
                icon: "fa-tree",
                color: "text-green-500",
                bg: "bg-green-50"
            },
            {
                id: 11,
                name: "Izin Penggunaan Stadion Soemitro Kolopaking",
                icon: "fa-futbol",
                color: "text-cyan-600",
                bg: "bg-cyan-100"
            },
            {
                id: 12,
                name: "Izin Penggunaan Gedung Olahraga (GOR)",
                icon: "fa-running",
                color: "text-orange-600",
                bg: "bg-orange-100"
            },
            {
                id: 13,
                name: "Izin Penyelenggaraan Perparkiran",
                icon: "fa-parking",
                color: "text-gray-600",
                bg: "bg-gray-200"
            },
            {
                id: 14,
                name: "Izin Pendirian Program/Satuan Pendidikan",
                icon: "fa-school",
                color: "text-sky-600",
                bg: "bg-sky-100"
            },
            {
                id: 15,
                name: "Izin Operasional Program/Satuan Pendidikan",
                icon: "fa-chalkboard-teacher",
                color: "text-violet-600",
                bg: "bg-violet-100"
            },
            {
                id: 16,
                name: "PKKPR Non Berusaha",
                icon: "fa-map-marked-alt",
                color: "text-blue-500",
                bg: "bg-blue-50"
            },
            {
                id: 17,
                name: "Pencabutan Izin Praktik Tenaga Medis",
                icon: "fa-user-slash",
                color: "text-rose-600",
                bg: "bg-rose-100"
            },
            {
                id: 18,
                name: "Izin Tayang/Pemasangan Reklame",
                icon: "fa-ad",
                color: "text-purple-600",
                bg: "bg-purple-100"
            }
        ];

        // Render Service Grid (Show first 8 items)
        const container = document.getElementById('service-container');

        services.slice(0, 8).forEach(service => {
            const card = document.createElement('a');
            card.href = 'form-permohonan.html';
            card.className =
                'service-card bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition-all group flex flex-col items-center text-center h-full';
            card.innerHTML = `
                <div class="w-16 h-16 rounded-2xl ${service.bg} ${service.color} flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas ${service.icon}"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">${service.name}</h3>
                <p class="text-xs text-gray-400 mt-auto">Klik untuk mengajukan</p>
            `;
            container.appendChild(card);
        });
    </script>
</div>
