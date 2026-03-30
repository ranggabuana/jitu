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
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                    <p class="text-xs font-bold uppercase text-gray-400">Status Saat Ini</p>
                    <div class="mt-2" id="dStatusBadge"></div>
                    <p class="text-xs text-gray-500 mt-3">Estimasi SLA: <span class="font-semibold text-gray-700" id="dSla">-</span></p>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                    <p class="text-xs font-bold uppercase text-gray-400">Aksi</p>
                    <div class="mt-3 space-y-2">
                        <a href="#" class="w-full inline-flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2.5 rounded-lg font-bold transition-all">
                            <i class="fas fa-search"></i> Buka Tracking
                        </a>
                        <button class="w-full hidden items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg font-bold transition-all">
                            <i class="fas fa-download"></i> Unduh SK Izin
                        </button>
                        <a href="#" class="w-full hidden items-center justify-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2.5 rounded-lg font-bold transition-all">
                            <i class="fas fa-pen-to-square"></i> Perbaiki Berkas
                        </a>
                    </div>
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
