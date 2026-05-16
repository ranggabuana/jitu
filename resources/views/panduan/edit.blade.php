<x-layout>
    <x-slot:title>Edit Panduan</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('panduan.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
            <i class="mdi mdi-arrow-left"></i>
            <span>Kembali ke Daftar Panduan</span>
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden max-w-4xl">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white">Form Edit Panduan</h2>
        </div>

        <form action="{{ route('panduan.update', $panduan->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="nama_panduan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Panduan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_panduan" id="nama_panduan" value="{{ old('nama_panduan', $panduan->nama_panduan) }}" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_panduan') border-red-500 @enderror"
                        placeholder="Masukkan nama panduan">
                    @error('nama_panduan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $panduan->slug) }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('slug') border-red-500 @enderror"
                        placeholder="Otomatis dari nama jika kosong">
                    @error('slug')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">File Panduan (PDF)</label>
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-700 w-fit">
                        <i class="mdi mdi-file-pdf-box text-2xl text-red-500"></i>
                        <span class="text-sm text-gray-600 dark:text-gray-400">File saat ini: 
                            <a href="{{ route('panduan.preview', $panduan->id) }}" target="_blank" class="text-blue-600 hover:underline">Preview PDF</a>
                        </span>
                    </div>
                    <input type="file" name="file" id="file" accept=".pdf"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('file') border-red-500 @enderror">
                </div>
                <p class="text-xs text-gray-500 mt-1">Hanya file PDF, maksimal 5MB. Kosongkan jika tidak ingin mengubah file.</p>
                @error('file')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="4"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('deskripsi') border-red-500 @enderror"
                    placeholder="Masukkan deskripsi panduan">{{ old('deskripsi', $panduan->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" required
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                    <option value="aktif" {{ old('status', $panduan->status ? 'aktif' : 'tidak_aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ old('status', $panduan->status ? 'aktif' : 'tidak_aktif') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                <button type="button" onclick="window.location.reload()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition-colors">
                    Perbarui Panduan
                </button>
            </div>
        </form>
    </div>
</x-layout>
