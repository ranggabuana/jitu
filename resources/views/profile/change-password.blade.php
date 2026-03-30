<x-layout>
    <x-slot:title>Ubah Password</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.show') }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <i class="mdi mdi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Ubah Password</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Perbarui password akun Anda</p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-start gap-2">
                    <i class="mdi mdi-alert-circle text-red-600 dark:text-red-400 mt-0.5"></i>
                    <div class="text-red-700 dark:text-red-400 text-sm">
                        <p class="font-medium mb-1">Terdapat kesalahan:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Change Password Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <form action="{{ route('profile.update-password') }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Password Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="current_password" name="current_password" required
                            class="w-full px-4 py-2.5 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('current_password') border-red-500 @enderror"
                            placeholder="Masukkan password saat ini">
                        <button type="button" onclick="togglePassword('current_password', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <i class="mdi mdi-eye-off text-lg"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required minlength="8"
                            class="w-full px-4 py-2.5 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                            placeholder="Minimal 8 karakter">
                        <button type="button" onclick="togglePassword('password', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <i class="mdi mdi-eye-off text-lg"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                        <i class="mdi mdi-information-outline"></i>
                        Minimal 8 karakter
                    </p>
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                            class="w-full px-4 py-2.5 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="Ulangi password baru">
                        <button type="button" onclick="togglePassword('password_confirmation', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <i class="mdi mdi-eye-off text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                    <div class="flex items-start gap-2">
                        <i class="mdi mdi-shield-lock text-amber-600 dark:text-amber-400 mt-0.5"></i>
                        <div class="text-amber-700 dark:text-amber-400 text-sm">
                            <p class="font-medium mb-1">Tips Keamanan:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Gunakan kombinasi huruf, angka, dan simbol</li>
                                <li>Jangan gunakan password yang mudah ditebak</li>
                                <li>Jangan bagikan password kepada siapapun</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg transition-colors font-medium flex items-center justify-center gap-2">
                        <i class="mdi mdi-key"></i>
                        <span>Ubah Password</span>
                    </button>
                    <a href="{{ route('profile.show') }}"
                        class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg transition-colors font-medium">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('mdi-eye-off');
                icon.classList.add('mdi-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('mdi-eye');
                icon.classList.add('mdi-eye-off');
            }
        }
    </script>
</x-layout>
