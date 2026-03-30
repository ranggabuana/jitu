<x-pemohon.layout>
    <x-slot:title>Profil Saya - Dashboard Pemohon</x-slot:title>

    <x-pemohon.navbar></x-pemohon.navbar>

    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('pemohon.dashboard') }}"
                    class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Profil Saya</h1>
                    <p class="text-sm text-gray-500">Kelola informasi akun Anda</p>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center gap-2 text-green-700">
                    <i class="fas fa-check-circle"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="mb-6 flex gap-3">
            <a href="{{ route('pemohon.profile.edit') }}"
                class="flex-1 bg-white border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                        <i class="fas fa-pencil text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Edit Profil</h3>
                        <p class="text-xs text-gray-500">Ubah data pribadi</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('pemohon.profile.change-password') }}"
                class="flex-1 bg-white border border-gray-200 rounded-lg p-4 hover:border-orange-300 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center group-hover:bg-orange-100 transition-colors">
                        <i class="fas fa-key text-orange-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Ganti Password</h3>
                        <p class="text-xs text-gray-500">Ubah kata sandi</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Profile Information -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-800">Informasi Pribadi</h2>
                <p class="text-sm text-gray-500">Data akun Anda</p>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-4 mb-6">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=1e40af&color=fff"
                        class="w-20 h-20 rounded-full border border-gray-200" alt="{{ $user->name }}">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="fas fa-user-tag mr-1"></i> {{ ucfirst($user->role) }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Username</p>
                        <p class="font-medium text-gray-800">{{ $user->username }}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold mb-1">NIK / NIP</p>
                        <p class="font-medium text-gray-800">{{ $user->nip ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Email</p>
                        <p class="font-medium text-gray-800">{{ $user->email }}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Nomor Telepon</p>
                        <p class="font-medium text-gray-800">{{ $user->no_hp ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Status Pemohon</p>
                        <p class="font-medium text-gray-800">
                            @if($user->status_pemohon === 'badan_usaha')
                                <i class="fas fa-building text-purple-600 mr-1"></i> Badan Usaha
                            @else
                                <i class="fas fa-user text-blue-600 mr-1"></i> Perorangan
                            @endif
                        </p>
                    </div>
                    @if($user->status_pemohon === 'badan_usaha')
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                            <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Nama Perusahaan</p>
                            <p class="font-medium text-gray-800">{{ $user->nama_perusahaan ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                            <p class="text-xs text-gray-400 uppercase font-semibold mb-1">NPWP</p>
                            <p class="font-medium text-gray-800">{{ $user->npwp ?? '-' }}</p>
                        </div>
                    @endif
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Status Akun</p>
                        <p class="font-medium text-gray-800">
                            @if($user->status === 'aktif')
                                <span class="inline-flex items-center gap-1 text-green-600">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-yellow-600">
                                    <i class="fas fa-clock"></i> Tidak Aktif
                                </span>
                            @endif
                        </p>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Terdaftar Sejak</p>
                        <p class="font-medium text-gray-800">{{ $user->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-pemohon.footer></x-pemohon.footer>
</x-pemohon.layout>
