<x-layout>
    <x-slot:title>Formulir - {{ $perijinan->nama_perijinan }}</x-slot:title>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Formulir Permohonan</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $perijinan->nama_perijinan }}</p>
            </div>
            <a href="{{ route('perijinan.index') }}"
                class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg transition-colors inline-flex items-center gap-2">
                <i class="mdi mdi-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Isian -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <i class="mdi mdi-file-document text-white text-2xl"></i>
                        <h2 class="text-xl font-bold text-white">Formulir Permohonan</h2>
                    </div>
                </div>

                <form action="#" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    
                    @if($perijinan->activeFormFields->count() > 0)
                        @foreach($perijinan->activeFormFields as $field)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ $field->label }}
                                    @if($field->is_required)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                                
                                @php
                                    $inputClass = 'w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-500';
                                    $requiredAttr = $field->is_required ? 'required' : '';
                                @endphp

                                {{-- Text --}}
                                @if($field->type === 'text')
                                    <input type="text" name="{{ $field->name }}" 
                                        placeholder="{{ $field->placeholder }}" 
                                        {{ $requiredAttr }}
                                        class="{{ $inputClass }}">
                                
                                {{-- Textarea --}}
                                @elseif($field->type === 'textarea')
                                    <textarea name="{{ $field->name }}" 
                                        placeholder="{{ $field->placeholder }}" 
                                        {{ $requiredAttr }}
                                        rows="4"
                                        class="{{ $inputClass }}"></textarea>
                                
                                {{-- Number --}}
                                @elseif($field->type === 'number')
                                    <input type="number" name="{{ $field->name }}" 
                                        placeholder="{{ $field->placeholder }}" 
                                        {{ $requiredAttr }}
                                        class="{{ $inputClass }}">
                                
                                {{-- Date --}}
                                @elseif($field->type === 'date')
                                    <input type="date" name="{{ $field->name }}" 
                                        {{ $requiredAttr }}
                                        class="{{ $inputClass }}">
                                
                                {{-- Email --}}
                                @elseif($field->type === 'email')
                                    <input type="email" name="{{ $field->name }}" 
                                        placeholder="{{ $field->placeholder }}" 
                                        {{ $requiredAttr }}
                                        class="{{ $inputClass }}">
                                
                                {{-- Phone --}}
                                @elseif($field->type === 'phone')
                                    <input type="tel" name="{{ $field->name }}" 
                                        placeholder="{{ $field->placeholder }}" 
                                        {{ $requiredAttr }}
                                        class="{{ $inputClass }}">
                                
                                {{-- Select --}}
                                @elseif($field->type === 'select')
                                    <select name="{{ $field->name }}" 
                                        {{ $requiredAttr }}
                                        class="{{ $inputClass }}">
                                        <option value="">-- Pilih {{ $field->label }} --</option>
                                        @if($field->options && is_array($field->options))
                                            @foreach($field->options as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                
                                {{-- Radio --}}
                                @elseif($field->type === 'radio')
                                    <div class="space-y-2">
                                        @if($field->options && is_array($field->options))
                                            @foreach($field->options as $index => $option)
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" name="{{ $field->name }}" 
                                                        value="{{ $option }}"
                                                        {{ $index === 0 && $requiredAttr ? 'required' : '' }}
                                                        class="w-4 h-4 text-purple-600 focus:ring-purple-500">
                                                    <span class="text-gray-700 dark:text-gray-300">{{ $option }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                
                                {{-- Checkbox --}}
                                @elseif($field->type === 'checkbox')
                                    <div class="space-y-2">
                                        @if($field->options && is_array($field->options))
                                            @foreach($field->options as $option)
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="checkbox" name="{{ $field->name }}[]" 
                                                        value="{{ $option }}"
                                                        class="w-4 h-4 text-purple-600 focus:ring-purple-500">
                                                    <span class="text-gray-700 dark:text-gray-300">{{ $option }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>

                                {{-- File --}}
                                @elseif($field->type === 'file')
                                    @php
                                        $acceptAttr = '';
                                        if ($field->file_types) {
                                            $types = array_map(fn($t) => '.' . trim($t), explode(',', $field->file_types));
                                            $acceptAttr = 'accept="' . implode(',', $types) . '"';
                                        }
                                    @endphp
                                    <input type="file" name="{{ $field->name }}"
                                        {{ $requiredAttr }}
                                        {{ $acceptAttr }}
                                        data-max-size="{{ $field->max_file_size ?? 0 }}"
                                        class="{{ $inputClass}} file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                    @if($field->file_types || $field->max_file_size)
                                        <div class="flex items-center gap-2 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            @if($field->file_types)
                                                <span class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                                    <i class="mdi mdi-file-document"></i>
                                                    <span>{{ $field->file_types }}</span>
                                                </span>
                                            @endif
                                            @if($field->max_file_size)
                                                <span class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                                    <i class="mdi mdi-weight"></i>
                                                    <span>Max: {{ number_format($field->max_file_size / 1024, 1) }} MB</span>
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                @endif

                                @if($field->help_text)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <i class="mdi mdi-information"></i>
                                        {{ $field->help_text }}
                                    </p>
                                @endif
                            </div>
                        @endforeach

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit"
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors font-medium flex items-center justify-center gap-2">
                                <i class="mdi mdi-content-save"></i>
                                <span>Kirim Permohonan</span>
                            </button>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="mdi mdi-file-document-outline text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400">Formulir belum tersedia</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                                Admin belum menambahkan field formulir untuk perijinan ini
                            </p>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Sidebar Informasi -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Informasi Pengisian -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    <i class="mdi mdi-information text-blue-500"></i>
                    Informasi Pengisian
                </h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <i class="mdi mdi-pencil text-blue-500 mt-0.5"></i>
                        <p class="text-gray-700 dark:text-gray-300 text-sm">
                            Isi semua field yang ditandai dengan tanda bintang (<span class="text-red-500">*</span>)
                        </p>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="mdi mdi-paperclip text-blue-500 mt-0.5"></i>
                        <p class="text-gray-700 dark:text-gray-300 text-sm">
                            Pastikan semua dokumen persyaratan telah disiapkan
                        </p>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="mdi mdi-check-circle text-blue-500 mt-0.5"></i>
                        <p class="text-gray-700 dark:text-gray-300 text-sm">
                            Periksa kembali data yang telah diisi sebelum mengirim
                        </p>
                    </div>
                </div>
            </div>

            <!-- Daftar Dokumen yang Harus Dilampirkan -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    <i class="mdi mdi-file-check"></i>
                    Dokumen Persyaratan
                </h3>
                @if($perijinan->persyaratan)
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 text-sm">
                        {!! $perijinan->persyaratan !!}
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Belum ada informasi dokumen yang dilampirkan
                    </p>
                @endif
            </div>

            <!-- Kontak Bantuan -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-3">
                    <i class="mdi mdi-headset"></i> Butuh Bantuan?
                </h3>
                <div class="space-y-2 text-sm text-blue-700 dark:text-blue-400">
                    <p><i class="mdi mdi-phone"></i> Telepon: (021) 1234-5678</p>
                    <p><i class="mdi mdi-email"></i> Email: helpdesk@contoh.go.id</p>
                    <p><i class="mdi mdi-message"></i> WhatsApp: 0812-3456-7890</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Actions -->
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex gap-3">
        <a href="{{ route('perijinan.show', $perijinan->id) }}"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center gap-2">
            <i class="mdi mdi-eye"></i>
            <span>Lihat Detail</span>
        </a>
        <a href="{{ route('perijinan.alur-validasi', $perijinan->id) }}"
            class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center gap-2">
            <i class="mdi mdi-sitemap"></i>
            <span>Lihat Alur Validasi</span>
        </a>
    </div>

    <script>
        // File size validation
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const maxSize = this.dataset.maxSize;
                if (maxSize && maxSize > 0 && this.files[0]) {
                    const fileSize = this.files[0].size / 1024; // Convert to KB
                    if (fileSize > maxSize) {
                        const maxSizeMB = (maxSize / 1024).toFixed(2);
                        alert(`Ukuran file melebihi batas maksimal ${maxSizeMB} MB. Ukuran file saat ini: ${fileSize.toFixed(2)} MB`);
                        this.value = ''; // Clear the input
                    }
                }
            });
        });
    </script>
</x-layout>
