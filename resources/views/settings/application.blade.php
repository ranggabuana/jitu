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
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Konfigurasi informasi aplikasi dan kontak</p>
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

    <form action="{{ route('settings.application.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - General & Contact Settings -->
            <div class="lg:col-span-2 space-y-6">
                <!-- General Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-application text-gray-500"></i>
                            Pengaturan Umum
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- App Name -->
                        <div>
                            <label for="app_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="mdi mdi-format-title text-gray-400 mr-1"></i> Nama Aplikasi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="app_name" id="app_name"
                                value="{{ old('app_name', $generalSettings['app_name'] ?? 'JITU - Layanan Perizinan Terpadu') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                placeholder="Masukkan nama aplikasi">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nama aplikasi yang akan ditampilkan di header dan title</p>
                        </div>

                        <!-- App Description -->
                        <div>
                            <label for="app_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="mdi mdi-text-box-outline text-gray-400 mr-1"></i> Deskripsi Aplikasi
                            </label>
                            <textarea name="app_description" id="app_description" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors resize-none"
                                placeholder="Deskripsi singkat tentang aplikasi">{{ old('app_description', $generalSettings['app_description'] ?? '') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Deskripsi untuk meta tag dan informasi aplikasi</p>
                        </div>

                        <!-- App Logo -->
                        <div>
                            <label for="app_logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="mdi mdi-image-outline text-gray-400 mr-1"></i> Logo Aplikasi
                            </label>
                            <div class="flex items-start gap-4">
                                @if(isset($generalSettings['app_logo']) && file_exists(public_path($generalSettings['app_logo'])))
                                    <div class="w-32 h-32 rounded-lg border-2 border-gray-200 dark:border-gray-600 overflow-hidden flex items-center justify-center bg-gray-50 dark:bg-gray-700">
                                        <img src="{{ asset($generalSettings['app_logo']) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                                    </div>
                                @else
                                    <div class="w-32 h-32 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center bg-gray-50 dark:bg-gray-700">
                                        <div class="text-center text-gray-400">
                                            <i class="mdi mdi-image-off text-3xl"></i>
                                            <p class="text-xs mt-1">Belum ada logo</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <input type="file" name="app_logo" id="app_logo" accept="image/*"
                                        class="block w-full text-sm text-gray-500 dark:text-gray-400
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-lg file:border-0
                                            file:text-sm file:font-medium
                                            file:bg-blue-50 file:text-blue-700
                                            dark:file:bg-blue-900/30 dark:file:text-blue-400
                                            hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50
                                            transition-colors cursor-pointer">
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Format: PNG, JPG, JPEG, SVG (Max 2MB)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-contact-mail text-gray-500"></i>
                            Informasi Kontak
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- WhatsApp -->
                        <div>
                            <label for="whatsapp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="mdi mdi-whatsapp text-green-500 mr-1"></i> Nomor WhatsApp
                            </label>
                            <input type="text" name="whatsapp" id="whatsapp"
                                value="{{ old('whatsapp', $contactSettings['whatsapp'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                placeholder="Contoh: 081234567890">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: 08xxxxxxxxxx (tanpa +62)</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="mdi mdi-email-outline text-gray-400 mr-1"></i> Email
                            </label>
                            <input type="email" name="email" id="email"
                                value="{{ old('email', $contactSettings['email'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                placeholder="Contoh: info@banjarnegara.go.id">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="mdi mdi-phone-outline text-gray-400 mr-1"></i> Nomor Telepon
                            </label>
                            <input type="text" name="phone" id="phone"
                                value="{{ old('phone', $contactSettings['phone'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                placeholder="Contoh: (0286) 123456">
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="mdi mdi-map-marker-outline text-gray-400 mr-1"></i> Alamat
                            </label>
                            <textarea name="address" id="address" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors resize-none"
                                placeholder="Alamat lengkap kantor">{{ old('address', $contactSettings['address'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Social Media Settings -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 sticky top-6">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-share-variant text-gray-500"></i>
                            Media Sosial
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Facebook -->
                        <div>
                            <label for="facebook" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="mdi mdi-facebook text-blue-600 mr-1"></i> Facebook
                            </label>
                            <input type="url" name="facebook" id="facebook"
                                value="{{ old('facebook', $socialMediaSettings['facebook'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                placeholder="https://facebook.com/username">
                        </div>

                        <!-- Instagram -->
                        <div>
                            <label for="instagram" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="mdi mdi-instagram text-pink-600 mr-1"></i> Instagram
                            </label>
                            <input type="url" name="instagram" id="instagram"
                                value="{{ old('instagram', $socialMediaSettings['instagram'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                placeholder="https://instagram.com/username">
                        </div>

                        <!-- YouTube -->
                        <div>
                            <label for="youtube" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="mdi mdi-youtube text-red-600 mr-1"></i> YouTube
                            </label>
                            <input type="url" name="youtube" id="youtube"
                                value="{{ old('youtube', $socialMediaSettings['youtube'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                placeholder="https://youtube.com/@channel">
                        </div>

                        <!-- TikTok -->
                        <div>
                            <label for="tiktok" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="mdi mdi-tiktok text-gray-800 mr-1"></i> TikTok
                            </label>
                            <input type="url" name="tiktok" id="tiktok"
                                value="{{ old('tiktok', $socialMediaSettings['tiktok'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                placeholder="https://tiktok.com/@username">
                        </div>

                        <!-- Twitter -->
                        <div>
                            <label for="twitter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="mdi mdi-twitter text-blue-400 mr-1"></i> Twitter / X
                            </label>
                            <input type="url" name="twitter" id="twitter"
                                value="{{ old('twitter', $socialMediaSettings['twitter'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                placeholder="https://twitter.com/username">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 rounded-b-lg">
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                            <i class="mdi mdi-content-save"></i>
                            Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-layout>
