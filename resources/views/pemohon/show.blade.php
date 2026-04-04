<x-layout>
    <x-slot:title>Detail Pemohon</x-slot:title>

    <div class="mb-8">
        <a href="{{ route('pemohon.index') }}"
            class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white mb-4 transition-colors">
            <i class="mdi mdi-arrow-left text-lg"></i> <span class="font-medium">Kembali</span>
        </a>
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                <i class="mdi mdi-account text-gray-600 dark:text-gray-300 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pemohon->name }}</h1>
                <p class="text-gray-500 dark:text-gray-400">{{ $pemohon->email }}</p>
            </div>
        </div>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Section 1: Informasi Pribadi -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <i class="mdi mdi-account-outline text-gray-600 dark:text-gray-300"></i>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Informasi Pribadi</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Nama Lengkap</dt>
                            <dd class="text-base text-gray-900 dark:text-white font-medium">{{ $pemohon->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Username</dt>
                            <dd class="text-base text-gray-900 dark:text-white font-medium">{{ $pemohon->username }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Email</dt>
                            <dd class="text-base text-gray-900 dark:text-white font-medium">{{ $pemohon->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">NIK / NIP</dt>
                            <dd class="text-base text-gray-900 dark:text-white font-mono">{{ $pemohon->nip ?? '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Nomor Telepon</dt>
                            <dd class="text-base text-gray-900 dark:text-white">{{ $pemohon->no_hp ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status Pemohon</dt>
                            <dd class="mt-1">
                                @if ($pemohon->status_pemohon === 'badan_usaha')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                        <i class="mdi mdi-domain text-[10px]"></i> Badan Usaha
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                        <i class="mdi mdi-account text-[10px]"></i> Perorangan
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Alamat & Wilayah -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <i class="mdi mdi-map-marker text-gray-600 dark:text-gray-300"></i>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Alamat & Wilayah</h2>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Alamat Lengkap</dt>
                        <dd
                            class="text-base text-gray-900 dark:text-white leading-relaxed bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            {{ $pemohon->alamat_lengkap ?? '-' }}
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Provinsi</dt>
                            <dd class="text-base text-gray-900 dark:text-white font-medium">
                                {{ $pemohon->provinsi->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Kabupaten/Kota</dt>
                            <dd class="text-base text-gray-900 dark:text-white font-medium">
                                {{ $pemohon->kabupaten->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Kecamatan</dt>
                            <dd class="text-base text-gray-900 dark:text-white font-medium">
                                {{ $pemohon->kecamatan->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Kelurahan/Desa</dt>
                            <dd class="text-base text-gray-900 dark:text-white font-medium">
                                {{ $pemohon->kelurahan->name ?? '-' }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Badan Usaha (Conditional) -->
            @if ($pemohon->status_pemohon === 'badan_usaha')
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <i class="mdi mdi-domain text-gray-600 dark:text-gray-300"></i>
                        </div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Informasi Badan Usaha</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Nama Perusahaan
                                </dt>
                                <dd class="text-base text-gray-900 dark:text-white font-medium">
                                    {{ $pemohon->nama_perusahaan ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">NPWP</dt>
                                <dd class="text-base text-gray-900 dark:text-white font-mono">
                                    {{ $pemohon->npwp ?? '-' }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Section 4: Foto KTP -->
            @if ($pemohon->foto_ktp)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <i class="mdi mdi-badge-account text-gray-600 dark:text-gray-300"></i>
                        </div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Foto KTP</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-center bg-gray-50 dark:bg-gray-700/30 rounded-lg p-6">
                            @php
                                $extension = pathinfo($pemohon->foto_ktp, PATHINFO_EXTENSION);
                            @endphp
                            @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ asset($pemohon->foto_ktp) }}" alt="Foto KTP {{ $pemohon->name }}"
                                    class="max-w-sm w-full h-auto rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm cursor-zoom-in hover:shadow-md transition-shadow"
                                    onclick="window.open('{{ asset($pemohon->foto_ktp) }}', '_blank')">
                            @elseif (strtolower($extension) === 'pdf')
                                <a href="{{ asset($pemohon->foto_ktp) }}" target="_blank"
                                    class="inline-flex items-center gap-3 px-5 py-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <i class="mdi mdi-file-pdf-box text-red-500 text-2xl"></i>
                                    <div class="text-left">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">File KTP (PDF)
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Klik untuk membuka</div>
                                    </div>
                                </a>
                            @else
                                <a href="{{ asset($pemohon->foto_ktp) }}" target="_blank"
                                    class="inline-flex items-center gap-2 px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <i class="mdi mdi-file-outline text-gray-500 dark:text-gray-400"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Lihat File KTP</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Section 5: Informasi Sistem -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <i class="mdi mdi-information-outline text-gray-600 dark:text-gray-300"></i>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Informasi Sistem</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Role</dt>
                            <dd class="mt-1">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                    Pemohon
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Terdaftar Pada</dt>
                            <dd class="text-base text-gray-900 dark:text-white font-medium">
                                {{ $pemohon->created_at->format('d M Y, H:i') }} WIB
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Status Akun</h2>
                </div>
                <div class="p-6">
                    <div class="text-center mb-5">
                        <div
                            class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="mdi mdi-account text-gray-500 dark:text-gray-400 text-4xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $pemohon->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $pemohon->email }}</p>
                    </div>

                    <div class="mb-5">
                        @if ($pemohon->status === 'aktif')
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 w-full justify-center">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span> Aktif
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 border border-amber-200 dark:border-amber-800 w-full justify-center">
                                <span class="w-2 h-2 bg-amber-500 rounded-full"></span> Tidak Aktif
                            </span>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('pemohon.update-status', $pemohon->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" name="status"
                                value="{{ $pemohon->status === 'aktif' ? 'tidak_aktif' : 'aktif' }}"
                                class="w-full {{ $pemohon->status === 'aktif' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600' }} text-white px-4 py-2.5 rounded-lg font-medium transition-colors flex items-center justify-center gap-2 text-sm">
                                <i
                                    class="mdi mdi-{{ $pemohon->status === 'aktif' ? 'clock-outline' : 'check-circle' }} text-base"></i>
                                {{ $pemohon->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>

                        <form action="{{ route('pemohon.destroy', $pemohon->id) }}" method="POST"
                            class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 rounded-lg font-medium transition-colors flex items-center justify-center gap-2 text-sm btn-delete"
                                data-action="{{ route('pemohon.destroy', $pemohon->id) }}">
                                <i class="mdi mdi-delete text-base"></i> Hapus Pemohon
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2 text-sm">
                    <i class="mdi mdi-lightbulb-on text-gray-500 dark:text-gray-400"></i>
                    Informasi
                </h3>
                <ul class="space-y-2.5 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-start gap-2.5">
                        <i class="mdi mdi-check text-green-500 mt-0.5"></i>
                        <span>Pemohon dapat login setelah diaktifkan</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i class="mdi mdi-check text-green-500 mt-0.5"></i>
                        <span>Dapat mengajukan perizinan online</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i class="mdi mdi-check text-green-500 mt-0.5"></i>
                        <span>Dapat memantau status pengajuan</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-layout>
