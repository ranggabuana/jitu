<x-layout>
    <x-slot:title>Pengaturan - Database</x-slot:title>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <i class="mdi mdi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Pengaturan Database</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Backup dan restore database</p>
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

    <!-- Backup Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="mdi mdi-cloud-upload text-gray-500"></i>
                Backup Database
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Backup Database -->
                <form action="{{ route('settings.backup.database') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-4 rounded-lg transition-colors cursor-pointer">
                        <div class="flex items-center justify-center gap-3">
                            <i class="mdi mdi-database text-2xl"></i>
                            <div class="text-left">
                                <p class="font-medium">Backup Database</p>
                                <p class="text-xs text-blue-100">Export database ke SQL</p>
                            </div>
                        </div>
                    </button>
                </form>

                <!-- Backup Aplikasi -->
                <form action="{{ route('settings.backup.aplikasi') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white px-6 py-4 rounded-lg transition-colors cursor-pointer">
                        <div class="flex items-center justify-center gap-3">
                            <i class="mdi mdi-folder-zip text-2xl"></i>
                            <div class="text-left">
                                <p class="font-medium">Backup Aplikasi</p>
                                <p class="text-xs text-purple-100">Seluruh folder aplikasi</p>
                            </div>
                        </div>
                    </button>
                </form>

                <!-- Full Backup -->
                <form action="{{ route('settings.backup.full') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-4 rounded-lg transition-colors cursor-pointer">
                        <div class="flex items-center justify-center gap-3">
                            <i class="mdi mdi-backup-restore text-2xl"></i>
                            <div class="text-left">
                                <p class="font-medium">Full Backup</p>
                                <p class="text-xs text-green-100">Database + Aplikasi</p>
                            </div>
                        </div>
                    </button>
                </form>
            </div>

            <!-- Info Note -->
            <div
                class="mt-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <i class="mdi mdi-information text-amber-600 dark:text-amber-400 text-xl mt-0.5"></i>
                    <div class="text-amber-700 dark:text-amber-400 text-sm">
                        <p class="font-medium mb-1">Catatan Penting:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Pastikan mysqldump tersedia di server untuk backup database</li>
                            <li>Backup akan disimpan di <code
                                    class="bg-amber-100 dark:bg-amber-900/40 px-1.5 py-0.5 rounded">public/backups/</code>
                            </li>
                            <li>Full backup akan membuat file zip berisi database dan storage</li>
                            <li>Disarankan melakukan backup secara berkala</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Files List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="mdi mdi-history text-gray-500"></i>
                Riwayat Backup
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Database Backups -->
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <i class="mdi mdi-database text-blue-500"></i>
                        Backup Database
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                        <div id="database-backups" class="space-y-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Memuat daftar backup...
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Backup Aplikasi -->
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <i class="mdi mdi-folder-zip text-purple-500"></i>
                        Backup Aplikasi
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                        <div id="aplikasi-backups" class="space-y-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Memuat daftar backup...
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Full Backups -->
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <i class="mdi mdi-backup-restore text-green-500"></i>
                        Full Backup
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                        <div id="full-backups" class="space-y-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Memuat daftar backup...
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load backup lists
        function loadBackups(type, elementId) {
            fetch(`/settings/backup/${type}/list`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById(elementId);
                    if (data.files && data.files.length > 0) {
                        let html = '';
                        data.files.forEach(file => {
                            html += `
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center gap-3">
                                        <i class="mdi mdi-file text-gray-400"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">${file.name}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">${file.date} • ${file.size}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="/settings/backup/${type}/${file.name}/download" 
                                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400 p-2" title="Download">
                                            <i class="mdi mdi-download"></i>
                                        </a>
                                        <button onclick="deleteBackup('${type}', '${file.name}')"
                                            class="text-red-600 hover:text-red-700 dark:text-red-400 p-2" title="Hapus">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </div>
                                </div>
                            `;
                        });
                        container.innerHTML = html;
                    } else {
                        container.innerHTML =
                            '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada backup</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading backups:', error);
                    document.getElementById(elementId).innerHTML =
                        '<p class="text-sm text-red-500 text-center py-4">Gagal memuat daftar backup</p>';
                });
        }

        // Load all backup lists on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadBackups('database', 'database-backups');
            loadBackups('aplikasi', 'aplikasi-backups');
            loadBackups('full', 'full-backups');
        });

        // Delete backup
        function deleteBackup(type, filename) {
            Swal.fire({
                title: 'Hapus Backup?',
                text: 'Apakah Anda yakin ingin menghapus backup ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/settings/backup/${type}/${filename}/delete`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Backup berhasil dihapus.',
                                confirmButtonColor: '#2563eb'
                            });
                            // Reload the backup list
                            loadBackups(type, type + '-backups');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Terjadi kesalahan',
                                confirmButtonColor: '#2563eb'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting backup:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menghapus backup.',
                            confirmButtonColor: '#2563eb'
                        });
                    });
                }
            });
        }
    </script>
</x-layout>
