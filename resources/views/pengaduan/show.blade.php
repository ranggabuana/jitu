<x-layout>
    <x-slot:title>Detail Pengaduan</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('admin.pengaduan.index') }}"
            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
            <i class="mdi mdi-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    @if ($errors->any())
        <meta name="error-message" content="{{ implode(', ', $errors->all()) }}">
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content - Complaint Details -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $pengaduan->no_pengaduan }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ $pengaduan->tanggal_pengaduan->isoFormat('dddd, D MMMM Y - HH:mm') }}
                        </p>
                    </div>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                            'proses' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
                            'selesai' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                            'ditolak' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-sm rounded-full {{ $statusColors[$pengaduan->status] }}">
                        {{ $pengaduan->status_label }}
                    </span>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Pelapor</label>
                            <p class="text-gray-800 dark:text-gray-200 mt-1">{{ $pengaduan->nama }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                            <p class="text-gray-800 dark:text-gray-200 mt-1">{{ $pengaduan->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">No. HP</label>
                            <p class="text-gray-800 dark:text-gray-200 mt-1">{{ $pengaduan->no_hp }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Kategori</label>
                            <p class="text-gray-800 dark:text-gray-200 mt-1">
                                <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                                    {{ ucfirst($pengaduan->kategori) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Isi Pengaduan</label>
                        <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $pengaduan->isi_pengaduan }}</p>
                        </div>
                    </div>

                    @if($pengaduan->lampiran)
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Lampiran</label>
                            <div class="mt-2 flex items-center gap-3">
                                <a href="{{ route('admin.pengaduan.download', $pengaduan->id) }}"
                                    class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    <i class="mdi mdi-file-download mr-2"></i>
                                    <span>Download Lampiran</span>
                                </a>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    ({{ strtoupper(pathinfo($pengaduan->lampiran, PATHINFO_EXTENSION)) }} - {{ $pengaduan->formatted_file_size }})
                                </span>
                            </div>
                        </div>
                    @endif

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Ditindaklanjuti Oleh</label>
                        <p class="text-gray-800 dark:text-gray-200 mt-1">
                            {{ $pengaduan->user?->name ?? '-' }}
                        </p>
                        @if($pengaduan->tanggal_respon)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $pengaduan->tanggal_respon->isoFormat('dddd, D MMMM Y - HH:mm') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar - Status Update Form -->
        <div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 sticky top-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">
                    <i class="mdi mdi-cog text-blue-500 mr-2"></i>
                    Tindak Lanjuti
                </h3>

                <form action="{{ route('admin.pengaduan.update-status', $pengaduan->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                            <option value="pending" {{ old('status', $pengaduan->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="proses" {{ old('status', $pengaduan->status) === 'proses' ? 'selected' : '' }}>Dalam Proses</option>
                            <option value="selesai" {{ old('status', $pengaduan->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="ditolak" {{ old('status', $pengaduan->status) === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div>
                        <label for="respon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Respon / Catatan
                        </label>
                        <textarea name="respon" id="respon" rows="5"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Tambahkan respon atau catatan tindak lanjut...">{{ old('respon', $pengaduan->respon) }}</textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition-colors">
                        <i class="mdi mdi-content-save mr-2"></i>Simpan Perubahan
                    </button>
                </form>

                @if($pengaduan->respon)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Respon Terakhir:</h4>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $pengaduan->respon }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
