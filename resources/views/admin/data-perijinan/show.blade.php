<x-layout>
    <x-slot:title>Detail {{ $application->no_registrasi }}</x-slot:title>

    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif
    @if (session('error'))
        <meta name="error-message" content="{{ session('error') }}">
    @endif

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    <i class="mdi mdi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
                <div>
                    <div class="flex items-center gap-2">
                        <span
                            class="font-mono font-bold text-lg text-gray-800 dark:text-white">{{ $application->no_registrasi }}</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $application->status_color }}">
                            {{ $application->status_label }}
                        </span>
                        @if($application->is_pembetulan)
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50">
                                Pembetulan Izin
                            </span>
                        @elseif($application->perpanjang_dari_id)
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-200 dark:border-purple-800/50">
                                Perpanjang Izin
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800/50">
                                Pengajuan Izin
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $application->perijinan->nama_perijinan }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    <i class="mdi mdi-clock"></i> {{ $application->created_at->format('d M Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Identity Modal -->
    <div id="modal-identity" class="fixed inset-0 z-[400] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl overflow-y-auto max-h-[90vh] animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50 dark:bg-gray-800/50 sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center border border-blue-200 dark:border-blue-800">
                        <i class="mdi mdi-account-details text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white">Identitas Lengkap Pemohon</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">Data Akun, Wilayah & Perusahaan</p>
                    </div>
                </div>
                <button type="button" onclick="closeIdentityModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <i class="mdi mdi-close text-2xl"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Column 1: Informasi Pribadi -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest border-b border-blue-100 dark:border-blue-900/30 pb-2 flex items-center gap-1.5">
                            <i class="mdi mdi-account-circle-outline"></i> Informasi Pribadi
                        </h4>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Nama Lengkap</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->name }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Username</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->username }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">NIK / NIP</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->nip ?? $application->user->nik ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Email</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->email }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">No. Telepon / WA</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->no_hp ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Jenis Kelamin</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->jenis_kelamin ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Pendidikan Terakhir</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->pendidikan ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Pekerjaan</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->pekerjaan ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Column 2: Alamat & Wilayah -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-green-600 dark:text-green-400 uppercase tracking-widest border-b border-green-100 dark:border-green-900/30 pb-2 flex items-center gap-1.5">
                            <i class="mdi mdi-map-marker-outline"></i> Wilayah & Alamat
                        </h4>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Provinsi</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->provinsi->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Kabupaten / Kota</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->kabupaten->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Kecamatan</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->kecamatan->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Kelurahan / Desa</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->kelurahan->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Alamat KTP</label>
                            <p class="text-xs font-medium text-gray-600 dark:text-gray-400 leading-relaxed bg-gray-50 dark:bg-gray-800/40 p-2 rounded-lg">{{ $application->user->alamat_ktp ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Alamat Domisili</label>
                            <p class="text-xs font-medium text-gray-600 dark:text-gray-400 leading-relaxed bg-gray-50 dark:bg-gray-800/40 p-2 rounded-lg">{{ $application->user->alamat_domisili ?? $application->user->alamat_lengkap ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Column 3: Informasi Bisnis & Sistem -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest border-b border-purple-100 dark:border-purple-900/30 pb-2 flex items-center gap-1.5">
                            <i class="mdi mdi-domain"></i> Bisnis & Sistem
                        </h4>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Status Pemohon</label>
                            <div class="mt-1">
                                @if ($application->user->status_pemohon === 'badan_usaha')
                                    <span class="px-2 py-1 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-[10px] font-bold">BADAN USAHA</span>
                                @else
                                    <span class="px-2 py-1 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-[10px] font-bold">PERORANGAN</span>
                                @endif
                            </div>
                        </div>
                        @if ($application->user->status_pemohon === 'badan_usaha')
                            <div>
                                <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Nama Perusahaan</label>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->nama_perusahaan ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">NPWP Perusahaan</label>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 font-mono">{{ $application->user->npwp ?? '-' }}</p>
                            </div>
                        @endif
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Status Akun</label>
                            <div class="mt-1">
                                @if ($application->user->status === 'aktif')
                                    <span class="px-2 py-1 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-[10px] font-bold">AKTIF</span>
                                @else
                                    <span class="px-2 py-1 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 text-[10px] font-bold">TIDAK AKTIF</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Terdaftar Sejak</label>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $application->user->created_at ? $application->user->created_at->format('d M Y') : '-' }}</p>
                        </div>
                        @if ($application->user->foto_ktp)
                            <div>
                                <label class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Berkas KTP</label>
                                <div class="mt-2">
                                    <a href="{{ asset($application->user->foto_ktp) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50 rounded-lg text-xs font-semibold transition-all">
                                        <i class="mdi mdi-file-document-outline"></i> Lihat Foto KTP
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                <button type="button" onclick="closeIdentityModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-xl text-sm font-bold transition-all">Tutup</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openIdentityModal() {
            const m = document.getElementById('modal-identity');
            m.classList.remove('hidden'); m.classList.add('flex'); document.body.style.overflow = 'hidden';
        }
        function closeIdentityModal() {
            const m = document.getElementById('modal-identity');
            m.classList.remove('flex'); m.classList.add('hidden'); document.body.style.overflow = 'auto';
        }
        document.getElementById('modal-identity').addEventListener('click', function(e) { if (e.target === this) closeIdentityModal(); });

        function switchTab(tabId) {
            document.querySelectorAll('.tab-content-panel').forEach(el => el.classList.add('hidden'));
            const targetPanel = document.getElementById('tab-panel-' + tabId);
            if (targetPanel) targetPanel.classList.remove('hidden');
            
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-blue-600', 'text-blue-600', 'bg-blue-50/50');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            const activeBtn = document.getElementById('tab-btn-' + tabId);
            if (activeBtn) {
                activeBtn.classList.add('border-blue-600', 'text-blue-600', 'bg-blue-50/50');
                activeBtn.classList.remove('border-transparent', 'text-gray-500');
            }
            localStorage.setItem('active_detail_tab', tabId);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const lastTab = localStorage.getItem('active_detail_tab') || 'data-pemohon';
            const tabBtn = document.getElementById('tab-btn-' + lastTab);
            switchTab(tabBtn ? lastTab : 'data-pemohon');

            // --- SLA Timer Logic ---
            const counterEl = document.getElementById('sla-counter');
            if (counterEl && counterEl.dataset.running === 'true') {
                let initialSeconds = parseInt(counterEl.dataset.initial) || 0;
                let startTimeStr = counterEl.dataset.startTime; // ISO String from server
                let startTime = startTimeStr ? new Date(startTimeStr).getTime() : null;
                
                setInterval(() => {
                    let totalSeconds = initialSeconds;
                    
                    if (startTime) {
                        let now = new Date().getTime();
                        let sessionSeconds = Math.floor((now - startTime) / 1000);
                        totalSeconds = initialSeconds + (sessionSeconds > 0 ? sessionSeconds : 0);
                    }
                    
                    // Update display
                    counterEl.textContent = formatDuration(totalSeconds);
                    
                    // Update hidden inputs in all forms
                    // We send ONLY the session increment to the backend
                    // Or actually, let's calculate exact diff on backend too
                    let diffFromInitial = totalSeconds - initialSeconds;
                    document.querySelectorAll('.elapsed-seconds-input').forEach(input => {
                        input.value = diffFromInitial > 0 ? diffFromInitial : 0;
                    });
                }, 1000);
            }
        });

        /**
         * Helper to format seconds to Indonesian duration string (JS version)
         */
        function formatDuration(seconds) {
            if (seconds < 1) return '0 detik';
            
            let hours = Math.floor(seconds / 3600);
            let minutes = Math.floor((seconds % 3600) / 60);
            let secs = seconds % 60;
            
            let parts = [];
            if (hours > 0) parts.push(hours + ' jam');
            if (minutes > 0) parts.push(minutes + ' menit');
            if (secs > 0 || parts.length === 0) parts.push(secs + ' detik');
            
            return parts.join(' ');
        }
    </script>
    @endpush

    @php
        $userRole = auth()->user()->role;
        $isOperatorOpd = $userRole === 'operator_opd';
        $isVerifikator = $userRole === 'verifikator';
        $isKepalaOpd = $userRole === 'kepala_opd';
        $isAdmin = $userRole === 'admin';
        $isFo = $userRole === 'fo';
        $isBo = $userRole === 'bo';
        $isKadin = $userRole === 'kadin';
        $isMultiOpd = $application->perijinan->is_multi_opd;

        $rekomFieldsQuery = $application->perijinan->formFields()->where('form_type', 'rekom')->where('is_active', true);
        if (auth()->user()->role === 'operator_opd' && auth()->user()->opd_id) {
            $rekomFieldsQuery->where(function($q) {
                $q->where('opd_id', auth()->user()->opd_id)->orWhereNull('opd_id');
            });
        }
        $rekomFields = $rekomFieldsQuery->orderBy('order')->get();
        
        $izinFields = $application->perijinan->formFields->where('form_type', 'izin')->where('is_active', true)->sortBy('order');

        // Validation flow variables
        $cv = $application->validasiRecords->where('order', $application->current_step)->first();

        // Find specific record for CURRENT USER
        $userAssignedRecord = $application->validasiRecords->first(function($v) {
            return $v->validationFlow && $v->validationFlow->assigned_user_id == auth()->id() && $v->status === 'pending';
        });

        // Parallel Validation Check for Multi-OPD
        $isParallelOpdTurn = false;
        if ($isMultiOpd && ($isOperatorOpd || $isKepalaOpd)) {
            $opdSteps = $application->validasiRecords->filter(function($v) {
                return $v->validationFlow && in_array($v->validationFlow->role, ['operator_opd', 'kepala_opd']);
            });
            $minOpdOrder = $opdSteps->min('order');

            // Logic: phase starts when current_step reaches the first OPD step
            if ($application->current_step >= $minOpdOrder && $userAssignedRecord) {
                // If Kepala OPD, must wait for their specific Operator in the same OPD
                if ($isKepalaOpd) {
                    $myOperatorRecord = $application->validasiRecords->first(function($v) {
                        return $v->validationFlow->role === 'operator_opd' && 
                               $v->validationFlow->assignedUser &&
                               $v->validationFlow->assignedUser->opd_id == auth()->user()->opd_id;
                    });
                    // Only turn if Operator has approved OR if there is no operator flow for this OPD (unlikely but safe)
                    $isParallelOpdTurn = !$myOperatorRecord || $myOperatorRecord->status === 'approved';
                } else {
                    // Operator can start as soon as phase is reached
                    $isParallelOpdTurn = true;
                }
            }
        }

        $canVal = false;
        if ($application->status !== 'approved' && $application->status !== 'diperbaiki' && $application->status !== 'perbaikan' && $application->status !== 'rejected') {
            if ($isParallelOpdTurn) {
                $canVal = true;
            } elseif ($cv && $cv->validationFlow->assigned_user_id === auth()->id()) {
                $canVal = true;
            }
        }

        // Fix: isFutureValidator should be false if canVal is true (parallel case)
        $isFutureValidator = (!$canVal && $userAssignedRecord && $userAssignedRecord->order > $application->current_step);

        // Strict sequential editing logic for Recommendation Form
        // For Multi-OPD, any OPD operator can edit if phase is reached
        $canEditRekom = ($isOperatorOpd || $isAdmin) && !in_array($application->status, ['approved', 'diperbaiki', 'rejected', 'perbaikan']);
        if ($isOperatorOpd && !$isAdmin && $isMultiOpd) {
            $opdSteps = $application->validasiRecords->filter(fn($v) => in_array($v->validationFlow->role, ['operator_opd', 'kepala_opd']));
            $minOpdOrder = $opdSteps->min('order');
            $canEditRekom = ($application->current_step >= $minOpdOrder);
        } elseif ($isOperatorOpd && !$isAdmin) {
            // Single OPD sequential
            $canEditRekom = ($cv && $cv->validationFlow->assigned_user_id === auth()->id() && $cv->validationFlow->role === 'operator_opd');
        }

        // Strict sequential editing logic for Izin Form
        $isIzinTurn = ($cv && $cv->validationFlow->assigned_user_id === auth()->id() && $cv->validationFlow->role === 'verifikator');
        $canEditIzin = ($isVerifikator || $isAdmin) && $isIzinTurn && !in_array($application->status, ['approved', 'diperbaiki', 'rejected', 'perbaikan']);

        // Strict sequential editing logic for BO Form
        $isBoTurn = ($cv && $cv->validationFlow->assigned_user_id === auth()->id() && $cv->validationFlow->role === 'bo');
        $canEditBo = ($isBo || $isAdmin) && $isBoTurn && !in_array($application->status, ['approved', 'diperbaiki', 'rejected', 'perbaikan']);

        // Admin is purely an observer/monitor and cannot edit or validate
        if ($isAdmin) {
            $canVal = false;
            $canEditRekom = false;
            $canEditIzin = false;
            $canEditBo = false;
        }

        $boFields = $application->perijinan->has_bo_form
            ? $application->perijinan->formFields()->where('form_type', 'bo')->where('is_active', true)->orderBy('order')->get()
            : collect([]);

        // Tab visibility logic
        $showRekomTab = !$application->is_pembetulan && ($isOperatorOpd || $isKepalaOpd || $isAdmin || $isVerifikator || $isKadin);
        $showIzinTab = ($isVerifikator || $isAdmin || $isKadin);
    @endphp
    <!-- Navigation Tabs -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            <li class="mr-2">
                <button type="button" onclick="switchTab('data-pemohon')" id="tab-btn-data-pemohon"
                    class="tab-btn inline-flex items-center gap-2 p-4 border-b-2 rounded-t-lg transition-all">
                    <i class="mdi mdi-account-box-outline"></i> Data dan Berkas Pemohon
                </button>
            </li>
            @if($showRekomTab)
            <li class="mr-2">
                <button type="button" onclick="switchTab('dokumen-rekom')" id="tab-btn-dokumen-rekom"
                    class="tab-btn inline-flex items-center gap-2 p-4 border-b-2 rounded-t-lg transition-all">
                    <i class="mdi mdi-file-document-edit-outline"></i> Dokumen Rekomendasi
                </button>
            </li>
            @endif
            @if($showIzinTab)
            <li class="mr-2">
                <button type="button" onclick="switchTab('dokumen-izin')" id="tab-btn-dokumen-izin"
                    class="tab-btn inline-flex items-center gap-2 p-4 border-b-2 rounded-t-lg transition-all">
                    <i class="mdi mdi-certificate-outline"></i> Dokumen Izin
                </button>
            </li>
            @endif
        </ul>

        @php
            $myFinishedRecord = $application->validasiRecords->where('user_id', auth()->id())->where('status', '!=', 'pending')->sortByDesc('validated_at')->first();
            $activeTask = ($canVal) ? (($isParallelOpdTurn && $userAssignedRecord) ? $userAssignedRecord : $cv) : null;
        @endphp

        @if($canVal || $myFinishedRecord)
            @php
                $isRunning = (bool)$canVal;
                $initialSeconds = $isRunning ? ($activeTask->duration_seconds ?? 0) : ($myFinishedRecord->duration_seconds ?? 0);
                $bgColor = $isRunning ? 'bg-blue-50 dark:bg-blue-900/30' : 'bg-red-50 dark:bg-red-900/20';
                $borderColor = $isRunning ? 'border-blue-100 dark:border-blue-800' : 'border-red-100 dark:border-red-900/30';
                $textColor = $isRunning ? 'text-blue-700 dark:text-blue-300' : 'text-red-700 dark:text-red-400';
                $iconColor = $isRunning ? 'text-blue-600' : 'text-red-600';
                $labelColor = $isRunning ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400';
            @endphp
            <div class="flex items-center gap-3 px-4 py-2 {{ $bgColor }} border {{ $borderColor }} rounded-xl mb-2 md:mb-0">
                <div class="flex flex-col">
                    <span class="text-[9px] font-black {{ $labelColor }} uppercase tracking-widest">SLA {{ $isRunning ? 'Counter' : 'Record' }}</span>
                    <div class="flex items-center gap-2">
                        <i class="mdi {{ $isRunning ? 'mdi-timer-outline animate-pulse' : 'mdi-timer-off-outline' }} {{ $iconColor }}"></i>
                        <span id="sla-counter" class="text-sm font-mono font-black {{ $textColor }}"
                            data-initial="{{ $initialSeconds }}"
                            data-start-time="{{ $isRunning && $activeTask && $activeTask->sla_start_at ? $activeTask->sla_start_at->toIso8601String() : '' }}"
                            data-running="{{ $isRunning ? 'true' : 'false' }}">
                            {{ formatDuration($initialSeconds) }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">

            <!-- TAB 1: DATA DAN BERKAS PEMOHON -->
            <div id="tab-panel-data-pemohon" class="tab-content-panel space-y-6">
                @if($application->is_pembetulan && $application->alasan_pembetulan)
                    <div class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl p-5 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/40 flex items-center justify-center flex-shrink-0 border border-red-200 dark:border-red-800">
                                <i class="mdi mdi-alert-circle-outline text-red-600 dark:text-red-400 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-red-800 dark:text-red-300 uppercase tracking-tight mb-1">Alasan Pembetulan Izin</h4>
                                <p class="text-xs text-red-700 dark:text-red-400 leading-relaxed font-semibold italic">"{{ $application->alasan_pembetulan }}"</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($isFutureValidator && $application->status !== 'approved' && $application->status !== 'diperbaiki' && $application->status !== 'rejected')
                    <div class="p-4 bg-rose-50 dark:bg-rose-900/20 border-l-4 border-rose-500 rounded-r-xl flex gap-3 shadow-sm mb-6">
                        <i class="mdi mdi-lock-clock text-rose-600 text-xl mt-0.5"></i>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-rose-800 dark:text-rose-300 uppercase tracking-tight">Belum Waktu Tindakan</h4>
                            <p class="text-xs text-rose-700 dark:text-rose-400 leading-relaxed mt-1">Anda (sebagai <strong>{{ auth()->user()->role_label }}</strong>) belum dapat melakukan tindakan apa pun karena permohonan masih dalam <strong>tahapan {{ $cv->validationFlow->role_label ?? 'Lainnya' }}</strong>.</p>
                        </div>
                    </div>
                @endif
                <!-- Card: Tombol Lihat Data Formulir -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center border border-amber-200 dark:border-amber-800">
                                <i class="mdi mdi-form-select text-amber-600 dark:text-amber-400 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-800 dark:text-white text-base">Isian Formulir Global</h2>
                                <p class="text-gray-500 text-[10px] uppercase font-bold mt-0.5 tracking-wider">Data Isian yang diinput oleh pemohon</p>
                            </div>
                        </div>
                        <button type="button" onclick="openFormDataModal()" class="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold uppercase transition-all shadow-md active:scale-95">
                            <i class="mdi mdi-eye text-base"></i>
                            Lihat Isian Formulir
                        </button>
                    </div>
                </div>


                <!-- Card: Dokumen Surat Pengajuan -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center border border-blue-200 dark:border-blue-800">
                                <i class="mdi mdi-file-document-multiple text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-800 dark:text-white text-base">Dokumen Surat Pengajuan</h2>
                                <p class="text-gray-500 text-[10px] uppercase font-bold mt-0.5 tracking-wider">Digenerate otomatis dari data pemohon</p>
                            </div>
                        </div>
                        @if(!$isFo && !$isBo && !$isAdmin)
                        <form action="{{ route('data-perijinan.regenerate-documents', $application->id) }}" method="POST" onsubmit="return confirm('Lanjutkan?');">
                            @csrf <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800 rounded-lg text-[10px] font-bold uppercase transition-all"><i class="mdi mdi-sync text-sm"></i> Update</button>
                        </form>
                        @endif
                    </div>
                    <div class="p-5">
                        @php
                            $basicDocs = [
                                ['label' => 'Surat Pernyataan', 'desc' => 'Pernyataan kebenaran data', 'icon' => 'mdi-file-certificate-outline', 'file' => $application->file_pernyataan],
                                ['label' => 'Surat Permohonan', 'desc' => 'Permohonan resmi instansi', 'icon' => 'mdi-file-send-outline', 'file' => $application->file_permohonan],
                                ['label' => 'Surat Keabsahan', 'desc' => 'Pernyataan keabsahan dokumen', 'icon' => 'mdi-file-check-outline', 'file' => $application->file_keabsahan],
                            ];
                        @endphp
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @foreach($basicDocs as $doc)
                                @if($doc['file'])
                                    @php $rp = str_replace('uploads/perijinan/', '', $doc['file']); @endphp
                                    <div class="rounded-2xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 overflow-hidden flex flex-col transition-all hover:shadow-md">
                                        <div class="p-4 flex-1">
                                            <div class="flex items-start gap-3 mb-3">
                                                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center border border-blue-200 dark:border-blue-800 flex-shrink-0"><i class="mdi {{ $doc['icon'] }} text-xl text-blue-600"></i></div>
                                                <div class="flex-1 min-w-0">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black text-white bg-blue-500 mb-1 uppercase">PDF</span>
                                                    <h4 class="text-xs font-bold text-gray-800 dark:text-white leading-tight truncate">{{ $doc['label'] }}</h4>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 mt-auto">
                                                <button onclick="openPdfPreview('{{ asset($doc['file']) }}', '{{ $doc['label'] }}')" class="flex-1 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-bold uppercase tracking-wider">Pratinjau</button>
                                                <a href="{{ route('data-perijinan.download-file', $rp) }}" class="p-1.5 bg-white dark:bg-gray-800 border border-gray-200 rounded-lg text-gray-600 transition-colors"><i class="mdi mdi-download"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                @php
                    // Who can VIEW the BO form:
                    // - BO: can edit (if their turn) or read-only
                    // - Admin: always read-only
                    // - Verifikator & Kadin: read-only (validators above BO who need to review BO data)
                    $canViewBoForm = $application->perijinan->has_bo_form
                        && ($isBo || $isAdmin || $isVerifikator || $isKadin)
                        && ($canEditBo || !empty($application->bo_data));
                @endphp
                @if($canViewBoForm)
                <!-- Card: Form Khusus BO -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-emerald-200 dark:border-emerald-900/30 overflow-hidden mb-6">
                    <div class="px-5 py-4 border-b border-emerald-100 dark:border-emerald-900/50 bg-emerald-50/50 dark:bg-emerald-800/10 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center border border-emerald-200 dark:border-emerald-800">
                                <i class="mdi mdi-account-cog text-emerald-600 dark:text-emerald-400 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-800 dark:text-white text-base">Form Khusus BO</h2>
                                <p class="text-gray-500 text-[10px] uppercase font-bold mt-0.5 tracking-wider">Formulir Khusus Back Office (BO)</p>
                            </div>
                        </div>
                        @if(!$canEditBo)
                            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-[10px] font-bold rounded-full uppercase border dark:border-gray-600">Hanya Baca</span>
                        @endif
                    </div>
                    <div class="p-5">
                        @if($canEditBo)
                            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 rounded-r-xl flex flex-col gap-1">
                                <div class="flex gap-3">
                                    <i class="mdi mdi-information-outline text-emerald-600 text-xl mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-xs text-emerald-700 dark:text-emerald-300 leading-relaxed">Lengkapi data formulir khusus BO berikut ini.</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('data-perijinan.bo-data.save', $application->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <input type="hidden" name="elapsed_seconds" class="elapsed-seconds-input" value="0">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($boFields as $field)
                                    <div class="space-y-2 {{ $field->type === 'table' ? 'md:col-span-2' : '' }}">
                                        <label class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-tighter">{{ $field->label }} @if($field->is_required)<span class="text-red-500">*</span>@endif</label>
                                        @php 
                                                                                        $val = $application->bo_data[$field->name] ?? null; 
                                            $isAlreadySaved = array_key_exists($field->name, $application->bo_data ?? []);

                                            // Auto-fill from global form ONLY if never saved before
                                            if (!$isAlreadySaved) {
                                                $matchingGlobalField = $application->perijinan->activeFormFields
                                                    ->where('form_type', 'global')
                                                    ->where('name', $field->name)
                                                    ->first() ?? $application->perijinan->activeFormFields
                                                    ->where('form_type', 'global')
                                                    ->filter(function($f) use ($field) {
                                                        return strtolower($f->label) === strtolower($field->label);
                                                    })->first();

                                                if ($matchingGlobalField) {
                                                    if ($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar') {
                                                        $globalFiles = $application->form_files[$matchingGlobalField->id] ?? [];
                                                        $val = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                                                    } else {
                                                        $val = $application->form_data[$matchingGlobalField->id] ?? '';
                                                        if (is_array($val)) $val = implode(', ', $val);
                                                    }
                                                }
                                            }

                                            $val = $val ?? '';
                                            $ro = !$canEditBo ? 'readonly disabled' : '';
                                            $cls = "w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all";
                                            
                                            // Dynamic variable override
                                            $isDynamic = !empty($field->dynamic_variable);
                                            if ($isDynamic) {
                                                $val = resolveDynamicVariable($application, $field->dynamic_variable);
                                                $ro = 'readonly disabled';
                                                $cls .= ' bg-amber-50/50 dark:bg-amber-900/10 border-amber-200 dark:border-amber-700';
                                            }
                                        @endphp

                                        @if($field->type === 'textarea')
                                            <textarea name="{{ $field->name }}" class="{{ $cls }} min-h-[120px]" {{ $ro }}>{{ $val }}</textarea>
                                        @elseif($field->type === 'select')
                                            <select name="{{ $field->name }}" class="{{ $cls }}" {{ $ro }}>
                                                <option value="">-- Pilih --</option>
                                                @php
                                                    $options = $field->options ?? [];
                                                    if ($val !== '' && $val !== null) {
                                                        $existsCaseInsensitive = false;
                                                        foreach ($options as $opt) {
                                                            if (strtolower($opt) === strtolower($val)) {
                                                                $existsCaseInsensitive = true;
                                                                break;
                                                            }
                                                        }
                                                        if (!$existsCaseInsensitive) {
                                                            $options[] = $val;
                                                        }
                                                    }
                                                @endphp
                                                @foreach($options as $opt)
                                                    <option value="{{ $opt }}" {{ strtolower($val) == strtolower($opt) ? 'selected' : '' }}>{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar')
                                            <div class="space-y-2">
                                                <input type="file" name="{{ $field->name }}" class="{{ $cls }}" {{ $ro }} accept="{{ ($field->type === 'pas_foto' || $field->type === 'gambar') ? '.jpg,.jpeg,.png' : '*' }}">
                                                @if($val)
                                                    @if($field->type === 'pas_foto')
                                                        <div class="mb-2 mt-2">
                                                            <img src="{{ asset($val) }}" style="width: 2.79cm; height: 3.81cm; object-fit: cover;" class="rounded border shadow-sm" alt="Pas Foto" />
                                                        </div>
                                                    @elseif($field->type === 'gambar')
                                                        <div class="mb-2 mt-2">
                                                            <img src="{{ asset($val) }}" style="max-width: 300px; max-height: 200px; object-fit: contain;" class="rounded border shadow-sm" alt="Gambar" />
                                                        </div>
                                                    @endif
                                                    <div class="flex items-center gap-2 mt-2 p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg border border-emerald-100 dark:border-emerald-800">
                                                        <i class="mdi mdi-file-check text-emerald-600"></i>
                                                        <a href="{{ asset($val) }}" target="_blank" class="text-xs font-bold text-emerald-700 dark:text-emerald-300 hover:underline truncate">Lihat File Terupload</a>
                                                    </div>
                                                @endif

                                                @php
                                                    $matchingGlobalField = $application->perijinan->activeFormFields
                                                        ->where('form_type', 'global')
                                                        ->where('name', $field->name)
                                                        ->first() ?? $application->perijinan->activeFormFields
                                                        ->where('form_type', 'global')
                                                        ->filter(function($f) use ($field) {
                                                            return strtolower($f->label) === strtolower($field->label);
                                                        })->first();
                                                    
                                                    $globalFile = null;
                                                    if ($matchingGlobalField) {
                                                        $globalFiles = $application->form_files[$matchingGlobalField->id] ?? [];
                                                        $globalFile = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                                                    }
                                                @endphp
                                                @if($globalFile && $globalFile !== $val)
                                                    <div class="flex items-center gap-2 mt-1 p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-100 dark:border-amber-800/50">
                                                        <i class="mdi mdi-information-outline text-amber-600 text-sm"></i>
                                                        <span class="text-[10px] text-amber-700 dark:text-amber-400 font-bold">Referensi Pemohon:</span>
                                                        <a href="{{ asset($globalFile) }}" target="_blank" class="text-[10px] font-bold text-blue-600 hover:underline truncate">Buka Berkas Pemohon</a>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($field->type === 'date')
                                            <input type="date" name="{{ $field->name }}" value="{{ $val }}" class="{{ $cls }}" {{ $ro }}>
                                        @elseif($field->type === 'number')
                                            <input type="number" name="{{ $field->name }}" value="{{ $val }}" class="{{ $cls }}" {{ $ro }}>
                                        @elseif($field->type === 'table')
                                            @include('components.form-field.table-input', ['field' => $field, 'val' => $val, 'ro' => $ro])
                                        @else
                                            <input type="text" name="{{ $field->name }}" value="{{ $val }}" class="{{ $cls }}" {{ $ro }}>
                                        @endif
                                        @if($field->help_text)
                                            <p class="text-[10px] text-gray-400 italic mt-1">{{ $field->help_text }}</p>
                                        @endif
                                        @if(!empty($field->dynamic_variable))
                                            <div class="flex items-center gap-1.5 mt-1.5 px-2.5 py-1.5 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-100 dark:border-amber-800/50">
                                                <i class="mdi mdi-link-variant text-amber-500 text-sm"></i>
                                                <p class="text-[10px] text-amber-700 dark:text-amber-400 font-medium">
                                                    <i class="mdi mdi-lock-outline"></i> Input ini diisi otomatis dari variabel dinamis <code class="font-mono bg-amber-100 dark:bg-amber-900/30 px-1 py-0.5 rounded text-[9px]">{{ $field->dynamic_variable }}</code>. Nilai akan tampil pada surat.
                                                </p>
                                            </div>
                                        @endif
                                        @error($field->name)
                                            <p class="text-xs text-red-500 font-bold mt-1"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            {{-- Pembetulan: BO upload izin PDF section --}}
                            @if($application->is_pembetulan)
                            <div class="mt-8 pt-6 border-t-2 border-dashed border-blue-200 dark:border-blue-800">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-9 h-9 bg-blue-100 dark:bg-blue-900/40 rounded-xl flex items-center justify-center border border-blue-200 dark:border-blue-800">
                                        <i class="mdi mdi-file-sign text-blue-600 dark:text-blue-400 text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-black text-blue-800 dark:text-blue-300 uppercase tracking-wide">Unggah Surat Izin (Siap TTE)</h4>
                                        <p class="text-[10px] text-blue-600 dark:text-blue-400">Wajib — file ini akan diperiksa Verifikator lalu ditandatangani Kadin</p>
                                    </div>
                                </div>

                                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800 space-y-3">
                                    <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                                        <i class="mdi mdi-information-outline mr-1"></i>
                                        Siapkan template surat izin dalam format <strong>Word (.docx)</strong> yang berisi variabel dinamis (misal: ${NAMA_PEMOHON}, ${QRCODE}).
                                        Sistem akan otomatis mengganti variabel tersebut dengan data asli dan mengonversinya menjadi PDF yang siap ditandatangani secara elektronik (TTE) oleh Kadin.
                                    </p>

                                    <div class="space-y-2">
                                        @if($canEditBo)
                                            <input type="file" name="file_izin_pembetulan" id="file_izin_pembetulan"
                                                class="w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:transition-all cursor-pointer"
                                                accept=".docx">
                                            @error('file_izin_pembetulan')
                                                <p class="text-xs text-red-500 font-bold mt-1"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                                            @enderror
                                        @endif

                                        @if($application->file_izin_pembetulan && file_exists(public_path($application->file_izin_pembetulan)))
                                            @php
                                                $docxTemplatePath = str_replace('.pdf', '_template.docx', $application->file_izin_pembetulan);
                                            @endphp
                                            <div class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-xl border border-blue-200 dark:border-blue-800">
                                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/60 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <i class="mdi mdi-file-word text-blue-600 dark:text-blue-400 text-lg"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-bold text-blue-700 dark:text-blue-300">Template DOCX Terupload <span class="font-normal text-[10px] text-blue-500 dark:text-blue-400">(siap dikonversi ke PDF)</span></p>
                                                    <p class="text-[10px] text-blue-600 dark:text-blue-400 truncate">{{ file_exists(public_path($docxTemplatePath)) ? basename($docxTemplatePath) : basename($application->file_izin_pembetulan) }}</p>
                                                </div>
                                                <div class="flex items-center gap-2 flex-shrink-0">
                                                    @if(file_exists(public_path($docxTemplatePath)))
                                                        <a href="{{ asset($docxTemplatePath) }}" download
                                                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-bold transition-all flex items-center gap-1 shadow-sm">
                                                            <i class="mdi mdi-download"></i> Unduh DOCX Template
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                                                <i class="mdi mdi-alert-circle-outline text-amber-600"></i>
                                                <p class="text-xs text-amber-700 dark:text-amber-400 font-bold">Belum ada file template DOCX yang diunggah. Harap unggah sebelum menyetujui.</p>
                                            </div>
                                        @endif

                                        {{-- Dynamic Variable Trigger Button --}}
                                        <button type="button" onclick="document.getElementById('modal-variabel-dinamis').classList.remove('hidden')"
                                            class="mt-1 w-full flex items-center justify-center gap-2 px-3 py-2 bg-indigo-50 dark:bg-indigo-900/20 border border-dashed border-indigo-300 dark:border-indigo-700 rounded-xl text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider hover:bg-indigo-100 transition-colors">
                                            <i class="mdi mdi-code-braces text-sm"></i>
                                            Lihat Daftar Variabel Dinamis
                                        </button>
                                    </div>


                                </div>
                            </div>
                            @endif

                            @if($canEditBo)
                                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg flex items-center gap-3 active:scale-95"><i class="mdi mdi-content-save-check text-lg"></i> SIMPAN DATA BO</button>
                                </div>
                            @endif
                        </form>

                    </div>
                </div>
                @endif
            </div>

            <!-- TAB 2: DOKUMEN REKOMENDASI -->
            @if($showRekomTab)
            <div id="tab-panel-dokumen-rekom" class="tab-content-panel hidden space-y-6">
                @if($application->perijinan->is_multi_opd)
                    @php
                        $involvedOpds = $application->perijinan->activeValidationFlows
                            ->whereIn('role', ['operator_opd', 'kepala_opd'])
                            ->whereNotNull('assigned_user_id')
                            ->map(fn($f) => $f->assignedUser->opd)
                            ->filter()
                            ->unique('id');
                        
                        $isHighLevel = $isAdmin || $isVerifikator || $isKadin;
                        $myOpd = auth()->user()->opd;
                    @endphp

                    @if($isKepalaOpd && $myOpd)
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-r-xl flex gap-3 shadow-sm mb-4">
                            <i class="mdi mdi-information-variant text-blue-600 text-xl mt-0.5"></i>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300 uppercase">Informasi Verifikasi</h4>
                                <p class="text-xs text-blue-700 dark:text-blue-400 leading-relaxed mt-1">Berikut adalah <strong>Draft Surat Rekomendasi</strong> dari Operator {{ $myOpd->nama_opd }}. Silahkan berikan TTE pada Draft agar menjadi Surat Rekomendasi Resmi.</p>
                            </div>
                        </div>
                    @endif

                    @foreach($involvedOpds as $opd)
                        @php
                            $isMyOpd = auth()->user()->opd_id == $opd->id;
                            $opdRekomData = $application->rekom_data_multi[$opd->id] ?? [];
                            $opdFileRekom = $application->file_rekom_multi[$opd->id] ?? null;
                            
                            // Check if this OPD has finished their part (Head approved)
                            $opdHeadValidation = $application->validasiRecords->first(function($v) use ($opd) {
                                return $v->validationFlow->role === 'kepala_opd' && $v->validationFlow->assignedUser->opd_id == $opd->id;
                            });
                            $isOpdFinished = $opdHeadValidation && $opdHeadValidation->status === 'approved';
                        @endphp

                        @if($isHighLevel || $isMyOpd)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $isMyOpd ? 'border-purple-300 ring-1 ring-purple-100' : 'border-gray-200' }} overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center border border-purple-200 dark:border-purple-800"><i class="mdi mdi-office-building text-purple-600 dark:text-purple-400 text-xl"></i></div>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-800 dark:text-white">Rekomendasi: {{ $opd->nama_opd }}</h3>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Status:</span>
                                            @if($isOpdFinished)
                                                <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[8px] font-black uppercase">Selesai</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[8px] font-black uppercase">Draft</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($opdFileRekom)
                                    <div class="flex gap-2">
                                        <button onclick="openPdfPreview('{{ asset($opdFileRekom) }}?t={{ time() }}', 'Draft Rekomendasi {{ $opd->nama_opd }}')" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-[9px] font-black uppercase shadow-sm">Buka PDF</button>
                                    </div>
                                @endif
                            </div>

                            @php 
                                $signedFile = $application->file_rekom_multi_tte[$opd->id] ?? null;
                            @endphp

                            @if($signedFile)
                            <!-- Signed Document Card (New) -->
                            <div class="px-5 py-4 border-t border-purple-100 dark:border-purple-900/50 bg-green-50/30 dark:bg-green-900/10 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center border border-green-200 dark:border-green-800"><i class="mdi mdi-file-check text-green-600 dark:text-green-400 text-lg"></i></div>
                                    <div>
                                        <h3 class="text-[11px] font-bold text-gray-800 dark:text-white">Surat Rekomendasi Resmi (TTE)</h3>
                                        <p class="text-[9px] text-green-600 font-bold uppercase tracking-wider">Terbit</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    @if($isKepalaOpd || $isKadin || $isAdmin || $isVerifikator)
                                        <button onclick="verifyEsignPdf('rekom', {{ $opd->id }})" class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-[9px] font-black uppercase shadow-sm" title="Verifikasi TTE"><i class="mdi mdi-shield-check"></i> Cek Dokumen TTE</button>
                                    @endif
                                    <button onclick="openPdfPreview('{{ asset($signedFile) }}?t={{ time() }}', 'Surat Rekomendasi Resmi {{ $opd->nama_opd }}')" class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-[9px] font-black uppercase shadow-sm">Buka PDF</button>
                                </div>
                            </div>
                            @endif

                            @if($isMyOpd && $isOperatorOpd && $canEditRekom)
                            <div class="p-6">
                                @if($application->perijinan->keterangan_rekom)
                                    <div class="mb-6 p-4 bg-purple-50 dark:bg-purple-900/20 border-l-4 border-purple-500 rounded-r-xl flex gap-3 shadow-sm">
                                        <i class="mdi mdi-information-outline text-purple-600 text-xl mt-0.5"></i>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-bold text-purple-800 dark:text-purple-300 uppercase">Panduan Pengisian</h4>
                                            <p class="text-xs text-purple-700 dark:text-purple-400 leading-relaxed mt-1 italic">
                                                {!! nl2br(e($application->perijinan->keterangan_rekom)) !!}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                <form action="{{ route('data-perijinan.rekom-data.save', $application->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="opd_id" value="{{ $opd->id }}">
                                    <input type="hidden" name="elapsed_seconds" class="elapsed-seconds-input" value="0">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach($rekomFields->filter(fn($f) => $f->opd_id == $opd->id || $f->opd_id === null) as $field)
                                            <div class="space-y-2 {{ $field->type === 'table' ? 'md:col-span-2' : '' }}">
                                                <label class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-tighter">{{ $field->label }} @if($field->is_required)<span class="text-red-500">*</span>@endif</label>
                                                @php 
                                                    $val = $opdRekomData[$field->name] ?? null; 
                                                    $isAlreadySaved = array_key_exists($field->name, $opdRekomData);

                                                    if (!$isAlreadySaved) {
                                                        if ($application->perijinan->has_bo_form) {
                                                            $matchingBoField = $application->perijinan->activeFormFields
                                                                ->where('form_type', 'bo')
                                                                ->where('name', $field->name)
                                                                ->first() ?? $application->perijinan->activeFormFields
                                                                ->where('form_type', 'bo')
                                                                ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                                                ->first();

                                                            if ($matchingBoField) {
                                                                $val = $application->bo_data[$matchingBoField->name] ?? null;
                                                            }
                                                        } else {
                                                            $matchingGlobalField = $application->perijinan->activeFormFields
                                                                ->where('form_type', 'global')
                                                                ->where('name', $field->name)
                                                                ->first() ?? $application->perijinan->activeFormFields
                                                                ->where('form_type', 'global')
                                                                ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                                                ->first();

                                                            if ($matchingGlobalField) {
                                                                if ($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar') {
                                                                    $globalFiles = $application->form_files[$matchingGlobalField->id] ?? [];
                                                                    $val = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                                                                } else {
                                                                    $val = $application->form_data[$matchingGlobalField->id] ?? '';
                                                                    if (is_array($val)) $val = implode(', ', $val);
                                                                }
                                                            }
                                                        }
                                                    }

                                                    $val = $val ?? '';
                                                    $ro = '';
                                                    $cls = "w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-purple-500 outline-none transition-all"; 
                                                @endphp

                                                @if($field->type === 'textarea')
                                                    <textarea name="{{ $field->name }}" {{ $ro }} class="{{ $cls }} min-h-[120px]">{{ $val }}</textarea>
                                                @elseif($field->type === 'select')
                                                    <select name="{{ $field->name }}" class="{{ $cls }}" {{ $ro }}>
                                                        <option value="">-- Pilih --</option>
                                                        @php
                                                            $options = $field->options ?? [];
                                                            if ($val !== '' && $val !== null) {
                                                                $existsCaseInsensitive = false;
                                                                foreach ($options as $opt) {
                                                                    if (strtolower($opt) === strtolower($val)) {
                                                                        $existsCaseInsensitive = true;
                                                                        break;
                                                                    }
                                                                }
                                                                if (!$existsCaseInsensitive) {
                                                                    $options[] = $val;
                                                                }
                                                            }
                                                        @endphp
                                                        @foreach($options as $opt)
                                                            <option value="{{ $opt }}" {{ strtolower($val) == strtolower($opt) ? 'selected' : '' }}>{{ $opt }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($field->type === 'pas_foto' || $field->type === 'file' || $field->type === 'gambar')
                                                    <div class="space-y-2">
                                                        <input type="file" name="{{ $field->name }}" class="{{ $cls }}" accept="{{ ($field->type === 'pas_foto' || $field->type === 'gambar') ? '.jpg,.jpeg,.png' : '*' }}">
                                                        @if($val)
                                                            @if($field->type === 'pas_foto')
                                                                <div class="mb-2">
                                                                    <img src="{{ asset($val) }}" style="width: 2.79cm; height: 3.81cm; object-fit: cover;" class="rounded border shadow-sm" alt="Pas Foto" />
                                                                </div>
                                                            @elseif($field->type === 'gambar')
                                                                <div class="mb-2">
                                                                    <img src="{{ asset($val) }}" style="max-width: 300px; max-height: 200px; object-fit: contain;" class="rounded border shadow-sm" alt="Gambar" />
                                                                </div>
                                                            @endif
                                                            <div class="flex items-center gap-2 mt-2 p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg border border-purple-100 dark:border-purple-800">
                                                                <i class="mdi mdi-file-check text-purple-600"></i>
                                                                <a href="{{ asset($val) }}" target="_blank" class="text-xs font-bold text-purple-700 dark:text-purple-300 hover:underline truncate">Lihat File Terupload</a>
                                                            </div>
                                                        @endif

                                                        @php
                                                            $matchingGlobalField = $application->perijinan->activeFormFields
                                                                ->where('form_type', 'global')
                                                                ->where('name', $field->name)
                                                                ->first() ?? $application->perijinan->activeFormFields
                                                                ->where('form_type', 'global')
                                                                ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                                                ->first();
                                                            
                                                            $globalFile = null;
                                                            if ($matchingGlobalField) {
                                                                $globalFiles = $application->form_files[$matchingGlobalField->id] ?? [];
                                                                $globalFile = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                                                            }
                                                        @endphp
                                                        @if($globalFile && $globalFile !== $val)
                                                            <div class="flex items-center gap-2 mt-1 p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-100 dark:border-amber-800/50">
                                                                <i class="mdi mdi-information-outline text-amber-600 text-sm"></i>
                                                                <span class="text-[10px] text-amber-700 dark:text-amber-400 font-bold">Referensi Pemohon:</span>
                                                                <a href="{{ asset($globalFile) }}" target="_blank" class="text-[10px] font-bold text-blue-600 hover:underline truncate">Buka Berkas Pemohon</a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @elseif($field->type === 'date')
                                                    <input type="date" name="{{ $field->name }}" value="{{ $val }}" class="{{ $cls }}">
                                                @elseif($field->type === 'number')
                                                    <input type="number" name="{{ $field->name }}" value="{{ $val }}" class="{{ $cls }}">
                                                @elseif($field->type === 'table')
                                                    @include('components.form-field.table-input', ['field' => $field, 'val' => $val, 'ro' => ''])
                                                @else
                                                    <input type="text" name="{{ $field->name }}" value="{{ $val }}" class="{{ $cls }}">
                                                @endif
                                                @if($field->help_text)
                                                    <p class="text-[10px] text-gray-400 italic mt-1">{{ $field->help_text }}</p>
                                                @endif
                                                @if(!empty($field->dynamic_variable))
                                                    <div class="flex items-center gap-1.5 mt-1.5 px-2.5 py-1.5 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-100 dark:border-amber-800/50">
                                                        <i class="mdi mdi-link-variant text-amber-500 text-sm"></i>
                                                        <p class="text-[10px] text-amber-700 dark:text-amber-400 font-medium">
                                                            <i class="mdi mdi-lock-outline"></i> Input ini diisi otomatis dari variabel dinamis <code class="font-mono bg-amber-100 dark:bg-amber-900/30 px-1 py-0.5 rounded text-[9px]">{{ $field->dynamic_variable }}</code>. Nilai akan tampil pada surat.
                                                        </p>
                                                    </div>
                                                @endif
                                                @error($field->name)
                                                    <p class="text-xs text-red-500 font-bold mt-1"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Masa Aktif Rekomendasi Field -->
                                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                                        <div class="max-w-xs">
                                            <input type="date" name="masa_aktif_rekom" value="{{ $opdRekomData['masa_aktif_rekom'] ?? '' }}" required
                                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-purple-500 outline-none transition-all">
                                            @error('masa_aktif_rekom')
                                                <p class="text-xs text-red-500 font-bold mt-1"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                                            @enderror
                                            <p class="text-[10px] text-gray-400 mt-2 italic">Atur batas waktu berlakunya surat rekomendasi dari OPD Anda.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                                        <button type="submit" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg flex items-center gap-3 active:scale-95"><i class="mdi mdi-content-save-check text-lg"></i> SIMPAN & GENERATE REKOMENDASI</button>
                                    </div>
                                </form>
                            </div>
                            @else
                            <div class="p-6 bg-gray-50/50 dark:bg-gray-900/20">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($rekomFields->filter(fn($f) => $f->opd_id == $opd->id || $f->opd_id === null) as $field)
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 {{ $field->type === 'table' ? 'md:col-span-2' : '' }}">
                                            <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ $field->label }}</label>
                                            <div class="text-xs font-semibold text-gray-700 dark:text-gray-200">
                                                @php $v = $opdRekomData[$field->name] ?? '-'; @endphp
                                                @if($field->type === 'table')
                                                    @include('components.form-field.table-input', [
                                                        'field' => $field,
                                                        'val' => is_array($v) ? $v : [],
                                                        'ro' => 'readonly disabled'
                                                    ])
                                                @elseif(($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar') && $v !== '-')
                                                    @if($field->type === 'pas_foto')
                                                        <div class="mb-2">
                                                            <img src="{{ asset($v) }}" style="width: 2.79cm; height: 3.81cm; object-fit: cover;" class="rounded border shadow-sm" alt="Pas Foto" />
                                                        </div>
                                                    @elseif($field->type === 'gambar')
                                                        <div class="mb-2">
                                                            <img src="{{ asset($v) }}" style="max-width: 300px; max-height: 200px; object-fit: contain;" class="rounded border shadow-sm" alt="Gambar" />
                                                        </div>
                                                    @endif
                                                    <a href="{{ asset($v) }}" target="_blank" class="text-blue-600 hover:underline">Buka Berkas</a>
                                                @else
                                                    {{ $v }}
                                                @endif
                                            </div>
                                            @if($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar')
                                                @php
                                                    $matchingGlobalField = $application->perijinan->activeFormFields
                                                        ->where('form_type', 'global')
                                                        ->where('name', $field->name)
                                                        ->first() ?? $application->perijinan->activeFormFields
                                                        ->where('form_type', 'global')
                                                        ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                                        ->first();
                                                    
                                                    $globalFile = null;
                                                    if ($matchingGlobalField) {
                                                        $globalFiles = $application->form_files[$matchingGlobalField->id] ?? [];
                                                        $globalFile = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                                                    }
                                                @endphp
                                                @if($globalFile && $globalFile !== $v)
                                                    <div class="mt-2 flex items-center gap-1.5 text-[10px]">
                                                        <span class="text-amber-600 font-bold uppercase tracking-tighter">Ref Pemohon:</span>
                                                        <a href="{{ asset($globalFile) }}" target="_blank" class="text-blue-600 font-bold hover:underline">Lihat Dokumen</a>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                    
                                    @if(!empty($opdRekomData['masa_aktif_rekom']))
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
                                            <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Masa Aktif Rekomendasi</label>
                                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">
                                                {{ \Carbon\Carbon::parse($opdRekomData['masa_aktif_rekom'])->format('d M Y') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    @endforeach
                @else
                    <!-- Existing Standard Recommendation View -->
                    @if($isKepalaOpd)
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-r-xl flex gap-3 shadow-sm">
                            <i class="mdi mdi-information-variant text-blue-600 text-xl mt-0.5"></i>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300">Informasi Verifikasi</h4>
                                <p class="text-xs text-blue-700 dark:text-blue-400 leading-relaxed mt-1">Berikut adalah <strong>Draft Surat Rekomendasi</strong> dari Operator OPD. Silahkan berikan TTE pada Draft agar menjadi Surat Rekomendasi Resmi.</p>
                            </div>
                        </div>
                    @endif

                    @if($application->file_rekom && !empty($application->rekom_data))
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-purple-200 dark:border-purple-900/30 overflow-hidden">
                            <div class="px-5 py-4 border-b border-purple-100 dark:border-purple-900/50 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center border border-purple-200 dark:border-purple-800"><i class="mdi mdi-file-check text-purple-600 dark:text-purple-400 text-xl"></i></div>
                                    <div><h3 class="text-sm font-bold text-gray-800 dark:text-white">Draft Surat Rekomendasi</h3><p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Hasil Generate Otomatis</p></div>
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="openPdfPreview('{{ asset($application->file_rekom) }}?t={{ time() }}', 'Draft Surat Rekomendasi')" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-wider shadow-sm">Buka PDF</button>
                                </div>
                            </div>
                        </div>

                        @if($application->file_rekom_tte)
                        <!-- Signed Document Card (Single OPD) -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-green-200 dark:border-green-900/30 overflow-hidden mb-6">
                            <div class="px-5 py-4 border-b border-green-100 dark:border-green-900/50 bg-green-50/50 dark:bg-green-800/20 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/40 flex items-center justify-center border border-green-200 dark:border-green-800"><i class="mdi mdi-file-check text-green-600 dark:text-green-400 text-xl"></i></div>
                                    <div><h3 class="text-sm font-bold text-gray-800 dark:text-white">Surat Rekomendasi Resmi (TTE)</h3><p class="text-[10px] text-green-600 font-bold uppercase tracking-wider">Terbit</p></div>
                                </div>
                                <div class="flex gap-2">
                                    @if($isKepalaOpd || $isKadin || $isAdmin || $isVerifikator)
                                        <button onclick="verifyEsignPdf('rekom', null)" class="px-4 py-2 bg-green-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-wider shadow-sm" title="Verifikasi TTE"><i class="mdi mdi-shield-check"></i> Cek Dokumen TTE</button>
                                    @endif
                                    <button onclick="openPdfPreview('{{ asset($application->file_rekom_tte) }}?t={{ time() }}', 'Surat Rekomendasi Resmi')" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-wider shadow-sm">Buka PDF</button>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center border border-purple-200 dark:border-purple-800"><i class="mdi mdi-file-document-edit text-purple-600 dark:text-purple-400 text-xl"></i></div>
                                <div><h3 class="text-base font-bold text-gray-800 dark:text-white">Formulir Rekomendasi</h3><p class="text-[10px] text-purple-600 font-bold uppercase tracking-wider">Tugas Operator OPD</p></div>
                            </div>
                            @if(!$canEditRekom) <span class="px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold rounded-full uppercase border">Hanya Baca</span> @endif
                        </div>
                        <div class="p-6">
                            @if($isFutureValidator && !in_array($application->status, ['approved', 'diperbaiki', 'rejected', 'perbaikan']))
                                <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/20 border-l-4 border-rose-500 rounded-r-xl flex gap-3 shadow-sm">
                                    <i class="mdi mdi-lock-clock text-rose-600 text-xl mt-0.5"></i>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-bold text-rose-800 dark:text-rose-300 uppercase tracking-tight">Belum Waktu Tindakan</h4>
                                        <p class="text-xs text-rose-700 dark:text-rose-400 leading-relaxed mt-1">Anda (sebagai <strong>{{ auth()->user()->role_label }}</strong>) belum dapat melakukan tindakan pada tab ini karena permohonan masih dalam <strong>tahapan {{ $cv->validationFlow->role_label ?? 'Lainnya' }}</strong>.</p>
                                    </div>
                                </div>
                            @endif

                            @if($isOperatorOpd && $canEditRekom)
                                <div class="mb-6 p-4 bg-purple-50 dark:bg-purple-900/20 border-l-4 border-purple-500 rounded-r-xl flex flex-col gap-1">
                                    <div class="flex gap-3">
                                        <i class="mdi mdi-information-outline text-purple-600 text-xl mt-0.5"></i>
                                        <div class="flex-1">
                                            <p class="text-xs text-purple-700 dark:text-purple-300 leading-relaxed">Lengkapi data verifikasi teknis. Data ini akan otomatis digunakan dalam <strong>Surat Rekomendasi</strong>.</p>
                                            @if($application->perijinan->keterangan_rekom)
                                                <div class="mt-2 p-2 bg-white/50 dark:bg-purple-800/30 rounded-lg border border-purple-100 dark:border-purple-800">
                                                    <p class="text-[11px] text-purple-800 dark:text-purple-200 font-medium italic">
                                                        <i class="mdi mdi-lightbulb-on-outline mr-1"></i> {!! nl2br(e($application->perijinan->keterangan_rekom)) !!}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex gap-3 mt-1">
                                        <i class="mdi mdi-auto-fix text-purple-50 text-base"></i>
                                        <p class="text-[11px] text-purple-600/80 italic">Beberapa isian telah terisi otomatis dari data formulir global pemohon. Silakan ubah jika terdapat data yang belum sesuai.</p>
                                    </div>
                                </div>
                            @endif
                            <form action="{{ route('data-perijinan.rekom-data.save', $application->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <input type="hidden" name="elapsed_seconds" class="elapsed-seconds-input" value="0">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($rekomFields as $field)
                                        <div class="space-y-2 {{ $field->type === 'table' ? 'md:col-span-2' : '' }}">
                                            <label class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-tighter">{{ $field->label }} @if($field->is_required)<span class="text-red-500">*</span>@endif</label>
                                            @php 
                                                                                                $val = $application->rekom_data[$field->name] ?? null; 
                                                $isAlreadySaved = array_key_exists($field->name, $application->rekom_data ?? []);

                                                if (!$isAlreadySaved) {
                                                    if ($application->perijinan->has_bo_form) {
                                                        $matchingBoField = $application->perijinan->activeFormFields
                                                            ->where('form_type', 'bo')
                                                            ->where('name', $field->name)
                                                            ->first() ?? $application->perijinan->activeFormFields
                                                            ->where('form_type', 'bo')
                                                            ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                                            ->first();

                                                        if ($matchingBoField) {
                                                            $val = $application->bo_data[$matchingBoField->name] ?? null;
                                                        }
                                                    } else {
                                                        $matchingGlobalField = $application->perijinan->activeFormFields
                                                            ->where('form_type', 'global')
                                                            ->where('name', $field->name)
                                                            ->first() ?? $application->perijinan->activeFormFields
                                                            ->where('form_type', 'global')
                                                            ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                                            ->first();

                                                        if ($matchingGlobalField) {
                                                            if ($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar') {
                                                                $globalFiles = $application->form_files[$matchingGlobalField->id] ?? [];
                                                                $val = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                                                            } else {
                                                                $val = $application->form_data[$matchingGlobalField->id] ?? '';
                                                                if (is_array($val)) $val = implode(', ', $val);
                                                            }
                                                        }
                                                    }
                                                }

                                                $val = $val ?? '';
                                                $ro = !$canEditRekom ? 'readonly disabled' : '';
                                                $cls = "w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-purple-500 outline-none transition-all";
                                                
                                                // Dynamic variable override
                                                $isDynamic = !empty($field->dynamic_variable);
                                                if ($isDynamic) {
                                                    $val = resolveDynamicVariable($application, $field->dynamic_variable);
                                                    $ro = 'readonly disabled';
                                                    $cls .= ' bg-amber-50/50 dark:bg-amber-900/10 border-amber-200 dark:border-amber-700';
                                                }
                                            @endphp

                                            @if($field->type === 'textarea')
                                                <textarea name="{{ $field->name }}" class="{{ $cls }} min-h-[120px]" {{ $ro }}>{{ $val }}</textarea>
                                            @elseif($field->type === 'select')
                                                <select name="{{ $field->name }}" class="{{ $cls }}" {{ $ro }}>
                                                    <option value="">-- Pilih --</option>
                                                    @php
                                                        $options = $field->options ?? [];
                                                        if ($val !== '' && $val !== null) {
                                                            $existsCaseInsensitive = false;
                                                            foreach ($options as $opt) {
                                                                if (strtolower($opt) === strtolower($val)) {
                                                                    $existsCaseInsensitive = true;
                                                                    break;
                                                                }
                                                            }
                                                            if (!$existsCaseInsensitive) {
                                                                $options[] = $val;
                                                            }
                                                        }
                                                    @endphp
                                                    @foreach($options as $opt)
                                                        <option value="{{ $opt }}" {{ strtolower($val) == strtolower($opt) ? 'selected' : '' }}>{{ $opt }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar')
                                                <div class="space-y-2">
                                                    <input type="file" name="{{ $field->name }}" class="{{ $cls }}" {{ $ro }} accept="{{ ($field->type === 'pas_foto' || $field->type === 'gambar') ? '.jpg,.jpeg,.png' : '*' }}">
                                                    @if($val)
                                                        @if($field->type === 'pas_foto')
                                                            <div class="mb-2">
                                                                <img src="{{ asset($val) }}" style="width: 2.79cm; height: 3.81cm; object-fit: cover;" class="rounded border shadow-sm" alt="Pas Foto" />
                                                            </div>
                                                        @elseif($field->type === 'gambar')
                                                            <div class="mb-2">
                                                                <img src="{{ asset($val) }}" style="max-width: 300px; max-height: 200px; object-fit: contain;" class="rounded border shadow-sm" alt="Gambar" />
                                                            </div>
                                                        @endif
                                                        <div class="flex items-center gap-2 mt-2 p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg border border-purple-100 dark:border-purple-800">
                                                            <i class="mdi mdi-file-check text-purple-600"></i>
                                                            <a href="{{ asset($val) }}" target="_blank" class="text-xs font-bold text-purple-700 dark:text-purple-300 hover:underline truncate">Lihat File Terupload</a>
                                                        </div>
                                                    @endif

                                                    @php
                                                        $matchingGlobalField = $application->perijinan->activeFormFields
                                                            ->where('form_type', 'global')
                                                            ->where('name', $field->name)
                                                            ->first() ?? $application->perijinan->activeFormFields
                                                            ->where('form_type', 'global')
                                                            ->filter(function($f) use ($field) {
                                                                return strtolower($f->label) === strtolower($field->label);
                                                            })->first();
                                                        
                                                        $globalFile = null;
                                                        if ($matchingGlobalField) {
                                                            $globalFiles = $application->form_files[$matchingGlobalField->id] ?? [];
                                                            $globalFile = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                                                        }
                                                    @endphp
                                                    @if($globalFile && $globalFile !== $val)
                                                        <div class="flex items-center gap-2 mt-1 p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-100 dark:border-amber-800/50">
                                                            <i class="mdi mdi-information-outline text-amber-600 text-sm"></i>
                                                            <span class="text-[10px] text-amber-700 dark:text-amber-400 font-bold">Referensi Pemohon:</span>
                                                            <a href="{{ asset($globalFile) }}" target="_blank" class="text-[10px] font-bold text-blue-600 hover:underline truncate">Buka Berkas Pemohon</a>
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($field->type === 'date')
                                                <input type="date" name="{{ $field->name }}" value="{{ $val }}" class="{{ $cls }}" {{ $ro }}>
                                            @elseif($field->type === 'number')
                                                <input type="number" name="{{ $field->name }}" value="{{ $val }}" class="{{ $cls }}" {{ $ro }}>
                                            @elseif($field->type === 'table')
                                                @include('components.form-field.table-input', ['field' => $field, 'val' => $val, 'ro' => $ro])
                                            @else
                                                <input type="text" name="{{ $field->name }}" value="{{ $val }}" class="{{ $cls }}" {{ $ro }}>
                                            @endif
                                            @if($field->help_text)
                                                <p class="text-[10px] text-gray-400 italic mt-1">{{ $field->help_text }}</p>
                                            @endif
                                            @error($field->name)
                                                <p class="text-xs text-red-500 font-bold mt-1"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Masa Aktif Rekomendasi Field -->
                                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <div class="max-w-xs">
                                        <label class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-tighter mb-2">Masa Aktif Surat Rekomendasi <span class="text-red-500">*</span></label>
                                        <input type="date" name="masa_aktif_rekom" value="{{ $application->rekom_data['masa_aktif_rekom'] ?? '' }}" required
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-purple-500 outline-none transition-all"
                                            {{ !$canEditRekom ? 'readonly disabled' : '' }}>
                                        @error('masa_aktif_rekom')
                                            <p class="text-xs text-red-500 font-bold mt-1"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                                        @enderror
                                        <p class="text-[10px] text-gray-400 mt-2 italic">Atur batas waktu berlakunya surat rekomendasi.</p>
                                    </div>
                                </div>
                                
                                @if($canEditRekom)
                                    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                                        <button type="submit" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg flex items-center gap-3 active:scale-95"><i class="mdi mdi-content-save-check text-lg"></i> SIMPAN & GENERATE REKOMENDASI</button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            <!-- TAB 3: DOKUMEN IZIN / SK -->
            @if($showIzinTab)
            <div id="tab-panel-dokumen-izin" class="tab-content-panel hidden space-y-6">
                @if($isKadin)
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-r-xl flex gap-3 shadow-sm">
                        <i class="mdi mdi-information-variant text-blue-600 text-xl mt-0.5"></i>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300">Informasi Verifikasi</h4>
                            @if($application->is_pembetulan)
                                <p class="text-xs text-blue-700 dark:text-blue-400 leading-relaxed mt-1">Ini adalah pengajuan <strong>pembetulan izin</strong>. Berikan TTE pada <strong>file PDF yang diunggah oleh BO</strong> (bukan generate ulang dari template).</p>
                            @else
                                <p class="text-xs text-blue-700 dark:text-blue-400 leading-relaxed mt-1">Berikut adalah <strong>Draft Surat Izin / SK</strong> dari Verifikator. Silahkan berikan TTE pada Draft agar menjadi Surat Izin / SK Resmi.</p>
                            @endif
                        </div>
                    </div>
                @endif


                @if($application->is_pembetulan && $application->file_izin_pembetulan && file_exists(public_path($application->file_izin_pembetulan)))
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-blue-200 dark:border-blue-900/30 overflow-hidden mb-2">
                    <div class="px-5 py-4 border-b border-blue-100 dark:border-blue-900/50 bg-blue-50/50 dark:bg-blue-900/20 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center border border-blue-200 dark:border-blue-800"><i class="mdi mdi-file-sign text-blue-600 dark:text-blue-400 text-xl"></i></div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-800 dark:text-white">Surat Izin dari BO (Pembetulan)</h3>
                                <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">Diunggah BO — Siap TTE Kadin</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($isVerifikator && $application->validasiRecords->where('order', $application->current_step)->first()?->status === 'pending')
                                <form action="{{ route('data-perijinan.pembetulan.refresh', $application->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-wider shadow-sm flex items-center gap-1">
                                        <i class="mdi mdi-refresh"></i> Perbarui PDF
                                    </button>
                                </form>
                            @endif
                            <button onclick="openPdfPreview('{{ asset($application->file_izin_pembetulan) }}?t={{ time() }}', 'Draft Izin Pembetulan dari BO')" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-wider shadow-sm">Buka PDF</button>
                        </div>
                    </div>
                </div>
                @elseif($application->is_pembetulan)
                <div class="flex items-center gap-2 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800 mb-2">
                    <i class="mdi mdi-alert-circle-outline text-amber-600 text-lg"></i>
                    <p class="text-xs text-amber-700 dark:text-amber-400 font-bold">BO belum mengunggah file PDF surat izin untuk pembetulan ini.</p>
                </div>
                @endif

                 @if(!$application->is_pembetulan && $application->file_izin && !empty($application->izin_data))
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-200 dark:border-indigo-900/30 overflow-hidden">
                        <div class="px-5 py-4 border-b border-indigo-100 dark:border-indigo-900/50 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center border border-indigo-200 dark:border-indigo-800"><i class="mdi mdi-certificate text-indigo-600 dark:text-indigo-400 text-xl"></i></div>
                                <div><h3 class="text-sm font-bold text-gray-800 dark:text-white">Draft Surat Izin / SK</h3><p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Hasil Generate Otomatis</p></div>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="openPdfPreview('{{ asset($application->file_izin) }}?t={{ time() }}', 'Draft Dokumen Izin / SK')" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-wider shadow-sm">Buka PDF</button>
                            </div>
                        </div>
                    </div>
                 @endif

                    @if($application->file_izin_tte)
                    <!-- Signed Document Card (Surat Izin) -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-200 dark:border-indigo-900/30 overflow-hidden mb-6">
                        <div class="px-5 py-4 border-b border-indigo-100 dark:border-indigo-900/50 bg-indigo-50/50 dark:bg-indigo-800/20 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center border border-indigo-200 dark:border-indigo-800"><i class="mdi mdi-certificate text-indigo-600 dark:text-indigo-400 text-xl"></i></div>
                                <div><h3 class="text-sm font-bold text-gray-800 dark:text-white">Surat Izin / SK Resmi (TTE)</h3><p class="text-[10px] text-indigo-600 font-bold uppercase tracking-wider">Terbit</p></div>
                            </div>
                            <div class="flex gap-2">
                                @if($isKadin || $isAdmin || $isVerifikator)
                                    <button onclick="verifyEsignPdf('izin', null)" class="px-4 py-2 bg-green-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-wider shadow-sm" title="Verifikasi TTE"><i class="mdi mdi-shield-check"></i> Cek Dokumen TTE</button>
                                @endif
                                <button onclick="openPdfPreview('{{ asset($application->file_izin_tte) }}?t={{ time() }}', 'Surat Izin / SK Resmi')" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-wider shadow-sm">Buka PDF</button>
                            </div>
                        </div>
                    </div>
                    @endif
                @if(!$application->is_pembetulan)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center border border-indigo-200 dark:border-indigo-800"><i class="mdi mdi-file-certificate text-indigo-600 text-xl"></i></div>
                            <div><h3 class="text-base font-bold text-gray-800 dark:text-white">Formulir Izin / SK</h3><p class="text-[10px] text-indigo-600 font-bold uppercase tracking-wider">Tugas Verifikator</p></div>
                        </div>
                        @if(!$canEditIzin) <span class="px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold rounded-full uppercase border">Hanya Baca</span> @endif
                    </div>
                    <div class="p-6">
                        @if($isFutureValidator && !in_array($application->status, ['approved', 'diperbaiki', 'rejected', 'perbaikan']))
                            <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/20 border-l-4 border-rose-500 rounded-r-xl flex gap-3 shadow-sm">
                                <i class="mdi mdi-lock-clock text-rose-600 text-xl mt-0.5"></i>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-rose-800 dark:text-rose-300 uppercase tracking-tight">Belum Waktu Tindakan</h4>
                                    <p class="text-xs text-rose-700 dark:text-rose-400 leading-relaxed mt-1">Anda (sebagai <strong>{{ auth()->user()->role_label }}</strong>) belum dapat melakukan tindakan pada tab ini karena permohonan masih dalam <strong>tahapan {{ $cv->validationFlow->role_label ?? 'Lainnya' }}</strong>.</p>
                                </div>
                            </div>
                        @endif

                        @if($isVerifikator && $canEditIzin)
                            <div class="mb-6 p-4 bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 rounded-r-xl flex flex-col gap-1">
                                <div class="flex gap-3">
                                    <i class="mdi mdi-information-outline text-indigo-600 text-xl mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-xs text-indigo-700 dark:text-indigo-300 leading-relaxed">Lengkapi data izin final. Data ini akan otomatis digunakan dalam <strong>Surat Izin / Keputusan</strong>.</p>
                                        @if($application->perijinan->keterangan_izin)
                                            <div class="mt-2 p-2 bg-white/50 dark:bg-indigo-800/30 rounded-lg border border-indigo-100 dark:border-indigo-800">
                                                <p class="text-[11px] text-indigo-800 dark:text-indigo-200 font-medium italic">
                                                    <i class="mdi mdi-lightbulb-on-outline mr-1"></i> {!! nl2br(e($application->perijinan->keterangan_izin)) !!}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-3 mt-1">
                                    <i class="mdi mdi-auto-fix text-indigo-500 text-base"></i>
                                    <p class="text-[11px] text-indigo-600/80 italic">Beberapa isian telah terisi otomatis dari data formulir global pemohon. Silakan ubah jika terdapat data yang belum sesuai.</p>
                                </div>
                            </div>
                        @endif
                        <form action="{{ route('data-perijinan.izin-data.save', $application->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <input type="hidden" name="elapsed_seconds" class="elapsed-seconds-input" value="0">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($izinFields as $field)
                                    <div class="space-y-2 {{ $field->type === 'table' ? 'md:col-span-2' : '' }}">
                                        <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-tighter">{{ $field->label }} @if($field->is_required)<span class="text-red-500">*</span>@endif</label>
                                        @php 
                                            $val = $application->izin_data[$field->name] ?? null; 
                                            $isAlreadySaved = array_key_exists($field->name, $application->izin_data ?? []);
                                            
                                            if (!$isAlreadySaved) {
                                                if ($application->perijinan->validasi_tanpa_opd) {
                                                    if ($application->perijinan->has_bo_form) {
                                                        // Default from form khusus bo (bo_data)
                                                        $matchingBoField = $application->perijinan->activeFormFields
                                                            ->where('form_type', 'bo')
                                                            ->where('name', $field->name)
                                                            ->first() ?? $application->perijinan->activeFormFields
                                                            ->where('form_type', 'bo')
                                                            ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                                            ->first();
                                                        
                                                        if ($matchingBoField && !empty($application->bo_data[$matchingBoField->name])) {
                                                            $val = $application->bo_data[$matchingBoField->name];
                                                        }
                                                    }
                                                    
                                                    // Fallback/Default to global form
                                                    if (empty($val)) {
                                                        $matchingGlobalField = $application->perijinan->activeFormFields
                                                            ->where('form_type', 'global')
                                                            ->where('name', $field->name)
                                                            ->first() ?? $application->perijinan->activeFormFields
                                                            ->where('form_type', 'global')
                                                            ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                                            ->first();
                                                        if ($matchingGlobalField) {
                                                            $val = $application->form_data[$matchingGlobalField->id] ?? '';
                                                            if (is_array($val)) $val = implode(', ', $val);
                                                        }
                                                    }
                                                } else {
                                                    if ($application->perijinan->is_multi_opd) {
                                                        // Multi-OPD: Default from Global form
                                                        $matchingGlobalField = $application->perijinan->activeFormFields
                                                            ->where('form_type', 'global')
                                                            ->where('name', $field->name)
                                                            ->first() ?? $application->perijinan->activeFormFields
                                                            ->where('form_type', 'global')
                                                            ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                                            ->first();

                                                        if ($matchingGlobalField) {
                                                            $val = $application->form_data[$matchingGlobalField->id] ?? '';
                                                            if (is_array($val)) $val = implode(', ', $val);
                                                        }
                                                    } else {
                                                        // Single OPD: Default from Rekom data
                                                        $matchingRekomField = $application->perijinan->activeFormFields
                                                            ->where('form_type', 'rekom')
                                                            ->where('name', $field->name)
                                                            ->first() ?? $application->perijinan->activeFormFields
                                                            ->where('form_type', 'rekom')
                                                            ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                                            ->first();

                                                        if ($matchingRekomField && !empty($application->rekom_data[$matchingRekomField->name])) {
                                                            $val = $application->rekom_data[$matchingRekomField->name];
                                                        }

                                                        if (empty($val) && $field->type !== 'file') {
                                                            $matchingGlobalField = $application->perijinan->activeFormFields
                                                                ->where('form_type', 'global')
                                                                ->where('name', $field->name)
                                                                ->first() ?? $application->perijinan->activeFormFields
                                                                ->where('form_type', 'global')
                                                                ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                                                ->first();

                                                            if ($matchingGlobalField) {
                                                                $val = $application->form_data[$matchingGlobalField->id] ?? '';
                                                                if (is_array($val)) $val = implode(', ', $val);
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            $val = $val ?? '';
                                            $ro = !$canEditIzin ? 'readonly disabled' : '';
                                            $cls = "w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all";
                                            
                                            // Dynamic variable override
                                            $isDynamic = !empty($field->dynamic_variable);
                                            if ($isDynamic) {
                                                $val = resolveDynamicVariable($application, $field->dynamic_variable);
                                                $ro = 'readonly disabled';
                                                $cls .= ' bg-amber-50/50 dark:bg-amber-900/10 border-amber-200 dark:border-amber-700';
                                            }
                                        @endphp
                                        @if($field->type === 'textarea')
                                            <textarea name="{{ $field->name }}" class="{{ $cls }} min-h-[120px]" {{ $ro }}>{{ $val }}</textarea>
                                        @elseif($field->type === 'select')
                                            <select name="{{ $field->name }}" class="{{ $cls }}" {{ $ro }}>
                                                <option value="">-- Pilih --</option>
                                                @php
                                                    $options = $field->options ?? [];
                                                    if ($val !== '' && $val !== null) {
                                                        $existsCaseInsensitive = false;
                                                        foreach ($options as $opt) {
                                                            if (strtolower($opt) === strtolower($val)) {
                                                                $existsCaseInsensitive = true;
                                                                break;
                                                            }
                                                        }
                                                        if (!$existsCaseInsensitive) {
                                                            $options[] = $val;
                                                        }
                                                    }
                                                @endphp
                                                @foreach($options as $opt)
                                                    <option value="{{ $opt }}" {{ strtolower($val) == strtolower($opt) ? 'selected' : '' }}>{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar')
                                            <div class="space-y-2">
                                                <input type="file" name="{{ $field->name }}" class="{{ $cls }}" {{ $ro }} accept="{{ ($field->type === 'pas_foto' || $field->type === 'gambar') ? '.jpg,.jpeg,.png' : '*' }}">
                                                @if($val)
                                                    @if($field->type === 'pas_foto')
                                                        <div class="mb-2">
                                                            <img src="{{ asset($val) }}" style="width: 2.79cm; height: 3.81cm; object-fit: cover;" class="rounded border shadow-sm" alt="Pas Foto" />
                                                        </div>
                                                    @elseif($field->type === 'gambar')
                                                        <div class="mb-2">
                                                            <img src="{{ asset($val) }}" style="max-width: 300px; max-height: 200px; object-fit: contain;" class="rounded border shadow-sm" alt="Gambar" />
                                                        </div>
                                                    @endif
                                                    <div class="flex items-center gap-2 mt-2 p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg border border-indigo-100 dark:border-indigo-800">
                                                        <i class="mdi mdi-file-check text-indigo-600"></i>
                                                        <a href="{{ asset($val) }}" target="_blank" class="text-xs font-bold text-indigo-700 dark:text-indigo-300 hover:underline truncate">Lihat File Terupload</a>
                                                    </div>
                                                @endif

                                                @php
                                                    // File reference priority: BO (if validasi_tanpa_opd & has_bo_form) -> Rekom -> Global
                                                    $globalFile = null;
                                                    
                                                    if ($application->perijinan->validasi_tanpa_opd) {
                                                        if ($application->perijinan->has_bo_form) {
                                                            $matchingBoFileField = $application->perijinan->activeFormFields
                                                                ->where('form_type', 'bo')
                                                                ->where('name', $field->name)
                                                                ->first() ?? $application->perijinan->activeFormFields
                                                                ->where('form_type', 'bo')
                                                                ->filter(function($f) use ($field) {
                                                                    return strtolower($f->label) === strtolower($field->label);
                                                                })->first();
                                                            if ($matchingBoFileField) {
                                                                $globalFile = $application->bo_data[$matchingBoFileField->name] ?? null;
                                                            }
                                                        }
                                                    } else {
                                                        // 1. Try Rekom file
                                                        $matchingRekomFileField = $application->perijinan->activeFormFields
                                                            ->where('form_type', 'rekom')
                                                            ->where('name', $field->name)
                                                            ->first() ?? $application->perijinan->activeFormFields
                                                            ->where('form_type', 'rekom')
                                                            ->filter(function($f) use ($field) {
                                                                return strtolower($f->label) === strtolower($field->label);
                                                            })->first();
                                                        
                                                        if ($matchingRekomFileField) {
                                                            $globalFile = $application->rekom_data[$matchingRekomFileField->name] ?? null;
                                                        }
                                                    }

                                                    // 2. Fallback to Global file if no file found yet
                                                    if (!$globalFile) {
                                                        $matchingGlobalFileField = $application->perijinan->activeFormFields
                                                            ->where('form_type', 'global')
                                                            ->where('name', $field->name)
                                                            ->first() ?? $application->perijinan->activeFormFields
                                                            ->where('form_type', 'global')
                                                            ->filter(function($f) use ($field) {
                                                                return strtolower($f->label) === strtolower($field->label);
                                                            })->first();
                                                        
                                                        if ($matchingGlobalFileField) {
                                                            $globalFiles = $application->form_files[$matchingGlobalFileField->id] ?? [];
                                                            $globalFile = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                                                        }
                                                    }
                                                @endphp
                                                @if($globalFile && $globalFile !== $val)
                                                    <div class="flex items-center gap-2 mt-1 p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-100 dark:border-amber-800/50">
                                                        <i class="mdi mdi-information-outline text-amber-600 text-sm"></i>
                                                        <span class="text-[10px] text-amber-700 dark:text-amber-400 font-bold">Referensi:</span>
                                                        <a href="{{ asset($globalFile) }}" target="_blank" class="text-[10px] font-bold text-blue-600 hover:underline truncate">Buka Berkas Sebelumnya</a>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($field->type === 'date')
                                            <input type="date" name="{{ $field->name }}" value="{{ $val }}" class="{{ $cls }}" {{ $ro }}>
                                        @elseif($field->type === 'number')
                                            <input type="number" name="{{ $field->name }}" value="{{ $val }}" class="{{ $cls }}" {{ $ro }}>
                                        @elseif($field->type === 'table')
                                            @include('components.form-field.table-input', ['field' => $field, 'val' => $val, 'ro' => $ro])
                                        @else
                                            <input type="text" name="{{ $field->name }}" value="{{ $val }}" class="{{ $cls }}" {{ $ro }}>
                                        @endif
                                        @if($field->help_text)
                                            <p class="text-[10px] text-gray-400 italic mt-1">{{ $field->help_text }}</p>
                                        @endif
                                        @if(!empty($field->dynamic_variable))
                                            <div class="flex items-center gap-1.5 mt-1.5 px-2.5 py-1.5 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-100 dark:border-amber-800/50">
                                                <i class="mdi mdi-link-variant text-amber-500 text-sm"></i>
                                                <p class="text-[10px] text-amber-700 dark:text-amber-400 font-medium">
                                                    <i class="mdi mdi-lock-outline"></i> Input ini diisi otomatis dari variabel dinamis <code class="font-mono bg-amber-100 dark:bg-amber-900/30 px-1 py-0.5 rounded text-[9px]">{{ $field->dynamic_variable }}</code>. Nilai akan tampil pada surat.
                                                </p>
                                            </div>
                                        @endif
                                        @error($field->name)
                                            <p class="text-xs text-red-500 font-bold mt-1"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                                        @enderror
                                        </div>
                                @endforeach
                            </div>

                            <!-- Masa Aktif Field (Above Save Button) -->
                            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                               <div class="max-w-xs">
                                   <label class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-tighter mb-2">Masa Aktif Surat Izin <span class="text-red-500">*</span></label>
                                   <input type="date" name="masa_aktif" value="{{ $application->masa_aktif ? $application->masa_aktif->format('Y-m-d') : '' }}" required
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                                       {{ !$canEditIzin ? 'readonly disabled' : '' }}>
                                   @error('masa_aktif')
                                       <p class="text-xs text-red-500 font-bold mt-1"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                                   @enderror
                                   <p class="text-[10px] text-gray-400 mt-2 italic">Atur batas waktu berlakunya surat izin.</p>
                               </div>
                            </div>

                            @if($canEditIzin)
                                <div class="mt-4 flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-[10px] font-black uppercase active:scale-95 transition-all shadow-lg flex items-center gap-3"><i class="mdi mdi-content-save-check text-lg"></i> SIMPAN & GENERATE IZIN</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Right Side: Status & Timeline -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50"><h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2"><i class="mdi mdi-history text-blue-500"></i> Status & Riwayat</h3></div>
                <div class="p-5">
                    <div class="space-y-0">
                        @foreach ($application->validasiRecords as $index => $v)
                            @php 
                                $sc = ['approved' => 'bg-green-500', 'pending' => 'bg-gray-300 dark:bg-gray-600', 'rejected' => 'bg-red-500', 'revision' => 'bg-orange-500']; 
                                $isCurrent = ($v->order == $application->current_step && in_array($application->status, ['submitted', 'in_progress']));
                                
                                // Check if this is current user's task (including parallel multi-opd)
                                $isMyTurn = false;
                                if ($application->status === 'submitted' || $application->status === 'in_progress') {
                                    if ($isMultiOpd && in_array($userRole, ['operator_opd', 'kepala_opd'])) {
                                        // Highlight specific parallel task
                                        if ($v->validationFlow->assigned_user_id == auth()->id() && $v->status === 'pending') {
                                            $opdSteps = $application->validasiRecords->filter(fn($vr) => in_array($v->validationFlow->role, ['operator_opd', 'kepala_opd']));
                                            $minOpdOrder = $opdSteps->min('order');
                                            if ($application->current_step >= $minOpdOrder) {
                                                if ($userRole === 'kepala_opd') {
                                                    $myOp = $application->validasiRecords->first(fn($vr) => $vr->validationFlow->role === 'operator_opd' && $vr->validationFlow->assignedUser->opd_id == auth()->user()->opd_id);
                                                    $isMyTurn = $myOp && $myOp->status === 'approved';
                                                } else {
                                                    $isMyTurn = true;
                                                }
                                            }
                                        }
                                    } else {
                                        // Standard sequential highlight
                                        if ($isCurrent) {
                                            $isMyTurn = ($v->validationFlow->role === $userRole && 
                                                       (in_array($v->validationFlow->role, ['verifikator', 'kadin']) || 
                                                        $v->user_id === auth()->id() || 
                                                        $v->validationFlow->assigned_user_id === auth()->id()));
                                        }
                                    }
                                }
                            @endphp
                            <div class="relative flex gap-3 {{ !$loop->last ? 'pb-4' : '' }} {{ $isCurrent ? 'bg-blue-50/50 dark:bg-blue-900/10 -mx-5 px-5 py-3 first:rounded-t-none last:rounded-b-none border-y border-blue-100/50 dark:border-blue-800/30' : '' }}">
                                <div class="relative flex-shrink-0 mt-1.5">
                                    <div class="w-2.5 h-2.5 rounded-full {{ $sc[$v->status] ?? 'bg-gray-300' }} z-10"></div>
                                    @if($isCurrent)
                                        <div class="absolute -inset-1 bg-blue-500 rounded-full animate-ping opacity-30"></div>
                                        <div class="absolute inset-0 bg-blue-400 rounded-full animate-pulse opacity-40"></div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-0.5">
                                        <h4 class="font-bold text-gray-800 dark:text-white text-[11px] uppercase tracking-tighter flex items-center gap-1.5 flex-wrap">
                                            {{ $v->validationFlow->role_label ?? 'Tahap ' . ($index + 1) }}
                                            @php
                                                $assignedOpd = $v->validationFlow->assignedUser->opd ?? null;
                                                $actualOpd = $v->validator->opd ?? null;
                                                $opdToDisplay = $actualOpd ?? $assignedOpd;
                                            @endphp
                                            @if($opdToDisplay)
                                                <span class="text-purple-600 dark:text-purple-400 font-bold">({{ $opdToDisplay->nama_opd }})</span>
                                            @endif
                                            @if($isCurrent)
                                                <span class="flex h-1.5 w-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></span>
                                            @endif
                                        </h4>
                                        <div class="flex items-center gap-2">
                                            @if($isMyTurn)
                                                <span class="px-2 py-0.5 rounded-full text-[8px] font-black bg-blue-600 text-white uppercase tracking-wider animate-bounce">Anda</span>
                                            @endif
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $v->status_color }} uppercase">{{ $v->status_label }}</span>
                                        </div>
                                    </div>
                                    @php
                                        $displayUser = $v->validator ?? ($v->validationFlow->assignedUser ?? null);
                                    @endphp
                                    @if($displayUser)
                                        <p class="text-[10px] text-gray-600 dark:text-gray-400 font-medium mb-1">
                                            <i class="mdi mdi-account-outline text-[11px]"></i> {{ $displayUser->name }}
                                        </p>
                                    @endif

                                    @if ($v->validated_at) 
                                        <p class="text-[9px] text-gray-500">{{ $v->validated_at->format('d/m/Y H:i') }}</p> 
                                    @else
                                        @if($isCurrent)
                                            <p class="text-[9px] text-blue-600 dark:text-blue-400 font-bold uppercase tracking-widest animate-pulse">Sedang Berjalan</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50"><h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2"><i class="mdi mdi-account-card-outline text-blue-500"></i> Profil Pemohon</h3></div>
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center border border-blue-200 dark:border-blue-800"><i class="mdi mdi-account text-blue-600 dark:text-blue-400 text-xl"></i></div>
                        <div class="min-w-0 flex-1"><h3 class="font-bold text-gray-800 dark:text-white truncate text-xs">{{ $application->user->name }}</h3><p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">{{ $application->user->status_pemohon_label }}</p></div>
                    </div>
                    <button type="button" onclick="openIdentityModal()" class="w-full py-2 bg-blue-50 text-blue-700 rounded-xl text-[10px] font-black uppercase transition-all hover:bg-blue-100 flex items-center justify-center gap-2 border border-blue-100"><i class="mdi mdi-account-details-outline text-sm"></i> Detail Profil</button>
                    @if($application->catatan_pemohon)<div class="mt-3 p-3 bg-amber-50 rounded-xl border border-amber-100"><p class="text-[9px] text-amber-700 font-black uppercase">Catatan:</p><p class="text-[10px] text-amber-800 italic">"{{ $application->catatan_pemohon }}"</p></div>@endif
                </div>
            </div>

            @if ($application->status === 'perbaikan')
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-5 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-800/40 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-account-edit text-amber-600 dark:text-amber-400 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-amber-800 dark:text-amber-300 uppercase tracking-tight mb-1">Sedang Dalam Perbaikan</h4>
                            <p class="text-xs text-amber-700 dark:text-amber-400 leading-relaxed">Permohonan ini telah dikembalikan ke pemohon untuk perbaikan berkas/data. Tindakan validasi akan tersedia kembali setelah pemohon mengirimkan ulang perbaikan.</p>
                        </div>
                    </div>
                </div>
            @elseif ($isFutureValidator && $application->status !== 'approved' && $application->status !== 'rejected')
                <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl p-5 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-800/40 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-lock-clock text-rose-600 dark:text-rose-400 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-rose-800 dark:text-rose-300 uppercase tracking-tight mb-1">Belum Waktu Validasi</h4>
                            <p class="text-xs text-rose-700 dark:text-rose-400 leading-relaxed">Anda belum dapat melakukan tindakan validasi karena saat ini permohonan masih dalam tahapan <strong>{{ $cv->validationFlow->role_label ?? 'Lainnya' }}</strong>. Silakan tunggu hingga berkas sampai pada tahapan Anda.</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($canVal)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50"><h3 class="font-black text-gray-800 dark:text-white text-xs uppercase tracking-widest flex items-center gap-2"><i class="mdi mdi-shield-check text-blue-500"></i> Tindakan Validasi</h3></div>
                    <div class="p-5">
                        <form id="validationForm" action="{{ route('data-perijinan.validate', $application->id) }}" method="POST">
                            @csrf <input type="hidden" name="action" id="validationAction" value="">
                            <input type="hidden" name="elapsed_seconds" class="elapsed-seconds-input" value="0">
                            <input type="hidden" name="passphrase" id="passphrase_input" value="">
                            <textarea name="catatan" id="catatan" rows="3" class="w-full px-3 py-2 text-xs border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-xl mb-1 focus:ring-2 focus:ring-blue-500 outline-none resize-none" placeholder="Berikan catatan..."></textarea>
                            @error('catatan')
                                <p class="text-xs text-red-500 font-bold mb-3"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                            @enderror
                            
                            @if($isVerifikator && $application->perijinan->is_multi_opd)
                                <select name="target_opd_id" id="target_opd_id" class="hidden">
                                    <option value="">-- Pilih Kepala OPD --</option>
                                    @php
                                        $involvedOpds = $application->validasiRecords
                                            ->filter(fn($v) => $v->validationFlow && $v->validationFlow->role === 'kepala_opd')
                                            ->map(fn($v) => $v->validationFlow->assignedUser->opd ?? null)
                                            ->filter()
                                            ->unique('id');
                                    @endphp
                                    @foreach($involvedOpds as $opd)
                                        <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                                    @endforeach
                                </select>
                            @endif

                            <div class="space-y-4">
                                @if($isKadin || $isKepalaOpd)
                                    @php
                                        $isSigned = false;
                                        if($isKepalaOpd) {
                                            $isSigned = $application->perijinan->is_multi_opd ? !empty($application->file_rekom_multi_tte[auth()->user()->opd_id ?? 0]) : !empty($application->file_rekom_tte);
                                        } else if($isKadin) {
                                            $isSigned = !empty($application->file_izin_tte);
                                        }
                                    @endphp

                                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Langkah 1: Digital Signature</p>
                                        @if($isSigned)
                                            <div class="flex items-center gap-3 p-3 bg-green-100/50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                                                <i class="mdi mdi-check-decagram text-green-600 text-xl"></i>
                                                <div>
                                                    <p class="text-xs font-bold text-green-700 dark:text-green-400">Dokumen Telah di-TTE</p>
                                                    <p class="text-[9px] text-green-600/70 uppercase font-black">Siap untuk disetujui</p>
                                                </div>
                                            </div>
                                        @else
                                            <button type="button" onclick="openEsignModal('{{ $isKepalaOpd ? 'rekom' : 'izin' }}')" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl text-[10px] font-black uppercase flex justify-center items-center gap-2 shadow-md transition-all active:scale-95">
                                                <i class="mdi mdi-pen text-lg"></i> Berikan TTE Dokumen
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Langkah 2: Keputusan Akhir</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <button type="button" onclick="submitValidation('approved')" 
                                                class="py-2.5 bg-blue-600 text-white rounded-xl text-[9px] font-black uppercase disabled:opacity-50 disabled:cursor-not-allowed"
                                                {{ !$isSigned ? 'disabled' : '' }}>
                                                {{ $isKadin ? 'Setujui & Terbitkan Izin' : 'Setujui' }}
                                            </button>
                                            <button type="button" onclick="submitValidation('rejected')" class="py-2.5 bg-red-600 text-white rounded-xl text-[9px] font-black uppercase">Tolak</button>
                                        </div>
                                    </div>
                                @else
                                    <div class="grid grid-cols-2 gap-2">
                                        @php
                                            $isDraftGenerated = true;
                                            if ($isOperatorOpd) {
                                                if ($application->perijinan->is_multi_opd) {
                                                    // Gunakan opd_id dari user yang sedang login
                                                    $opdId = auth()->user()->opd_id;
                                                    $isDraftGenerated = !empty($application->rekom_data_multi[$opdId]) && !empty($application->file_rekom_multi[$opdId]);
                                                } else {
                                                    $isDraftGenerated = !empty($application->rekom_data) && !empty($application->file_rekom);
                                                }
                                            } else if ($isVerifikator) {
                                                if ($application->is_pembetulan) {
                                                    $isDraftGenerated = !empty($application->file_izin_pembetulan) && file_exists(public_path($application->file_izin_pembetulan));
                                                } else {
                                                    $isDraftGenerated = !empty($application->izin_data) && !empty($application->file_izin);
                                                }
                                            } else if ($isBo && $application->perijinan->has_bo_form && $boFields->count() > 0) {
                                                $isDraftGenerated = !empty($application->bo_data);
                                            }

                                            if ($isBo && $application->is_pembetulan) {
                                                $hasPembetulanPdf = !empty($application->file_izin_pembetulan) && file_exists(public_path($application->file_izin_pembetulan));
                                                if (!$hasPembetulanPdf) {
                                                    $isDraftGenerated = false;
                                                }
                                            }

                                            $disabledTitle = '';
                                            if (!$isDraftGenerated) {
                                                if ($isBo && $application->is_pembetulan && empty($application->file_izin_pembetulan)) {
                                                    $disabledTitle = 'Harap unggah file PDF surat izin pada bagian "Unggah Surat Izin (Siap TTE)" terlebih dahulu';
                                                } elseif ($isBo) {
                                                    $disabledTitle = 'Harap lengkapi dan simpan Formulir Khusus BO terlebih dahulu';
                                                } elseif ($isOperatorOpd) {
                                                    $disabledTitle = 'Harap lengkapi formulir dokumen rekomendasi lalu Simpan & Generate Draft terlebih dahulu';
                                                } elseif ($isVerifikator && $application->is_pembetulan) {
                                                    $disabledTitle = 'Menunggu BO mengunggah berkas PDF surat izin yang siap ditandatangani (TTE)';
                                                } else {
                                                    $disabledTitle = 'Harap lengkapi formulir dokumen izin lalu Simpan & Generate Draft terlebih dahulu';
                                                }
                                            }
                                        @endphp
                                        <button type="button" onclick="submitValidation('approved')" 
                                            class="py-2.5 {{ $isDraftGenerated ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed opacity-50' }} text-white rounded-xl text-[9px] font-black uppercase transition-colors"
                                            {{ !$isDraftGenerated ? 'disabled title="'.$disabledTitle.'"' : '' }}>
                                            Setujui
                                        </button>
                                        <button type="button" onclick="submitValidation('rejected')" class="py-2.5 bg-red-600 hover:bg-red-700 transition-colors text-white rounded-xl text-[9px] font-black uppercase">Tolak</button>
                                    </div>
                                @endif

                                @if(!$isVerifikator && !$isKadin)
                                    <button type="button" onclick="submitValidation('revision')" class="w-full py-2.5 bg-orange-500 text-white rounded-xl text-[9px] font-black uppercase">Perbaikan ke Pemohon</button>
                                @endif
                                
                                <div class="grid grid-cols-1 gap-2 mt-1">
                                    @if($isKadin)
                                        <button type="button" onclick="submitValidation('return_to_verifikator')" class="py-2.5 bg-blue-600 text-white rounded-xl text-[9px] font-black uppercase">Kembalikan ke Verifikator</button>
                                    @endif

                                    @if($isOperatorOpd)
                                        <button type="button" onclick="submitValidation('return_to_bo')" class="py-2.5 bg-blue-600 text-white rounded-xl text-[9px] font-black uppercase">Kembalikan ke BO</button>
                                    @endif

                                    @if($isKepalaOpd)
                                        <button type="button" onclick="submitValidation('return_to_operator_opd')" class="py-2.5 bg-blue-600 text-white rounded-xl text-[9px] font-black uppercase">Kembalikan ke Operator OPD</button>
                                    @endif
                                    
                                    @if($isVerifikator)
                                        @if($application->perijinan->validasi_tanpa_opd)
                                            <button type="button" onclick="submitValidation('return_to_bo')" class="py-2.5 bg-blue-600 text-white rounded-xl text-[9px] font-black uppercase">Kembalikan ke BO</button>
                                        @else
                                            <button type="button" onclick="handleReturnKepalaOpd()" class="py-2.5 bg-blue-600 text-white rounded-xl text-[9px] font-black uppercase">Kembalikan ke Kepala OPD</button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
            @endif

            {{-- Renewal History Timeline Card --}}
            @if(isset($renewalHistory) && $renewalHistory->count() > 1)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="mdi mdi-link-variant text-blue-500"></i> Histori Perpanjangan Izin
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="relative border-l border-gray-200 dark:border-gray-700 ml-3 pl-6 space-y-5">
                            @foreach($renewalHistory as $hApp)
                                @php
                                    $isSelf = ($hApp->id == $application->id);
                                @endphp
                                <div class="relative">
                                    {{-- Bullet dot --}}
                                    <div class="absolute -left-[31px] top-1.5 w-3.5 h-3.5 rounded-full border-2 {{ $isSelf ? 'bg-blue-600 border-white dark:border-gray-900 shadow-sm scale-110' : 'bg-gray-200 border-white dark:border-gray-900' }}"></div>
                                    
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('data-perijinan.show', $hApp->id) }}" 
                                               class="font-mono text-xs font-bold {{ $isSelf ? 'text-blue-600 dark:text-blue-400 font-extrabold' : 'text-gray-700 dark:text-gray-300 hover:text-blue-500' }}">
                                                {{ $hApp->no_registrasi }}
                                            </a>
                                            @if($isSelf)
                                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-tighter bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                    Dokumen Ini
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex flex-col gap-0.5 mt-0.5">
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400">
                                                Tanggal: {{ $hApp->created_at->format('d/m/Y') }}
                                            </p>
                                            @if($hApp->masa_aktif)
                                                <p class="text-[10px] text-gray-500 dark:text-gray-400">
                                                    Masa Berlaku s/d: {{ $hApp->masa_aktif->format('d/m/Y') }}
                                                </p>
                                            @endif
                                            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-tighter mt-0.5">
                                                Status: <span class="{{ $hApp->status_color }} px-1.5 py-0.5 rounded-full text-[8px] font-bold">{{ $hApp->status_label }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modals -->
    <div id="imagePreviewModal" class="fixed inset-0 bg-black/80 z-[500] hidden items-center justify-center p-4" onclick="this.classList.add('hidden')">
        <div class="max-w-4xl w-full text-center"><img id="previewImageElement" src="" class="mx-auto max-h-[85vh] rounded-xl shadow-2xl"><p id="previewImageName" class="text-white mt-4 font-bold"></p></div>
    </div>
    <div id="modal-pdf-preview" class="fixed inset-0 z-[300] hidden items-center justify-center bg-black/70 backdrop-blur-sm"><div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl flex flex-col overflow-hidden" style="width: min(960px, 95vw); height: 90vh;"><div class="flex items-center justify-between px-5 py-3 border-b bg-gray-50 dark:bg-gray-800 flex-shrink-0"><div class="flex items-center gap-3"><div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center"><i class="mdi mdi-file-pdf-box text-red-600 text-lg"></i></div><div><h3 id="pdf-modal-title" class="text-sm font-bold">Pratinjau</h3></div></div><button onclick="closePdfPreview()" class="text-gray-400 hover:text-gray-600"><i class="mdi mdi-close text-lg"></i></button></div><div class="flex-1 bg-gray-200 overflow-hidden"><iframe id="pdf-modal-iframe" src="" class="w-full h-full border-0"></iframe></div></div></div>

    <!-- Modal: Data Formulir Global -->
    <div id="modal-form-data" class="fixed inset-0 z-[450] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <!-- existing modal structure -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center border border-amber-200 dark:border-amber-800">
                        <i class="mdi mdi-form-select text-amber-600 dark:text-amber-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white">Data Isian Formulir</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">Formulir Global oleh Pemohon</p>
                    </div>
                </div>
                <button type="button" onclick="closeFormDataModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <i class="mdi mdi-close text-2xl"></i>
                </button>
            </div>
            
            <div class="p-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @php
                        $globalFields = $application->perijinan->activeFormFields->where('form_type', 'global')->sortBy('order');
                    @endphp
                    @forelse($globalFields as $field)
                        <div class="space-y-1">
                            <label class="text-[10px] text-gray-400 uppercase font-black tracking-widest">{{ $field->label }}</label>
                            <div class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700">
                                @if($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar')
                                    @php 
                                        $files = $application->form_files[$field->id] ?? [];
                                        $filesArray = is_array($files) ? $files : [$files];
                                        $filesArray = array_filter($filesArray);
                                    @endphp
                                    @if(count($filesArray) > 0)
                                        <div class="flex flex-col gap-2">
                                            @foreach($filesArray as $file)
                                                @if($field->type === 'pas_foto')
                                                    <div class="mb-2">
                                                        <img src="{{ asset($file) }}" style="width: 2.79cm; height: 3.81cm; object-fit: cover;" class="rounded border shadow-sm" alt="Pas Foto" />
                                                    </div>
                                                @elseif($field->type === 'gambar')
                                                    <div class="mb-2">
                                                        <img src="{{ asset($file) }}" style="max-width: 300px; max-height: 200px; object-fit: contain;" class="rounded border shadow-sm" alt="Gambar" />
                                                    </div>
                                                @endif
                                                <a href="{{ asset($file) }}" target="_blank" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-bold text-xs truncate">
                                                    <i class="mdi mdi-file-download-outline text-base"></i>
                                                    Buka Berkas
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-400 italic">Tidak ada file diunggah</p>
                                    @endif
                                @elseif($field->type === 'table')
                                    @php
                                        $val = $application->form_data[$field->id] ?? null;
                                    @endphp
                                    @include('components.form-field.table-input', [
                                        'field' => $field,
                                        'val' => $val,
                                        'ro' => 'readonly disabled',
                                        'inputNamePrefix' => "form_fields[{$field->id}]"
                                    ])
                                @else
                                    <p class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                        @php 
                                            $val = $application->form_data[$field->id] ?? '-';
                                            if (is_array($val)) $val = implode(', ', $val);
                                        @endphp
                                        {{ $val ?: '-' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center">
                            <i class="mdi mdi-file-document-outline text-4xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500 italic">Tidak ada data formulir global.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                <button type="button" onclick="closeFormDataModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-xl text-sm font-bold transition-all shadow-sm">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal: E-sign TTE -->
    <div id="modal-esign" class="fixed inset-0 z-[600] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50 dark:bg-gray-800/50">
                <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="mdi mdi-shield-key-outline text-green-600"></i> Autentikasi TTE
                </h3>
                <button onclick="closeEsignModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"><i class="mdi mdi-close text-xl"></i></button>
            </div>
            <div class="p-6">
                <input type="hidden" id="esign_doc_type" value="">
                <div class="mb-4">
                    <p class="text-xs text-gray-600 dark:text-gray-400">Silakan masukkan passphrase sertifikat elektronik Anda untuk melakukan Tanda Tangan Elektronik (TTE) dokumen ini.</p>
                </div>
                <div class="relative">
                    <input type="text" id="esign_passphrase" autocomplete="off" style="-webkit-text-security: disc;" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-green-500 outline-none transition-all pr-10 text-gray-800 dark:text-white" placeholder="Masukkan Passphrase...">
                    <button type="button" onclick="togglePassphrase()" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                        <i id="icon-toggle-pass" class="mdi mdi-eye"></i>
                    </button>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3 bg-gray-50 dark:bg-gray-800/50">
                <button type="button" onclick="closeEsignModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-xl text-xs font-bold uppercase hover:bg-gray-300 transition-all">Batal</button>
                <button type="button" onclick="submitEsignOnly()" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-bold uppercase transition-all shadow-md flex items-center gap-2">
                    <i class="mdi mdi-check"></i> Proses TTE
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Pilih Kepala OPD Tujuan Pengembalian -->
    <div id="modal-return-kepala-opd" class="fixed inset-0 z-[450] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center border border-blue-200 dark:border-blue-800">
                        <i class="mdi mdi-office-building text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white">Pilih Kepala OPD</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">Tujuan Pengembalian Validasi</p>
                    </div>
                </div>
                <button type="button" onclick="closeReturnKepalaOpdModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <i class="mdi mdi-close text-2xl"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-widest">Kepala OPD</label>
                    <select id="modal_target_opd_id" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all text-gray-800 dark:text-white">
                        <option value="">-- Pilih Kepala OPD --</option>
                        @php
                            $involvedOpds = $application->validasiRecords
                                ->filter(fn($v) => $v->validationFlow && $v->validationFlow->role === 'kepala_opd')
                                ->map(fn($v) => $v->validationFlow->assignedUser->opd ?? null)
                                ->filter()
                                ->unique('id');
                        @endphp
                        @foreach($involvedOpds as $opd)
                            <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                        @endforeach
                    </select>
                </div>
                <p class="text-[11px] text-gray-500 leading-relaxed italic">
                    <i class="mdi mdi-information-outline mr-0.5"></i>
                    Pengembalian validasi akan mereset persetujuan Kepala OPD yang dipilih, serta menghapus draft TTE rekomendasi dari OPD tersebut.
                </p>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3">
                <button type="button" onclick="closeReturnKepalaOpdModal()" class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-xl text-xs font-bold transition-all">Batal</button>
                <button type="button" onclick="confirmReturnKepalaOpd()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all">Lanjutkan</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openEsignModal(docType) {
            document.getElementById('esign_doc_type').value = docType;
            const m = document.getElementById('modal-esign');
            m.classList.remove('hidden'); m.classList.add('flex'); document.body.style.overflow = 'hidden';
            
            const input = document.getElementById('esign_passphrase');
            input.value = '';
            input.style.webkitTextSecurity = 'disc';
            
            const icon = document.getElementById('icon-toggle-pass');
            icon.classList.remove('mdi-eye-off');
            icon.classList.add('mdi-eye');
            
            input.focus();
        }
        function closeEsignModal() {
            const m = document.getElementById('modal-esign');
            m.classList.remove('flex'); m.classList.add('hidden'); document.body.style.overflow = 'auto';
        }
        function togglePassphrase() {
            const input = document.getElementById('esign_passphrase');
            const icon = document.getElementById('icon-toggle-pass');
            if (input.style.webkitTextSecurity === 'none') {
                input.style.webkitTextSecurity = 'disc';
                icon.classList.remove('mdi-eye-off');
                icon.classList.add('mdi-eye');
            } else {
                input.style.webkitTextSecurity = 'none';
                icon.classList.remove('mdi-eye');
                icon.classList.add('mdi-eye-off');
            }
        }
        
        function submitEsignOnly() {
            const pass = document.getElementById('esign_passphrase').value;
            const docType = document.getElementById('esign_doc_type').value;
            if (!pass) {
                Swal.fire({ title: 'Perhatian', text: 'Passphrase TTE tidak boleh kosong!', icon: 'warning' });
                return;
            }
            
            Swal.fire({
                title: 'Memproses TTE...',
                text: 'Harap tunggu, sedang menghubungi server E-Sign...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch('{{ route("data-perijinan.apply-tte", $application->id) }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ 
                    passphrase: pass, 
                    doc_type: docType, 
                    elapsed_seconds: document.querySelector('.elapsed-seconds-input') ? document.querySelector('.elapsed-seconds-input').value : 0,
                    _token: '{{ csrf_token() }}' 
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({ title: 'Berhasil', text: res.message, icon: 'success' }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal TTE', res.message || 'Terjadi kesalahan.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            });
        }

        function submitEsignValidation() {
            // Deprecated - replaced by submitEsignOnly
        }

        function openFormDataModal() {
            const m = document.getElementById('modal-form-data');
            m.classList.remove('hidden'); m.classList.add('flex'); document.body.style.overflow = 'hidden';
        }
        function closeFormDataModal() {
            const m = document.getElementById('modal-form-data');
            m.classList.remove('flex'); m.classList.add('hidden'); document.body.style.overflow = 'auto';
        }
        document.getElementById('modal-form-data').addEventListener('click', function(e) { if (e.target === this) closeFormDataModal(); });

        function verifyEsignPdf(docType, opdId = null) {
            Swal.fire({
                title: 'Verifikasi PDF...',
                text: 'Harap tunggu, sedang memeriksa keaslian dokumen ke server E-Sign...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const data = {
                doc_type: docType,
                opd_id: opdId,
                _token: '{{ csrf_token() }}'
            };

            fetch('{{ route("data-perijinan.verify-pdf", $application->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                console.log('TTE Verify Response:', res); // Debug log
                if (res.error) {
                    Swal.fire('Gagal Verifikasi', res.message || 'Terjadi kesalahan sistem.', 'error');
                } else {
                    const conclusion = (res.conclusion || '').toUpperCase();
                    if (conclusion === 'NO_SIGNATURE') {
                        Swal.fire('Informasi Verifikasi', res.description || 'Dokumen belum memiliki Tanda Tangan Elektronik.', 'info');
                    } else if (conclusion === 'VALID_SIGNATURE' || conclusion === 'VALID' || conclusion === 'WARNING') {
                        let html = '<div class="text-left text-xs space-y-2">';
                        
                        if (conclusion === 'WARNING') {
                            html += '<p class="p-2 bg-amber-50 text-amber-700 border border-amber-100 rounded-lg"><strong>Status:</strong> ' + (res.description || 'Dokumen Valid (Sertifikat Demo/Tidak Terpercaya)') + '</p>';
                        } else {
                            html += '<p class="p-2 bg-green-50 text-green-700 border border-green-100 rounded-lg"><strong>Status:</strong> Dokumen Valid & TTE Terverifikasi</p>';
                        }

                        if (res.signatureInformations && res.signatureInformations.length > 0) {
                            res.signatureInformations.forEach((sig, idx) => {
                                html += '<div class="p-2 bg-gray-50 border rounded mt-2">';
                                html += '<p><strong>Penanda Tangan:</strong> ' + (sig.signerName || '-') + '</p>';
                                html += '<p><strong>Waktu TTE:</strong> ' + (sig.signatureDate || '-') + '</p>';
                                html += '<p><strong>Alasan:</strong> ' + (sig.reason || '-') + '</p>';
                                html += '</div>';
                            });
                        }
                        html += '</div>';
                        Swal.fire({ title: 'TTE Terverifikasi', html: html, icon: 'success' });
                    } else {
                        // Jika status lain, tampilkan pesan asli dari API agar jelas
                        Swal.fire({
                            title: 'Hasil Verifikasi: ' + (res.conclusion || 'Unknown'),
                            text: res.description || 'Silahkan periksa detail dokumen Anda.',
                            icon: conclusion.includes('VALID') ? 'success' : 'info'
                        });
                    }
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            });
        }

        function previewImage(url, title) { 
            const img = document.getElementById('previewImageElement'); 
            img.src = url; 
            document.getElementById('previewImageName').textContent = title; 
            const m = document.getElementById('imagePreviewModal'); 
            m.classList.remove('hidden'); 
            m.classList.add('flex'); 
        }

        function openPdfPreview(url, title) { document.getElementById('pdf-modal-title').textContent = title; document.getElementById('pdf-modal-iframe').src = url; const m = document.getElementById('modal-pdf-preview'); m.classList.remove('hidden'); m.classList.add('flex'); }
        function closePdfPreview() { document.getElementById('modal-pdf-preview').classList.add('hidden'); document.getElementById('pdf-modal-iframe').src = ''; }
        function submitValidation(action) { 
            const isKadin = {{ $isKadin ? 'true' : 'false' }};
            const isVerifikator = {{ $isVerifikator ? 'true' : 'false' }};
            const isMultiOpd = {{ $application->perijinan->is_multi_opd ? 'true' : 'false' }};

            let targetOpdLabel = '';
            if (action === 'return_to_kepala_opd' && isVerifikator && isMultiOpd) {
                const selectEl = document.getElementById('target_opd_id');
                if (selectEl && !selectEl.value) {
                    Swal.fire('Perhatian', 'Harap pilih Kepala OPD tujuan terlebih dahulu.', 'warning');
                    return;
                }
                targetOpdLabel = ' (' + selectEl.options[selectEl.selectedIndex].text + ')';
            }

            const texts = { 
                'approved': isKadin ? 'MENERBITKAN SURAT IZIN' : 'MENYETUJUI', 
                'rejected': 'MENOLAK', 
                'revision': 'MEMINTA PERBAIKAN KE PEMOHON', 
                'return_to_bo': 'MENGEMBALIKAN KE BACK OFFICE (BO)',
                'return_to_operator_opd': 'MENGEMBALIKAN KE OPERATOR OPD',
                'return_to_kepala_opd': 'MENGEMBALIKAN KE KEPALA OPD' + targetOpdLabel,
                'return_to_verifikator': 'MENGEMBALIKAN KE VERIFIKATOR'
            }; 
            const colors = {
                'approved': '#16a34a',
                'rejected': '#dc2626',
                'return_to_bo': '#2563eb',
                'return_to_operator_opd': '#2563eb',
                'return_to_kepala_opd': '#2563eb',
                'return_to_verifikator': '#2563eb'
            };
            Swal.fire({ 
                title: 'Konfirmasi', 
                text: "Apakah Anda yakin ingin " + texts[action] + " pengajuan ini?", 
                icon: 'question', 
                showCancelButton: true, 
                confirmButtonText: 'Ya, Lanjutkan',
                confirmButtonColor: colors[action] || '#2563eb'
            }).then((result) => { 
                if (result.isConfirmed) { 
                    document.getElementById('validationAction').value = action; 
                    document.getElementById('validationForm').submit(); 
                } 
            }); 
        }

        function handleReturnKepalaOpd() {
            const isMultiOpd = {{ $application->perijinan->is_multi_opd ? 'true' : 'false' }};
            if (isMultiOpd) {
                openReturnKepalaOpdModal();
            } else {
                submitValidation('return_to_kepala_opd');
            }
        }

        function openReturnKepalaOpdModal() {
            const m = document.getElementById('modal-return-kepala-opd');
            if (m) {
                m.classList.remove('hidden');
                m.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeReturnKepalaOpdModal() {
            const m = document.getElementById('modal-return-kepala-opd');
            if (m) {
                m.classList.remove('flex');
                m.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        function confirmReturnKepalaOpd() {
            const modalSelect = document.getElementById('modal_target_opd_id');
            const mainSelect = document.getElementById('target_opd_id');
            
            if (modalSelect && !modalSelect.value) {
                Swal.fire('Perhatian', 'Harap pilih Kepala OPD tujuan terlebih dahulu.', 'warning');
                return;
            }
            
            if (mainSelect) {
                mainSelect.value = modalSelect.value;
            }
            
            closeReturnKepalaOpdModal();
            submitValidation('return_to_kepala_opd');
        }
    </script>
    @endpush

{{-- ============================================================ --}}
{{-- Modal: Daftar Variabel Dinamis PDF Pembetulan               --}}
{{-- ============================================================ --}}
<div id="modal-variabel-dinamis" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('modal-variabel-dinamis').classList.add('hidden')"></div>

    {{-- Modal Panel --}}
    <div class="relative w-full max-w-2xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl flex flex-col max-h-[85vh] overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-600 to-violet-600 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="mdi mdi-code-braces text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-white font-black text-sm uppercase tracking-wide">Variabel Dinamis</h3>
                    <p class="text-indigo-200 text-[10px]">Sisipkan ke dalam PDF — sistem akan mengganti otomatis saat upload</p>
                </div>
            </div>
            <button onclick="document.getElementById('modal-variabel-dinamis').classList.add('hidden')"
                class="w-8 h-8 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center text-white transition-colors">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        {{-- Info Banner --}}
        <div class="px-6 py-3 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-100 dark:border-amber-800 flex items-start gap-2 flex-shrink-0">
            <i class="mdi mdi-information-outline text-amber-600 text-base mt-0.5 flex-shrink-0"></i>
            <p class="text-[11px] text-amber-700 dark:text-amber-400 leading-relaxed">
                Ketik placeholder tepat seperti yang tertera (huruf kapital, termasuk <code class="bg-amber-100 dark:bg-amber-900/50 px-1 rounded font-mono">${ }</code>).
                <strong>${QRCODE}</strong> selalu tampil di pojok kanan bawah halaman yang mengandung teks tersebut.
                Klik tombol <i class="mdi mdi-content-copy"></i> untuk menyalin.
            </p>
        </div>

        {{-- Scrollable Content --}}
        <div class="overflow-y-auto flex-1 p-6 space-y-5">
            @php
                $placeholderGroups = [
                    ['label' => 'QR Code', 'icon' => 'mdi-qrcode', 'color' => 'violet', 'items' => [
                        '${QRCODE}' => 'QR Code verifikasi izin (gambar di pojok kanan bawah halaman)',
                    ]],
                    ['label' => 'Data Pemohon', 'icon' => 'mdi-account-outline', 'color' => 'blue', 'items' => [
                        '${NAMA_PEMOHON}'    => 'Nama lengkap pemohon',
                        '${NIK}'             => 'NIK pemohon',
                        '${NO_HP}'           => 'Nomor HP pemohon',
                        '${EMAIL}'           => 'Email pemohon',
                        '${PEKERJAAN}'       => 'Pekerjaan pemohon',
                        '${NAMA_PERUSAHAAN}' => 'Nama perusahaan',
                        '${NPWP}'            => 'NPWP pemohon',
                    ]],
                    ['label' => 'Alamat', 'icon' => 'mdi-map-marker-outline', 'color' => 'emerald', 'items' => [
                        '${ALAMAT_KTP}'     => 'Alamat sesuai KTP',
                        '${ALAMAT_DOMISILI}'=> 'Alamat domisili',
                        '${ALAMAT_LENGKAP}' => 'Alamat lengkap (dengan kecamatan, kabupaten, dll.)',
                        '${KELURAHAN}'      => 'Kelurahan/Desa',
                        '${KECAMATAN}'      => 'Kecamatan',
                        '${KABUPATEN}'      => 'Kabupaten/Kota',
                        '${PROVINSI}'       => 'Provinsi',
                    ]],
                    ['label' => 'Data Perizinan', 'icon' => 'mdi-certificate-outline', 'color' => 'amber', 'items' => [
                        '${NAMA_LAYANAN}'    => 'Nama jenis perizinan',
                        '${NO_REGISTRASI}'   => 'Nomor registrasi pengajuan',
                        '${NOMOR_IZIN}'      => 'Nomor surat izin',
                        '${NOMOR_REKOM}'     => 'Nomor surat rekomendasi',
                        '${TANGGAL}'         => 'Tanggal pengajuan',
                        '${TANGGAL_HARI_INI}'=> 'Tanggal hari ini (saat upload PDF)',
                        '${MASA_AKTIF}'      => 'Masa aktif / berlaku izin',
                        '${TANGGAL_REKOM_TTE}'=> 'Tanggal rekomendasi ditandatangani secara TTE',
                    ]],
                    ['label' => 'Field Formulir Dinamis', 'icon' => 'mdi-form-select', 'color' => 'rose', 'items' => [
                        '${NAMA_FIELD}' => 'Ganti NAMA_FIELD dengan nama field formulir (global / BO / rekom / izin)',
                    ]],
                ];

                $colorMap = [
                    'violet'  => ['badge' => 'bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300 border-violet-200 dark:border-violet-700',  'icon' => 'text-violet-600',  'header' => 'text-violet-700 dark:text-violet-300'],
                    'blue'    => ['badge' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-700',          'icon' => 'text-blue-600',    'header' => 'text-blue-700 dark:text-blue-300'],
                    'emerald' => ['badge' => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700', 'icon' => 'text-emerald-600', 'header' => 'text-emerald-700 dark:text-emerald-300'],
                    'amber'   => ['badge' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-700',      'icon' => 'text-amber-600',   'header' => 'text-amber-700 dark:text-amber-300'],
                    'rose'    => ['badge' => 'bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-700',            'icon' => 'text-rose-600',    'header' => 'text-rose-700 dark:text-rose-300'],
                ];
            @endphp

            @foreach($placeholderGroups as $group)
            @php $c = $colorMap[$group['color']]; @endphp
            <div>
                {{-- Group Header --}}
                <div class="flex items-center gap-2 mb-2">
                    <i class="mdi {{ $group['icon'] }} {{ $c['icon'] }} text-base"></i>
                    <h4 class="text-xs font-black {{ $c['header'] }} uppercase tracking-widest">{{ $group['label'] }}</h4>
                    <div class="flex-1 h-px bg-gray-100 dark:bg-gray-700"></div>
                </div>

                {{-- Variable Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($group['items'] as $key => $desc)
                    <div class="flex items-start gap-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700 group/var hover:border-indigo-200 dark:hover:border-indigo-700 transition-colors">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <code class="text-[10px] font-mono font-bold {{ $c['badge'] }} px-1.5 py-0.5 rounded border truncate">{{ $key }}</code>
                            </div>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">{{ $desc }}</p>
                        </div>
                        <button type="button" onclick="copyVarToClipboard('{{ $key }}', this)"
                            title="Salin"
                            class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                            <i class="mdi mdi-content-copy text-sm"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-end flex-shrink-0">
            <button onclick="document.getElementById('modal-variabel-dinamis').classList.add('hidden')"
                class="px-5 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-bold uppercase transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
function copyVarToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const icon = btn.querySelector('i');
        icon.classList.replace('mdi-content-copy', 'mdi-check');
        btn.classList.add('text-emerald-600');
        setTimeout(() => {
            icon.classList.replace('mdi-check', 'mdi-content-copy');
            btn.classList.remove('text-emerald-600');
        }, 1500);
    });
}
// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('modal-variabel-dinamis')?.classList.add('hidden');
    }
});
</script>
</x-layout>