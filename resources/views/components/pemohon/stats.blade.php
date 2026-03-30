<section class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-white rounded-2xl border border-amber-200 p-6 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-bold uppercase text-gray-400">Total Pengajuan</p>
            <p class="text-3xl font-extrabold text-gray-800 mt-1" id="statTotal">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl">
            <i class="fas fa-folder-open"></i>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-amber-200 p-6 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-bold uppercase text-gray-400">Sedang Diproses</p>
            <p class="text-3xl font-extrabold text-gray-800 mt-1" id="statInProgress">{{ $stats['in_progress'] ?? 0 }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center text-xl">
            <i class="fas fa-spinner"></i>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-amber-200 p-6 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-bold uppercase text-gray-400">Butuh Perbaikan</p>
            <p class="text-3xl font-extrabold text-gray-800 mt-1" id="statFix">{{ $stats['needs_fix'] ?? 0 }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-red-100 text-red-700 flex items-center justify-center text-xl">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-amber-200 p-6 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-bold uppercase text-gray-400">Izin Terbit</p>
            <p class="text-3xl font-extrabold text-gray-800 mt-1" id="statDone">{{ $stats['completed'] ?? 0 }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-green-100 text-green-700 flex items-center justify-center text-xl">
            <i class="fas fa-check-circle"></i>
        </div>
    </div>
</section>
