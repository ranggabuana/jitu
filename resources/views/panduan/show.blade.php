<x-layout>
    <x-slot:title>Detail Panduan</x-slot:title>

    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('panduan.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
            <i class="mdi mdi-arrow-left"></i>
            <span>Kembali ke Daftar Panduan</span>
        </a>
        <div class="flex gap-2">
            <a href="{{ route('panduan.edit', $panduan->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-1">
                <i class="mdi mdi-pencil"></i> Edit
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white">Informasi Panduan</h2>
        </div>
        
        <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Panduan</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">{{ $panduan->nama_panduan }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Slug</h3>
                    <p class="mt-1 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-900 px-2 py-1 rounded w-fit">{{ $panduan->slug }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</h3>
                    <div class="mt-1">
                        @if($panduan->status)
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">Aktif</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">Tidak Aktif</span>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deskripsi</h3>
                    <div class="mt-1 text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-100 dark:border-gray-800">
                        {!! nl2br(e($panduan->deskripsi ?? 'Tidak ada deskripsi.')) !!}
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Terakhir Diperbarui</h3>
                    <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $panduan->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pratinjau File</h3>
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-900 flex items-center justify-center aspect-[3/4] lg:aspect-auto lg:h-[600px]">
                    @if($panduan->file && file_exists(public_path($panduan->file)))
                        <iframe src="{{ route('panduan.preview', $panduan->id) }}" class="w-full h-full" frameborder="0"></iframe>
                    @else
                        <div class="text-center p-8">
                            <i class="mdi mdi-file-alert-outline text-6xl text-gray-400"></i>
                            <p class="mt-2 text-gray-500">File tidak tersedia atau tidak ditemukan.</p>
                        </div>
                    @endif
                </div>
                <div class="flex justify-center">
                    <a href="{{ route('panduan.preview', $panduan->id) }}" target="_blank" class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                        <i class="mdi mdi-open-in-new"></i> Buka di Tab Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layout>
