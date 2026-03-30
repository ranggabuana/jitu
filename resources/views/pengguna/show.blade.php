<x-layout>
    <x-slot:title>Detail Pengguna</x-slot:title>
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Detail Pengguna</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Informasi lengkap pengguna</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('pengguna.data.index') }}"
                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                    <i class="mdi mdi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 max-w-4xl">
        <!-- User Profile Header -->
        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
            <div
                class="w-20 h-20 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $user->name }}</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-500">Username: {{ $user->username }}</p>
            </div>
        </div>

        <!-- User Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">NIP</p>
                <p class="text-gray-800 dark:text-white">{{ $user->nip ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">No. HP</p>
                <p class="text-gray-800 dark:text-white">{{ $user->no_hp ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Role</p>
                @php
                    $roleColors = [
                        'admin' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                        'fo' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        'bo' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                        'operator_opd' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                        'kepala_opd' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                        'verifikator' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                        'kadin' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
                    ];
                @endphp
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $user->role_label }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Status</p>
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->status === 'aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                    {{ $user->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>
        </div>

        <!-- OPD Info -->
        @if ($user->opd)
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Informasi OPD</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nama OPD</p>
                        <p class="text-gray-800 dark:text-white">{{ $user->opd->nama_opd }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Kode OPD</p>
                        <p class="text-gray-800 dark:text-white">{{ $user->opd->kode_opd }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Account Information -->
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Informasi Akun</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Terdaftar Sejak</p>
                    <p class="text-gray-800 dark:text-white">{{ $user->created_at->format('d/m/Y H:i') }} WIB</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Terakhir Update</p>
                    <p class="text-gray-800 dark:text-white">{{ $user->updated_at->format('d/m/Y H:i') }} WIB</p>
                </div>
                @if ($user->email_verified_at)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Email Verified</p>
                        <p class="text-gray-800 dark:text-white">{{ $user->email_verified_at->format('d/m/Y H:i') }}
                            WIB</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
