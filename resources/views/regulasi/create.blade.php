<x-layout>
    <x-slot:title>Tambah Regulasi</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('regulasi.index') }}"
            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
            <i class="mdi mdi-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    @if ($errors->any())
        <meta name="error-message" content="{{ implode(', ', $errors->all()) }}">
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Tambah Regulasi Baru</h2>

        <form action="{{ route('regulasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="jenis_regulasi_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Jenis Regulasi <span class="text-red-500">*</span>
                </label>
                <select name="jenis_regulasi_id" id="jenis_regulasi_id"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('jenis_regulasi_id') border-red-500 @enderror"
                    required>
                    <option value="">Pilih Jenis Regulasi</option>
                    @foreach($jenisRegulasi as $jenis)
                        <option value="{{ $jenis->id }}" {{ old('jenis_regulasi_id') == $jenis->id ? 'selected' : '' }}>
                            {{ $jenis->nama_jenis }}
                        </option>
                    @endforeach
                </select>
                @error('jenis_regulasi_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nama_regulasi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nama Regulasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_regulasi" id="nama_regulasi" value="{{ old('nama_regulasi') }}"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_regulasi') border-red-500 @enderror"
                    placeholder="Contoh: Peraturan Daerah No. 1 Tahun 2024" required>
                @error('nama_regulasi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Slug (Opsional)
                </label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('slug') border-red-500 @enderror"
                    placeholder="perda-no-1-tahun-2024">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kosongkan untuk generate otomatis dari nama regulasi</p>
                @error('slug')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="file_regulasi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    File Regulasi <span class="text-red-500">*</span>
                </label>
                <input type="file" name="file_regulasi" id="file_regulasi"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('file_regulasi') border-red-500 @enderror"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar" required>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, RAR (Max: 10MB)</p>
                @error('file_regulasi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Deskripsi (Opsional)
                </label>
                <textarea name="deskripsi" id="deskripsi" rows="4"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('deskripsi') border-red-500 @enderror"
                    placeholder="Deskripsi singkat tentang regulasi ini...">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status" id="status"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror"
                    required>
                    <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ old('status') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition-colors">
                    <i class="mdi mdi-content-save mr-2"></i>Simpan
                </button>
                <a href="{{ route('regulasi.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layout>
