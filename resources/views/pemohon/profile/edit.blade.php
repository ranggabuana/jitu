<x-pemohon.layout>
    <x-slot:title>Edit Profil - Dashboard Pemohon</x-slot:title>

    <x-pemohon.navbar></x-pemohon.navbar>

    <main class="flex-1 max-w-[95%] mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('pemohon.profile.show') }}"
                    class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Edit Profil</h1>
                    <p class="text-sm text-gray-500">Perbarui informasi akun Anda</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <form action="{{ route('pemohon.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                            placeholder="Nama lengkap" autofocus>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('username') border-red-500 @enderror"
                            placeholder="Username untuk login">
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                            placeholder="email@example.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">
                            NIK (Nomor Induk Kependudukan) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nip" name="nip" value="{{ old('nip', $user->nip) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nip') border-red-500 @enderror"
                            placeholder="16 digit NIK" minlength="16" maxlength="16" pattern="[0-9]{16}">
                        @error('nip')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">
                            No. WhatsApp <span class="text-gray-400 text-xs">(Opsional)</span>
                        </label>
                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('no_hp') border-red-500 @enderror"
                            placeholder="08xxxxxxxxxx">
                        @error('no_hp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status_pemohon" class="block text-sm font-medium text-gray-700 mb-2">
                            Status Pemohon <span class="text-red-500">*</span>
                        </label>
                        <select id="status_pemohon" name="status_pemohon"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status_pemohon') border-red-500 @enderror"
                            onchange="toggleBadanUsahaFields(this.value)">
                            <option value="perorangan" {{ old('status_pemohon', $user->status_pemohon) === 'perorangan' ? 'selected' : '' }}>Perorangan</option>
                            <option value="badan_usaha" {{ old('status_pemohon', $user->status_pemohon) === 'badan_usaha' ? 'selected' : '' }}>Badan Usaha</option>
                        </select>
                        @error('status_pemohon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Kelamin <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis_kelamin" name="jenis_kelamin"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jenis_kelamin') border-red-500 @enderror">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pendidikan" class="block text-sm font-medium text-gray-700 mb-2">
                            Pendidikan Terakhir <span class="text-red-500">*</span>
                        </label>
                        <select id="pendidikan" name="pendidikan"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pendidikan') border-red-500 @enderror">
                            <option value="">-- Pilih Pendidikan Terakhir --</option>
                            @foreach(['SD/MI', 'SMP/MTS', 'SMA/MA', 'SMK/MAK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'] as $edu)
                                <option value="{{ $edu }}" {{ old('pendidikan', $user->pendidikan) === $edu ? 'selected' : '' }}>{{ $edu }}</option>
                            @endforeach
                        </select>
                        @error('pendidikan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                @php
                    $pekerjaanPreset = ['PNS', 'TNI', 'POLRI', 'Swasta', 'Wirausaha'];
                    $isPekerjaanLainnya = !empty($user->pekerjaan) && !in_array($user->pekerjaan, $pekerjaanPreset);
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="pekerjaan" class="block text-sm font-medium text-gray-700 mb-2">
                            Pekerjaan <span class="text-red-500">*</span>
                        </label>
                        <select id="pekerjaan" name="pekerjaan"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pekerjaan') border-red-500 @enderror"
                            onchange="togglePekerjaanLainnya()">
                            <option value="">-- Pilih Pekerjaan --</option>
                            @foreach($pekerjaanPreset as $job)
                                <option value="{{ $job }}" {{ old('pekerjaan', $isPekerjaanLainnya ? 'Lainnya' : $user->pekerjaan) === $job ? 'selected' : '' }}>{{ $job }}</option>
                            @endforeach
                            <option value="Lainnya" {{ old('pekerjaan', $isPekerjaanLainnya ? 'Lainnya' : $user->pekerjaan) === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('pekerjaan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="pekerjaanLainnyaWrapper" class="{{ old('pekerjaan', $isPekerjaanLainnya ? 'Lainnya' : $user->pekerjaan) === 'Lainnya' ? '' : 'hidden' }}">
                        <label for="pekerjaan_lainnya" class="block text-sm font-medium text-gray-700 mb-2">
                            Sebutkan Pekerjaan Lainnya <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="pekerjaan_lainnya" name="pekerjaan_lainnya" 
                            value="{{ old('pekerjaan_lainnya', $isPekerjaanLainnya ? $user->pekerjaan : '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pekerjaan_lainnya') border-red-500 @enderror"
                            placeholder="Masukkan pekerjaan manual">
                        @error('pekerjaan_lainnya')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div id="badan_usaha_fields" class="border-t border-gray-100 pt-6 mb-6"
                    style="{{ old('status_pemohon', $user->status_pemohon) === 'badan_usaha' ? '' : 'display: none;' }}">
                    <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-building text-purple-600"></i>
                        Informasi Badan Usaha
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama_perusahaan" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Perusahaan
                            </label>
                            <input type="text" id="nama_perusahaan" name="nama_perusahaan"
                                value="{{ old('nama_perusahaan', $user->nama_perusahaan) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_perusahaan') border-red-500 @enderror"
                                placeholder="PT/CV/UD ...">
                            @error('nama_perusahaan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="npwp" class="block text-sm font-medium text-gray-700 mb-2">
                                NPWP
                            </label>
                            <input type="text" id="npwp" name="npwp" value="{{ old('npwp', $user->npwp) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('npwp') border-red-500 @enderror"
                                placeholder="00.000.000.0-000.000">
                            @error('npwp')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Wilayah Selection -->
                <div class="border-t border-gray-100 pt-6 mb-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-green-600"></i>
                        Data Wilayah & Alamat
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-2">
                                Provinsi
                            </label>
                            <select id="provinsi" name="provinsi_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-wilayah">
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                        </div>

                        <div>
                            <label for="kabupaten" class="block text-sm font-medium text-gray-700 mb-2">
                                Kabupaten/Kota
                                <i class="fas fa-spinner fa-spin ml-2 text-blue-500" id="loader-kabupaten" style="display: none;"></i>
                            </label>
                            <select id="kabupaten" name="kabupaten_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-wilayah" disabled>
                                <option value="">-- Pilih Kabupaten/Kota --</option>
                            </select>
                        </div>

                        <div>
                            <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-2">
                                Kecamatan
                                <i class="fas fa-spinner fa-spin ml-2 text-blue-500" id="loader-kecamatan" style="display: none;"></i>
                            </label>
                            <select id="kecamatan" name="kecamatan_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-wilayah" disabled>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>

                        <div>
                            <label for="kelurahan" class="block text-sm font-medium text-gray-700 mb-2">
                                Kelurahan/Desa
                                <i class="fas fa-spinner fa-spin ml-2 text-blue-500" id="loader-kelurahan" style="display: none;"></i>
                            </label>
                            <select id="kelurahan" name="kelurahan_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-wilayah" disabled>
                                <option value="">-- Pilih Kelurahan/Desa --</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="alamat_ktp" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat KTP
                        </label>
                        <textarea id="alamat_ktp" name="alamat_ktp" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder="Jalan, nomor rumah, RT/RW, dan keterangan lainnya sesuai KTP">{{ old('alamat_ktp', $user->alamat_ktp) }}</textarea>
                        @error('alamat_ktp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="alamat_domisili" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat Domisili
                        </label>
                        <textarea id="alamat_domisili" name="alamat_domisili" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder="Jalan, nomor rumah, RT/RW, dan keterangan lainnya tempat tinggal saat ini">{{ old('alamat_domisili', $user->alamat_domisili) }}</textarea>
                        @error('alamat_domisili')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="foto_ktp" class="block text-sm font-medium text-gray-700 mb-2">
                            Foto KTP <span class="text-gray-400 text-xs">(Kosongkan jika tidak diubah)</span>
                        </label>
                        <div class="flex flex-col md:flex-row gap-4 items-start">
                            @if($user->foto_ktp)
                                <div class="shrink-0">
                                    <p class="text-xs text-gray-500 mb-1">KTP Saat Ini:</p>
                                    <a href="{{ route('secure-file', ['filepath' => $user->foto_ktp]) }}" target="_blank" class="block">
                                        <img src="{{ route('secure-file', ['filepath' => $user->foto_ktp]) }}" alt="KTP" class="w-32 h-20 object-cover rounded-lg border border-gray-300">
                                    </a>
                                </div>
                            @endif
                            <div class="flex-1 w-full">
                                <input type="file" id="foto_ktp" name="foto_ktp" accept="image/jpeg,image/png,image/jpg,application/pdf"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('foto_ktp') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, PDF (Maks. 2MB)</p>
                                @error('foto_ktp')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('pemohon.profile.show') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </main>

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
    </style>
    <script>
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
            loadProvinsi('{{ old("provinsi_id", $user->provinsi_id) }}');
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
                    loadKabupaten(selectedId, '{{ old("kabupaten_id", $user->kabupaten_id) }}');
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

            $.get('{{ url('api/wilayah/provinsi') }}/' + provinsiId + '/kabupaten', function(data) {
                $('#loader-kabupaten').hide();
                $('#kabupaten').prop('disabled', false);
                let options = '<option value="">-- Pilih Kabupaten/Kota --</option>';
                data.data.forEach(item => {
                    options += `<option value="${item.id}" ${item.id == selectedId ? 'selected' : ''}>${item.name}</option>`;
                });
                $('#kabupaten').html(options).trigger('change');

                if (selectedId) {
                    loadKecamatan(selectedId, '{{ old("kecamatan_id", $user->kecamatan_id) }}');
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
                    loadKelurahan(selectedId, '{{ old("kelurahan_id", $user->kelurahan_id) }}');
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
    </main>

    <x-pemohon.footer></x-pemohon.footer>
</x-pemohon.layout>
