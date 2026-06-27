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
                @if($perijinan->has_bo_form && (auth()->user()->isAdmin() || auth()->user()->isBo()))
                <li class="mr-2">
                    <button onclick="switchTab('bo')" id="tab-btn-bo" class="tab-btn inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group {{ auth()->user()->isAdmin() ? 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' : 'text-indigo-600 border-indigo-600 dark:text-indigo-500 dark:border-indigo-500' }}">
                        <i id="tab-icon-bo" class="mdi mdi-account-cog mr-2 text-lg {{ auth()->user()->isAdmin() ? 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300' : 'text-indigo-600 dark:text-indigo-500' }}"></i>
                        BO Form
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
                            <option value="pas_foto">Pas Foto (3x4)</option>
                            <option value="gambar">Gambar (Dokumentasi/Bebas)</option>
                            <option value="table">Table (Grid/Matriks)</option>
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

                        <!-- Gambar Dimension Configuration (shown when type is gambar) -->
                        <div id="gambar_dimension_container" class="hidden grid grid-cols-2 gap-3 pt-3 border-t border-gray-200 dark:border-gray-700 mt-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Panjang Gambar (Tinggi / Height) (cm)</label>
                                <input type="number" step="0.1" name="options[img_height]" id="img_height" class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm text-gray-950 dark:text-gray-100" placeholder="Contoh: 4">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Lebar Gambar (Width) (cm)</label>
                                <input type="number" step="0.1" name="options[img_width]" id="img_width" class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm text-gray-950 dark:text-gray-100" placeholder="Contoh: 3">
                            </div>
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

                    <!-- Table Configuration (shown when type is table) -->
                    <div id="table_config_container" class="hidden bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 space-y-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                                <i class="mdi mdi-table text-indigo-600 dark:text-indigo-400 text-sm"></i>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Konfigurasi Desain Table</h4>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mb-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Baris (Rows)</label>
                                <input type="number" id="table_rows" value="2" min="1" max="150" class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm text-gray-950 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Kolom (Cols)</label>
                                <input type="number" id="table_cols" value="3" min="1" max="150" class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm text-gray-950 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Jenis Font</label>
                                <select id="table_font_family" onchange="serializeTableDesign('create'); renderPreviewGrid('create');" class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm text-gray-950 dark:text-gray-100">
                                    <option value="">Default</option>
                                    <option value="'Times New Roman', Times, serif">Times New Roman</option>
                                    <option value="'Bookman Old Style', Bookman, Georgia, serif">Bookman</option>
                                    <option value="Arial, Helvetica, sans-serif">Arial</option>
                                    <option value="'Courier New', Courier, monospace">Courier New</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="resizeTableDesigner('create')" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-colors flex items-center justify-center gap-1">
                                <i class="mdi mdi-table-check"></i> Terapkan Ukuran Grid
                            </button>
                            <button type="button" onclick="resetTableDesigner('create')" title="Reset ke grid kosong"
                                class="px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-700 text-xs font-bold rounded-lg hover:bg-red-100 transition-colors flex items-center gap-1">
                                <i class="mdi mdi-refresh"></i> Reset
                            </button>
                        </div>
                        
                        <!-- Table Preview / Selector Grid -->
                        <div class="mt-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 p-2 overflow-x-auto">
                            <table id="table_designer_preview_create" class="w-full border-collapse border border-gray-300 text-xs text-gray-900 dark:text-white min-w-[300px]"></table>
                        </div>
                        
                        <!-- Cell Configurator Section -->
                        <div id="cell_configurator_create" class="hidden p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 space-y-3">
                            <div class="flex items-center justify-between">
                                <h5 class="text-xs font-bold text-gray-800 dark:text-white">Edit Sel <span id="selected_cell_label_create" class="font-mono text-indigo-600 dark:text-indigo-400"></span></h5>
                                <span id="cell_span_info_create" class="text-[10px] text-gray-400"></span>
                            </div>
                            <!-- Merge/Split buttons -->
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" onclick="mergeRight('create')" class="px-2.5 py-1.5 text-[10px] font-bold bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-700 rounded-lg hover:bg-blue-100 transition-colors flex items-center gap-1">
                                    <i class="mdi mdi-table-merge-cells"></i> Gabung Kanan
                                </button>
                                <button type="button" onclick="mergeDown('create')" class="px-2.5 py-1.5 text-[10px] font-bold bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-700 rounded-lg hover:bg-blue-100 transition-colors flex items-center gap-1">
                                    <i class="mdi mdi-table-merge-cells" style="transform:rotate(90deg);display:inline-block;"></i> Gabung Bawah
                                </button>
                                <button type="button" onclick="splitCell('create')" class="px-2.5 py-1.5 text-[10px] font-bold bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-700 rounded-lg hover:bg-orange-100 transition-colors flex items-center gap-1">
                                    <i class="mdi mdi-table-split-cell"></i> Pisahkan Sel
                                </button>
                            </div>
                            <!-- Insert / Delete Row & Col -->
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" onclick="insertRowAt('create', tableDesigners.create.selectedRow)" title="Sisipkan baris di atas sel ini"
                                    class="px-2.5 py-1.5 text-[10px] font-bold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-700 rounded-lg hover:bg-emerald-100 transition-colors flex items-center gap-1">
                                    <i class="mdi mdi-table-row-plus-before"></i> Sisip Baris Atas
                                </button>
                                <button type="button" onclick="insertRowAt('create', tableDesigners.create.selectedRow + 1)" title="Sisipkan baris di bawah sel ini"
                                    class="px-2.5 py-1.5 text-[10px] font-bold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-700 rounded-lg hover:bg-emerald-100 transition-colors flex items-center gap-1">
                                    <i class="mdi mdi-table-row-plus-after"></i> Sisip Baris Bawah
                                </button>
                                <button type="button" onclick="insertColAt('create', tableDesigners.create.selectedCol)" title="Sisipkan kolom di kiri sel ini"
                                    class="px-2.5 py-1.5 text-[10px] font-bold bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400 border border-teal-200 dark:border-teal-700 rounded-lg hover:bg-teal-100 transition-colors flex items-center gap-1">
                                    <i class="mdi mdi-table-column-plus-before"></i> Sisip Kol Kiri
                                </button>
                                <button type="button" onclick="insertColAt('create', tableDesigners.create.selectedCol + 1)" title="Sisipkan kolom di kanan sel ini"
                                    class="px-2.5 py-1.5 text-[10px] font-bold bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400 border border-teal-200 dark:border-teal-700 rounded-lg hover:bg-teal-100 transition-colors flex items-center gap-1">
                                    <i class="mdi mdi-table-column-plus-after"></i> Sisip Kol Kanan
                                </button>
                                <button type="button" onclick="deleteRow('create')" title="Hapus baris ini"
                                    class="px-2.5 py-1.5 text-[10px] font-bold bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-700 rounded-lg hover:bg-red-100 transition-colors flex items-center gap-1">
                                    <i class="mdi mdi-table-row-remove"></i> Hapus Baris
                                </button>
                                <button type="button" onclick="deleteCol('create')" title="Hapus kolom ini"
                                    class="px-2.5 py-1.5 text-[10px] font-bold bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-700 rounded-lg hover:bg-red-100 transition-colors flex items-center gap-1">
                                    <i class="mdi mdi-table-column-remove"></i> Hapus Kolom
                                </button>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Teks Sel / Judul Kolom</label>
                                <input type="text" id="cell_content_create" oninput="updateCellProperties('create')" class="w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-xs text-gray-950 dark:text-gray-100">
                            </div>

                            <!-- Text Formatting Panel -->
                            <div class="bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 rounded-lg p-2.5 space-y-2">
                                <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-1"><i class="mdi mdi-format-text"></i> Format Teks</p>

                                <!-- Bold / Italic / Underline -->
                                <div class="flex items-center gap-1.5">
                                    <button type="button" id="cell_fmt_bold_create" onclick="toggleCellFormat('create','bold')" title="Bold"
                                        class="fmt-btn w-7 h-7 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 transition-colors flex items-center justify-center text-xs font-black">
                                        <i class="mdi mdi-format-bold"></i>
                                    </button>
                                    <button type="button" id="cell_fmt_italic_create" onclick="toggleCellFormat('create','italic')" title="Italic"
                                        class="fmt-btn w-7 h-7 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 transition-colors flex items-center justify-center text-xs">
                                        <i class="mdi mdi-format-italic"></i>
                                    </button>
                                    <button type="button" id="cell_fmt_underline_create" onclick="toggleCellFormat('create','underline')" title="Underline"
                                        class="fmt-btn w-7 h-7 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 transition-colors flex items-center justify-center text-xs">
                                        <i class="mdi mdi-format-underline"></i>
                                    </button>
                                    <div class="w-px h-5 bg-gray-200 dark:bg-gray-600 mx-0.5"></div>
                                    <!-- Alignment -->
                                    <button type="button" id="cell_fmt_align_left_create" onclick="setCellAlign('create','left')" title="Rata Kiri"
                                        class="fmt-btn w-7 h-7 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 transition-colors flex items-center justify-center text-xs">
                                        <i class="mdi mdi-format-align-left"></i>
                                    </button>
                                    <button type="button" id="cell_fmt_align_center_create" onclick="setCellAlign('create','center')" title="Rata Tengah"
                                        class="fmt-btn w-7 h-7 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 transition-colors flex items-center justify-center text-xs">
                                        <i class="mdi mdi-format-align-center"></i>
                                    </button>
                                    <button type="button" id="cell_fmt_align_right_create" onclick="setCellAlign('create','right')" title="Rata Kanan"
                                        class="fmt-btn w-7 h-7 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 transition-colors flex items-center justify-center text-xs">
                                        <i class="mdi mdi-format-align-right"></i>
                                    </button>
                                </div>

                                <!-- Font Size & Width -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Ukuran Font</label>
                                        <select id="cell_font_size_create" onchange="updateCellProperties('create')" class="w-full px-2 py-1 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-xs text-gray-950 dark:text-gray-100">
                                            <option value="">Default</option>
                                            <option value="10px">10px (Kecil)</option>
                                            <option value="11px">11px</option>
                                            <option value="12px">12px</option>
                                            <option value="13px">13px</option>
                                            <option value="14px">14px (Normal)</option>
                                            <option value="16px">16px (Besar)</option>
                                            <option value="18px">18px</option>
                                            <option value="20px">20px</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Lebar Kolom (%)</label>
                                        <input type="number" id="cell_width_create" oninput="updateCellProperties('create')" min="5" max="100" placeholder="Auto"
                                            class="w-full px-2 py-1 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-xs text-gray-950 dark:text-gray-100">
                                    </div>
                                </div>

                                <!-- Colors -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Warna Teks</label>
                                        <div class="flex items-center gap-1.5">
                                            <input type="color" id="cell_text_color_create" oninput="updateCellProperties('create')" value="#000000"
                                                class="w-7 h-7 rounded border border-gray-200 dark:border-gray-600 cursor-pointer p-0">
                                            <button type="button" onclick="resetCellColor('create','text')" class="text-[10px] text-gray-400 hover:text-red-500 transition-colors" title="Reset">
                                                <i class="mdi mdi-close-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Warna Latar</label>
                                        <div class="flex items-center gap-1.5">
                                            <input type="color" id="cell_bg_color_create" oninput="updateCellProperties('create')" value="#ffffff"
                                                class="w-7 h-7 rounded border border-gray-200 dark:border-gray-600 cursor-pointer p-0">
                                            <button type="button" onclick="resetCellColor('create','bg')" class="text-[10px] text-gray-400 hover:text-red-500 transition-colors" title="Reset">
                                                <i class="mdi mdi-close-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Text Formatting Panel -->

                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="cell_is_input_create" onchange="updateCellProperties('create')" class="w-3.5 h-3.5 text-indigo-600 rounded">
                                <label for="cell_is_input_create" class="text-xs font-semibold text-gray-700 dark:text-gray-300">Merupakan Field Isian</label>
                            </div>
                            <div id="cell_input_options_create" class="hidden space-y-2">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tipe Isian</label>
                                        <select id="cell_input_type_create" onchange="updateCellProperties('create')" class="w-full px-2 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-xs text-gray-950 dark:text-gray-100">
                                            <option value="text">Text</option>
                                            <option value="number">Number</option>
                                            <option value="date">Date</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Key Isian (Unique)</label>
                                        <input type="text" id="cell_input_name_create" oninput="updateCellProperties('create')" class="w-full px-2 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-xs text-gray-950 dark:text-gray-100" placeholder="spesifikasi_1">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Isi Otomatis / Variabel Dinamis</label>
                                    <div class="space-y-1.5">
                                        <select onchange="insertVarPlaceholder('create', this)" class="w-full px-2 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-xs text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            <option value="">-- Pilih Variabel --</option>
                                            <optgroup label="Sistem / Surat">
                                                <option value="${nomor_registrasi}">No. Registrasi</option>
                                                <option value="${nomor_surat}">No. Surat/Izin</option>
                                                <option value="${tanggal_daftar}">Tgl. Daftar</option>
                                                <option value="${nama_layanan}">Nama Layanan</option>
                                            </optgroup>
                                            @if($perijinan->activeFormFields->count() > 0)
                                                @php
                                                    $groupedFields = $perijinan->activeFormFields->groupBy('form_type');
                                                @endphp
                                                @foreach($groupedFields as $type => $fields)
                                                    <optgroup label="Form {{ strtoupper($type) }}">
                                                        @foreach($fields as $f)
                                                            @if($f->type !== 'table')
                                                                <option value="{{ '${' . str_replace(' ', '_', strtolower($f->name)) . '}' }}">{{ $f->label }}</option>
                                                            @endif
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            @endif
                                        </select>
                                        <input type="text" id="cell_dynamic_var_create" oninput="updateCellProperties('create')" placeholder="Ketik manual atau pilih di atas..." class="w-full px-2 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-xs text-gray-955 dark:text-gray-100">
                                    </div>
                                    <p class="text-[9px] text-gray-400 mt-0.5 italic">Pilih dari dropdown di atas atau ketik manual.</p>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="options[table_data]" id="table_data_json_create">
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
                                @if($perijinan->has_bo_form && (auth()->user()->isAdmin() || auth()->user()->isBo()))
                                    <span class="text-gray-500 dark:text-gray-400 font-normal tab-count hidden" id="count-bo">({{ $perijinan->formFields->where('form_type', 'bo')->count() }})</span>
                                @endif
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Urutkan field dengan drag & drop</p>
                        </div>
                    </div>
                </div>

                @if ($perijinan->formFields->count() > 0)
                    @foreach(['global', 'rekom', 'izin', 'bo'] as $type)
                        @if($type === 'bo' && !$perijinan->has_bo_form) @continue @endif
                        @php
                            $canSeeType = auth()->user()->isAdmin() || 
                                          ($type === 'rekom' && auth()->user()->isOperatorOpd()) ||
                                          ($type === 'izin' && auth()->user()->isVerifikator()) ||
                                          ($type === 'bo' && auth()->user()->isBo());
                        @endphp
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
                                            @if($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar')
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
                                                        $opdCustomFields = $perijinan->formFields()->where('opd_id', $opd->id)->get();
                                                        $count = $opdCustomFields->count(); 
                                                        $hasCustom = $count > 0;
                                                    @endphp
                                                    <div class="flex flex-col bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                                                        <div class="flex items-center justify-between px-3 py-2 border-b border-gray-50 dark:border-gray-700/50 bg-gray-50/30 dark:bg-gray-900/20">
                                                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $opd->nama_opd }}</span>
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
                                                        @if($hasCustom)
                                                            <div class="px-3 py-2">
                                                                <div class="flex flex-wrap gap-1.5">
                                                                    @foreach($opdCustomFields as $f)
                                                                        <span class="text-[9px] px-1.5 py-0.5 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded border border-gray-100 dark:border-gray-600 font-medium">
                                                                            {{ $f->label }}
                                                                        </span>
                                                                    @endforeach
                                                                </div>
                                                            </div>
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
                                        '${TANGGAL_HARI_INI}' => 'Tanggal Hari Ini (Lengkap)',
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

                            <!-- Variabel Khusus BO -->
                            @if($perijinan->has_bo_form)
                            <div class="dynamic-var-section hidden" id="var-section-bo">
                                <span class="text-[10px] uppercase tracking-wider font-bold text-emerald-800 dark:text-emerald-300 block mb-2">Variabel Form Khusus BO:</span>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($perijinan->formFields->where('form_type', 'bo') as $field)
                                        @php $varName = strtoupper(str_replace(' ', '_', $field->label)); @endphp
                                        <button type="button"
                                            onclick="insertPlaceholder('{{ '${' . $varName . '}' }}')"
                                            title="{{ $field->label }}"
                                            class="inline-flex items-center gap-1.5 bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-700 hover:border-emerald-600 rounded-lg px-2.5 py-1.5 text-[11px] font-mono font-bold transition-all shadow-sm">
                                            <i class="mdi mdi-plus text-xs"></i>{{ '${' . $varName . '}' }}
                                        </button>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">Belum ada field di BO Form</span>
                                    @endforelse
                                </div>
                            </div>
                            @endif
                        </div>                    </div>

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
                                                <a href="{{ route('perijinan.templates.download', ['id' => $perijinan->id, 'type' => 'rekom', 'force_global' => 1, 't' => time()]) }}" class="flex items-center gap-1.5 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded transition-colors font-semibold">
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
                                                <a href="{{ route('perijinan.templates.download', ['id' => $perijinan->id, 'type' => 'rekom', 't' => time()]) }}" class="flex items-center gap-1.5 text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded transition-colors font-semibold shadow-sm">
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
                        <option value="pas_foto">Pas Foto (3x4)</option>
                        <option value="gambar">Gambar (Dokumentasi/Bebas)</option>
                        <option value="table">Table (Grid/Matriks)</option>
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

                <!-- Table Configuration (shown when type is table) for Edit -->
                <div id="edit_table_config_container" class="hidden bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 space-y-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                            <i class="mdi mdi-table text-indigo-600 dark:text-indigo-400 text-sm"></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Konfigurasi Desain Table (Edit)</h4>
                    </div>
                    <div class="grid grid-cols-3 gap-3 mb-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Baris (Rows)</label>
                            <input type="number" id="edit_table_rows" value="2" min="1" max="150" class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm text-gray-950 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Kolom (Cols)</label>
                            <input type="number" id="edit_table_cols" value="3" min="1" max="150" class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm text-gray-950 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Jenis Font</label>
                            <select id="edit_table_font_family" onchange="serializeTableDesign('edit'); renderPreviewGrid('edit');" class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm text-gray-950 dark:text-gray-100">
                                <option value="">Default</option>
                                <option value="'Times New Roman', Times, serif">Times New Roman</option>
                                <option value="'Bookman Old Style', Bookman, Georgia, serif">Bookman</option>
                                <option value="Arial, Helvetica, sans-serif">Arial</option>
                                <option value="'Courier New', Courier, monospace">Courier New</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="resizeTableDesigner('edit')" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-colors flex items-center justify-center gap-1">
                            <i class="mdi mdi-table-check"></i> Terapkan Ukuran Grid
                        </button>
                        <button type="button" onclick="resetTableDesigner('edit')" title="Reset ke grid kosong"
                            class="px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-700 text-xs font-bold rounded-lg hover:bg-red-100 transition-colors flex items-center gap-1">
                            <i class="mdi mdi-refresh"></i> Reset
                        </button>
                    </div>
                    
                    <!-- Table Preview / Selector Grid -->
                    <div class="mt-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 p-2 overflow-x-auto">
                        <table id="edit_table_designer_preview_edit" class="w-full border-collapse border border-gray-300 text-xs text-gray-900 dark:text-white min-w-[300px]"></table>
                    </div>
                    
                    <!-- Cell Configurator Section -->
                    <div id="cell_configurator_edit" class="hidden p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 space-y-3">
                        <div class="flex items-center justify-between">
                            <h5 class="text-xs font-bold text-gray-800 dark:text-white">Edit Sel <span id="selected_cell_label_edit" class="font-mono text-indigo-600 dark:text-indigo-400"></span></h5>
                            <span id="cell_span_info_edit" class="text-[10px] text-gray-400"></span>
                        </div>
                        <!-- Merge/Split buttons -->
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="mergeRight('edit')" class="px-2.5 py-1.5 text-[10px] font-bold bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-700 rounded-lg hover:bg-blue-100 transition-colors flex items-center gap-1">
                                <i class="mdi mdi-table-merge-cells"></i> Gabung Kanan
                            </button>
                            <button type="button" onclick="mergeDown('edit')" class="px-2.5 py-1.5 text-[10px] font-bold bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-700 rounded-lg hover:bg-blue-100 transition-colors flex items-center gap-1">
                                <i class="mdi mdi-table-merge-cells" style="transform:rotate(90deg);display:inline-block;"></i> Gabung Bawah
                            </button>
                            <button type="button" onclick="splitCell('edit')" class="px-2.5 py-1.5 text-[10px] font-bold bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-700 rounded-lg hover:bg-orange-100 transition-colors flex items-center gap-1">
                                <i class="mdi mdi-table-split-cell"></i> Pisahkan Sel
                            </button>
                        </div>
                        <!-- Insert / Delete Row & Col -->
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="insertRowAt('edit', tableDesigners.edit.selectedRow)" title="Sisipkan baris di atas sel ini"
                                class="px-2.5 py-1.5 text-[10px] font-bold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-700 rounded-lg hover:bg-emerald-100 transition-colors flex items-center gap-1">
                                <i class="mdi mdi-table-row-plus-before"></i> Sisip Baris Atas
                            </button>
                            <button type="button" onclick="insertRowAt('edit', tableDesigners.edit.selectedRow + 1)" title="Sisipkan baris di bawah sel ini"
                                class="px-2.5 py-1.5 text-[10px] font-bold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-700 rounded-lg hover:bg-emerald-100 transition-colors flex items-center gap-1">
                                <i class="mdi mdi-table-row-plus-after"></i> Sisip Baris Bawah
                            </button>
                            <button type="button" onclick="insertColAt('edit', tableDesigners.edit.selectedCol)" title="Sisipkan kolom di kiri sel ini"
                                class="px-2.5 py-1.5 text-[10px] font-bold bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400 border border-teal-200 dark:border-teal-700 rounded-lg hover:bg-teal-100 transition-colors flex items-center gap-1">
                                <i class="mdi mdi-table-column-plus-before"></i> Sisip Kol Kiri
                            </button>
                            <button type="button" onclick="insertColAt('edit', tableDesigners.edit.selectedCol + 1)" title="Sisipkan kolom di kanan sel ini"
                                class="px-2.5 py-1.5 text-[10px] font-bold bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400 border border-teal-200 dark:border-teal-700 rounded-lg hover:bg-teal-100 transition-colors flex items-center gap-1">
                                <i class="mdi mdi-table-column-plus-after"></i> Sisip Kol Kanan
                            </button>
                            <button type="button" onclick="deleteRow('edit')" title="Hapus baris ini"
                                class="px-2.5 py-1.5 text-[10px] font-bold bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-700 rounded-lg hover:bg-red-100 transition-colors flex items-center gap-1">
                                <i class="mdi mdi-table-row-remove"></i> Hapus Baris
                            </button>
                            <button type="button" onclick="deleteCol('edit')" title="Hapus kolom ini"
                                class="px-2.5 py-1.5 text-[10px] font-bold bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-700 rounded-lg hover:bg-red-100 transition-colors flex items-center gap-1">
                                <i class="mdi mdi-table-column-remove"></i> Hapus Kolom
                            </button>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Teks Sel / Judul Kolom</label>
                            <input type="text" id="cell_content_edit" oninput="updateCellProperties('edit')" class="w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-xs text-gray-950 dark:text-gray-100">
                        </div>

                        <!-- Text Formatting Panel -->
                        <div class="bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 rounded-lg p-2.5 space-y-2">
                            <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-1"><i class="mdi mdi-format-text"></i> Format Teks</p>

                            <!-- Bold / Italic / Underline -->
                            <div class="flex items-center gap-1.5">
                                <button type="button" id="cell_fmt_bold_edit" onclick="toggleCellFormat('edit','bold')" title="Bold"
                                    class="fmt-btn w-7 h-7 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 transition-colors flex items-center justify-center text-xs font-black">
                                    <i class="mdi mdi-format-bold"></i>
                                </button>
                                <button type="button" id="cell_fmt_italic_edit" onclick="toggleCellFormat('edit','italic')" title="Italic"
                                    class="fmt-btn w-7 h-7 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 transition-colors flex items-center justify-center text-xs">
                                    <i class="mdi mdi-format-italic"></i>
                                </button>
                                <button type="button" id="cell_fmt_underline_edit" onclick="toggleCellFormat('edit','underline')" title="Underline"
                                    class="fmt-btn w-7 h-7 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 transition-colors flex items-center justify-center text-xs">
                                    <i class="mdi mdi-format-underline"></i>
                                </button>
                                <div class="w-px h-5 bg-gray-200 dark:bg-gray-600 mx-0.5"></div>
                                <!-- Alignment -->
                                <button type="button" id="cell_fmt_align_left_edit" onclick="setCellAlign('edit','left')" title="Rata Kiri"
                                    class="fmt-btn w-7 h-7 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 transition-colors flex items-center justify-center text-xs">
                                    <i class="mdi mdi-format-align-left"></i>
                                </button>
                                <button type="button" id="cell_fmt_align_center_edit" onclick="setCellAlign('edit','center')" title="Rata Tengah"
                                    class="fmt-btn w-7 h-7 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 transition-colors flex items-center justify-center text-xs">
                                    <i class="mdi mdi-format-align-center"></i>
                                </button>
                                <button type="button" id="cell_fmt_align_right_edit" onclick="setCellAlign('edit','right')" title="Rata Kanan"
                                    class="fmt-btn w-7 h-7 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 transition-colors flex items-center justify-center text-xs">
                                    <i class="mdi mdi-format-align-right"></i>
                                </button>
                            </div>

                            <!-- Font Size & Width -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Ukuran Font</label>
                                    <select id="cell_font_size_edit" onchange="updateCellProperties('edit')" class="w-full px-2 py-1 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-xs text-gray-950 dark:text-gray-100">
                                        <option value="">Default</option>
                                        <option value="10px">10px (Kecil)</option>
                                        <option value="11px">11px</option>
                                        <option value="12px">12px</option>
                                        <option value="13px">13px</option>
                                        <option value="14px">14px (Normal)</option>
                                        <option value="16px">16px (Besar)</option>
                                        <option value="18px">18px</option>
                                        <option value="20px">20px</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Lebar Kolom (%)</label>
                                    <input type="number" id="cell_width_edit" oninput="updateCellProperties('edit')" min="5" max="100" placeholder="Auto"
                                        class="w-full px-2 py-1 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-xs text-gray-950 dark:text-gray-100">
                                </div>
                            </div>

                            <!-- Colors -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Warna Teks</label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="color" id="cell_text_color_edit" oninput="updateCellProperties('edit')" value="#000000"
                                            class="w-7 h-7 rounded border border-gray-200 dark:border-gray-600 cursor-pointer p-0">
                                        <button type="button" onclick="resetCellColor('edit','text')" class="text-[10px] text-gray-400 hover:text-red-500 transition-colors" title="Reset">
                                            <i class="mdi mdi-close-circle"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Warna Latar</label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="color" id="cell_bg_color_edit" oninput="updateCellProperties('edit')" value="#ffffff"
                                            class="w-7 h-7 rounded border border-gray-200 dark:border-gray-600 cursor-pointer p-0">
                                        <button type="button" onclick="resetCellColor('edit','bg')" class="text-[10px] text-gray-400 hover:text-red-500 transition-colors" title="Reset">
                                            <i class="mdi mdi-close-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Text Formatting Panel -->

                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="cell_is_input_edit" onchange="updateCellProperties('edit')" class="w-3.5 h-3.5 text-indigo-600 rounded">
                            <label for="cell_is_input_edit" class="text-xs font-semibold text-gray-700 dark:text-gray-300">Merupakan Field Isian</label>
                        </div>
                        <div id="cell_input_options_edit" class="hidden space-y-2">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tipe Isian</label>
                                    <select id="cell_input_type_edit" onchange="updateCellProperties('edit')" class="w-full px-2 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-xs text-gray-950 dark:text-gray-100">
                                        <option value="text">Text</option>
                                        <option value="number">Number</option>
                                        <option value="date">Date</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Key Isian (Unique)</label>
                                    <input type="text" id="cell_input_name_edit" oninput="updateCellProperties('edit')" class="w-full px-2 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-xs text-gray-950 dark:text-gray-100" placeholder="spesifikasi_1">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Isi Otomatis / Variabel Dinamis</label>
                                <div class="space-y-1.5">
                                    <select onchange="insertVarPlaceholder('edit', this)" class="w-full px-2 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-xs text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        <option value="">-- Pilih Variabel --</option>
                                        <optgroup label="Sistem / Surat">
                                            <option value="${nomor_registrasi}">No. Registrasi</option>
                                            <option value="${nomor_surat}">No. Surat/Izin</option>
                                            <option value="${tanggal_daftar}">Tgl. Daftar</option>
                                            <option value="${nama_layanan}">Nama Layanan</option>
                                        </optgroup>
                                        @if($perijinan->activeFormFields->count() > 0)
                                            @php
                                                $groupedFields = $perijinan->activeFormFields->groupBy('form_type');
                                            @endphp
                                            @foreach($groupedFields as $type => $fields)
                                                <optgroup label="Form {{ strtoupper($type) }}">
                                                    @foreach($fields as $f)
                                                        @if($f->type !== 'table')
                                                            <option value="{{ '${' . str_replace(' ', '_', strtolower($f->name)) . '}' }}">{{ $f->label }}</option>
                                                        @endif
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="text" id="cell_dynamic_var_edit" oninput="updateCellProperties('edit')" placeholder="Ketik manual atau pilih di atas..." class="w-full px-2 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-xs text-gray-955 dark:text-gray-100">
                                </div>
                                <p class="text-[9px] text-gray-400 mt-0.5 italic">Pilih dari dropdown di atas atau ketik manual.</p>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="options[table_data]" id="edit_table_data_json_edit">
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

                    <!-- Gambar Dimension Configuration (shown when type is gambar) for Edit -->
                    <div id="edit_gambar_dimension_container" class="hidden grid grid-cols-2 gap-3 pt-3 border-t border-gray-200 dark:border-gray-700 mt-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Panjang Gambar (Tinggi / Height) (cm)</label>
                            <input type="number" step="0.1" name="options[img_height]" id="edit_img_height" class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm text-gray-950 dark:text-gray-100" placeholder="Contoh: 4">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Lebar Gambar (Width) (cm)</label>
                            <input type="number" step="0.1" name="options[img_width]" id="edit_img_width" class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm text-gray-950 dark:text-gray-100" placeholder="Contoh: 3">
                        </div>
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

        let tableDesignerInitialized = false;

        // Toggle options and file config based on field type
        function toggleOptions() {
            const type = document.getElementById('type').value;
            const optionsContainer = document.getElementById('options_container');
            const fileConfigContainer = document.getElementById('file_config_container');
            const tableConfigContainer = document.getElementById('table_config_container');
            const needsOptions = ['select', 'radio', 'checkbox'].includes(type);
            const needsFileConfig = type === 'file' || type === 'pas_foto' || type === 'gambar';
            const needsTableConfig = type === 'table';
            
            optionsContainer.classList.toggle('hidden', !needsOptions);
            fileConfigContainer.classList.toggle('hidden', !needsFileConfig);
            tableConfigContainer.classList.toggle('hidden', !needsTableConfig);

            const gambarDimensionContainer = document.getElementById('gambar_dimension_container');
            if (gambarDimensionContainer) {
                gambarDimensionContainer.classList.toggle('hidden', type !== 'gambar');
            }
            
            if (needsTableConfig && !tableDesignerInitialized) {
                initTableDesigner('create');
                tableDesignerInitialized = true;
            }
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
            const editTableConfigContainer = document.getElementById('edit_table_config_container');
            const needsOptions = ['select', 'radio', 'checkbox'].includes(type);
            const needsFileConfig = type === 'file' || type === 'pas_foto' || type === 'gambar';
            const needsTableConfig = type === 'table';
            
            optionsContainer.classList.toggle('hidden', !needsOptions);
            editFileConfigContainer.classList.toggle('hidden', !needsFileConfig);
            editTableConfigContainer.classList.toggle('hidden', !needsTableConfig);

            const editGambarDimensionContainer = document.getElementById('edit_gambar_dimension_container');
            if (editGambarDimensionContainer) {
                editGambarDimensionContainer.classList.toggle('hidden', type !== 'gambar');
            }
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

            // Setup gambar dimensions config
            const imgWidth = (field.options && field.options.img_width) ? field.options.img_width : '';
            const imgHeight = (field.options && field.options.img_height) ? field.options.img_height : '';
            document.getElementById('edit_img_width').value = imgWidth;
            document.getElementById('edit_img_height').value = imgHeight;

            // Setup options
            const optionsList = document.getElementById('edit_options_list');
            optionsList.innerHTML = '';
            if (field.options && Array.isArray(field.options) && field.type !== 'table') {
                field.options.forEach((opt, index) => {
                    addEditOption(opt);
                });
            } else {
                addEditOption();
            }

            // Setup table config if type is table
            if (field.type === 'table') {
                let tableData = (field.options && field.options.table_data) ? field.options.table_data : null;
                // table_data may be stored as a nested JSON string — parse it if needed
                if (tableData && typeof tableData === 'string') {
                    try { tableData = JSON.parse(tableData); } catch(e) { tableData = null; }
                }
                initTableDesigner('edit', tableData);
            }

            toggleEditOptions();
            document.getElementById('editModal').classList.remove('hidden');
        }

        // ====================================================================
        // TABLE GRID DESIGNER — proper merge/split engine
        // ====================================================================
        // Internal representation: a flat 2D array [numRows][numCols].
        // Each cell: { content, is_input, input_type, input_name, colspan, rowspan, skipped }
        // "skipped" = true means this slot is absorbed by a merged parent; we don't render it.
        // ====================================================================
        let tableDesigners = {
            create: { numRows: 2, numCols: 3, grid: [], selectedRow: -1, selectedCol: -1 },
            edit:   { numRows: 2, numCols: 3, grid: [], selectedRow: -1, selectedCol: -1 }
        };

        // ── helpers ──────────────────────────────────────────────────────────
        function makeCell(r, c) {
            return { content: `Kolom ${c+1}`, is_input: false, input_type: 'text',
                     input_name: `cell_${r}_${c}`, dynamic_var: '', colspan: 1, rowspan: 1, skipped: false, fmt: {} };
        }

        // Build a fresh grid, carrying over existing cell data where possible.
        function buildGrid(mode, numRows, numCols, oldGrid) {
            const g = [];
            for (let r = 0; r < numRows; r++) {
                g.push([]);
                for (let c = 0; c < numCols; c++) {
                    const existing = oldGrid && oldGrid[r] && oldGrid[r][c];
                    if (existing) {
                        // Clip spans so they don't exceed new dimensions
                        const cs = Math.min(existing.colspan || 1, numCols - c);
                        const rs = Math.min(existing.rowspan || 1, numRows - r);
                        g[r].push(Object.assign({}, existing, { colspan: cs, rowspan: rs }));
                    } else {
                        g[r].push(makeCell(r, c));
                    }
                }
            }
            // Recompute skipped flags
            recomputeSkipped(g, numRows, numCols);
            return g;
        }

        // Mark cells covered by spans as skipped.
        function recomputeSkipped(g, numRows, numCols) {
            // First clear all
            for (let r = 0; r < numRows; r++)
                for (let c = 0; c < numCols; c++)
                    if (g[r] && g[r][c]) g[r][c].skipped = false;
            // Then mark cells dominated by a span
            for (let r = 0; r < numRows; r++) {
                for (let c = 0; c < numCols; c++) {
                    const cell = g[r] && g[r][c];
                    if (!cell || cell.skipped) continue;
                    const cs = cell.colspan || 1;
                    const rs = cell.rowspan || 1;
                    if (cs > 1 || rs > 1) {
                        for (let dr = 0; dr < rs; dr++) {
                            for (let dc = 0; dc < cs; dc++) {
                                if (dr === 0 && dc === 0) continue;
                                if (g[r+dr] && g[r+dr][c+dc]) {
                                    g[r+dr][c+dc].skipped = true;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Convert sparse grid (with skipped cells) into the rows array format
        // that the backend expects: only non-skipped cells per row.
        function gridToRows(g, numRows) {
            const rows = [];
            for (let r = 0; r < numRows; r++) {
                const row = [];
                for (let c = 0; c < (g[r] || []).length; c++) {
                    const cell = g[r][c];
                    if (!cell.skipped) {
                        row.push({
                            content:    cell.content,
                            is_input:   cell.is_input,
                            input_type: cell.input_type,
                            input_name: cell.input_name,
                            dynamic_var: cell.dynamic_var || '',
                            colspan:    cell.colspan,
                            rowspan:    cell.rowspan,
                            fmt:        cell.fmt || {}
                        });
                    }
                }
                rows.push(row);
            }
            return rows;
        }

        // Rebuild flat grid from the saved rows format (which has no skipped cells).
        function rowsToGrid(rows) {
            const numRows = rows.length;
            const numCols = rows.reduce((mx, row) => {
                const w = row.reduce((s, c) => s + (c.colspan || 1), 0);
                return Math.max(mx, w);
            }, 0);
            const g = [];
            for (let r = 0; r < numRows; r++) {
                g.push(new Array(numCols).fill(null).map((_, c) => makeCell(r, c)));
            }
            // Fill in cells from saved rows
            for (let r = 0; r < numRows; r++) {
                let col = 0;
                for (const cell of rows[r]) {
                    // Skip already-skipped slots
                    while (col < numCols && g[r][col] && g[r][col].skipped) col++;
                    if (col >= numCols) break;
                    Object.assign(g[r][col], cell, { skipped: false });
                    col += (cell.colspan || 1);
                }
            }
            recomputeSkipped(g, numRows, numCols);
            return { grid: g, numRows, numCols };
        }

        // ── init (used when first loading saved data) ────────────────────────
        function initTableDesigner(mode, loadedData = null) {
            const d = tableDesigners[mode];
            const rowsInput = document.getElementById(mode === 'create' ? 'table_rows' : 'edit_table_rows');
            const colsInput = document.getElementById(mode === 'create' ? 'table_cols' : 'edit_table_cols');
            const fontSelect = document.getElementById(mode === 'create' ? 'table_font_family' : 'edit_table_font_family');

            d.selectedRow = -1;
            d.selectedCol = -1;
            document.getElementById(`cell_configurator_${mode}`).classList.add('hidden');

            if (loadedData && loadedData.fontFamily && fontSelect) {
                fontSelect.value = loadedData.fontFamily;
            } else if (fontSelect) {
                fontSelect.value = '';
            }

            if (loadedData && loadedData.rows && loadedData.rows.length) {
                const r = rowsToGrid(loadedData.rows);
                d.grid    = r.grid;
                d.numRows = r.numRows;
                d.numCols = r.numCols;
                rowsInput.value = d.numRows;
                colsInput.value = d.numCols;
            } else {
                d.numRows = parseInt(rowsInput.value) || 2;
                d.numCols = parseInt(colsInput.value) || 3;
                // If grid already exists (e.g. toggling tab), preserve it; otherwise build fresh
                if (d.grid && d.grid.length > 0) {
                    d.grid = buildGrid(mode, d.numRows, d.numCols, d.grid);
                } else {
                    d.grid = buildGrid(mode, d.numRows, d.numCols, null);
                }
            }

            renderPreviewGrid(mode);
            serializeTableDesign(mode);
        }

        // ── resize (add/remove rows or cols while keeping existing design) ────
        function resizeTableDesigner(mode) {
            const d = tableDesigners[mode];
            const rowsInput = document.getElementById(mode === 'create' ? 'table_rows' : 'edit_table_rows');
            const colsInput = document.getElementById(mode === 'create' ? 'table_cols' : 'edit_table_cols');

            let newRows = parseInt(rowsInput.value) || 2;
            let newCols = parseInt(colsInput.value) || 3;

            if (newRows > 150) { newRows = 150; rowsInput.value = 150; }
            if (newRows < 1) { newRows = 1; rowsInput.value = 1; }
            if (newCols > 150) { newCols = 150; colsInput.value = 150; }
            if (newCols < 1) { newCols = 1; colsInput.value = 1; }

            // Clamp selected cell if it falls outside new bounds
            if (d.selectedRow >= newRows || d.selectedCol >= newCols) {
                d.selectedRow = -1;
                d.selectedCol = -1;
                document.getElementById(`cell_configurator_${mode}`).classList.add('hidden');
            }

            // Build new grid carrying over all existing cell data
            const oldGrid = (d.grid && d.grid.length > 0) ? d.grid : null;
            d.numRows = newRows;
            d.numCols = newCols;
            d.grid = buildGrid(mode, newRows, newCols, oldGrid);

            renderPreviewGrid(mode);
            serializeTableDesign(mode);
        }

        // ── reset (wipe grid and start fresh from current row/col inputs) ────
        function resetTableDesigner(mode) {
            if (!confirm('Reset grid? Semua desain yang sudah dibuat akan dihapus.')) return;
            const d = tableDesigners[mode];
            const rowsInput = document.getElementById(mode === 'create' ? 'table_rows' : 'edit_table_rows');
            const colsInput = document.getElementById(mode === 'create' ? 'table_cols' : 'edit_table_cols');

            d.selectedRow = -1;
            d.selectedCol = -1;
            document.getElementById(`cell_configurator_${mode}`).classList.add('hidden');

            let newRows = parseInt(rowsInput.value) || 2;
            let newCols = parseInt(colsInput.value) || 3;

            if (newRows > 150) { newRows = 150; rowsInput.value = 150; }
            if (newRows < 1) { newRows = 1; rowsInput.value = 1; }
            if (newCols > 150) { newCols = 150; colsInput.value = 150; }
            if (newCols < 1) { newCols = 1; colsInput.value = 1; }

            d.numRows = newRows;
            d.numCols = newCols;
            d.grid = buildGrid(mode, d.numRows, d.numCols, null);

            renderPreviewGrid(mode);
            serializeTableDesign(mode);
        }

        // ── render ────────────────────────────────────────────────────────────
        function renderPreviewGrid(mode) {
            const d = tableDesigners[mode];
            const tableEl = document.getElementById(
                mode === 'create' ? 'table_designer_preview_create' : 'edit_table_designer_preview_edit'
            );
            if (!tableEl) return;
            tableEl.innerHTML = '';

            const fontSelect = document.getElementById(mode === 'create' ? 'table_font_family' : 'edit_table_font_family');
            if (fontSelect && fontSelect.value) {
                tableEl.style.fontFamily = fontSelect.value;
            } else {
                tableEl.style.fontFamily = '';
            }

            for (let r = 0; r < d.numRows; r++) {
                const tr = document.createElement('tr');
                for (let c = 0; c < d.numCols; c++) {
                    const cell = d.grid[r] && d.grid[r][c];
                    if (!cell || cell.skipped) continue;

                    const td = document.createElement('td');
                    td.setAttribute('colspan', cell.colspan || 1);
                    td.setAttribute('rowspan', cell.rowspan || 1);

                    // Apply cell formatting
                    const fmt = cell.fmt || {};
                    let baseCss = 'border:1px solid #d1d5db;padding:8px 10px;cursor:pointer;transition:background .15s;min-width:60px;';
                    if (fmt.width) baseCss += `width:${fmt.width}%;`;
                    if (fmt.bgColor && fmt.bgColor !== '#ffffff') baseCss += `background-color:${fmt.bgColor};`;
                    if (fmt.color && fmt.color !== '#000000') baseCss += `color:${fmt.color};`;
                    if (fmt.fontSize) baseCss += `font-size:${fmt.fontSize};`;
                    if (fmt.bold) baseCss += 'font-weight:700;';
                    if (fmt.italic) baseCss += 'font-style:italic;';
                    if (fmt.underline) baseCss += 'text-decoration:underline;';
                    baseCss += `text-align:${fmt.align || 'center'};`;
                    td.style.cssText = baseCss;

                    const isSelected = d.selectedRow === r && d.selectedCol === c;
                    if (isSelected) {
                        td.style.background = '#e0e7ff';
                        td.style.outline = '2px solid #6366f1';
                    }

                    if (cell.is_input) {
                        td.innerHTML = `<span style="background:#f3e8ff;color:#7e22ce;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;">${cell.input_name}</span>`;
                    } else {
                        if (!fmt.fontWeight) td.style.fontWeight = fmt.bold ? '700' : '600';
                        if (!fmt.fontSize) td.style.fontSize = '11px';
                        td.textContent = cell.content || '-';
                    }

                    if (cell.colspan > 1 || cell.rowspan > 1) {
                        const badge = document.createElement('span');
                        badge.style.cssText = 'display:block;font-size:9px;color:#9ca3af;margin-top:2px;';
                        badge.textContent = `${cell.colspan}×${cell.rowspan}`;
                        td.appendChild(badge);
                    }

                    td.onclick = () => selectCell(mode, r, c);
                    tr.appendChild(td);
                }
                tableEl.appendChild(tr);
            }
        }

        // ── select cell ───────────────────────────────────────────────────────
        function selectCell(mode, r, c) {
            const d = tableDesigners[mode];
            d.selectedRow = r;
            d.selectedCol = c;
            renderPreviewGrid(mode);

            const cell = d.grid[r][c];
            const cfg = document.getElementById(`cell_configurator_${mode}`);
            cfg.classList.remove('hidden');

            document.getElementById(`selected_cell_label_${mode}`).textContent = `[Baris ${r+1}, Kolom ${c+1}]`;
            const infoEl = document.getElementById(`cell_span_info_${mode}`);
            if (infoEl) infoEl.textContent = `colspan=${cell.colspan}, rowspan=${cell.rowspan}`;

            document.getElementById(`cell_content_${mode}`).value    = cell.content || '';
            document.getElementById(`cell_is_input_${mode}`).checked  = cell.is_input || false;
            document.getElementById(`cell_input_type_${mode}`).value  = cell.input_type || 'text';
            document.getElementById(`cell_input_name_${mode}`).value  = cell.input_name || `cell_${r}_${c}`;
            document.getElementById(`cell_dynamic_var_${mode}`).value = cell.dynamic_var || '';

            // Load formatting
            const fmt = cell.fmt || {};
            syncFmtButtons(mode, fmt);
            const fsEl = document.getElementById(`cell_font_size_${mode}`);
            if (fsEl) fsEl.value = fmt.fontSize || '';
            const wEl = document.getElementById(`cell_width_${mode}`);
            if (wEl) wEl.value = fmt.width || '';
            const tcEl = document.getElementById(`cell_text_color_${mode}`);
            if (tcEl) tcEl.value = fmt.color || '#000000';
            const bcEl = document.getElementById(`cell_bg_color_${mode}`);
            if (bcEl) bcEl.value = fmt.bgColor || '#ffffff';

            const inputOpts = document.getElementById(`cell_input_options_${mode}`);
            cell.is_input ? inputOpts.classList.remove('hidden') : inputOpts.classList.add('hidden');
        }

        // ── sync formatting toggle buttons UI ────────────────────────────────
        function syncFmtButtons(mode, fmt) {
            const active = 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 border-indigo-300 dark:border-indigo-600';
            const inactive = 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600';
            ['bold','italic','underline'].forEach(prop => {
                const btn = document.getElementById(`cell_fmt_${prop}_${mode}`);
                if (!btn) return;
                btn.className = btn.className.replace(/bg-\S+|text-\S+|border-\S+/g, '').trim();
                const classes = fmt[prop] ? active : inactive;
                btn.classList.add(...classes.split(' '));
            });
            const alignMap = { left: 'align_left', center: 'align_center', right: 'align_right' };
            Object.entries(alignMap).forEach(([val, key]) => {
                const btn = document.getElementById(`cell_fmt_${key}_${mode}`);
                if (!btn) return;
                btn.className = btn.className.replace(/bg-\S+|text-\S+|border-\S+/g, '').trim();
                const classes = (fmt.align === val) ? active : inactive;
                btn.classList.add(...classes.split(' '));
            });
        }

        // ── toggle bold/italic/underline ─────────────────────────────────────
        function toggleCellFormat(mode, prop) {
            const d = tableDesigners[mode];
            const r = d.selectedRow, c = d.selectedCol;
            if (r === -1) return;
            const cell = d.grid[r][c];
            if (!cell.fmt) cell.fmt = {};
            cell.fmt[prop] = !cell.fmt[prop];
            syncFmtButtons(mode, cell.fmt);
            renderPreviewGrid(mode);
            serializeTableDesign(mode);
        }

        // ── set text alignment ────────────────────────────────────────────────
        function setCellAlign(mode, align) {
            const d = tableDesigners[mode];
            const r = d.selectedRow, c = d.selectedCol;
            if (r === -1) return;
            const cell = d.grid[r][c];
            if (!cell.fmt) cell.fmt = {};
            cell.fmt.align = (cell.fmt.align === align) ? '' : align;
            syncFmtButtons(mode, cell.fmt);
            renderPreviewGrid(mode);
            serializeTableDesign(mode);
        }

        // ── reset color ───────────────────────────────────────────────────────
        function resetCellColor(mode, type) {
            const d = tableDesigners[mode];
            const r = d.selectedRow, c = d.selectedCol;
            if (r === -1) return;
            const cell = d.grid[r][c];
            if (!cell.fmt) cell.fmt = {};
            if (type === 'text') {
                cell.fmt.color = '';
                const el = document.getElementById(`cell_text_color_${mode}`);
                if (el) el.value = '#000000';
            } else {
                cell.fmt.bgColor = '';
                const el = document.getElementById(`cell_bg_color_${mode}`);
                if (el) el.value = '#ffffff';
            }
            renderPreviewGrid(mode);
            serializeTableDesign(mode);
        }

        // ── update cell properties ────────────────────────────────────────────
        function updateCellProperties(mode) {
            const d = tableDesigners[mode];
            const r = d.selectedRow, c = d.selectedCol;
            if (r === -1) return;
            const cell = d.grid[r][c];

            cell.content    = document.getElementById(`cell_content_${mode}`).value;
            cell.is_input   = document.getElementById(`cell_is_input_${mode}`).checked;
            cell.input_type = document.getElementById(`cell_input_type_${mode}`).value;
            cell.input_name = document.getElementById(`cell_input_name_${mode}`).value.toLowerCase().replace(/[^a-z0-9_]/g, '');
            cell.dynamic_var = document.getElementById(`cell_dynamic_var_${mode}`).value;

            // Collect formatting
            if (!cell.fmt) cell.fmt = {};
            const fsEl = document.getElementById(`cell_font_size_${mode}`);
            if (fsEl) cell.fmt.fontSize = fsEl.value;
            const wEl = document.getElementById(`cell_width_${mode}`);
            if (wEl) cell.fmt.width = wEl.value ? parseInt(wEl.value) : '';
            const tcEl = document.getElementById(`cell_text_color_${mode}`);
            if (tcEl) cell.fmt.color = tcEl.value;
            const bcEl = document.getElementById(`cell_bg_color_${mode}`);
            if (bcEl) cell.fmt.bgColor = bcEl.value;

            const inputOpts = document.getElementById(`cell_input_options_${mode}`);
            cell.is_input ? inputOpts.classList.remove('hidden') : inputOpts.classList.add('hidden');

            const infoEl = document.getElementById(`cell_span_info_${mode}`);
            if (infoEl) infoEl.textContent = `colspan=${cell.colspan}, rowspan=${cell.rowspan}`;

            renderPreviewGrid(mode);
            serializeTableDesign(mode);
        }

        // ── merge right ───────────────────────────────────────────────────────
        function mergeRight(mode) {
            const d = tableDesigners[mode];
            const r = d.selectedRow, c = d.selectedCol;
            if (r === -1) { alert('Pilih sel terlebih dahulu.'); return; }

            const cell = d.grid[r][c];
            const nextC = c + cell.colspan;
            if (nextC >= d.numCols) { alert('Tidak ada kolom di sebelah kanan yang bisa digabungkan.'); return; }

            // Check that target cols in ALL rows covered by this cell's rowspan are not already skipped by another parent
            for (let dr = 0; dr < (cell.rowspan || 1); dr++) {
                const targetCell = d.grid[r + dr] && d.grid[r + dr][nextC];
                if (!targetCell || (targetCell.skipped && !_isSkippedByCell(d.grid, r + dr, nextC, r, c))) {
                    alert('Sel di sebelah kanan sudah digabung dengan sel lain. Pisahkan dulu sebelum menggabung.'); return;
                }
            }

            // Increase colspan and mark absorbed cells as skipped
            cell.colspan = (cell.colspan || 1) + (d.grid[r][nextC].colspan || 1);
            recomputeSkipped(d.grid, d.numRows, d.numCols);

            renderPreviewGrid(mode);
            serializeTableDesign(mode);
            selectCell(mode, r, c);
        }

        // ── merge down ────────────────────────────────────────────────────────
        function mergeDown(mode) {
            const d = tableDesigners[mode];
            const r = d.selectedRow, c = d.selectedCol;
            if (r === -1) { alert('Pilih sel terlebih dahulu.'); return; }

            const cell = d.grid[r][c];
            const nextR = r + cell.rowspan;
            if (nextR >= d.numRows) { alert('Tidak ada baris di bawah yang bisa digabungkan.'); return; }

            // Check cells in target row are not already absorbed by another parent
            for (let dc = 0; dc < (cell.colspan || 1); dc++) {
                const targetCell = d.grid[nextR] && d.grid[nextR][c + dc];
                if (!targetCell || (targetCell.skipped && !_isSkippedByCell(d.grid, nextR, c + dc, r, c))) {
                    alert('Sel di bawah sudah digabung dengan sel lain. Pisahkan dulu sebelum menggabung.'); return;
                }
            }

            cell.rowspan = (cell.rowspan || 1) + (d.grid[nextR][c].rowspan || 1);
            recomputeSkipped(d.grid, d.numRows, d.numCols);

            renderPreviewGrid(mode);
            serializeTableDesign(mode);
            selectCell(mode, r, c);
        }

        // Check if (tr, tc) is skipped because of parent (pr, pc)
        function _isSkippedByCell(grid, tr, tc, pr, pc) {
            const parent = grid[pr] && grid[pr][pc];
            if (!parent) return false;
            const cs = parent.colspan || 1, rs = parent.rowspan || 1;
            return tr >= pr && tr < pr + rs && tc >= pc && tc < pc + cs;
        }

        // ── split cell ────────────────────────────────────────────────────────
        function splitCell(mode) {
            const d = tableDesigners[mode];
            const r = d.selectedRow, c = d.selectedCol;
            if (r === -1) { alert('Pilih sel terlebih dahulu.'); return; }

            const cell = d.grid[r][c];
            if ((cell.colspan || 1) === 1 && (cell.rowspan || 1) === 1) {
                alert('Sel ini tidak dalam kondisi tergabung.'); return;
            }

            // Release all absorbed cells
            const cs = cell.colspan || 1, rs = cell.rowspan || 1;
            for (let dr = 0; dr < rs; dr++) {
                for (let dc = 0; dc < cs; dc++) {
                    if (dr === 0 && dc === 0) continue;
                    if (d.grid[r+dr] && d.grid[r+dr][c+dc]) {
                        const released = d.grid[r+dr][c+dc];
                        released.skipped  = false;
                        released.colspan  = 1;
                        released.rowspan  = 1;
                        released.content  = `Kolom ${c+dc+1}`;
                        released.is_input = false;
                        released.input_name = `cell_${r+dr}_${c+dc}`;
                    }
                }
            }
            cell.colspan = 1;
            cell.rowspan = 1;
            recomputeSkipped(d.grid, d.numRows, d.numCols);

            renderPreviewGrid(mode);
            serializeTableDesign(mode);
            selectCell(mode, r, c);
        }

        // ── insert row at position ────────────────────────────────────────────
        function insertRowAt(mode, insertPos) {
            const d = tableDesigners[mode];
            const rowsInput = document.getElementById(mode === 'create' ? 'table_rows' : 'edit_table_rows');

            if (insertPos < 0) insertPos = 0;
            if (insertPos > d.numRows) insertPos = d.numRows;
            if (d.numRows >= 150) { alert('Maksimal 150 baris.'); return; }

            // Adjust rowspans of cells whose span crosses the insertion point
            for (let r = 0; r < d.numRows; r++) {
                for (let c = 0; c < d.numCols; c++) {
                    const cell = d.grid[r] && d.grid[r][c];
                    if (!cell || cell.skipped) continue;
                    const rs = cell.rowspan || 1;
                    // Cell starts before insertPos and its span reaches into or past it
                    if (r < insertPos && r + rs > insertPos) {
                        cell.rowspan = rs + 1;
                    }
                }
            }

            // Build a fresh row for the new position
            const newRow = [];
            for (let c = 0; c < d.numCols; c++) {
                newRow.push(makeCell(insertPos, c));
            }

            // Splice the new row in
            d.grid.splice(insertPos, 0, newRow);
            d.numRows++;
            rowsInput.value = d.numRows;

            // Shift selected cell down if it was at or below the insertion point
            if (d.selectedRow >= insertPos) d.selectedRow++;

            recomputeSkipped(d.grid, d.numRows, d.numCols);
            renderPreviewGrid(mode);
            serializeTableDesign(mode);

            // Re-select the same cell (now shifted)
            if (d.selectedRow >= 0 && d.selectedCol >= 0) {
                selectCell(mode, d.selectedRow, d.selectedCol);
            }
        }

        // ── insert col at position ────────────────────────────────────────────
        function insertColAt(mode, insertPos) {
            const d = tableDesigners[mode];
            const colsInput = document.getElementById(mode === 'create' ? 'table_cols' : 'edit_table_cols');

            if (insertPos < 0) insertPos = 0;
            if (insertPos > d.numCols) insertPos = d.numCols;
            if (d.numCols >= 150) { alert('Maksimal 150 kolom.'); return; }

            // Adjust colspans of cells whose span crosses the insertion point
            for (let r = 0; r < d.numRows; r++) {
                for (let c = 0; c < d.numCols; c++) {
                    const cell = d.grid[r] && d.grid[r][c];
                    if (!cell || cell.skipped) continue;
                    const cs = cell.colspan || 1;
                    if (c < insertPos && c + cs > insertPos) {
                        cell.colspan = cs + 1;
                    }
                }
            }

            // Splice a new cell into each row at insertPos
            for (let r = 0; r < d.numRows; r++) {
                d.grid[r].splice(insertPos, 0, makeCell(r, insertPos));
            }
            d.numCols++;
            colsInput.value = d.numCols;

            // Shift selected cell right if it was at or past the insertion point
            if (d.selectedCol >= insertPos) d.selectedCol++;

            recomputeSkipped(d.grid, d.numRows, d.numCols);
            renderPreviewGrid(mode);
            serializeTableDesign(mode);

            if (d.selectedRow >= 0 && d.selectedCol >= 0) {
                selectCell(mode, d.selectedRow, d.selectedCol);
            }
        }

        // ── delete selected row ───────────────────────────────────────────────
        function deleteRow(mode) {
            const d = tableDesigners[mode];
            const r = d.selectedRow;
            if (r === -1) { alert('Pilih sel terlebih dahulu.'); return; }
            if (d.numRows <= 1) { alert('Tidak dapat menghapus baris terakhir.'); return; }

            // Safety: warn if any visible cell in this row has rowspan > 1 spanning downward
            for (let c = 0; c < d.numCols; c++) {
                const cell = d.grid[r] && d.grid[r][c];
                if (cell && !cell.skipped && (cell.rowspan || 1) > 1) {
                    alert('Baris ini memiliki sel yang digabung ke bawah. Pisahkan sel terlebih dahulu sebelum menghapus baris.'); return;
                }
            }

            // Shrink rowspan of cells that span across the deleted row
            for (let rr = 0; rr < d.numRows; rr++) {
                for (let c = 0; c < d.numCols; c++) {
                    const cell = d.grid[rr] && d.grid[rr][c];
                    if (!cell || cell.skipped) continue;
                    const rs = cell.rowspan || 1;
                    if (rr < r && rr + rs > r) {
                        cell.rowspan = Math.max(1, rs - 1);
                    }
                }
            }

            d.grid.splice(r, 1);
            d.numRows--;
            document.getElementById(mode === 'create' ? 'table_rows' : 'edit_table_rows').value = d.numRows;

            d.selectedRow = -1;
            d.selectedCol = -1;
            document.getElementById(`cell_configurator_${mode}`).classList.add('hidden');

            recomputeSkipped(d.grid, d.numRows, d.numCols);
            renderPreviewGrid(mode);
            serializeTableDesign(mode);
        }

        // ── delete selected col ───────────────────────────────────────────────
        function deleteCol(mode) {
            const d = tableDesigners[mode];
            const c = d.selectedCol;
            if (c === -1) { alert('Pilih sel terlebih dahulu.'); return; }
            if (d.numCols <= 1) { alert('Tidak dapat menghapus kolom terakhir.'); return; }

            // Safety: warn if any visible cell in this col has colspan > 1 spanning rightward
            for (let r = 0; r < d.numRows; r++) {
                const cell = d.grid[r] && d.grid[r][c];
                if (cell && !cell.skipped && (cell.colspan || 1) > 1) {
                    alert('Kolom ini memiliki sel yang digabung ke kanan. Pisahkan sel terlebih dahulu sebelum menghapus kolom.'); return;
                }
            }

            // Shrink colspan of cells that span across the deleted column
            for (let r = 0; r < d.numRows; r++) {
                for (let cc = 0; cc < d.numCols; cc++) {
                    const cell = d.grid[r] && d.grid[r][cc];
                    if (!cell || cell.skipped) continue;
                    const cs = cell.colspan || 1;
                    if (cc < c && cc + cs > c) {
                        cell.colspan = Math.max(1, cs - 1);
                    }
                }
            }

            for (let r = 0; r < d.numRows; r++) {
                d.grid[r].splice(c, 1);
            }
            d.numCols--;
            document.getElementById(mode === 'create' ? 'table_cols' : 'edit_table_cols').value = d.numCols;

            d.selectedRow = -1;
            d.selectedCol = -1;
            document.getElementById(`cell_configurator_${mode}`).classList.add('hidden');

            recomputeSkipped(d.grid, d.numRows, d.numCols);
            renderPreviewGrid(mode);
            serializeTableDesign(mode);
        }

        // ── serialize ─────────────────────────────────────────────────────────
        function serializeTableDesign(mode) {
            const d = tableDesigners[mode];
            const fontSelect = document.getElementById(mode === 'create' ? 'table_font_family' : 'edit_table_font_family');
            const fontFamily = fontSelect ? fontSelect.value : '';
            const rows = gridToRows(d.grid, d.numRows);
            const json = JSON.stringify({ fontFamily, rows });
            document.getElementById(
                mode === 'create' ? 'table_data_json_create' : 'edit_table_data_json_edit'
            ).value = json;
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
            const titles = { 'global': 'Global Form', 'rekom': 'Rekom Form', 'izin': 'Izin Form', 'bo': 'BO Form' };
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
            const varSectionBo = document.getElementById('var-section-bo');

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
                
                // Show Global + Rekom variables + BO variables
                if (varSectionGlobal) varSectionGlobal.classList.remove('hidden');
                if (varSectionRekom) varSectionRekom.classList.remove('hidden');
                if (varSectionIzin) varSectionIzin.classList.add('hidden');
                if (varSectionBo) varSectionBo.classList.remove('hidden');
            } else if (tabId === 'izin') {
                if (templateContainer) templateContainer.classList.remove('hidden');
                if (sequenceContainer) sequenceContainer.classList.remove('hidden');
                if (opdFieldStatus) opdFieldStatus.classList.add('hidden');
                if (opdTemplateStatus) opdTemplateStatus.classList.add('hidden');
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
                
                // Show Global + Izin variables + BO variables
                if (varSectionGlobal) varSectionGlobal.classList.remove('hidden');
                if (varSectionRekom) varSectionRekom.classList.add('hidden');
                if (varSectionIzin) varSectionIzin.classList.remove('hidden');
                if (varSectionBo) varSectionBo.classList.remove('hidden');
            } else if (tabId === 'bo') {
                if (templateContainer) templateContainer.classList.add('hidden');
                if (sequenceContainer) sequenceContainer.classList.add('hidden');
                if (opdFieldStatus) opdFieldStatus.classList.add('hidden');
                if (opdTemplateStatus) opdTemplateStatus.classList.add('hidden');
                if (varSectionBo) varSectionBo.classList.add('hidden');
            } else {
                if (templateContainer) templateContainer.classList.add('hidden');
                if (sequenceContainer) sequenceContainer.classList.add('hidden');
                if (opdFieldStatus) opdFieldStatus.classList.add('hidden');
                if (opdTemplateStatus) opdTemplateStatus.classList.add('hidden');
                if (varSectionBo) varSectionBo.classList.add('hidden');
            }        }

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

        function insertVarPlaceholder(mode, selectEl) {
            const val = selectEl.value;
            if (val) {
                document.getElementById(`cell_dynamic_var_${mode}`).value = val;
                updateCellProperties(mode);
                selectEl.value = ''; // reset select
            }
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    </div> <!-- End form-builder-app -->
</x-layout>
