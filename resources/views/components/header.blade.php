<header class="bg-white dark:bg-gray-800 shadow-md py-4 px-6 flex justify-between items-center">
    <div class="flex items-center">
        <button id="sidebar-toggle-btn" class="mr-4 text-gray-500 dark:text-gray-300 lg:hidden">
            <i class="mdi mdi-menu text-xl"></i>
        </button>
        <button id="sidebar-collapse-btn" class="mr-4 text-gray-500 dark:text-gray-300 hidden lg:block">
            <i class="mdi mdi-chevron-left text-xl"></i>
        </button>

        <!-- Breadcrumb -->
        @php
            $routeName = Route::currentRouteName();
            $breadcrumbs = [];
            
            // Default home breadcrumb
            $breadcrumbs[] = [
                'title' => 'Home',
                'url' => route('dashboard'),
                'active' => $routeName === 'dashboard' || request()->path() === 'dashboard'
            ];

            $segmentTitles = [
                'dashboard' => 'Dashboard',
                'opd' => 'OPD',
                'perijinan' => 'Perijinan',
                'data-perijinan' => 'Data Perijinan',
                'berita' => 'Berita',
                'regulasi' => 'Regulasi',
                'panduan' => 'Panduan',
                'jenis-regulasi' => 'Jenis Regulasi',
                'pengaduan' => 'Pengaduan',
                'data-skm' => 'Data SKM',
                'hasil-skm' => 'Hasil SKM',
                'profile' => 'Profil',
                'settings' => 'Pengaturan',
                'application' => 'Aplikasi',
                'email' => 'Email',
                'database' => 'Database',
                'logs' => 'Log Sistem',
                'pengaduan-handlers' => 'Petugas Pengaduan',
                'log-tte' => 'Log TTE',
                'selesai' => 'Selesai',
                'ditolak' => 'Ditolak',
                'dalam-proses' => 'Dalam Proses',
                'belum-diproses' => 'Belum Diproses',
                'create' => 'Tambah',
                'edit' => 'Edit',
                'show' => 'Detail',
                'form-builder' => 'Form Builder',
                'alur-validasi' => 'Alur Validasi',
                'change-password' => 'Ubah Password',
                'pengguna' => 'Pengguna',
                'pemohon' => 'Pemohon',
                'pemerintah' => 'Pemerintah',
                'sla-report' => 'Laporan SLA',
            ];

            if ($routeName && strpos($routeName, '.') !== false) {
                $parts = explode('.', $routeName);
                $totalParts = count($parts);
                
                foreach ($parts as $index => $part) {
                    if ($index === 0) {
                        if ($part === 'dashboard') {
                            $breadcrumbs[] = [
                                'title' => 'Dashboard',
                                'url' => null,
                                'active' => true
                            ];
                            continue;
                        }
                        
                        $title = isset($segmentTitles[$part]) ? $segmentTitles[$part] : ucwords(str_replace('-', ' ', $part));
                        $indexRouteName = $part . '.index';
                        $url = Route::has($indexRouteName) ? route($indexRouteName) : null;
                        $isLast = ($index === $totalParts - 1) || ($parts[$index + 1] === 'index');
                        
                        $breadcrumbs[] = [
                            'title' => $title,
                            'url' => $isLast ? null : $url,
                            'active' => $isLast
                        ];
                    } else {
                        if ($part === 'index') {
                            continue;
                        }
                        
                        $title = isset($segmentTitles[$part]) ? $segmentTitles[$part] : ucwords(str_replace('-', ' ', $part));
                        $isLast = ($index === $totalParts - 1);
                        
                        $breadcrumbs[] = [
                            'title' => $title,
                            'url' => null,
                            'active' => $isLast
                        ];
                    }
                }
            } else {
                $segments = request()->segments();
                $builtUrl = '';
                
                foreach ($segments as $index => $segment) {
                    if ($segment === 'dashboard') {
                        if (count($segments) === 1) {
                            $breadcrumbs[] = [
                                'title' => 'Dashboard',
                                'url' => null,
                                'active' => true
                            ];
                        }
                        continue;
                    }

                    if (is_numeric($segment)) {
                        $hasEditNext = isset($segments[$index + 1]) && $segments[$index + 1] === 'edit';
                        $hasFormBuilderNext = isset($segments[$index + 1]) && $segments[$index + 1] === 'form-builder';
                        $hasAlurNext = isset($segments[$index + 1]) && $segments[$index + 1] === 'alur-validasi';
                        $hasSlaNext = isset($segments[$index + 1]) && $segments[$index + 1] === 'sla-report';
                        
                        if (!$hasEditNext && !$hasFormBuilderNext && !$hasAlurNext && !$hasSlaNext) {
                            $breadcrumbs[] = [
                                'title' => 'Detail',
                                'url' => null,
                                'active' => true
                            ];
                        }
                        $builtUrl .= '/' . $segment;
                        continue;
                    }

                    $builtUrl .= '/' . $segment;
                    $title = isset($segmentTitles[$segment]) ? $segmentTitles[$segment] : ucwords(str_replace('-', ' ', $segment));
                    $isLast = ($index === count($segments) - 1) || 
                              ($index === count($segments) - 2 && is_numeric($segments[count($segments) - 1]));
                              
                    $breadcrumbs[] = [
                        'title' => $title,
                        'url' => $isLast ? null : url($builtUrl),
                        'active' => $isLast
                    ];
                }
            }
        @endphp

        <nav aria-label="Breadcrumb" class="ml-4 breadcrumb-nav">
            <ol class="flex items-center space-x-2 text-sm">
                @foreach($breadcrumbs as $breadcrumb)
                    @if(!$loop->first)
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </li>
                    @endif
                    <li>
                        @if($breadcrumb['active'] || !$breadcrumb['url'])
                            <span class="text-gray-500 dark:text-gray-400 font-medium">
                                {{ $breadcrumb['title'] }}
                            </span>
                        @else
                            <a href="{{ $breadcrumb['url'] }}"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                {{ $breadcrumb['title'] }}
                            </a>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    </div>
    <div class="flex items-center space-x-2 sm:space-x-4">
        <div class="relative">
            <button id="user-menu-button" class="flex items-center space-x-2 focus:outline-none cursor-pointer">
                <div
                    class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white sm:w-10 sm:h-10">
                    <span class="font-semibold text-xs sm:text-sm">{{ substr(Auth::user()->name ?? 'AU', 0, 1) }}</span>
                </div>
                <span
                    class="text-gray-700 dark:text-gray-300 font-medium hidden sm:inline-block">{{ Auth::user()->name ?? 'User' }}</span>
                <i class="mdi mdi-chevron-down text-gray-500 dark:text-gray-400 hidden sm:inline-block"></i>
            </button>

            <!-- Dropdown Menu -->
            <div id="user-dropdown"
                class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl py-2 z-20 border border-gray-200 dark:border-gray-700 overflow-hidden opacity-0 invisible">
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ Auth::user()->name ?? 'Admin User' }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ Auth::user()->email ?? 'email@example.com' }}</p>
                </div>
                <a href="{{ route('profile.show') }}"
                    class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                    <i class="mdi mdi-account-outline text-blue-500 dark:text-blue-400 text-lg mr-3"></i>
                    <span>Profil Saya</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                        <i class="mdi mdi-logout text-red-500 dark:text-red-400 text-lg mr-3"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
