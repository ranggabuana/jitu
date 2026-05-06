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
            <button onclick="switchTab('tab-general')" id="btn-tab-general" class="tab-btn active-tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                <i class="mdi mdi-application-outline text-lg"></i>
                Pengaturan Umum
            </button>
            <button onclick="switchTab('tab-contact')" id="btn-tab-contact" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                <i class="mdi mdi-contact-mail-outline text-lg"></i>
                Kontak & Sosial Media
            </button>
            <button onclick="switchTab('tab-operational')" id="btn-tab-operational" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                <i class="mdi mdi-clock-check-outline text-lg"></i>
                Hari & Jam Kerja
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
                </div>
            </div>

            <!-- Tab: Operational -->
            <div id="tab-operational" class="tab-content hidden space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30">
                        <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-clock-check text-orange-500"></i>
                            Pengaturan Jam Kerja
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        @php
                            $selectedDays = json_decode($workingHours['work_days'] ?? '["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"]', true);
                            $days = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
                        @endphp
                        
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($days as $key => $label)
                                <label class="flex items-center gap-2 p-3 border border-gray-100 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <input type="checkbox" name="work_days[]" value="{{ $key }}" {{ in_array($key, $selectedDays) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Jam Mulai Kerja</label>
                                <input type="time" name="work_start_time" value="{{ old('work_start_time', $workingHours['work_start_time'] ?? '08:00') }}" class="form-input">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Jam Selesai Kerja</label>
                                <input type="time" name="work_end_time" value="{{ old('work_end_time', $workingHours['work_end_time'] ?? '16:00') }}" class="form-input">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex items-center justify-between">
                        <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-calendar-remove text-red-500"></i>
                            Hari Libur
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

            <!-- Global Submit Button -->
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-4 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all flex items-center gap-3">
                    <i class="mdi mdi-content-save-all text-xl"></i>
                    Simpan Semua Perubahan
                </button>
            </div>
        </form>
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
    </script>
    @endpush
</x-layout>