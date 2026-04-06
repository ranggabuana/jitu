<div id="sidebar"
    class="sidebar w-64 bg-white dark:bg-gray-800 shadow-lg h-screen fixed z-10 transform transition-all duration-300 ease-in-out lg:translate-x-0 -translate-x-full lg:translate-x-0">
    <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex items-center sidebar-header">
        <i class="mdi mdi-chart-line text-blue-500 mr-2 text-xl"></i>
        <h1 class="text-xl font-bold text-gray-800 dark:text-white font-sans">JITU Dashboard</h1>
    </div>
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 sidebar-user-info">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div
                    class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white">
                    <span class="font-semibold">{{ substr(Auth::user()->name ?? 'AU', 0, 1) }}</span>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name ?? 'Admin User' }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email ?? 'admin@example.com' }}</p>
            </div>
        </div>
    </div>
    <nav class="mt-5">
        <!-- Beranda - Single Menu -->
        <a href="{{ route('dashboard') }}"
            class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 transition-colors group {{ request()->routeIs('dashboard') ? 'active-menu' : '' }} menu-item">
            <i class="mdi mdi-home mr-3 text-blue-500 dark:text-blue-400 transition-colors text-lg"></i>
            <span
                class="font-medium {{ request()->routeIs('dashboard') ? 'text-blue-500 dark:text-blue-400' : '' }}">Beranda</span>
        </a>

        <!-- Divider: MAIN -->
        <div class="my-4 ml-6">
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">MAIN</span>
        </div>

        <!-- Master Data Dropdown (Admin Only) -->
        @if (Auth::user()->canAccessAdminMenus())
            <div class="relative">
                <button onclick="toggleSubmenu('master-data-submenu', 'master-data-icon')"
                    class="w-full flex items-center justify-between px-6 py-3 text-gray-700 dark:text-gray-300 transition-colors group menu-item {{ request()->routeIs('opd.*') || request()->routeIs('perijinan.*') ? 'active-menu' : '' }}">
                    <div class="flex items-center">
                        <i
                            class="mdi mdi-database mr-3 text-gray-500 dark:text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors text-lg"></i>
                        <span class="font-medium">Master Data</span>
                    </div>
                    <i id="master-data-icon"
                        class="mdi mdi-chevron-down ml-2 transition-transform duration-300 text-gray-500 dark:text-gray-400 text-lg"></i>
                </button>

                <div id="master-data-submenu" class="submenu pl-10 mt-1" style="max-height: 0;">
                    <a href="{{ route('opd.index') }}"
                        class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('opd.*') ? 'active-menu' : '' }}">
                        <i class="mdi mdi-circle text-[8px] mr-2 text-gray-500 dark:text-gray-400"></i>
                        <span>Data OPD</span>
                    </a>
                    <a href="{{ route('perijinan.index') }}"
                        class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('perijinan.*') ? 'active-menu' : '' }}">
                        <i class="mdi mdi-circle text-[8px] mr-2 text-gray-500 dark:text-gray-400"></i>
                        <span>Jenis Perijinan</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- Perijinan Dropdown (Admin & Assigned Users) -->
        @if (Auth::user()->canAccessDataPerijinan())
            <div class="relative">
                <button onclick="toggleSubmenu('perijinan-submenu', 'perijinan-icon')"
                    class="w-full flex items-center justify-between px-6 py-3 text-gray-700 dark:text-gray-300 transition-colors group menu-item {{ request()->routeIs('data-perijinan.*') ? 'active-menu' : '' }}">
                    <div class="flex items-center">
                        <i
                            class="mdi mdi-file-document mr-3 text-gray-500 dark:text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors text-lg"></i>
                        <span class="font-medium">Data Perijinan</span>
                    </div>
                    <i id="perijinan-icon"
                        class="mdi mdi-chevron-down ml-2 transition-transform duration-300 text-gray-500 dark:text-gray-400 text-lg"></i>
                </button>

                <div id="perijinan-submenu" class="submenu pl-10 mt-1" style="max-height: 0;">
                    <a href="{{ route('data-perijinan.dalam-proses') }}"
                        class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center justify-between submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('data-perijinan.dalam-proses') ? 'active-menu' : '' }}">
                        <div class="flex items-center">
                            <i class="mdi mdi-circle text-[8px] mr-2 text-gray-500 dark:text-gray-400"></i>
                            <span>Dalam Proses</span>
                        </div>
                        @if ($countDalamProses > 0)
                            <span
                                class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 ml-2 text-xs font-bold text-white bg-red-500 rounded-full">
                                {{ $countDalamProses }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('data-perijinan.perlu-perbaikan') }}"
                        class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center justify-between submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('data-perijinan.perlu-perbaikan') ? 'active-menu' : '' }}">
                        <div class="flex items-center">
                            <i class="mdi mdi-circle text-[8px] mr-2 text-orange-500"></i>
                            <span class="text-orange-600 dark:text-orange-400">Perlu Perbaikan</span>
                        </div>
                        @if ($countPerluPerbaikan > 0)
                            <span
                                class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 ml-2 text-xs font-bold text-white bg-orange-500 rounded-full">
                                {{ $countPerluPerbaikan }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('data-perijinan.selesai') }}"
                        class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('data-perijinan.selesai') ? 'active-menu' : '' }}">
                        <i class="mdi mdi-circle text-[8px] mr-2 text-gray-500 dark:text-gray-400"></i>
                        <span>Selesai</span>
                    </a>
                    <a href="{{ route('data-perijinan.ditolak') }}"
                        class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center justify-between submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('data-perijinan.ditolak') ? 'active-menu' : '' }}">
                        <div class="flex items-center">
                            <i class="mdi mdi-circle text-[8px] mr-2 text-red-500"></i>
                            <span class="text-red-600 dark:text-red-400">Ditolak</span>
                        </div>
                        @if ($countDitolak > 0)
                            <span
                                class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 ml-2 text-xs font-bold text-white bg-red-600 rounded-full">
                                {{ $countDitolak }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        @endif

        <!-- Divider: FRONTPAGE (Admin Only) -->
        @if (Auth::user()->canAccessAdminMenus())
            <div class="my-4 ml-6">
                <span
                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">FRONTPAGE</span>
            </div>

            <!-- Berita -->
            <a href="{{ route('berita.index') }}"
                class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 transition-colors group menu-item {{ request()->routeIs('berita.*') ? 'active-menu' : '' }}">
                <i
                    class="mdi mdi-newspaper mr-3 text-gray-500 dark:text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors text-lg"></i>
                <span
                    class="font-medium {{ request()->routeIs('berita.*') ? 'text-blue-500 dark:text-blue-400' : '' }}">Berita</span>
            </a>

            <!-- Regulasi -->
            <a href="{{ route('regulasi.index') }}"
                class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 transition-colors group menu-item {{ request()->routeIs('regulasi.*') ? 'active-menu' : '' }}">
                <i
                    class="mdi mdi-scale-balance mr-3 text-gray-500 dark:text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors text-lg"></i>
                <span
                    class="font-medium {{ request()->routeIs('regulasi.*') ? 'text-blue-500 dark:text-blue-400' : '' }}">Regulasi</span>
            </a>
        @endif

        <!-- Pengaduan (Only for authorized users - Admin & Assigned Handlers) -->
        @if (Auth::user()->canAccessPengaduanMenu())
            <a href="{{ route('admin.pengaduan.index') }}"
                class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 transition-colors group menu-item {{ request()->routeIs('admin.pengaduan.*') ? 'active-menu' : '' }}">
                <i
                    class="mdi mdi-bullhorn mr-3 text-gray-500 dark:text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors text-lg"></i>
                <span
                    class="font-medium {{ request()->routeIs('admin.pengaduan.*') ? 'text-blue-500 dark:text-blue-400' : '' }}">Pengaduan</span>
            </a>
        @endif

        <!-- SKM (Survey Kepuasan Masyarakat) - Admin Only -->
        @if (Auth::user()->canAccessAdminMenus())
            <div class="relative">
                <button onclick="toggleSubmenu('skm-submenu', 'skm-icon')"
                    class="w-full flex items-center justify-between px-6 py-3 text-gray-700 dark:text-gray-300 transition-colors group menu-item {{ request()->routeIs('skm.*') ? 'active-menu' : '' }}">
                    <div class="flex items-center">
                        <i
                            class="mdi mdi-emoticon-happy mr-3 text-gray-500 dark:text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors text-lg"></i>
                        <span
                            class="font-medium {{ request()->routeIs('skm.*') ? 'text-blue-500 dark:text-blue-400' : '' }}">SKM</span>
                    </div>
                    <i id="skm-icon"
                        class="mdi mdi-chevron-down ml-2 transition-transform duration-300 text-gray-500 dark:text-gray-400 text-lg"></i>
                </button>

                <div id="skm-submenu" class="submenu pl-10 mt-1" style="max-height: 0;">
                    <a href="{{ route('skm.data.index') }}"
                        class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('skm.data.*') ? 'active-menu' : '' }}">
                        <i class="mdi mdi-circle text-[8px] mr-2 text-gray-500 dark:text-gray-400"></i>
                        <span>Data SKM</span>
                    </a>
                    <a href="{{ route('skm.hasil.index') }}"
                        class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('skm.hasil.*') ? 'active-menu' : '' }}">
                        <i class="mdi mdi-circle text-[8px] mr-2 text-gray-500 dark:text-gray-400"></i>
                        <span>Laporan SKM</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- Divider: SETTING (Admin Only) -->
        @if (Auth::user()->canAccessAdminMenus())
            <div class="my-4 ml-6">
                <span
                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Setting</span>
            </div>

            <!-- Pengguna Dropdown -->
            <div class="relative">
                <button onclick="toggleSubmenu('pengguna-submenu', 'pengguna-icon')"
                    class="w-full flex items-center justify-between px-6 py-3 text-gray-700 dark:text-gray-300 transition-colors group menu-item {{ request()->routeIs('pengguna.*') ? 'active-menu' : '' }}">
                    <div class="flex items-center">
                        <i
                            class="mdi mdi-account-multiple mr-3 text-gray-500 dark:text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors text-lg"></i>
                        <span
                            class="font-medium {{ request()->routeIs('pengguna.*') ? 'text-blue-500 dark:text-blue-400' : '' }}">Pengguna</span>
                    </div>
                    <i id="pengguna-icon"
                        class="mdi mdi-chevron-down ml-2 transition-transform duration-300 text-gray-500 dark:text-gray-400 text-lg"></i>
                </button>

                <div id="pengguna-submenu" class="submenu pl-10 mt-1" style="max-height: 0;">
                    <a href="{{ route('pengguna.data.index') }}"
                        class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('pengguna.data.*') ? 'active-menu' : '' }}">
                        <i class="mdi mdi-circle text-[8px] mr-2 text-gray-500 dark:text-gray-400"></i>
                        <span>Data Pengguna</span>
                    </a>
                    <a href="{{ route('pemohon.index') }}"
                        class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('pemohon.*') ? 'active-menu' : '' }}">
                        <i class="mdi mdi-circle text-[8px] mr-2 text-gray-500 dark:text-gray-400"></i>
                        <span>Data Pemohon</span>
                    </a>
                    <a href="{{ route('pemerintah.index') }}"
                        class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('pemerintah.*') ? 'active-menu' : '' }}">
                        <i class="mdi mdi-circle text-[8px] mr-2 text-gray-500 dark:text-gray-400"></i>
                        <span>Data Pemerintah</span>
                    </a>
                </div>
            </div>

            <!-- Pengaturan Dropdown -->
            <div class="relative">
                <button onclick="toggleSubmenu('pengaturan-submenu', 'pengaturan-icon')"
                    class="w-full flex items-center justify-between px-6 py-3 text-gray-700 dark:text-gray-300 transition-colors group menu-item {{ request()->routeIs('settings.*') ? 'active-menu' : '' }}">
                    <div class="flex items-center">
                        <i
                            class="mdi mdi-cog mr-3 text-gray-500 dark:text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors text-lg"></i>
                        <span
                            class="font-medium {{ request()->routeIs('settings.*') ? 'text-blue-500 dark:text-blue-400' : '' }}">Pengaturan</span>
                    </div>
                    <i id="pengaturan-icon"
                        class="mdi mdi-chevron-down ml-2 transition-transform duration-300 text-gray-500 dark:text-gray-400 text-lg"></i>
                </button>

                <div id="pengaturan-submenu" class="submenu pl-10 mt-1" style="max-height: 0;">
                    @if (Auth::user()->role === 'admin')
                        <a href="{{ route('settings.pengaduan-handlers') }}"
                            class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('settings.pengaduan-handlers') ? 'active-menu' : '' }}">
                            <i class="mdi mdi-circle text-[8px] mr-2 text-gray-500 dark:text-gray-400"></i>
                            <span>Petugas Pengaduan</span>
                        </a>
                    @endif
                    <a href="{{ route('settings.application') }}"
                        class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('settings.application') ? 'active-menu' : '' }}">
                        <i class="mdi mdi-circle text-[8px] mr-2 text-gray-500 dark:text-gray-400"></i>
                        <span>Pengaturan Aplikasi</span>
                    </a>
                    <a href="{{ route('settings.database') }}"
                        class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('settings.database') ? 'active-menu' : '' }}">
                        <i class="mdi mdi-circle text-[8px] mr-2 text-gray-500 dark:text-gray-400"></i>
                        <span>Database</span>
                    </a>
                    <a href="{{ route('settings.logs') }}"
                        class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 transition-colors rounded-lg mx-2 my-1 flex items-center submenu-item hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('settings.logs') ? 'active-menu' : '' }}">
                        <i class="mdi mdi-circle text-[8px] mr-2 text-gray-500 dark:text-gray-400"></i>
                        <span>Log Aplikasi</span>
                    </a>
                </div>
            </div>
        @endif

        <hr class="my-3">
        <!-- Logout Button -->
        <form action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit"
                class="mt-2 w-full flex items-center px-6 py-3 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-800/30 rounded-lg transition-colors duration-200 border-l-4 border-red-500 hover:border-red-600 cursor-pointer">
                <i class="mdi mdi-logout text-lg mr-3"></i>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </nav>

    <script>
        function toggleSubmenu(submenuId, iconId) {
            const submenu = document.getElementById(submenuId);
            const icon = document.getElementById(iconId);

            if (submenu.classList.contains('open')) {
                submenu.classList.remove('open');
                submenu.style.maxHeight = '0';
                icon.classList.remove('rotate-180');
            } else {
                submenu.classList.add('open');
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
                icon.classList.add('rotate-180');
            }
        }

        // Auto-open submenu if active menu item is inside
        document.addEventListener('DOMContentLoaded', function() {
            // Check for active submenu items and open parent submenu
            const activeSubmenuItems = document.querySelectorAll('.submenu .active-menu');
            activeSubmenuItems.forEach(function(item) {
                const submenu = item.closest('.submenu');
                if (submenu && !submenu.classList.contains('open')) {
                    const submenuId = submenu.id;
                    // Find the button that controls this submenu
                    const button = submenu.previousElementSibling;
                    if (button && button.tagName === 'BUTTON') {
                        // Extract submenuId from onclick attribute
                        const onclickAttr = button.getAttribute('onclick');
                        if (onclickAttr) {
                            // Extract iconId from onclick parameter
                            const match = onclickAttr.match(
                                /toggleSubmenu\(['"]([^'"]+)['"],\s*['"]([^'"]+)['"]\)/);
                            if (match) {
                                const targetSubmenuId = match[1];
                                const targetIconId = match[2];
                                // Open the submenu
                                const targetSubmenu = document.getElementById(targetSubmenuId);
                                const targetIcon = document.getElementById(targetIconId);
                                if (targetSubmenu && targetIcon) {
                                    targetSubmenu.classList.add('open');
                                    targetSubmenu.style.maxHeight = targetSubmenu.scrollHeight + 'px';
                                    targetIcon.classList.add('rotate-180');
                                }
                            }
                        }
                    }
                }
            });
        });

        // Handle window resize to adjust submenu height
        window.addEventListener('resize', function() {
            const openSubmenus = document.querySelectorAll('.submenu.open');
            openSubmenus.forEach(function(submenu) {
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
            });
        });
    </script>
</div>
