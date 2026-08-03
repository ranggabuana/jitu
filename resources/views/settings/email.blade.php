<x-layout>
    <x-slot:title>Pengaturan Email</x-slot:title>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <i class="mdi mdi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Pengaturan Email</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Konfigurasi server SMTP dan template pesan
                        email</p>
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

    <!-- Navigation Tabs -->
    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
        <nav class="flex space-x-8 min-w-max px-2">
            <button onclick="switchTab('tab-smtp')" id="btn-tab-smtp"
                class="tab-btn active-tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-all">
                <i class="mdi mdi-server-network text-lg"></i>
                Konfigurasi SMTP
            </button>
            <button onclick="switchTab('tab-templates')" id="btn-tab-templates"
                class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-all">
                <i class="mdi mdi-email-edit-outline text-lg"></i>
                Template Email
            </button>
        </nav>
    </div>

    <!-- Main Container: Full Width (100%) -->
    <div class="w-full">
        <!-- Tab: SMTP Configuration -->
        <div id="tab-smtp" class="tab-content space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left: Form (8/12) -->
                <div class="lg:col-span-8 space-y-6">
                    <form action="{{ route('settings.email.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" value="smtp">

                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div
                                class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30">
                                <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                    <i class="mdi mdi-cog text-blue-500"></i>
                                    Pengaturan Server
                                </h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">SMTP
                                            Host</label>
                                        <input type="text" name="mail_host"
                                            value="{{ old('mail_host', $smtpSettings['mail_host'] ?? config('mail.mailers.smtp.host')) }}"
                                            class="form-input" placeholder="smtp.gmail.com">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">SMTP
                                            Port</label>
                                        <input type="number" name="mail_port"
                                            value="{{ old('mail_port', $smtpSettings['mail_port'] ?? config('mail.mailers.smtp.port')) }}"
                                            class="form-input" placeholder="587">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">SMTP
                                            Username</label>
                                        <input type="text" name="mail_username"
                                            value="{{ old('mail_username', $smtpSettings['mail_username'] ?? config('mail.mailers.smtp.username')) }}"
                                            class="form-input" placeholder="email@gmail.com">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">SMTP
                                            Password</label>
                                        <div class="relative">
                                            <input type="password" name="mail_password" id="mail_password"
                                                value="{{ old('mail_password', $smtpSettings['mail_password'] ?? config('mail.mailers.smtp.password')) }}"
                                                class="form-input" placeholder="Sandi aplikasi">
                                            <button type="button" onclick="togglePasswordVisibility('mail_password')"
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                                                <i class="mdi mdi-eye" id="mail_password_icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Enkripsi</label>
                                    <select name="mail_encryption" class="form-input">
                                        <option value="tls"
                                            {{ old('mail_encryption', $smtpSettings['mail_encryption'] ?? config('mail.mailers.smtp.encryption')) == 'tls' ? 'selected' : '' }}>
                                            TLS</option>
                                        <option value="ssl"
                                            {{ old('mail_encryption', $smtpSettings['mail_encryption'] ?? config('mail.mailers.smtp.encryption')) == 'ssl' ? 'selected' : '' }}>
                                            SSL</option>
                                        <option value="null"
                                            {{ old('mail_encryption', $smtpSettings['mail_encryption'] ?? config('mail.mailers.smtp.encryption')) == null ? 'selected' : '' }}>
                                            None</option>
                                    </select>
                                </div>

                                <hr class="my-4 border-gray-100 dark:border-gray-700">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email
                                            Pengirim</label>
                                        <input type="email" name="mail_from_address"
                                            value="{{ old('mail_from_address', $smtpSettings['mail_from_address'] ?? config('mail.from.address')) }}"
                                            class="form-input" placeholder="no-reply@jitu.id">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama
                                            Pengirim</label>
                                        <input type="text" name="mail_from_name"
                                            value="{{ old('mail_from_name', $smtpSettings['mail_from_name'] ?? config('mail.from.name')) }}"
                                            class="form-input" placeholder="JITU Banjarnegara">
                                    </div>
                                </div>
                            </div>
                            <div
                                class="px-6 py-4 bg-gray-50/50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold transition-all flex items-center gap-2">
                                    <i class="mdi mdi-content-save"></i> Simpan Konfigurasi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Right: Test Connection (4/12) -->
                <div class="lg:col-span-4 space-y-6">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">
                        <div
                            class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30">
                            <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <i class="mdi mdi-email-check text-emerald-500"></i>
                                Test Koneksi
                            </h2>
                        </div>
                        <div class="p-6">
                            <p class="text-xs text-gray-500 mb-4 font-medium">Kirim email percobaan ke alamat Anda
                                untuk memastikan konfigurasi server SMTP sudah benar.</p>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Email
                                        Penerima Tes</label>
                                    <input type="email" id="test_email" class="form-input"
                                        placeholder="Contoh: nama@gmail.com">
                                </div>
                                <button type="button" id="btn-test-connection"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20">
                                    <i class="mdi mdi-send"></i> Jalankan Tes Koneksi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Email Templates -->
        <div id="tab-templates" class="tab-content hidden space-y-6">
            @php
                $defaultApproved = 'Kabar baik! Permohonan izin Anda dengan nomor registrasi {{registrationNumber}} untuk jenis {{permitName}} telah resmi disetujui. Silakan login ke dashboard dan mengisi Survei Kepuasan Masyarakat (SKM) untuk mengunduh dokumen izin Anda.';
                $defaultRejected = 'Mohon maaf, permohonan izin Anda dengan nomor registrasi {{registrationNumber}} untuk jenis {{permitName}} ditolak dikarenakan hal berikut: {{notes}}';
                $defaultReturned = 'Terdapat berkas yang perlu Anda perbaiki pada permohonan izin dengan nomor registrasi {{registrationNumber}} ({{permitName}}). Berikut catatan perbaikan dari petugas: {{notes}}. Mohon segera lengkapi agar proses dapat dilanjutkan.';
                $defaultComplaint = 'Halo {{userName}}, pengaduan Anda dengan rincian berikut: "{{complaintDetail}}" kini memiliki status baru: "{{complaintStatus}}". Berikut tanggapan/catatan dari petugas: "{{complaintResponse}}". Terima kasih.';
            @endphp
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Side: Forms (8/12) -->
                <div class="lg:col-span-8">
                    <form action="{{ route('settings.email.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" value="templates">

                        <div class="space-y-6">
                            <!-- Template: Lupa Password -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div
                                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex justify-between items-center">
                                    <h2
                                        class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                        <i class="mdi mdi-lock-reset text-orange-500"></i>
                                        Email Lupa Password
                                    </h2>
                                    <button type="button" onclick="previewEmail('forgot_password')" class="text-xs bg-orange-100 hover:bg-orange-200 text-orange-700 px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1.5 border border-orange-200">
                                        <i class="mdi mdi-eye"></i> Preview
                                    </button>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Subjek
                                            Email</label>
                                        <input type="text" name="forgot_password_subject" id="forgot_password_subject"
                                            value="{{ old('forgot_password_subject', $templateSettings['forgot_password_subject'] ?? 'Permintaan Reset Password') }}"
                                            class="form-input">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Isi
                                            Pesan (Body)</label>
                                        <textarea name="forgot_password_content" id="forgot_password_content" rows="6" class="form-input resize-none">{{ old('forgot_password_content', $templateSettings['forgot_password_content'] ?? 'Kami menerima permintaan untuk melakukan pengaturan ulang kata sandi (reset password) pada akun Anda. Klik tombol di bawah ini untuk melanjutkan proses:') }}</textarea>
                                        <!-- Quick Variables -->
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span
                                                class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ userName }}</span>
                                            <span
                                                class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ appName }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Template: Aktivasi Akun -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div
                                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex justify-between items-center">
                                    <h2
                                        class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                        <i class="mdi mdi-account-check text-green-500"></i>
                                        Email Aktivasi Akun
                                    </h2>
                                    <button type="button" onclick="previewEmail('account_activated')" class="text-xs bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1.5 border border-green-200">
                                        <i class="mdi mdi-eye"></i> Preview
                                    </button>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Subjek
                                            Email</label>
                                        <input type="text" name="account_activated_subject" id="account_activated_subject"
                                            value="{{ old('account_activated_subject', $templateSettings['account_activated_subject'] ?? 'Akivasi Akun Berhasil') }}"
                                            class="form-input">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Isi
                                            Pesan (Body)</label>
                                        <textarea name="account_activated_content" id="account_activated_content" rows="6" class="form-input resize-none">{{ old('account_activated_content', $templateSettings['account_activated_content'] ?? 'Selamat! Akun Anda telah berhasil diverifikasi dan diaktifkan oleh admin. Sekarang Anda sudah dapat mengakses dashboard pemohon untuk mengajukan perizinan secara online.') }}</textarea>
                                        <!-- Quick Variables -->
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ userName }}</span>
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ appName }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Template: Permohonan Disetujui -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div
                                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex justify-between items-center">
                                    <h2
                                        class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                        <i class="mdi mdi-file-check text-emerald-500"></i>
                                        Email Permohonan Disetujui (Final)
                                    </h2>
                                    <button type="button" onclick="previewEmail('permit_approved')" class="text-xs bg-emerald-100 hover:bg-emerald-200 text-emerald-700 px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1.5 border border-emerald-200">
                                        <i class="mdi mdi-eye"></i> Preview
                                    </button>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Subjek
                                            Email</label>
                                        <input type="text" name="permit_approved_subject" id="permit_approved_subject"
                                            value="{{ old('permit_approved_subject', $templateSettings['permit_approved_subject'] ?? 'Selamat! Permohonan Izin Anda Telah Disetujui') }}"
                                            class="form-input">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Isi
                                            Pesan (Body)</label>
                                        <textarea name="permit_approved_content" id="permit_approved_content" rows="6" class="form-input resize-none">{{ old('permit_approved_content', $templateSettings['permit_approved_content'] ?? $defaultApproved) }}</textarea>
                                        <!-- Quick Variables -->
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ userName }}</span>
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ registrationNumber }}</span>
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ permitName }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Template: Permohonan Ditolak -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div
                                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex justify-between items-center">
                                    <h2
                                        class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                        <i class="mdi mdi-file-cancel text-red-500"></i>
                                        Email Permohonan Ditolak
                                    </h2>
                                    <button type="button" onclick="previewEmail('permit_rejected')" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1.5 border border-red-200">
                                        <i class="mdi mdi-eye"></i> Preview
                                    </button>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Subjek
                                            Email</label>
                                        <input type="text" name="permit_rejected_subject" id="permit_rejected_subject"
                                            value="{{ old('permit_rejected_subject', $templateSettings['permit_rejected_subject'] ?? 'Pemberitahuan: Permohonan Izin Anda Ditolak') }}"
                                            class="form-input">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Isi
                                            Pesan (Body)</label>
                                        <textarea name="permit_rejected_content" id="permit_rejected_content" rows="6" class="form-input resize-none">{{ old('permit_rejected_content', $templateSettings['permit_rejected_content'] ?? $defaultRejected) }}</textarea>
                                        <!-- Quick Variables -->
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ userName }}</span>
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ registrationNumber }}</span>
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ notes }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Template: Permohonan Dikembalikan -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div
                                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex justify-between items-center">
                                    <h2
                                        class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                        <i class="mdi mdi-file-replace text-orange-500"></i>
                                        Email Permohonan Perlu Perbaikan
                                    </h2>
                                    <button type="button" onclick="previewEmail('permit_returned')" class="text-xs bg-orange-100 hover:bg-orange-200 text-orange-700 px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1.5 border border-orange-200">
                                        <i class="mdi mdi-eye"></i> Preview
                                    </button>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Subjek
                                            Email</label>
                                        <input type="text" name="permit_returned_subject" id="permit_returned_subject"
                                            value="{{ old('permit_returned_subject', $templateSettings['permit_returned_subject'] ?? 'Pemberitahuan: Perbaikan Berkas Permohonan Izin') }}"
                                            class="form-input">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Isi
                                            Pesan (Body)</label>
                                        <textarea name="permit_returned_content" id="permit_returned_content" rows="6" class="form-input resize-none">{{ old('permit_returned_content', $templateSettings['permit_returned_content'] ?? $defaultReturned) }}</textarea>
                                        <!-- Quick Variables -->
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ userName }}</span>
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ registrationNumber }}</span>
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ notes }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Template: Perubahan Status Pengaduan -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div
                                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex justify-between items-center">
                                    <h2
                                        class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                        <i class="mdi mdi-comment-alert-outline text-blue-500"></i>
                                        Email Perubahan Status Pengaduan
                                    </h2>
                                    <button type="button" onclick="previewEmail('complaint_status_changed')" class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1.5 border border-blue-200">
                                        <i class="mdi mdi-eye"></i> Preview
                                    </button>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Subjek
                                            Email</label>
                                        <input type="text" name="complaint_status_changed_subject" id="complaint_status_changed_subject"
                                            value="{{ old('complaint_status_changed_subject', $templateSettings['complaint_status_changed_subject'] ?? 'Pemberitahuan: Status Pengaduan Anda Telah Diperbarui') }}"
                                            class="form-input">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Isi
                                            Pesan (Body)</label>
                                        <textarea name="complaint_status_changed_content" id="complaint_status_changed_content" rows="6" class="form-input resize-none">{{ old('complaint_status_changed_content', $templateSettings['complaint_status_changed_content'] ?? $defaultComplaint) }}</textarea>
                                        <!-- Quick Variables -->
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ userName }}</span>
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ registrationNumber }}</span>
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ complaintDetail }}</span>
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ complaintStatus }}</span>
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[10px] font-bold rounded text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">@{{ complaintResponse }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold transition-all flex items-center gap-2">
                                    <i class="mdi mdi-content-save-all"></i> Simpan Semua Template
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Right Side: Placeholder Guide (4/12) -->
                <div class="lg:col-span-4">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">
                        <div
                            class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-blue-50/50 dark:bg-blue-900/20">
                            <h2
                                class="text-sm font-bold text-blue-900 dark:text-blue-300 uppercase tracking-wider flex items-center gap-2">
                                <i class="mdi mdi-information-variant text-lg"></i>
                                Panduan Variabel Pintar
                            </h2>
                        </div>
                        <div class="p-6 space-y-6">
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                Anda dapat menyisipkan tulisan di bawah ini ke dalam template agar data berubah secara
                                dinamis sesuai penerima email.
                            </p>

                            <div class="space-y-4">
                                <div
                                    class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <code
                                        class="text-blue-600 dark:text-blue-400 font-bold text-sm">@{{ userName }}</code>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Berubah menjadi
                                        <strong>Nama Lengkap</strong> pemohon.
                                    </p>
                                </div>
                                <div
                                    class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <code
                                        class="text-blue-600 dark:text-blue-400 font-bold text-sm">@{{ appName }}</code>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Berubah menjadi
                                        <strong>Nama Pengirim</strong> aplikasi.
                                    </p>
                                </div>
                                <div
                                    class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <code
                                        class="text-blue-600 dark:text-blue-400 font-bold text-sm">@{{ registrationNumber }}</code>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Berubah menjadi
                                        <strong>Nomor Registrasi</strong> pengajuan.
                                    </p>
                                </div>
                                <div
                                    class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <code
                                        class="text-blue-600 dark:text-blue-400 font-bold text-sm">@{{ permitName }}</code>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Berubah menjadi
                                        <strong>Nama Jenis Perizinan</strong>.
                                    </p>
                                </div>
                                <div
                                    class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <code
                                        class="text-blue-600 dark:text-blue-400 font-bold text-sm">@{{ notes }}</code>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Berubah menjadi
                                        <strong>Catatan</strong> dari validator.
                                    </p>
                                </div>
                                <div
                                    class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <code
                                        class="text-blue-600 dark:text-blue-400 font-bold text-sm">@{{ complaintDetail }}</code>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Berubah menjadi
                                        <strong>Isi Detail Pengaduan</strong> yang dilaporkan.
                                    </p>
                                </div>
                                <div
                                    class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <code
                                        class="text-blue-600 dark:text-blue-400 font-bold text-sm">@{{ complaintStatus }}</code>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Berubah menjadi
                                        <strong>Status Pengaduan Terbaru</strong>.
                                    </p>
                                </div>
                                <div
                                    class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <code
                                        class="text-blue-600 dark:text-blue-400 font-bold text-sm">@{{ complaintResponse }}</code>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Berubah menjadi
                                        <strong>Respon / Catatan Tanggapan</strong> dari petugas.
                                    </p>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                                <h4
                                    class="text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase mb-3 flex items-center gap-1">
                                    <i class="mdi mdi-lightbulb-on-outline text-orange-500"></i> Contoh Penggunaan:
                                </h4>
                                <div class="space-y-4">
                                    <!-- Contoh userName -->
                                    <div
                                        class="p-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg border border-dashed border-gray-200 dark:border-gray-700">
                                        <p class="text-[9px] text-blue-500 font-bold uppercase mb-1">Contoh 1
                                            (@{{ userName }}):</p>
                                        <p class="text-[11px] text-gray-600 dark:text-gray-400 mb-1 italic">"Halo
                                            @{{ userName }}, silakan login..."</p>
                                        <i class="mdi mdi-arrow-down text-center block text-gray-300 text-xs my-1"></i>
                                        <p class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                                            "Halo Budi Santoso, silakan login..."</p>
                                    </div>

                                    <!-- Contoh appName -->
                                    <div
                                        class="p-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg border border-dashed border-gray-200 dark:border-gray-700">
                                        <p class="text-[9px] text-blue-500 font-bold uppercase mb-1">Contoh 2
                                            (@{{ appName }}):</p>
                                        <p class="text-[11px] text-gray-600 dark:text-gray-400 mb-1 italic">"Salam
                                            hormat, Tim @{{ appName }}"</p>
                                        <i class="mdi mdi-arrow-down text-center block text-gray-300 text-xs my-1"></i>
                                        <p class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                                            "Salam hormat, Tim JITU (Jaringan Informasi Terpadu) Banjarnegara"</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            background-color: #ffffff;
            font-size: 0.875rem;
            color: #1f2937;
            transition: all 0.2s;
        }

        .dark .form-input {
            background-color: #111827;
            border-color: #374151;
            color: #f9fafb;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .tab-btn {
            transition: all 0.3s;
        }

        .active-tab-btn {
            color: #2563eb !important;
            border-color: #2563eb !important;
        }

        .dark .active-tab-btn {
            color: #60a5fa !important;
            border-color: #3b82f6 !important;
        }
    </style>

    @push('scripts')
        <script>
            function switchTab(tabId) {
                document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
                document.getElementById(tabId).classList.remove('hidden');
                document.querySelectorAll('.tab-btn').forEach(el => {
                    el.classList.remove('active-tab-btn');
                    el.classList.add('border-transparent', 'text-gray-500');
                });
                const activeBtn = document.getElementById('btn-' + tabId);
                activeBtn.classList.add('active-tab-btn');
                activeBtn.classList.remove('border-transparent', 'text-gray-500');
                localStorage.setItem('active_email_tab', tabId);
            }

            function togglePasswordVisibility(inputId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(inputId + '_icon');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('mdi-eye');
                    icon.classList.add('mdi-eye-off');
                } else {
                    input.type = 'password';
                    icon.classList.remove('mdi-eye-off');
                    icon.classList.add('mdi-eye');
                }
            }

            function previewEmail(type) {
                const content = document.getElementById(type + '_content').value;
                const subject = document.getElementById(type + '_subject').value;

                Swal.fire({
                    title: 'Memuat Preview...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('{{ route('settings.email.preview') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            type: type,
                            content: content
                        })
                    })
                    .then(response => response.text())
                    .then(html => {
                        Swal.fire({
                            title: 'Preview Template Email',
                            html: `
                        <div class="text-left mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Subjek Email:</p>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">${subject}</p>
                        </div>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-inner bg-white">
                            <iframe srcdoc="${html.replace(/"/g, '&quot;')}" sandbox="allow-same-origin" class="w-full border-none" style="height: 500px;"></iframe>
                        </div>
                    `,
                            width: '800px',
                            showCloseButton: true,
                            showConfirmButton: false,
                            customClass: {
                                container: 'email-preview-modal'
                            }
                        });
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Gagal memuat preview email.', 'error');
                    });
            }

            document.addEventListener('DOMContentLoaded', function() {
                const lastTab = localStorage.getItem('active_email_tab');
                if (lastTab && document.getElementById(lastTab)) {
                    switchTab(lastTab);
                }

                document.getElementById('btn-test-connection').addEventListener('click', function() {
                    const email = document.getElementById('test_email').value;
                    if (!email) {
                        Swal.fire('Error', 'Masukkan email penerima!', 'error');
                        return;
                    }

                    const btn = this;
                    const originalContent = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i>';

                    fetch('{{ route('settings.email.test') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                test_email: email
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire({
                                icon: data.success ? 'success' : 'error',
                                title: data.success ? 'Berhasil!' : 'Gagal!',
                                text: data.message,
                                confirmButtonColor: data.success ? '#059669' : '#dc2626'
                            });
                        })
                        .finally(() => {
                            btn.disabled = false;
                            btn.innerHTML = originalContent;
                        });
                });
            });
        </script>
    @endpush
</x-layout>
