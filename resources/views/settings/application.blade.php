<x-layout>
    <x-slot:title>Pengaturan Aplikasi</x-slot:title>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <i class="mdi mdi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Pengaturan Aplikasi</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kelola informasi dasar dan operasional aplikasi</p>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center gap-2 text-green-700 dark:text-green-400">
                <i class="mdi mdi-check-circle"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center gap-2 text-red-700 dark:text-red-400">
                <i class="mdi mdi-alert-circle"></i>
                <span class="font-medium">Terjadi kesalahan validasi:</span>
            </div>
            <ul class="mt-2 ml-6 list-disc text-sm text-red-600 dark:text-red-400">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top Navigation Tabs -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
        <nav class="flex space-x-8 min-w-max px-2" aria-label="Tabs">
            <button onclick="switchTab('tab-general')" id="btn-tab-general" class="tab-btn active-tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-all">
                <i class="mdi mdi-application-outline text-lg"></i>
                Pengaturan Umum
            </button>
            <button onclick="switchTab('tab-contact')" id="btn-tab-contact" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-all">
                <i class="mdi mdi-contact-mail-outline text-lg"></i>
                Kontak & Sosial Media
            </button>
            <button onclick="switchTab('tab-operational')" id="btn-tab-operational" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-all">
                <i class="mdi mdi-clock-check-outline text-lg"></i>
                Hari & Jam Kerja
            </button>
            <button onclick="switchTab('tab-dokumen')" id="btn-tab-dokumen" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-all">
                <i class="mdi mdi-file-document-multiple-outline text-lg"></i>
                Dokumen Pemohon
            </button>
        </nav>
    </div>

    <div class="max-w-5xl">
        <form action="{{ route('settings.application.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Tab: General -->
            <div id="tab-general" class="tab-content space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30">
                        <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-application text-blue-500"></i>
                            Identitas Aplikasi
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="app_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Aplikasi</label>
                            <input type="text" name="app_name" id="app_name" value="{{ old('app_name', $generalSettings['app_name'] ?? '') }}" class="form-input" placeholder="Masukkan nama aplikasi">
                        </div>
                        <div>
                            <label for="app_description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Deskripsi Aplikasi</label>
                            <textarea name="app_description" id="app_description" rows="3" class="form-input resize-none">{{ old('app_description', $generalSettings['app_description'] ?? '') }}</textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Logo Aplikasi</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-20 h-20 rounded-lg border-2 border-gray-100 dark:border-gray-700 overflow-hidden bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                                        @if(isset($generalSettings['app_logo']) && file_exists(public_path($generalSettings['app_logo'])))
                                            <img src="{{ asset($generalSettings['app_logo']) }}" class="max-w-full max-h-full object-contain">
                                        @else
                                            <i class="mdi mdi-image-outline text-3xl text-gray-300"></i>
                                        @endif
                                    </div>
                                    <input type="file" name="app_logo" class="text-xs file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Gambar TTE</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-20 h-20 rounded-lg border-2 border-gray-100 dark:border-gray-700 overflow-hidden bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                                        @if(isset($generalSettings['gambar_tte']) && file_exists(public_path($generalSettings['gambar_tte'])))
                                            <img src="{{ asset($generalSettings['gambar_tte']) }}" class="max-w-full max-h-full object-contain">
                                        @else
                                            <i class="mdi mdi-signature-freehand text-3xl text-gray-300"></i>
                                        @endif
                                    </div>
                                    <input type="file" name="gambar_tte" class="text-xs file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Panduan Pendaftaran (PDF/Gambar)</label>
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-lg border-2 border-gray-100 dark:border-gray-700 overflow-hidden bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-center">
                                    @if(isset($generalSettings['panduan_pendaftaran']) && file_exists(public_path($generalSettings['panduan_pendaftaran'])))
                                        <i class="mdi mdi-file-check text-3xl text-green-500"></i>
                                        <p class="text-[8px] mt-1 text-gray-400">Tersedia</p>
                                    @else
                                        <i class="mdi mdi-file-outline text-3xl text-gray-300"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="panduan_pendaftaran" class="text-xs file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
                                    @if(isset($generalSettings['panduan_pendaftaran']))
                                        <a href="{{ asset($generalSettings['panduan_pendaftaran']) }}" target="_blank" class="text-xs text-blue-600 hover:underline mt-2 inline-block">Lihat file saat ini</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Save Button for this tab -->
                    <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold transition-all flex items-center gap-2">
                            <i class="mdi mdi-content-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab: Contact & Social -->
            <div id="tab-contact" class="tab-content hidden space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30">
                        <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-phone-classic text-green-500"></i>
                            Informasi Kontak
                        </h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="whatsapp" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">WhatsApp</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-4 rounded-l-xl border border-r-0 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 font-bold text-sm">
                                    +62
                                </span>
                                <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $contactSettings['whatsapp'] ?? '') }}" class="form-input rounded-l-none" placeholder="8123456xxx">
                            </div>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $contactSettings['email'] ?? '') }}" class="form-input" placeholder="admin@example.com">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Telepon Kantor</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $contactSettings['phone'] ?? '') }}" class="form-input" placeholder="(0286) xxxx">
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Alamat Kantor</label>
                            <input type="text" name="address" id="address" value="{{ old('address', $contactSettings['address'] ?? '') }}" class="form-input" placeholder="Jl. Raya No. 123">
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30">
                        <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-share-variant text-purple-500"></i>
                            Media Sosial
                        </h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="facebook" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Facebook URL</label>
                            <input type="url" name="facebook" id="facebook" value="{{ old('facebook', $socialMediaSettings['facebook'] ?? '') }}" class="form-input">
                        </div>
                        <div>
                            <label for="instagram" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Instagram URL</label>
                            <input type="url" name="instagram" id="instagram" value="{{ old('instagram', $socialMediaSettings['instagram'] ?? '') }}" class="form-input">
                        </div>
                        <div>
                            <label for="youtube" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">YouTube URL</label>
                            <input type="url" name="youtube" id="youtube" value="{{ old('youtube', $socialMediaSettings['youtube'] ?? '') }}" class="form-input">
                        </div>
                        <div>
                            <label for="tiktok" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">TikTok URL</label>
                            <input type="url" name="tiktok" id="tiktok" value="{{ old('tiktok', $socialMediaSettings['tiktok'] ?? '') }}" class="form-input">
                        </div>
                    </div>
                    <!-- Save Button for this tab -->
                    <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold transition-all flex items-center gap-2">
                            <i class="mdi mdi-content-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab: Operational -->
            <div id="tab-operational" class="tab-content hidden space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30">
                        <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-clock-check text-orange-500"></i>
                            Pengaturan Jam Kerja Harian
                        </h2>
                    </div>
                    <div class="p-6">
                        <p class="text-xs text-gray-500 mb-6 italic">Atur jam operasional berbeda untuk setiap hari. Kosongkan checkbox jika hari tersebut adalah hari libur rutin.</p>
                        
                        <div class="space-y-4">
                            @php
                                $workHours = json_decode($workingHours['work_hours'] ?? '[]', true);
                                $days = [
                                    'Monday' => 'Senin',
                                    'Tuesday' => 'Selasa',
                                    'Wednesday' => 'Rabu',
                                    'Thursday' => 'Kamis',
                                    'Friday' => 'Jumat',
                                    'Saturday' => 'Sabtu',
                                    'Sunday' => 'Minggu'
                                ];
                            @endphp
                            
                            <div class="grid grid-cols-12 gap-4 pb-2 border-b border-gray-100 dark:border-gray-700 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                <div class="col-span-4">Hari</div>
                                <div class="col-span-4">Jam Mulai</div>
                                <div class="col-span-4">Jam Selesai</div>
                            </div>

                            @foreach($days as $key => $label)
                                @php
                                    $isActive = isset($workHours[$key]['active']) && $workHours[$key]['active'] == '1';
                                    $startTime = $workHours[$key]['start'] ?? '08:00';
                                    $endTime = $workHours[$key]['end'] ?? '16:00';
                                @endphp
                                <div class="grid grid-cols-12 gap-4 items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="col-span-4">
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="checkbox" name="work_hours[{{ $key }}][active]" value="1" {{ $isActive ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                        </label>
                                    </div>
                                    <div class="col-span-4">
                                        <input type="time" name="work_hours[{{ $key }}][start]" value="{{ $startTime }}" class="form-input py-1 px-3 text-xs">
                                    </div>
                                    <div class="col-span-4">
                                        <input type="time" name="work_hours[{{ $key }}][end]" value="{{ $endTime }}" class="form-input py-1 px-3 text-xs">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Save Button for this tab -->
                    <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold transition-all flex items-center gap-2">
                            <i class="mdi mdi-content-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex items-center justify-between">
                        <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-calendar-remove text-red-500"></i>
                            Hari Libur Nasional / Tanggal Merah
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row gap-4 mb-6">
                            <div class="flex-1">
                                <input type="text" id="holiday_date" class="form-input" placeholder="Pilih tanggal libur...">
                            </div>
                            <div class="flex-1">
                                <input type="text" id="holiday_description" class="form-input" placeholder="Keterangan libur...">
                            </div>
                            <button type="button" id="btn-add-holiday" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold transition-all flex items-center justify-center gap-2">
                                <i class="mdi mdi-plus"></i> Tambah
                            </button>
                        </div>

                        <div class="max-h-64 overflow-y-auto border border-gray-100 dark:border-gray-700 rounded-lg">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 font-bold text-gray-600 dark:text-gray-400">Tanggal</th>
                                        <th class="px-4 py-3 font-bold text-gray-600 dark:text-gray-400">Keterangan</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="holidays-list" class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @forelse($holidays as $holiday)
                                        <tr id="holiday-row-{{ $holiday->id }}">
                                            <td class="px-4 py-3">{{ $holiday->date->format('d M Y') }}</td>
                                            <td class="px-4 py-3 text-gray-500">{{ $holiday->description ?: '-' }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" onclick="deleteHoliday({{ $holiday->id }})" class="text-red-500 hover:text-red-700"><i class="mdi mdi-trash-can-outline text-lg"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="no-holidays"><td colspan="3" class="px-4 py-8 text-center text-gray-400 italic">Belum ada hari libur</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tab: Dokumen Pemohon -->
            <div id="tab-dokumen" class="tab-content hidden space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex justify-between items-center">
                        <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-file-document-multiple text-purple-500"></i>
                            Master Dokumen Pemohon
                        </h2>
                        <button type="button" onclick="openModalDokumen()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-1">
                            <i class="mdi mdi-plus"></i> Tambah Dokumen
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                            <table class="w-full text-left text-sm whitespace-nowrap">
                                <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 font-bold text-gray-600 dark:text-gray-400">Nama Dokumen</th>
                                        <th class="px-4 py-3 font-bold text-gray-600 dark:text-gray-400">Tipe File</th>
                                        <th class="px-4 py-3 font-bold text-gray-600 dark:text-gray-400">Ukuran Maks (KB)</th>
                                        <th class="px-4 py-3 font-bold text-gray-600 dark:text-gray-400">Jenis</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @forelse($masterDokumens ?? [] as $dokumen)
                                        <tr>
                                            <td class="px-4 py-3">{{ $dokumen->nama_dokumen }}</td>
                                            <td class="px-4 py-3"><span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">{{ $dokumen->tipe_data_file }}</span></td>
                                            <td class="px-4 py-3">{{ number_format($dokumen->max_size ?? 2048, 0, ',', '.') }} KB</td>
                                            <td class="px-4 py-3">
                                                @if($dokumen->jenis === 'umum')
                                                    <span class="bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-1 rounded text-xs font-medium">Umum</span>
                                                @else
                                                    <span class="bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 px-2 py-1 rounded text-xs font-medium">Spesifik</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center flex justify-center gap-2">
                                                <button type="button" onclick="editDokumen({{ $dokumen->id }}, '{{ addslashes($dokumen->nama_dokumen) }}', '{{ addslashes($dokumen->tipe_data_file) }}', '{{ $dokumen->max_size ?? 2048 }}', '{{ $dokumen->jenis }}')" class="text-blue-500 hover:text-blue-700">
                                                    <i class="mdi mdi-pencil-outline text-lg"></i>
                                                </button>
                                                <button type="button" onclick="deleteDokumen({{ $dokumen->id }})" class="text-red-500 hover:text-red-700">
                                                    <i class="mdi mdi-trash-can-outline text-lg"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400 italic">Belum ada master dokumen</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <!-- Hidden Form for Deleting Dokumen -->
    <form id="form-delete-dokumen" method="POST" class="hidden">
        @csrf @method('DELETE')
    </form>

    <!-- Modal Form Dokumen Pemohon -->
    <div id="modal-dokumen" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform" id="modal-dokumen-content">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/30 dark:bg-gray-700/30">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white" id="modal-dokumen-title">Tambah Dokumen</h3>
                <button type="button" onclick="closeModalDokumen()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="mdi mdi-close text-xl"></i>
                </button>
            </div>
            <form id="form-dokumen" method="POST" action="{{ route('settings.application.dokumen.store') }}">
                @csrf
                <input type="hidden" name="_method" id="form-dokumen-method" value="POST">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Dokumen</label>
                        <input type="text" name="nama_dokumen" id="dokumen_nama" required class="form-input" placeholder="Cth: KTP, NPWP, Akta Notaris">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tipe File</label>
                        <input type="text" name="tipe_data_file" id="dokumen_tipe" required class="form-input" placeholder="Cth: pdf, jpg, png">
                        <p class="text-xs text-gray-500 mt-1">Pisahkan dengan koma (tanpa spasi jika bisa)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Ukuran Maksimal (KB)</label>
                        <input type="number" name="max_size" id="dokumen_max_size" required class="form-input" placeholder="Cth: 2048" value="2048">
                        <p class="text-xs text-gray-500 mt-1">1024 KB = 1 MB. Standar 2MB (2048).</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Jenis Dokumen</label>
                        <select name="jenis" id="dokumen_jenis" required class="form-input">
                            <option value="umum">Umum (Sering digunakan)</option>
                            <option value="spesifik">Spesifik (Khusus perizinan tertentu)</option>
                        </select>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30 flex justify-end gap-2">
                    <button type="button" onclick="closeModalDokumen()" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            background-color: #ffffff;
            font-size: 0.875rem;
            color: #1f2937;
            transition: all 0.2s;
        }
        .dark .form-input {
            background-color: #111827;
            border-color: #374151;
            color: #f9fafb;
        }
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        .tab-btn {
            transition: all 0.3s;
        }
        .active-tab-btn {
            color: #2563eb !important;
            border-color: #2563eb !important;
        }
        .dark .active-tab-btn {
            color: #60a5fa !important;
            border-color: #3b82f6 !important;
        }
    </style>

    @push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <script>
        function switchTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Show target content
            document.getElementById(tabId).classList.remove('hidden');
            
            // Update buttons
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('active-tab-btn');
                el.classList.add('border-transparent', 'text-gray-500');
            });
            const activeBtn = document.getElementById('btn-' + tabId);
            activeBtn.classList.add('active-tab-btn');
            activeBtn.classList.remove('border-transparent', 'text-gray-500');
            
            // Save last active tab
            localStorage.setItem('active_setting_tab', tabId);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const lastTab = localStorage.getItem('active_setting_tab');
            if (lastTab && document.getElementById(lastTab)) {
                switchTab(lastTab);
            }

            flatpickr("#holiday_date", { locale: "id", dateFormat: "Y-m-d", altInput: true, altFormat: "d F Y", minDate: "today" });

            const btnAddHoliday = document.getElementById('btn-add-holiday');
            btnAddHoliday.addEventListener('click', function() {
                const date = document.getElementById('holiday_date').value;
                const description = document.getElementById('holiday_description').value;
                if (!date) { Swal.fire('Peringatan', 'Pilih tanggal!', 'warning'); return; }

                btnAddHoliday.disabled = true;
                btnAddHoliday.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i>';

                fetch('{{ route('settings.application.holiday.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ date: date, description: description })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) { location.reload(); } 
                    else { Swal.fire('Gagal!', data.message, 'error'); }
                })
                .finally(() => { btnAddHoliday.disabled = false; btnAddHoliday.innerHTML = '<i class="mdi mdi-plus"></i> Tambah'; });
            });
        });

        function deleteHoliday(id) {
            Swal.fire({
                title: 'Hapus hari libur?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ route('settings.application.holiday.delete', ['id' => ':id']) }}`.replace(':id', id), {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(r => r.json()).then(d => d.success ? location.reload() : null);
                }
            });
        }

        // --- Master Dokumen Pemohon Scripts ---
        const modalDokumen = document.getElementById('modal-dokumen');
        const modalDokumenContent = document.getElementById('modal-dokumen-content');
        
        function openModalDokumen() {
            document.getElementById('form-dokumen-method').value = 'POST';
            document.getElementById('form-dokumen').action = '{{ route("settings.application.dokumen.store") }}';
            document.getElementById('modal-dokumen-title').innerText = 'Tambah Dokumen';
            document.getElementById('dokumen_nama').value = '';
            document.getElementById('dokumen_tipe').value = '';
            document.getElementById('dokumen_max_size').value = '2048';
            document.getElementById('dokumen_jenis').value = 'umum';
            
            modalDokumen.classList.remove('hidden');
            modalDokumen.classList.add('flex');
            setTimeout(() => {
                modalDokumenContent.classList.remove('scale-95');
                modalDokumenContent.classList.add('scale-100');
            }, 10);
        }
        
        function closeModalDokumen() {
            modalDokumenContent.classList.remove('scale-100');
            modalDokumenContent.classList.add('scale-95');
            setTimeout(() => {
                modalDokumen.classList.remove('flex');
                modalDokumen.classList.add('hidden');
            }, 200);
        }
        
        function editDokumen(id, nama, tipe, maxSize, jenis) {
            document.getElementById('form-dokumen-method').value = 'PUT';
            document.getElementById('form-dokumen').action = '{{ route("settings.application.dokumen.update", ["id" => ":id"]) }}'.replace(':id', id);
            document.getElementById('modal-dokumen-title').innerText = 'Edit Dokumen';
            document.getElementById('dokumen_nama').value = nama;
            document.getElementById('dokumen_tipe').value = tipe;
            document.getElementById('dokumen_max_size').value = maxSize;
            document.getElementById('dokumen_jenis').value = jenis;
            
            modalDokumen.classList.remove('hidden');
            modalDokumen.classList.add('flex');
            setTimeout(() => {
                modalDokumenContent.classList.remove('scale-95');
                modalDokumenContent.classList.add('scale-100');
            }, 10);
        }
        
        function deleteDokumen(id) {
            Swal.fire({
                title: 'Hapus Master Dokumen?',
                text: "Dokumen yang terhubung mungkin akan terdampak.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('form-delete-dokumen');
                    form.action = '{{ route("settings.application.dokumen.delete", ["id" => ":id"]) }}'.replace(':id', id);
                    form.submit();
                }
            });
        }
    </script>
    @endpush
</x-layout>
