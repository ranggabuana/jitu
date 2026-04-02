<x-pemohon.layout>
    <x-slot:title>Dashboard Pemohon - JITU Banjarnegara</x-slot:title>

    <!-- Navbar -->
    <x-pemohon.navbar></x-pemohon.navbar>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Hero Section -->
        <x-pemohon.hero></x-pemohon.hero>

        <!-- Statistics Cards -->
        <x-pemohon.stats :stats="$stats"></x-pemohon.stats>

        <!-- Main Grid -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Applications (2/3 width) -->
            <div class="lg:col-span-2">
                <x-pemohon.applications :applications="$recentApplications"></x-pemohon.applications>
            </div>

            <!-- Right Column - Profile (1/3 width) -->
            <div class="space-y-6">
                <x-pemohon.profile :user="$user"></x-pemohon.profile>
            </div>
        </section>

    </main>

    <!-- Detail Modal -->
    <x-pemohon.detail-modal></x-pemohon.detail-modal>

    <!-- Footer -->
    <x-pemohon.footer></x-pemohon.footer>

    <!-- Scripts -->
    <script>
        // Sample data for demonstration
        const stats = @json($stats);

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initStats();
            initMessages();
            initApplications();
        });

        function initStats() {
            document.getElementById('statTotal').textContent = stats.total;
            document.getElementById('statInProgress').textContent = stats.in_progress;
            document.getElementById('statFix').textContent = stats.needs_fix;
            document.getElementById('statDone').textContent = stats.completed;
        }

        function initMessages() {
            // Initialize messages list
        }

        function initApplications() {
            // Initialize applications table
        }

        function openDetail(id) {
            // Fetch detail data via AJAX
            fetch(`/pemohon/perijinan/${id}/application-detail`)
                .then(response => response.json())
                .then(response => {
                    if (!response.success) {
                        throw new Error(response.message || 'Gagal memuat detail');
                    }
                    const data = response.data;
                    
                    // Populate modal data
                    document.getElementById('dTitle').textContent = data.perijinan.nama_perijinan;
                    document.getElementById('dReg').textContent = data.no_registrasi;
                    document.getElementById('dStatusBadge').innerHTML = `<span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold ${data.status_color}">${data.status_label}</span>`;
                    
                    // Calculate SLA
                    const createdDate = new Date(data.created_at);
                    const now = new Date();
                    const diffDays = Math.floor((now - createdDate) / (1000 * 60 * 60 * 24));
                    document.getElementById('dSla').textContent = `${diffDays} hari yang lalu` || '-';
                    
                    // Build timeline
                    let timelineHtml = '';
                    data.validasi_records.forEach((validasi, index) => {
                        const isCompleted = validasi.status === 'approved';
                        const isRevision = validasi.status === 'revision';
                        const isRejected = validasi.status === 'rejected';
                        
                        let dotColor = 'bg-gray-400';
                        let icon = `<span class="text-white font-bold text-sm">${index + 1}</span>`;
                        let statusClass = 'border-gray-200';
                        
                        if (isCompleted) {
                            dotColor = 'bg-green-500';
                            icon = '<i class="fas fa-check text-white text-sm"></i>';
                            statusClass = 'border-green-200';
                        } else if (isRevision) {
                            dotColor = 'bg-orange-500';
                            icon = '<i class="fas fa-exclamation-triangle text-white text-sm"></i>';
                            statusClass = 'border-orange-200';
                        } else if (isRejected) {
                            dotColor = 'bg-red-500';
                            icon = '<i class="fas fa-times text-white text-sm"></i>';
                            statusClass = 'border-red-200';
                        }
                        
                        // Determine validator display based on role
                        let validatorHtml = '';
                        const assignedRoles = ['operator_opd', 'kepala_opd'];
                        
                        if (validasi.validator && assignedRoles.includes(validasi.validator.role)) {
                            // Show name for assigned roles
                            validatorHtml = `
                                <div class="mt-2 flex items-center gap-2 bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg p-2 border border-amber-200">
                                    <div class="w-6 h-6 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user-check text-white text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-semibold text-gray-800">
                                            <i class="fas fa-user-tie mr-1"></i>
                                            ${validasi.validator.name}
                                        </p>
                                        <p class="text-xs text-gray-500">${validasi.validator.role_label || 'Validator'}</p>
                                    </div>
                                </div>
                            `;
                        } else if (validasi.validator && ['fo', 'bo', 'verifikator', 'kadin'].includes(validasi.validator.role)) {
                            // Show role only for collective roles
                            validatorHtml = `
                                <div class="mt-2 flex items-center gap-2 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-2 border border-blue-200">
                                    <div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-users text-white text-xs"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-gray-800">
                                        <i class="fas fa-user-check mr-1"></i>
                                        Divalidasi oleh ${validasi.validation_flow.role_label || validasi.validator.role_label}
                                    </p>
                                </div>
                            `;
                        }
                        
                        timelineHtml += `
                            <div class="relative pl-4">
                                <div class="absolute -left-[29px] w-16 h-16 rounded-full flex items-center justify-center shadow-lg flex-shrink-0 border-4 border-white ${dotColor}">
                                    ${icon}
                                </div>
                                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 border-2 ${statusClass}">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-gray-800 text-sm">${validasi.validation_flow.role_label || 'Tahap ' + (index + 1)}</h4>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${validasi.status_color}">${validasi.status_label}</span>
                                    </div>
                                    ${validasi.catatan ? `<div class="mt-2 bg-white rounded-lg p-2 border border-gray-200"><p class="text-xs text-gray-700"><i class="fas fa-comment-alt text-amber-500 mr-1"></i><strong>Catatan:</strong> ${validasi.catatan}</p></div>` : ''}
                                    ${validasi.validated_at ? `<p class="text-xs text-gray-500 mt-2"><i class="fas fa-clock mr-1"></i> ${new Date(validasi.validated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })} WIB</p>` : ''}
                                    ${validatorHtml}
                                </div>
                            </div>
                        `;
                    });
                    
                    document.getElementById('dTimeline').innerHTML = timelineHtml;
                    
                    // Update action buttons
                    const actionButtons = document.querySelectorAll('#detailModal .space-y-2 a, #detailModal .space-y-2 button');
                    actionButtons.forEach(btn => {
                        if (btn.textContent.includes('Buka Tracking')) {
                            btn.href = `/pemohon/tracking/${data.id}`;
                            btn.classList.remove('hidden');
                        } else if (btn.textContent.includes('Perbaiki')) {
                            if (data.status === 'perbaikan') {
                                btn.href = `/pemohon/pengajuan/${data.id}/edit`;
                                btn.classList.remove('hidden');
                                btn.classList.add('flex');
                            } else {
                                btn.classList.add('hidden');
                                btn.classList.remove('flex');
                            }
                        } else if (btn.textContent.includes('Unduh')) {
                            if (data.status === 'approved') {
                                btn.classList.remove('hidden');
                                btn.classList.add('flex');
                            } else {
                                btn.classList.add('hidden');
                                btn.classList.remove('flex');
                            }
                        }
                    });
                    
                    // Show modal
                    const modal = document.getElementById('detailModal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                })
                .catch(error => {
                    console.error('Error loading detail:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat detail pengajuan: ' + error.message
                    });
                });
        }

        function closeDetail() {
            document.getElementById('detailModal').classList.add('hidden');
            document.getElementById('detailModal').classList.remove('flex');
        }
    </script>
</x-pemohon.layout>
