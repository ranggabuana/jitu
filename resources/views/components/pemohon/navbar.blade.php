<nav class="bg-white shadow-sm border-b border-amber-100 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/images/logo-banjarnegara.png') }}" alt="Logo Banjarnegara"
                    class="w-8 h-8 object-contain">
                <div class="leading-tight">
                    <span class="font-bold text-xl text-amber-900">JITU</span>
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest font-semibold">Pemohon</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <!-- Top Menu -->
                <div class="hidden md:flex items-center gap-6 mr-4">
                    <a href="{{ route('pemohon.dashboard') }}" class="text-sm font-semibold {{ request()->routeIs('pemohon.dashboard') ? 'text-amber-700' : 'text-gray-600 hover:text-amber-700' }} transition-colors">
                        Dashboard
                    </a>
                    <a href="{{ route('pemohon.perijinan') }}" class="text-sm font-semibold {{ request()->routeIs('pemohon.perijinan*') || request()->routeIs('pemohon.pengajuan*') ? 'text-amber-700' : 'text-gray-600 hover:text-amber-700' }} transition-colors">
                        Ajukan Izin
                    </a>
                    <a href="{{ route('pemohon.tracking') }}" class="text-sm font-semibold {{ request()->routeIs('pemohon.tracking*') ? 'text-amber-700' : 'text-gray-600 hover:text-amber-700' }} transition-colors">
                        Tracking
                    </a>
                    <a href="{{ route('pemohon.dokumen.index') }}" class="text-sm font-semibold {{ request()->routeIs('pemohon.dokumen*') ? 'text-amber-700' : 'text-gray-600 hover:text-amber-700' }} transition-colors">
                        Dokumen Saya
                    </a>
                </div>

                <!-- User Profile Dropdown -->
                <div class="relative">
                    <button onclick="toggleUserDropdown()"
                        class="flex items-center gap-2 focus:outline-none cursor-pointer hover:bg-amber-50 px-3 py-2 rounded-lg transition-colors">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=78350f&color=fff"
                            class="w-9 h-9 rounded-full border border-amber-200" alt="{{ auth()->user()->name }}">
                        <div class="text-right leading-tight hidden sm:block">
                            <p class="text-sm font-bold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-400">NIK: {{ auth()->user()->nip ?? '-' }}</p>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-gray-400 hidden sm:block"></i>
                    </button>

                    <!-- User Dropdown Menu -->
                    <div id="user-dropdown"
                        class="absolute right-0 mt-2 w-64 bg-white border border-amber-100 rounded-xl shadow-xl py-2 z-50 opacity-0 invisible transition-all duration-200 transform origin-top-right">
                        <div class="px-4 py-3 border-b border-amber-100">
                            <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('pemohon.profile.show') }}"
                            class="flex items-center px-4 py-3 text-gray-700 hover:bg-amber-50 transition-colors">
                            <i class="fas fa-user-circle text-amber-700 w-5 text-center mr-3"></i>
                            <span class="text-sm font-medium">Profil Saya</span>
                        </a>
                        <a href="{{ route('pemohon.profile.change-password') }}"
                            class="flex items-center px-4 py-3 text-gray-700 hover:bg-amber-50 transition-colors">
                            <i class="fas fa-key text-amber-700 w-5 text-center mr-3"></i>
                            <span class="text-sm font-medium">Ganti Password</span>
                        </a>
                        <div class="border-t border-amber-100 my-1"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center px-4 py-3 text-red-600 hover:bg-red-50 transition-colors">
                                <i class="fas fa-sign-out-alt w-5 text-center mr-3"></i>
                                <span class="text-sm font-medium">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    function toggleUserDropdown() {
        const dropdown = document.getElementById('user-dropdown');
        const isHidden = dropdown.classList.contains('invisible');

        // Close all other dropdowns first
        closeAllDropdowns();

        if (isHidden) {
            dropdown.classList.remove('opacity-0', 'invisible');
            dropdown.classList.add('opacity-100', 'visible');
        } else {
            dropdown.classList.remove('opacity-100', 'visible');
            dropdown.classList.add('opacity-0', 'invisible');
        }
    }

    function closeAllDropdowns() {
        // Close user dropdown
        const userDropdown = document.getElementById('user-dropdown');
        if (userDropdown) {
            userDropdown.classList.remove('opacity-100', 'visible');
            userDropdown.classList.add('opacity-0', 'invisible');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('user-dropdown');
        const button = event.target.closest('button[onclick="toggleUserDropdown()"]');

        if (dropdown && !dropdown.contains(event.target) && !button) {
            closeAllDropdowns();
        }
    });

    // Close dropdown on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllDropdowns();
        }
    });
</script>
