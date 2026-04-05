<x-layout>
    <x-slot:title>Tambah Berita</x-slot:title>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Tambah Berita</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Tambah berita baru</p>
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
        <form action="{{ route('berita.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Judul Berita <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('judul') border-red-500 @enderror"
                        placeholder="Masukkan judul berita" autofocus>
                    @error('judul')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Slug <span class="text-gray-400 text-xs">(Opsional, akan digenerate otomatis)</span>
                    </label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror"
                        placeholder="slug-berita">
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="gambar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Gambar <span class="text-gray-400 text-xs">(Opsional, max 2MB)</span>
                </label>
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-3">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
                        <div>
                            <p class="text-sm text-blue-800 dark:text-blue-300 font-medium">Ukuran yang Direkomendasikan</p>
                            <p class="text-xs text-blue-700 dark:text-blue-400">Gambar akan ditampilkan pada slider landing page dengan ukuran <strong>1920 x 600 px</strong> (rasio 16:5). Gunakan ukuran ini agar gambar tampil optimal tanpa terpotong.</p>
                        </div>
                    </div>
                </div>
                <input type="file" id="gambar" name="gambar" accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gambar') border-red-500 @enderror"
                    onchange="previewImage(this)">
                @error('gambar')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <div id="image_preview" class="mt-2 hidden">
                    <img id="preview_img" src="" alt="Preview" class="max-h-48 rounded-lg shadow-md">
                </div>
            </div>

            <div class="mb-6">
                <label for="konten" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Konten Berita <span class="text-red-500">*</span>
                </label>
                <textarea id="konten" name="konten" rows="5"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('konten') border-red-500 @enderror"
                    placeholder="Masukkan konten berita">{{ old('konten') }}</textarea>
                @error('konten')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status" name="status"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                    <option value="tidak_aktif" {{ old('status') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Simpan
                </button>
                <a href="{{ route('berita.index') }}"
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
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
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
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            },
            language: 'id'
        };

        // Initialize editor
        let editors = {};
        
        // Konten Editor
        ClassicEditor
            .create(document.querySelector('#konten'), editorConfig)
            .then(editor => {
                editors['konten'] = editor;
                // Real-time sync on change
                editor.model.document.on('change:data', () => {
                    editor.updateSourceElement();
                });
            })
            .catch(error => {
                console.error('Error initializing Konten editor:', error);
            });

        // Image preview
        function previewImage(input) {
            const preview = document.getElementById('image_preview');
            const previewImg = document.getElementById('preview_img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }

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
            const konten = document.querySelector('#konten').value.trim();

            if (!konten) {
                alert('Konten berita harus diisi!');
                e.preventDefault();
                return false;
            }

            // Debug: Log form data
            console.log('Form submitting...');
            console.log('konten length:', konten.length);
        });
    </script>
</x-layout>
