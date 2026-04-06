<x-layout>
    <x-slot:title>Tambah Pengguna Pemerintah</x-slot:title>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Tambah Pengguna Pemerintah</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Tambah pengguna pemerintah baru ke sistem</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 max-w-4xl">
        <form action="{{ route('pemerintah.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
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
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
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
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                        placeholder="email@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nip" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        NIP <span class="text-gray-400 text-xs">(Opsional)</span>
                    </label>
                    <input type="text" id="nip" name="nip" value="{{ old('nip') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nip') border-red-500 @enderror"
                        placeholder="Nomor Induk Pegawai">
                    @error('nip')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="opd_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    OPD <span class="text-red-500">*</span>
                </label>
                <select id="opd_id" name="opd_id" required
                    class="opd-select w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 @error('opd_id') border-red-500 @enderror">
                    <option value="">Pilih OPD</option>
                    @foreach($opds as $opd)
                        <option value="{{ $opd->id }}" {{ old('opd_id') == $opd->id ? 'selected' : '' }}>{{ $opd->nama_opd }}</option>
                    @endforeach
                </select>
                @error('opd_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="no_hp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        No. HP <span class="text-gray-400 text-xs">(Opsional)</span>
                    </label>
                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('no_hp') border-red-500 @enderror"
                        placeholder="08xxxxxxxxxx">
                    @error('no_hp')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak_aktif" {{ old('status') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <hr class="my-6 border-gray-200 dark:border-gray-700">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Password <span class="text-red-500">*</span>
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
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ulangi password">
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
                    Simpan
                </button>
                <a href="{{ route('pemerintah.index') }}"
                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        /* Select2 Dark/Light Theme Overrides */
        .select2-container--default .select2-selection--single {
            height: 42px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            display: flex !important;
            align-items: center !important;
            background-color: #ffffff !important;
        }
        .dark .select2-container--default .select2-selection--single {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #111827 !important;
            padding-left: 1rem !important;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #e5e7eb !important;
        }
        .select2-dropdown {
            border-color: #d1d5db !important;
            border-radius: 0.5rem !important;
        }
        .dark .select2-dropdown {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #e5e7eb !important;
        }
        .dark .select2-results__option {
            color: #e5e7eb !important;
        }
        .dark .select2-results__option--highlighted {
            background-color: #4b5563 !important;
            color: #ffffff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
    </style>

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
    </script>

    <!-- jQuery & Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#opd_id').select2({
                placeholder: 'Cari dan pilih OPD...',
                allowClear: false,
                width: '100%',
                language: {
                    noResults: function() {
                        return "OPD tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });
        });
    </script>
</x-layout>
