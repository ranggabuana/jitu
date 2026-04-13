<footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-4 px-6">
    <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="text-gray-600 dark:text-gray-400 text-sm">
            © {{ date('Y') }} Admin Dashboard. All rights reserved.
        </div>
        <div class="flex space-x-6 mt-2 md:mt-0">
            <a href="#"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 text-sm">Privacy
                Policy</a>
            <a href="#"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 text-sm">Terms of
                Service</a>
            <a href="#"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 text-sm">Contact</a>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="back-to-top"
    class="fixed bottom-6 right-6 bg-gradient-to-r from-blue-500 to-purple-600 text-white p-3 rounded-full shadow-lg opacity-0 invisible transition-opacity duration-300 hover:from-blue-600 hover:to-purple-700 focus:outline-none">
    <i class="mdi mdi-arrow-up text-xl"></i>
</button>
</div>

<!-- Modal for Add/Edit -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-transform scale-95 animate-fade-in">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 id="modal-title" class="text-lg font-medium text-gray-800 dark:text-white">Tambah Data Baru</h3>
            <button id="close-modal"
                class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                <i class="mdi mdi-close"></i>
            </button>
        </div>
        <form id="data-form" class="mt-4">
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2"
                    for="name">Nama</label>
                <input type="text" id="name"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2"
                    for="email">Email</label>
                <input type="email" id="email"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2"
                    for="role">Role</label>
                <select id="role"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all"
                    required>
                    <option value="">Pilih Role</option>
                    <option value="admin">Admin</option>
                    <option value="editor">Editor</option>
                    <option value="viewer">Viewer</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2"
                    for="status">Status</label>
                <select id="status"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all"
                    required>
                    <option value="">Pilih Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" id="cancel-btn"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancel</button>
                <button type="submit"
                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all transform hover:scale-105">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Initialize charts
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Chart
        const salesCtx = document.getElementById('salesChart')?.getContext('2d');
        if (salesCtx) {
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Sales',
                        data: [12000, 19000, 15000, 18000, 22000, 19500],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        }
                    }
                }
            });
        }

        // Traffic Chart
        const trafficCtx = document.getElementById('trafficChart')?.getContext('2d');
        if (trafficCtx) {
            const trafficChart = new Chart(trafficCtx, {
                type: 'bar',
                data: {
                    labels: ['Direct', 'Social', 'Referral', 'Organic'],
                    datasets: [{
                        label: 'Traffic Sources',
                        data: [35, 25, 20, 20],
                        backgroundColor: [
                            '#3b82f6',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444'
                        ],
                        borderColor: [
                            '#2563eb',
                            '#059669',
                            '#d97706',
                            '#dc2626'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        }
                    }
                }
            });
        }

        // Theme Toggle
        const themeToggle = document.getElementById('theme-toggle');

        themeToggle?.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' :
                'light');
        });

        // Check for saved theme preference - default to light mode
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Notification dropdown
        const notificationBtn = document.getElementById('notification-btn');
        const notificationDropdown = document.getElementById('notification-dropdown');

        notificationBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown?.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (notificationBtn && !notificationBtn.contains(e.target)) {
                notificationDropdown?.classList.add('hidden');
            }
        });

        // Submenu Toggle
        const submenuTrigger = document.getElementById('submenu-trigger');
        const submenuContent = document.getElementById('submenu-content');
        const submenuChevron = submenuTrigger?.querySelector('.mdi-chevron-down');

        const submenuTrigger2 = document.getElementById('submenu-trigger-2');
        const submenuContent2 = document.getElementById('submenu-content-2');
        const submenuChevron2 = submenuTrigger2?.querySelector('.mdi-chevron-down');

        submenuTrigger?.addEventListener('click', () => {
            submenuContent?.classList.toggle('open');
            submenuChevron?.classList.toggle('rotate-180');
        });

        submenuTrigger2?.addEventListener('click', () => {
            submenuContent2?.classList.toggle('open');
            submenuChevron2?.classList.toggle('rotate-180');
        });

        // Menu Selection
        const menuItems = document.querySelectorAll('nav a');
        const homeLink = document.querySelector('a[href="#"]');
        const tableSection = document.getElementById('table-section');
        const pageTitle = document.getElementById('page-title');

        // Set initial active menu
        document.getElementById('beranda-link')?.classList.add('active-menu');

        // Function to update active menu
        function setActiveMenu(clickedElement) {
            menuItems.forEach(item => {
                item.classList.remove('active-menu');
            });
            clickedElement.classList.add('active-menu');
        }

        // Modal functionality
        const modal = document.getElementById('modal');
        const addBtn = document.getElementById('add-btn');
        const closeModal = document.getElementById('close-modal');
        const cancelBtn = document.getElementById('cancel-btn');
        const form = document.getElementById('data-form');

        addBtn?.addEventListener('click', () => {
            document.getElementById('modal-title').textContent = 'Tambah Data Baru';
            form?.reset();
            modal?.classList.remove('hidden');
            setTimeout(() => {
                modal?.querySelector('div').classList.remove('scale-95');
            }, 10);
        });

        closeModal?.addEventListener('click', () => {
            modal?.querySelector('div').classList.add('scale-95');
            setTimeout(() => {
                modal?.classList.add('hidden');
            }, 200);
        });

        cancelBtn?.addEventListener('click', () => {
            modal?.querySelector('div').classList.add('scale-95');
            setTimeout(() => {
                modal?.classList.add('hidden');
            }, 200);
        });

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal?.querySelector('div').classList.add('scale-95');
                setTimeout(() => {
                    modal?.classList.add('hidden');
                }, 200);
            }
        });

        // Form submission
        form?.addEventListener('submit', (e) => {
            e.preventDefault();
            // Here you would typically send the data to a server
            alert('Data berhasil disimpan!');
            modal?.querySelector('div').classList.add('scale-95');
            setTimeout(() => {
                modal?.classList.add('hidden');
            }, 200);
        });

        // Edit button functionality
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('modal-title').textContent = 'Ubah Data';
                // In a real app, you would populate the form with existing data
                modal?.classList.remove('hidden');
                setTimeout(() => {
                    modal?.querySelector('div').classList.remove('scale-95');
                }, 10);
            });
        });

        // Delete button functionality
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                    // In a real app, you would remove the row from the table and database
                    btn.closest('tr').remove();
                    alert('Data berhasil dihapus!');
                }
            });
        });

        // Mobile sidebar toggle
        const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
        const sidebarCollapseBtn = document.getElementById('sidebar-collapse-btn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');

        // Toggle sidebar on mobile
        sidebarToggleBtn?.addEventListener('click', () => {
            sidebar?.classList.toggle('-translate-x-full');

            // Change the icon based on the state
            const icon = sidebarToggleBtn.querySelector('i');
            if (sidebar?.classList.contains('-translate-x-full')) {
                icon.className = 'mdi mdi-menu text-xl'; // menu icon when closed
            } else {
                icon.className = 'mdi mdi-close text-xl'; // close icon when open
            }
        });

        // Also close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024 && sidebar && !sidebar.contains(e.target) &&
                sidebarToggleBtn && !sidebarToggleBtn.contains(e.target) &&
                !sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.add('-translate-x-full');
                const icon = sidebarToggleBtn.querySelector('i');
                icon.className = 'mdi mdi-menu text-xl';
            }
        });

        // Collapse/expand sidebar on desktop
        sidebarCollapseBtn?.addEventListener('click', () => {
            const isCollapsed = sidebar?.classList.contains('collapsed');

            if (isCollapsed) {
                // Expand sidebar
                sidebar?.classList.remove('collapsed');

                // Update the icon to collapse
                const icon = sidebarCollapseBtn.querySelector('i');
                icon.className = 'mdi mdi-chevron-left text-xl'; // collapse icon when expanded

                // Update main content margin
                mainContent?.classList.remove('lg:ml-0');
                mainContent?.classList.add('lg:ml-64');
            } else {
                // Collapse sidebar
                sidebar?.classList.add('collapsed');

                // Update the icon to expand
                const icon = sidebarCollapseBtn.querySelector('i');
                icon.className = 'mdi mdi-chevron-right text-xl'; // expand icon when collapsed

                // Update main content margin to full width
                mainContent?.classList.remove('lg:ml-64');
                mainContent?.classList.add('lg:ml-0');
            }
        });

        // User dropdown functionality
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');

        userMenuButton?.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown?.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (userMenuButton && !userMenuButton.contains(e.target)) {
                userDropdown?.classList.add('hidden');
            }
        });

        // Back to Top button functionality
        const backToTopButton = document.getElementById('back-to-top');

        // Show/hide button based on scroll position
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton?.classList.remove('opacity-0', 'invisible');
                backToTopButton?.classList.add('opacity-100', 'visible');
            } else {
                backToTopButton?.classList.add('opacity-0', 'invisible');
                backToTopButton?.classList.remove('opacity-100', 'visible');
            }
        });

        // Scroll to top when button is clicked
        backToTopButton?.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });

    // User dropdown functionality
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');

    userMenuButton?.addEventListener('click', function(e) {
        e.stopPropagation();
        // Toggle visibility with animation
        if (userDropdown?.classList.contains('invisible')) {
            // Show dropdown with animation
            userDropdown?.classList.remove('animate-close');
            userDropdown?.classList.add('animate-open', 'visible');
        } else {
            // Hide dropdown with animation
            userDropdown?.classList.remove('animate-open');
            userDropdown?.classList.add('animate-close');
            // Wait for animation to complete before hiding
            setTimeout(() => {
                userDropdown?.classList.remove('visible');
            }, 300);
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (userMenuButton && !userMenuButton.contains(event.target) && userDropdown && !userDropdown.contains(
                event.target)) {
            userDropdown?.classList.remove('animate-open');
            userDropdown?.classList.add('animate-close');
            // Wait for animation to complete before hiding
            setTimeout(() => {
                userDropdown?.classList.remove('visible');
            }, 300);
        }
    });

    // Handle mobile responsiveness - close dropdown when screen size changes
    window.addEventListener('resize', function() {
        if (window.innerWidth < 640) { // sm breakpoint
            userDropdown?.classList.remove('animate-open');
            userDropdown?.classList.add('animate-close');
            setTimeout(() => {
                userDropdown?.classList.remove('visible');
            }, 300);
        }
    });

    // Also close dropdown when scrolling on mobile to prevent issues
    document.addEventListener('scroll', function() {
        if (window.innerWidth < 640) {
            userDropdown?.classList.remove('animate-open');
            userDropdown?.classList.add('animate-close');
            setTimeout(() => {
                userDropdown?.classList.remove('visible');
            }, 300);
        }
    }, {
        passive: true
    });
</script>
