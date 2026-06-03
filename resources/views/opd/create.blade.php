<x-layout>
    <x-slot:title>Tambah Data OPD</x-slot:title>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Tambah OPD</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Tambahkan data Organisasi Perangkat Daerah baru</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('opd.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <label for="nama_opd" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nama OPD
                </label>
                <input type="text" id="nama_opd" name="nama_opd" value="{{ old('nama_opd') }}"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_opd') border-red-500 @enderror"
                    placeholder="Masukkan nama OPD" autofocus>
                @error('nama_opd')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="kode_opd" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Kode OPD
                </label>
                <input type="text" id="kode_opd" name="kode_opd" value="{{ old('kode_opd') }}"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kode_opd') border-red-500 @enderror"
                    placeholder="Masukkan kode OPD">
                @error('kode_opd')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="gambar_tte" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Gambar TTE (Tanda Tangan Elektronik)
                </label>
                <input type="file" id="gambar_tte" name="gambar_tte" accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gambar_tte') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format yang didukung: JPEG, PNG, JPG, GIF, SVG. Ukuran maksimal: 2MB.</p>
                @error('gambar_tte')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Simpan
                </button>
                <a href="{{ route('opd.index') }}"
                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layout>
