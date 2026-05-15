<!-- Bottom Navigation for Mobile -->
<div class="md:hidden fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
    <div class="grid h-full max-w-lg grid-cols-4 mx-auto font-medium">
        <!-- Dashboard -->
        <a href="{{ route('pemohon.dashboard') }}" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group {{ request()->routeIs('pemohon.dashboard') ? 'text-amber-700' : 'text-gray-500 hover:text-amber-700' }}">
            <i class="fas fa-home text-lg mb-1 {{ request()->routeIs('pemohon.dashboard') ? 'text-amber-700' : 'text-gray-400 group-hover:text-amber-700' }}"></i>
            <span class="text-[10px]">Beranda</span>
        </a>
        
        <!-- Ajukan Izin -->
        <a href="{{ route('pemohon.perijinan') }}" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group {{ request()->routeIs('pemohon.perijinan*') || request()->routeIs('pemohon.pengajuan*') ? 'text-amber-700' : 'text-gray-500 hover:text-amber-700' }}">
            <i class="fas fa-plus-circle text-lg mb-1 {{ request()->routeIs('pemohon.perijinan*') || request()->routeIs('pemohon.pengajuan*') ? 'text-amber-700' : 'text-gray-400 group-hover:text-amber-700' }}"></i>
            <span class="text-[10px]">Ajukan</span>
        </a>
        
        <!-- Tracking -->
        <a href="{{ route('pemohon.tracking') }}" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group {{ request()->routeIs('pemohon.tracking*') ? 'text-amber-700' : 'text-gray-500 hover:text-amber-700' }}">
            <i class="fas fa-search text-lg mb-1 {{ request()->routeIs('pemohon.tracking*') ? 'text-amber-700' : 'text-gray-400 group-hover:text-amber-700' }}"></i>
            <span class="text-[10px]">Tracking</span>
        </a>
        
        <!-- Dokumen Saya -->
        <a href="{{ route('pemohon.dokumen.index') }}" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group {{ request()->routeIs('pemohon.dokumen*') ? 'text-amber-700' : 'text-gray-500 hover:text-amber-700' }}">
            <i class="fas fa-file-alt text-lg mb-1 {{ request()->routeIs('pemohon.dokumen*') ? 'text-amber-700' : 'text-gray-400 group-hover:text-amber-700' }}"></i>
            <span class="text-[10px]">Dokumen</span>
        </a>
    </div>
</div>