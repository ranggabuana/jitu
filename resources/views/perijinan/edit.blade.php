<x-layout>
    <x-slot:title>Ubah Jenis Perijinan</x-slot:title>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Jenis Perijinan</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Perbarui data jenis perijinan</p>
    </div>

    <!-- CKEditor 5 CSS -->
    <style>
        .ck-editor__editable {
            min-height: 300px;
            border-radius: 0.5rem !important;
        }

        .dark .ck-editor__editable {
            background-color: #1f2937 !important;
            color: #e5e7eb !important;
            border-color: #4b5563 !important;
        }

        .dark .ck-toolbar {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
        }

        .dark .ck-toolbar .ck-button {
            color: #e5e7eb !important;
        }

        .dark .ck-toolbar .ck-button:hover {
            background-color: #4b5563 !important;
        }

        .dark .ck-toolbar .ck-button.ck-on {
            background-color: #4b5563 !important;
        }

        .dark .ck-dropdown__panel {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
        }

        .dark .ck-list__item:hover {
            background-color: #4b5563 !important;
        }

        .dark .ck-input-text {
            background-color: #1f2937 !important;
            color: #e5e7eb !important;
            border-color: #4b5563 !important;
        }
    </style>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 max-w-6xl">
        <form action="{{ route('perijinan.update', $perijinan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="nama_perijinan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nama Perijinan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_perijinan" name="nama_perijinan"
                    value="{{ old('nama_perijinan', $perijinan->nama_perijinan) }}"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_perijinan') border-red-500 @enderror"
                    placeholder="Masukkan nama perijinan" autofocus>
                @error('nama_perijinan')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="dasar_hukum" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Dasar Hukum <span class="text-red-500">*</span>
                </label>
                <textarea id="dasar_hukum" name="dasar_hukum" rows="5"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dasar_hukum') border-red-500 @enderror"
                    placeholder="Masukkan dasar hukum perijinan">{{ old('dasar_hukum', $perijinan->dasar_hukum) }}</textarea>
                @error('dasar_hukum')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="persyaratan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Persyaratan <span class="text-red-500">*</span>
                </label>
                <textarea id="persyaratan" name="persyaratan" rows="5"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('persyaratan') border-red-500 @enderror"
                    placeholder="Masukkan persyaratan perijinan">{{ old('persyaratan', $perijinan->persyaratan) }}</textarea>
                @error('persyaratan')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="prosedur" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Prosedur <span class="text-red-500">*</span>
                </label>
                <textarea id="prosedur" name="prosedur" rows="5"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('prosedur') border-red-500 @enderror"
                    placeholder="Masukkan prosedur perijinan">{{ old('prosedur', $perijinan->prosedur) }}</textarea>
                @error('prosedur')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="informasi_biaya" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Informasi Biaya
                </label>
                <input type="text" id="informasi_biaya" name="informasi_biaya"
                    value="{{ old('informasi_biaya', $perijinan->informasi_biaya) }}"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('informasi_biaya') border-red-500 @enderror"
                    placeholder="Contoh: Rp 150.000 atau Gratis">
                @error('informasi_biaya')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="gambar_alur" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="mdi mdi-chart-timeline-variant text-gray-400 mr-1"></i> Gambar Alur
                </label>
                <div class="flex items-start gap-4">
                    @if ($perijinan->gambar_alur && file_exists(public_path($perijinan->gambar_alur)))
                        <div
                            class="w-48 rounded-lg border-2 border-gray-200 dark:border-gray-600 overflow-hidden flex items-center justify-center bg-gray-50 dark:bg-gray-700">
                            <img src="{{ asset($perijinan->gambar_alur) }}" alt="Gambar Alur"
                                class="max-w-full max-h-full object-contain">
                        </div>
                    @else
                        <div
                            class="w-48 h-32 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center bg-gray-50 dark:bg-gray-700">
                            <div class="text-center text-gray-400">
                                <i class="mdi mdi-image-off text-3xl"></i>
                                <p class="text-xs mt-1">Belum ada gambar</p>
                            </div>
                        </div>
                    @endif
                    <div class="flex-1">
                        <input type="file" name="gambar_alur" id="gambar_alur" accept="image/*"
                            class="block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-medium
                                file:bg-blue-50 file:text-blue-700
                                dark:file:bg-blue-900/30 dark:file:text-blue-400
                                hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50
                                transition-colors cursor-pointer">
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Format: PNG, JPG, JPEG (Max 2MB)
                        </p>
                        @error('gambar_alur')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Update
                </button>
                <a href="{{ route('perijinan.index') }}"
                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <!-- CKEditor 5 Script -->
    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
    <script>
        // Classic Editor configuration with full features
        const editorConfig = {
            toolbar: [
                'heading', '|',
                'fontSize', 'fontFamily', '|',
                'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', '|',
                'link', 'blockQuote', 'insertTable', '|',
                'bulletedList', 'numberedList', '|',
                'outdent', 'indent', '|',
                'alignment', '|',
                'fontColor', 'fontBackgroundColor', '|',
                'removeFormat', '|',
                'undo', 'redo', '|',
                'sourceEditing'
            ],
            heading: {
                options: [{
                        model: 'paragraph',
                        title: 'Paragraph',
                        class: 'ck-heading_paragraph'
                    },
                    {
                        model: 'heading1',
                        view: 'h1',
                        title: 'Heading 1',
                        class: 'ck-heading_heading1'
                    },
                    {
                        model: 'heading2',
                        view: 'h2',
                        title: 'Heading 2',
                        class: 'ck-heading_heading2'
                    },
                    {
                        model: 'heading3',
                        view: 'h3',
                        title: 'Heading 3',
                        class: 'ck-heading_heading3'
                    },
                    {
                        model: 'heading4',
                        view: 'h4',
                        title: 'Heading 4',
                        class: 'ck-heading_heading4'
                    },
                    {
                        model: 'heading5',
                        view: 'h5',
                        title: 'Heading 5',
                        class: 'ck-heading_heading5'
                    },
                    {
                        model: 'heading6',
                        view: 'h6',
                        title: 'Heading 6',
                        class: 'ck-heading_heading6'
                    }
                ]
            },
            fontSize: {
                options: [10, 12, 14, 'default', 18, 20, 22, 24, 28, 32, 36, 40, 48]
            },
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Impact, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
            },
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            alignment: {
                options: ['left', 'right', 'center', 'justify']
            },
            htmlSupport: {
                allow: [{
                    name: /.*/,
                    attributes: true,
                    classes: true,
                    styles: true
                }]
            },
            language: 'id'
        };

        // Initialize editors
        let editors = {};
        let editorsLoaded = 0;
        const totalEditors = 3;

        // Dasar Hukum Editor
        ClassicEditor
            .create(document.querySelector('#dasar_hukum'), editorConfig)
            .then(editor => {
                editors['dasar_hukum'] = editor;
                editorsLoaded++;
                // Real-time sync on change
                editor.model.document.on('change:data', () => {
                    editor.updateSourceElement();
                });
            })
            .catch(error => {
                console.error('Error initializing Dasar Hukum editor:', error);
            });

        // Persyaratan Editor
        ClassicEditor
            .create(document.querySelector('#persyaratan'), editorConfig)
            .then(editor => {
                editors['persyaratan'] = editor;
                editorsLoaded++;
                // Real-time sync on change
                editor.model.document.on('change:data', () => {
                    editor.updateSourceElement();
                });
            })
            .catch(error => {
                console.error('Error initializing Persyaratan editor:', error);
            });

        // Prosedur Editor
        ClassicEditor
            .create(document.querySelector('#prosedur'), editorConfig)
            .then(editor => {
                editors['prosedur'] = editor;
                editorsLoaded++;
                // Real-time sync on change
                editor.model.document.on('change:data', () => {
                    editor.updateSourceElement();
                });
            })
            .catch(error => {
                console.error('Error initializing Prosedur editor:', error);
            });

        // Form submit handler - validate and sync data before submit
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            // Sync all editors before submit
            Object.values(editors).forEach(editor => {
                if (editor) {
                    editor.updateSourceElement();
                }
            });

            // Manual validation for CKEditor fields
            const dasarHukum = document.querySelector('#dasar_hukum').value.trim();
            const persyaratan = document.querySelector('#persyaratan').value.trim();
            const prosedur = document.querySelector('#prosedur').value.trim();

            let hasError = false;

            if (!dasarHukum) {
                alert('Dasar Hukum harus diisi!');
                hasError = true;
            } else if (!persyaratan) {
                alert('Persyaratan harus diisi!');
                hasError = true;
            } else if (!prosedur) {
                alert('Prosedur harus diisi!');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            // Debug: Log form data
            console.log('Form submitting...');
            console.log('dasar_hukum length:', dasarHukum.length);
            console.log('persyaratan length:', persyaratan.length);
            console.log('prosedur length:', prosedur.length);
        });
    </script>
</x-layout>
