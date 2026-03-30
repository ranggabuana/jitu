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
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tambah field baru ke formulir</p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('perijinan.form-field.store', $perijinan->id) }}" method="POST"
                    class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="order" value="{{ $perijinan->formFields->count() + 1 }}">

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
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Required</span>
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
                                Daftar Field
                                <span
                                    class="text-gray-500 dark:text-gray-400 font-normal">({{ $perijinan->formFields->count() }})</span>
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Urutkan field dengan drag & drop</p>
                        </div>
                    </div>
                </div>

                @if ($perijinan->formFields->count() > 0)
                    <div id="fields_list" class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($perijinan->formFields as $field)
                            <div class="field-item p-5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                data-field-id="{{ $field->id }}">
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
                                                    <i class="mdi mdi-asterisk"></i> Required
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
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Required</span>
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
            document.getElementById('editForm').action = `/perijinan/${field.perijinan_id}/form-field/${id}`;
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
                    const fieldsList = document.getElementById('fields_list');
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

                    await fetch(`/perijinan/{{ $perijinan->id }}/form-field/reorder`, {
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
    </script>
</x-layout>
