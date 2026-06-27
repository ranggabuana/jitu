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

            <div class="mb-6 max-w-md">
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

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6 mb-6">
                <h3 class="text-md font-semibold text-gray-800 dark:text-white mb-4">Kustomisasi Label Pilihan (Skala 1 - 4)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="opsi_1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Label Nilai 1 (Default: Kurang Baik)
                        </label>
                        <input type="text" id="opsi_1" name="opsi_1" value="{{ old('opsi_1', 'Kurang Baik') }}" placeholder="Kurang Baik"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('opsi_1') border-red-500 @enderror">
                        @error('opsi_1')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="opsi_2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Label Nilai 2 (Default: Cukup Baik)
                        </label>
                        <input type="text" id="opsi_2" name="opsi_2" value="{{ old('opsi_2', 'Cukup Baik') }}" placeholder="Cukup Baik"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('opsi_2') border-red-500 @enderror">
                        @error('opsi_2')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="opsi_3" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Label Nilai 3 (Default: Baik)
                        </label>
                        <input type="text" id="opsi_3" name="opsi_3" value="{{ old('opsi_3', 'Baik') }}" placeholder="Baik"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('opsi_3') border-red-500 @enderror">
                        @error('opsi_3')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="opsi_4" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Label Nilai 4 (Default: Sangat Baik)
                        </label>
                        <input type="text" id="opsi_4" name="opsi_4" value="{{ old('opsi_4', 'Sangat Baik') }}" placeholder="Sangat Baik"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('opsi_4') border-red-500 @enderror">
                        @error('opsi_4')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
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
