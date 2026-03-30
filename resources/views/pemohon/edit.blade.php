<x-layout>
    <x-slot:title>Edit Pemohon</x-slot:title>
    <div class="mb-6">
        <a href="{{ route('pemohon.index') }}"
            class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 mb-4">
            <i class="mdi mdi-arrow-left"></i> Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Pemohon</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Perbarui data pemohon</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 max-w-4xl">
        <form action="{{ route('pemohon.update', $pemohon->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $pemohon->name) }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="Nama lengkap" autofocus>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="username" name="username" value="{{ old('username', $pemohon->username) }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('username') border-red-500 @enderror"
                        placeholder="Username untuk login">
                    @error('username')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $pemohon->email) }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                        placeholder="email@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nip" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        NIK / NIP <span class="text-gray-400 text-xs">(Opsional)</span>
                    </label>
                    <input type="text" id="nip" name="nip" value="{{ old('nip', $pemohon->nip) }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nip') border-red-500 @enderror"
                        placeholder="Nomor Induk Kependudukan / Pegawai">
                    @error('nip')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="no_hp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        No. HP <span class="text-gray-400 text-xs">(Opsional)</span>
                    </label>
                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $pemohon->no_hp) }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('no_hp') border-red-500 @enderror"
                        placeholder="08xxxxxxxxxx">
                    @error('no_hp')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status_pemohon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status Pemohon <span class="text-red-500">*</span>
                    </label>
                    <select id="status_pemohon" name="status_pemohon"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status_pemohon') border-red-500 @enderror"
                        onchange="toggleBadanUsahaFields(this.value)">
                        <option value="perorangan" {{ old('status_pemohon', $pemohon->status_pemohon) === 'perorangan' ? 'selected' : '' }}>Perorangan</option>
                        <option value="badan_usaha" {{ old('status_pemohon', $pemohon->status_pemohon) === 'badan_usaha' ? 'selected' : '' }}>Badan Usaha</option>
                    </select>
                    @error('status_pemohon')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div id="badan_usaha_fields" class="border-t border-gray-200 dark:border-gray-700 pt-6 mb-6"
                style="{{ old('status_pemohon', $pemohon->status_pemohon) === 'badan_usaha' ? '' : 'display: none;' }}">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-building text-purple-600"></i>
                    Informasi Badan Usaha
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama_perusahaan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nama Perusahaan <span class="text-gray-400 text-xs">(Opsional)</span>
                        </label>
                        <input type="text" id="nama_perusahaan" name="nama_perusahaan"
                            value="{{ old('nama_perusahaan', $pemohon->nama_perusahaan) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_perusahaan') border-red-500 @enderror"
                            placeholder="Nama perusahaan / badan usaha">
                        @error('nama_perusahaan')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="npwp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            NPWP <span class="text-gray-400 text-xs">(Opsional)</span>
                        </label>
                        <input type="text" id="npwp" name="npwp" value="{{ old('npwp', $pemohon->npwp) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('npwp') border-red-500 @enderror"
                            placeholder="Nomor Pokok Wajib Pajak">
                        @error('npwp')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status" name="status"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                    <option value="aktif" {{ old('status', $pemohon->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ old('status', $pemohon->status) === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <hr class="my-6 border-gray-200 dark:border-gray-700">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Password Baru <span class="text-gray-400 text-xs">(Kosongkan jika tidak diubah)</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                            placeholder="Minimal 8 karakter">
                        <button type="button" onclick="togglePassword('password', 'password-eye-icon')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i id="password-eye-icon" class="mdi mdi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Konfirmasi Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ulangi password baru">
                        <button type="button" onclick="togglePassword('password_confirmation', 'password-confirm-eye-icon')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i id="password-confirm-eye-icon" class="mdi mdi-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Update
                </button>
                <a href="{{ route('pemohon.index') }}"
                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('mdi-eye');
                icon.classList.add('mdi-eye-off');
            } else {
                input.type = 'password';
                icon.classList.remove('mdi-eye-off');
                icon.classList.add('mdi-eye');
            }
        }

        function toggleBadanUsahaFields(status) {
            const container = document.getElementById('badan_usaha_fields');
            const namaPerusahaanInput = document.getElementById('nama_perusahaan');
            const npwpInput = document.getElementById('npwp');

            if (status === 'badan_usaha') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
                namaPerusahaanInput.value = '';
                npwpInput.value = '';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleBadanUsahaFields(document.getElementById('status_pemohon').value);
        });
    </script>
</x-layout>
