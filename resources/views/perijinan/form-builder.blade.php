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
                <li class="mr-2">
                    <button onclick="switchTab('global')" id="tab-btn-global" class="tab-btn inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group text-indigo-600 border-indigo-600 dark:text-indigo-500 dark:border-indigo-500">
                        <i id="tab-icon-global" class="mdi mdi-earth mr-2 text-lg text-indigo-600 dark:text-indigo-500"></i>
                        Global Form
                    </button>
                </li>
                <li class="mr-2">
                    <button onclick="switchTab('rekom')" id="tab-btn-rekom" class="tab-btn inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">
                        <i id="tab-icon-rekom" class="mdi mdi-file-document-outline mr-2 text-lg text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300"></i>
                        Rekom Form
                    </button>
                </li>
                <li class="mr-2">
                    <button onclick="switchTab('izin')" id="tab-btn-izin" class="tab-btn inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">
                        <i id="tab-icon-izin" class="mdi mdi-file-certificate-outline mr-2 text-lg text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300"></i>
                        Izin Form
                    </button>
                </li>
            </ul>
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
                                <span class="text-gray-500 dark:text-gray-400 font-normal tab-count" id="count-global">({{ $perijinan->formFields->whereIn('form_type', ['global', null])->count() }})</span>
                                <span class="text-gray-500 dark:text-gray-400 font-normal tab-count hidden" id="count-rekom">({{ $perijinan->formFields->where('form_type', 'rekom')->count() }})</span>
                                <span class="text-gray-500 dark:text-gray-400 font-normal tab-count hidden" id="count-izin">({{ $perijinan->formFields->where('form_type', 'izin')->count() }})</span>
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Urutkan field dengan drag & drop</p>
                        </div>
                    </div>
                </div>

                @if ($perijinan->formFields->count() > 0)
                    @foreach(['global', 'rekom', 'izin'] as $type)
                        <div id="fields_list_{{ $type }}" class="fields-container divide-y divide-gray-100 dark:divide-gray-700 {{ $type === 'global' ? '' : 'hidden' }}">
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
            </div>
        </div>
        </div>

        <!-- Template Editor Container (Hidden by default, shown on rekom and izin tabs) -->
        <div id="template-editor-container" class="mt-6 hidden">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <form action="{{ route('perijinan.templates.update', $perijinan->id) }}" method="POST">
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
                            <button type="button" onclick="resetTemplateToDefault()"
                                class="flex items-center gap-2 text-xs font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 border border-red-200 dark:border-red-800 rounded-lg px-3 py-2 transition-all">
                                <i class="mdi mdi-refresh text-base"></i>
                                Reset ke Default
                            </button>
                        </div>
                    </div>

                    <div id="placeholder-guide" class="hidden border-b border-blue-100 dark:border-blue-900/50 bg-gradient-to-r from-blue-50 to-indigo-50/50 dark:from-blue-950/30 dark:to-indigo-950/20 px-6 py-4">
                        <p class="text-xs text-blue-700 dark:text-blue-300 mb-3 leading-relaxed font-medium">
                            <i class="mdi mdi-information-outline mr-1"></i>
                            Ketikkan kode berikut di dalam dokumen. Kode ini akan <strong>diganti otomatis</strong> dengan data riil pemohon saat surat dicetak:
                        </p>
                        <div class="space-y-4">
                            <!-- Variabel Dasar -->
                            <div>
                                <span class="text-[10px] uppercase tracking-wider font-bold text-blue-800 dark:text-blue-300 block mb-2">Variabel Dasar (Sistem):</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach([
                                        '[NAMA PEMOHON]' => 'Nama Lengkap',
                                        '[NIK]' => 'NIK (KTP)',
                                        '[ALAMAT LENGKAP]' => 'Alamat Pemohon',
                                        '[NO HP]' => 'No. Telepon',
                                        '[EMAIL]' => 'Email',
                                        '[PEKERJAAN]' => 'Pekerjaan',
                                        '[NAMA IZIN]' => 'Jenis Izin',
                                        '[TANGGAL]' => 'Tanggal Pengajuan',
                                        '[NO REGISTRASI]' => 'No. Registrasi',
                                        '[LOGO KABUPATEN]' => 'Logo Kabupaten (Header)',
                                        '[GAMBAR TTE]' => 'Gambar TTE (Tanda Tangan Elektronik)',
                                        '<!-- pagebreak -->' => 'Tambah Halaman Baru (Pemisah)',
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

                            <!-- Variabel Global -->
                            <div class="dynamic-var-section" id="var-section-global">
                                <span class="text-[10px] uppercase tracking-wider font-bold text-emerald-800 dark:text-emerald-300 block mb-2">Variabel dari Global Form:</span>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($perijinan->formFields->where('form_type', 'global') as $field)
                                        <button type="button"
                                            onclick="insertPlaceholder('[{{ strtoupper($field->name) }}]')"
                                            title="{{ $field->label }}"
                                            class="inline-flex items-center gap-1.5 bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-700 hover:border-emerald-600 rounded-lg px-2.5 py-1.5 text-[11px] font-mono font-bold transition-all shadow-sm">
                                            <i class="mdi mdi-plus text-xs"></i>[{{ strtoupper($field->name) }}]
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
                                        <button type="button"
                                            onclick="insertPlaceholder('[{{ strtoupper($field->name) }}]')"
                                            title="{{ $field->label }}"
                                            class="inline-flex items-center gap-1.5 bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-700 text-purple-700 dark:text-purple-300 hover:bg-purple-600 hover:text-white dark:hover:bg-purple-700 hover:border-purple-600 rounded-lg px-2.5 py-1.5 text-[11px] font-mono font-bold transition-all shadow-sm">
                                            <i class="mdi mdi-plus text-xs"></i>[{{ strtoupper($field->name) }}]
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
                                        <button type="button"
                                            onclick="insertPlaceholder('[{{ strtoupper($field->name) }}]')"
                                            title="{{ $field->label }}"
                                            class="inline-flex items-center gap-1.5 bg-white dark:bg-gray-800 border border-indigo-200 dark:border-indigo-700 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-700 hover:border-indigo-600 rounded-lg px-2.5 py-1.5 text-[11px] font-mono font-bold transition-all shadow-sm">
                                            <i class="mdi mdi-plus text-xs"></i>[{{ strtoupper($field->name) }}]
                                        </button>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">Belum ada field di Izin Form</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-0">
                        <div id="tpl-rekom-container" class="hidden">
                            <textarea name="template_surat_rekom" id="editor_rekom" class="w-full focus:outline-none">{{ $perijinan->template_surat_rekom }}</textarea>
                        </div>
                        <div id="tpl-izin-container" class="hidden">
                            <textarea name="template_surat_izin" id="editor_izin" class="w-full focus:outline-none">{{ $perijinan->template_surat_izin }}</textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                        <button type="button" onclick="previewCurrentTemplate()" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2 rounded-lg font-bold transition-all flex items-center gap-2 shadow-sm">
                            <i class="mdi mdi-eye-outline"></i> Pratinjau Dokumen
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold transition-all flex items-center gap-2 shadow-sm">
                            <i class="mdi mdi-content-save"></i> Simpan Template
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pratinjau Dokumen Modal -->
        <div id="modal-doc-preview" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-6xl mx-4 flex flex-col" style="height: 95vh; max-height: 95vh;">
                <!-- Modal header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-t-2xl flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                            <i class="mdi mdi-file-eye-outline text-emerald-600 dark:text-emerald-400 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-800 dark:text-white" id="modal-preview-title">Pratinjau Dokumen</h3>
                            <p class="text-xs text-gray-500">Simulasi tampilan surat saat dicetak. Data ditampilkan dengan data simulasi.</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeDocPreview()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                        <i class="mdi mdi-close text-xl"></i>
                    </button>
                </div>

                <!-- PDF Preview container -->
                <div class="flex-1 overflow-hidden bg-gray-100 dark:bg-gray-950 p-0 relative">
                    <div id="modal-preview-loading" class="absolute inset-0 z-10 flex items-center justify-center bg-white/80 dark:bg-gray-900/80 hidden">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                            <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Menyiapkan PDF...</p>
                        </div>
                    </div>
                    <iframe id="modal-preview-iframe" class="w-full h-full border-0" src="about:blank"></iframe>
                </div>

                <!-- Modal footer -->
                <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-b-2xl flex-shrink-0 flex justify-between items-center">
                    <p class="text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                        <i class="mdi mdi-alert-circle-outline"></i>
                        Data di atas adalah simulasi pratinjau.
                    </p>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="closeDocPreview()"
                            class="px-5 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-all">
                            Tutup Pratinjau
                        </button>
                    </div>
                </div>
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

            // 7. Toggle Template Editor
            const templateContainer = document.getElementById('template-editor-container');
            const editorRekom = document.getElementById('tpl-rekom-container');
            const editorIzin = document.getElementById('tpl-izin-container');
            const editorTitle = document.getElementById('template-editor-title');
            
            // Sections for variables
            const varSectionGlobal = document.getElementById('var-section-global');
            const varSectionRekom = document.getElementById('var-section-rekom');
            const varSectionIzin = document.getElementById('var-section-izin');

            if (tabId === 'rekom') {
                templateContainer.classList.remove('hidden');
                editorRekom.classList.remove('hidden');
                editorIzin.classList.add('hidden');
                if (editorTitle) editorTitle.textContent = 'Rekom';
                
                // Show Global + Rekom variables
                varSectionGlobal.classList.remove('hidden');
                varSectionRekom.classList.remove('hidden');
                varSectionIzin.classList.add('hidden');
            } else if (tabId === 'izin') {
                templateContainer.classList.remove('hidden');
                editorRekom.classList.add('hidden');
                editorIzin.classList.remove('hidden');
                if (editorTitle) editorTitle.textContent = 'Izin';
                
                // Show Global + Izin variables
                varSectionGlobal.classList.remove('hidden');
                varSectionRekom.classList.add('hidden');
                varSectionIzin.classList.remove('hidden');
            } else {
                templateContainer.classList.add('hidden');
            }
        }

        function togglePlaceholderGuide() {
            const guide = document.getElementById('placeholder-guide');
            guide.classList.toggle('hidden');
        }

        function insertPlaceholder(code) {
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

        async function previewCurrentTemplate() {
            const currentTab = localStorage.getItem('formBuilderTab') || 'global';
            if (currentTab !== 'rekom' && currentTab !== 'izin') return;

            const editorId = currentTab === 'rekom' ? 'editor_rekom' : 'editor_izin';
            let content = '';

            if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                content = tinymce.get(editorId).getContent();
            } else {
                content = document.getElementById(editorId).value;
            }

            // Show Modal and Loading
            document.getElementById('modal-preview-title').textContent = 'Pratinjau PDF — ' + (currentTab === 'rekom' ? 'Surat Rekomendasi' : 'Surat Izin');
            const modal = document.getElementById('modal-doc-preview');
            const iframe = document.getElementById('modal-preview-iframe');
            const loading = document.getElementById('modal-preview-loading');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            loading.classList.remove('hidden');
            iframe.src = 'about:blank';

            try {
                const response = await fetch("{{ route('perijinan.preview-template', $perijinan->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        template_type: currentTab,
                        template_content: content
                    })
                });

                if (!response.ok) throw new Error('Gagal mengenerate pratinjau');

                const blob = await response.blob();
                const url = URL.createObjectURL(blob);
                
                iframe.src = url;
                iframe.onload = () => {
                    loading.classList.add('hidden');
                };
            } catch (error) {
                console.error(error);
                loading.classList.add('hidden');
                Swal.fire('Gagal', 'Terjadi kesalahan saat menyiapkan pratinjau PDF.', 'error');
                closeDocPreview();
            }
        }

        function makeElementManageable(el) {
            let isDragging = false;
            let startX, startY, initialLeft, initialTop;

            // Apply styles for resize and basic positioning
            el.style.position = 'relative';
            el.style.cursor = 'move';
            el.style.resize = 'both';
            el.style.overflow = 'hidden'; // Required for 'resize' property to work
            el.style.border = '1px dashed #3b82f6';
            el.style.backgroundColor = 'rgba(59, 130, 246, 0.05)';
            el.style.minWidth = '40px';
            el.style.minHeight = '40px';
            
            // Add a small resize handle indicator at the bottom right
            const handle = document.createElement('div');
            handle.style.position = 'absolute';
            handle.style.right = '0';
            handle.style.bottom = '0';
            handle.style.width = '10px';
            handle.style.height = '10px';
            handle.style.background = 'linear-gradient(135deg, transparent 50%, #3b82f6 50%)';
            handle.style.cursor = 'nwse-resize';
            handle.style.pointerEvents = 'none'; // click goes to parent for resize
            el.appendChild(handle);
            
            el.onmousedown = function(e) {
                // If clicking near the bottom-right corner, assume we want to resize (let browser handle it)
                const rect = el.getBoundingClientRect();
                const isResizeZone = (e.clientX > rect.right - 15 && e.clientY > rect.bottom - 15);
                
                if (isResizeZone) {
                    // Let the default browser behavior handle resizing
                    return;
                }

                isDragging = true;
                startX = e.clientX;
                startY = e.clientY;
                
                // Get current computed style for left/top
                const style = window.getComputedStyle(el);
                initialLeft = parseInt(style.left) || 0;
                initialTop = parseInt(style.top) || 0;

                el.style.zIndex = 1000;

                function onMouseMove(e) {
                    if (!isDragging) return;
                    const dx = e.clientX - startX;
                    const dy = e.clientY - startY;
                    el.style.left = (initialLeft + dx) + 'px';
                    el.style.top = (initialTop + dy) + 'px';
                }

                function onMouseUp() {
                    isDragging = false;
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                }

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            };

            el.ondragstart = function() { return false; };
        }

        function closeDocPreview() {
            const modal = document.getElementById('modal-doc-preview');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        async function applyAndSavePreview() {
            const currentTab = localStorage.getItem('formBuilderTab') || 'global';
            const editorId = currentTab === 'rekom' ? 'editor_rekom' : 'editor_izin';
            
            let content = '';
            if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                content = tinymce.get(editorId).getContent();
            } else {
                content = document.getElementById(editorId).value;
            }

            const managedElements = document.querySelectorAll('#modal-preview-body .preview-manageable');
            
            managedElements.forEach(managedEl => {
                const placeholder = managedEl.dataset.placeholder;
                const width = managedEl.style.width;
                const left = managedEl.style.left || '0px';
                const top = managedEl.style.top || '0px';

                const styleStr = `display: inline-block; width: ${width}; position: relative; left: ${left}; top: ${top}; vertical-align: middle;`;
                
                const escapedPlaceholder = placeholder.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                // Regex to find the placeholder either bare or inside an existing template-wrapper
                const regex = new RegExp(`(<div class="template-wrapper"[^>]*>)?${escapedPlaceholder}(<\\/div>)?`, 'g');
                
                content = content.replace(regex, `<div class="template-wrapper" style="${styleStr}">${placeholder}</div>`);
            });

            // Update TinyMCE
            if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                tinymce.get(editorId).setContent(content);
                tinymce.get(editorId).save();
            } else {
                document.getElementById(editorId).value = content;
            }

            // Show loading and submit
            Swal.fire({
                title: 'Menyimpan Tata Letak...',
                text: 'Perubahan ukuran dan posisi sedang diterapkan.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    const form = document.querySelector('form[action*="templates"]');
                    if (form) form.submit();
                }
            });
        }

        // Close modal on backdrop click
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('modal-doc-preview');
            if (e.target === modal) closeDocPreview();
        });

        const defaultTemplates = {
            rekom: @json(\App\Services\DocumentGenerator::getDefaultSuratRekomTemplate()),
            izin: @json(\App\Services\DocumentGenerator::getDefaultSuratIzinTemplate())
        };

        function resetTemplateToDefault() {
            const currentTab = localStorage.getItem('formBuilderTab') || 'global';
            if (currentTab !== 'rekom' && currentTab !== 'izin') return;

            Swal.fire({
                title: 'Reset Template?',
                text: 'Template ' + (currentTab === 'rekom' ? 'Rekomendasi' : 'Izin') + ' akan dikembalikan ke format bawaan asli. Perubahan yang belum disimpan akan hilang.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const editorId = currentTab === 'rekom' ? 'editor_rekom' : 'editor_izin';
                    const defaultHtml = defaultTemplates[currentTab];

                    if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                        tinymce.get(editorId).setContent(defaultHtml);
                        tinymce.get(editorId).save();
                    } else {
                        document.getElementById(editorId).value = defaultHtml;
                    }

                    // Show loading and submit form
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Template sedang dikembalikan ke bawaan.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            const form = document.querySelector('form[action*="templates"]');
                            if (form) form.submit();
                        }
                    });
                }
            });
        }

        // Initialize tabs on page load
        document.addEventListener('DOMContentLoaded', () => {
            const savedTab = localStorage.getItem('formBuilderTab') || 'global';
            switchTab(savedTab);

            // Init TinyMCE
            if (typeof tinymce !== 'undefined') {
                const tinymceConfigs = {
                    height: 500,
                    menubar: true,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'pagebreak', 'nonbreaking'
                    ],
                    toolbar: 'undo redo | blocks fontfamily fontsize lineheight | ' +
                    'bold italic underline strikethrough | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | pagebreak | help',
                    font_family_formats: 'Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Bookman Old Style=bookman old style,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats',
                    content_style: `
                        body { 
                            font-family: "Times New Roman", serif; 
                            font-size: 12pt; 
                            line-height: 1.5; 
                            max-width: 800px; 
                            margin: 40px auto; 
                            background: #fff; 
                            padding: 60px 80px; 
                            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
                            min-height: 1000px;
                            color: #000;
                        } 
                        html { background: #f4f4f7; }
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
