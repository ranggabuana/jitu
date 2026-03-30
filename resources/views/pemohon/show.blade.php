<x-layout>
    <x-slot:title>Detail Pemohon</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('pemohon.index') }}"
            class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 mb-4">
            <i class="mdi mdi-arrow-left"></i> Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Detail Pemohon</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Informasi lengkap pemohon</p>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Informasi Pribadi</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">Nama Lengkap</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $pemohon->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">Username</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $pemohon->username }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">Status Pemohon</label>
                            <p class="text-gray-900 dark:text-white font-medium">
                                @if ($pemohon->status_pemohon === 'badan_usaha')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 border border-purple-200 dark:border-purple-700">
                                        <i class="fas fa-building"></i>
                                        Badan Usaha
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 border border-blue-200 dark:border-blue-700">
                                        <i class="fas fa-user"></i>
                                        Perorangan
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">NIK / NIP</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $pemohon->nip ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">Email</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $pemohon->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">Nomor Telepon</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $pemohon->no_hp ?? '-' }}</p>
                        </div>
                        @if ($pemohon->status_pemohon === 'badan_usaha')
                            <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-4 mt-2">
                                <h4
                                    class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                    <i class="fas fa-building text-purple-600"></i>
                                    Informasi Badan Usaha
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs text-gray-500 dark:text-gray-400">Nama Perusahaan</label>
                                        <p class="text-gray-900 dark:text-white font-medium">
                                            {{ $pemohon->nama_perusahaan ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 dark:text-gray-400">NPWP</label>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $pemohon->npwp ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-4 mt-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500 dark:text-gray-400">Role</label>
                                    <p class="text-gray-900 dark:text-white font-medium">
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            <i class="mdi mdi-account-circle"></i> Pemohon
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500 dark:text-gray-400">Terdaftar Pada</label>
                                    <p class="text-gray-900 dark:text-white font-medium">
                                        {{ $pemohon->created_at->format('d M Y, H:i') }} WIB
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Status Akun</h2>
                </div>
                <div class="p-6">
                    <div class="text-center mb-4">
                        <div
                            class="w-20 h-20 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="mdi mdi-account text-blue-600 dark:text-blue-300 text-4xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 dark:text-white">{{ $pemohon->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $pemohon->email }}</p>
                    </div>

                    <div class="mb-4">
                        @if ($pemohon->status === 'aktif')
                            <span
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 w-full justify-center">
                                <i class="mdi mdi-check-circle"></i> Aktif
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 w-full justify-center">
                                <i class="mdi mdi-clock-outline"></i> Tidak Aktif
                            </span>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="space-y-2">
                        <form method="POST" action="{{ route('pemohon.update-status', $pemohon->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" name="status"
                                value="{{ $pemohon->status === 'aktif' ? 'tidak_aktif' : 'aktif' }}"
                                class="w-full {{ $pemohon->status === 'aktif' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                <i
                                    class="mdi mdi-{{ $pemohon->status === 'aktif' ? 'clock-outline' : 'check-circle' }}"></i>
                                {{ $pemohon->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>

                        <form action="{{ route('pemohon.destroy', $pemohon->id) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center gap-2 btn-delete"
                                data-action="{{ route('pemohon.destroy', $pemohon->id) }}">
                                <i class="mdi mdi-delete"></i> Hapus Pemohon
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg shadow overflow-hidden p-6">
                <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <i class="mdi mdi-information text-blue-600 dark:text-blue-400"></i>
                    Informasi
                </h3>
                <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-start gap-2">
                        <i class="mdi mdi-check text-green-500 mt-0.5"></i>
                        <span>Pemohon dapat login setelah diaktifkan oleh admin</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="mdi mdi-check text-green-500 mt-0.5"></i>
                        <span>Pemohon dapat mengajukan perizinan online</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="mdi mdi-check text-green-500 mt-0.5"></i>
                        <span>Pemohon dapat memantau status pengajuan</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-layout>
