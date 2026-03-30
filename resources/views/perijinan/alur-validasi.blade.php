<x-layout>
    <x-slot:title>Alur Validasi - {{ $perijinan->nama_perijinan }}</x-slot:title>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('perijinan.index') }}"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                <i class="mdi mdi-arrow-left text-xl"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Alur Validasi</h1>
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
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola urutan validasi untuk perijinan ini
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('perijinan.show', $perijinan->id) }}"
                        class="inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">
                        <span>Detail</span>
                        <i class="mdi mdi-arrow-right"></i>
                    </a>
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
        <!-- Form Tambah Validasi -->
        <div class="lg:col-span-1">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 sticky top-6">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-lg bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center">
                            <i class="mdi mdi-plus-circle text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-medium text-gray-800 dark:text-white">Tambah Tahap Validasi</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tambahkan role validasi baru</p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('perijinan.validation-flow.store', $perijinan->id) }}" method="POST"
                    class="p-6 space-y-4">
                    @csrf

                    <input type="hidden" name="order" value="{{ $perijinan->validationFlows->count() + 1 }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Role Validasi <span class="text-red-500 ml-1">*</span>
                        </label>
                        <select name="role" id="role" required onchange="toggleUserAssignment()"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                            <option value="">-- Pilih Role --</option>
                            @foreach ($availableRoles as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- User Assignment (for operator_opd and kepala_opd) -->
                    <div id="user_assignment_container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Petugas OPD <span class="text-red-500 ml-1">*</span>
                        </label>
                        <select name="assigned_user_id" id="assigned_user_id"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                            <option value="">-- Pilih Petugas --</option>
                        </select>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                            <i class="mdi mdi-information-outline"></i>
                            Pilih petugas yang akan melakukan validasi pada tahap ini
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Deskripsi Tugas
                        </label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                            placeholder="Deskripsi tugas validasi untuk role ini..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            SLA (Jam)
                        </label>
                        <input type="number" name="sla_hours" min="1" max="720"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                            placeholder="Contoh: 24 (1 hari)">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                            <i class="mdi mdi-clock-outline"></i>
                            Waktu maksimal untuk menyelesaikan validasi
                        </p>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked
                                class="w-4 h-4 text-orange-600 rounded border-gray-300 focus:ring-orange-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktif</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2.5 rounded-lg transition-colors font-medium flex items-center justify-center gap-2 shadow-sm">
                        <i class="mdi mdi-content-save"></i>
                        <span>Simpan Tahap Validasi</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Daftar Validasi Flow -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                            <i class="mdi mdi-sitemap text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-medium text-gray-800 dark:text-white">
                                Urutan Validasi
                                <span
                                    class="text-gray-500 dark:text-gray-400 font-normal">({{ $perijinan->validationFlows->count() }}
                                    tahap)</span>
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Drag & drop untuk mengubah urutan</p>
                        </div>
                    </div>
                </div>

                @if ($perijinan->validationFlows->count() > 0)
                    <div id="validation_flows_list" class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($perijinan->validationFlows as $flow)
                            <div class="flow-item p-5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                data-flow-id="{{ $flow->id }}" draggable="true">
                                <div class="flex items-center gap-4">
                                    <!-- Drag Handle -->
                                    <button
                                        class="cursor-move text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <i class="mdi mdi-drag-vertical text-xl"></i>
                                    </button>

                                    <!-- Order Badge -->
                                    <div
                                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center flex-shrink-0 shadow-md shadow-blue-500/30">
                                        <span class="text-white font-bold text-lg">{{ $flow->order }}</span>
                                    </div>

                                    <!-- Flow Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-semibold text-gray-900 dark:text-white">
                                                {{ $flow->role_label }}
                                            </span>
                                            @if ($flow->is_active)
                                                <span
                                                    class="bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-xs px-2 py-1 rounded-md font-medium">
                                                    <i class="mdi mdi-check-circle"></i> Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs px-2 py-1 rounded-md font-medium">
                                                    <i class="mdi mdi-pause-circle"></i> Nonaktif
                                                </span>
                                            @endif
                                        </div>
                                        <div
                                            class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                            <span
                                                class="flex items-center gap-1.5 bg-indigo-50 dark:bg-indigo-900/20 px-2.5 py-1 rounded-md text-indigo-600 dark:text-indigo-400">
                                                <i class="mdi mdi-account-outline"></i>
                                                {{ $flow->role }}
                                            </span>
                                            @if ($flow->assignedUser)
                                                <span
                                                    class="flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-900/20 px-2.5 py-1 rounded-md text-emerald-600 dark:text-emerald-400">
                                                    <i class="mdi mdi-account-check"></i>
                                                    <span>{{ $flow->assignedUser->name }}</span>
                                                </span>
                                            @endif
                                            @if ($flow->sla_hours)
                                                <span
                                                    class="flex items-center gap-1.5 bg-orange-50 dark:bg-orange-900/20 px-2.5 py-1 rounded-md text-orange-600 dark:text-orange-400">
                                                    <i class="mdi mdi-clock-outline"></i>
                                                    <span>SLA: {{ $flow->sla_hours }} jam
                                                        @if ($flow->sla_hours >= 24)
                                                            ({{ number_format($flow->sla_hours / 24, 1) }} hari)
                                                        @endif
                                                    </span>
                                                </span>
                                            @endif
                                        </div>
                                        @if ($flow->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 line-clamp-2">
                                                <i class="mdi mdi-information-outline mr-1"></i>
                                                {{ $flow->description }}
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center gap-2">
                                        <button onclick="editFlow(this)" data-flow-id="{{ $flow->id }}"
                                            data-perijinan-id="{{ $flow->perijinan_id }}"
                                            data-role="{{ $flow->role }}" data-order="{{ $flow->order }}"
                                            data-assigned-user-id="{{ $flow->assigned_user_id ?? '' }}"
                                            data-description="{{ $flow->description }}"
                                            data-sla-hours="{{ $flow->sla_hours ?? '' }}"
                                            data-is-active="{{ $flow->is_active ? '1' : '0' }}"
                                            class="text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 p-2 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors"
                                            title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <form
                                            action="{{ route('perijinan.validation-flow.delete', [$perijinan->id, $flow->id]) }}"
                                            method="POST" class="delete-form inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors btn-delete"
                                                data-action="{{ route('perijinan.validation-flow.delete', [$perijinan->id, $flow->id]) }}"
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
                            <i class="mdi mdi-sitemap text-3xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">Belum ada tahap validasi</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Tambahkan tahap validasi pertama Anda
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
                        class="w-10 h-10 rounded-lg bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center">
                        <i class="mdi mdi-pencil text-orange-600 dark:text-orange-400"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-medium text-gray-800 dark:text-white">Edit Tahap Validasi</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Perbarui informasi tahap validasi</p>
                    </div>
                </div>
                <button onclick="closeEditModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="mdi mdi-close text-xl"></i>
                </button>
            </div>
            <form id="editForm" method="POST" class="p-6 space-y-4" onsubmit="return handleFormSubmit(event)">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Role Validasi <span class="text-red-500 ml-1">*</span>
                    </label>
                    <select name="role" id="edit_role" required onchange="toggleEditUserAssignment()"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                        @foreach ($availableRoles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- User Assignment (for operator_opd and kepala_opd) -->
                <div id="edit_user_assignment_container" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Petugas OPD <span class="text-red-500 ml-1">*</span>
                    </label>
                    <select name="assigned_user_id" id="edit_assigned_user_id"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                        <option value="">-- Pilih Petugas --</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Deskripsi Tugas
                    </label>
                    <textarea name="description" id="edit_description" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                        placeholder="Deskripsi tugas validasi untuk role ini..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        SLA (Jam)
                    </label>
                    <input type="number" name="sla_hours" id="edit_sla_hours" min="1" max="720"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                        placeholder="Contoh: 24 (1 hari)">
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1"
                            class="w-4 h-4 text-orange-600 rounded border-gray-300 focus:ring-orange-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktif</span>
                    </label>
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit"
                        class="flex-1 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2.5 rounded-lg transition-colors font-medium flex items-center justify-center gap-2 shadow-sm">
                        <i class="mdi mdi-content-save"></i>
                        Update Tahap Validasi
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
        const operatorOpdUsers = @json($operatorOpdUsers);
        const kepalaOpdUsers = @json($kepalaOpdUsers);

        function populateUsers(role, selectId) {
            const select = document.getElementById(selectId);
            let users = {};

            if (role === 'operator_opd') users = operatorOpdUsers;
            if (role === 'kepala_opd') users = kepalaOpdUsers;

            select.innerHTML = '<option value="">-- Pilih Petugas --</option>';

            Object.entries(users).forEach(([id, name]) => {
                select.innerHTML += `<option value="${id}">${name}</option>`;
            });
        }

        function toggleUserAssignment(roleId, containerId, selectId) {
            const role = document.getElementById(roleId).value;
            const container = document.getElementById(containerId);
            const select = document.getElementById(selectId);

            if (['operator_opd', 'kepala_opd'].includes(role)) {
                container.classList.remove('hidden');
                select.required = true;
                populateUsers(role, selectId);
            } else {
                container.classList.add('hidden');
                select.required = false;
                select.value = '';
            }
        }

        document.getElementById('role')?.addEventListener('change', () => {
            toggleUserAssignment('role', 'user_assignment_container', 'assigned_user_id');
        });

        document.getElementById('edit_role')?.addEventListener('change', () => {
            toggleUserAssignment('edit_role', 'edit_user_assignment_container', 'edit_assigned_user_id');
        });

        function editFlow(btn) {

            const form = document.getElementById('editForm');

            form.action = `/perijinan/${btn.dataset.perijinanId}/validation-flow/${btn.dataset.flowId}`;

            document.getElementById('edit_role').value = btn.dataset.role;
            document.getElementById('edit_description').value = btn.dataset.description || '';
            document.getElementById('edit_sla_hours').value = btn.dataset.slaHours || '';
            document.getElementById('edit_is_active').checked = btn.dataset.isActive === '1';

            toggleUserAssignment('edit_role', 'edit_user_assignment_container', 'edit_assigned_user_id');

            if (btn.dataset.assignedUserId) {
                document.getElementById('edit_assigned_user_id').value = btn.dataset.assignedUserId;
            }

            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        document.getElementById('editForm').addEventListener('submit', async function(e) {

            e.preventDefault();

            const form = this;
            const btn = form.querySelector('button[type="submit"]');

            btn.disabled = true;

            const formData = new FormData(form);

            const res = await fetch(form.action, {
                method: 'POST', // gunakan POST
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();

            if (data.success) {

                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    confirmButtonColor: '#ea580c'
                });

                location.reload();

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Terjadi kesalahan'
                });

                btn.disabled = false;
            }

        });

        let dragged;

        document.querySelectorAll('.flow-item').forEach(el => {

            el.draggable = true;

            el.addEventListener('dragstart', () => dragged = el);

            el.addEventListener('dragover', e => e.preventDefault());

            el.addEventListener('drop', async function() {

                if (dragged === this) return;

                const parent = this.parentNode;

                parent.insertBefore(dragged, this);

                const ids = [...parent.querySelectorAll('.flow-item')]
                    .map(i => i.dataset.flowId);

                await fetch(`/perijinan/{{ $perijinan->id }}/validation-flow/reorder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .content
                    },
                    body: JSON.stringify({
                        flow_ids: ids
                    })
                });

                location.reload();

            });

        });
    </script>
</x-layout>
