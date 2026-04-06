<x-layout>
    <x-slot:title>Detail Pengguna Pemerintah</x-slot:title>

    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Detail Pengguna Pemerintah</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Informasi lengkap pengguna</p>
            </div>
            <a href="{{ route('pemerintah.index') }}"
                class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <i class="mdi mdi-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-8">
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="mdi mdi-shield-account text-4xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                    <p class="text-purple-100">{{ '@' }}{{ $user->username }}</p>
                </div>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Email -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-2">
                    <i class="mdi mdi-email text-purple-600 dark:text-purple-400"></i>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</h3>
                </div>
                <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $user->email }}</p>
            </div>

            <!-- NIP -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-2">
                    <i class="mdi mdi-card-account-details text-purple-600 dark:text-purple-400"></i>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">NIP</h3>
                </div>
                <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $user->nip ?? '-' }}</p>
            </div>

            <!-- No. HP -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-2">
                    <i class="mdi mdi-phone text-purple-600 dark:text-purple-400"></i>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">No. HP</h3>
                </div>
                <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $user->no_hp ?? '-' }}</p>
            </div>

            <!-- Role -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-2">
                    <i class="mdi mdi-shield-account text-purple-600 dark:text-purple-400"></i>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</h3>
                </div>
                <p class="text-gray-900 dark:text-gray-100 font-medium">Pemerintah</p>
            </div>

            <!-- OPD -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-2">
                    <i class="mdi mdi-domain text-purple-600 dark:text-purple-400"></i>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">OPD</h3>
                </div>
                <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $user->opd->nama_opd ?? '-' }}</p>
            </div>

            <!-- Status -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-2">
                    <i class="mdi mdi-{{ $user->status === 'aktif' ? 'check-circle' : 'close-circle' }} text-{{ $user->status === 'aktif' ? 'green' : 'red' }}-600 dark:text-{{ $user->status === 'aktif' ? 'green' : 'red' }}-400"></i>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold
                    {{ $user->status === 'aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                    {{ $user->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>

            <!-- Terdaftar -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-2">
                    <i class="mdi mdi-calendar-plus text-purple-600 dark:text-purple-400"></i>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Terdaftar</h3>
                </div>
                <p class="text-gray-900 dark:text-gray-100 font-medium">
                    {{ $user->created_at->format('d M Y, H:i') }}
                </p>
            </div>

            <!-- Terakhir Update -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-2">
                    <i class="mdi mdi-calendar-edit text-purple-600 dark:text-purple-400"></i>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Terakhir Update</h3>
                </div>
                <p class="text-gray-900 dark:text-gray-100 font-medium">
                    {{ $user->updated_at->format('d M Y, H:i') }}
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <a href="{{ route('pemerintah.edit', $user->id) }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors flex items-center gap-2">
                <i class="mdi mdi-pencil"></i>
                <span>Edit</span>
            </a>
        </div>
    </div>
</x-layout>
