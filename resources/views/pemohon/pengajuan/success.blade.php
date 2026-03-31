<x-pemohon.layout>
    <x-slot:title>Pengajuan Berhasil - JITU Banjarnegara</x-slot:title>

    <!-- Navbar -->
    <x-pemohon.navbar></x-pemohon.navbar>

    <!-- Main Content -->
    <main class="flex-1 max-w-3xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-12">
        <!-- Success Card -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-amber-200">
            <!-- Success Header -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 p-8 text-center text-white">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-4xl"></i>
                </div>
                <h1 class="text-2xl font-bold mb-2">Pengajuan Berhasil!</h1>
                <p class="text-green-100">Permintaan perizinan Anda telah dikirim</p>
            </div>

            <!-- Content -->
            <div class="p-8 space-y-6">
                <!-- Registration Number -->
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                    <p class="text-sm text-gray-600 mb-2">Nomor Registrasi</p>
                    <p class="text-3xl font-bold text-amber-700 font-mono">{{ $data->no_registrasi }}</p>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Simpan nomor ini untuk tracking pengajuan Anda
                    </p>
                </div>

                <!-- Application Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Jenis Perizinan</p>
                        <p class="font-semibold text-gray-800">{{ $data->perijinan->nama_perijinan }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tanggal Pengajuan</p>
                        <p class="font-semibold text-gray-800">{{ $data->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Status</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $data->status_color }}">
                            {{ $data->status_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tahap Validasi</p>
                        <p class="font-semibold text-gray-800">{{ $data->current_step }} dari {{ $data->perijinan->activeValidationFlows->count() }} tahap</p>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
                    <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-list-check text-blue-600"></i>
                        Langkah Selanjutnya
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Pengajuan Anda akan segera diverifikasi oleh petugas</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-clock text-blue-500 mt-0.5"></i>
                            <span>Proses validasi akan dilakukan secara berurutan sesuai alur</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-bell text-amber-500 mt-0.5"></i>
                            <span>Anda akan mendapat notifikasi jika ada update status</span>
                        </li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <a href="{{ route('pemohon.tracking', $data->id) }}"
                        class="flex-1 text-center px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-semibold transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-search"></i>
                        <span>Track Pengajuan</span>
                    </a>
                    <a href="{{ route('pemohon.dashboard') }}"
                        class="flex-1 text-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition-colors">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <x-pemohon.footer></x-pemohon.footer>
</x-pemohon.layout>
