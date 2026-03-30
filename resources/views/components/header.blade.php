<header class="bg-white dark:bg-gray-800 shadow-md py-4 px-6 flex justify-between items-center">
    <div class="flex items-center">
        <button id="sidebar-toggle-btn" class="mr-4 text-gray-500 dark:text-gray-300 lg:hidden">
            <i class="mdi mdi-menu text-xl"></i>
        </button>
        <button id="sidebar-collapse-btn" class="mr-4 text-gray-500 dark:text-gray-300 hidden lg:block">
            <i class="mdi mdi-chevron-left text-xl"></i>
        </button>

        <!-- Breadcrumb -->
        <nav aria-label="Breadcrumb" class="ml-4 breadcrumb-nav">
            <ol class="flex items-center space-x-2 text-sm">
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                        Home
                    </a>
                </li>
                <li class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 mx-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('dashboard') }}"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                        Dashboard
                    </a>
                </li>
                <li class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 mx-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-gray-500 dark:text-gray-400 font-medium">
                        @yield('page-title', 'Beranda')
                    </span>
                </li>
            </ol>
        </nav>
    </div>
    <div class="flex items-center space-x-2 sm:space-x-4">
        <button id="theme-toggle"
            class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-gray-100 transition-colors cursor-pointer">
            <i class="mdi mdi-moon-waning-crescent dark:hidden"></i>
            <i class="mdi mdi-white-balance-sunny hidden dark:block"></i>
        </button>
        <div class="relative">
            <button id="notification-btn"
                class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-gray-100 transition-colors relative cursor-pointer">
                <i class="mdi mdi-bell"></i>
                <span
                    class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
            </button>

            <!-- Notification Dropdown -->
            <div id="notification-dropdown"
                class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-md shadow-lg py-2 z-20">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Notifications</h3>
                </div>
                <a href="#"
                    class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 pt-1">
                            <div
                                class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                <i class="mdi mdi-information text-blue-500 dark:text-blue-400 text-xs"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">New message</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">You have received a new message
                                from John Doe</p>
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">2 minutes ago</p>
                        </div>
                    </div>
                </a>
                <a href="#"
                    class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 pt-1">
                            <div
                                class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                <i class="mdi mdi-check-circle text-green-500 dark:text-green-400 text-xs"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Order completed</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Your order #12345 has been
                                completed</p>
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">1 hour ago</p>
                        </div>
                    </div>
                </a>
                <a href="#"
                    class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 pt-1">
                            <div
                                class="w-6 h-6 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                                <i class="mdi mdi-alert text-yellow-500 dark:text-yellow-400 text-xs"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Server warning</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">CPU usage is at 90%</p>
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">3 hours ago</p>
                        </div>
                    </div>
                </a>
                <a href="#"
                    class="block px-4 py-3 text-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-b-md">
                    View all notifications
                </a>
            </div>
        </div>
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
                {{-- <a href="#"
                    class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                    <i class="mdi mdi-cog-outline text-blue-500 dark:text-blue-400 text-lg mr-3"></i>
                    <span>Settings</span>
                </a>
                <a href="#"
                    class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                    <i class="mdi mdi-email-outline text-blue-500 dark:text-blue-400 text-lg mr-3"></i>
                    <span>Messages</span>
                    <span
                        class="ml-auto bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 text-xs font-medium px-2 py-0.5 rounded-full">3</span>
                </a> --}}
            </div>
        </div>
    </div>
</header>
