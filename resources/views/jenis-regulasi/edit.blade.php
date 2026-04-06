<x-layout>
    <x-slot:title>Edit Jenis Regulasi</x-slot:title>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Jenis Regulasi</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Perbarui data jenis regulasi</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 max-w-3xl">
        <form action="{{ route('jenis-regulasi.update', $jenisRegulasi->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="nama_jenis" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nama Jenis <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_jenis" name="nama_jenis" value="{{ old('nama_jenis', $jenisRegulasi->nama_jenis) }}"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_jenis') border-red-500 @enderror"
                    placeholder="Contoh: Peraturan Daerah, Peraturan Bupati, dll" autofocus>
                @error('nama_jenis')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Slug <span class="text-gray-400 text-xs">(Opsional - otomatis jika kosong)</span>
                </label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $jenisRegulasi->slug) }}"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror"
                    placeholder="peraturan-daerah">
                @error('slug')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Deskripsi <span class="text-gray-400 text-xs">(Opsional)</span>
                </label>
                <textarea id="deskripsi" name="deskripsi" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('deskripsi') border-red-500 @enderror resize-none"
                    placeholder="Deskripsi singkat tentang jenis regulasi ini">{{ old('deskripsi', $jenisRegulasi->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status" name="status"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                    <option value="aktif" {{ old('status', $jenisRegulasi->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ old('status', $jenisRegulasi->status) === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Update
                </button>
                <a href="{{ route('jenis-regulasi.index') }}"
                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layout>
