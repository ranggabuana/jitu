<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-gray-800">Dokumen Saya</h3>
            <p class="text-sm text-gray-500">Dokumen yang telah diunggah atau tersimpan pada aplikasi.</p>
        </div>
        <div class="hidden md:flex items-center gap-2 text-xs text-gray-500">
            <i class="fas fa-folder-open"></i>
            <span id="docCount">{{ count($documents) }} Dokumen</span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                <tr>
                    <th class="p-4 border-b">Nama Dokumen</th>
                    <th class="p-4 border-b">Jenis</th>
                    <th class="p-4 border-b">Tanggal</th>
                    <th class="p-4 border-b">Status</th>
                    <th class="p-4 border-b text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100" id="docTable">
                @forelse($documents as $doc)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 border-b">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <span class="font-medium text-gray-800">{{ $doc['name'] }}</span>
                            </div>
                        </td>
                        <td class="p-4 border-b">
                            <span class="text-sm text-gray-600">{{ $doc['type'] }}</span>
                        </td>
                        <td class="p-4 border-b">
                            <span class="text-sm text-gray-500">{{ $doc['date'] }}</span>
                        </td>
                        <td class="p-4 border-b">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> {{ $doc['status'] }}
                            </span>
                        </td>
                        <td class="p-4 border-b text-right">
                            <a href="{{ $doc['url'] }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                <i class="fas fa-download"></i> Unduh
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">
                            <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada dokumen</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-6 border-t border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h4 class="font-bold text-gray-800">Preview Dokumen</h4>
                <p class="text-sm text-gray-500">Tampilan ringkas dokumen Anda.</p>
            </div>
            <a href="#" class="hidden md:inline-flex items-center gap-2 text-blue-600 font-bold hover:text-blue-800 text-sm">
                Kelola Dokumen <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        <div id="docPreview" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($documents as $doc)
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center hover:shadow-md transition-all cursor-pointer">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mx-auto mb-2">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-700 truncate">{{ $doc['name'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $doc['type'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
