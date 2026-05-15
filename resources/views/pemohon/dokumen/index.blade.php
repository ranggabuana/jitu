<x-pemohon.layout>
    <x-slot:title>Dokumen Saya</x-slot:title>

    <x-pemohon.navbar></x-pemohon.navbar>

    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dokumen Saya</h1>
        <p class="text-gray-600 dark:text-gray-400">Kelola dokumen umum dan spesifik Anda untuk mempermudah proses perizinan.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 flex items-center gap-2 text-green-700 dark:text-green-400">
            <i class="mdi mdi-check-circle text-xl"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center gap-2 text-red-700 dark:text-red-400">
                <i class="mdi mdi-alert-circle text-xl"></i>
                <span class="font-medium">Terjadi kesalahan validasi:</span>
            </div>
            <ul class="mt-2 ml-7 list-disc text-sm text-red-600 dark:text-red-400">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button onclick="switchTab('tab-umum')" id="btn-tab-umum" class="active-tab-btn tab-btn whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600 dark:text-blue-400 transition-all flex items-center gap-2">
                <i class="mdi mdi-folder-outline text-lg"></i>
                Dokumen Umum
            </button>
            <button onclick="switchTab('tab-spesifik')" id="btn-tab-spesifik" class="tab-btn whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 hover:border-gray-300 transition-all flex items-center gap-2">
                <i class="mdi mdi-folder-star-outline text-lg"></i>
                Dokumen Spesifik
            </button>
        </nav>
    </div>

    <!-- Tab Umum -->
    <div id="tab-umum" class="tab-content block">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($masterUmum as $master)
                @php
                    $myDoc = $userDokumens->get($master->id);
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white">{{ $master->nama_dokumen }}</h3>
                            <p class="text-xs text-gray-500 mt-1 uppercase">{{ $master->tipe_data_file }} &bull; Max {{ number_format($master->max_size ?? 2048, 0, ',', '.') }} KB</p>
                        </div>
                        @if($myDoc)
                            <span class="bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 px-2 py-1 rounded-full text-xs font-semibold flex items-center gap-1">
                                <i class="mdi mdi-check"></i> Tersedia
                            </span>
                        @else
                            <span class="bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 px-2 py-1 rounded-full text-xs font-semibold flex items-center gap-1">
                                <i class="mdi mdi-close"></i> Belum Ada
                            </span>
                        @endif
                    </div>
                    <div class="p-5 bg-gray-50/50 dark:bg-gray-700/30">
                        @if($myDoc)
                            <div class="mb-4 flex items-center justify-between">
                                <div class="text-sm truncate text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                    <i class="mdi mdi-file-document text-blue-500"></i>
                                    <span class="truncate">{{ basename($myDoc->file_path) }}</span>
                                </div>
                                <a href="{{ asset($myDoc->file_path) }}" target="_blank" class="text-blue-600 hover:underline text-xs flex-shrink-0 ml-2">Lihat File</a>
                            </div>
                        @endif
                        <form action="{{ route('pemohon.dokumen.upload', $master->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="flex items-center gap-2">
                                <input type="file" name="file_dokumen" required accept=".{{ str_replace(',', ',.', $master->tipe_data_file) }}" class="block w-full text-sm text-gray-500 dark:text-gray-400
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-xs file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100 cursor-pointer border border-gray-200 dark:border-gray-600 rounded-full bg-white dark:bg-gray-800"/>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full text-sm font-medium transition-colors shadow-sm whitespace-nowrap">
                                    {{ $myDoc ? 'Ganti' : 'Unggah' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-gray-800 p-8 rounded-xl text-center shadow-sm border border-gray-100 dark:border-gray-700 text-gray-500">
                    <i class="mdi mdi-folder-open text-4xl text-gray-300 mb-2"></i>
                    <p>Belum ada master dokumen umum.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Tab Spesifik -->
    <div id="tab-spesifik" class="tab-content hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($masterSpesifik as $master)
                @php
                    $myDoc = $userDokumens->get($master->id);
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white">{{ $master->nama_dokumen }}</h3>
                            <p class="text-xs text-gray-500 mt-1 uppercase">{{ $master->tipe_data_file }} &bull; Max {{ number_format($master->max_size ?? 2048, 0, ',', '.') }} KB</p>
                        </div>
                        @if($myDoc)
                            <span class="bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 px-2 py-1 rounded-full text-xs font-semibold flex items-center gap-1">
                                <i class="mdi mdi-check"></i> Tersedia
                            </span>
                        @else
                            <span class="bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 px-2 py-1 rounded-full text-xs font-semibold flex items-center gap-1">
                                <i class="mdi mdi-close"></i> Belum Ada
                            </span>
                        @endif
                    </div>
                    <div class="p-5 bg-gray-50/50 dark:bg-gray-700/30">
                        @if($myDoc)
                            <div class="mb-4 flex items-center justify-between">
                                <div class="text-sm truncate text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                    <i class="mdi mdi-file-document text-blue-500"></i>
                                    <span class="truncate">{{ basename($myDoc->file_path) }}</span>
                                </div>
                                <a href="{{ asset($myDoc->file_path) }}" target="_blank" class="text-blue-600 hover:underline text-xs flex-shrink-0 ml-2">Lihat File</a>
                            </div>
                        @endif
                        <form action="{{ route('pemohon.dokumen.upload', $master->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="flex items-center gap-2">
                                <input type="file" name="file_dokumen" required accept=".{{ str_replace(',', ',.', $master->tipe_data_file) }}" class="block w-full text-sm text-gray-500 dark:text-gray-400
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-xs file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100 cursor-pointer border border-gray-200 dark:border-gray-600 rounded-full bg-white dark:bg-gray-800"/>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full text-sm font-medium transition-colors shadow-sm whitespace-nowrap">
                                    {{ $myDoc ? 'Ganti' : 'Unggah' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-gray-800 p-8 rounded-xl text-center shadow-sm border border-gray-100 dark:border-gray-700 text-gray-500">
                    <i class="mdi mdi-folder-open text-4xl text-gray-300 mb-2"></i>
                    <p>Belum ada master dokumen spesifik.</p>
                </div>
            @endforelse
        </div>
    </div>
    </main>

    <x-pemohon.footer></x-pemohon.footer>

    @push('scripts')
    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('block'));
            
            document.getElementById(tabId).classList.remove('hidden');
            document.getElementById(tabId).classList.add('block');
            
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400', 'active-tab-btn');
                el.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });
            
            const activeBtn = document.getElementById('btn-' + tabId);
            activeBtn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            activeBtn.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400', 'active-tab-btn');
        }
    </script>
    @endpush
</x-pemohon-layout>