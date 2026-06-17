<x-layout>
    <x-slot:title>Kelola Formulir - {{ $perijinan->nama_perijinan }}</x-slot:title>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('perijinan.index') }}"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                <i class="mdi mdi-arrow-left text-xl"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Kelola Formulir</h1>
            </div>
        </div>

        <!-- Permit Info Card -->
        <div
            class="mt-4 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl border border-indigo-100 dark:border-indigo-800 p-5">
            <div class="flex items-start gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-500/30">
                    <i class="mdi mdi-file-document-outline text-white text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white truncate">
                        {{ $perijinan->nama_perijinan }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Formulir ini digunakan untuk mengajukan
                        permohonan perijinan tersebut</p>
                    @if ($perijinan->dasar_hukum)
                        <div class="mt-3 flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <i class="mdi mdi-gavel mt-0.5"></i>
                            <span class="line-clamp-2">{!! strip_tags($perijinan->dasar_hukum) !!}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif

    @if (session('error'))
        <meta name="error-message" content="{{ session('error') }}">
    @endif

    <div id="form-builder-app">
        <!-- Tabs Navigation -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                @if(auth()->user()->isAdmin())
                    <li class="mr-2">
                        <button onclick="switchTab('global')" id="tab-btn-global" class="tab-btn inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group text-indigo-600 border-indigo-600 dark:text-indigo-500 dark:border-indigo-500">
                            <i id="tab-icon-global" class="mdi mdi-earth mr-2 text-lg text-indigo-600 dark:text-indigo-500"></i>
                            Global Form
                        </button>
                    </li>
                @endif
                @if(auth()->user()->isAdmin() || auth()->user()->isOperatorOpd())
                <li class="mr-2">
                    <button onclick="switchTab('rekom')" id="tab-btn-rekom" class="tab-btn inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group {{ auth()->user()->isAdmin() ? 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' : 'text-indigo-600 border-indigo-600 dark:text-indigo-500 dark:border-indigo-500' }}">
                        <i id="tab-icon-rekom" class="mdi mdi-file-document-outline mr-2 text-lg {{ auth()->user()->isAdmin() ? 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300' : 'text-indigo-600 dark:text-indigo-500' }}"></i>
                        Rekom Form
                    </button>
                </li>
                @endif
                @if(auth()->user()->isAdmin() || auth()->user()->isVerifikator())
                <li class="mr-2">
                    <button onclick="switchTab('izin')" id="tab-btn-izin" class="tab-btn inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group {{ auth()->user()->isAdmin() ? 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' : 'text-indigo-600 border-indigo-600 dark:text-indigo-500 dark:border-indigo-500' }}">
                        <i id="tab-icon-izin" class="mdi mdi-file-certificate-outline mr-2 text-lg {{ auth()->user()->isAdmin() ? 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300' : 'text-indigo-600 dark:text-indigo-500' }}"></i>
                        Izin Form
                    </button>
                </li>
                @endif
            </ul>
        </div>

        <!-- Sequence Number Configuration (Visible only for Rekom and Izin) -->
        <div id="sequence-config-container" class="mb-6 hidden">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/30 overflow-hidden">
                <div class="px-6 py-4 bg-indigo-50/50 dark:bg-indigo-900/10 border-b border-indigo-100 dark:border-indigo-900/30 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                        <i class="mdi mdi-cog-outline text-indigo-600 dark:text-indigo-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white">Pengaturan Tambahan Form <span id="config-header-type"></span></h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400" id="config-header-subtitle">Atur nomor surat dan panduan pengisian untuk formulir ini</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="w-full">
                        @if(auth()->user()->isAdmin() || auth()->user()->isOperatorOpd())
                            <div id="rekom-number-config" class="hidden">
                                <div class="flex flex-col md:flex-row items-end gap-4 w-full">
                                    <div class="w-full md:w-48">
                                        <label class="block text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-1.5">
                                            Nomor Urut Selanjutnya
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="mdi mdi-numeric text-gray-400"></i>
                                            </div>
                                            <input type="number" form="template-form" name="next_nomor_rekom" id="next_nomor_rekom"
                                                value="{{ $perijinan->next_nomor_rekom ?? 1 }}" min="1"
                                                class="w-full pl-9 pr-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold"
                                                placeholder="1">
                                        </div>
                                    </div>
                                    <div class="flex-grow w-full">
                                        <label class="block text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-1.5">
                                            Keterangan Panduan (Informasi untuk Petugas)
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="mdi mdi-information-outline text-gray-400"></i>
                                            </div>
                                            <input type="text" form="template-form" name="keterangan_rekom" 
                                                value="{{ $perijinan->keterangan_rekom }}"
                                                class="w-full pl-9 pr-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm"
                                                placeholder="Masukkan informasi panduan pengisian untuk Operator OPD...">
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <button type="submit" form="template-form"
                                            class="h-[42px] px-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl border border-indigo-600 flex items-center gap-2 transition-all shadow-lg shadow-indigo-200 dark:shadow-none active:scale-95">
                                            <i class="mdi mdi-content-save-check text-lg"></i>
                                            <span class="text-xs font-black uppercase tracking-widest">Simpan</span>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-2 italic ml-1">Keterangan ini akan tampil sebagai panduan di halaman validasi Operator OPD.</p>
                            </div>
                        @endif

                        @if(auth()->user()->isAdmin() || auth()->user()->role === 'verifikator')
                            <div id="izin-number-config" class="hidden">
                                <div class="flex flex-col md:flex-row items-end gap-4 w-full">
                                    <div class="w-full md:w-48">
                                        <label class="block text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-1.5">
                                            Nomor Urut Selanjutnya
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="mdi mdi-numeric text-gray-400"></i>
                                            </div>
                                            <input type="number" form="template-form" name="next_nomor_izin" id="next_nomor_izin"
                                                value="{{ $perijinan->next_nomor_izin ?? 1 }}" min="1"
                                                class="w-full pl-9 pr-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold"
                                                placeholder="1">
                                        </div>
                                    </div>
                                    <div class="flex-grow w-full">
                                        <label class="block text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-1.5">
                                            Keterangan Panduan (Informasi untuk Verifikator)
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="mdi mdi-information-outline text-gray-400"></i>
                                            </div>
                                            <input type="text" form="template-form" name="keterangan_izin" 
                                                value="{{ $perijinan->keterangan_izin }}"
                                                class="w-full pl-9 pr-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm"
                                                placeholder="Masukkan informasi panduan pengisian untuk Verifikator...">
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <button type="submit" form="template-form"
                                            class="h-[42px] px-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl border border-indigo-600 flex items-center gap-2 transition-all shadow-lg shadow-indigo-200 dark:shadow-none active:scale-95">
                                            <i class="mdi mdi-content-save-check text-lg"></i>
                                            <span class="text-xs font-black uppercase tracking-widest">Simpan</span>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-2 italic ml-1">Keterangan ini akan tampil sebagai panduan di halaman validasi Verifikator.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Tambah Field -->
            <div class="lg:col-span-1">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 sticky top-6">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                                <i class="mdi mdi-plus-circle text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-medium text-gray-800 dark:text-white">Tambah Field</h2>
                                <p class="text-xs text-gray-500 dark:text-gray-400" id="add-field-subtitle">Tambah field baru ke Global Form</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('perijinan.form-field.store', $perijinan->id) }}" method="POST"
                        class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="form_type" id="create_form_type" value="global">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Label <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="text" name="label" id="label" required
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="Contoh: Nama Lengkap" oninput="generateFieldName()">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Field Name <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" name="name" id="name" required readonly
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                placeholder="Akan dibuat otomatis">
                            <button type="button" onclick="toggleFieldNameEdit()" 
                                class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-lg transition-colors"
                                title="Edit manual">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 flex items-center gap-1">
                            <i class="mdi mdi-information-outline"></i>
                            Dibuat otomatis dari label, gunakan huruf kecil dan underscore
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Tipe Input <span class="text-red-500 ml-1">*</span>
                        </label>
                        <select name="type" id="type" required onchange="toggleOptions()"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            <option value="text">Text (Single Line)</option>
                            <option value="textarea">Textarea (Multi Line)</option>
                            <option value="number">Number</option>
                            <option value="date">Date</option>
                            <option value="email">Email</option>
                            <option value="phone">Phone</option>
                            <option value="select">Dropdown Select</option>
                            <option value="radio">Radio Button</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="file">File Upload</option>
                        </select>
                    </div>

                    <!-- File Configuration (shown when type is file) -->
                    <div id="file_config_container" class="hidden bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 space-y-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                                <i class="mdi mdi-file-upload text-indigo-600 dark:text-indigo-400 text-sm"></i>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Konfigurasi File</h4>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <i class="mdi mdi-file-document text-gray-500 dark:text-gray-400 mr-1"></i>
                                Tipe File yang Diizinkan
                            </label>
                            <input type="text" name="file_types" id="file_types"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                placeholder="pdf, doc, docx, xls, xlsx">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                                Pisahkan dengan koma. Contoh: <code class="bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">pdf, jpg, png</code>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <i class="mdi mdi-weight text-gray-500 dark:text-gray-400 mr-1"></i>
                                Ukuran File Maksimal (KB)
                            </label>
                            <input type="number" name="max_file_size" id="max_file_size"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                placeholder="2048">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                                Contoh: <code class="bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">2048</code> = 2MB, <code class="bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">5120</code> = 5MB
                            </p>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="button" onclick="setPresetFileTypes(['pdf'])"
                                class="px-3 py-1.5 text-xs bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-md hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors font-medium">
                                <i class="mdi mdi-file-pdf"></i> PDF Only
                            </button>
                            <button type="button" onclick="setPresetFileTypes(['jpg', 'jpeg', 'png'])"
                                class="px-3 py-1.5 text-xs bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors font-medium">
                                <i class="mdi mdi-image"></i> Images
                            </button>
                            <button type="button" onclick="setPresetFileTypes(['pdf', 'doc', 'docx'])"
                                class="px-3 py-1.5 text-xs bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-md hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors font-medium">
                                <i class="mdi mdi-file-document"></i> Documents
                            </button>
                        </div>
                    </div>

                    <!-- Options Input (for select/radio/checkbox) -->
                    <div id="options_container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Pilihan Options
                        </label>
                        <div id="options_list" class="space-y-2">
                            <div class="flex gap-2">
                                <input type="text" name="options[]"
                                    class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                                    placeholder="Opsi 1">
                                <button type="button" onclick="addOption()"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg transition-colors">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Placeholder
                        </label>
                        <input type="text" name="placeholder" id="placeholder"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="Contoh: Masukkan nama Anda">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Help Text
                        </label>
                        <input type="text" name="help_text" id="help_text"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="Contoh: Isi sesuai dengan KTP">
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_required" id="is_required" value="1"
                                class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Wajib diisi</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked
                                class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg transition-colors font-medium flex items-center justify-center gap-2 shadow-sm">
                        <i class="mdi mdi-content-save"></i>
                        <span>Simpan Field</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Daftar Field -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
                            <i class="mdi mdi-format-list-bulleted text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-medium text-gray-800 dark:text-white">
                                Daftar Field <span id="daftar-field-tab-name">Global Form</span>
                                @if(auth()->user()->isAdmin())
                                    <span class="text-gray-500 dark:text-gray-400 font-normal tab-count" id="count-global">({{ $perijinan->formFields->whereIn('form_type', ['global', null])->count() }})</span>
                                @endif
                                @if(auth()->user()->isAdmin() || auth()->user()->isOperatorOpd())
                                    <span class="text-gray-500 dark:text-gray-400 font-normal tab-count hidden" id="count-rekom">({{ $perijinan->formFields->where('form_type', 'rekom')->count() }})</span>
                                @endif
                                @if(auth()->user()->isAdmin() || auth()->user()->isVerifikator())
                                    <span class="text-gray-500 dark:text-gray-400 font-normal tab-count hidden" id="count-izin">({{ $perijinan->formFields->where('form_type', 'izin') ->count() }})</span>
                                @endif
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Urutkan field dengan drag & drop</p>
                        </div>
                    </div>
                </div>

                @if ($perijinan->formFields->count() > 0)
                    @foreach(['global', 'rekom', 'izin'] as $type)
                        @php
                            $canSeeType = auth()->user()->isAdmin() || 
                                          ($type === 'rekom' && auth()->user()->isOperatorOpd()) ||
                                          ($type === 'izin' && auth()->user()->isVerifikator());
                        @endphp
                        @if($canSeeType)
                        <div id="fields_list_{{ $type }}" class="fields-container divide-y divide-gray-100 dark:divide-gray-700 {{ $type === (auth()->user()->isOperatorOpd() ? 'rekom' : (auth()->user()->isVerifikator() ? 'izin' : 'global')) ? '' : 'hidden' }}">
                            @foreach ($perijinan->formFields->where('form_type', $type === 'global' ? null : $type)->union($perijinan->formFields->where('form_type', $type)) as $field)
                                <div class="field-item p-5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                    data-field-id="{{ $field->id }}" data-form-type="{{ $type }}">
                                    <div class="flex items-center gap-4">
                                    <!-- Drag Handle -->
                                    <button
                                        class="cursor-move text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <i class="mdi mdi-drag-vertical text-xl"></i>
                                    </button>

                                    <!-- Order Badge -->
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center flex-shrink-0 shadow-md shadow-indigo-500/30">
                                        <span class="text-white font-bold text-lg">{{ $field->order }}</span>
                                    </div>

                                    <!-- Field Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="font-medium text-gray-900 dark:text-white">
                                                {{ $field->label }}
                                            </span>
                                            @if ($field->is_required)
                                                <span
                                                    class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-xs px-2 py-1 rounded-md font-medium">
                                                    <i class="mdi mdi-asterisk"></i> Wajib diisi
                                                </span>
                                            @endif
                                            @if ($field->opd_id)
                                                <span
                                                    class="bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 text-xs px-2 py-1 rounded-md font-bold uppercase tracking-tighter">
                                                    <i class="mdi mdi-office-building"></i> {{ $field->opd->kode_opd ?? 'OPD' }}
                                                </span>
                                            @endif
                                            @if ($field->is_active)
                                                <span
                                                    class="bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-xs px-2 py-1 rounded-md font-medium">
                                                    <i class="mdi mdi-check-circle"></i> Active
                                                </span>
                                            @else
                                                <span
                                                    class="bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs px-2 py-1 rounded-md font-medium">
                                                    <i class="mdi mdi-pause-circle"></i> Inactive
                                                </span>
                                            @endif
                                        </div>
                                        <div
                                            class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                            <span
                                                class="flex items-center gap-1.5 bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-md">
                                                <i class="mdi mdi-code-tags text-xs"></i>
                                                <code class="font-mono">{{ $field->name }}</code>
                                            </span>
                                            <span
                                                class="flex items-center gap-1.5 bg-indigo-50 dark:bg-indigo-900/20 px-2.5 py-1 rounded-md text-indigo-600 dark:text-indigo-400">
                                                <i class="mdi mdi-shape-outline"></i>
                                                {{ $field->type }}
                                            </span>
                                            @if($field->type === 'file')
                                                @if($field->file_types)
                                                    <span
                                                        class="flex items-center gap-1.5 bg-orange-50 dark:bg-orange-900/20 px-2.5 py-1 rounded-md text-orange-600 dark:text-orange-400">
                                                        <i class="mdi mdi-file-document-outline"></i>
                                                        <span>{{ $field->file_types }}</span>
                                                    </span>
                                                @endif
                                                @if($field->max_file_size)
                                                    <span
                                                        class="flex items-center gap-1.5 bg-orange-50 dark:bg-orange-900/20 px-2.5 py-1 rounded-md text-orange-600 dark:text-orange-400">
                                                        <i class="mdi mdi-weight"></i>
                                                        <span>Max: {{ number_format($field->max_file_size / 1024, 1) }} MB</span>
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center gap-2">
                                        @php
                                            $canManageField = auth()->user()->isAdmin() || 
                                                             (auth()->user()->isOperatorOpd() && $field->opd_id === auth()->user()->opd_id) ||
                                                             (auth()->user()->isVerifikator() && $field->form_type === 'izin');
                                        @endphp
                                        @if($canManageField)
                                            <button onclick="editField({{ $field->id }}, {{ json_encode($field) }})"
                                                class="text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 p-2 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors"
                                                title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>
                                            <form
                                                action="{{ route('perijinan.form-field.delete', [$perijinan->id, $field->id]) }}"
                                                method="POST" class="delete-form inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors btn-delete"
                                                    data-action="{{ route('perijinan.form-field.delete', [$perijinan->id, $field->id]) }}"
                                                    title="Hapus">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] text-gray-400 italic px-2">Hanya Lihat</span>
                                        @endif
                                    </div>
                                </div>
                                </div>
                                @endforeach
                                </div>
                                @endif
                                @endforeach
                                @else
                                <div class="p-12 text-center">
                                <div
                                class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                                <i class="mdi mdi-form-select text-3xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 font-medium">Belum ada field formulir</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Tambahkan field pertama Anda
                                menggunakan form di samping</p>
                                </div>
                                @endif

                                <!-- OPD Field Customization Status (Admin Only) -->
                                @if(auth()->user()->isAdmin())
                                    @php
                                        $involvedOpds = $perijinan->activeValidationFlows
                                            ->whereIn('role', ['operator_opd', 'kepala_opd'])
                                            ->pluck('assignedUser.opd')
                                            ->filter()
                                            ->unique('id');
                                    @endphp
                                    @if($involvedOpds->count() > 0)
                                        <div id="opd-field-status" class="hidden px-6 py-4 bg-indigo-50/50 dark:bg-indigo-900/20 border-t border-gray-100 dark:border-gray-700 mt-4 rounded-b-xl">
                                            <div class="flex items-center gap-2 mb-3">
                                                <i class="mdi mdi-office-building text-indigo-500 text-lg"></i>
                                                <span class="text-xs font-bold text-gray-800 dark:text-gray-200">Status Kustomisasi Field per OPD</span>
                                            </div>
                                            <div class="flex flex-col gap-2">
                                                @foreach($involvedOpds as $opd)
                                                    @php 
                                                        $count = $perijinan->formFields()->where('opd_id', $opd->id)->count(); 
                                                        $hasCustom = $count > 0;
                                                    @endphp
                                                    <div class="flex items-center justify-between bg-white dark:bg-gray-800 px-3 py-2 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm">
                                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $opd->nama_opd }}</span>
                                                        @if($hasCustom)
                                                            <span class="inline-flex items-center gap-1 text-[10px] font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 rounded-md">
                                                                {{ $count }} Field Kustom
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-400 italic">
                                                                Default Admin
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                </div>
                                </div>
                                </div>

        <!-- Template Editor Container (Hidden by default, shown on rekom and izin tabs) -->
        <div id="template-editor-container" class="mt-6 hidden">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <form action="{{ route('perijinan.templates.update', $perijinan->id) }}" method="POST" id="template-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <i class="mdi mdi-file-document-edit text-blue-500"></i>
                                Pengaturan Template Surat <span id="template-editor-title">Rekom</span>
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Edit langsung seperti menggunakan pengolah kata.
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="togglePlaceholderGuide()" id="btn-toggle-guide"
                                class="flex items-center gap-2 text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 border border-blue-200 dark:border-blue-800 rounded-lg px-3 py-2 transition-all">
                                <i class="mdi mdi-tag-text-outline text-base"></i>
                                Lihat Variabel Dinamis
                            </button>
                        </div>
                    </div>

                    <div id="placeholder-guide" class="hidden border-b border-blue-100 dark:border-blue-900/50 bg-gradient-to-r from-blue-50 to-indigo-50/50 dark:from-blue-950/30 dark:to-indigo-950/20 px-6 py-4">
                        <p class="text-xs text-blue-700 dark:text-blue-300 mb-3 leading-relaxed font-medium">
                            <i class="mdi mdi-information-outline mr-1"></i>
                            Ketikkan kode berikut di dalam dokumen. Kode ini akan <strong>diganti otomatis</strong> dengan data riil pemohon saat surat dicetak:
                        </p>
                        <div class="space-y-4">
                            <!-- Variabel Data Pemohon -->
                            <div>
                                <span class="text-[10px] uppercase tracking-wider font-bold text-blue-800 dark:text-blue-300 block mb-2">Variabel Data Pemohon (Tabel Users & Wilayah):</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach([
                                        '${NAMA_PEMOHON}' => 'Nama Lengkap',
                                        '${NIK}' => 'NIK (KTP)',
                                        '${USERNAME}' => 'Username',
                                        '${EMAIL}' => 'Email',
                                        '${NO_HP}' => 'No. Telepon',
                                        '${PEKERJAAN}' => 'Pekerjaan',
                                        '${NAMA_PERUSAHAAN}' => 'Nama Perusahaan',
                                        '${NPWP}' => 'NPWP',
                                        '${ALAMAT_KTP}' => 'Alamat Sesuai KTP',
                                        '${ALAMAT_DOMISILI}' => 'Alamat Domisili',
                                        '${PROVINSI}' => 'Provinsi',
                                        '${KABUPATEN}' => 'Kabupaten/Kota',
                                        '${KECAMATAN}' => 'Kecamatan',
                                        '${KELURAHAN}' => 'Kelurahan/Desa',
                                        '${ALAMAT_LENGKAP}' => 'Alamat Lengkap (Gabungan)',
                                        '${STATUS_PEMOHON}' => 'Status Pemohon',
                                        '${ROLE}' => 'Role User',
                                        '${STATUS_USER}' => 'Status Akun User',
                                        '${OPD_USER}' => 'OPD Asal User',
                                    ] as $code => $label)
                                    <button type="button"
                                        onclick="insertPlaceholder('{{ $code }}')"
                                        title="{{ $label }}"
                                        class="inline-flex items-center gap-1.5 bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-300 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-700 hover:border-blue-600 rounded-lg px-2.5 py-1.5 text-[11px] font-mono font-bold transition-all shadow-sm">
                                        <i class="mdi mdi-plus text-xs"></i>{{ $code }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Variabel Dasar (Sistem) -->
                            <div>
                                <span class="text-[10px] uppercase tracking-wider font-bold text-amber-800 dark:text-amber-300 block mb-2">Variabel Dasar (Sistem):</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach([
                                        '${NAMA_IZIN}' => 'Jenis Izin',
                                        '${TANGGAL}' => 'Tanggal Hari Ini (Lengkap)',
                                        '${NO_REGISTRASI}' => 'Nomor Registrasi Permohonan',
                                        '${NOMOR_SURAT}' => 'Nomor Surat Lengkap (Format: KODE/NO/OPD/TAHUN)',
                                        '${MASA_AKTIF}' => 'Masa Aktif Izin',
                                        '${LOGO_KABUPATEN}' => 'Logo Kabupaten (Header)',
                                        '${GAMBAR_TTE}' => 'Gambar TTE (Tanda Tangan Elektronik)',
                                        '${QRCODE}' => 'QR Code (Scan untuk Verifikasi Izin)',
                                        ] as $code => $label)
                                    <button type="button"
                                        onclick="insertPlaceholder('{{ $code }}')"
                                        title="{{ $label }}"
                                        class="inline-flex items-center gap-1.5 bg-white dark:bg-gray-800 border border-amber-200 dark:border-amber-700 text-amber-700 dark:text-amber-300 hover:bg-amber-600 hover:text-white dark:hover:bg-amber-700 hover:border-amber-600 rounded-lg px-2.5 py-1.5 text-[11px] font-mono font-bold transition-all shadow-sm">
                                        <i class="mdi mdi-plus text-xs"></i>{{ $code }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Variabel Global -->
                            <div class="dynamic-var-section" id="var-section-global">
                                <span class="text-[10px] uppercase tracking-wider font-bold text-emerald-800 dark:text-emerald-300 block mb-2">Variabel dari Global Form:</span>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($perijinan->formFields->where('form_type', 'global') as $field)
                                        @php $varName = strtoupper(str_replace(' ', '_', $field->label)); @endphp
                                        <button type="button"
                                            onclick="insertPlaceholder('{{ '${' . $varName . '}' }}')"
                                            title="{{ $field->label }}"
                                            class="inline-flex items-center gap-1.5 bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-700 hover:border-emerald-600 rounded-lg px-2.5 py-1.5 text-[11px] font-mono font-bold transition-all shadow-sm">
                                            <i class="mdi mdi-plus text-xs"></i>{{ '${' . $varName . '}' }}
                                        </button>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">Belum ada field di Global Form</span>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Variabel Khusus Rekom -->
                            <div class="dynamic-var-section hidden" id="var-section-rekom">
                                <span class="text-[10px] uppercase tracking-wider font-bold text-purple-800 dark:text-purple-300 block mb-2">Variabel Khusus Rekom Form:</span>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($perijinan->formFields->where('form_type', 'rekom') as $field)
                                        @php $varName = strtoupper(str_replace(' ', '_', $field->label)); @endphp
                                        <button type="button"
                                            onclick="insertPlaceholder('{{ '${' . $varName . '}' }}')"
                                            title="{{ $field->label }}"
                                            class="inline-flex items-center gap-1.5 bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-700 text-purple-700 dark:text-purple-300 hover:bg-purple-600 hover:text-white dark:hover:bg-purple-700 hover:border-purple-600 rounded-lg px-2.5 py-1.5 text-[11px] font-mono font-bold transition-all shadow-sm">
                                            <i class="mdi mdi-plus text-xs"></i>{{ '${' . $varName . '}' }}
                                        </button>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">Belum ada field di Rekom Form</span>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Variabel Khusus Izin -->
                            <div class="dynamic-var-section hidden" id="var-section-izin">
                                <span class="text-[10px] uppercase tracking-wider font-bold text-indigo-800 dark:text-indigo-300 block mb-2">Variabel Khusus Izin Form:</span>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($perijinan->formFields->where('form_type', 'izin') as $field)
                                        @php $varName = strtoupper(str_replace(' ', '_', $field->label)); @endphp
                                        <button type="button"
                                            onclick="insertPlaceholder('{{ '${' . $varName . '}' }}')"
                                            title="{{ $field->label }}"
                                            class="inline-flex items-center gap-1.5 bg-white dark:bg-gray-800 border border-indigo-200 dark:border-indigo-700 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-700 hover:border-indigo-600 rounded-lg px-2.5 py-1.5 text-[11px] font-mono font-bold transition-all shadow-sm">
                                            <i class="mdi mdi-plus text-xs"></i>{{ '${' . $varName . '}' }}
                                        </button>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">Belum ada field di Izin Form</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- OPD Template Customization Status (Admin Only) -->
                        @if(auth()->user()->isAdmin())
                            @php
                                // Ensure $involvedOpds is available (re-calculate if needed, or it's from the top @php block)
                                $involvedOpds = $perijinan->activeValidationFlows
                                    ->whereIn('role', ['operator_opd', 'kepala_opd'])
                                    ->pluck('assignedUser.opd')
                                    ->filter()
                                    ->unique('id');
                            @endphp
                            @if($involvedOpds->count() > 0)
                                <div id="opd-template-status" class="hidden mb-6">
                                    <label class="block text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-3">Kustomisasi Template per OPD</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($involvedOpds as $opd)
                                            @php
                                                $config = $perijinan->opdConfigs->where('opd_id', $opd->id)->first();
                                                $hasCustomTemplate = $config && $config->template_surat_rekom;
                                            @endphp
                                            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800">
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-bold text-gray-800 dark:text-white">{{ $opd->nama_opd }}</span>
                                                    <span class="text-[9px] text-gray-500">{{ $opd->kode_opd }}</span>
                                                </div>
                                                @if($hasCustomTemplate)
                                                    <div class="flex items-center gap-2">
                                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[9px] font-black uppercase">Kustom</span>
                                                        <a href="{{ route('perijinan.templates.download', ['id' => $perijinan->id, 'type' => 'rekom', 'opd_id' => $opd->id]) }}" class="text-gray-400 hover:text-indigo-600 transition-colors">
                                                            <i class="mdi mdi-download text-base"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-400 text-[9px] font-bold uppercase">Default Admin</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if(auth()->user()->isAdmin() || auth()->user()->isOperatorOpd())
                            <div id="tpl-rekom-container" class="hidden">
                                
                                @if(auth()->user()->isOperatorOpd())
                                    <!-- Bagian 1: Template Admin (Read Only) -->
                                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                        <label class="block text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">
                                            Template Acuan (Dari Admin)
                                        </label>
                                        @if($perijinan->template_surat_rekom && Str::endsWith($perijinan->template_surat_rekom, '.docx'))
                                            <div class="flex items-center justify-between bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 shadow-sm">
                                                <p class="text-sm text-gray-700 dark:text-gray-300 font-medium flex items-center">
                                                    <i class="mdi mdi-file-document-outline text-gray-400 mr-2 text-lg"></i> 
                                                    {{ basename($perijinan->template_surat_rekom) }}
                                                </p>
                                                <a href="{{ route('perijinan.templates.download', ['id' => $perijinan->id, 'type' => 'rekom', 'force_global' => 1]) }}" class="flex items-center gap-1.5 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded transition-colors font-semibold">
                                                    <i class="mdi mdi-download"></i> Unduh Acuan
                                                </a>
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500 italic">Admin belum mengunggah template acuan.</p>
                                        @endif
                                    </div>

                                    <!-- Bagian 2: Template Kustom OPD -->
                                    <div class="mb-4 p-4 bg-indigo-50/30 dark:bg-indigo-900/10 rounded-xl border border-indigo-100 dark:border-indigo-900/30">
                                        <label class="block text-sm font-bold text-indigo-900 dark:text-indigo-100 mb-1">
                                            Template Kustom OPD Anda
                                        </label>
                                        <p class="text-xs text-indigo-600/80 dark:text-indigo-400/80 mb-3">Unggah file .docx di sini jika OPD Anda memiliki format surat rekomendasi yang berbeda dengan acuan Admin.</p>
                                        
                                        <input type="file" name="file_template_rekom" accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document" 
                                               class="w-full px-4 py-2 border border-indigo-200 dark:border-indigo-800 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 mb-3 focus:ring-indigo-500 focus:border-indigo-500">
                                        
                                        @if(isset($opdConfig) && $opdConfig->template_surat_rekom)
                                            <div class="flex items-center justify-between bg-indigo-100 dark:bg-indigo-900/40 border border-indigo-200 dark:border-indigo-800 rounded-lg p-3">
                                                <p class="text-sm text-indigo-700 dark:text-indigo-400 font-bold flex items-center">
                                                    <i class="mdi mdi-check-decagram mr-2 text-lg"></i> 
                                                    {{ basename($opdConfig->template_surat_rekom) }}
                                                </p>
                                                <a href="{{ route('perijinan.templates.download', ['id' => $perijinan->id, 'type' => 'rekom']) }}" class="flex items-center gap-1.5 text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded transition-colors font-semibold shadow-sm">
                                                    <i class="mdi mdi-download"></i> Unduh Kustom
                                                </a>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2 text-xs text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20 p-2 rounded-lg border border-orange-100 dark:border-orange-900/30">
                                                <i class="mdi mdi-information"></i> Saat ini menggunakan template acuan dari Admin.
                                            </div>
                                        @endif
                                    </div>

                                @else
                                    <!-- Tampilan untuk Admin -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Upload Template Surat Rekomendasi (.docx) (Global/Acuan)
                                        </label>
                                        <input type="file" name="file_template_rekom" accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document" 
                                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                                        
                                        @if($perijinan->template_surat_rekom && Str::endsWith($perijinan->template_surat_rekom, '.docx'))
                                            <div class="mt-2 flex items-center justify-between bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                                                <p class="text-sm text-green-700 dark:text-green-400 font-medium">
                                                    <i class="mdi mdi-check-circle mr-1"></i> File template aktif: {{ basename($perijinan->template_surat_rekom) }}
                                                </p>
                                                <a href="{{ route('perijinan.templates.download', ['id' => $perijinan->id, 'type' => 'rekom']) }}" class="flex items-center gap-1.5 text-xs bg-white dark:bg-gray-800 border border-green-300 dark:border-green-700 text-green-700 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-800 px-3 py-1.5 rounded transition-colors shadow-sm font-semibold">
                                                    <i class="mdi mdi-download"></i> Unduh Template
                                                </a>
                                            </div>
                                        @endif
                                        <p class="mt-2 text-xs text-gray-500">
                                            <i class="mdi mdi-information"></i> Buat surat menggunakan Microsoft Word, gunakan variabel dengan format <code>${NAMA_VARIABEL}</code>, lalu unggah ke sini.
                                        </p>
                                    </div>
                                @endif
                                </div>
                                @endif

                                @if(auth()->user()->isAdmin() || auth()->user()->role === 'verifikator')
                            <div id="tpl-izin-container" class="hidden">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Upload Template Surat Izin (.docx)
                                    </label>
                                    <input type="file" name="file_template_izin" accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document" 
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                                    @if($perijinan->template_surat_izin && Str::endsWith($perijinan->template_surat_izin, '.docx'))
                                        <div class="mt-2 flex items-center justify-between bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                                            <p class="text-sm text-green-700 dark:text-green-400 font-medium">
                                                <i class="mdi mdi-check-circle mr-1"></i> File template aktif: {{ basename($perijinan->template_surat_izin) }}
                                            </p>
                                            <a href="{{ route('perijinan.templates.download', ['id' => $perijinan->id, 'type' => 'izin']) }}" class="flex items-center gap-1.5 text-xs bg-white dark:bg-gray-800 border border-green-300 dark:border-green-700 text-green-700 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-800 px-3 py-1.5 rounded transition-colors shadow-sm font-semibold">
                                                <i class="mdi mdi-download"></i> Unduh Template
                                            </a>
                                        </div>
                                    @endif
                                    <p class="mt-2 text-xs text-gray-500">
                                        <i class="mdi mdi-information"></i> Buat surat menggunakan Microsoft Word, gunakan variabel dengan format <code>${NAMA_VARIABEL}</code>, lalu unggah ke sini.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold transition-all flex items-center gap-2 shadow-sm">
                            <i class="mdi mdi-content-save"></i> Simpan Template
                        </button>
                    </div>
                </form>
            </div>
        </div>

    <!-- Edit Modal -->
    <div id="editModal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div
                class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between sticky top-0 bg-white dark:bg-gray-800 z-10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                        <i class="mdi mdi-pencil text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-medium text-gray-800 dark:text-white">Edit Field</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Perbarui informasi field</p>
                    </div>
                </div>
                <button onclick="closeEditModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="mdi mdi-close text-xl"></i>
                </button>
            </div>
            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_type" id="edit_form_type">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Label <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="text" name="label" id="edit_label" required
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Field Name <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="text" name="name" id="edit_name" required
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Tipe Input <span class="text-red-500 ml-1">*</span>
                    </label>
                    <select name="type" id="edit_type" required onchange="toggleEditOptions()"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        <option value="text">Text (Single Line)</option>
                        <option value="textarea">Textarea (Multi Line)</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>
                        <option value="email">Email</option>
                        <option value="phone">Phone</option>
                        <option value="select">Dropdown Select</option>
                        <option value="radio">Radio Button</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="file">File Upload</option>
                    </select>
                </div>

                <!-- Options Input (for select/radio/checkbox) -->
                <div id="edit_options_container" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Pilihan Options
                    </label>
                    <div id="edit_options_list" class="space-y-2"></div>
                    <button type="button" onclick="addEditOption()"
                        class="mt-2 text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium flex items-center gap-1">
                        <i class="mdi mdi-plus"></i> Tambah Opsi
                    </button>
                </div>

                <!-- File Configuration (shown when type is file) -->
                <div id="edit_file_config_container" class="hidden bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 space-y-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                            <i class="mdi mdi-file-upload text-indigo-600 dark:text-indigo-400 text-sm"></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Konfigurasi File</h4>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            <i class="mdi mdi-file-document text-gray-500 dark:text-gray-400 mr-1"></i>
                            Tipe File yang Diizinkan
                        </label>
                        <input type="text" name="file_types" id="edit_file_types"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="pdf, doc, docx, xls, xlsx">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                            Pisahkan dengan koma. Contoh: <code class="bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">pdf, jpg, png</code>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            <i class="mdi mdi-weight text-gray-500 dark:text-gray-400 mr-1"></i>
                            Ukuran File Maksimal (KB)
                        </label>
                        <input type="number" name="max_file_size" id="edit_max_file_size"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="2048">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                            Contoh: <code class="bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">2048</code> = 2MB, <code class="bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">5120</code> = 5MB
                        </p>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="button" onclick="setEditPresetFileTypes(['pdf'])"
                            class="px-3 py-1.5 text-xs bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-md hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors font-medium">
                            <i class="mdi mdi-file-pdf"></i> PDF Only
                        </button>
                        <button type="button" onclick="setEditPresetFileTypes(['jpg', 'jpeg', 'png'])"
                            class="px-3 py-1.5 text-xs bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors font-medium">
                            <i class="mdi mdi-image"></i> Images
                        </button>
                        <button type="button" onclick="setEditPresetFileTypes(['pdf', 'doc', 'docx'])"
                            class="px-3 py-1.5 text-xs bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-md hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors font-medium">
                            <i class="mdi mdi-file-document"></i> Documents
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Placeholder
                    </label>
                    <input type="text" name="placeholder" id="edit_placeholder"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Help Text
                    </label>
                    <input type="text" name="help_text" id="edit_help_text"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_required" id="edit_is_required" value="1"
                            class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Wajib diisi</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1"
                            class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg transition-colors font-medium flex items-center justify-center gap-2 shadow-sm">
                        <i class="mdi mdi-content-save"></i>
                        Update Field
                    </button>
                    <button type="button" onclick="closeEditModal()"
                        class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2.5 rounded-lg transition-colors font-medium">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Generate field name from label
        function generateFieldName() {
            const label = document.getElementById('label').value;
            const nameInput = document.getElementById('name');
            
            // Convert to lowercase, replace spaces and special chars with underscore
            const fieldName = label
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s]/g, '')  // Remove special characters
                .replace(/\s+/g, '_');         // Replace spaces with underscore
            
            nameInput.value = fieldName;
        }

        // Toggle field name edit mode
        let isFieldNameEditable = false;
        function toggleFieldNameEdit() {
            const nameInput = document.getElementById('name');
            isFieldNameEditable = !isFieldNameEditable;
            
            if (isFieldNameEditable) {
                nameInput.readOnly = false;
                nameInput.classList.remove('bg-gray-50', 'dark:bg-gray-800');
                nameInput.classList.add('bg-white', 'dark:bg-gray-700');
            } else {
                nameInput.readOnly = true;
                nameInput.classList.add('bg-gray-50', 'dark:bg-gray-800');
                nameInput.classList.remove('bg-white', 'dark:bg-gray-700');
            }
        }

        // Toggle options and file config based on field type
        function toggleOptions() {
            const type = document.getElementById('type').value;
            const optionsContainer = document.getElementById('options_container');
            const fileConfigContainer = document.getElementById('file_config_container');
            const needsOptions = ['select', 'radio', 'checkbox'].includes(type);
            const needsFileConfig = type === 'file';
            
            optionsContainer.classList.toggle('hidden', !needsOptions);
            fileConfigContainer.classList.toggle('hidden', !needsFileConfig);
        }

        // Set preset file types
        function setPresetFileTypes(types) {
            document.getElementById('file_types').value = types.join(', ');
        }

        // Set preset file types for edit modal
        function setEditPresetFileTypes(types) {
            document.getElementById('edit_file_types').value = types.join(', ');
        }

        function toggleEditOptions() {
            const type = document.getElementById('edit_type').value;
            const optionsContainer = document.getElementById('edit_options_container');
            const editFileConfigContainer = document.getElementById('edit_file_config_container');
            const needsOptions = ['select', 'radio', 'checkbox'].includes(type);
            const needsFileConfig = type === 'file';
            
            optionsContainer.classList.toggle('hidden', !needsOptions);
            editFileConfigContainer.classList.toggle('hidden', !needsFileConfig);
        }

        // Add new option input
        function addOption() {
            const optionsList = document.getElementById('options_list');
            const count = optionsList.children.length;
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `
                <input type="text" name="options[]"
                    class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                    placeholder="Opsi ${count + 1}">
                <button type="button" onclick="this.parentElement.remove()"
                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <i class="mdi mdi-delete"></i>
                </button>
            `;
            optionsList.appendChild(div);
        }

        function addEditOption(value = '') {
            const optionsList = document.getElementById('edit_options_list');
            const count = optionsList.children.length;
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `
                <input type="text" name="options[]" value="${value}"
                    class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                    placeholder="Opsi ${count + 1}">
                <button type="button" onclick="this.parentElement.remove()"
                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <i class="mdi mdi-delete"></i>
                </button>
            `;
            optionsList.appendChild(div);
        }

        // Edit field
        function editField(id, field) {
            const currentPath = window.location.pathname;
            document.getElementById('editForm').action = currentPath.replace('/form-builder', `/form-field/${id}`);
            document.getElementById('edit_form_type').value = field.form_type || 'global';
            document.getElementById('edit_label').value = field.label;
            document.getElementById('edit_name').value = field.name;
            document.getElementById('edit_type').value = field.type;
            document.getElementById('edit_placeholder').value = field.placeholder || '';
            document.getElementById('edit_help_text').value = field.help_text || '';
            document.getElementById('edit_is_required').checked = field.is_required;
            document.getElementById('edit_is_active').checked = field.is_active;
            
            // Setup file config
            document.getElementById('edit_file_types').value = field.file_types || '';
            document.getElementById('edit_max_file_size').value = field.max_file_size || '';

            // Setup options
            const optionsList = document.getElementById('edit_options_list');
            optionsList.innerHTML = '';
            if (field.options && Array.isArray(field.options)) {
                field.options.forEach((opt, index) => {
                    addEditOption(opt);
                });
            } else {
                addEditOption();
            }

            toggleEditOptions();
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Close modal on outside click
        document.getElementById('editModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Sortable using vanilla JS
        let draggedItem = null;

        document.querySelectorAll('.field-item').forEach(item => {
            item.setAttribute('draggable', 'true');

            item.addEventListener('dragstart', function(e) {
                draggedItem = this;
                this.classList.add('opacity-50');
                e.dataTransfer.effectAllowed = 'move';
            });

            item.addEventListener('dragend', function() {
                this.classList.remove('opacity-50');
                draggedItem = null;
            });

            item.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            });

            item.addEventListener('drop', async function(e) {
                e.preventDefault();
                if (draggedItem && draggedItem !== this) {
                    const fieldsList = this.closest('.fields-container');
                    const oldIndex = Array.from(fieldsList.querySelectorAll('.field-item')).indexOf(draggedItem);
                    const newIndex = Array.from(fieldsList.querySelectorAll('.field-item')).indexOf(this);

                    if (oldIndex < newIndex) {
                        this.parentNode.insertBefore(draggedItem, this.nextSibling);
                    } else {
                        this.parentNode.insertBefore(draggedItem, this);
                    }

                    // Update order on server
                    const fieldIds = Array.from(fieldsList.querySelectorAll('.field-item'))
                        .map(item => item.dataset.fieldId);

                    const currentPath = window.location.pathname;
                    const fetchUrl = currentPath.replace('/form-builder', '/form-field/reorder');

                    await fetch(fetchUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({
                            field_ids: fieldIds
                        })
                    });

                    // Reload page to update order badges
                    location.reload();
                }
            });
        });

        // Initialize options visibility on load
        toggleOptions();
        
        // --- Tabs Logic ---
        function switchTab(tabId) {
            // Save state
            localStorage.setItem('formBuilderTab', tabId);
            
            // 1. Update Buttons Styling
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('text-indigo-600', 'border-indigo-600', 'dark:text-indigo-500', 'dark:border-indigo-500');
                btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
            });
            const activeBtn = document.getElementById(`tab-btn-${tabId}`);
            if (activeBtn) {
                activeBtn.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                activeBtn.classList.add('text-indigo-600', 'border-indigo-600', 'dark:text-indigo-500', 'dark:border-indigo-500');
            }

            // 2. Update Icons Styling
            document.querySelectorAll('.tab-btn i').forEach(icon => {
                icon.classList.remove('text-indigo-600', 'dark:text-indigo-500');
                icon.classList.add('text-gray-400', 'group-hover:text-gray-500', 'dark:text-gray-500', 'dark:group-hover:text-gray-300');
            });
            const activeIcon = document.getElementById(`tab-icon-${tabId}`);
            if (activeIcon) {
                activeIcon.classList.remove('text-gray-400', 'group-hover:text-gray-500', 'dark:text-gray-500', 'dark:group-hover:text-gray-300');
                activeIcon.classList.add('text-indigo-600', 'dark:text-indigo-500');
            }

            // 3. Update Subtitle and List Title
            const titles = { 'global': 'Global Form', 'rekom': 'Rekom Form', 'izin': 'Izin Form' };
            const subtitleEl = document.getElementById('add-field-subtitle');
            if (subtitleEl) subtitleEl.textContent = `Tambah field baru ke ${titles[tabId]}`;
            const listTitleEl = document.getElementById('daftar-field-tab-name');
            if (listTitleEl) listTitleEl.textContent = titles[tabId];
            
            // 4. Update hidden form_type input
            const inputEl = document.getElementById('create_form_type');
            if (inputEl) inputEl.value = tabId;

            // 5. Toggle Counts
            document.querySelectorAll('.tab-count').forEach(el => el.classList.add('hidden'));
            const countEl = document.getElementById(`count-${tabId}`);
            if (countEl) countEl.classList.remove('hidden');

            // 6. Toggle List Containers
            document.querySelectorAll('.fields-container').forEach(el => el.classList.add('hidden'));
            const listEl = document.getElementById(`fields_list_${tabId}`);
            if (listEl) listEl.classList.remove('hidden');

            // 7. Toggle Template Editor and Sequence Config
            const templateContainer = document.getElementById('template-editor-container');
            const sequenceContainer = document.getElementById('sequence-config-container');
            const opdFieldStatus = document.getElementById('opd-field-status');
            const opdTemplateStatus = document.getElementById('opd-template-status');
            const rekomNumConfig = document.getElementById('rekom-number-config');
            const izinNumConfig = document.getElementById('izin-number-config');
            const editorRekom = document.getElementById('tpl-rekom-container');
            const editorIzin = document.getElementById('tpl-izin-container');
            const editorTitle = document.getElementById('template-editor-title');
            
            // Sections for variables
            const varSectionGlobal = document.getElementById('var-section-global');
            const varSectionRekom = document.getElementById('var-section-rekom');
            const varSectionIzin = document.getElementById('var-section-izin');

            if (tabId === 'rekom') {
                if (templateContainer) templateContainer.classList.remove('hidden');
                if (sequenceContainer) sequenceContainer.classList.remove('hidden');
                if (opdFieldStatus) opdFieldStatus.classList.remove('hidden');
                if (opdTemplateStatus) opdTemplateStatus.classList.remove('hidden');
                if (rekomNumConfig) rekomNumConfig.classList.remove('hidden');
                if (izinNumConfig) izinNumConfig.classList.add('hidden');
                if (editorRekom) editorRekom.classList.remove('hidden');
                if (editorIzin) editorIzin.classList.add('hidden');
                if (editorTitle) editorTitle.textContent = 'Rekom';

                // Update Header
                const configHeaderType = document.getElementById('config-header-type');
                if (configHeaderType) configHeaderType.textContent = 'Rekomendasi';
                const configHeaderSubtitle = document.getElementById('config-header-subtitle');
                if (configHeaderSubtitle) configHeaderSubtitle.textContent = 'Atur nomor urut dan panduan pengisian untuk Surat Rekomendasi';
                
                // Show Global + Rekom variables
                if (varSectionGlobal) varSectionGlobal.classList.remove('hidden');
                if (varSectionRekom) varSectionRekom.classList.remove('hidden');
                if (varSectionIzin) varSectionIzin.classList.add('hidden');
            } else if (tabId === 'izin') {
                if (templateContainer) templateContainer.classList.remove('hidden');
                if (sequenceContainer) sequenceContainer.classList.remove('hidden');
                if (rekomNumConfig) rekomNumConfig.classList.add('hidden');
                if (izinNumConfig) izinNumConfig.classList.remove('hidden');
                if (editorRekom) editorRekom.classList.add('hidden');
                if (editorIzin) editorIzin.classList.remove('hidden');
                if (editorTitle) editorTitle.textContent = 'Izin';

                // Update Header
                const configHeaderType = document.getElementById('config-header-type');
                if (configHeaderType) configHeaderType.textContent = 'Izin';
                const configHeaderSubtitle = document.getElementById('config-header-subtitle');
                if (configHeaderSubtitle) configHeaderSubtitle.textContent = 'Atur nomor urut dan panduan pengisian untuk Surat Izin';
                
                // Show Global + Izin variables
                if (varSectionGlobal) varSectionGlobal.classList.remove('hidden');
                if (varSectionRekom) varSectionRekom.classList.add('hidden');
                if (varSectionIzin) varSectionIzin.classList.remove('hidden');
            } else {
                if (templateContainer) templateContainer.classList.add('hidden');
                if (sequenceContainer) sequenceContainer.classList.add('hidden');
            }
        }

        function togglePlaceholderGuide() {
            const guide = document.getElementById('placeholder-guide');
            guide.classList.toggle('hidden');
        }

        function insertPlaceholder(code) {
            // Copy to clipboard
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(code).then(() => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Tersalin!',
                            text: 'Variabel ' + code + ' telah disalin ke clipboard.',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    }
                });
            }

            const currentTab = localStorage.getItem('formBuilderTab') || 'global';
            let activeEditorId = currentTab === 'rekom' ? 'editor_rekom' : 'editor_izin';

            if (typeof tinymce !== 'undefined') {
                const ed = tinymce.get(activeEditorId);
                if (ed) {
                    ed.insertContent(' ' + code + ' ');
                    ed.focus();
                } else {
                    // Fallback to text area insertion if TinyMCE failed to load
                    const ta = document.getElementById(activeEditorId);
                    if (ta) {
                        const start = ta.selectionStart;
                        const end = ta.selectionEnd;
                        const text = ta.value;
                        ta.value = text.substring(0, start) + ' ' + code + ' ' + text.substring(end);
                        ta.focus();
                        ta.selectionStart = start + code.length + 2;
                        ta.selectionEnd = start + code.length + 2;
                    }
                }
            }
        }

        // Initialize tabs on page load
        document.addEventListener('DOMContentLoaded', () => {
            const userRole = "{{ auth()->user()->role }}";
            let savedTab = localStorage.getItem('formBuilderTab') || 'global';
            
            if (userRole === 'operator_opd') {
                savedTab = 'rekom';
            } else if (userRole === 'verifikator') {
                savedTab = 'izin';
            }
            
            switchTab(savedTab);

            // Init TinyMCE
            if (typeof tinymce !== 'undefined') {
                const updateEditorLayout = (editor, width, height, padding) => {
                    const body = editor.getBody();
                    if (width) body.style.width = width;
                    if (height) body.style.minHeight = height;
                    if (padding) body.style.padding = padding;
                };

                const tinymceConfigs = {
                    height: 800,
                    menubar: 'file edit view insert format layout tools table help',
                    menu: {
                        layout: { title: 'Layout', items: 'papersize margins' }
                    },
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'pagebreak', 
                        'nonbreaking', 'emoticons', 'accordion', 'visualchars', 'directionality'
                    ],
                    // Multiple toolbars to mimic Word layout and prevent collapsing
                    toolbar1: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | superscript subscript | removeformat',
                    toolbar2: 'alignleft aligncenter alignright alignjustify | lineheight | bullist numlist outdent indent | table image link | charmap emoticons | pagebreak | fullscreen preview code help',
                    toolbar_mode: 'wrap', // Ensure tools wrap instead of hiding behind '...' button
                    font_family_formats: 'Arial=arial,helvetica,sans-serif; Courier New=courier new,courier,monospace; Akurat=akurat,sans-serif; Times New Roman=times new roman,times,serif; Verdana=verdana,geneva,sans-serif; Georgia=georgia,palatino,serif; Tahoma=tahoma,arial,helvetica,sans-serif',
                    font_size_formats: '8pt 9pt 10pt 11pt 12pt 14pt 18pt 24pt 30pt 36pt 48pt 60pt 72pt 96pt',
                    content_style: `
                        body { 
                            font-family: "Times New Roman", serif; 
                            font-size: 12pt; 
                            line-height: 1.5; 
                            width: 21cm; 
                            min-height: 29.7cm; 
                            margin: 1cm auto; 
                            padding: 2cm 2.5cm; 
                            background: white; 
                            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
                            color: #000;
                            box-sizing: border-box;
                            transition: all 0.3s ease;
                        } 
                        html { background: #f4f4f7; padding: 20px 0; }
                        .mce-content-body[data-mce-placeholder]:not(.mce-visualblocks)::before {
                            color: #ccc;
                        }
                    `,
                    branding: false,
                    promotion: false,
                    skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
                    content_css: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
                    pagebreak_separator: '<!-- pagebreak -->',
                    setup: function (editor) {
                        editor.on('change', function () {
                            editor.save();
                        });

                        // Register Paper Size Menu
                        editor.ui.registry.addNestedMenuItem('papersize', {
                            text: 'Ukuran Kertas',
                            icon: 'resize-handle',
                            getSubmenuItems: function () {
                                return [
                                    {
                                        type: 'menuitem',
                                        text: 'A4 (21cm x 29.7cm)',
                                        onAction: function () {
                                            updateEditorLayout(editor, '21cm', '29.7cm', null);
                                        }
                                    },
                                    {
                                        type: 'menuitem',
                                        text: 'F4 / Folio (21.5cm x 33cm)',
                                        onAction: function () {
                                            updateEditorLayout(editor, '21.5cm', '33cm', null);
                                        }
                                    },
                                    {
                                        type: 'menuitem',
                                        text: 'Legal (21.59cm x 35.56cm)',
                                        onAction: function () {
                                            updateEditorLayout(editor, '21.59cm', '35.56cm', null);
                                        }
                                    }
                                ];
                            }
                        });

                        // Register Margins Menu
                        editor.ui.registry.addNestedMenuItem('margins', {
                            text: 'Margin (Batas Tepi)',
                            icon: 'line-height',
                            getSubmenuItems: function () {
                                return [
                                    {
                                        type: 'menuitem',
                                        text: 'Normal (T:2.5, B:2.5, K:3, Kn:2.5)',
                                        onAction: function () {
                                            updateEditorLayout(editor, null, null, '2.5cm 2.5cm 3cm 2.5cm');
                                        }
                                    },
                                    {
                                        type: 'menuitem',
                                        text: 'Narrow (1.27cm)',
                                        onAction: function () {
                                            updateEditorLayout(editor, null, null, '1.27cm');
                                        }
                                    },
                                    {
                                        type: 'menuitem',
                                        text: 'Wide (Kiri & Kanan 5cm)',
                                        onAction: function () {
                                            updateEditorLayout(editor, null, null, '2.5cm 5cm');
                                        }
                                    }
                                ];
                            }
                        });
                    }
                };

                tinymce.init({
                    ...tinymceConfigs,
                    selector: '#editor_rekom'
                });
                
                tinymce.init({
                    ...tinymceConfigs,
                    selector: '#editor_izin'
                });
            }
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    </div> <!-- End form-builder-app -->
</x-layout>
