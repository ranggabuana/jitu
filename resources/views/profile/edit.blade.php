<x-layout>
    <x-slot:title>Edit Profil</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.show') }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <i class="mdi mdi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Edit Profil</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Perbarui informasi akun Anda</p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-start gap-2">
                    <i class="mdi mdi-alert-circle text-red-600 dark:text-red-400 mt-0.5"></i>
                    <div class="text-red-700 dark:text-red-400 text-sm">
                        <p class="font-medium mb-1">Terdapat kesalahan:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Edit Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <form action="{{ route('profile.update') }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror">
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('username') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                        <i class="mdi mdi-information-outline"></i>
                        Username digunakan untuk login
                    </p>
                </div>

                <!-- NIP -->
                <div>
                    <label for="nip" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        NIP
                    </label>
                    <input type="text" id="nip" name="nip" value="{{ old('nip', $user->nip) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('nip') border-red-500 @enderror"
                        placeholder="Nomor Induk Pegawai">
                </div>

                <!-- No HP -->
                <div>
                    <label for="no_hp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nomor HP
                    </label>
                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('no_hp') border-red-500 @enderror"
                        placeholder="08xxxxxxxxxx">
                </div>

                @if ($user->role === 'pemohon')
                <!-- Jenis Kelamin -->
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('jenis_kelamin') border-red-500 @enderror">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Pendidikan Terakhir -->
                <div>
                    <label for="pendidikan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pendidikan Terakhir <span class="text-red-500">*</span>
                    </label>
                    <select id="pendidikan" name="pendidikan" required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('pendidikan') border-red-500 @enderror">
                        <option value="">-- Pilih Pendidikan Terakhir --</option>
                        @foreach(['SD/MI', 'SMP/MTS', 'SMA/MA', 'SMK/MAK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'] as $edu)
                            <option value="{{ $edu }}" {{ old('pendidikan', $user->pendidikan) === $edu ? 'selected' : '' }}>{{ $edu }}</option>
                        @endforeach
                    </select>
                </div>

                @php
                    $pekerjaanPreset = ['PNS', 'TNI', 'POLRI', 'Swasta', 'Wirausaha'];
                    $isPekerjaanLainnya = !empty($user->pekerjaan) && !in_array($user->pekerjaan, $pekerjaanPreset);
                @endphp
                <!-- Pekerjaan -->
                <div>
                    <label for="pekerjaan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pekerjaan <span class="text-red-500">*</span>
                    </label>
                    <select id="pekerjaan" name="pekerjaan" required onchange="togglePekerjaanLainnyaGlobal()"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('pekerjaan') border-red-500 @enderror">
                        <option value="">-- Pilih Pekerjaan --</option>
                        @foreach($pekerjaanPreset as $job)
                            <option value="{{ $job }}" {{ old('pekerjaan', $isPekerjaanLainnya ? 'Lainnya' : $user->pekerjaan) === $job ? 'selected' : '' }}>{{ $job }}</option>
                        @endforeach
                        <option value="Lainnya" {{ old('pekerjaan', $isPekerjaanLainnya ? 'Lainnya' : $user->pekerjaan) === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Pekerjaan Lainnya -->
                <div id="pekerjaanLainnyaWrapperGlobal" class="{{ old('pekerjaan', $isPekerjaanLainnya ? 'Lainnya' : $user->pekerjaan) === 'Lainnya' ? '' : 'hidden' }}">
                    <label for="pekerjaan_lainnya" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sebutkan Pekerjaan Lainnya <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="pekerjaan_lainnya" name="pekerjaan_lainnya" 
                        value="{{ old('pekerjaan_lainnya', $isPekerjaanLainnya ? $user->pekerjaan : '') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('pekerjaan_lainnya') border-red-500 @enderror"
                        placeholder="Masukkan pekerjaan manual">
                </div>
                @endif

                <!-- Buttons -->
                <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg transition-colors font-medium flex items-center justify-center gap-2">
                        <i class="mdi mdi-content-save"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                    <a href="{{ route('profile.show') }}"
                        class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg transition-colors font-medium">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if ($user->role === 'pemohon')
    @push('scripts')
    <script>
        function togglePekerjaanLainnyaGlobal() {
            const pekerjaanSelect = document.getElementById('pekerjaan');
            const wrapper = document.getElementById('pekerjaanLainnyaWrapperGlobal');
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
        document.addEventListener('DOMContentLoaded', function() {
            togglePekerjaanLainnyaGlobal();
        });
    </script>
    @endpush
    @endif
</x-layout>
