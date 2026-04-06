<x-layout>
    <x-slot:title>Data Pemerintah</x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Data Pemerintah</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola pengguna pemerintah</p>
        </div>
        <a href="{{ route('pemerintah.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition-colors font-semibold shadow-lg">
            <i class="mdi mdi-plus-circle"></i>
            <span>Tambah Data</span>
        </a>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    @if (session('error'))
        <meta name="error-message" content="{{ session('error') }}">
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Total Pemerintah -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transform transition-all hover:scale-105 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Pemerintah</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($totalPemerintah) }}</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Semua pengguna pemerintah</p>
                </div>
                <div class="p-4 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                    <i class="mdi mdi-shield-account text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Aktif -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transform transition-all hover:scale-105 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Aktif</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($aktifCount) }}</h3>
                    <p class="text-xs text-green-500 mt-2">
                        <i class="mdi mdi-check-circle"></i> Pengguna aktif
                    </p>
                </div>
                <div class="p-4 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                    <i class="mdi mdi-check-circle text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <!-- Toolbar -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Total: <strong class="text-gray-800 dark:text-white">{{ $users->total() }} pengguna</strong>
                </span>
            </div>

            <div class="flex items-center gap-2">
                <label for="status_filter" class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Status:</label>
                <select id="status_filter" name="status_filter"
                    class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onchange="updateStatusFilter(this.value)">
                    <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="aktif" {{ $statusFilter == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ $statusFilter == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>

            <div class="w-full sm:w-auto sm:flex-1 sm:max-w-md">
                <form id="search_form" method="GET" action="{{ route('pemerintah.index') }}" class="relative">
                    <input type="text" name="search" id="search" value="{{ $search }}"
                        placeholder="Search Pengguna..."
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 pl-10 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="mdi mdi-magnify absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <button type="submit" class="hidden">Search</button>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Pengguna
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            OPD
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Terdaftar
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="mdi mdi-shield-account text-purple-600 dark:text-purple-300"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ '@' }}{{ $user->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->opd->nama_opd ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('pemerintah.update-status', $user->id) }}" class="status-toggle-form">
                                    @csrf
                                    @method('PATCH')
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="status" value="1"
                                            class="sr-only peer status-toggle" data-id="{{ $user->id }}"
                                            {{ $user->status === 'aktif' ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer dark:bg-gray-600 peer-checked:bg-green-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600">
                                        </div>
                                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300 status-label">
                                            {{ $user->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </label>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2 flex-wrap">
                                    <a href="{{ route('pemerintah.show', $user->id) }}"
                                        class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors"
                                        title="Detail">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('pemerintah.edit', $user->id) }}"
                                        class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors"
                                        title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('pemerintah.destroy', $user->id) }}" method="POST"
                                        class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors btn-delete"
                                            data-action="{{ route('pemerintah.destroy', $user->id) }}" title="Hapus">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="mdi mdi-shield-off text-gray-400 dark:text-gray-500 text-3xl"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">Belum ada data pengguna pemerintah</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <script>
        function updateStatusFilter(status) {
            const url = new URL(window.location.href);
            url.searchParams.set('status_filter', status);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }

        let searchTimeout;
        document.getElementById('search')?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('search_form').submit();
            }, 500);
        });

        // Handle status toggle with AJAX
        document.addEventListener('DOMContentLoaded', function() {
            const statusToggles = document.querySelectorAll('.status-toggle');

            statusToggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const form = this.closest('.status-toggle-form');
                    const statusLabel = form.querySelector('.status-label');
                    const newStatus = this.checked ? 'aktif' : 'tidak_aktif';
                    const originalChecked = this.checked;

                    toggle.disabled = true;
                    statusLabel.textContent = 'Mengubah...';

                    fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                                'X-HTTP-Method-Override': 'PATCH',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ status: newStatus })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                statusLabel.textContent = newStatus === 'aktif' ? 'Aktif' : 'Tidak Aktif';
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            } else {
                                toggle.checked = !originalChecked;
                                statusLabel.textContent = originalChecked ? 'Aktif' : 'Tidak Aktif';
                                toggle.disabled = false;
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message || 'Gagal mengubah status.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toggle.checked = !originalChecked;
                            statusLabel.textContent = originalChecked ? 'Aktif' : 'Tidak Aktif';
                            toggle.disabled = false;
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat mengubah status.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        });
                });
            });
        });
    </script>
</x-layout>
