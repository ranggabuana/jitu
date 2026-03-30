<div>
    <!-- Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/images/logo-banjarnegara.png') }}" alt="Logo Banjarnegara"
                        class="w-10 h-10 object-contain drop-shadow-md">
                    <div>
                        <span class="font-bold text-2xl tracking-tight text-blue-900">JITU</span>
                        <p class="text-[10px] text-gray-500 leading-tight">Sistem Informasi Perijinan Terpadu</p>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-semibold">Banjarnegara</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/"
                        class="{{ request()->routeIs('landing') ? 'text-blue-600 font-medium border-b-2 border-blue-600 pb-1' : 'text-gray-500 hover:text-blue-600 font-medium transition-colors' }}">Beranda</a>
                    <a href="{{ route('layanan') }}"
                        class="{{ request()->routeIs('layanan*') ? 'text-blue-600 font-medium border-b-2 border-blue-600 pb-1' : 'text-gray-500 hover:text-blue-600 font-medium transition-colors' }}">Layanan</a>
                    <a href="{{ route('informasi') }}"
                        class="{{ request()->routeIs('informasi*') ? 'text-blue-600 font-medium border-b-2 border-blue-600 pb-1' : 'text-gray-500 hover:text-blue-600 font-medium transition-colors' }}">Informasi</a>
                    <a href="{{ route('pengaduan.create') }}"
                        class="{{ request()->routeIs('pengaduan.create*') ? 'text-blue-600 font-medium border-b-2 border-blue-600 pb-1' : 'text-gray-500 hover:text-blue-600 font-medium transition-colors' }}">Pengaduan</a>
                    <a href="{{ route('regulasi.public') }}"
                        class="{{ request()->routeIs('regulasi.public*') ? 'text-blue-600 font-medium border-b-2 border-blue-600 pb-1' : 'text-gray-500 hover:text-blue-600 font-medium transition-colors' }}">Regulasi</a>
                    <a href="{{ route('skm') }}"
                        class="{{ request()->routeIs('skm*') ? 'text-blue-600 font-medium border-b-2 border-blue-600 pb-1' : 'text-gray-500 hover:text-blue-600 font-medium transition-colors' }}">SKM</a>
                </div>
                <div class="flex items-center gap-3">
                    @auth
                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center gap-2 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 px-4 py-2 rounded-full font-semibold transition-all">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <span class="hidden md:inline">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                    <span
                                        class="inline-block mt-2 text-xs px-2 py-1 bg-{{ auth()->user()->status === 'aktif' ? 'green' : 'yellow' }}-100 text-{{ auth()->user()->status === 'aktif' ? 'green' : 'yellow' }}-600 rounded-full">
                                        Status: {{ auth()->user()->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                                <a href="{{ route('landing') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-home mr-2"></i> Beranda
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a target="_blank" href="{{ route('login') }}"
                            class="hidden md:inline-flex items-center gap-2 border border-blue-200 text-blue-700 hover:bg-blue-50 px-5 py-2.5 rounded-full font-bold transition-all">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="{{ route('front.register') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full font-bold shadow-md transition-all flex items-center gap-2">
                            <i class="fas fa-user-plus"></i> <span class="hidden md:inline">Daftar</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</div>
