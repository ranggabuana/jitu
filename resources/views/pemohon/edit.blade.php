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
        <form action="{{ route('pemohon.update', $pemohon->id) }}" method="POST" enctype="multipart/form-data">
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
                        NIK (Nomor Induk Kependudukan) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nip" name="nip" value="{{ old('nip', $pemohon->nip) }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nip') border-red-500 @enderror"
                        placeholder="16 digit NIK" minlength="16" maxlength="16" pattern="[0-9]{16}">
                    @error('nip')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="no_hp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        No. WhatsApp <span class="text-gray-400 text-xs">(Opsional)</span>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <select id="jenis_kelamin" name="jenis_kelamin"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jenis_kelamin') border-red-500 @enderror">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin', $pemohon->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin', $pemohon->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pendidikan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pendidikan Terakhir <span class="text-red-500">*</span>
                    </label>
                    <select id="pendidikan" name="pendidikan"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pendidikan') border-red-500 @enderror">
                        <option value="">-- Pilih Pendidikan Terakhir --</option>
                        @foreach(['SD/MI', 'SMP/MTS', 'SMA/MA', 'SMK/MAK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'] as $edu)
                            <option value="{{ $edu }}" {{ old('pendidikan', $pemohon->pendidikan) === $edu ? 'selected' : '' }}>{{ $edu }}</option>
                        @endforeach
                    </select>
                    @error('pendidikan')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @php
                $pekerjaanPreset = ['PNS', 'TNI', 'POLRI', 'Swasta', 'Wirausaha'];
                $isPekerjaanLainnya = !empty($pemohon->pekerjaan) && !in_array($pemohon->pekerjaan, $pekerjaanPreset);
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="pekerjaan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pekerjaan <span class="text-red-500">*</span>
                    </label>
                    <select id="pekerjaan" name="pekerjaan"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pekerjaan') border-red-500 @enderror"
                        onchange="togglePekerjaanLainnya()">
                        <option value="">-- Pilih Pekerjaan --</option>
                        @foreach($pekerjaanPreset as $job)
                            <option value="{{ $job }}" {{ old('pekerjaan', $isPekerjaanLainnya ? 'Lainnya' : $pemohon->pekerjaan) === $job ? 'selected' : '' }}>{{ $job }}</option>
                        @endforeach
                        <option value="Lainnya" {{ old('pekerjaan', $isPekerjaanLainnya ? 'Lainnya' : $pemohon->pekerjaan) === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('pekerjaan')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div id="pekerjaanLainnyaWrapper" class="{{ old('pekerjaan', $isPekerjaanLainnya ? 'Lainnya' : $pemohon->pekerjaan) === 'Lainnya' ? '' : 'hidden' }}">
                    <label for="pekerjaan_lainnya" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sebutkan Pekerjaan Lainnya <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="pekerjaan_lainnya" name="pekerjaan_lainnya" 
                        value="{{ old('pekerjaan_lainnya', $isPekerjaanLainnya ? $pemohon->pekerjaan : '') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pekerjaan_lainnya') border-red-500 @enderror"
                        placeholder="Masukkan pekerjaan manual">
                    @error('pekerjaan_lainnya')
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

            <!-- Wilayah Selection -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-green-600"></i>
                    Data Wilayah & Alamat
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="provinsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Provinsi
                        </label>
                        <select id="provinsi" name="provinsi_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-wilayah">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>

                    <div>
                        <label for="kabupaten" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Kabupaten/Kota
                            <i class="fas fa-spinner fa-spin ml-2 text-blue-500" id="loader-kabupaten" style="display: none;"></i>
                        </label>
                        <select id="kabupaten" name="kabupaten_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-wilayah" disabled>
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                        </select>
                    </div>

                    <div>
                        <label for="kecamatan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Kecamatan
                            <i class="fas fa-spinner fa-spin ml-2 text-blue-500" id="loader-kecamatan" style="display: none;"></i>
                        </label>
                        <select id="kecamatan" name="kecamatan_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-wilayah" disabled>
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>

                    <div>
                        <label for="kelurahan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Kelurahan/Desa
                            <i class="fas fa-spinner fa-spin ml-2 text-blue-500" id="loader-kelurahan" style="display: none;"></i>
                        </label>
                        <select id="kelurahan" name="kelurahan_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-wilayah" disabled>
                            <option value="">-- Pilih Kelurahan/Desa --</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="alamat_ktp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alamat KTP
                    </label>
                    <textarea id="alamat_ktp" name="alamat_ktp" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        placeholder="Jalan, nomor rumah, RT/RW, dan keterangan lainnya sesuai KTP">{{ old('alamat_ktp', $pemohon->alamat_ktp) }}</textarea>
                    @error('alamat_ktp')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="alamat_domisili" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alamat Domisili
                    </label>
                    <textarea id="alamat_domisili" name="alamat_domisili" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        placeholder="Jalan, nomor rumah, RT/RW, dan keterangan lainnya tempat tinggal saat ini">{{ old('alamat_domisili', $pemohon->alamat_domisili) }}</textarea>
                    @error('alamat_domisili')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="foto_ktp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Foto KTP <span class="text-gray-400 text-xs">(Kosongkan jika tidak diubah)</span>
                    </label>
                    <div class="flex flex-col md:flex-row gap-4 items-start">
                        @if($pemohon->foto_ktp)
                            <div class="shrink-0">
                                <p class="text-xs text-gray-500 mb-1">KTP Saat Ini:</p>
                                <a href="{{ route('secure-file', ['filepath' => $pemohon->foto_ktp]) }}" target="_blank" class="block">
                                    <img src="{{ route('secure-file', ['filepath' => $pemohon->foto_ktp]) }}" alt="KTP" class="w-32 h-20 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                                </a>
                            </div>
                        @endif
                        <div class="flex-1 w-full">
                            <input type="file" id="foto_ktp" name="foto_ktp" accept="image/jpeg,image/png,image/jpg,application/pdf"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('foto_ktp') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, PDF (Maks. 2MB)</p>
                            @error('foto_ktp')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status Akun <span class="text-red-500">*</span>
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
                    <div class="mt-2 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Kriteria Password Kuat:</p>
                        <ul class="text-[10px] text-gray-600 dark:text-gray-400 space-y-1 list-disc list-inside">
                            <li>Minimal 8 karakter</li>
                            <li>Mengandung huruf besar (A-Z) dan kecil (a-z)</li>
                            <li>Mengandung angka (0-9)</li>
                            <li>Mengandung simbol (contoh: @, #, $, %, dll)</li>
                        </ul>
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
                    Update Pemohon
                </button>
                <a href="{{ route('pemohon.index') }}"
                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            background-color: #fff;
        }
        .dark .select2-container--bootstrap-5 .select2-selection {
            background-color: #374151;
            border-color: #4b5563;
            color: #e5e7eb;
        }
        .dark .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: #e5e7eb;
        }
        .dark .select2-dropdown {
            background-color: #374151;
            border-color: #4b5563;
            color: #e5e7eb;
        }
        .dark .select2-results__option--highlighted[aria-selected] {
            background-color: #2563eb;
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

            // Real-time NIK validation
            const nikInput = document.getElementById('nip');
            let nikTimeout;

            nikInput.addEventListener('input', function() {
                clearTimeout(nikTimeout);
                const nik = this.value;
                const statusPemohon = document.getElementById('status_pemohon').value;

                if (nik.length === 16 && /^[0-9]{16}$/.test(nik)) {
                    nikTimeout = setTimeout(() => {
                        $.post('{{ route("api.nik.check") }}', {
                            nik: nik,
                            status_pemohon: statusPemohon,
                            _token: '{{ csrf_token() }}'
                        }, function(data) {
                            // On edit, we need to ignore the current user
                            // The API doesn't know the current user ID, but we can check if it's the same as current
                            if (data.exists && nik !== '{{ $pemohon->nip }}') {
                                nikInput.classList.add('border-red-500');
                                nikInput.classList.remove('border-green-500');
                                if (!document.getElementById('nik-error-ajax')) {
                                    $(nikInput).after(`<p id="nik-error-ajax" class="mt-1 text-sm text-red-600">NIK ini sudah terdaftar.</p>`);
                                }
                            } else {
                                nikInput.classList.remove('border-red-500');
                                nikInput.classList.add('border-green-500');
                                $('#nik-error-ajax').remove();
                            }
                        });
                    }, 500);
                } else {
                    nikInput.classList.remove('border-red-500', 'border-green-500');
                    $('#nik-error-ajax').remove();
                }
            });

            // Initialize Select2
            $('.select2-wilayah').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Wilayah cascading logic
            $('#provinsi').on('change', function() {
                loadKabupaten($(this).val());
            });

            $('#kabupaten').on('change', function() {
                loadKecamatan($(this).val());
            });

            $('#kecamatan').on('change', function() {
                loadKelurahan($(this).val());
            });

            // Initial load
            loadProvinsi('{{ old("provinsi_id", $pemohon->provinsi_id) }}');
            togglePekerjaanLainnya();
        });

        function loadProvinsi(selectedId = null) {
            $.get('{{ route("api.wilayah.provinsi") }}', function(data) {
                let options = '<option value="">-- Pilih Provinsi --</option>';
                data.data.forEach(item => {
                    options += `<option value="${item.id}" ${item.id == selectedId ? 'selected' : ''}>${item.name}</option>`;
                });
                $('#provinsi').html(options).trigger('change');
                
                if (selectedId) {
                    loadKabupaten(selectedId, '{{ old("kabupaten_id", $pemohon->kabupaten_id) }}');
                }
            });
        }

        function loadKabupaten(provinsiId, selectedId = null) {
            if (!provinsiId) {
                $('#kabupaten, #kecamatan, #kelurahan').html('<option value="">-- Pilih --</option>').prop('disabled', true).trigger('change');
                return;
            }

            $('#loader-kabupaten').show();
            $('#kabupaten').prop('disabled', true).empty().append('<option value="">Memuat data...</option>');

            $.get(`{{ url('api/wilayah/provinsi') }}/${provinsiId}/kabupaten`, function(data) {
                $('#loader-kabupaten').hide();
                $('#kabupaten').prop('disabled', false);
                let options = '<option value="">-- Pilih Kabupaten/Kota --</option>';
                data.data.forEach(item => {
                    options += `<option value="${item.id}" ${item.id == selectedId ? 'selected' : ''}>${item.name}</option>`;
                });
                $('#kabupaten').html(options).trigger('change');

                if (selectedId) {
                    loadKecamatan(selectedId, '{{ old("kecamatan_id", $pemohon->kecamatan_id) }}');
                }
            }).fail(function() {
                $('#loader-kabupaten').hide();
                $('#kabupaten').empty().append('<option value="">-- Gagal memuat data --</option>');
            });
        }

        function loadKecamatan(kabupatenId, selectedId = null) {
            if (!kabupatenId) {
                $('#kecamatan, #kelurahan').html('<option value="">-- Pilih --</option>').prop('disabled', true).trigger('change');
                return;
            }

            $('#loader-kecamatan').show();
            $('#kecamatan').prop('disabled', true).empty().append('<option value="">Memuat data...</option>');

            $.get(`{{ url('api/wilayah/kabupaten') }}/${kabupatenId}/kecamatan`, function(data) {
                $('#loader-kecamatan').hide();
                $('#kecamatan').prop('disabled', false);
                let options = '<option value="">-- Pilih Kecamatan --</option>';
                data.data.forEach(item => {
                    options += `<option value="${item.id}" ${item.id == selectedId ? 'selected' : ''}>${item.name}</option>`;
                });
                $('#kecamatan').html(options).trigger('change');

                if (selectedId) {
                    loadKelurahan(selectedId, '{{ old("kelurahan_id", $pemohon->kelurahan_id) }}');
                }
            }).fail(function() {
                $('#loader-kecamatan').hide();
                $('#kecamatan').empty().append('<option value="">-- Gagal memuat data --</option>');
            });
        }

        function loadKelurahan(kecamatanId, selectedId = null) {
            if (!kecamatanId) {
                $('#kelurahan').html('<option value="">-- Pilih --</option>').prop('disabled', true).trigger('change');
                return;
            }

            $('#loader-kelurahan').show();
            $('#kelurahan').prop('disabled', true).empty().append('<option value="">Memuat data...</option>');

            $.get(`{{ url('api/wilayah/kecamatan') }}/${kecamatanId}/kelurahan`, function(data) {
                $('#loader-kelurahan').hide();
                $('#kelurahan').prop('disabled', false);
                let options = '<option value="">-- Pilih Kelurahan/Desa --</option>';
                data.data.forEach(item => {
                    options += `<option value="${item.id}" ${item.id == selectedId ? 'selected' : ''}>${item.name}</option>`;
                });
                $('#kelurahan').html(options).trigger('change');
            }).fail(function() {
                $('#loader-kelurahan').hide();
                $('#kelurahan').empty().append('<option value="">-- Gagal memuat data --</option>');
            });
        }

        function togglePekerjaanLainnya() {
            const pekerjaanSelect = document.getElementById('pekerjaan');
            const wrapper = document.getElementById('pekerjaanLainnyaWrapper');
            const input = document.getElementById('pekerjaan_lainnya');

            if (pekerjaanSelect.value === 'Lainnya') {
                wrapper.classList.remove('hidden');
                input.required = true;
            } else {
                wrapper.classList.add('hidden');
                input.required = false;
                input.value = '';
            }
        }
    </script>
    @endpush
</x-layout>
