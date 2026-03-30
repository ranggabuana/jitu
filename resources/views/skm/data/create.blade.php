<x-layout>
    <x-slot:title>Tambah Pertanyaan SKM</x-slot:title>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Tambah Pertanyaan SKM</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Tambah pertanyaan baru untuk Survey Kepuasan Masyarakat</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 max-w-4xl">
        <form action="{{ route('skm.data.store') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label for="pertanyaan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pertanyaan <span class="text-red-500">*</span>
                </label>
                <textarea id="pertanyaan" name="pertanyaan" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pertanyaan') border-red-500 @enderror"
                    placeholder="Masukkan pertanyaan">{{ old('pertanyaan') }}</textarea>
                @error('pertanyaan')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="urutan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Urutan <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="urutan" name="urutan" value="{{ old('urutan', 0) }}" min="0"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('urutan') border-red-500 @enderror">
                    @error('urutan')
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

            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg mb-6">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-2">Info Skala Penilaian:</h4>
                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <li><strong>1</strong> = Kurang Baik</li>
                    <li><strong>2</strong> = Cukup Baik</li>
                    <li><strong>3</strong> = Baik</li>
                    <li><strong>4</strong> = Sangat Baik</li>
                </ul>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Simpan
                </button>
                <a href="{{ route('skm.data.index') }}"
                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layout>
