<x-pemohon.layout>
    <x-slot:title>Ajukan {{ $perijinan->nama_perijinan }} - JITU Banjarnegara</x-slot:title>

    <!-- Navbar -->
    <x-pemohon.navbar></x-pemohon.navbar>

    <!-- Main Content -->
    <main class="flex-1 max-w-5xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-br from-amber-600 via-amber-700 to-amber-800 rounded-3xl shadow-xl p-6 text-white">
            <div class="flex items-center gap-4">
                <a href="{{ route('pemohon.perijinan') }}" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold mb-1">Formulir Pengajuan</h1>
                    <p class="text-amber-100 text-sm">{{ $perijinan->nama_perijinan }}</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('pemohon.pengajuan.store') }}" method="POST" enctype="multipart/form-data"
            id="pengajuanForm" class="space-y-6">
            @csrf
            <input type="hidden" name="perijinan_id" value="{{ $perijinan->id }}">

            <!-- Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-info-circle text-amber-600"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-800">Informasi Pengajuan</h2>
                        <p class="text-sm text-gray-500">Pastikan semua data yang diisi benar dan lengkap</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-amber-50 rounded-xl p-4">
                    <div>
                        <p class="text-sm text-gray-600">Jenis Perizinan</p>
                        <p class="font-semibold text-gray-800">{{ $perijinan->nama_perijinan }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tahap Validasi</p>
                        <p class="font-semibold text-gray-800">{{ $perijinan->activeValidationFlows->count() }} Tahap
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Pemohon</p>
                        <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Dynamic Form Fields Section -->
            @if ($perijinan->activeFormFields->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-amber-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-700 to-amber-800 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                            <h2 class="font-bold text-white">Formulir Perizinan</h2>
                            <span class="bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full">
                                {{ $perijinan->activeFormFields->count() }} Field
                            </span>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        @if ($perijinan->activeFormFields->count() === 0)
                            <p class="text-gray-500 text-center py-4">Tidak ada formulir untuk perizinan ini.</p>
                        @endif
                        
                        @foreach ($perijinan->activeFormFields as $index => $field)
                            <div class="form-field-group" data-order="{{ $field->order }}">
                                @if ($field->type === 'text' || $field->type === 'email' || $field->type === 'number' || $field->type === 'date')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $field->label }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <input type="{{ $field->type }}" name="form_fields[{{ $field->id }}]"
                                            value="{{ old('form_fields.' . $field->id) }}"
                                            @if ($field->is_required) required @endif
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                            placeholder="{{ $field->placeholder ?? 'Masukkan ' . strtolower($field->label) }}">
                                        @if ($field->help_text)
                                            <p class="mt-1 text-xs text-gray-500">{{ $field->help_text }}</p>
                                        @endif
                                    </div>
                                @elseif ($field->type === 'textarea')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $field->label }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <textarea name="form_fields[{{ $field->id }}]" rows="4" @if ($field->is_required) required @endif
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                            placeholder="{{ $field->placeholder ?? 'Masukkan ' . strtolower($field->label) }}">{{ old('form_fields.' . $field->id) }}</textarea>
                                        @if ($field->help_text)
                                            <p class="mt-1 text-xs text-gray-500">{{ $field->help_text }}</p>
                                        @endif
                                    </div>
                                @elseif ($field->type === 'select')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $field->label }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <select name="form_fields[{{ $field->id }}]"
                                            @if ($field->is_required) required @endif
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                            <option value="">Pilih {{ strtolower($field->label) }}</option>
                                            @if (is_array($field->options))
                                                @foreach ($field->options as $option)
                                                    <option value="{{ $option }}"
                                                        {{ old('form_fields.' . $field->id) == $option ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($field->help_text)
                                            <p class="mt-1 text-xs text-gray-500">{{ $field->help_text }}</p>
                                        @endif
                                    </div>
                                @elseif ($field->type === 'radio')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $field->label }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <div class="space-y-2">
                                            @if (is_array($field->options))
                                                @foreach ($field->options as $option)
                                                    <label class="flex items-center gap-2 cursor-pointer">
                                                        <input type="radio" name="form_fields[{{ $field->id }}]"
                                                            value="{{ $option }}"
                                                            @if (old('form_fields.' . $field->id) == $option) checked @endif
                                                            @if ($field->is_required && $loop->first) required @endif
                                                            class="w-4 h-4 text-amber-600 focus:ring-amber-500">
                                                        <span class="text-sm text-gray-700">{{ $option }}</span>
                                                    </label>
                                                @endforeach
                                            @endif
                                        </div>
                                        @if ($field->help_text)
                                            <p class="mt-1 text-xs text-gray-500">{{ $field->help_text }}</p>
                                        @endif
                                    </div>
                                @elseif ($field->type === 'checkbox')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $field->label }}
                                        </label>
                                        <div class="space-y-2">
                                            @if (is_array($field->options))
                                                @foreach ($field->options as $option)
                                                    <label class="flex items-center gap-2 cursor-pointer">
                                                        <input type="checkbox"
                                                            name="form_fields[{{ $field->id }}][]"
                                                            value="{{ $option }}"
                                                            @if (in_array($option, old('form_fields.' . $field->id, []))) checked @endif
                                                            class="w-4 h-4 text-amber-600 focus:ring-amber-500 rounded">
                                                        <span class="text-sm text-gray-700">{{ $option }}</span>
                                                    </label>
                                                @endforeach
                                            @endif
                                        </div>
                                        @if ($field->help_text)
                                            <p class="mt-1 text-xs text-gray-500">{{ $field->help_text }}</p>
                                        @endif
                                    </div>
                                @elseif ($field->type === 'file')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $field->label }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <div
                                            class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-amber-500 transition-colors">
                                            <input type="file" name="form_files[{{ $field->id }}]"
                                                id="file_{{ $field->id }}"
                                                @if ($field->is_required) required @endif
                                                accept="{{ $field->file_types ?? '*' }}" class="hidden"
                                                onchange="previewFile(this, {{ $field->id }})">
                                            <label for="file_{{ $field->id }}" class="cursor-pointer">
                                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-semibold text-amber-600">Klik untuk upload</span>
                                                    atau drag & drop
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $field->file_types ?? 'Semua format' }} (Max
                                                    {{ $field->file_size ?? '2MB' }})
                                                </p>
                                            </label>
                                            <div id="preview_{{ $field->id }}" class="mt-3 hidden"></div>
                                        </div>
                                        @if ($field->help_text)
                                            <p class="mt-1 text-xs text-gray-500">{{ $field->help_text }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Persyaratan Info -->
            @if ($perijinan->persyaratan)
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-blue-600"></i>
                        </div>
                        <h2 class="font-bold text-gray-800">Persyaratan</h2>
                    </div>
                    <div class="text-gray-700 text-sm">
                        {!! $perijinan->persyaratan !!}
                    </div>
                </div>
            @endif

            <!-- Submit Button -->
            <div class="flex items-center justify-between gap-4 pt-4">
                <a href="{{ route('pemohon.perijinan') }}"
                    class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Batal
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Kirim Pengajuan</span>
                </button>
            </div>
        </form>
    </main>

    <!-- Footer -->
    <x-pemohon.footer></x-pemohon.footer>

    <!-- Scripts -->
    <script>
        function previewFile(input, fieldId) {
            const preview = document.getElementById('preview_' + fieldId);

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileName = file.name;
                const fileSize = (file.size / 1024).toFixed(2); // KB

                preview.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-center gap-3">
                        <i class="fas fa-file text-green-600 text-2xl"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize} KB</p>
                        </div>
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                `;
                preview.classList.remove('hidden');
            }
        }

        // Form validation
        document.getElementById('pengajuanForm').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi!');
            }
        });
    </script>
</x-pemohon.layout>
