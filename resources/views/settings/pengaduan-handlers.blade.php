<x-layout>
    <x-slot:title>Petugas Pengaduan</x-slot:title>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <i class="mdi mdi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Petugas Pengaduan</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kelola petugas yang dapat mengakses menu pengaduan</p>
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

    @if (session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center gap-2 text-red-700 dark:text-red-400">
                <i class="mdi mdi-alert-circle"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Assign Handler -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 sticky top-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                            <i class="mdi mdi-account-plus text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-medium text-gray-800 dark:text-white">Tambah Petugas</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tunjuk user sebagai petugas pengaduan</p>
                        </div>
                    </div>
                </div>
                <form id="assignForm" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Pilih User <span class="text-red-500 ml-1">*</span>
                        </label>
                        
                        <!-- Searchable Select -->
                        <div class="relative">
                            <input type="text" id="userSearch" 
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                                placeholder="Cari nama atau email user...">
                            <i class="mdi mdi-magnify absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                        
                        <div id="userDropdown" 
                            class="hidden absolute z-50 w-full max-w-md mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <!-- User options will be populated here -->
                        </div>
                        
                        <input type="hidden" name="user_id" id="user_id" required>
                        <input type="hidden" name="user_name" id="user_name">
                        
                        <!-- Selected User Display -->
                        <div id="selectedUser" class="hidden mt-2 p-3 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                                        <span id="selectedUserInitial">U</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white" id="selectedUserName">User Name</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" id="selectedUserEmail">user@example.com</p>
                                    </div>
                                </div>
                                <button type="button" onclick="clearSelectedUser()" 
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                        </div>
                        
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                            <i class="mdi mdi-information-outline"></i>
                            User yang ditunjuk akan dapat mengakses menu pengaduan
                        </p>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg transition-colors font-medium flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="mdi mdi-account-check"></i>
                        <span>Tunjuk sebagai Petugas</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Daftar Petugas -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                            <i class="mdi mdi-badge-account-outline text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-medium text-gray-800 dark:text-white">
                                Petugas Aktif
                                <span class="text-gray-500 dark:text-gray-400 font-normal">({{ $handlers->count() }} petugas)</span>
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">User yang dapat mengelola pengaduan</p>
                        </div>
                    </div>
                </div>

                @if($handlers->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($handlers as $handler)
                            <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4 flex-1">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-md">
                                            {{ substr($handler->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h3 class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $handler->user->name ?? 'Unknown User' }}
                                                </h3>
                                                <span class="bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-xs px-2 py-1 rounded-md font-medium">
                                                    <i class="mdi mdi-check-circle"></i> Aktif
                                                </span>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                                <span class="flex items-center gap-1.5">
                                                    <i class="mdi mdi-email-outline"></i>
                                                    {{ $handler->user->email ?? '-' }}
                                                </span>
                                                <span class="flex items-center gap-1.5">
                                                    <i class="mdi mdi-shield-account"></i>
                                                    {{ ucfirst($handler->user->role ?? 'user') }}
                                                </span>
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                <span class="flex items-center gap-1">
                                                    <i class="mdi mdi-calendar-clock"></i>
                                                    Ditunjuk pada: {{ $handler->assigned_at->isoFormat('D MMMM Y, HH:mm') }}
                                                </span>
                                                @if($handler->assignedBy)
                                                    <span class="flex items-center gap-1 mt-1">
                                                        <i class="mdi mdi-account-tie"></i>
                                                        Oleh: {{ $handler->assignedBy->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center gap-2">
                                        <button onclick="removeHandler({{ $handler->user_id }})"
                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                            title="Hapus Petugas">
                                            <i class="mdi mdi-account-remove-outline text-xl"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                            <i class="mdi mdi-badge-account-outline text-4xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">Belum ada petugas pengaduan</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                            Gunakan form di samping untuk menunjuk petugas
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // User data from server
        const usersData = @json($users);
        let selectedUser = null;

        // Searchable dropdown functionality
        const userSearch = document.getElementById('userSearch');
        const userDropdown = document.getElementById('userDropdown');
        const userIdInput = document.getElementById('user_id');
        const userNameInput = document.getElementById('user_name');

        // Filter users based on search
        function filterUsers(searchTerm) {
            const term = searchTerm.toLowerCase().trim();
            if (!term) return usersData;
            
            return usersData.filter(user => 
                user.name.toLowerCase().includes(term) || 
                user.email.toLowerCase().includes(term)
            );
        }

        // Render dropdown options
        function renderDropdown(users) {
            if (users.length === 0) {
                userDropdown.innerHTML = `
                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                        <i class="mdi mdi-account-search-outline text-3xl mb-2"></i>
                        <p class="text-sm">User tidak ditemukan</p>
                    </div>
                `;
            } else {
                userDropdown.innerHTML = users.map(user => `
                    <div class="user-option p-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                         data-user-id="${user.id}"
                         data-user-name="${user.name}"
                         data-user-email="${user.email}">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold flex-shrink-0">
                                ${user.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white text-sm truncate">${user.name}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">${user.email}</p>
                            </div>
                        </div>
                    </div>
                `).join('');

                // Add click handlers
                userDropdown.querySelectorAll('.user-option').forEach(option => {
                    option.addEventListener('click', () => selectUser(option));
                });
            }
        }

        // Select user
        function selectUser(option) {
            const userId = option.dataset.userId;
            const userName = option.dataset.userName;
            const userEmail = option.dataset.userEmail;

            selectedUser = { id: userId, name: userName, email: userEmail };
            
            userIdInput.value = userId;
            userNameInput.value = userName;
            
            // Update selected user display
            document.getElementById('selectedUserInitial').textContent = userName.charAt(0).toUpperCase();
            document.getElementById('selectedUserName').textContent = userName;
            document.getElementById('selectedUserEmail').textContent = userEmail;
            document.getElementById('selectedUser').classList.remove('hidden');
            
            // Hide dropdown and clear search
            userDropdown.classList.add('hidden');
            userSearch.value = '';
        }

        // Clear selected user
        function clearSelectedUser() {
            selectedUser = null;
            userIdInput.value = '';
            userNameInput.value = '';
            document.getElementById('selectedUser').classList.add('hidden');
            userSearch.focus();
        }

        // Search input handler
        userSearch.addEventListener('input', (e) => {
            const searchTerm = e.target.value;
            const filteredUsers = filterUsers(searchTerm);
            
            if (searchTerm.length > 0 || filteredUsers.length > 0) {
                userDropdown.classList.remove('hidden');
                renderDropdown(filteredUsers);
            } else {
                userDropdown.classList.add('hidden');
            }
        });

        // Show dropdown on focus
        userSearch.addEventListener('focus', () => {
            const filteredUsers = filterUsers(userSearch.value);
            if (filteredUsers.length > 0) {
                userDropdown.classList.remove('hidden');
                renderDropdown(filteredUsers);
            }
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!userSearch.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });

        // Handle assign form submission
        document.getElementById('assignForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = this;
            const submitBtn = document.getElementById('submitBtn');
            const userId = form.querySelector('#user_id').value;

            if (!userId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih user yang akan ditunjuk!',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Memproses...';

            try {
                const response = await fetch('{{ route("settings.pengaduan-handlers.assign") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ user_id: userId })
                });

                const data = await response.json();

                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        confirmButtonColor: '#4f46e5'
                    });

                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan',
                        confirmButtonColor: '#4f46e5'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan. Silakan coba lagi.',
                    confirmButtonColor: '#4f46e5'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });

        // Remove handler
        async function removeHandler(userId) {
            const result = await Swal.fire({
                title: 'Hapus Petugas?',
                text: 'User ini tidak akan lagi dapat mengakses menu pengaduan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch(`/settings/pengaduan-handlers/remove/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        confirmButtonColor: '#4f46e5'
                    });

                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan',
                        confirmButtonColor: '#4f46e5'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan. Silakan coba lagi.',
                    confirmButtonColor: '#4f46e5'
                });
            }
        }
    </script>
</x-layout>
