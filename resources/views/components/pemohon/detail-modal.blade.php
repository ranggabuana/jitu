<div id="detailModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full overflow-hidden animate-modalSlideUp">
        <div class="p-6 border-b border-amber-100 flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Detail Pengajuan</p>
                <h3 class="text-lg font-extrabold text-gray-800 mt-1" id="dTitle">-</h3>
                <p class="text-sm text-gray-500 mt-1">No. Reg: <span class="font-mono text-amber-700 font-semibold" id="dReg">-</span></p>
            </div>
            <button onclick="closeDetail()" class="text-gray-400 hover:text-red-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white border border-amber-200 rounded-xl p-5">
                    <h4 class="font-bold text-gray-800 mb-4">Timeline</h4>
                    <div class="relative pl-6 border-l-2 border-amber-200 space-y-6" id="dTimeline">
                        <!-- Timeline items will be loaded dynamically -->
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <!-- Status Card -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-300 dark:from-amber-900/20 dark:to-orange-900/10 dark:border-amber-700 rounded-xl p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-2">
                        <i class="fas fa-info-circle mr-1"></i> Status Saat Ini
                    </p>
                    <div class="mt-2" id="dStatusBadge"></div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-3">
                        <i class="fas fa-clock mr-1"></i> Estimasi SLA: 
                        <span class="font-semibold text-gray-800 dark:text-gray-200" id="dSla">-</span>
                    </p>
                </div>
                
                <!-- Action Buttons -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-3">
                        <i class="fas fa-bolt mr-1"></i> Aksi
                    </p>
                    <div class="space-y-2">
                        <a href="#" id="btnTracking" 
                           class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white px-4 py-3 rounded-lg font-bold transition-all shadow-md hover:shadow-lg">
                            <i class="fas fa-search"></i> Buka Tracking
                        </a>
                        <button id="btnDownload" 
                                class="w-full hidden items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-3 rounded-lg font-bold transition-all shadow-md hover:shadow-lg">
                            <i class="fas fa-download"></i> Unduh SK Izin
                        </button>
                        <a href="#" id="btnEdit" 
                           class="w-full hidden items-center justify-center gap-2 bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white px-4 py-3 rounded-lg font-bold transition-all shadow-md hover:shadow-lg">
                            <i class="fas fa-pen-to-square"></i> Perbaiki Berkas
                        </a>
                    </div>
                </div>
                
                <!-- Info Card for Approved/Rejected -->
                <div id="dInfoCard" class="hidden bg-white dark:bg-gray-800 border rounded-xl p-5 shadow-sm">
                    <!-- Dynamic content based on status -->
                </div>
            </div>
        </div>
        <div class="p-6 border-t border-amber-100 bg-white flex justify-end">
            <button onclick="closeDetail()" class="px-5 py-2.5 rounded-xl border border-amber-200 text-gray-700 font-semibold hover:bg-amber-50 transition-all">Tutup</button>
        </div>
    </div>
</div>

<script>
function closeDetail() {
    const modal = document.getElementById('detailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modal when clicking outside
document.getElementById('detailModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetail();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetail();
    }
});
</script>
