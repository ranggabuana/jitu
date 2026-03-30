<x-layout>
    <x-slot:title>Profil Saya</x-slot:title>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if(auth()->user()->role === 'pemohon')
                    <a href="{{ route('pemohon.dashboard') }}"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                        <i class="mdi mdi-arrow-left text-xl"></i>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                        <i class="mdi mdi-arrow-left text-xl"></i>
                    </a>
                @endif
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Profil Saya</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kelola informasi akun Anda</p>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center gap-2 text-green-700 dark:text-green-400">
                <i class="mdi mdi-check-circle"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="mb-6 flex gap-3">
        <a href="{{ route('profile.edit') }}"
            class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/30 transition-colors">
                    <i class="mdi mdi-pencil text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-200">Edit Profil</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Ubah data pribadi</p>
                </div>
            </div>
        </a>
        @if(auth()->user()->role !== 'pemohon')
            <a href="{{ route('profile.change-password') }}"
                class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-orange-300 dark:hover:border-orange-600 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center group-hover:bg-orange-100 dark:group-hover:bg-orange-900/30 transition-colors">
                        <i class="mdi mdi-key text-orange-600 dark:text-orange-400"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-200">Ubah Password</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Ganti kata sandi</p>
                    </div>
                </div>
            </a>
        @endif
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="text-center">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-3xl font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </span>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $user->email }}</p>
                    <span class="inline-flex items-center gap-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 text-xs px-2 py-1 rounded mt-2">
                        <i class="mdi mdi-shield-account"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Username</span>
                            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $user->username }}</span>
                        </div>
                        @if($user->nip)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">NIP</span>
                                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $user->nip }}</span>
                            </div>
                        @endif
                        @if($user->no_hp)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">No. HP</span>
                                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $user->no_hp }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Terdaftar</span>
                            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Profile Details -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-800 dark:text-white">Detail Profil</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Nama Lengkap</label>
                            <p class="text-gray-800 dark:text-gray-200">{{ $user->name }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Email</label>
                            <p class="text-gray-800 dark:text-gray-200">{{ $user->email }}</p>
                        </div>

                        <!-- Username -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Username</label>
                            <p class="text-gray-800 dark:text-gray-200">{{ $user->username }}</p>
                        </div>

                        <!-- NIP -->
                        @if($user->nip)
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">NIP</label>
                                <p class="text-gray-800 dark:text-gray-200">{{ $user->nip }}</p>
                            </div>
                        @endif

                        <!-- No HP -->
                        @if($user->no_hp)
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Nomor HP</label>
                                <p class="text-gray-800 dark:text-gray-200">{{ $user->no_hp }}</p>
                            </div>
                        @endif

                        <!-- Role -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Role</label>
                            <p class="text-gray-800 dark:text-gray-200 capitalize">{{ $user->role }}</p>
                        </div>

                        @if($user->opd)
                            <!-- OPD -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">OPD</label>
                                <p class="text-gray-800 dark:text-gray-200">{{ $user->opd->nama_opd }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
