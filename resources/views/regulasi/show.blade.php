<x-layout>
    <x-slot:title>Detail Regulasi</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('regulasi.index') }}"
            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
            <i class="mdi mdi-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $regulasi->nama_regulasi }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Dibuat oleh {{ $regulasi->user?->name ?? 'System' }} pada {{ $regulasi->created_at->format('d M Y, H:i') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('regulasi.edit', $regulasi->id) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                    <i class="mdi mdi-pencil mr-2"></i>Edit
                </a>
                <a href="{{ route('regulasi.download', $regulasi->id) }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                    <i class="mdi mdi-download mr-2"></i>Download
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Slug</label>
                    <p class="text-gray-800 dark:text-gray-200 mt-1">{{ $regulasi->slug }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                    <p class="mt-1">
                        @if($regulasi->status === 'aktif')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                Aktif
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                Tidak Aktif
                            </span>
                        @endif
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">File</label>
                    <p class="text-gray-800 dark:text-gray-200 mt-1">
                        <i class="mdi mdi-file mr-2"></i>{{ pathinfo($regulasi->file_regulasi, PATHINFO_FILENAME) }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Type: {{ strtoupper(pathinfo($regulasi->file_regulasi, PATHINFO_EXTENSION)) }} | 
                        Size: {{ $regulasi->formatted_file_size }}
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi</label>
                    <p class="text-gray-800 dark:text-gray-200 mt-1">{{ $regulasi->deskripsi ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Update</label>
                    <p class="text-gray-800 dark:text-gray-200 mt-1">{{ $regulasi->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layout>
